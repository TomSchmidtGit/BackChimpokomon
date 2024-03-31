<?php

namespace App\Controller;

use App\Entity\Chimpokodex;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChimpokodexRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChimpokodexController extends AbstractController
{

    /**
     * Renvoie toutes les entrées Chimokomons du Chimpokodex
     *
     * @param ChimpokodexRepository $repository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     */
    #[OA\Response(
        response:200,
        description: "Retourne la liste des chimpokomons",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: new Model(type:Chimpokodex::class))
        )
    )]
    #[Route('/api/chimpokodex', name: 'chimpokodex.getAll', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function getAllChimpokodex(ChimpokodexRepository $repository,  SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $idCache = 'getAllChimpokodex';
        $jsonChimpokodex = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer) {

            $item->tag("chimpokodexCache");
            $chimpokodexs = $repository->findAllByStatus('ON');
            return $serializer->serialize($chimpokodexs, 'json', ['groups' => "getAllWithinEvolutions"]);


        });

        return new JsonResponse($jsonChimpokodex,200,[],true);   
    }

    #[Route('/api/chimpokodex/{idChimpokodex}', name: 'chimpokodex.get', methods: ['GET'])]
    #[ParamConverter("chimpokodex", options: ["id" => "idChimpokodex"])]
    /**
     * Renvoie le chimpokomon dont l'id est en paramètre
     *
     * @param Chimpokodex $chimpokodex
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getChimpokodex(Chimpokodex $chimpokodex,  SerializerInterface $serializer, ChimpokodexRepository $repository): JsonResponse
    {
        $chimpokodexs = $repository->findByStatus('ON',$chimpokodex->getId());
        $jsonChimpokodex = $serializer->serialize($chimpokodexs, 'json', ['groups' => "getAllWithinEvolutions"]);
        return new JsonResponse($jsonChimpokodex,200,[],true); 
    }
    
    #[Route('/api/chimpokodex', name: 'chimpokodex.post', methods: ['POST'])]
    /**
     * Création Chimpokomon
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function createChimpokodex(Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager, UrlGeneratorInterface $urlGenerator, ChimpokodexRepository $repository, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse
    {
        $chimpokodex = $serializer->deserialize($request->getContent(), Chimpokodex::class,"json");

        $dateNow = new \DateTime();

        $evolutionID = $request->toArray()["evolutionId"];
        $evolution = $repository->find($evolutionID);

        if(!is_null($evolution) && $evolution instanceof Chimpokodex) {
            $chimpokodex->addEvolution($evolution);
        }

        $chimpokodex
        ->setStatus('ON')
        ->setCreatedAt($dateNow)
        ->setUpdatedAt($dateNow);

        $errors = $validator->validate($chimpokodex);
        if ($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($chimpokodex);
        $manager->flush();

        $cache->invalidateTags(["chimpokodexCache"]);
    
        $jsonChimpokodex = $serializer->serialize($chimpokodex, 'json', ['groups' => "getAllWithinEvolutions"]);

        $location = $urlGenerator->generate('chimpokodex.get', ["idChimpokodex" => $chimpokodex->getId()], UrlGeneratorInterface::ABSOLUTE_URL);


        return new JsonResponse($jsonChimpokodex,Response::HTTP_CREATED,["Location" => $location],true);
    }

    #[Route('/api/chimpokodex/{id}', name: 'chimpokodex.update', methods: ['PUT'])]
    /**
     * Modifier un Chimpo
     *
     * @param Chimpokodex $chimpokodex
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function updateChimpokodex(Chimpokodex $chimpokodex, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, ChimpokodexRepository $repository, TagAwareCacheInterface $cache): JsonResponse
    {
        $updatedChimpokodex = $serializer->deserialize($request->getContent(), Chimpokodex::class, "json", [AbstractNormalizer::OBJECT_TO_POPULATE => $chimpokodex]);
        $updatedChimpokodex->setUpdatedAt(new \DateTime());

        if (isset($request->toArray()["evolutionId"])){
            $newEvolutionID = $request->toArray()["evolutionId"];
            $newEvolution = $repository->find($newEvolutionID);
            $oldEvolution = $repository->find($chimpokodex->getEvolution()->first());


            if(!is_null($newEvolution) && $newEvolution instanceof Chimpokodex && $oldEvolution !== $newEvolution) {
                $updatedChimpokodex->removeEvolution($oldEvolution);
                $updatedChimpokodex->addEvolution($newEvolution);
            }
        }
        
        $entityManager->persist($updatedChimpokodex);
        $entityManager->flush();

        $cache->invalidateTags(["chimpokodexCache"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    
    #[Route('/api/chimpokodex/{id}', name: 'chimpokodex.softdelete', methods: ['DELETE'])]
    /**
     * Mettre en OFF le statut d'un chimpokomon ou alors le delete si on précise 'forcedelete = true'
     *
     * @param Chimpokodex $chimpokodex
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function softdeleteChimpokodex(Chimpokodex $chimpokodex,  Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $forceDelete = $request->toArray();

        if(isset($forceDelete["forcedelete"]) && $forceDelete["forcedelete"] == true)
        {
            $entityManager->remove($chimpokodex);
        }
        else {
            $chimpokodex->setStatus("OFF");
        }

        $entityManager->flush();

        $cache->invalidateTags(["chimpokodexCache"]);
        
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
     
    }

}

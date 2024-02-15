<?php

namespace App\DataFixtures;

use App\Entity\Chimpokodex;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(){

        $this->faker = Factory::create('fr_FR');
    }
    
    /**
     * Load New datas
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $chimpokodexEntries = [];
        for ($i=0; $i < 151; $i++) { 
            //Instantiate new Chimpokodex Entity to Fullfill
            $chimpokodex = new Chimpokodex();
            //Handle created && updated datetime
            $created = $this->faker->dateTimeBetween("-1 week", "now");
            $updated = $this->faker->dateTimeBetween($created, "now");
            //Asign Properties to Entity
            $chimpokodex
            ->setName($this->faker->word())
            ->setPvMax($this->faker->numberBetween(1,100))
            ->setCreatedAt($created)
            ->setUpdatedAt($updated)
            ->setStatus($this->faker->randomElement(['ON','OFF']));
            
            //stock Chimpokodex Entry
            $chimpokodexEntries[] = $chimpokodex;
            //Add to transaction
            $manager->persist($chimpokodex);
        }

        //Execute transaction 
        foreach ($chimpokodexEntries as $key => $chimpokodexEntry) {
            $evolution = $chimpokodexEntries[array_rand($chimpokodexEntries, 1)];
                $chimpokodexEntry->addEvolution($evolution);
                $manager->persist($chimpokodexEntry); 
        }

        $manager->flush();


    }
}
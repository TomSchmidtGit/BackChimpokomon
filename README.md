## Configuration de l'environnement

Avant de démarrer le projet, assurez-vous que votre système est à jour et que les services nécessaires sont configurés et en cours d'exécution (Php, Apache2).

Voici les étapes à suivre :

### Mise à jour du système

Il est recommandé de mettre à jour la liste des paquets et leurs versions sur votre machine. Ouvrez un terminal et exécutez les commandes suivantes :

```bash
sudo apt update
sudo apt upgrade

Lancer le service Apache2 (bien penser à le fermer après utilisation) : 
sudo service apache2 start

Bien se positionner dans le bon dossier puis lancer le serveur :

cd chemin/vers/votre/projet
symfony serve


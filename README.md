## Configuration de l'environnement

Avant de démarrer le projet, assurez-vous que votre système est à jour et que les services nécessaires sont configurés et en cours d'exécution (Php, Apache2 et Symfony notamment).

Voici les étapes à suivre :

### Mise à jour du système

Il est recommandé de mettre à jour la liste des paquets et leurs versions sur votre machine. Ouvrez un terminal et exécutez les commandes suivantes :
```bash
sudo apt update
sudo apt upgrade
```

### Mise en place :

Lancer le service Apache2 (penser à le fermer après utilisation), se positionner dans le bon dossier puis lancer le serveur
```bash
sudo service apache2 start
cd chemin/vers/votre/projet
```
Mettre en place la BDD : 
```bash
php bin/console d:d:c
php bin/console doctrine:fixture:load
```

Lancer le serveur : 
```bash
symfony serve
```

Le serveur se lancera sur le port 8000 du localhost.

### Documentation :

Les routes de l'API sont consultables une fois le serveur lancé via http://localhost:8000/api/doc

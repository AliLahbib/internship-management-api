README - API Backend Gestion des Stages (Symfony 7)

1. Introduction

Cette API est développée en Symfony 7 pour la gestion des stages académiques. Elle permet la gestion des demandes de stage, la validation des mémoires, l'organisation des soutenances et la génération des attestations.

2. Prérequis

Avant d'exécuter ce projet, assurez-vous d'avoir installé :

PHP 8.2+

Composer

Symfony CLI

MySQL 8+

Docker & Docker Compose (optionnel pour l'environnement de développement)

3. Installation
   3.1 Cloner le projet

      git clone https://github.com/AliLahbib/internship-management-api.git
      cd internship-management-api

  3.2 Installer les dépendances

      composer install

  3.3 Configurer l'environnement
  Copier le fichier .env.example en .env et modifier les variables de connexion à la base de données :

  3.5 Créer la base de données et exécuter les migrations

      php bin/console doctrine:database:create
      php bin/console doctrine:migrations:migrate

3.6 Lancer le serveur Symfony

    symfony   server:start


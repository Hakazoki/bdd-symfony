<div align="center">

# Hub de restaurants

Application Symfony 8 : Application de gestion de restaurants pour expérimenter les différentes facettes d'un projet symfony.

![Symfony](https://img.shields.io/badge/Symfony-8.0-000000?style=for-the-badge&logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-%E2%89%A5%208.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Doctrine](https://img.shields.io/badge/Doctrine%20ORM-3-FC6A31?style=for-the-badge&logo=doctrine&logoColor=white)

</div>

## Features

- Liste des restaurants (description, adresse, note) et filtre par ville
- Ajouter, modifier et supprimer des restaurants
- Laisser un avis (note 1-5 + texte) et répondre à un avis
- Inscription, connexion et rôles (utilisateur, propriétaire, administrateur)
- Limitation des tentatives de connexion


## Roles

| Action | Visiteur | Utilisateur connecté | Propriétaire du restaurant | Administrateur |
|--------|:--------:|:--------------------:|:--------------------------:|:--------------:|
| Consulter restaurants, villes et régions | Oui | Oui | Oui | Oui |
| Ajouter un restaurant | Non | Oui | Oui | Oui |
| Modifier / supprimer un restaurant | Non | Non | Oui (le sien) | Oui (tous) |
| Ajouter un commentaire / répondre | Non | Oui | Oui | Oui |
| Supprimer un commentaire | Non | Oui (le sien) | Oui (le sien) | Oui (tous) |
| Ajouter / supprimer une ville ou une région | Non | Non | Non | Oui |

> Un utilisateur créant un restaurant en devient le propriétaire.

## Diagramme d’architecture du projet

[![Diagramme d’architecture du projet hakazoki/bdd-symfony](https://gitdiagram.com/hakazoki/bdd-symfony/diagram.png)](https://gitdiagram.com/hakazoki/bdd-symfony?utm_source=readme&utm_medium=picture)

## Prérequis

- PHP ≥ 8.4 (avec l'extension `pdo_pgsql`) et Composer
- Docker (pour PostgreSQL)
- Symfony CLI (facultatif)

## Installation

Après avoir cloné le projet, éxécutez les commandes suivantes à sa racine pour installer les dépendances, créer la bdd et charger le jeu de données :

```bash
composer install
docker compose up -d --wait database
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

> Les identifiants de la base (`POSTGRES_*`) sont modifiables dans le fichier `.env`.

## Lancer l'application

```bash
symfony server:start
```

Puis accédez à l'addresse du serveur depuis un navigateur (par défaut `http://127.0.0.1:8000`).

## Comptes de démo

Comptes présents dans le jeu de données. Le mot de passe de tous les comptes est `password`.

| Compte                   | Rôle                         |
|--------------------------|------------------------------|
| `thomas@restau-cool.bzh` | propriétaire de Zozan Kebab  |
| `dorian@restau-cool.bzh` | propriétaire de Au Fût Perdu |
| `gromli@restau-cool.bzh` | propriétaire de Chez Gromli  |
| `admin@restau-cool.bzh`  | administrateur               |

> Après 3 tentatives de connexion échouées, la connexion est bloquée pendant 1 minute.

#### Après avoir clonner éxécutez ces commandes à la racine du projet pour setup la bdd et les jeu de données.
```BASH
composer install
docker compose up -d --wait database
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```
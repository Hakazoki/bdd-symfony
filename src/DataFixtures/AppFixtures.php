<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use App\Entity\Region;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User()) ->setEmail('alice@test.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $regions = [];
        foreach (['Île-de-France', 'Auvergne-Rhône-Alpes', "Provence-Alpes-Côte d'Azur", 'Nouvelle-Aquitaine', 'Ille-et-Vilaine', 'Univers'] as $nom) {
            $region = (new Region())->setNom($nom);
            $manager->persist($region);
            $regions[$nom] = $region;
        }

        $villes = [];
        foreach (
            [
                'Paris' => 'Île-de-France',
                'Lyon' => 'Auvergne-Rhône-Alpes',
                'Marseille' => "Provence-Alpes-Côte d'Azur",
                'Bordeaux' => 'Nouvelle-Aquitaine',
                'Fougères' => 'Ille-et-Vilaine',
                'Ciel' => 'Univers',
            ] as $nom => $regionNom
        ) {
            $ville = (new Ville())->setNom($nom)->setRegion($regions[$regionNom]);
            $manager->persist($ville);
            $villes[$nom] = $ville;
        }

        $restaurants = [
            ['Zozan Kebab', 'Premier kebab étoilé au guide Michelin en 2023, cuisin traditionnelle, innovante, novatrice et à la pointe de la technologie. Broche à kebab légué de père en fils depuis la nuit des temps. Accueil accueillant, service rapide, efficace et avec le sourire. Le restaurant nouvellement décoré par nos experts est prêt à vous accueillir.', '4  Boulevard du Maréchal Leclerc', 'Fougères'],
            ['Chez Gromli', "L'ambroisie des dieu.", 'Palais divin', 'Ciel'],
            ['Au Fût Perdu', "Cidre de qualité (a vos risques et périls)", '8 bd de clignancourt', 'Fougères'],
            ['La Bouillabaisse', 'Spécialités de poisson.', '45 quai du Port', 'Marseille'],
        ];

        foreach ($restaurants as [$nom, $description, $adresse, $villeNom]) {
            $restaurant = (new Restaurant())
                ->setNom($nom)
                ->setDescription($description)
                ->setAdresse($adresse)
                ->setVille($villes[$villeNom]);
            $manager->persist($restaurant);

            $commentaire = (new Commentaire())->setRestaurant($restaurant)->setAuteur($user)->setContenu('Glorpissimement glorpesque !')->setNote(5);
            $manager->persist($commentaire);
            $manager->persist((new Commentaire())->setRestaurant($restaurant)->setAuteur($user)->setParent($commentaire)->setContenu('Merci pour votre avis !'));
            $manager->persist((new Commentaire())->setRestaurant($restaurant)->setAuteur($user)->setContenu('૮ ˶ᵔ ᵕ ᵔ˶ ა')->setNote(3));
        }

        $manager->flush();
    }
}

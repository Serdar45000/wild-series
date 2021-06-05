<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public const EPISODES = [
        [
            'title' => "La nouvelle voisine des surdoués",
            'number' => 1,
            'synopsis' => 'Leonard, un scientifique pour qui les relations sociales sont difficiles, tombe amoureux de la jolie voisine, au grand désarroi de son colocataire Sheldon.',
        ],
        [
            'title' => "Des voisins encombrants",
            'number' => 2,
            'synopsis' => 'Leonard propose de recevoir un colis pour Penny, mais les choses tournent mal lorsque Sheldon se met à nettoyer son appartement.',
        ],
        [
            'title' => "Le corollaire de patte-de-velours",
            'number' => 3,
            'synopsis' => 'Leonard est dévasté quand il voit Penny embrasser un autre homme. Il veut inviter à sortir avec lui sous couvert pour un dîner avec les garçons.',
        ],
        [
            'title' => "Les poissons luminescents",
            'number' => 4,
            'synopsis' => 'Leonard appelle la mère de Sheldon lorsque celui-ci développe une obsession pour la conception de ponchos et les poissons bioluminescents à la suite de son licenciement.',
        ],
        [
            'title' => "Le postulat du hamburger",
            'number' => 5,
            'synopsis' => 'Après un rapport sexuel avec Leslie, ce qui énerve Léonard parce que la liaison est sans lendemain.',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (ProgramFixtures::PROGRAMS as $programTitle => $programDescription) {
            foreach (SeasonFixtures::SEASONS as $seasonTitle => $seasonDescription) {
                foreach (self::EPISODES as $number => $episodeDescription) {
                    $episode = new Episode();
                    $episode->setTitle($episodeDescription['title']);
                    $episode->setNumber($episodeDescription['number']);
                    $episode->setSynopsis($episodeDescription['synopsis']);
                    $episode->setSeason($this->getReference('season_'. $programTitle . '_' . $seasonTitle));
                    $manager->persist($episode);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}

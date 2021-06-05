<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS = [
        '1' => [
            'number' => 1,
            'description' => 'Leonard et Sheldon pourraient vous dire tout ce que vous voudriez savoir à propos de physique quantique. Mais ils seraient bien incapables de vous expliquer quoi que ce soit sur la vie réelle.',
            'year' => 2007
        ],
        '2' => [
            'number' => 2,
            'description' => 'Alors que les obsessions de Sheldon sont intensifiées, Leonard et Penny se rapprochent. Pendant ce temps, Howard multiplie les tentatives pour séduire des femmes.',
            'year' => 2008
        ],
        '3' => [
            'number' => 3,
            'description' => 'Sheldon et ses amis sont de retour de leur expédition au pôle Nord. Alors que Penny et Leonard débutent leur relation, Howard commence à fréquenter Bernadette Rostenkowski.',
            'year' => 2009
        ],
        '4' => [
            'number' => 4,
            'description' => 'Sheldon entame une relation platonique avec Amy Farrah Fowler, une brillante neuroscientifique. Pendant ce temps, Leonard fréquente Priya, ce qui déplaît fortement à son frère Raj.',
            'year' => 2010
        ],
        '5' => [
            'number' => 5,
            'description' => 'Alors que Penny regrette d’avoir passé la nuit avec Raj, Leonard décide de rompre avec Priya. Dans le même temps, le couple Amy et Sheldon passe un nouveau cap.',
            'year' => 2011
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (ProgramFixtures::PROGRAMS as $title => $description) {
            foreach (self::SEASONS as $number => $seasonDescription) {
                $season = new Season();
                $season->setNumber($seasonDescription['number']);
                $season->setYear($seasonDescription['year']);
                $season->setDescription($seasonDescription['description']);
                $season->setProgram($this->getReference('program_' . $title));
                $manager->persist($season);
                $this->addReference('season_' . $title . '_' . $number, $season);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}

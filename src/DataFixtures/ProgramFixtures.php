<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        'The Big Bang Theory' => [
            'summary' => 'Leonard Hofstadter et Sheldon Cooper vivent en colocation à Pasadena. Ce sont tous deux des physiciens surdoués, geeks de surcroît. La série est axée la majeure partie comique de la série. Ils partagent quasiment tout leur temps libre avec leurs deux amis Howard Wolowitz et Rajesh Koothrappali pour jouer à des jeux vidéo comme Halo, organiser un marathon de la saga Star Wars...',
            'poster' => 'https://images-na.ssl-images-amazon.com/images/I/717Se%2BlZiOL._AC_SY445_.jpg',
            'country' => 'USA',
            'year' => '2007',
            'category' => 'Comédie'
        ],
        'Scrubs' => [
            'summary' => 'Scrubs est une série centrée sur la vie du personnel dans un hôpital du Sacré-Cœur (Sacred Heart) et particulièrement sur celle de John Dorian, alias « J.D. ». Au début de la série, J.D. est un jeune interne qui entre au  Sacré-Cœur, tout comme son meilleur ami et colocataire, apprenti-chirurgien Christopher Turk...',
            'poster' => 'https://images-na.ssl-images-amazon.com/images/I/61d3FWwGxbL._AC_SY445_.jpg',
            'country' => 'USA',
            'year' => '2001',
            'category' => 'Comédie'
        ],
        'Brooklyn Nine-Nine' => [
            'summary' => 'Brooklyn Nine-Nine raconte la vie du commissariat de police arrondissement de Brooklyn à New York. Arrivée du nouveau capitaine, froid et strict, fait rapidement regretter aux détectives son prédécesseur. « Brooklyn Nine-Nine: the Law without the Order »  une satire de Law and Order), les divers personnages la composant sont dotés de caractères très marqués voire extravagants, mettant ainsi à mal cette harmonie dans les bureaux.',
            'poster' => 'https://images-na.ssl-images-amazon.com/images/I/71i-2iLxpLL._AC_SY445_.jpg',
            'country' => 'USA',
            'year' => '2013',
            'category' => 'Comédie'
        ],
        'Upload' => [
            'summary' => 'Dans un futur où les humains sont capables de se « téléverser » dans la vie après la mort, Nathan, mort prématurément, est accueilli dans sa version du paradis par une certaine Nora. Il va lui falloir s\'adapter… ',
            'poster' => 'https://www.themoviedb.org/t/p/w220_and_h330_face/6SIDIB59JYsQ8EfUgM0IaFfwXtS.jpg',
            'country' => 'USA',
            'year' => '2019',
            'category' => 'Fantastique'
        ],
        'L\'Arme fatale' => [
            'summary' => 'Roger Murtaugh, un policier vétéran cardiaque fait équipe avec Martin Riggs, ancien seal, dans les rues de Los Angeles. Le duo, bien qu\'improbable, est efficace mais leurs aventures finissent souvent par de nombreux dégâts.',
            'poster' => 'https://images-eu.ssl-images-amazon.com/images/I/71cSmG9PDpL.__AC_SX300_SY300_QL70_ML2_.jpg',
            'country' => 'USA',
            'year' => '2016',
            'category' => 'Action'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $title => $description) {
            $program = new Program();
            $program->setTitle($title);
            $program->setSummary($description['summary']);
            $program->setPoster($description['poster']);
            $program->setYear($description['year']);
            $program->setCountry($description['country']);
            $program->setCategory($this->getReference('category_' . $description['category']));
            //ici les acteurs sont insérés via une boucle pour être DRY mais ce n'est pas obligatoire
            for ($i=0; $i < count(ActorFixtures::ACTORS); $i++) {
                $program->addActor($this->getReference('actor_' . $i));
            }
            $manager->persist($program);
            $this->addReference('program_' . $title, $program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ActorFixtures::class,
            CategoryFixtures::class,
        ];
    }
}

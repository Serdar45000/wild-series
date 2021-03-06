<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Création d’un utilisateur de type “contributeur” (= auteur)
        $contributor = new User();
        $contributor->setEmail('contributor@monsite.com');
        $contributor->setRoles(['ROLE_CONTRIBUTOR']);
        $contributor->setUsername('Contributor');
        $contributor->setPassword($this->passwordEncoder->encodePassword(
            $contributor,
            'contributorpassword'
        ));
        $manager->persist($contributor);
        $this->addReference('contributor', $contributor);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUsername('Admin');
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $manager->persist($admin);
        $this->addReference('admin', $admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}

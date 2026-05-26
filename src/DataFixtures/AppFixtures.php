<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Club;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setFirstname('Admin')
              ->setLastname('ISET')
              ->setEmail('admin@iset.tn')
              ->setRoles(['ROLE_ADMIN'])
              ->setIsVerified(true)
              ->setDtype('admin')
              ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Président
        $president = new User();
        $president->setFirstname('Mohamed')
                  ->setLastname('Ben Ali')
                  ->setEmail('president@iset.tn')
                  ->setRoles(['ROLE_PRESIDENT'])
                  ->setIsVerified(true)
                  ->setDtype('president')
                  ->setPassword($this->hasher->hashPassword($president, 'president123'));
        $manager->persist($president);

        // Étudiant
        $student = new User();
        $student->setFirstname('Sarra')
                ->setLastname('Trabelsi')
                ->setEmail('etudiant@iset.tn')
                ->setRoles(['ROLE_USER'])
                ->setIsVerified(true)
                ->setDtype('student')
                ->setPassword($this->hasher->hashPassword($student, 'etudiant123'));
        $manager->persist($student);

        // Club
        $club = new Club();
        $club->setName('Club Tech ISET')
             ->setDescription('Club dédié à la technologie.')
             ->setDomain('Technologie')
             ->setStatus('approved')
             ->setCreatedAt(new \DateTime())
             ->setProposedBy($president);
        $manager->persist($club);

        $manager->flush();
    }
}
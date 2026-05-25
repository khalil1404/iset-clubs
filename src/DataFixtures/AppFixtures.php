<?php

namespace App\DataFixtures;

<<<<<<< HEAD
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
=======
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
        $admin = new User();
        $admin->setFirstname('Admin')
              ->setLastname('ISET')
              ->setEmail('admin@iset.tn')
              ->setRoles(['ROLE_ADMIN'])
              ->setIsVerified(true)
              ->setDtype('admin')
              ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $president = new User();
        $president->setFirstname('Mohamed')
                  ->setLastname('Ben Ali')
                  ->setEmail('president@iset.tn')
                  ->setRoles(['ROLE_PRESIDENT'])
                  ->setIsVerified(true)
                  ->setDtype('president')
                  ->setPassword($this->hasher->hashPassword($president, 'president123'));
        $manager->persist($president);

        $student = new User();
        $student->setFirstname('Sarra')
                ->setLastname('Trabelsi')
                ->setEmail('etudiant@iset.tn')
                ->setRoles(['ROLE_USER'])
                ->setIsVerified(true)
                ->setDtype('student')
                ->setPassword($this->hasher->hashPassword($student, 'etudiant123'));
        $manager->persist($student);

        $manager->flush();
    }
}
>>>>>>> develop

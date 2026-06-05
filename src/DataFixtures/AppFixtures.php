<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Club;
use App\Entity\ClubMember;
use App\Entity\Evenement;
use App\Entity\Recrutement;
use App\Entity\Reclamation;
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
        $admin->setFirstname('Admin')->setLastname('ISET')
              ->setEmail('admin@iset.tn')->setRoles(['ROLE_ADMIN'])
              ->setIsVerified(true)->setDtype('admin')
              ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Président 1
        $pres1 = new User();
        $pres1->setFirstname('Mohamed')->setLastname('Ben Ali')
              ->setEmail('president@iset.tn')->setRoles(['ROLE_PRESIDENT'])
              ->setIsVerified(true)->setDtype('president')
              ->setPassword($this->hasher->hashPassword($pres1, 'president123'));
        $manager->persist($pres1);

        // Président 2
        $pres2 = new User();
        $pres2->setFirstname('Fatma')->setLastname('Gharbi')
              ->setEmail('president2@iset.tn')->setRoles(['ROLE_PRESIDENT'])
              ->setIsVerified(true)->setDtype('president')
              ->setPassword($this->hasher->hashPassword($pres2, 'president123'));
        $manager->persist($pres2);

        // 5 Étudiants
        $students = [];
        $names = [
            ['Sarra', 'Trabelsi', 'etudiant1'],
            ['Ahmed', 'Mansour', 'etudiant2'],
            ['Nour', 'Belhaj', 'etudiant3'],
            ['Youssef', 'Karray', 'etudiant4'],
            ['Ines', 'Hamdi', 'etudiant5'],
        ];
        foreach ($names as $n) {
            $s = new User();
            $s->setFirstname($n[0])->setLastname($n[1])
              ->setEmail($n[2].'@iset.tn')
              ->setRoles(['ROLE_USER'])->setIsVerified(true)->setDtype('student')
              ->setPassword($this->hasher->hashPassword($s, 'etudiant123'));
            $manager->persist($s);
            $students[] = $s;
        }

        $manager->flush();

        // Club 1
        $club1 = new Club();
        $club1->setName('Club Tech ISET')
              ->setDescription('Club dédié à la technologie et au développement web.')
              ->setDomain('Technologie')->setStatus('approved')
              ->setCreatedAt(new \DateTimeImmutable())
              ->setProposedBy($pres1);
        $manager->persist($club1);

        // Club 2
        $club2 = new Club();
        $club2->setName('Club Sport ISET')
              ->setDescription('Club sportif de l\'ISET Zaghouan.')
              ->setDomain('Sport')->setStatus('approved')
              ->setCreatedAt(new \DateTimeImmutable())
              ->setProposedBy($pres2);
        $manager->persist($club2);

        $manager->flush();

        // Membres
        foreach (array_slice($students, 0, 3) as $s) {
            $m = new ClubMember();
            $m->setUser($s)->setClub($club1)
              ->setRole('member')->setJoinedAt(new \DateTimeImmutable());
            $manager->persist($m);
        }

        // Événement 1
        $e1 = new Evenement();
        $e1->setNomEvenement('Hackathon ISET 2026')
           ->setDescription('Compétition de développement sur 24h.')
           ->setLieu('Salle informatique ISET Zaghouan')
           ->setDateDebut(new \DateTime('+7 days'))
           ->setDateFin(new \DateTime('+8 days'))
           ->setStatus('approved')
           ->setCreatedAt(new \DateTimeImmutable())
           ->setClub($club1);
        $manager->persist($e1);

        // Événement 2
        $e2 = new Evenement();
        $e2->setNomEvenement('Tournoi Football ISET')
           ->setDescription('Tournoi inter-classes de football.')
           ->setLieu('Terrain de sport ISET')
           ->setDateDebut(new \DateTime('+14 days'))
           ->setDateFin(new \DateTime('+14 days'))
           ->setStatus('approved')
           ->setCreatedAt(new \DateTimeImmutable())
           ->setClub($club2);
        $manager->persist($e2);

        // Recrutement
        $rec = new Recrutement();
        $rec->setTitle('Développeur Web Junior')
            ->setDescription('Rejoignez notre équipe de développement.')
            ->setRequirements('HTML, CSS, PHP, Symfony')
            ->setDeadline(new \DateTime('+30 days'))
            ->setStatus('open')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setClub($club1);
        $manager->persist($rec);

        // Réclamation
        $recl = new Reclamation();
        $recl->setUser($students[0])
             ->setSubject('Problème de connexion')
             ->setMessage('Je n\'arrive pas à me connecter depuis 2 jours.')
             ->setStatus('pending')
             ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($recl);

        $manager->flush();
        echo "✅ Fixtures chargées !\n";
    }
}
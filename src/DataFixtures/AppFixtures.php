<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Club;
use App\Entity\ClubMember;
use App\Entity\Evenement;
use App\Entity\Recrutement;
use App\Entity\Candidature;
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
        // ---- ADMIN ----
        $admin = new User();
        $admin->setFirstname('Admin')->setLastname('ISET')
              ->setEmail('admin@iset.tn')->setRoles(['ROLE_ADMIN'])
              ->setIsVerified(true)->setDtype('admin')
              ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // ---- PRESIDENTS ----
        $president1 = new User();
        $president1->setFirstname('Mohamed')->setLastname('Ben Ali')
                   ->setEmail('president@iset.tn')->setRoles(['ROLE_PRESIDENT'])
                   ->setIsVerified(true)->setDtype('president')
                   ->setPassword($this->hasher->hashPassword($president1, 'president123'));
        $manager->persist($president1);

        $president2 = new User();
        $president2->setFirstname('Amine')->setLastname('Gharbi')
                   ->setEmail('president2@iset.tn')->setRoles(['ROLE_PRESIDENT'])
                   ->setIsVerified(true)->setDtype('president')
                   ->setPassword($this->hasher->hashPassword($president2, 'president123'));
        $manager->persist($president2);

        // ---- STUDENTS ----
        $students = [];
        $studentData = [
            ['Sarra', 'Trabelsi', 'etudiant@iset.tn'],
            ['Yassine', 'Mansouri', 'etudiant2@iset.tn'],
            ['Lina', 'Bouaziz', 'etudiant3@iset.tn'],
            ['Khalil', 'Nafeti', 'etudiant4@iset.tn'],
            ['Rania', 'Hamdi', 'etudiant5@iset.tn'],
        ];
        foreach ($studentData as [$fn, $ln, $email]) {
            $s = new User();
            $s->setFirstname($fn)->setLastname($ln)->setEmail($email)
              ->setRoles(['ROLE_USER'])->setIsVerified(true)->setDtype('student')
              ->setPassword($this->hasher->hashPassword($s, 'etudiant123'));
            $manager->persist($s);
            $students[] = $s;
        }

        // ---- CLUBS ----
        $club1 = new Club();
        $club1->setName('Club Tech ISET')
              ->setDescription('Club dédié au développement web, mobile et intelligence artificielle.')
              ->setDomain('Technologie')->setStatus('approved')
              ->setCreatedAt(new \DateTimeImmutable('-2 months'))
              ->setProposedBy($president1);
        $manager->persist($club1);

        $club2 = new Club();
        $club2->setName('Club Scientifique')
              ->setDescription('Exploration des sciences, mathématiques et physique appliquée.')
              ->setDomain('Sciences')->setStatus('approved')
              ->setCreatedAt(new \DateTimeImmutable('-1 month'))
              ->setProposedBy($president2);
        $manager->persist($club2);

        $club3 = new Club();
        $club3->setName('Club Entrepreneuriat')
              ->setDescription('Pour les futurs entrepreneurs : business plan, startups, pitching.')
              ->setDomain('Business')->setStatus('pending')
              ->setCreatedAt(new \DateTimeImmutable('-1 week'))
              ->setProposedBy($president1);
        $manager->persist($club3);

        // ---- CLUB MEMBERS ----
        foreach ([$students[0], $students[1], $students[2]] as $s) {
            $m = new ClubMember();
            $m->setUser($s)->setClub($club1)->setRole('member')
              ->setJoinedAt(new \DateTimeImmutable('-3 weeks'));
            $manager->persist($m);
        }
        foreach ([$students[3], $students[4]] as $s) {
            $m = new ClubMember();
            $m->setUser($s)->setClub($club2)->setRole('member')
              ->setJoinedAt(new \DateTimeImmutable('-2 weeks'));
            $manager->persist($m);
        }

        // ---- EVENEMENTS ----
        $event1 = new Evenement();
        $event1->setNomEvenement('Hackathon Web 2026')
               ->setDescription('48h pour créer une application web innovante.')
               ->setDateDebut(new \DateTime('+2 weeks'))
               ->setDateFin(new \DateTime('+2 weeks +2 days'))
               ->setLieu('Salle Informatique A')
               ->setStatus('approved')
               ->setCreatedAt(new \DateTimeImmutable())
               ->setClub($club1);
        $manager->persist($event1);

        $event2 = new Evenement();
        $event2->setNomEvenement('Atelier Intelligence Artificielle')
               ->setDescription('Introduction au Machine Learning avec Python.')
               ->setDateDebut(new \DateTime('+1 week'))
               ->setDateFin(new \DateTime('+1 week +3 hours'))
               ->setLieu('Amphi 2')
               ->setStatus('approved')
               ->setCreatedAt(new \DateTimeImmutable())
               ->setClub($club1);
        $manager->persist($event2);

        $event3 = new Evenement();
        $event3->setNomEvenement('Conférence Sciences & Innovation')
               ->setDescription('Conférence sur les dernières avancées scientifiques.')
               ->setDateDebut(new \DateTime('+3 weeks'))
               ->setDateFin(new \DateTime('+3 weeks +4 hours'))
               ->setLieu('Salle de conférences')
               ->setStatus('pending')
               ->setCreatedAt(new \DateTimeImmutable())
               ->setClub($club2);
        $manager->persist($event3);

        // ---- RECRUTEMENTS ----
        $rec1 = new Recrutement();
        $rec1->setTitle('Développeur Web Frontend')
             ->setDescription('Nous cherchons un développeur passionné par React et Symfony.')
             ->setRequirements('HTML, CSS, JavaScript, notions de PHP')
             ->setDeadline(new \DateTime('+3 weeks'))
             ->setStatus('open')
             ->setCreatedAt(new \DateTimeImmutable())
             ->setClub($club1);
        $manager->persist($rec1);

        $rec2 = new Recrutement();
        $rec2->setTitle('Responsable Communication')
             ->setDescription('Gérer les réseaux sociaux et la communication du club.')
             ->setRequirements('Créativité, maîtrise des réseaux sociaux, Canva')
             ->setDeadline(new \DateTime('+2 weeks'))
             ->setStatus('open')
             ->setCreatedAt(new \DateTimeImmutable())
             ->setClub($club1);
        $manager->persist($rec2);

        $rec3 = new Recrutement();
        $rec3->setTitle('Animateur Scientifique')
             ->setDescription('Animer des ateliers de vulgarisation scientifique.')
             ->setRequirements('Licence en sciences, pédagogie, communication')
             ->setDeadline(new \DateTime('+4 weeks'))
             ->setStatus('open')
             ->setCreatedAt(new \DateTimeImmutable())
             ->setClub($club2);
        $manager->persist($rec3);

        // ---- CANDIDATURES ----
        $cand1 = new Candidature();
        $cand1->setUser($students[0])->setRecrutement($rec1)
              ->setMessage('Je suis passionnée par le développement web.')
              ->setCvFilename('cv_sarra.pdf')
              ->setStatus('pending')
              ->setSubmittedAt(new \DateTimeImmutable('-2 days'));
        $manager->persist($cand1);

        $cand2 = new Candidature();
        $cand2->setUser($students[1])->setRecrutement($rec1)
              ->setMessage('Développeur junior avec 1 an d\'expérience en Symfony.')
              ->setCvFilename('cv_yassine.pdf')
              ->setStatus('accepted')
              ->setSubmittedAt(new \DateTimeImmutable('-5 days'));
        $manager->persist($cand2);

        $cand3 = new Candidature();
        $cand3->setUser($students[2])->setRecrutement($rec2)
              ->setMessage('Je gère les réseaux sociaux de mon lycée depuis 2 ans.')
              ->setCvFilename('cv_lina.pdf')
              ->setStatus('pending')
              ->setSubmittedAt(new \DateTimeImmutable('-1 day'));
        $manager->persist($cand3);

        // ---- RECLAMATIONS ----
        $recl1 = new Reclamation();
        $recl1->setUser($students[3])
              ->setSubject('Problème d\'accès à l\'espace club')
              ->setMessage('Je n\'arrive pas à accéder à la page de mon club depuis 2 jours.')
              ->setStatus('pending')
              ->setCreatedAt(new \DateTimeImmutable('-3 days'));
        $manager->persist($recl1);

        $recl2 = new Reclamation();
        $recl2->setUser($students[4])
              ->setSubject('Candidature non reçue')
              ->setMessage('J\'ai soumis ma candidature il y a une semaine mais pas de réponse.')
              ->setStatus('in_progress')
              ->setCreatedAt(new \DateTimeImmutable('-1 week'));
        $manager->persist($recl2);

        $recl3 = new Reclamation();
        $recl3->setUser($students[0])
              ->setSubject('Événement annulé sans notification')
              ->setMessage('L\'atelier du 15 mai a été annulé sans nous prévenir.')
              ->setStatus('resolved')
              ->setCreatedAt(new \DateTimeImmutable('-2 weeks'));
        $manager->persist($recl3);

        $manager->flush();

        echo "✅ Fixtures: 1 admin, 2 presidents, 5 students, 3 clubs, 3 events, 3 recrutements, 3 candidatures, 3 reclamations\n";
    }
}
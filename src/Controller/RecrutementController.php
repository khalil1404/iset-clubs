<?php

namespace App\Controller;

use App\Entity\Recrutement;
use App\Entity\Candidature;
use App\Repository\RecrutementRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/recruitment')]
class RecrutementController extends AbstractController
{
    #[Route('/', name: 'app_recruitment_index')]
    public function index(RecrutementRepository $repo): Response
    {
        return $this->render('recruitment/index.html.twig', [
            'offers' => $repo->findBy(['status' => 'open'], ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('/{id}', name: 'app_recruitment_show', requirements: ['id' => '\d+'])]
    public function show(Recrutement $recrutement): Response
    {
        return $this->render('recruitment/show.html.twig', [
            'offer' => $recrutement
        ]);
    }

    #[Route('/new', name: 'app_recruitment_new')]
    #[IsGranted('ROLE_PRESIDENT')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $club = $em->getRepository(\App\Entity\Club::class)
                       ->findOneBy(['proposedBy' => $this->getUser()]);

            $offer = new Recrutement();
            $offer->setTitle($request->request->get('title'))
                  ->setDescription($request->request->get('description'))
                  ->setRequirements($request->request->get('requirements'))
                  ->setDeadline(new \DateTime($request->request->get('deadline')))
                  ->setStatus('open')
                  ->setCreatedAt(new \DateTime())
                  ->setClub($club);

            $em->persist($offer);
            $em->flush();
            $this->addFlash('success', 'Offre publiée !');
            return $this->redirectToRoute('app_recruitment_index');
        }
        return $this->render('recruitment/new.html.twig');
    }

    #[Route('/{id}/apply', name: 'app_recruitment_apply', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function apply(
        Recrutement $recrutement,
        Request $request,
        EntityManagerInterface $em,
        CandidatureRepository $candRepo,
        SluggerInterface $slugger
    ): Response {
        $existing = $candRepo->findOneBy([
            'user' => $this->getUser(),
            'recrutement' => $recrutement
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Déjà postulé.');
            return $this->redirectToRoute('app_recruitment_show',
                ['id' => $recrutement->getId()]);
        }

        $cand = new Candidature();
        $cand->setUser($this->getUser())
             ->setRecrutement($recrutement)
             ->setMessage($request->request->get('message'))
             ->setStatus('pending')
             ->setSubmittedAt(new \DateTime());

        $cv = $request->files->get('cv');
        if ($cv) {
            $fn = 'cv-'.uniqid().'.'.$cv->guessExtension();
            $cv->move($this->getParameter('cv_directory'), $fn);
            $cand->setCvFilename($fn);
        }

        $em->persist($cand);
        $em->flush();
        $this->addFlash('success', 'Candidature envoyée !');
        return $this->redirectToRoute('app_recruitment_show',
            ['id' => $recrutement->getId()]);
    }

    #[Route('/my-applications', name: 'app_my_applications')]
    #[IsGranted('ROLE_USER')]
    public function myApplications(CandidatureRepository $candRepo): Response
    {
        return $this->render('recruitment/my_applications.html.twig', [
            'applications' => $candRepo->findBy(['user' => $this->getUser()])
        ]);
    }

    #[Route('/president/candidatures', name: 'app_president_candidatures')]
    #[IsGranted('ROLE_PRESIDENT')]
    public function presidentCandidatures(
        CandidatureRepository $candRepo,
        EntityManagerInterface $em
    ): Response {
        $club = $em->getRepository(\App\Entity\Club::class)
                   ->findOneBy(['proposedBy' => $this->getUser()]);

        $candidatures = [];
        if ($club) {
            $offers = $em->getRepository(Recrutement::class)
                         ->findBy(['club' => $club]);
            foreach ($offers as $offer) {
                foreach ($candRepo->findBy(['recrutement' => $offer]) as $c) {
                    $candidatures[] = $c;
                }
            }
        }

        return $this->render('recruitment/president_candidatures.html.twig', [
            'candidatures' => $candidatures
        ]);
    }
}
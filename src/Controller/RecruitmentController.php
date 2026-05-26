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
        $offers = $repo->findBy(['status' => 'open'], ['createdAt' => 'DESC']);
        return $this->render('recruitment/index.html.twig', ['offers' => $offers]);
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
            $clubRepo = $em->getRepository(\App\Entity\Club::class);
            $club = $clubRepo->findOneBy(['proposedBy' => $this->getUser()]);

            $offer = new Recrutement();
            $offer->setTitle($request->request->get('title'));
            $offer->setDescription($request->request->get('description'));
            $offer->setRequirements($request->request->get('requirements'));
            $offer->setDeadline(new \DateTime($request->request->get('deadline')));
            $offer->setStatus('open');
            $offer->setCreatedAt(new \DateTime());
            $offer->setClub($club);

            $em->persist($offer);
            $em->flush();
            $this->addFlash('success', 'Offre de recrutement publiée !');
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
            $this->addFlash('warning', 'Vous avez déjà postulé.');
            return $this->redirectToRoute('app_recruitment_show', ['id' => $recrutement->getId()]);
        }

        $candidature = new Candidature();
        $candidature->setUser($this->getUser());
        $candidature->setRecrutement($recrutement);
        $candidature->setMessage($request->request->get('message'));
        $candidature->setStatus('pending');
        $candidature->setSubmittedAt(new \DateTime());

        $cvFile = $request->files->get('cv');
        if ($cvFile) {
            $filename = 'cv-'.$slugger->slug($this->getUser()->getFullName()).'-'.uniqid().'.'.$cvFile->guessExtension();
            $cvFile->move($this->getParameter('cv_directory'), $filename);
            $candidature->setCvFilename($filename);
        }

        $em->persist($candidature);
        $em->flush();
        $this->addFlash('success', 'Candidature envoyée !');
        return $this->redirectToRoute('app_recruitment_show', ['id' => $recrutement->getId()]);
    }

    #[Route('/my-applications', name: 'app_my_applications')]
    #[IsGranted('ROLE_USER')]
    public function myApplications(CandidatureRepository $candRepo): Response
    {
        $applications = $candRepo->findBy(['user' => $this->getUser()]);
        return $this->render('recruitment/my_applications.html.twig', [
            'applications' => $applications
        ]);
    }
}
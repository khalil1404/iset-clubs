<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Participation;
use App\Repository\EvenementRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/events')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_event_index')]
    public function index(EvenementRepository $repo): Response
    {
        $events = $repo->findBy(['status' => 'approved'], ['dateDebut' => 'ASC']);
        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/{id}', name: 'app_event_show', requirements: ['id' => '\d+'])]
    public function show(Evenement $event, ParticipationRepository $partRepo): Response
    {
        $isRegistered = false;
        if ($this->getUser()) {
            $isRegistered = (bool) $partRepo->findOneBy([
                'user' => $this->getUser(),
                'evenement' => $event
            ]);
        }
        $participants = $partRepo->findBy(['evenement' => $event]);
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'isRegistered' => $isRegistered,
            'participants' => $participants
        ]);
    }

    #[Route('/new', name: 'app_event_new')]
    #[IsGranted('ROLE_PRESIDENT')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        if ($request->isMethod('POST')) {
            $event = new Evenement();
            $event->setNomEvenement($request->request->get('nomEvenement'));
            $event->setDescription($request->request->get('description'));
            $event->setLieu($request->request->get('lieu'));
            $event->setDateDebut(new \DateTime($request->request->get('dateDebut')));
            $event->setDateFin(new \DateTime($request->request->get('dateFin')));
            $event->setStatus('pending');
            $event->setCreatedAt(new \DateTime());

            $clubRepo = $em->getRepository(\App\Entity\Club::class);
            $club = $clubRepo->findOneBy(['proposedBy' => $this->getUser()]);
            $event->setClub($club);

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $filename = $slugger->slug('event').'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('events_directory'), $filename);
                $event->setImage($filename);
            }

            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Événement soumis ! En attente de validation.');
            return $this->redirectToRoute('app_event_index');
        }
        return $this->render('event/new.html.twig');
    }

    #[Route('/{id}/register', name: 'app_event_register', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function register(
        Evenement $event,
        EntityManagerInterface $em,
        ParticipationRepository $partRepo
    ): Response {
        $existing = $partRepo->findOneBy([
            'user' => $this->getUser(),
            'evenement' => $event
        ]);

        if (!$existing) {
            $participation = new Participation();
            $participation->setUser($this->getUser());
            $participation->setEvenement($event);
            $participation->setRegisteredAt(new \DateTime());
            $em->persist($participation);
            $em->flush();
            $this->addFlash('success', 'Inscription confirmée !');
        } else {
            $this->addFlash('warning', 'Vous êtes déjà inscrit.');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/{id}/unregister', name: 'app_event_unregister', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function unregister(
        Evenement $event,
        EntityManagerInterface $em,
        ParticipationRepository $partRepo
    ): Response {
        $participation = $partRepo->findOneBy([
            'user' => $this->getUser(),
            'evenement' => $event
        ]);

        if ($participation) {
            $em->remove($participation);
            $em->flush();
            $this->addFlash('success', 'Désinscription effectuée.');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }
}
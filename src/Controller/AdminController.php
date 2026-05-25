<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Repository\UserRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(
        ClubRepository $clubRepo,
        UserRepository $userRepo,
        EvenementRepository $eventRepo
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'totalClubs'    => count($clubRepo->findAll()),
            'pendingClubs'  => count($clubRepo->findBy(['status' => 'pending'])),
            'totalUsers'    => count($userRepo->findAll()),
            'totalEvents'   => count($eventRepo->findAll()),
            'pendingEvents' => count($eventRepo->findBy(['status' => 'pending'])),
            'recentClubs'   => $clubRepo->findBy([], ['id' => 'DESC'], 5),
            'recentUsers'   => $userRepo->findBy([], ['id' => 'DESC'], 5),
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepo->findAll(),
        ]);
    }

    #[Route('/clubs', name: 'app_admin_clubs')]
    public function clubs(ClubRepository $clubRepo): Response
    {
        return $this->render('admin/clubs.html.twig', [
            'clubs' => $clubRepo->findAll(),
        ]);
    }

    #[Route('/clubs/{id}/approve', name: 'app_admin_club_approve')]
    public function approveClub(
        int $id,
        ClubRepository $clubRepo,
        EntityManagerInterface $em
    ): Response {
        $club = $clubRepo->find($id);
        if ($club) {
            $club->setStatus('approved');
            $em->flush();
            $this->addFlash('success', "Club approuvé !");
        }
        return $this->redirectToRoute('app_admin_clubs');
    }

    #[Route('/clubs/{id}/reject', name: 'app_admin_club_reject')]
    public function rejectClub(
        int $id,
        ClubRepository $clubRepo,
        EntityManagerInterface $em
    ): Response {
        $club = $clubRepo->find($id);
        if ($club) {
            $club->setStatus('rejected');
            $em->flush();
            $this->addFlash('warning', "Club refusé.");
        }
        return $this->redirectToRoute('app_admin_clubs');
    }

    #[Route('/events', name: 'app_admin_events')]
    public function events(EvenementRepository $eventRepo): Response
    {
        return $this->render('admin/events.html.twig', [
            'events' => $eventRepo->findAll(),
        ]);
    }

    #[Route('/events/{id}/approve', name: 'app_admin_event_approve')]
    public function approveEvent(
        int $id,
        EvenementRepository $eventRepo,
        EntityManagerInterface $em
    ): Response {
        $event = $eventRepo->find($id);
        if ($event) {
            $event->setStatus('approved');
            $em->flush();
            $this->addFlash('success', "Événement approuvé !");
        }
        return $this->redirectToRoute('app_admin_events');
    }

    #[Route('/events/{id}/reject', name: 'app_admin_event_reject')]
    public function rejectEvent(
        int $id,
        EvenementRepository $eventRepo,
        EntityManagerInterface $em
    ): Response {
        $event = $eventRepo->find($id);
        if ($event) {
            $event->setStatus('rejected');
            $em->flush();
            $this->addFlash('warning', "Événement refusé.");
        }
        return $this->redirectToRoute('app_admin_events');
    }
}
```php
<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Repository\UserRepository;
use App\Repository\EvenementRepository;
use App\Repository\CandidatureRepository;
use App\Repository\ReclamationRepository;
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
        EvenementRepository $eventRepo,
        CandidatureRepository $candRepo,
        ReclamationRepository $reclRepo
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'totalClubs'          => count($clubRepo->findAll()),
            'pendingClubs'        => count($clubRepo->findBy(['status' => 'pending'])),

            'totalUsers'          => count($userRepo->findAll()),

            'totalEvents'         => count($eventRepo->findAll()),
            'pendingEvents'       => count($eventRepo->findBy(['status' => 'pending'])),

            'totalCandidatures'   => count($candRepo->findAll()),
            'pendingCandidatures' => count($candRepo->findBy(['status' => 'pending'])),

            'totalReclamations'   => count($reclRepo->findAll()),
            'pendingReclamations' => count($reclRepo->findBy(['status' => 'pending'])),

            'recentClubs'         => $clubRepo->findBy([], ['id' => 'DESC'], 5),
            'recentUsers'         => $userRepo->findBy([], ['id' => 'DESC'], 5),
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
            $this->addFlash('success', 'Club approuvé !');
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
            $this->addFlash('warning', 'Club refusé.');
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
            $this->addFlash('success', 'Événement approuvé !');
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
            $this->addFlash('warning', 'Événement refusé.');
        }

        return $this->redirectToRoute('app_admin_events');
    }

    #[Route('/candidatures', name: 'app_admin_candidatures')]
    public function candidatures(CandidatureRepository $candRepo): Response
    {
        return $this->render('admin/candidatures.html.twig', [
            'candidatures' => $candRepo->findAll(),
        ]);
    }

    #[Route('/candidatures/{id}/accept', name: 'app_admin_candidature_accept')]
    public function acceptCandidature(
        int $id,
        CandidatureRepository $candRepo,
        EntityManagerInterface $em
    ): Response {
        $cand = $candRepo->find($id);

        if ($cand) {
            $cand->setStatus('accepted');
            $em->flush();
            $this->addFlash('success', 'Candidature acceptée !');
        }

        return $this->redirectToRoute('app_admin_candidatures');
    }

    #[Route('/candidatures/{id}/reject', name: 'app_admin_candidature_reject')]
    public function rejectCandidature(
        int $id,
        CandidatureRepository $candRepo,
        EntityManagerInterface $em
    ): Response {
        $cand = $candRepo->find($id);

        if ($cand) {
            $cand->setStatus('rejected');
            $em->flush();
            $this->addFlash('warning', 'Candidature refusée.');
        }

        return $this->redirectToRoute('app_admin_candidatures');
    }

    #[Route('/reclamations', name: 'app_admin_reclamations')]
    public function reclamations(ReclamationRepository $repo): Response
    {
        return $this->render('admin/reclamations.html.twig', [
            'reclamations' => $repo->findAll(),
        ]);
    }

    #[Route('/reclamations/{id}/resolve', name: 'app_admin_reclamation_resolve')]
    public function resolveReclamation(
        int $id,
        ReclamationRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $r = $repo->find($id);

        if ($r) {
            $r->setStatus('resolved');
            $em->flush();
            $this->addFlash('success', 'Réclamation résolue !');
        }

        return $this->redirectToRoute('app_admin_reclamations');
    }
}
```

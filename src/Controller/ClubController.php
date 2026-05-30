<?php

namespace App\Controller;
use App\Entity\ClubMember;
use App\Repository\ClubMemberRepository;
use App\Entity\Club;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/clubs')]
class ClubController extends AbstractController
{
    #[Route('/', name: 'app_club_index')]
    public function index(ClubRepository $clubRepository): Response
    {
        $clubs = $clubRepository->findBy(['status' => 'approved']);
        return $this->render('club/index.html.twig', [
            'clubs' => $clubs,
        ]);
    }

    #[Route('/{id}', name: 'app_club_show', requirements: ['id' => '\d+'])]
    public function show(Club $club): Response
    {
        return $this->render('club/show.html.twig', [
            'club' => $club,
        ]);
    }

    #[Route('/new', name: 'app_club_new')]
    #[IsGranted('ROLE_PRESIDENT')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                $originalFilename = pathinfo(
                    $logoFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();
                $logoFile->move(
                    $this->getParameter('logos_directory'),
                    $newFilename
                );
                $club->setLogo($newFilename);
            }

            $club->setStatus('pending');
            $club->setCreatedAt(new \DateTime());
            $club->setProposedBy($this->getUser());
            $em->persist($club);
            $em->flush();

            $this->addFlash('success', 'Club créé ! En attente de validation.');
            return $this->redirectToRoute('app_my_club');
        }

        return $this->render('club/new.html.twig', ['form' => $form]);
    }

    #[Route('/my-club', name: 'app_my_club')]
    #[IsGranted('ROLE_PRESIDENT')]
    public function myClub(ClubRepository $clubRepository): Response
    {
        $club = $clubRepository->findOneBy([
            'proposedBy' => $this->getUser()
        ]);
        return $this->render('club/my_club.html.twig', ['club' => $club]);
    }
    #[Route('/{id}/join', name: 'app_club_join', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function join(
    Club $club,
    EntityManagerInterface $em,
    ClubMemberRepository $memberRepo
): Response {
    $user = $this->getUser();
    $existing = $memberRepo->findOneBy(['user' => $user, 'club' => $club]);

    if (!$existing) {
        $member = new ClubMember();
        $member->setUser($user);
        $member->setClub($club);
        $member->setRole('member');
        $member->setJoinedAt(new \DateTimeImmutable());
        $em->persist($member);
        $em->flush();
        $this->addFlash('success', 'Vous avez rejoint le club !');
    } else {
        $this->addFlash('warning', 'Vous êtes déjà membre de ce club.');
    }

    return $this->redirectToRoute('app_club_show', ['id' => $club->getId()]);
}

#[Route('/{id}/leave', name: 'app_club_leave', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function leave(
    Club $club,
    EntityManagerInterface $em,
    ClubMemberRepository $memberRepo
): Response {
    $member = $memberRepo->findOneBy([
        'user' => $this->getUser(),
        'club' => $club
    ]);

    if ($member) {
        $em->remove($member);
        $em->flush();
        $this->addFlash('success', 'Vous avez quitté le club.');
    }

    return $this->redirectToRoute('app_club_show', ['id' => $club->getId()]);
}
#[Route('/my-clubs', name: 'app_my_clubs')]
#[IsGranted('ROLE_USER')]
public function myClubs(ClubMemberRepository $memberRepo): Response
{
    $memberships = $memberRepo->findBy(['user' => $this->getUser()]);
    return $this->render('club/my_clubs.html.twig', [
        'memberships' => $memberships
    ]);
}
}
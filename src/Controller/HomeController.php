<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ClubRepository $clubRepository,
        EvenementRepository $evenementRepository
    ): Response {
        $clubs = $clubRepository->findBy(
            ['status' => 'approved'],
            ['id' => 'DESC'],
            3
        );

        return $this->render('home/index.html.twig', [
            'clubs' => $clubs,
            'events' => [],
        ]);
    }
}
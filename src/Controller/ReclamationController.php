<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reclamations')]
#[IsGranted('ROLE_USER')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index')]
    public function index(ReclamationRepository $repo): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $repo->findBy(['user' => $this->getUser()])
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $r = new Reclamation();
            $r->setUser($this->getUser())
              ->setSubject($request->request->get('subject'))
              ->setMessage($request->request->get('message'))
              ->setStatus('pending')
              ->setCreatedAt(new \DateTime());
            $em->persist($r);
            $em->flush();
            $this->addFlash('success', 'Réclamation envoyée !');
            return $this->redirectToRoute('app_reclamation_index');
        }
        return $this->render('reclamation/new.html.twig');
    }
}
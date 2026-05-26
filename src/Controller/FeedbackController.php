<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Repository\EvenementRepository;
use App\Repository\FeedbackRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/feedback')]
#[IsGranted('ROLE_USER')]
class FeedbackController extends AbstractController
{
    #[Route('/event/{id}', name: 'app_feedback_new', methods: ['POST'])]
    public function new(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        EvenementRepository $eventRepo,
        FeedbackRepository $feedbackRepo,
        ParticipationRepository $partRepo
    ): Response {
        $event = $eventRepo->find($id);

        $participated = $partRepo->findOneBy([
            'user' => $this->getUser(),
            'evenement' => $event
        ]);

        if (!$participated) {
            $this->addFlash('error', 'Vous devez être inscrit pour laisser un feedback.');
            return $this->redirectToRoute('app_event_show', ['id' => $id]);
        }

        $existing = $feedbackRepo->findOneBy([
            'user' => $this->getUser(),
            'event' => $event
        ]);

        if (!$existing) {
            $feedback = new Feedback();
            $feedback->setUser($this->getUser());
            $feedback->setEvent($event);
            $feedback->setContent($request->request->get('content'));
            $feedback->setRating((int) $request->request->get('rating'));
            $feedback->setCreatedAt(new \DateTime());
            $em->persist($feedback);
            $em->flush();
            $this->addFlash('success', 'Feedback envoyé !');
        } else {
            $this->addFlash('warning', 'Vous avez déjà laissé un feedback.');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $id]);
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $firstname = trim($request->request->get('firstname', ''));
            $lastname = trim($request->request->get('lastname', ''));

            if ($firstname !== '') {
                $user->setFirstname($firstname);
            }
            if ($lastname !== '') {
                $user->setLastname($lastname);
            }

            $photoFile = $request->files->get('photo');
            if ($photoFile && $photoFile->isValid()) {
                $safeFilename = $slugger->slug($user->getFirstname());
                $filename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('profiles_directory'),
                    $filename
                );
                $user->setProfilePicture($filename);
            }

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', ['user' => $user]);
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MicroPostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function show(
        User $user,
        MicroPostRepository $posts
    ): Response {
        return $this->render('pages/profile/show.html.twig', [
            'user' => $user,
            'posts' => $posts->findAll(
                $user
            )
        ]);
    }

    #[Route('/profile/{id}/follows', name: 'app_profile_follows')]
    public function follows(User $user): Response
    {
        return $this->render('pages/profile/follows.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profile/{id}/followers', name: 'app_profile_followers')]
    public function followers(User $user): Response
    {
        return $this->render('pages/profile/followers.html.twig', [
            'user' => $user
        ]);
    }
}
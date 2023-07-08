<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(): Response
    {
        return $this->render('pages/micro_post/index.html.twig', [
            'controller_name' => 'MicroPostController',
        ]);
    }

    #[Route('micro-post/ajouter', name:'app_micro_post_add')]
    public function micro_post_add(HttpFoundationRequest $request, EntityManagerInterface $manager) : Response
    {
        $microPost = new MicroPost;

        $form = $this->createForm(MicroPostType::class,$microPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() &&  $form->isValid()){
            $microPost= $form->getData();
            
            $manager->persist($microPost);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre post à bient été posté !'
            );

            return $this->redirectToRoute('app_home');

        }

        return $this->renderForm('pages/micro_post/add.html.twig',[
            'form'=> $form
        ]);


        

    }
}

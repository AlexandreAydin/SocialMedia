<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post', methods:['GET'])]
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('pages/micro_post/index.html.twig', [
            'posts' => $posts->findAll(),
        ]);
    }

    #[Route('/micro-post/{id}', name: 'app_micro_post_show', methods: ['GET','POST'])]
    public function show(MicroPost $microPost): Response
    {
        return $this->render('pages/micro_post/show.html.twig',[
            "microPost" => $microPost
        ]);
    }


    
    #[Route('micro-post/ajouter', name:'app_micro_post_add', methods:['GET','POST'])]
    public function micro_post_add(Request $request, EntityManagerInterface $manager) : Response
    {
        $microPost = new MicroPost;

        $form = $this->createForm(MicroPostType::class,$microPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() &&  $form->isValid()){
            $microPost= $form->getData();
            
            $manager->persist($microPost);
            $manager->flush();

            /** Oublie pas de l'ajouter dans la vue twig */
            $this->addFlash(
                'success',
                'Votre post à bient été posté !'
            );

            return $this->redirectToRoute('app_micro_post');

        }

        return $this->renderForm('pages/micro_post/add.html.twig',[
            'form'=> $form->createView()
        ]);
    }

    #[Route('micro-post/{id}/edit', name:'app_micro_post_edit', methods:['GET','POST'])]
    public function micro_post_edit(MicroPost $microPost, 
    Request $request,
    EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(MicroPostType::class,$microPost);

        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){
            $microPost= $form->getData();
            
            $manager->persist($microPost);
            $manager->flush();


            $this->addFlash(
                'success',
                'Votre post à bient été mise à jour'
            );

            return $this->redirectToRoute('app_home');

        }

        return $this->render('pages/micro_post/edit.html.twig',[
            'form'=> $form->createView()
        ]);
 
    }

    #[Route('micro-post/{id}/suppression', 'app_micro_post_delete', methods:['GET'] )]
    public function micro_post_delete(EntityManagerInterface $manager, MicroPost $microPost): Response
    {
        $manager->remove($microPost);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre post à bient été supprimer'
        );

        return $this->redirectToRoute('app_home');
    }

}

<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post', methods:['GET'])]
    public function index(MicroPostRepository $posts): Response
    {
    
        return $this->render('pages/micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments(),
        ]);
    }

    #[Route('/micro-post/{id}', name: 'app_micro_post_show', methods: ['GET','POST'])]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function show(MicroPost $post): Response
    {
        return $this->render('pages/micro_post/show.html.twig',[
            "post" => $post
        ]);
    }


    
    #[Route('micro-post/ajouter', name:'app_micro_post_add',priority: 2)]
    #[IsGranted('ROLE_WRITTER')]
    public function micro_post_add(Request $request, EntityManagerInterface $manager) : Response
    {

        $post = new MicroPost;

        $form = $this->createForm(MicroPostType::class,$post);

        $form->handleRequest($request);
        if ($form->isSubmitted() &&  $form->isValid()){
            $post= $form->getData();
            $post->setAuthor($this->getUser());
            
            $manager->persist($post);
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
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function micro_post_edit(MicroPost $post, 
    Request $request,
    EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(MicroPostType::class,$post);

        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){
            $post= $form->getData();
            
            $manager->persist($post);
            $manager->flush();


            $this->addFlash(
                'success',
                'Votre post à bient été mise à jour'
            );

            return $this->redirectToRoute('app_micro_post');

        }

        return $this->render('pages/micro_post/edit.html.twig',[
            'form'=> $form->createView(),
            'post'=> $post
        ]);
 
    }

    #[Route('micro-post/{id}/suppression', name: 'app_micro_post_delete', methods:['GET'] )]
    public function micro_post_delete(EntityManagerInterface $manager, MicroPost $post): Response
    {
        $manager->remove($post);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre post à bient été supprimer'
        );

        return $this->redirectToRoute('app_home');
    }


    #[Route('micro-post/{id}/comment', name:'app_micro_post_comment', methods:['GET','POST'])]
    #[IsGranted('ROLE_COMMENTER')]
    public function add_comment(MicroPost $post, 
    Request $request,
    EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CommentType::class,new Comment());

        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){
            $comment= $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());

            
            $manager->persist($comment);
            $manager->flush();


            $this->addFlash(
                'success',
                'Votre post à bient été mise à jour'
            );

            return $this->redirectToRoute('app_micro_post_show', ['id' => $post->getId()]);


        }

        return $this->render('pages/micro_post/comment.html.twig',[
            'form'=> $form->createView(),
            'post' => $post
        ]);
 
    }

}

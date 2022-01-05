<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
class ArticleController extends AbstractController
{
    /**
     * @return Response
     *
     * @Route("/article", name="article_list")
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', array('articles'=>$articles));
    }
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/article/new", name="new_article")
     * Method({"GET", "POST"})
     */
    public function new(Request $request):Response
    {
        $article = new Article();
        $form = $this->createFormBuilder($article)
                ->add('title',TextType::class,array('attr'=>array('class'=>'form-control')))
                ->add('body',TextareaType::class,array('required'=>false,'attr'=>array('class'=>'form-control')))
                ->add('save',SubmitType::class,array('label'=>'Create','attr'=>array('class'=>'btn btn-primary mt-3')))
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/new.html.twig', array('form'=>$form->createView()));
    }
    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * Method({"DELETE"})
     */
    public function delete($id):Response
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('article_list',array('message'=>'Deleted!'));
    }
    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id)
    {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $form = $this->createFormBuilder($article)
                ->add('title',TextType::class,array('attr'=>array('class'=>'form-control')))
                ->add('body',TextareaType::class,array('required'=>false,'attr'=>array('class'=>'form-control')))
                ->add('save',SubmitType::class,array('label'=>'Edit','attr'=>array('class'=>'btn btn-primary mt-3')))
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/edit.html.twig', array('form'=>$form->createView()));
    }
    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id):Response
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        return $this->render('article/show.html.twig',array('article'=>$article));
    }
    // /**
    //  * @Route("/article/save")
    //  */
    // public function save()
    // {
    //     $entityManager = $this->getDoctrine()->getManager();

    //     $article = new Article();
    //     $article->setTitle('Article 2');
    //     $article->setBody('This is body 3');
    //     $entityManager->persist($article);
    //     $entityManager->flush();
    //     return new Response('Saved an article with the id of '.$article->getId());
    // }
}

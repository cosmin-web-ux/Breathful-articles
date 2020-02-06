<?php

namespace App\Controller;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     * @Method({"GET"})
     */
    public function index()
    {
        // return new Response('<html><body>Hello</body></html>');

        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/new", name="new_article")
     * Method({"GET", "POST"})
     */
    public function new(Request $request)
    {
        $article = new Article();
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('body', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Create', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     *
     * @param mixed $id
     */
    public function edit(Request $request, $id)
    {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('body', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Update', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->getForm()
      ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/save")
     */
    public function save()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $article = new Article();
        $article->setTitle('Article Three');
        $article->setBody('This is the body for article three');

        $entityManager->persist($article);
        $entityManager->flush();

        return new Response('Saved an article with the id of '.$article->getId());
    }

    /**
     * @Route("/article/{id}", name="article_show")
     *
     * @param mixed $id
     */
    public function show($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return $this->render('articles/show.html.twig', ['article' => $article]);
        // return new Response('Check out this great article: '.$article->getTitle().$article->getBody());
    }

    /**
     * @Route("/article/delete/{id}")
     * @Method({"DELETE"})
     *
     * @param mixed $id
     */
    public function delete(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }
}

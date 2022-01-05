<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @param ProductRepository $productRepository
     *
     * @return Response
     *
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @param ProductRepository $productRepository
     * @param $search
     *
     * @return Response
     *
     * @Route("/search/{search}", name="search", methods={"GET"})
     */
    public function search( ProductRepository $productRepository, $search): Response
    {
        dd($products = $productRepository->search($search));

        return $this->render('product/search.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/new", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        // $form = $this->createForm(ProductType::class, $product);
        $form = $this->createFormBuilder($product)
                ->add('productname',TextType::class,array('label'=>'Product Name','attr'=>array('class'=>'form-control')))
                ->add('description',TextareaType::class,array('label'=>'Description','required'=>false,'attr'=>array('class'=>'form-control')))
                ->add('save',SubmitType::class,array('label'=>'Create','attr'=>array('class'=>'btn btn-primary mt-3')))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param  Product $product
     *
     * @return Response
     *
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @Route("/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller\Frontend;

use App\Repository\PostRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\PostRepository $postRepository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $posts = $postRepository->findAll();
        $posts = $paginator->paginate($posts, $request->query->getInt('page', 1), 1);

        return $this->render('index.html.twig', compact('posts'));
    }
}
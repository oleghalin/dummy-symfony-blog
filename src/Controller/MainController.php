<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, PostRepository $postRepository)
    {
        $posts = $postRepository->paginate($request);

        $totalPosts = $posts->count();
        $pagesCount = (int) ceil($totalPosts / $postRepository->perPage);

        return $this->render('index.html.twig', compact('posts', 'pagesCount'));
    }
}
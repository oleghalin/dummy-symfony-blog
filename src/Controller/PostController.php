<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="post_index")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();
        return $this->render('blog/post/index.html.twig', compact('posts'));
    }

    /**
     * @Route("/posts/{postSlug}", name="post_show")
     * @param \App\Entity\Post $post
     * @ParamConverter("post", options={"mapping"={"postSlug"="slug"}})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Post $post)
    {
        return $this->render('blog/post/show.html.twig', compact('post'));
    }
}
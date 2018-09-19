<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController
{
    /**
     * @Route("/posts", name="post_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return new Response('Posts List');
    }

    /**
     * @Route("/posts/{slug}", name="post_show")
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($slug)
    {
        return new Response($slug);
    }
}
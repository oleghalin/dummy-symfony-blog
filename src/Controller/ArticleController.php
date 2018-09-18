<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ArticleController
{
    /**
     * @Route("/posts")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return new Response('Posts List');
    }

    /**
     * @Route("/posts/{slug}")
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($slug)
    {
        return new Response($slug);
    }
}
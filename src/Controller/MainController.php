<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class MainController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return new Response('Hello World');
    }
}
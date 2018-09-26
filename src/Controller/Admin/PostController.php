<?php
/**
 * Created by PhpStorm.
 * User: oleghalin
 * Date: 24.09.2018
 * Time: 18:00
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class PostController extends AbstractController
{
    /**
     * @Route("/posts")
     */
    public function index()
    {
        return $this->render('@Admin/post/show.html.twig');
    }
}
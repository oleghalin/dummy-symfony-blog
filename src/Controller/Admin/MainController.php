<?php
/**
 * Created by PhpStorm.
 * User: oleghalin
 * Date: 26.09.2018
 * Time: 10:36
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('@Admin/index.html.twig');
    }
}
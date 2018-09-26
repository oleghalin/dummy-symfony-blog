<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Repository\PostRepository;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class PostController
 *
 * @package App\Controller\Api
 * @Route("posts")
 */
class PostController extends FOSRestController
{
    /**
     * @var \App\Repository\PostRepository
     */
    private $postRepository;

    /**
     * PostController constructor.
     *
     * @param \App\Repository\PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/")
     * @View(serializerGroups={"user"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $posts = $this->postRepository->findAll();

        $view = $this->view($posts, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/{postSlug}")
     * @ParamConverter("post", options={"mapping"={"postSlug"="slug"}})
     * @param \App\Entity\Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Post $post)
    {
        $view = $this->view($post);
        return $this->handleView($view);
    }
}
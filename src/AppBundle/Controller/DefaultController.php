<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="posts_list")
     * @Template()
     */
    public function postsListAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['postType' => 'news'], ['id' => Criteria::DESC]);
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', $this->getParameter("page_from")),
            $this->getParameter("count_per_page")
        );

        return ['posts_pagination' => $pagination, 'categories' => $categories];
    }

    /**
     * @Route("/posts/{id}/{type}", name="posts_show", requirements={"id": "\d+", "type": "\w+"})
     */
    public function postShowAction(Request $request, Post $post, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'method' => Request::METHOD_POST,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setPost($post);
            $comment->setPublishedAt(new \DateTime());
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('posts_show', ['id' => $post->getId(), 'type' => $post->getPostType()]);
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $post->getComments(),
            $request->query->getInt('page', $this->getParameter("page_from")),
            $this->getParameter("count_per_page")
        );

        return $this->render("@App/Default/postShow_$type.html.twig",
            ['post' => $post, 'comments_pagination' => $pagination, 'form' => $form->createView()]);
    }

    /**
     * @Route("/categories", name="categories_list")
     * @Template("AppBundle:includes:categories.html.twig")
     */
    public function categoriesListAction()
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();

        return ['categories' => $categories];
    }

    /**
     * @Route("/posts/categories/{id}", name="post_by_category", requirements={"id": "\d+"})
     * @Template("AppBundle:Default:postsList.html.twig")
     */
    public function postsByCategoryAction(Request $request, Category $category)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['category' => $category->getId()], ['id' => Criteria::DESC]);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', $this->getParameter("page_from")),
            $this->getParameter("count_per_page")
        );

        return ['posts_pagination' => $pagination];
    }

    /**
     * @param Request $request
     * @return array
     * @Template()
     */
    public function sideBarAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findby([
            'postType' => 'ad'
        ], ['id' => Criteria::DESC]);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', $this->getParameter("page_from")),
            $this->getParameter("count_per_page")
        );

        return['posts_pagination' => $pagination];

    }
}

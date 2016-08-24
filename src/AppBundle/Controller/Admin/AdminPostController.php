<?php
/**
 * Created by PhpStorm.
 * User: kate
 * Date: 18.08.16
 * Time: 15:40
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminPostController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/posts")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminPostController extends Controller
{
    /**
     * @Route("/new", name="admin_new_posts")
     * @Method({"GET", "POST"})
     * @Template("AppBundle:Admin/AdminPost:form.html.twig")
     */
    public function newPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();
        $forms = $this->get('app.type_of_post')->getForms();
        foreach ($forms as $key => $form) {
            $postForm = $this->createForm(get_class($form), $post);
            $postForm->handleRequest($request);
            if ($postForm->isValid()){
                $post->setPostType($key);
                $em->persist($post);
                $em->flush();
                return $this->redirectToRoute('posts_list');
            }
        }
        $postTypeForm = $this->createForm(PostType::class, $post, ['forms' => $forms]);
        $postTypeForm->handleRequest($request);

        if ($postTypeForm->isValid()) {
            /**
             * @var string $type
             */
            $type = $post->getPostType();
            $postForm = $this->createForm(get_class($type), $post);

            return ['postForm' => $postForm->createView()];
        }


        return [
            'typeForm' => $postTypeForm->createView(),
        ];
    }

    /**
     * @Route("/{id}/edit", name="admin_edit_posts", requirements={"id": "\d+"})
     * @Method({"GET", "PUT"})
     * @Template("AppBundle:Admin/AdminPost:form.html.twig")
     */
    public function editPostAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $forms = $this->get('app.type_of_post')->getForms();
        $form = $this->createForm(get_class($forms[$post->getPostType()]), $post, [
            'method' => Request::METHOD_PUT,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('posts_list');
        }

        return [
            'typeForm' => $form->createView(),
        ];
    }

    /**
     * @Route("/{id}/delete", name="admin_delete_posts", requirements={"id": "\d+"})
     * @Method({"GET", "DELETE"})
     * @Template()
     */
    public function deletePostAction(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        $form = $this
            ->createFormBuilder($post, [
                'method' => Request::METHOD_DELETE,
                'action' => $this->generateUrl('admin_delete_posts', ['id' => $post->getId()])
            ])
            ->getForm();

        if ($request->getMethod() == Request::METHOD_GET) {
            return ['form' => $form->createView(), 'id' => $post->getId()];
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('posts_list');
    }
}
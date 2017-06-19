<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;

class DefaultController extends Controller
{
    /**
     * The home page. Lists posts and provides a link to create a new post.
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        return $this->listPosts($em);
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /*
    * Display a list of posts
    * @return $posts array TODO 
    */
    public function listPosts(EntityManagerInterface $em) {
        $repository = $em->getRepository('AppBundle:Post');
        $posts = $repository->findAll(); //TODO: sort by date
        return new Response(print_r($posts,true));
    }


    /**
     * Page for user to submit a post.
     * TODO: extract form to a class
     * @Route("/create")
     */
    public function createAction(Request $request, EntityManagerInterface $em){
        $post = new Post();

        //create form to fill post with data
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('email', TextType::class)
            ->add('description', TextareaType::class) 
            ->add('date', DateType::class)//TODO: fill automatically
            ->add('save', SubmitType::class, array('label' => 'Create Post'))
            ->getForm();

        $form->handleRequest($request);

        //TODO: validate emails and description length
        if ($form->isSubmitted() ) {
            if($form->isValid()){
                $post = $form->getData();

                // save post to db
                $em->persist($post);
                $em->flush();

                //return $this->redirectToRoute('task_success');
                return new Response("Submitted post!");
            }
            else{
                return new Response("Not valid!");
            }
        }

        return $this->render('default/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}

?>

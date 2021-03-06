<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        $posts = $this->listPosts($em);
        return $this->render('default/home.html.twig', [
            'posts' => $posts,
        ]);
    }

    /*
    * Display a list of posts
    * @return $posts array 
    */
    public function listPosts(EntityManagerInterface $em) {
        $repository = $em->getRepository('AppBundle:Post');
        $posts = $repository->findAll(); 
        $posts = array_reverse($posts);//not sure which way is "descending" order here.
        return $posts;
    }


    /**
     * Page for user to submit a post.
     * TODO: extract form to a class
     * @Route("/create", name="create")
     */
    public function createAction(Request $request, EntityManagerInterface $em){
        $post = new Post();

        //create form to fill post with data
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('email', TextType::class)
            ->add('description', TextareaType::class) 
            ->add('save', SubmitType::class, array('label' => 'Create Post'))
            ->getForm();

        $form->handleRequest($request);

        //Check that submitted information is valid.
        if ($form->isSubmitted() ) {
            $post = $form->getData();
            $validator = $this->get('validator');
            $errors = $validator->validate($post);

            if (count($errors) == 0) {
                $post->setDate(new \DateTime('now'));

                // save post to db
                $em->persist($post);
                $em->flush();

                return new Response("Submitted post!");
            }
            else{
                $errorString = (string) $errors;
                $url = $this->generateUrl('create');
                return new Response("Error: $errorString<br><a href='$url'>Go Back</a>");
            }
        }

        return $this->render('default/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}

?>

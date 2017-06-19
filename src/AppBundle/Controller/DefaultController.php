<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Book;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/list", name="list")
     */
    public function listAction(Request $request, EntityManagerInterface $em) {
        $repository = $em->getRepository('AppBundle:Book');
        $books = $repository->findAll();
        return new Response(print_r($books,true));
    }

    /**
     * @Route("/create")
     */
    public function createAction(Request $request, EntityManagerInterface $em){
        $book = new Book();

        //create form to fill book with data
        $form = $this->createFormBuilder($book)
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add('date', DateType::class)
            ->add('pdf', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Book'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            if($form->isValid()){
                $book = $form->getData();

                // save book to db
                $em->persist($book);
                $em->flush();

                //return $this->redirectToRoute('task_success');
                return new Response("Submitted book!");
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

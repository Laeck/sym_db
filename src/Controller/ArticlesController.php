<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Users;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index()
    {
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }

    /**
     * @Route("/articles/new", name="articles_new")
     */
    public function new(Request $request)
    {
        $em = $this->getDoctrine();
        $users = $em->getRepository(Users::class)->findAll();  
        $user = $em->getRepository(Users::class)->find(5);  


        // creates a task object and initializes some data for this example
        $article = new Articles();
        //$article->setTitre('Un titre');
        //$article->setContent('Votre contenu');
        $article->setUsers($user);
        $article->setDatecrea(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class)
            ->add('content', TextareaType::class, array('label' => 'Contenu'))
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
            ->getForm();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {
              // On enregistre notre objet $article dans la base de données, par exemple
              $em = $this->getDoctrine()->getManager();
              $em->persist($article);
              $em->flush();
      
              $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                   
              // On redirige vers la page de visualisation de l'annonce nouvellement créée
              return $this->redirectToRoute('users', array('users' => $users));
            }

        }

        return $this->render('articles/new.html.twig', array(
            'form' => $form->createView(),
        ));
    
    }    
}

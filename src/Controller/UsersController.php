<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Form\UsersType;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        $em = $this->getDoctrine();
        $users = $em->getRepository(Users::class)->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/add", name="users_add")
     */
    public function createUser(Request $request): Response
    {
        $user = new Users();
        $user->setDatecrea(new \DateTime());

        $form = $this->createForm(UsersType::class, $user);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {
              // On enregistre notre objet $article dans la base de données, par exemple
              $entityManager = $this->getDoctrine()->getManager();
              $users = $entityManager->getRepository(Users::class)->findAll();

              $entityManager->persist($user);
              $entityManager->flush();
      
              //$request->getSession()->getFlashBag()->add('notice', 'User bien enregistré.');
                   
              // On redirige vers la page de visualisation de l'annonce nouvellement créée
              return $this->redirectToRoute('users', array('users' => $users));
            }

        }

        return $this->render('users/form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/users/{id}", name="user_show")
     */
    public function showUser($id)
    {
        $em = $this->getDoctrine();
        
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('users/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/users/edit/{id}")
     */
    public function updateUser($id)
    {
        // ETAPE 1: Récuperer l'entitymanager et récuperer l'objet sur lequel on veut travailler
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Users::class)->find($id);

        // ETAPE 2 : Verification si l'ID appartient bien a un USER
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        // ETAPE 3 : Modifier les champs que l'on souhaite et on persiste en bdd avec flush()
        $user->setName('James Bond');
        $user->setDatemaj(new \DateTime());
        $entityManager->flush();

        // ETAPE 4 : On envoie vers la vue
        return $this->redirectToRoute('user_show', [
            'id' => $user->getId()
        ]);
    }    

    /**
     * @Route("/users/delete/{id}")
     */
    public function deleteUser($id)
    {
        // ETAPE 1: Récuperer l'entitymanager et récuperer l'objet sur lequel on veut travailler
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Users::class)->find($id);

        // ETAPE 2 : Verification si l'ID appartient bien a un USER
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        // ETAPE 3 : On supprime et on persiste en bdd avec flush()
        $entityManager->remove($user);
        $entityManager->flush();

        // ETAPE 4 : On envoie vers la vue
        return $this->render('users/deleted.html.twig');
    }

}

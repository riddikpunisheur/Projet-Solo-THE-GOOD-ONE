<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use PhpParser\Node\Expr\New_;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $UserRepository): Response
    {
        return $this->render('user/index.html.twig', [
           'users' => $UserRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET,POST"})
     */
    public function new(Request $request): Response
    {
        $user = New User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/new.html.twig', [
           'users' => $user,
           'form' => $form->createView(),  
           ]);
    }
    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response 
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET,POST"})
     */
    public function edit(UserPasswordEncoderInterface $passwordEncoder, Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getDAta();
            // Si rien n'a été tapé dans le champs password, on reçoit las valeur null
            // Si $password est différent de null, on modifie le mot de passe de $user
        if ($password != null){
            $encodedPassword = $passwordEncoder->encodePassword($user,$password);
            $user->setPassword($encodedPassword);             
        }

        $this->getDoctrine()->getManager()->flush();
        
        return $this->redirectToRoute('user_index');
       
    }
        return $this->render('user/edit.html.twig', [
        'user' => $user,
        'form' => $form,    ]); 
        }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // ... do something, like deleting an object
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            
        }
       

            return $this->redirectToRoute('user_index');
        }
        
  
    
} 

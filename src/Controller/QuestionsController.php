<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Questions;
use App\Repository\UserRepository;
use App\Repository\QuestionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class QuestionsController extends AbstractController
 {
/**
     * @Route("/", name="questions_list")
     * @Route("/user/{last_name}", name="question_list_by_user")
     * @ParamConverter("user", class="App:User")
     */
    public function list(Request $request, QuestionsRepository $questionsRepository, User $user = null)
    {
        // On vérifie si on vient de la route "question_list_by_user"
        if($request->attributes->get('_route') == 'question_list_by_user' && $user === null) {
            // On récupère le name passé dans l'attribut de requête
            $params = $request->attributes->get('_route_params');
            $selecteduser = $params['username'];
            // Equivaut à $selecteduser = $request->attributes->get('_route_params')['name'];

            // Flash + redirect
            $this->addFlash('success', 'Le mot-clé "'.$selecteduser.'" n\'existe pas. Affichage de toutes les questions.');
            return $this->redirectToRoute('questions_list');
        }

        // On va chercher la liste des questions par ordre inverse de date
        if($user) {
            // Avec user
            $questions = $questionsRepository->findByUser($user);
            $selecteduser = $user->getUsername();
        } else {
            // Sans user
            $questions = $questionsRepository->findBy(['isBlocked' => false], ['createdAt' => 'DESC']);
            $selecteduser = null;
        }

        // Nuage de mots-clés
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['username' => 'ASC']);

        return $this->render('questions/index.html.twig', [
            'questions' => $questions,
            'users' => $users,
            'selectedUser' => $selecteduser,
        ]);
    }

    
    /**
     * @Route("/{id}/edit", name="review_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Questions $questions): Response
    {
        $form = $this->createForm(QuestionsType::class, $questions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('questions_index');
        }

        return $this->render('questions/edit.html.twig', [
            'questions' => $questions,
            'form' => $form->createView(),
        ]);
    }

    /*****************PARTIE ADMIN *********************************** */
    /**
     * @Route("/admin/question/add", name="question_add")
     */
 
    public function add(Request $request, UserRepository $userRepository)
    {
        $questions = new Questions();

        $form = $this->createForm(QuestionsType::class, $questions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // On associe le user connecté à la question
            $questions->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($questions);
            $entityManager->flush();

            $this->addFlash('success', 'Question ajoutée');

            return $this->redirectToRoute('questions_show', ['id' => $questions->getId()]);
        }

        return $this->render('questions/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    
    /**
     * @Route("/admin/question/{id}/edit", name="admin_question_edit", requirements={"id": "\d+"})
     */
    /*
     public function edit(Questions $questions, Request $request)
    {
        // Avant toute chose, on teste si l'utilisateur a le droit de modifer la $question
        // Cette méthode retourne une 403 (Access Denied) si l'utilisateur
        // n'entre pas dans les conditions du Voter
        $this->denyAccessUnlessGranted('edit', $questions);

        // Ensuite, on code l'édition de la question comme d'habitude
        $form = $this->createForm(QuestionType::class, $questions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On transfert la nouvelle image si elle a été reçue
            //$filename = $imageUploader->moveFile($form->get('image')->getData(), 'questions');
            //$questions->setImage($filename);

            $this->getDoctrine()->getManager()->flush();

            // On redirige vers la route question_view de notre $question
            return $this->redirectToRoute('questions_show', ['id' => $questions->getId()]);
        }

        return $this->render('questions/edit.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    
*/

    /**
     * @Route("/admin/question/toggle/{id}", name="admin_question_toggle")
     */
    public function adminToggle(Questions $questions = null)
    {
        if (null === $questions) {
            throw $this->createNotFoundException('Questions non trouvée.');
        }

        
        // Save
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('success', 'Questions modérée.');

        return $this->redirectToRoute('questions_show', ['id' => $questions->getId()]);
    }

}

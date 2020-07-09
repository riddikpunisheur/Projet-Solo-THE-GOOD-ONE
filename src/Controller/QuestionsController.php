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
     * @Route("/question/{id}", name="question_show", requirements={"id": "\d+"})
     */
    public function show(Questions $questions, Request $request, UserRepository $userRepository, AnswerRepository $answerRepository)
    {
        // Is question blocked ?
        if ($questions->getIsBlocked()) {
            throw $this->createAccessDeniedException('Non autorisé.');
        }

        // On ne va pas traiter le formulaire alors qu'on ne souhaite pas l'afficher
        // On ne le fait donc que si la question est active
        if ($questions->isActive()) {
            $answer = new Answer();
            
            $form = $this->createForm(AnswerType::class, $answer);
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                
                // $answer = $form->getData();
                // On associe Réponse
                $answer->setQuestion($questions);
                
                // On attribue un nouveau DateTime à la propriété updatedAt de $question
                $questions->setUpdatedAt(new \DateTime());
                
                // On associe le user connecté à la réponse
                $answer->setUser($this->getUser());
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($answer);
                $entityManager->flush();
                
                $this->addFlash('success', 'Réponse ajoutée');
                
                return $this->redirectToRoute('questions_show', ['id' => $questions->getId()]);
            }
            // On définir notre FormView pour l'envoyer au template
            $formView = $form->createView();
        } else {
            // Si on n'a pas de formulaire, on définit au moins $formView
            $formView = null;
        }

        // Réponses non bloquées
        $answersNonBlocked = $answerRepository->findBy([
            'questions' => $questions,
            'isBlocked' => false,
        ]);

        return $this->render('questions/show.html.twig', [
            'questions' => $questions,
            'answersNonBlocked' => $answersNonBlocked,
            'form' => $formView,
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

            // Cette ligne de code est un vestige de Symfony 3, elle est inutile depuis Symfony 4.0
            // $question = $form->getData();

            // On déplace l'image reçu, si elle existe, dans un sous-dossier de /public, «questions»
            $filename = $imageUploader->moveFile($form->get('image')->getData(), 'questions');

            // moveFile() retourne le nom du fichier créé ou la valeur null
            // On attribue la valeur de $filename à la propriété image de $question
            $questions->setImage($filename);
            
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
    


    /**
     * @Route("/admin/question/toggle/{id}", name="admin_question_toggle")
     */
    public function adminToggle(Questions $questions = null)
    {
        if (null === $questions) {
            throw $this->createNotFoundException('Questions non trouvée.');
        }

        // Inverse the boolean value via not (!)
        $questions->setIsBlocked(!$questions->getIsBlocked());
        // Save
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('success', 'Questions modérée.');

        return $this->redirectToRoute('questions_show', ['id' => $questions->getId()]);
    }

}

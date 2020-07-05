<?php

namespace App\Controller;

use App\Entity\Questions;
use App\Entity\User;
use App\Repository\QuestionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/package, name="question_list")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/package")
     * @Route("/user/{name}", name="question_list_by_user")
     * @ParamConverter("user", class="App:user")
     */
    public function list(Request $request, QuestionsRepository $questionsRepository, User $user)
    {
        
        return $this->render('questions/index.html.twig', [
            'controller_name' => 'QuestionsController',
        ]);
    }
}

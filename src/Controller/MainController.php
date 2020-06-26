<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
     * 
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        
        return $this->render('homepage.html.twig', ["date" => "26/06/2020"]);
    }
}

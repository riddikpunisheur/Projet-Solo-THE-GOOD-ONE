<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController
{
    /**
     * @Route("/activity", name="activity")
     */
    public function index()
    {
        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}

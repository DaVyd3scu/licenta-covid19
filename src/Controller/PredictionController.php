<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    /**
     * @Route("/prediction", name="prediction")
     */
    public function index(): Response
    {
        return $this->render('prediction/index.html.twig', [
            'controller_name' => 'PredictionController',
        ]);
    }
}

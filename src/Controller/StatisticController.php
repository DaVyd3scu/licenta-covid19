<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    const STATISTIC_FILTER = [ 'byCases', 'byVaccines', 'byIncidence'];
    const TYPE_OF_FILTER = [ 'byActive', 'byHealed', 'byDeceased' ];

    /**
     * @Route("/statistic/{statisticFilter}/{typeOfFilter}", name="statistic")
     */
    public function index(string $statisticFilter = 'byCases', string $typeOfFilter = null): Response
    {
        if (
            !in_array($statisticFilter, self::STATISTIC_FILTER) ||
            ($typeOfFilter && !in_array($typeOfFilter, self::TYPE_OF_FILTER))
        ) {
            return $this->render('errors/error404.html.twig');
        }

        return $this->render('statistic/index.html.twig', [

        ]);
    }
}

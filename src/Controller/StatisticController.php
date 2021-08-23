<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StatisticController extends AbstractController
{
    const STATISTIC_FILTER = [ 'byCases', 'byVaccines', 'byIncidence' ];
    const TYPE_OF_FILTER = [ 'byActive', 'byHealed', 'byDeceased' ];

    /**
     * @Route("/statistic/{statisticFilter}/{typeOfFilter}", name="statistic")
     */
    public function index(string $statisticFilter = null, string $typeOfFilter = null): Response
    {
        if (
            (!in_array($statisticFilter, self::STATISTIC_FILTER) && $statisticFilter !== null) ||
            ($typeOfFilter && !in_array($typeOfFilter, self::TYPE_OF_FILTER))
        ) {
            return $this->render('errors/error404.html.twig');
        }

        if ($statisticFilter === self::STATISTIC_FILTER[0] && $typeOfFilter === null) {
            return $this->redirectToRoute('statistic', [
                'statisticFilter' => $statisticFilter,
                'typeOfFilter' => self::TYPE_OF_FILTER[0]
            ]);
        }

        return $this->render('statistic/index.html.twig', [
            'filter' => $statisticFilter
        ]);
    }
}

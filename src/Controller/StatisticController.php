<?php

namespace App\Controller;

use App\Repository\CountyRepository;
use App\Service\GetCasesDataService;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistic", name="statistic_")
 */
class StatisticController extends AbstractController
{
    const STATISTIC_FILTER = [ 'byCases', 'byVaccines', 'byIncidence' ];
    const TYPE_OF_FILTER = [ 'byActive', 'byHealed', 'byDeceased', 'astra-zeneca', 'johnson-and-johnson', 'moderna', 'pfizer' ];
    const FIRST_CASE_DATE = '2020-02-26';

    /**
     * @var CountyRepository
     */
    private $countyRepository;

    /**
     * @var GetCasesDataService
     */
    private $getCasesDataService;

    public function __construct(CountyRepository $countyRepository, GetCasesDataService $getCasesDataService)
    {
        $this->countyRepository = $countyRepository;
        $this->getCasesDataService = $getCasesDataService;
    }

    /**
     * @Route("/get-filtered-data", name="get_filtered_data", methods={"GET"})
     */
    public function getDataByFilters(Request $request): JsonResponse
    {
        $dataToBeSent = [];

        // Get data from request
        $statisticFilter = $request->query->get('statisticFilter');
        $typeOfFilter = $request->query->get('typeOfFilter');
        $startingPeriod = $request->query->get('startingPeriod');
        $endingPeriod = $request->query->get('endingPeriod');
        $county = $request->query->get('county');

        $county = ($county !== 'undefined' && $county !== '') ?
            $this->countyRepository->findOneBy(['code' => $county]) :
            null;
        $startingPeriod = ($startingPeriod !== '') ?
            DateTime::createFromFormat('Y-m-d', $startingPeriod) :
            DateTime::createFromFormat('Y-m-d', self::FIRST_CASE_DATE);
        $endingPeriod = ($endingPeriod !== '') ?
            DateTime::createFromFormat('Y-m-d', $endingPeriod) :
            new DateTime();

        $dataToBeSent = $this->getCasesDataService
            ->getCasesByFilters(
                $statisticFilter,
                $startingPeriod,
                $endingPeriod,
                $typeOfFilter,
                $county
            );

        return $this->json($dataToBeSent);
    }

    /**
     * @Route("/{statisticFilter}/{typeOfFilter}", name="index")
     */
    public function index(string $statisticFilter = '', string $typeOfFilter = ''): Response
    {
        if (
            (!in_array($statisticFilter, self::STATISTIC_FILTER) && $statisticFilter !== '') ||
            ($typeOfFilter && !in_array($typeOfFilter, self::TYPE_OF_FILTER))
        ) {
            return $this->render('errors/error404.html.twig');
        }

        if ($statisticFilter === self::STATISTIC_FILTER[0] && $typeOfFilter === '') {
            return $this->redirectToRoute('statistic_index', [
                'statisticFilter' => $statisticFilter,
                'typeOfFilter' => self::TYPE_OF_FILTER[0]
            ]);
        }

        $counties = [];
        $incidenceRate = [];
        $activeCasesForEachCounty = [];

        if ($typeOfFilter === 'byActive' || $statisticFilter === 'byIncidence') {
            $counties = $this->countyRepository->getCountiesArray();

            if ($statisticFilter === 'byIncidence') {
                $incidenceRate = $this->getCasesDataService->getIncidenceRate();
            }

            if ($typeOfFilter === 'byActive') {
                $activeCasesForEachCounty = $this->getCasesDataService->getTotalActiveCases();
            }
        }

        $cases = [];
        if ($statisticFilter === 'byCases' && $typeOfFilter && !$counties) {
            $todayDate = new DateTime();
            $startingDate = (new DateTime())->sub(new DateInterval('P14D'));
            $cases = $this->getCasesDataService->getCasesByFilters($statisticFilter, $startingDate, $todayDate, $typeOfFilter);

            $cases = json_encode($cases);
        }

        $vaccines = [];
        if ($statisticFilter === 'byVaccines') {
            $vaccines = $this->getCasesDataService->getVaccineData();
            $vaccines = json_encode($vaccines);
        }

        return $this->render('statistic/index.html.twig', [
            'filter' => $statisticFilter,
            'typeOfFilter' => $typeOfFilter,
            'counties' => $counties,
            'incidenceRate' => $incidenceRate,
            'activeCasesCounty' => $activeCasesForEachCounty,
            'cases' => $cases,
            'vaccines' => $vaccines
        ]);
    }
}

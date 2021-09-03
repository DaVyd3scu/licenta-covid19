<?php

namespace App\Service;

use App\Entity\Cases\ActiveCases;
use App\Entity\Cases\ActiveCasesByCounty;
use App\Entity\Cases\DeceasedCases;
use App\Entity\Cases\HealedCases;
use App\Entity\County;
use App\Entity\IncidenceRate;
use App\Entity\Vaccine\AstraZenecaVaccine;
use App\Entity\Vaccine\JohnsonAndJohnsonVaccine;
use App\Entity\Vaccine\ModernaVaccine;
use App\Entity\Vaccine\PfizerVaccine;
use App\Entity\Vaccine\Vaccines;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class GetCasesDataService
{
    const FILTERS_CLASSES = [
        'byCases' => [
            'byActive' => ActiveCases::class,
            'byHealed' => HealedCases::class,
            'byDeceased' => DeceasedCases::class
        ],
        'byVaccines' => [
            'pfizer' => PfizerVaccine::class,
            'astra-zeneca' => AstraZenecaVaccine::class,
            'moderna' => ModernaVaccine::class,
            'johnson-and-johnson' => JohnsonAndJohnsonVaccine::class
        ],
        'byIncidence' => IncidenceRate::class
    ];

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCasesByFilters(
        string $statisticFilter,
        DateTime $startingPeriod,
        DateTime $endingPeriod,
        string $typeOfFilter = '',
        County $county = null
    ): array
    {
        $dataToBeReturned = [
            'filter' => $statisticFilter,
            'typeOfFilter' => ($statisticFilter === 'byCases') ?
                str_replace('by', '', $typeOfFilter):
                ucwords(str_replace('-', ' ', $typeOfFilter))
        ];

        if ($statisticFilter === 'byCases') {
            if ($typeOfFilter !== '' && $county !== null) {
                $requestedData = $this->entityManager->getRepository(ActiveCasesByCounty::class)
                    ->findActiveCasesByFilters($county, $startingPeriod, $endingPeriod);

                foreach ($requestedData as $data) {
                    $date = date_format($data["date"], 'Y-m-d');
                    $dataToBeReturned['data'][$date] = $data["newCases"];
                }
            } else if ($typeOfFilter !== '') {
                $requestedData = $this->entityManager->getRepository(self::FILTERS_CLASSES[$statisticFilter][$typeOfFilter])
                    ->findByFilters($startingPeriod, $endingPeriod);

                foreach ($requestedData as $data) {
                    $date = date_format($data["date"], 'Y-m-d');
                    $dataToBeReturned['data'][$date] = $data["currentDayNumber"];
                }
            }
        }

        if ($statisticFilter === 'byVaccines') {
            if ($typeOfFilter !== '') {
                $requestedData = $this->entityManager->getRepository(self::FILTERS_CLASSES[$statisticFilter][$typeOfFilter])
                    ->findByFilters($startingPeriod, $endingPeriod);

                foreach ($requestedData as $data) {
                    $date = date_format($data["date"], 'Y-m-d');
                    $dataToBeReturned['data'][$date] = [
                        'numberOfDosesAdministered' => $data["currentDayNumberOfDoses"],
                        'peopleImmunized' => $data["peopleImmunized"]
                    ];
                }
            }
        }

        return $dataToBeReturned;
    }

    public function getIncidenceRate()
    {
        return $this->entityManager->getRepository(IncidenceRate::class)->getIncidenceArray();
    }

    public function getTotalActiveCases()
    {
        return $this->entityManager->getRepository(ActiveCasesByCounty::class)
            ->getTotalActiveCasesByCountyArray();
    }

    public function getVaccineData(): array
    {
        $dataToBeSent = [];
        $todayDate = new DateTime();
        $startingDate = (new DateTime())->sub(new DateInterval('P14D'));

        $astraZeneca = $this->entityManager->getRepository(AstraZenecaVaccine::class)
            ->findByFilters($startingDate, $todayDate);
        $johnson = $this->entityManager->getRepository(JohnsonAndJohnsonVaccine::class)
            ->findByFilters($startingDate, $todayDate);
        $moderna = $this->entityManager->getRepository(ModernaVaccine::class)
            ->findByFilters($startingDate, $todayDate);
        $pfizer = $this->entityManager->getRepository(PfizerVaccine::class)
            ->findByFilters($startingDate, $todayDate);

        for ($i = 0; $i < count($astraZeneca); $i++) {
            $date = date_format($astraZeneca[$i]["date"], 'Y-m-d');

            $dataToBeSent['Astra Zeneca'][$date] = $astraZeneca[$i]["currentDayNumberOfDoses"];
            $dataToBeSent['Johnson and Johnson'][$date] = $johnson[$i]["currentDayNumberOfDoses"];
            $dataToBeSent['Moderna'][$date] = $moderna[$i]["currentDayNumberOfDoses"];
            $dataToBeSent['Pfizer'][$date] = $pfizer[$i]["currentDayNumberOfDoses"];
        }

        return $dataToBeSent;
    }
}

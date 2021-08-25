<?php

namespace App\Service;

use App\Entity\Cases\ActiveCases;
use App\Entity\Cases\ActiveCasesByCounty;
use App\Entity\Cases\DeceasedCases;
use App\Entity\Cases\HealedCases;
use App\Entity\County;
use App\Entity\IncidenceRate;
use App\Entity\LastUpdated;
use App\Entity\TotalNumber;
use App\Entity\Vaccine\AstraZenecaVaccine;
use App\Entity\Vaccine\JohnsonAndJohnsonVaccine;
use App\Entity\Vaccine\ModernaVaccine;
use App\Entity\Vaccine\PfizerVaccine;
use App\Repository\CountyRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ImportDataService
{
    private const COUNTY_ID_INDEX = 0;
    private const COUNTY_NAME_INDEX = 1;
    private const COUNTY_CODE_INDEX = 5;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * @var CountyRepository
     */
    private $entityManager;

    /**
     * @var OutputInterface
     */
    private $consoleOutput;

    public function __construct(
        HttpClientInterface $client,
        ContainerBagInterface $params,
        EntityManagerInterface $entityManager
    ) {
        $this->client = $client;
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    /**
     * @param OutputInterface $output
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function insertDataIntoDatabase(OutputInterface $output)
    {
        $this->consoleOutput = $output;

        $this->insertCountiesIntoDatabase();
        $output->writeln('');

        $this->insertFromGeoSpatialIntoDatabase();
        $output->writeln('');

        $this->insertFromDateLaZiIntoDatabase();
        $output->writeln('');

        $this->insertNewCasesForCounties();
        $output->writeln('');

        $lastUpdated = new LastUpdated();
        $lastUpdated->setLastUpdate(new DateTime());

        $this->entityManager->persist($lastUpdated);
        $this->entityManager->flush();
    }

    /**
     * @param string $url
     *
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    private function getResponse(string $url): ResponseInterface
    {
        return $this->client->request('GET', $url);
    }

    private function insertCountiesIntoDatabase()
    {
        $fileUrl = $this->params->get('app.romania_counties_file');
        $file = fopen($fileUrl, 'r');

        if ($file !== false) {
            $this->consoleOutput->writeln('<info>====> Inserting Counties into database. <====</info>');

            while (($data = fgetcsv($file)) !== false) {
                if (is_numeric($data[self::COUNTY_ID_INDEX])) {
                    $county = new County();
                    $county->setCode($data[self::COUNTY_CODE_INDEX]);
                    $county->setName($data[self::COUNTY_NAME_INDEX]);

                    $this->entityManager->persist($county);
                }
            }
            $this->entityManager->flush();

            $this->consoleOutput->writeln('<info>====> Counties have been inserted into database. <====</info>');
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function insertFromGeoSpatialIntoDatabase()
    {
        // URL to get contents
        $urlGeoSpatial = $this->params->get('app.geo_spatial_covid_api_url');
        $contentGeoSpatial = $this->getResponse($urlGeoSpatial)->getContent();
        $casesFromGeoSpatial = json_decode($contentGeoSpatial, true);

        $fullData = $casesFromGeoSpatial["data"]["data"];

        if ($fullData) {
            $this->consoleOutput
                ->writeln(
                    '<info>====> Inserting data from https://covid19.geo-spatial.org into database <====</info>'
                );

            foreach ($fullData as $data) {
                $activeCases = new ActiveCases();
                $healedCases = new HealedCases();
                $deceasedCases = new DeceasedCases();
                $totalNumber = new TotalNumber();
                $dateString = $data["day_case"];
                $date = DateTime::createFromFormat('Y-m-d', $dateString);

                $this->consoleOutput
                    ->writeln(
                        "<question>==> Information date:</question><info> $dateString</info><question> <==</question>"
                    );

                $activeCases
                    ->setDate($date)
                    ->setCurrentDayNumber($data["new_case_no"]);
                $healedCases
                    ->setDate($date)
                    ->setCurrentDayNumber($data["new_healed_no"]);
                $deceasedCases
                    ->setDate($date)
                    ->setCurrentDayNumber($data["new_dead_no"]);
                $totalNumber->setDate($date)
                    ->setTotalCases($data["total_case"]);

                $this->entityManager->persist($activeCases);
                $this->entityManager->persist($healedCases);
                $this->entityManager->persist($deceasedCases);
                $this->entityManager->persist($totalNumber);
            }

            $this->entityManager->flush();

            $this->consoleOutput
                ->writeln(
                    '<info>====> Data from https://covid19.geo-spatial.org has been inserted into database. <====</info>'
                );
        } else {
            $this->consoleOutput
                ->writeln('<error>====> Data could not be loaded <====</error>');
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function insertFromDateLaZiIntoDatabase()
    {
        $urlDateLaZi = $this->params->get('app.date_la_zi_api_url');
        $contentDateLaZi = $this->getResponse($urlDateLaZi)->getContent();
        $casesFromDateLaZi = json_decode($contentDateLaZi, true);

        if ($casesFromDateLaZi) {
            $this->consoleOutput->writeln('<info>====> Inserting data from https://datelazi.ro into database. <====</info>');

            $todayData = $casesFromDateLaZi["currentDayStats"];
            $historicalData = $casesFromDateLaZi["historicalData"];
            ksort($historicalData);

            // Insert the historical data in chronological order
            foreach ($historicalData as $date => $info) {
                $this->insertData($info);
            }

            $this->entityManager->flush();

            // Insert the current day data
            $this->insertData($todayData);
            $this->insertIncidenceByCounty($todayData);

            $this->consoleOutput
                ->writeln('<info>====> Data from https://datelazi.ro has been inserted into database. <====</info>');
        } else {
            $this->consoleOutput
                ->writeln('<error>====> Data could not be loaded. <====</error>');
        }
    }

    private function insertData(array $data)
    {
        $dateString = $data["parsedOnString"];
        $date = DateTime::createFromFormat('Y-m-d', $dateString);

        $this->consoleOutput->writeln("<question>==> Information date:</question><info> $dateString</info><question> <==</question>");

        $totalNumber = $this->entityManager
            ->getRepository(TotalNumber::class)
            ->findOneBy(['date' => $date]);

        if ($totalNumber) {
            $totalNumber->setDosesOfVaccineAdministered($data["numberTotalDosesAdministered"]);
        }

        if ($data["countyInfectionsNumbers"]) {
            $date = DateTime::createFromFormat('Y-m-d', $data["parsedOnString"]);

            foreach ($data["countyInfectionsNumbers"] as $code => $number) {
                $activeCases = new ActiveCasesByCounty();

                $county = $this->entityManager
                    ->getRepository(County::class)
                    ->findOneBy(['code' => $code]);

                if ($county) {
                    $activeCases->setDate($date)->setCounty($county)->setCurrentDayNumber($number);

                    $this->entityManager->persist($activeCases);
                }
            }
        }

        if ($data["vaccines"]) {
            foreach ($data["vaccines"] as $vaccine => $info) {
                // Insert data for Pfizer
                if ($vaccine === 'pfizer') {
                    $vaccineObject = new PfizerVaccine();
                }
                // Insert data for Moderna
                if ($vaccine === 'moderna') {
                    $vaccineObject = new ModernaVaccine();
                }
                // Insert data for Astra Zeneca
                if ($vaccine === 'astra_zeneca') {
                    $vaccineObject = new AstraZenecaVaccine();
                }
                // Insert data for Johnson and Johnson
                if ($vaccine === 'johnson_and_johnson') {
                    $vaccineObject = new JohnsonAndJohnsonVaccine();
                }

                $vaccineObject->setDate($date)
                    ->setCurrentDayNumberOfDoses($info["total_administered"])
                    ->setPeopleImmunized($info["immunized"]);
                $this->entityManager->persist($vaccineObject);
            }
        }
    }

    /**
     * @param array $data
     */
    private function insertIncidenceByCounty(array $data)
    {
        if ($data["incidence"]) {
            foreach ($data["incidence"] as $code => $incidenceNumber) {
                $incidence = new IncidenceRate();

                $county = $this->entityManager
                    ->getRepository(County::class)
                    ->findOneBy(['code' => $code]);

                if ($county) {
                    $incidence->setCounty($county)->setIncidenceRate($incidenceNumber);

                    $this->entityManager->persist($incidence);
                }
            }

            $this->entityManager->flush();
        }
    }

    private function insertNewCasesForCounties()
    {
        $counties = $this->entityManager->getRepository(County::class)->findAll();
        $activeCasesByCountyRepository = $this->entityManager->getRepository(ActiveCasesByCounty::class);

        if ($counties) {
            $this->consoleOutput->writeln('<info>====> Inserting new cases for each county <====</info>');

            foreach ($counties as $county) {
                $this->consoleOutput->writeln(
                    '<question>==> Inserting for</question><info> '. $county->getName() .' county</info><question> <==</question>'
                );

                $activeCases = $activeCasesByCountyRepository->findBy(['county' => $county]);
                $numberOfResults = count($activeCases);

                for ($i = 1; $i < $numberOfResults; $i++) {
                    $newCases = $activeCases[$i]->getCurrentDayNumber() - $activeCases[$i - 1]->getCurrentDayNumber();

                    if ($newCases < 0) {
                        $newCases = 0;
                    }

                    $activeCases[$i]->setNewCases($newCases);
                }
            }
            $this->entityManager->flush();

            $this->consoleOutput->writeln(
                '<info>====> New cases for each county have been inserted <====</info>'
            );
        }
    }
}

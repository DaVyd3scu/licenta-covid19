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
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdateDataService
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function updateData(OutputInterface $output)
    {
        $geoSpatialData = $this->client
            ->request(
                'GET',
                $this->params->get('app.geo_spatial_covid_api_url')
            )->toArray()["data"]["data"];
        $numberOfResults = count($geoSpatialData);
        $lastUpdateRepository = $this->entityManager->getRepository(LastUpdated::class);
        $lastUpdate = $lastUpdateRepository->getLastUpdateDateTime();

        if ($lastUpdate) {
            $lastDayData = $geoSpatialData[$numberOfResults - 1];
            $numberOfDaysDiff = $this->compareDates(
                DateTime::createFromFormat('Y-m-d', $lastUpdate->format('Y-m-d')),
                DateTime::createFromFormat('Y-m-d', $lastDayData["day_case"])
            );

            if ($numberOfDaysDiff === 0) {
                $output->writeln('<info>You are up to date.</info>');
            } else {
                $dateLaZiData = $this->client
                    ->request(
                        'GET',
                        $this->params->get('app.date_la_zi_api_url')
                    )->toArray()["currentDayStats"];

                $this->updateDataFromGeoSpatial($lastDayData, $output);

                $this->updateDataFromDateLaZi($dateLaZiData, $lastUpdate, $output);

                $lastUpdated = $lastUpdateRepository->getLastUpdateObject();
                if ($lastUpdated) {
                    $lastUpdated->setLastUpdate(new DateTime());
                }
                $this->entityManager->flush();

                $output->writeln('<info>===========> Data has been successfully updated! <===========</info>');
            }
        } else {
            $output->writeln('<error>You must first import data in order to check for updates!</error>');
        }
    }

    /**
     * @param DateTime $date1
     * @param DateTime $date2
     *
     * @return int
     */
    private function compareDates(DateTime $date1, DateTime $date2): int
    {
        return abs($date1->diff($date2)->days);
    }

    /**
     * @param array $data
     * @param OutputInterface $output
     */
    private function updateDataFromGeoSpatial(array $data, OutputInterface $output): void
    {
        $date = DateTime::createFromFormat('Y-m-d', $data["day_case"]);

        $activeCases = new ActiveCases();
        $deceasedCases = new DeceasedCases();
        $healedCases = new HealedCases();
        $totalNumber = new TotalNumber();

        $output->writeln('<info>====> Inserting data from https://covid19.geo-spatial.org</info>');

        $activeCases->setDate($date)->setCurrentDayNumber($data["new_case_no"]);
        $deceasedCases->setDate($date)->setCurrentDayNumber($data["new_dead_no"]);
        $healedCases->setDate($date)->setCurrentDayNumber($data["new_healed_no"]);
        $totalNumber->setDate($date)->setTotalCases($data["total_case"]);

        $this->entityManager->persist($activeCases);
        $this->entityManager->persist($deceasedCases);
        $this->entityManager->persist($healedCases);
        $this->entityManager->persist($totalNumber);

        $this->entityManager->flush();

        $output->writeln(
            '<info>====> Data from https://covid19.geo-spatial.org has been successfully inserted. <====</info>'
        );
    }

    /**
     * @param array $data
     * @param DateTime $lastUpdate
     * @param OutputInterface $output
     */
    private function updateDataFromDateLaZi(array $data, DateTime $lastUpdate, OutputInterface $output): void
    {
        $date = DateTime::createFromFormat('Y-m-d', $data["parsedOnString"]);
        $totalNumber = $this->entityManager
            ->getRepository(TotalNumber::class)->findOneBy(['date' => $date]);

        $output->writeln('<info>====> Inserting data from https://datelazi.ro <====</info>');

        if ($totalNumber) {
            $totalNumber->setDosesOfVaccineAdministered($data["numberTotalDosesAdministered"]);
        }

        $countyInfectionsNumbers = $data["countyInfectionsNumbers"];
        if ($countyInfectionsNumbers) {
            $output->writeln('<info>==> Inserting number of infected cases for each county <==</info>');

            foreach ($countyInfectionsNumbers as $code => $numberInfected) {
                $county = $this->entityManager->getRepository(County::class)->findOneBy(['code' => $code]);

                if ($county) {
                    $previousDayNumber = $this->entityManager
                        ->getRepository(ActiveCasesByCounty::class)
                        ->findInfectionsNumberByCountyAndDate($county, $lastUpdate);
                    $currentDayNumber = $numberInfected;
                    $newCases = $currentDayNumber - $previousDayNumber;

                    $output->writeln('<question>==> Now inserting for ' . $county->getName() . ' <==</question>');

                    $activeCases = new ActiveCasesByCounty();
                    $activeCases->setDate($date)
                        ->setCounty($county)
                        ->setCurrentDayNumber($currentDayNumber)
                        ->setNewCases($newCases);

                    $this->entityManager->persist($activeCases);
                }
            }

            $this->entityManager->flush();
        }

        $incidenceRate = $data["incidence"];
        if ($incidenceRate) {
            $output->writeln('<info>==> Updating incidence rates <==</info>');

            foreach ($incidenceRate as $code => $incidence) {
                $county = $this->entityManager->getRepository(County::class)->findOneBy(['code' => $code]);

                if ($county) {
                    $output->writeln('<question>==> Now updating for ' . $county->getName() . ' <==</question>');

                    $incidenceToUpdate = $this->entityManager
                        ->getRepository(IncidenceRate::class)
                        ->findOneBy(['county' => $county]);

                    $incidenceToUpdate->setIncidenceRate($incidence);
                }
            }

            $this->entityManager->flush();
        }

        $vaccines = $data["vaccines"];
        if ($vaccines) {
            $output->writeln('<info>==> Inserting vaccine data. <==</info>');

            foreach ($vaccines as $vaccine => $info) {
                $vaccineObject = $this->getTypeOfVaccine($vaccine);

                $vaccineObject->setDate($date)
                    ->setCurrentDayNumberOfDoses($info["total_administered"])
                    ->setPeopleImmunized($info["immunized"]);

                $this->entityManager->persist($vaccineObject);
            }

            $this->entityManager->flush();
        }

        $output->writeln('<info>====> Data from https://datelazi.ro has been successfully inserted. <====</info>');
    }

    /**
     * @param string $vaccine
     *
     * @return AstraZenecaVaccine|JohnsonAndJohnsonVaccine|ModernaVaccine|PfizerVaccine|null
     */
    private function getTypeOfVaccine(string $vaccine)
    {
        if ($vaccine === 'pfizer') {
            return new PfizerVaccine();
        }
        if ($vaccine === 'moderna') {
            return new ModernaVaccine();
        }
        if ($vaccine === 'astra_zeneca') {
            return new AstraZenecaVaccine();
        }
        if ($vaccine === 'johnson_and_johnson') {
            return new JohnsonAndJohnsonVaccine();
        }

        return null;
    }
}

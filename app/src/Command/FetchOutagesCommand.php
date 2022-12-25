<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use App\Services\Import\OutageImporter;
use App\Services\Import\BoundariesImporter;
use App\Entity\Outage;
use App\Entity\Boundaries;

#[AsCommand(name: 'app:fetch:outages')]
class FetchOutagesCommand extends Command
{
    private HttpClientInterface $client;
    private ManagerRegistry $doctrine;
    private OutageImporter $outageImporter;
    private BoundariesImporter $boundariesImporter;
    private string $apiBoundariesUrl;
    private array $apiIncidentUrls;

    public function __construct(HttpClientInterface $client, ManagerRegistry $doctrine, OutageImporter $outageImporter, 
        BoundariesImporter $boundariesImporter, string $apiBoundariesUrl, array $apiIncidentUrls) {
        parent::__construct();
        $this->client = $client;
        $this->doctrine = $doctrine;
        $this->outageImporter = $outageImporter;
        $this->boundariesImporter = $boundariesImporter;
        $this->apiBoundariesUrl = $apiBoundariesUrl;
        $this->apiIncidentUrls = $apiIncidentUrls;
    }

    protected function configure(): void {
        $this->setDescription('Gets outages and stores them in the database')
            ->addOption(
                'fetch-boundaries',
                null,
                InputOption::VALUE_NONE,
                'Fetches boundaries data'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $startTime = microtime(true);
        $output->writeln('-- Downloading outages '. date('Y-m-d H:i:s') .' --');
        
        $boundariesRepo = $this->doctrine->getManager()->getRepository(Boundaries::class);
        $boundaries = $boundariesRepo->findLatest();
        $boundariesJson = null;

        if ($input->getOption('fetch-boundaries') !== false || $boundaries === null) {
            try {
                $boundariesJson = $this->fetchJsonFromApiUrl($this->apiBoundariesUrl);
            } catch (\Exception $ex) {
                $output->writeln(['Failure to read Boundaries API', $ex->getMessage()]);
                return Command::FAILURE;
            }
            $this->boundariesImporter->importFromJson($boundariesJson);
            $boundaries = $this->boundariesImporter->getObject();
            $this->boundariesImporter->persist();
        }

        $json = [];
        foreach ($this->apiIncidentUrls as $key => $url) {
            try {
                $json[$key] = $this->fetchJsonFromApiUrl($url);
            } catch (\Exception $ex) {
                $output->writeln(['Failure to read Incidents API', $ex->getMessage()]);
                return Command::FAILURE;
            }
        }
        $this->outageImporter->importFromJson($json);

        /** @var Outage */
        $outage = $this->outageImporter->getObject();
        $outage->setBoundaries($boundaries);
        if ($boundariesJson !== null) {
            $outage->setFullJson('boundaries', $boundariesJson);
        }

        $this->outageImporter->flush();
        
        $finishedTime = round(microtime(true) - $startTime, 3);
        $output->writeln('-- Completed in '. $finishedTime .'ms --');
        return Command::SUCCESS;
    }
    
    private function fetchJsonFromApiUrl(string $url): mixed {
        $content = $this->client->request('GET', $url)->getContent(true);
        return json_decode($content, true);
    }
}
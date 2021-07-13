<?php

namespace Console\App\Commands;

use Console\App\Interfaces\HttpClient;
use Console\App\Interfaces\Notifier;
use DOMDocument;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{
    protected static $defaultName = 'app:check';

    protected $notifiers = [];

    protected HttpClient $httpClient;

    protected $config = [];

    protected function configure()
    {
        $this
            ->setDescription('Checks a website URL')
            ->setHelp('Check either the page title or HTTP response code for a valid response. If no opptions are provided the script will check for HTTP/200')
            ->addArgument('siteUrl', InputArgument::REQUIRED)
            ->addOption('title', 't', InputOption::VALUE_REQUIRED)
            ->addOption('status', 's', InputOption::VALUE_REQUIRED);

        $this->loadConfig(CONFIG_FILE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('siteUrl');
        $titleOption = $input->getOption('title');
        $statusOption = $input->getOption('status');

        $response = $this->httpClient->get($url);

        if ($titleOption) {
            $title = $this->getTitleFromResponse($response);
            if ($title != $titleOption) {
                $this->sendNotification($url, "title", $titleOption, $title);
            }
        }

        if ($statusOption) {
            $status = $response->getStatusCode();

            if ($status != $statusOption) {
                $this->sendNotification($url, "status", $statusOption, $status);
            }
        }

        return Command::SUCCESS;
    }

    public function addNotifier(Notifier ...$notifiers): void
    {
        foreach ($notifiers as $notifier) {
            $this->notifiers[$notifier->getName()] = $notifier;
        }

    }

    public function addHttpClient(HttpClient $client): void
    {
        $this->httpClient = $client;
    }

    public function loadConfig($file)
    {
        $this->config = json_decode(file_get_contents($file));
    }

    public function getResponse(string $url): ResponseInterface
    {
        $res = $this->httpClient->get($url);
    }

    public function getTitleFromResponse(ResponseInterface $response): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();

        $dom->loadHTML($response->getBody()->getContents(), LIBXML_NOWARNING);

        $title = $dom->getElementsByTagName('title')[0]->nodeValue;

        return $title;
    }

    public function sendNotification(string $url, string $type, string $expected, string $actual)
    {
        $body = sprintf("The site at %s has failed a check. \r\n\r\n %s was expected to be %s but got %s",
            $url,
            $type,
            $expected,
            $actual
        );

        foreach ($this->notifiers as $notifier) {
            $notifier->send($body);
        }
    }
}
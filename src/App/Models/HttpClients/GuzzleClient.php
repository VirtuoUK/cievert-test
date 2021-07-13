<?php

namespace Console\App\Models\HttpClients;

use Console\App\Interfaces\HttpClient;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient implements HttpClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get(string $url): ResponseInterface
    {
        return $this->client->request('GET', $url);
    }
}
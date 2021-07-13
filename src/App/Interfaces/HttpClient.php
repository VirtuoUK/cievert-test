<?php

namespace Console\App\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface HttpClient
{
    public function get(string $url): ResponseInterface;
}
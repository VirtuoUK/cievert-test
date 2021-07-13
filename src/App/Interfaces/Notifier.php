<?php

namespace Console\App\Interfaces;

interface Notifier
{
    public function setup(array $options): void;

    public function send(string $body): void;

    public function addRecipients(array $recipients): void;

    public function getRecipients(): ?array;

    public function getName(): string;
}
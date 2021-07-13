<?php

namespace Console\App\Models\Notifiers;

use Console\App\Interfaces\Notifier;
use Twilio\Rest\Client as TwilioClient;

class TwilioNotifier implements Notifier
{

    const RECIPIENT_TYPE = 'tel';

    private $recipients = [];
    private $fromNumber = null;

    /** @var TwilioClient */
    protected $client;

    public function setup(array $config): void
    {
        $this->client = new TwilioClient($config['user'], $config['pass'], $config['sid'], $config['token']);
        $this->fromNumber = $config['from_no'];
    }

    public function send(string $body): void
    {
        foreach ($this->getRecipients() as $recipient) {
            $this->sendMessage($recipient[self::RECIPIENT_TYPE], $body);
        }
    }

    public function getName(): string
    {
        return self::class;
    }

    public function addRecipients(array $recipients): void
    {
        $this->recipients = $recipients;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    private function sendMessage(string $recipient, string $body): void
    {
        $this->client->messages->create(
            $recipient,
            [
                'from' => $this->fromNumber,
                'body' => $body
            ]
        );
    }
}

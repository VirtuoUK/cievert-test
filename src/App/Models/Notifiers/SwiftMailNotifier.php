<?php

namespace Console\App\Models\Notifiers;

use Console\App\Interfaces\Notifier;
use Swift_Transport;
use Swift_Mailer;

class SwiftMailNotifier implements Notifier
{

    const RECIPIENT_TYPE = 'email';

    private $recipients = [];
    private $fromAddress = null;

    /** @var Swift_Transport */
    protected $transport;

    /** @var Swift_Mailer */
    protected $mailer;

    public function setup(array $config): void
    {
        $this->transport = new \Swift_SmtpTransport(
            $config['smtp_host'],
            $config['smtp_port']
        );

        $this->fromAddress = $config['from_addr'];
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
        $message = (new Swift_Message('Site Down'))
            ->setFrom([$this->fromAddress => 'Site Monitor'])
            ->setTo([$recipient])
            ->setBody($body);

        $result = $this->mailer->send($message);
    }
}

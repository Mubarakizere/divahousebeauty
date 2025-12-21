<?php

namespace App\Mail\Transports;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrevoTransport extends AbstractTransport
{
    protected $client;
    protected $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        parent::__construct();
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();
        $to = array_map(fn($t) => ['email' => $t->getAddress()], $email->getTo());

        $data = [
            'sender' => [
                'email' => $email->getFrom()[0]->getAddress(),
                'name' => $email->getFrom()[0]->getName() ?: 'Diva House',
            ],
            'to' => $to,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?? 'No HTML',
            'textContent' => $email->getTextBody() ?? '',
        ];

        $this->client->request('POST', 'https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $this->apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
            'json' => $data,
        ]);
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}

<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class MicrosoftGraphTransport extends AbstractTransport
{
    protected string $tenantId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $fromAddress;

    public function __construct(string $tenantId, string $clientId, string $clientSecret, string $fromAddress)
    {
        parent::__construct();
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->fromAddress = $fromAddress;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $accessToken = $this->getAccessToken();

        $payload = $this->getPayload($email);

        $response = Http::withToken($accessToken)
            ->post("https://graph.microsoft.com/v1.0/users/{$this->fromAddress}/sendMail", $payload);

        if ($response->failed()) {
            throw new \Exception('Microsoft Graph API Error: ' . $response->body());
        }
    }

    protected function getAccessToken(): string
    {
        $response = Http::asForm()->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            throw new \Exception('Microsoft Graph OAuth Error: ' . $response->body());
        }

        return $response->json('access_token');
    }

    protected function getPayload(Email $email): array
    {
        $toRecipients = [];
        foreach ($email->getTo() as $address) {
            $toRecipients[] = [
                'emailAddress' => [
                    'address' => $address->getAddress(),
                    'name' => $address->getName(),
                ],
            ];
        }

        $ccRecipients = [];
        foreach ($email->getCc() as $address) {
            $ccRecipients[] = [
                'emailAddress' => [
                    'address' => $address->getAddress(),
                ],
            ];
        }

        $payload = [
            'message' => [
                'subject' => $email->getSubject(),
                'body' => [
                    'contentType' => $email->getHtmlBody() ? 'HTML' : 'Text',
                    'content' => $email->getHtmlBody() ?: $email->getTextBody(),
                ],
                'toRecipients' => $toRecipients,
                'ccRecipients' => $ccRecipients,
            ],
            'saveToSentItems' => 'true',
        ];

        // Attachments
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename'),
                'contentType' => $attachment->getMediaType() . '/' . $attachment->getMediaSubtype(),
                'contentBytes' => base64_encode($attachment->getBody()),
            ];
        }

        if (!empty($attachments)) {
            $payload['message']['attachments'] = $attachments;
        }

        return $payload;
    }

    public function __toString(): string
    {
        return 'microsoft-graph';
    }
}

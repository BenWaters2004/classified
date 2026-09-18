<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ClickSendSmsService
{
    public function send(string $to, string $body, bool $shortenUrls = true): array
    {
        $username = config('services.clicksend.username');
        $apiKey   = config('services.clicksend.api_key');

        $client = new Client([
            'base_uri' => 'https://rest.clicksend.com/v3/',
            'auth'     => [$username, $apiKey],
            'headers'  => ['Content-Type' => 'application/json'],
            'timeout'  => 20,
        ]);

        $payload = [
            'shorten_urls' => $shortenUrls, // ✅ toggle
            'messages' => [[
                'source' => 'api',
                'to'     => $to,
                'body'   => $body,
                'from'   => 'ClassifIeD',
            ]],
        ];

        try {
            $resp = $client->post('sms/send', ['json' => $payload]);
            $data = json_decode((string) $resp->getBody(), true) ?? [];

            $msg = $data['data']['messages'][0] ?? [];
            $messageId = $msg['message_id'] ?? null;

            Log::info('ClickSend SMS response', [
                'to'          => $to,
                'message_id'  => $messageId,
                'status'      => $msg['status'] ?? null,
                'shortenUrls' => $shortenUrls,
                'response'    => $data,
            ]);

            return [
                'raw'        => $data,
                'message_id' => $messageId,
                'status'     => $msg['status'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('ClickSend SMS send failed', [
                'to'          => $to,
                'shortenUrls' => $shortenUrls,
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
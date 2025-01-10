<?php

namespace App\Jobs;

use App\Models\App;
use App\Models\SentHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Queueable;

    protected App $app;
    protected int $sentHistoryId;

    public function __construct($app, $sentHistoryId)
    {
        $this->app = $app;
        $this->sentHistoryId = $sentHistoryId;
    }

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $sentHistory = SentHistory::find($this->sentHistoryId);

        if (!$sentHistory) {
            Log::error('SentHistory record not found for ID: ' . $this->sentHistoryId);
            return;
        }

        $url = 'https://zipwhats.com/api/create-message';

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $body = [
            'appkey' => $this->app->appkey,
            'authkey' => $this->app->authkey,
            'to' => $sentHistory->number,
            'message' => $sentHistory->content[2],
            'file' => $sentHistory->content[3],
            'sandbox' => 'false',
        ];

        $response = Http::withHeaders($headers)->post($url, $body);

        if ($response->successful()) {

            // Update history record with success
            $sentHistory->update([
                'has_sent' => true,
                'failed_message' => null
            ]);
        } else {
            Log::error('Error sending message to ' . $sentHistory->number . ': ' . $response->status() . ': ' . $response->body());

            $sentHistory->update([
                'has_sent' => false,
                'failed_message' => 'Error: ' . $response->status() . ': ' . $response->body()
            ]);
        }
    }
}

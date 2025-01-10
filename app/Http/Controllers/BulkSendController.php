<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use App\Models\App;
use App\Models\Number;
use App\Models\SentHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulkSendController extends Controller
{
    public function BulkSendFile(Request $request)//: JsonResponse
    {
        // create validate
        $request->validate([
            'sender' => 'required|string|exists:apps,sender_block',
            'message' => 'required|string',
            'file' => 'required|file',
            'messages_per_device' => 'optional|integer|default:10',
        ]);

        $getApp = App::userApps($request->sender);
        $appLimit = $request->input('device_limit', $getApp->count());
        $getApp = $getApp->take($appLimit);

        $numbers = Number::select('number')->where('is_active', true)
            ->where('slug', $request->sender)
            ->get();

        $message = $request->input('message');
        $file = $request->input('file');
        $messagesPerApp = $request->input('messages_per_device', 10);

        $interval = $request->input('interval', ['starts' => 5, 'ends' => 60]);
        $delayInSeconds = rand($interval['starts'], $interval['ends']);

        $currentAppIndex = 0;

        foreach ($numbers as $index => $number) {

            // Check if there is already a successful send with the same content for the same number
            if (SentHistory::where('number', $number->number)
                ->where('has_sent', true)
                ->whereJsonContains('content', [$request->sender, $request->receiver_block, $message, $file])
                ->exists()
            ) {
                continue;
            }

            // Selects the current app and limits the number of messages per app
            $app = $getApp[$currentAppIndex % $appLimit];

            // Create the sending history before dispatching the job
            $sentHistory = SentHistory::create([
                'number' => $number->number,
                'content' => [$request->sender, $request->receiver_block, $message, $file],
                'sent_by' => $app->appkey,
                'has_sent' => false,
                'failed_message' => null
            ]);

            // Dispatch the job with delay to space out the sends
            SendMessageJob::dispatch($app, $sentHistory->id)
                ->delay(now()->addSeconds($delayInSeconds * $index));

            $currentAppIndex++;
        }

        return response()->json(['message' => 'Messages sent for processing.']);
    }
}



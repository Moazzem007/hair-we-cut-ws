<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\MulticastSendReport;
use GuzzleHttp\Client as GuzzleClient;

class FcmController extends Controller
{
    protected $messaging;

    public function __construct()
{
    $credentials = env('FIREBASE_CREDENTIALS');

    if (empty($credentials) || !file_exists($credentials)) {
        throw new \RuntimeException("Firebase credentials file not found: {$credentials}");
    }

    // Disable SSL verification for local development (unsafe)
    putenv('CURL_CA_BUNDLE=');
    putenv('SSL_CERT_FILE=');

    $factory = (new \Kreait\Firebase\Factory)
        ->withServiceAccount($credentials);

    if ($projectId = env('FIREBASE_PROJECT_ID')) {
        $factory = $factory->withProjectId($projectId);
    }

    $this->messaging = $factory->createMessaging();
}


    /**
     * Send single device notification (recommended SDK way)
     */
    public function sendNotification(Request $request)
    {
        $token = $request->input('token');
        $title = $request->input('title', 'Title');
        $body  = $request->input('body', 'Body');

        if (empty($token)) {
            return response()->json(['success' => false, 'error' => 'token is required'], 422);
        }

        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            $response = $this->messaging->send($message);

            // Kreait returns a message ID string on success. Return it for debugging.
            return response()->json(['success' => true, 'messageId' => $response]);
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            // Messaging related exception (invalid token, etc)
            return response()->json(['success' => false, 'type' => 'messaging', 'error' => $e->getMessage()], 500);
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            // Auth / credentials / general firebase issues
            return response()->json(['success' => false, 'type' => 'firebase', 'error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'type' => 'other', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Send to multiple tokens (multicast, up to 500 tokens per call)
     */
    public function sendMulticast(Request $request)
    {
        $tokens = $request->input('tokens', []); // array of tokens
        $title = $request->input('title', 'Title');
        $body  = $request->input('body', 'Body');

        try {
            $notification = Notification::create($title, $body);
            $message = CloudMessage::new()->withNotification($notification);

            /** @var MulticastSendReport $report */
            $report = $this->messaging->sendMulticast($message, $tokens);

            return response()->json([
                'success' => true,
                'sent' => $report->successes()->count(),
                'failed' => $report->failures()->count(),
                'failures' => array_map(fn($f) => $f->error()->getMessage(), iterator_to_array($report->failures()))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

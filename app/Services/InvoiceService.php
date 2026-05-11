<?php

namespace App\Services;

use App\Dao\Models\Payment;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    public static function generate($payment_id)
    {
        $data = Payment::select(['payment.*', 'iuran.*', 'users.*'])
            ->leftJoinRelationship('has_iuran')
            ->leftJoinRelationship('has_user')
            ->where('payment_id', $payment_id)
            ->firstOrFail();

            $message = 'NOTIFIKASI PEMBAYARAN' . PHP_EOL . PHP_EOL;
            $message = $message.'Altet : ' . $data->name . PHP_EOL;
            $message = $message.'Voucher : ' . $data->iuran_nama . PHP_EOL;
            $message = $message.'Total : ' . $data->payment_value . PHP_EOL.PHP_EOL;
            $message = $message.'Link : ' . $data->payment_url . PHP_EOL;

            $send = [
                'target' => $data->phone,
                'message' => $message,
                // Optional: 'file' => storage_path('app/notifications/receipt_' . $send->id . '.jpg')
            ];

            $return = self::sendMessage($send);
            Payment::find($payment_id)->update([
                'payment_send' => now('Y-m-d'),
                'payment_wa' => json_encode($return)
            ]);
        }
    }

    /**
     * Sends a WhatsApp message using the configured gateway.
     *
     * @param array $data
     * @return string|false
     */
    private function sendMessage(array $data)
    {
        $gateway = env('WA_GATEWAY', 'fonnte');

        switch ($gateway) {
            case 'fonnte':
                $config = [
                    'url' => 'https://api.fonnte.com/send',
                    'headers' => [
                        'Authorization: ' . env('FONNTE_API_TOKEN')
                    ],
                    'options' => [
                        'target' => $data['target'] ?? null,
                        'message' => $data['message'] ?? null,
                        'url' => $data['url'] ?? null,
                        'filename' => $data['filename'] ?? null,
                        'schedule' => $data['schedule'] ?? 0,
                        'typing' => $data['typing'] ?? false,
                        'delay' => $data['delay'] ?? 2,
                        'countryCode' => $data['countryCode'] ?? 62,
                        'location' => $data['location'] ?? null,
                        'followup' => $data['followup'] ?? 0,
                        'inboxid' => $data['inboxid'] ?? 0,
                        'duration' => $data['duration'] ?? 1,
                    ]
                ];
                if (isset($data['file'])) {
                    $config['options']['file'] = new \CURLFile($data['file']);
                }
                break;
            case 'twilio':
                $config = [
                    'url' => 'https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json',
                    'headers' => [
                        'Authorization: Basic ' . base64_encode(env('TWILIO_ACCOUNT_SID') . ':' . env('TWILIO_AUTH_TOKEN'))
                    ],
                    'options' => [
                        'To' => $data['target'],
                        'From' => env('TWILIO_FROM'),
                        'Body' => $data['message'],
                    ]
                ];
                break;
            default:
                Log::error("Unsupported WhatsApp gateway: {$gateway}");
                return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $config['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $config['options'],
            CURLOPT_HTTPHEADER => $config['headers'],
        ]);

        $response = curl_exec($curl);
        $error_msg = null;
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if ($error_msg) {
            Log::error("WhatsApp sending error ({$gateway}): " . $error_msg);
            return false;
        }

        Log::info("WhatsApp sending response ({$gateway}): " . $response);
        return $response;
    }
}

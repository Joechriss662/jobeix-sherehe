<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsForwarderService
{
    public static function send($phone, $message)
    {
        $smsForwarderUrl = config('services.sms_forwarder.url', 'http://smsserver.dc.konzo.xyz/odata/odata/Texts');
        return Http::post($smsForwarderUrl, [
            'target' => $phone,
            'message' => $message,
            'rqStatusReport' => true,
        ]);
    }
}
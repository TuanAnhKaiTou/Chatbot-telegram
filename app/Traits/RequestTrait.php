<?php

namespace App\Traits;

trait RequestTrait {
    private function apiRequest($method, $params = []) {
        $url = 'https://api.telegram.org/bot'. config('app.telegram_token'). '/'. $method;
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
        $resp = curl_exec($handle);
        if ($resp == false) {
            curl_close($handle);
            throw new \Exception($resp['description']);
            return false;
        }
        curl_close($handle);
        $resp = json_decode($resp, true);
        if ($resp['ok'] == false) {
            throw new \Exception($resp['description']);
        }
        $resp = $resp['result'];
        return $resp;
    }
}

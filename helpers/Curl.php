<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/7/2018
 * Time: 9:43 PM
 */

namespace App\Helpers;


/**
 * Class Curl
 * @package App\Helpers
 */
class Curl
{

    /**
     * @param $url
     * @param $params
     * @param $headers
     * @return mixed|string
     */
    public static function Get($url, $params, $headers)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url . $params,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,

        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
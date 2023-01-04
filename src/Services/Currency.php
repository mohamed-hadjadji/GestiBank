<?php

namespace App\Services;

class Currency
{
    /**
     * Retourne le cours de conversion entre deux devices différentes
     * *
    @param string $from
     * @param string $to
     * @param string $amount
     * @return string
     */
    public function conversion($from, $to, $amount)

    {

        $curl = curl_init();

        //$date = "2022-12-09";
        $date = date("Y-m-d");

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.apilayer.com/currency_data/change?start_date=$date&end_date=$date",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/plain",
                "apikey: L9OiBiaZ4lKRj76AvZf5iteTs4gKtn3x"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $ouput = $from.$to; //USDEUR

        $response= json_decode($response, true);
        return floatval($response["quotes"][$ouput]["start_rate"])*floatval($amount);
    }
}
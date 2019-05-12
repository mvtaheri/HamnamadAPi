<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/13/2018
 * Time: 9:33 PM
 */

namespace App\Jobs\Crawl;


use App\Contract\Job\Job;
use App\Models\Market;

class SyncMarkets implements Job
{

    /**
     * @return mixed
     */
    public function handle()
    {
//        $intradaytrades = file_get_contents('http://185.173.106.244/api/v1/intradaytrades');
//
//        $intradaytrades = json_decode($intradaytrades)->data;
//
//        foreach ($intradaytrades as $intradaytrade) {
//
//            $trade['id'] = $intradaytrade->instrument->id;
//            $trade['date_time'] = $intradaytrade->date_time;
//            $trade['close_price'] = $intradaytrade->close_price;
//
//            $trades[] = $trade;
//        }
//
//        $uniqTrades = $this->arrayUnique($trades);
//
//        foreach ($uniqTrades as $trade) {
//            $instrument = file_get_contents("http://185.173.106.244/api/v1/instruments?instrument_id={$trade['id']}");
//            $market['id'] = $trade['id'];
//            $market['close_price'] = $trade['close_price'];
//            $market['date_time'] = $trade['date_time'];
//            $market['meta'] = json_decode($instrument)->data[0];
//
//            $instruments[] = $market;
//        }
//
//
//        foreach ($instruments as $instrument) {
//
//            if ($market = Market::findOne(['mabna_id' => $instrument['id']])) {
//                $market = Market::updateOneById($market->_id,
//                    [
//                        'sell.last_price' => $market->sell->price,
//                        'sell.price' => (int)$instrument['close_price'],
//                    ]
//                );
//            } else {
//                $market = Market::generate(
//                    $instrument['meta']->name,
//                    $instrument['meta']->english_name,
//                    $instrument['id'],
//                    (int)$instrument['close_price']
//                );
//            }
//        }

        $markets = Market::find();

        foreach ($markets as $market) {
            $lastPrice = $market->sell->price;
            $lastBuyPrice = ($market->buy->price > 0) ? $market->buy->price : rand(50, 100);

            $price = [
                $lastPrice + rand(5, 15),
                $lastPrice - rand(1, 15)
            ];

            $buy = [
                $lastBuyPrice + rand(5, 15),
                $lastBuyPrice - rand(1, 15)
            ];

            Market::updateOneById($market->_id,
                [
                    'buy.last_price' => $lastBuyPrice,
                    'buy.price' => $buy[array_rand($buy)],
                    'sell.last_price' => $lastPrice,
                    'sell.price' => $price[array_rand($price)],
                ]
            );
        }

//        if (!is_object($market)) {
//            return respond()->fail();
//        }
    }

    private function arrayUnique($array, $key = 'id')
    {
        $ids = array_column($array, 'id');
        $result = [];
        foreach ($ids as $id) {
            $keys[] = array_search($id, $ids);
        }

        $keys = array_unique($keys);
        foreach ($keys as $item) {
            $result[] = $array[$item];
        }

        return $result;
    }
}
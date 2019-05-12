<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/27/2018
 * Time: 6:42 AM
 */

namespace App\Jobs\Efficiency;


use App\Contract\Job\Job;
use App\Models\Efficiency;

class MarketEfficiency implements Job
{
    private $userId;

    /**
     * MarketEfficiency constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = (int)$userId;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $markets = Efficiency::byMarket($this->userId);

        return $this->calculateEfficiency($markets);
    }


    /**
     * @param $data
     * @return array
     */
    private function calculateEfficiency($data)
    {
        $efficiencies = [];
        foreach ($data as $d) {
            $sum = (array)$d->value;
            $sumEfficiency = 0;
            foreach ($sum as $s) {
                $sumEfficiency += ($s + 1);
            }
            $efficient['sum'] = $sumEfficiency - 1;
            $efficient['data'] = $d->_id;

            $efficiencies[] = $efficient;
        }

        return $efficiencies;
    }
}
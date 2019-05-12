<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/20/2018
 * Time: 2:02 AM
 */

namespace App\Jobs\User;


use App\Contract\Job\Job;
use App\Models\Efficiency;
use App\Models\Order;
use App\Models\People;

class GetUserState implements Job
{
    protected $userId;

    /**
     * GetUserState constructor.
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
        $categories = Efficiency::byCategories($this->userId);
        $markets = Efficiency::byMarket($this->userId);


        $efficienciesMarket = $this->calculateEfficiency($markets);
        $efficienciesCategories = $this->calculateEfficiency($categories);




    }

}
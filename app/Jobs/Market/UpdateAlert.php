<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/12/17
 * Time: 1:35 AM
 */

namespace App\Jobs\Market;


use App\Contract\Job\Job;
use App\Models\Alert;
use MongoDB\BSON\ObjectID;

/**
 * Class UpdateAlert
 * @package App\Jobs\Market
 */
class UpdateAlert implements Job
{
    /**
     * @var
     */
    protected $request;
    /**
     * @var
     */
    protected $userId;

    /**
     * SetAlertRequest constructor.
     * @param $request
     * @param $userId
     */
    public function __construct($request, $userId)
    {
        $this->request = $request;
        $this->userId = (int)$userId;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $updateAlert = Alert::updateOne(
            ['user_id'=>$this->userId,'market_id' => new ObjectID(request('market_id'))],
            ['rate' => (int)request('rate')]
        );
        
        if(is_object($updateAlert))
            return respond()->success();
        else
            return respond()->fail();

    }
}
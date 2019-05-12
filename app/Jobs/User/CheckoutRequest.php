<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/1/2018
 * Time: 9:38 PM
 */

namespace App\Jobs\User;


use App\Contract\Job\Job;
use App\Model\CheckoutRequest as CheckoutRequestModel;
use App\Models\People;

class CheckoutRequest implements Job
{
    protected $request;

    protected $userId;

    /**
     * CheckoutRequest constructor.
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
        $user = People::findByUserId($this->userId);

        $this->checkWalletAvailable($user);

        return CheckoutRequestModel::updateOne(['user_id' => $this->userId],
            [
                'amount' => (int)$this->request['amount'],
            ], ['upsert' => true]
        );

    }

    /**
     * @param $user
     */
    private function checkWalletAvailable($user): void
    {
        $available = $user->wallet->real->available ?? 0;

        if ($available < (int)$this->request['amount']) {
            die(respond()->fail('user available is less than amount'));
        }
    }
}

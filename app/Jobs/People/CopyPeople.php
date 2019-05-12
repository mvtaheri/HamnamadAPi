<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/7/17
 * Time: 2:20 AM
 */

namespace App\Jobs\People;


use App\Contract\Job\Job;
use App\Models\People;
use App\Models\Setting;
use App\Models\User;

/**
 * Class FollowingPeople
 * @package App\Jobs\People
 */
class CopyPeople implements Job
{
    /**
     * @var
     */
    protected $request;
    /**
     * @var int
     */
    protected $userId;

    /**
     * FollowingPeople constructor.
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
        //Check Count of User Followed
        $people=People::where('user_id',$this->userId)->first();
            if(count($people->following())>Setting::countOfFollowedPeople())
                return response()->json(['status'=>false ,'message' =>'Count of people which a user able followed is limited'] ,400);
            if ($request->input('amount') < Setting::minInvestment())
                return response()->json(['status' =>false , 'message' =>'Value of amount is less than minimum investment']);
            $people=People::where('user_id',$this->userId);
            $people->

        if (count((People::findOne(['user_id' => $this->userId]))->following) >= Setting::countOfFollowedPeople())
            return respond()->fail('Count of people which a user able followed is limited');

        //Check amount is great than minimum of investment value
        if ((int)$this->request ['amount'] < Setting::minInvestment())
            return respond()->fail('Value of amount is less than minimum investment');

        //Check User is exist or User id is own
        if (
            is_null(User::findOne('id', (int)$this->request ['following_id'], ['*'])) ||
            (User::findOne('id', (int)$this->request ['following_id'], ['*']))->id == $this->userId
        )
            return respond()->fail('User Not found');


        if (People::find([
            'user_id' => $this->userId,
            'copy_people_following.user_id' => (int)$this->request ['following_id']
        ]))
            die(respond()->fail('People Followed'));

        $this->addUserToFollowingList($this->userId, $this->request);
        $this->addPeopleToFollowerUser($this->userId, $this->request);

        return respond()->success();

    }


    /**
     * @param $userId
     * @param $request
     */
    private function addUserToFollowingList($userId, $request)
    {
        People::updateOne(['user_id' => $userId],
            [
                '$addToSet' => [
                    'copy_people_following' => [
                        'user_id' => (int)$request ['following_id'],
                        'amount' => (int)$request ['amount'],
                        'stop' => (int)$request ['stop']
                    ]
                ]
            ],
            [], null);
    }

    /**
     * @param $userId
     * @param $request
     */
    private function addPeopleToFollowerUser($userId, $request)
    {
        People::updateOne(['user_id' => (int)$request['following_id']],
            [
                '$addToSet' => [
                    'copy_people_follower' => [
                        'user_id' => (int)$userId,
                        'amount' => (int)$request['amount'],
                        'stop' => (int)$request['stop']
                    ]
                ],
                '$inc' => ['people_follow' => 1]
            ],
            [], null);
    }
}

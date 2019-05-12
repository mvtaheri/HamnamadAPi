<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/7/17
 * Time: 2:20 AM
 */

namespace App\Jobs\People;

use App\Jobs\Job;
use App\Model\User;
use App\Model\People;
use Carbon\Carbon;

class FollowPeople extends Job
{
    /**
     * @var
     */
    protected $following_id;
    /**
     * @var int
     */
    protected $userId;

    /**
     * FollowingPeople constructor.
     * @param $request
     * @param $userId
     */
    public function __construct($following_id, $userId)
    {
        $this->userId = $userId;
        $this->following_id =(int)$following_id;
    }


    /**
     * @return mixed
     */
    public function handle()
    {
        try{
            $user = User::where('id', $this->following_id)->first();
            if (!$user)
                return response()->json(['status' => false, 'message' => 'user not found !'], 400);
            $following= $this->addUserToFollowingList($this->userId, $this->following_id);
            $follower=$this->addPeopleToFollowerUser($this->userId, $this->following_id);
            return;
        }catch(\Exception $exception){
         throw new \Exception($exception->getMessage());
        }


    }


    private function addUserToFollowingList($userId, $following_id)
    {
        return People::where('user_id',$userId)->push(
            'following',
            [
                'user_id'=>$following_id,
                'created_at' =>Carbon::now()->toDateString()
            ]
            ,true
        );

    }


    private function addPeopleToFollowerUser($userId, $following_id)
    {
        return People::where('user_id',$following_id)->push(
            'follower',
            [
                'user_id'=>$userId ,
                'created_at' =>Carbon::now()->toDateString()
            ],
            true
        );
    }
}

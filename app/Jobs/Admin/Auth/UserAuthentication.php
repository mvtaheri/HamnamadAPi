<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Alipour
 * Date: 7/28/2017
 * Time: 12:22 PM
 */

namespace App\Jobs\Admin\Auth;
use App\Contract\Job\Job;
use App\Models\User;


/**
 * Class Auth
 * @package App\Jobs\Auth
 */
class UserAuthentication implements Job
{
    /**
     * @var
     */
    protected $request;
    /**
     * @var
     */
    protected $table;

    /**
     * Auth constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
       if ( $user = User::findOne(['username' => $this->request['username'], 'is_admin' => 1])){
           if(User::isMatchedPassword($this->request['password'],$user->password))
               return respond(User::generateToken($user->id))->success();
       }else
            return respond()->fail('User Not Found',403);

    }
}
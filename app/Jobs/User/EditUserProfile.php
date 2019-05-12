<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/13/17
 * Time: 12:38 AM
 */

namespace App\Jobs\User;


use App\Contract\Job\Job;
use App\Models\User;

/**
 * Class EditUserProfile
 * @package App\Jobs\User
 */
class EditUserProfile implements Job
{
    /**
     * @var int
     */
    protected $userId;
    /**
     * @var array
     */
    protected $request;

    /**
     * EditUserProfile constructor.
     * @param $userId
     * @param $request
     */
    public function __construct($userId, $request)
    {
        $this->userId = $userId;
        $this->request = $request;

    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $user = User::findById((int)$this->userId);
        if ($this->request['password'] !== "" && isset($this->request['password'])){
            $this->PasswordFieldValidate($this->request,$user->password);
            $this->clearValueOfArrayRequestField((array)$user);
          }

        return User::updateOne('id',(int)$this->userId,$this->request);
    }

    private function PasswordFieldValidate($request,$oldPassword)
    {
        $this->checkEnteredPasswordWithRepeatedThat($request['password'], $request['repeat_password']);
        $this->checkEnteredPasswordWithOldPassword($request['old_password'],$oldPassword);
        $this->removeAdditionalFields();

        return true;
    }

    private function checkEnteredPasswordWithRepeatedThat($password, $repeatPassword)
    {
        if ($password !== $repeatPassword)
            return die(respond('Password and repeat password is not match ')->fail());

        return true;
    }

    private function checkEnteredPasswordWithOldPassword($newPassword,$oldPassword)
    {
        if (!User::isMatchedPassword($newPassword, $oldPassword))
            return die(respond('Old password entered is not correct ')->fail());

        return true;
    }

    private function clearValueOfArrayRequestField($user)
    {
        foreach ($this->request as $key => $item) {
            if ($item == '' || !in_array($key,array_keys($user))){
                unset($this->request[$key]);
            }
            if ($key == 'password')
                $this->request['password']= User::hashPassword($this->request['password']);
        }

        return true;
    }

    private function removeAdditionalFields()
    {
        unset($this->request['old_password']);
        unset($this->request['repeat_password']);

        return true;
    }
}
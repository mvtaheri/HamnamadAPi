<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 3/16/2019
 * Time: 1:11 PM
 */

namespace App\Model;


use  Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Capsule\Manager as DB;

class ResetPassword extends Eloquent
{

  protected $table ='reset_password';


    /**
     * @param array $fillable
     * @return mixed
     */
    public function add(array $fillable){
        return self::add([
            'user_id'=>$fillable['user_id'],
            'user_email'=>$fillable['user_email'],
            'token'=>$fillable['token'],
            'expire_at'=> strtotime("+20 minutes", time())
        ]);
    }


    /**
     * @param $email
     * self
     */
 public  function deleteOldToken($email)
 {
     return self::where('user_email',$email)->delete();

 }

    /**
     * @param $token
     * @return mixed
     */
 public static function findByToken($token) {
     return self::where('token',$token)->first();
 }



}

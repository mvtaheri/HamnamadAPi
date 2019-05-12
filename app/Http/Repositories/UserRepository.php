<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/22/2019
 * Time: 2:03 PM
 */

namespace App\Http\Repositories;

use App\Model\User;
use Illuminate\Http\Request;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends Repository
{
    function __construct()
    {
        parent::__construct(new User());
    }

    public function store(Request $request)
    {
        try {
            return $this->create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'name' => $request->name,
                'type' => $request->type,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'country' => $request->country,
                'address' => $request->address,
                'know_level' => $request->know_level,
                'experience_level' => $request->experience_level,
                'avatar' => '',
                'setting' => serialize([
                    "full_name" => true,
                    "share_activity" => true,
                    "default_portfolio" => "overview",
                    "default_page" => "watchlist",
                    "language" => "english"
                ])
            ]);

        } catch (\PDOException $exception) {
            throw new \PDOException($exception->getMessage());
        }

    }

    /**
     * @param array $credintial
     * @return bool
     */
    public function attempt(array $credintial){
        $user=$this->model->where('username',$credintial['username'])->first();
        if ($user)
          if(Hash::check($credintial['password'],$user->password))
              return true;

              return false;
    }

}

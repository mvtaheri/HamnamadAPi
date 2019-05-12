<?php

namespace App\Http\Repositories;


use App\Model\People;
use App\Repositories\Repository;

class PeopleRepository extends Repository
{
   function __construct()
   {
       parent::__construct(new People());
   }

   public function findPeopleByUserId($user_id){
       return $this->model->where('user_id',$user_id)->first();
   }

   public function preparePeopleWithAssociateUser(){
       return $this->model::select('user_id','people_follow','risk','efficiency')->where('people_follow','>',0)
           ->with(['user'=>function($query){
               $query->where('enabled',1);
           }])->orderBy('people_follow','desc')->get();
   }
}

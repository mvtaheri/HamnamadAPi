<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/4/2019
 * Time: 3:47 PM
 */

namespace App\Http\Controllers;

use App\Model\Market;
use App\Model\People;
use App\Model\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addItemToWatchlis(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:mongodb.watchlists,_id',
            'item_id' => 'required'
        ],[
            'id'=>'Enter watchlist id to add item to it',
            'item' => 'Enter people or market id want to add'
        ]);
        try {
            $watchlist = Watchlist::find($request->input('id'));
            if ($watchlist->owner_id != Auth::user()->id)
                return response()->json(['status' => false, 'message' => 'Curent user not watchlist owner'], 400);
            if ($item = Market::find($request->input('item_id'))) {
                $item=$watchlist->markets()->attach($item);
            } elseif ($item = People::find($request->input('item_id'))) {
              $item=  $watchlist->peoples()->attach($item);
            } else
                return response()->json(['status' => false, 'message' => 'Item not found'], 400);

            return response()->json(['status'=>true ,'message' =>'Item Add' ,'data' =>$item] ,200);

        } catch (\Exception $exception) {
            return response()->json(['status' => true, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createNewWatchlist(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|alpha_dash'
        ]);
        try {
            $watchlis = Watchlist::create([
                'name' => $request->input('name'),
                'owner_id' => Auth::user()->id,
                'default' =>false
            ]);
            return response()->json(['status' => true, 'data' => $watchlis], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWatchlist(){
        $user=Auth::user();
        if (!$user)
            return response()->json(['status'=>false ,'message' =>'user not found']);
        try{
            $watchlist =  $user->watchlists()->get();
            $watchlistzzz[]=$watchlist->map(function ($watchlist){
                return [ $watchlist->name => [
                    'people'=> $watchlist->peoples()->get(['_id','username','user_id','name']),
                    'markets'=> $watchlist->markets()->get(['_id','title'])
                ]];
            });
            return response()->json(['status'=>true ,'data' =>$watchlistzzz],200);
        }catch(\Exception $exception){
            return response()->json(['status' =>false ,'message' =>$exception->getMessage()] ,400);
        }

    }

    /**
     * remove item from user watchlist
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function removeItem(Request $request){
        $this->validate($request,[
            'id' => 'required|exists:mongodb.watchlists,_id',
             'item_id' =>'required'
        ],[
            'item_id.required'=>'Enter all to remove Watchlist or Enter Item id'
        ]);
        $watchlist=Watchlist::find($request->input('id'));
        if ($watchlist->owner_id != Auth::user()->id)
            return response()->json(['status' =>false ,'message' =>'Curent user not watchlist owner'],400);
        if ($request->input('item_id') == 'all')
            $watchlist->delete();
        if ($item=Market::find($request->input('item_id'))){
            $r=$watchlist->markets()->detach($item);
            return response()->json(['status' =>true ,'message' =>'market remove from watchlist'],200);
        }
        if ($item=People::find($request->input('item_id'))){
            $r=$watchlist->peoples()->detach($item);
            return response()->json(['status' =>true ,'message' =>'People remove from watchlist'],200);
        }
        else
            return response()->json(['status' =>true ,'message' =>'Item not found'],200);

    }

}

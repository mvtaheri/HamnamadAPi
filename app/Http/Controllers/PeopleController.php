<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/7/17
 * Time: 1:48 AM
 */

namespace App\Http\Controllers;


use App\Http\Repositories\PeopleRepository;
use App\Model\Category;
use App\Model\Market;
use App\Model\Parents;
use App\Model\User;
Use App\Model\People;
use App\Jobs\People\CopyPeople;
use App\Jobs\People\FollowPeople;
use App\Model\Copy;
use App\Model\CopyPeopleFollowing;
use App\Model\Setting;
use App\Models\CategoryParent;
use App\Models\MarketCategory;
use App\Model\Order;
use App\Requests\People\SearchRequest;
use App\Transformers\User\UserTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID;
use MongoDB\Exception\RuntimeException;

/**
 * Class PeopleController
 * @package App\Controllers
 */
class PeopleController extends Controller
{

    /**
     * @var
     */
    protected $userId;

    protected $peopleRepository;


    /**
     * PeopleController constructor.
     */
    public function __construct(PeopleRepository $peopleRepository)
    {
        $this->userId = Auth::user()->id;
        $this->peopleRepository=$peopleRepository;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function followPeople(Request $request)
    {
        $this->validate($request, [
            'following_id' => 'required|exists:users,id'
        ], [
            'exists' => 'User Not Found!'
        ]);
        $following_id = $request->input('following_id');
        try {
            People::where('user_id', $this->userId)->push('following', ['user_id' => $following_id, 'created_at' => Carbon::now()->toDateString()], true);
            People::where('user_id', $following_id)->push('follower', ['user_id' => $this->userId, 'created_at' => Carbon::now()->toDateString()], true);
            return response()->json(['status' => true, 'message' => 'successfully Following User'], 200);
        } catch (RuntimeException $runtimeException) {
            return response()->json(['status' => false, 'message' => $runtimeException->getMessage()], 400);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function copyPeople(Request $request)
    {
        $this->validate($request, [
            'following_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:0',
            'stop' => 'required|integer|min:0'
        ], [
            'exists' => 'User Not Found'
        ]);
        $following_id = (int)$request->input('following_id');
        $amount=$request->input('amount');
        $stop =$request->input('stop');
        $following = People::forUser($this->userId)->first();
        $setting = Setting::withLang('en')->first();
        $setting->count_of_followed_people;
        if (count($following->following) > $setting->count_of_followed_people)
            return response()->json(['status' => false, 'message' => 'Count of people which a user able followed is limited'], 400);
        if ($request->input('amount') < $setting->min_investment)
            return response()->json(['status' => false, 'message' => 'Value of Amount is less than minimum investment']);
        $followed = $following->copys()->contains(function ($copy) use ($following_id) {
            if ($copy->type == 'following' && $copy->user_id == $following_id)
                return true;
        });
        if ($followed)
            return response()->json(['status' => false, 'message' => 'User Already Copy!']);
        $following->copys()->create([
             'user_id' => $following_id,
             'amount' => $amount,
             'stop' => $stop,
             'type' =>'following'
        ]);
        $follower=People::forUser($following_id)->first();
        $follower->copys()->create([
            'user_id'=>$this->userId,
            'amount' =>$amount,
            'stop'  =>$stop,
            'type' =>'follower'
        ]);
        $follower->increment('people_follow',1);

        return response()->json(['status' => true, 'message' => 'User Successfully Copy']);
    }

    public function discoverPeople()
    {
        $result['most_copy'] = $this->peopleRepository->preparePeopleWithAssociateUser();
        try {
            $user_order = User::with("order:id")->get();
            $result['most_trade'] = $user_order->map(function ($user) {
                if ($user->order()->count() > 0) {
                    $temp['user'] = $user;
                    $temp['order_count'] = $user->order()->count();
                    return $temp;
                }
            })->reject(function ($item) {
                return empty($item);
            })->sortBy('order_count', null, true);
        } catch (\MongoException $exception) {
            return response()->json($exception->getMessage());
        }
        return response()->json($result, 200);
    }

    public function search(Request $request)
    {
        $parent=(int)$request->input('parent');
            if ($request->has('parent'))
           $data=Parents::where('id',$parent)->with(['category','category.child'])->get();
        else
            $data= Parents::with(['category','category.child'])->get();
        if ($request->has('category_id')){
            $data=Market::where('category_id',(int)$request->input('category_id'))->get();
        }
//        return response()->json(['status'=>true ,'data'=>$data],200);
//        $allMarkets=$data->transform(function ($market){
//           return $market->_id;
//        })->toArray();
//        $orderrerere=Order::raw(function($collection) use ($allMarkets) {
//            $collection->aggregate([
//                'market_id' => ['$in' => $allMarkets ?? []]
//            ]);
//        });
//
//        if ($request->has('limit')){
//            $limit=$request->input('limit');
//            switch($limit){
//                case '1d':
//                    $query->where('created_at' ,'>=',Carbon::now()->subDays(1)->timestamp);
//                    break;
//                case '7d':
//                    $query->where('created_at','>=',Carbon::now()->subDays(7)->timestamp);
//                    break;
//                case '30d':
//                    $query->where('created_at','>=',Carbon::now()->subDays(30)->timestamp);
//                    break;
//                case '1w':
//                    $query->where('created_at','>=',Carbon::now()->subWeek(1)->timestamp);
//                    break;
//                case '1m':
//                    $query->where('created_at','>=',Carbon::now()->subMonth(1)->timestamp);
//                    break;
//                case '3m':
//                    $query->where('created_at','>=',Carbon::now()->subMonth(3)->timestamp);
//                    break;
//                case '6m':
//                    $query->where('created_at','>=',Carbon::now()->subMonth(6)->timestamp);
//                    break;
//                case '1y':
//                    $query->where('created_at','>=',Carbon::now()->subYear(1)->timestamp);
//                    break;
//
//            }
//        }
//        $orders=$query->get();

        return response()->json(['status'=>true ,'data'=>$data],200);

    }
}

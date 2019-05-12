<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Alipour
 * Date: 7/27/2017
 * Time: 12:06 AM
 */

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Model\Alert;
use App\Model\Category;
use App\Model\Feed;
use App\Model\Market;
use App\Model\Order;
use App\Model\User;
use App\Model\Wallet;
use App\Model\People;
use App\Model\Comment;
use App\Model\LikeFeed;
use App\Model\OrderBook;
use App\Rules\Base64Image;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Requests\User\PortfolioRequest;
use App\Requests\User\SetOrderWatchlist;
use App\Http\Repositories\UserRepository;
use App\Http\Resources\User\UserResource;
use App\Requests\User\UserProfileRequest;
use App\Requests\User\SetDefaultWatchlist;
use App\Transformers\User\OrderTransformer;
use App\Requests\User\EditUserProfileRequest;
use App\Transformers\Wallet\WalletTransformer;
use App\Transformers\Admin\User\UserTransformer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends Controller
{
    /**
     * @var
     */
    protected $userId;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->userId = Auth::user()->id;
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request, UserRepository $userRepository)
    {
        //     before:-18 years

        $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required|alpha_dash|min:6|confirmed',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'type' => 'integer|max:1',
            'gender' => 'required|in:male,female',
            'birthday' => 'required|date_format:Y-m-d',
            'country' => 'string',
            'experience_level' => 'integer|max:1',
            'know_level' => 'integer|max:1',
            'address' => 'string',
        ]);

        try {
            $user = $userRepository->store($request);
            $people = People::addUserInformationAndVirtualFund($user);
            $people->peoplewallet()->createMany([
                ['type' => 'real',
                    'available' => 0,
                    'allocated' => 0,
                    'morning_available' => 0

                ], [
                    'type' => 'virtual',
                    'available' => 10000,
                    'allocated' => 0,
                    'morining_available' => 0
                ]]);
            return response()->json(['status' => true, 'message' => 'Register Successfull!'], 200);
        } catch (\PDOException $pdo) {
            Log::error($pdo->getMessage());
            return response()->json(['status' => false, 'message' => $pdo->getMessage()], 500);
        }

    }

    public function updateUserInfo(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:users',
            'name' => 'string',
            'password' => 'alpha_dash|min:6|confirmed',
            'type' => 'integer|max:1',
            'gender' => 'in:male,female',
            'birthday' => 'date_format:Y-m-d',
            'country' => 'string',
            'experience_level' => 'integer|max:1',
            'know_level' => 'integer|max:1',
            'address' => 'string',
        ]);
        try {
            $user = User::find($request->input('id'));
            $user->update($request->all());
            $people = $user->people()->first();
            $peoplewallet = $people->wallet()->get();
            if (sizeof($peoplewallet) <= 0) {
                $people->wallet()->createMany([
                    ['type' => 'real',
                        'available' => 0,
                        'allocated' => 0,
                        'morning_available' => 0
                    ], [
                        'type' => 'virtual',
                        'available' => 10000,
                        'allocated' => 0,
                        'morining_available' => 0
                    ]]);
            }
            return response()->json(['status' => true, 'message' => 'user info has been successfully update']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @return string
     */
    public function getUserProfile()
    {
        try {
            $user = Auth::user();
            $userfeed = $user->feeds()->get();
            $people = People::forUser($user->id)->with('copys', 'following')->first();
            $copyPeopleFollower = $people->copys->filter(function ($copy) {
                if ($copy->type == 'follower')
                    return $copy;
            });
            $userFollowing = $people->following;
            $userdefaultWatchlist = $user->defaultWatchlist()->first();
            return response()->json(['status' => true,
                'data' => ['user_feed' => $userfeed,
                    'user' => $user,
                    'copy_people_follower' => $copyPeopleFollower,
                    'user_following' => $userFollowing,
                    'user_default_watchlis' => $userdefaultWatchlist]]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['status' => false, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function userList()
    {
        try {
            $users = UserResource::collection(User::activeUser()->get());
            return response()->json(['status' => true, 'data' => $users]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);

        }

    }

    /**
     * @return string
     */
    public function setDefaultWatchlist()
    {
        SetDefaultWatchlist::validate();

        People::updateOne(['user_id' => $this->userId], ['watchlist_setting.default' => $this->request['watchlist']]);

        return $this->respond()->success();
    }

    /**
     * @return string
     */
    public function setSortWatchlist()
    {
        SetOrderWatchlist::validate();


        $raw = file_get_contents('php://input');
        $watchlist = json_decode($raw, true);
        $category = $this->request['category'];

        if (empty($raw)) {
            die($this->respond()->fail('data is empty'));
        }
        switch ($this->request['type']) {
            case "user":
                foreach ($watchlist as $key => $item) {
                    if (User::findById((int)$item['user_id'])) {
                        $userWatchlist['user_id'] = (int)$item['user_id'];
                        $userWatchlist['order'] = (int)$key;
                    }

                    $order[] = $userWatchlist;
                }

                if ($order) {
                    $update = People::updateOne(
                        [
                            'user_id' => $this->userId,
                            "watchlist.{$category}.user" => ['$exists' => true]
                        ],
                        [
                            "watchlist.{$category}.user" => $order
                        ]);
                }
                break;

            case "markets":
                foreach ($watchlist as $key => $item) {
                    $marketWatchlist['market_id'] = new ObjectId($item['market_id']);
                    $marketWatchlist['order'] = (int)$key;

                    $order[] = $marketWatchlist;
                }


                $update = People::updateOne(
                    [
                        'user_id' => $this->userId,
                        "watchlist.{$category}.markets" => ['$exists' => true]
                    ],
                    [
                        "watchlist.{$category}.markets" => $order
                    ]);
                break;
        }

        if ($update && $update->getModifiedCount() > 0) {
            return $this->respond('watchlist Updated')->success();
        }

        return $this->respond()->fail('update action is failed');

    }

    /**
     * @return string
     */
    public function updateUserSetting(Request $request)
    {
        $this->validate($request, [
            'full_name' => 'string|min:3|max:50',
            'share_activity' => 'boolean',
            'default_portfolio' => 'string|max:100',
            'default_page' => 'string|max:100',
            'language' => 'in:English,Persian'
        ]);
        try {
            $setting = ['full_name', 'share_activity', 'default_portfolio', 'default_page', 'language'];
            foreach ($setting as $param) {
                if ($request->has($param))
                    $setValue["setting.$param"] = $request->$param;
            }
            $people = People::forUser($this->userId)->update($setValue, ['upsert' => true]);
            return response()->json(['status' => true, 'message' => 'Setting has been Updated successfully']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => true, 'message' => $exception->getMessage()]);
        }
    }

    /**
     *
     */
    public function getUserSetting()
    {
        try {
            $people = People::forUser($this->userId)->first();
            $setting['setting'] = $people->setting;
            $setting['people_id'] = $people->_id;
            $setting['user_name'] = $people->username;
            $setting['user_id'] = $people->user_id;
            return response()->json(['status' => true, 'data' => $setting]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function updateAvatar(Request $request)
    {
        $this->validate($request, [
            'photo' => ['required', new Base64Image()],
        ]);
        $image = $request->photo;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '_' . $this->userId . '_avatar_' . str_random(10) . '.' . 'png';
        $file = file_put_contents($imageName, base64_decode($image));
        $file = Storage::put(storage_path() . $imageName, base64_decode($image));
        if (!$file)
            return response()->json(['status' => false, 'message' => 'file not saved !'], 400);
        $user = Auth::user();
        $user->update([
            'avatar' => $imageName
        ]);
        $user->save();
        $contents = Storage::get($imageName);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    function getUserAvatar()
    {
        try {
            $user = Auth::user();
            $filename = Storage::get($user->avatar);
            if ($filename) {
                $image = base64_encode($filename);
                $src = 'data:' . mime_content_type($filename) . ';base64,' . $image;
            }
            return response()->json(['status' => true, 'message' => 'avatar is changeed'], 200);
        }catch(\Exception $exception){
            return response()->json(['status'=>true ,'message' =>$exception->getMessage()]);
        }
    }

    /**
     * @return string
     */
    public function deleteAvatar()
    {
        try{
        $user = Auth::user();
        $user->update(['avatar' => 'default.png']);
        $user->save();
        return response()->json(['status'=>true ,'message' =>'Avatar Is Set to default !'] ,200);
        }catch (\Exception $exception){
            return response()->json(['status'=>true ,'message'=>$exception->getMessage()] ,400);
        }
    }
}

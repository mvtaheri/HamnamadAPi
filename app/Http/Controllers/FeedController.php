<?php

namespace App\Http\Controllers;


use App\Model\Alert;
use App\Model\Feed;
use App\Model\Like;
use App\Model\Tag;
use App\Model\User;
use App\Model\Market;
use App\Model\People;
use App\Model\Comment;
use App\Model\Category;
use App\Model\LikeFeed;
use App\Model\UserConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\UserResource;
use App\Requests\Feed\LikeCommentRequest;
use App\Http\Resources\Feeds\FeedResource;
use App\Http\Repositories\PeopleRepository;
use App\Http\Resources\Feeds\CommentResource;
use App\Http\Resources\Market\MarketResource;
use Illuminate\Support\Facades\Log;

/**
 * Class FeedController
 * @package App\Controllers
 */
class FeedController extends Controller
{
    /**
     * @var int
     */
    private $userId;

    /**
     * FeedController constructor.
     */
    public function __construct()
    {
        $this->userId = Auth::user()->id;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRelatedFeedForCurentUser()
    {
        try {
            $user = User::where('id', $this->userId)->with(['watchlists.peoples'])->first();
            $people_ids = $user->watchlists->map(function ($watchlist) {
                return $watchlist->people_ids;
            })->collapse();
            $user_people_in_watchlist = People::whereIn('_id', $people_ids->all())->get(['user_id']);
            $userInWatchlist = $user_people_in_watchlist->map(function ($item) {
                return $item->user_id;
            });
            $this_user_people = People::forUser($this->userId)->first();
            $following_user_id = $this_user_people->following->map(function ($user) {
                return $user->user_id;
            });
            $following_user_id->push($this->userId);
            $temp = $following_user_id->merge($userInWatchlist);
            $ids = $temp->unique()->toArray();
            return response()->json(['status' => true, 'data' => $this->findFollowerFeeds($ids)], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @param $following
     * @param $userFeeds
     * @return mixed
     */
    private function findFollowerFeeds($ids)
    {
        $feeds = Feed::whereIn('user_id', $ids)->get()->sortByDesc('created_at');
        $userFeeds = $feeds->map(function ($feed) {
            $allFeed['feed'] = new FeedResource($feed);
            $allFeed['like_count'] = $feed->like()->count();
            $allFeed['is_like'] = LikeFeed::LikedByCurentUser($this->userId, $feed->id);
            $allFeed['comment'] = CommentResource::collection($feed->comments);
            $allFeed['user'] = new UserResource($feed->user);
            return $allFeed;
        });
        return $userFeeds;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addFeed(Request $request)
    {
        $this->validate($request,
            [
                'description' => 'required|string',
                'title' => 'required|string',
                'market_id' =>'sometimes|required|exists:mongodb.market,_id'
            ]
        );
        $feed = Feed::create([
                'user_id' => $this->userId,
                'description' => $request->input('description'),
            ]
        );
        $feed->tags()->create([
            'market_id' => $request->input('market_id')
        ]);
        if (!$feed) {
            return response()->json(['status' => false, 'message' => 'insert Action Falid'], 400);
        }
        if ($request->has('market_id') && Market::find($request->input('market_id'))) {
            $tag = Tag::create([
                'feed_id' => $feed->id,
                'market_id' => $request->input('market_id')
            ]);
            return response()->json(['status' => true, 'message' => 'Feed Inserted Successfully'], 200);
        } else
            return response()->json(['status' => false, 'message' => 'Market_id Not Found'], 400);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mention(Request $request)
    {
        $type = $request->input('type');
        $regex = $request->input('expression');

        switch ($type) {
            case "market":
                $markets = Market::where('title', 'LIKE', "%$regex%")->get();
                $result = MarketResource::collection($markets);
                break;
            case "user":
                $users = User::where('username', 'Like', "%$regex%")->get();
                $result = UserResource::collection($users);
                break;
        }

        return response()->json($result, 200);
    }

    /**
     * @param $markets
     * @return mixed
     */
    private function addChangePriceAndSentimentToMarket($markets)
    {
        foreach ($markets as $key => $market) {
            $markets[$key]->category = Category::getCategoryOfMarket($market->_id);
            $markets[$key]->alert = Alert::marketHasAlertbyUser($this->userId, $market->_id);
            $markets[$key]->change = ($market->sell->price - $market->sell->last_price) * 0.01;
            $markets[$key]->sentiment = Market::calculateSentimentToday($market->_id);
        }

        return $markets;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addComment(Request $request)
    {
        $this->validate($request, [
            'feed_id' => 'required|integer',
            'message' => 'required|string'
        ]);
        try {
            $comment = new Comment();
            $comment->user_id = $this->userId;
            $comment->feed_id = $request->input('feeed_id');
            $comment->description = $request->input('message');
            $comment->save();

        } catch (\PDOException $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }

        if ($comment) {
            return response()->json(['status' => true, 'message' => 'comment added successfully'], 200);
        }
        return response()->json(['status' => false, 'message' => 'error on add Comment'], 400);
    }

    /**
     * @return string
     * @throws \App\Exceptions\Job\UnexpectedJobException
     */
    public function getUserFeed()
    {
        try {
            $userFeeds = User::where('id', $this->userId)->with(['feeds', 'like', 'comments', 'comments.feed'])->first();
            return response()->json(['status' => true, 'data' => $userFeeds]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

}

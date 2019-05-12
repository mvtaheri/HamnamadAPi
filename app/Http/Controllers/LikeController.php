<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/7/2019
 * Time: 3:46 PM
 */

namespace App\Http\Controllers;


use App\Model\Like;
use App\Model\Feed;
use App\Model\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{

    protected $userId;

    public function __construct()
    {
        $this->userId = Auth::user()->id;
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function likeComment(Request $request, Comment $comment)
    {
        try {
            $this->handleLike($comment);
            return response()->json(['status' => true, 'message' => 'Liked'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }

    }

    /**
     * @param Request $request
     * @param Feed $feed
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function likeFeed(Request $request, Feed $feed)
    {
        try {
            $result = $this->handleLike($feed);
            return response()->json(['status' => $result['status'], 'data' => $result['message']], 200);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @param Like $like
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handleLike(Model $model)
    {
        try {
            $likeActivity = $model->userLike()->first();
            $user_unlike = $model->userUnlike()->first();
            if (is_null($likeActivity) && is_null($user_unlike)) {
                $like = new Like();
                Auth::user()->like()->save($like);
                $result['status'] = $model->likes()->save($like);
                $result['message'] = "liked";
            } else {
                if (!is_null($likeActivity)) {
                    $result['status'] = $likeActivity->delete();
                    $result['message'] = "unliked";
                }
                if (!is_null($user_unlike)) {
                    $result['status'] = $user_unlike->restore();
                    $result['message'] = "Liked";
                }

            }
            return $result;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw  new \Exception($exception->getMessage());
        }

    }


}

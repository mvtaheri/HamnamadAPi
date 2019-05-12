<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/11/2019
 * Time: 10:12 AM
 */

namespace App\Http\Controllers;


use App\Model\User;
use App\Model\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserWallet()
    {
        try {
            $user = Auth::user();
            $people = People::forUser(Auth::user()->id)->first();
            $userBuyOrder = User::where('id', $user->id)->with('buyOrder')->first();
            $profit = $userBuyOrder->buyOrder->map(function ($ubo) {
                $market = $ubo->market()->first();
                $currentSellPrice = $market->sell['price'];
                return $currentSellPrice - $ubo->price;
            })->sum();
            $peopleVirtualWallet = $people->virtualWallet()->first();

            $wallet = [
                'available' => $peopleVirtualWallet->available,
                'allocate' => $peopleVirtualWallet->allocated,
                'profit' => $profit,
                'equity' => $peopleVirtualWallet->available + $peopleVirtualWallet->allocated + $profit
            ];
            return response()->json(['status' => true, 'data' => $wallet], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['status' => false, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkoutRequest(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric'
        ]);
        try {
            $user = Auth::user();
            $people = People::forUser($user->id)->first();
            $realWallet = $people->realWallet()->first();
            if ($request->input('amount') > $realWallet->available)
                return response()->json(['status' => false, 'message' => 'Your wallet available is less than amount']);
            $user->checkoutRequests()->create([
                'amount' => $request->input('amount'),
                'status' => false
            ]);
            return response()->json(['status' => true, 'message' => 'your request to checkout created seccessfully']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);

        }
    }

    /**
     * @return string
     */
    public function addInventory(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric'
        ]);
        try {
            $amount = (int)$request->input('amount');
            if ($amount > 50000000)
                return response()->json(['status' => false, 'message' => 'your amount is not valid']);
            $people = People::forUser($this->userId)->first();
            $result = $people->incrementAvailableWallet('virtual', $amount);
            return response()->json(['status' => true, 'message' => 'wallet available increase successfully']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }
}

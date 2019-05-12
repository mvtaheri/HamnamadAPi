<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use App\Model\User;
use App\Model\People;
use  GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\ResetsPasswords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\ClientException;
use App\Http\Repositories\UserRepository;


class AuthController extends Controller
{
    use  ResetsPasswords;

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|exists:users,username',
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        $credintial = $request->only('username', 'password');
        if (!(new UserRepository())->attempt($credintial))
            return response()->json(['error' => 'UnAuthorized'], 401);

        return $this->proxy([
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'email' => $request->input('email')
        ]);
    }

    public function refresh(Request $request)
    {
        return $this->proxy([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->cookie('refreshToken')
        ]);
    }

    public function proxy($params)
    {
        $http = new Client();
        $client = DB::table('oauth_clients')->where('name', 'LIKE', '%Password Grant Client%')->where('revoked',0)->first();

        if ($client === null) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong 1',
            ], 401);
        }
        try {
            $response = $http->post(url('api/v1/oauth/token'), [
                'form_params' => array_merge($params, [
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'scope' => '',
                ])
            ]);
        } catch (ClientException $clientException) {
            return response()->json([
                'status' => false,
                'message' => $clientException->getMessage(),
            ], 401);
        }
        if ($response->getStatusCode() != 200) {
            return response()->json([
                'status' => false,
                'message' => $response->getBody(),
            ], $response->getStatusCode());
        }
        $data = json_decode((string)$response->getBody(), true);
        // attach a refresh token to the response via HttpOnly cookie
        return response([
            'access_token' => $data['access_token'],
            'expires_in' => $data['expires_in']
        ]);
    }

}

<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Model\People;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client as PClient;

class ExampleController extends Controller
{
    protected  $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = PClient::find(3);
    }

    //

    public function getAll(){

        return People::all();
    }

    public function getAllUsers(){
        return User::all();
    }

//    public function register(Request $request)
//    {
////        $request->user()->tokenCan('place-orders')
////        $token = $user->createToken('My Token', ['place-orders'])->accessToken;
//        $this->validate($request, [
//            'name' => 'required',
//            'username' =>'required',
//            'email' => 'required|email|unique:users,email',
//            'password' => 'required|min:6|confirmed'
//        ]);
//
//        $client_user = User::create([
//            'name' => $request->input('name'),
//            'email' => $request->input('email'),
//            'username' =>$request->input('username'),
//            'type' =>1,
//            'gender' =>'male',
//            'enabled' =>1,
//            'experience_level'=>1,
//            'password' => Hash::make($request->input('password'))
//        ]);
//        $token = $client_user->createToken('Pizza App', ['place-orders', 'list-orders'])->accessToken;
//
//        // create oauth client
//        $oauth_client = PClient::create([
//            'user_id'                => $client_user->id,
//            'id'                     => $client_user->email,
//            'name'                   => $client_user->name,
//            'secret'                 => base64_encode(hash_hmac('sha256',$client_user->password, 'secret', true)),
//            'password_client'        => 1,
//            'personal_access_client' => 0,
//            'redirect'               => '',
//            'revoked'                => 0,
//        ]);
//
//    }

//
//    public function login(Request $request)
//    {
//        $credentials = $request->only(['email', 'password']);
//        if (Auth::attempt($credentials)) {
//
//            $user = Auth::user();
//            $success['token'] = $user->createToken('MyApp')->accessToken;
//            return response()->json(['success' => $success], 200);
//        }
//        else {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
//    }
    /**
     * register API
     *
     * @return \Illuminate\Http\Response
     */
    public function registerUser(Request $request)
    {
        $input = $request->all();
        $validator = $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'username' =>'required|unique:users,username',
            'password' => 'required|confirmed'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' =>$request->username,
            'password' => Hash::make($request->password),
        ]);


        $params = [
            'grant_type' => 'password',
            'client_id' => 11,
            'client_secret' => 'fqzkmfSZXLzDNlONlpsZYhJoQ5W5BZXki7HUOBOb',
            'username' =>$user->username,
            'password' => $user->password,
            'scope' => '*'
        ];

//        $request->request->add($params);
//        $response = Request::create('/api/v1/oauth/token', 'POST');
//        return Route::dispatch($response);

        $http=new Client();

        $response = $http->post('localhost:8888/api/v1/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => 11,
                'client_secret' => 'fqzkmfSZXLzDNlONlpsZYhJoQ5W5BZXki7HUOBOb',
                'username' => $user->username,
                'password' => $user->password,
                'scope' => '[*]',
            ],
        ]);
        $result = json_decode($response->getBody()->getContents());
        $accessToken = $result->access_token;
        return response()->json(['result'=>$result ,'access_token' =>$accessToken ] ,'200');
/**
 * Test Use Router Instance
 */
//        $success['name'] = $user->name;
//        $success['token'] = $user->createToken('Password Grant Client',[])->accessToken;
//        return response()->json(['success' => $success], 200);
    }
    /**
     * admin login API
     * @return \Illuminate\Http\Response
     */
//    public function adminLogin(Request $request)
//    {
//        $input = $request->all();
//        $validator = Validator::make($input, [
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);
//        if ($validator->fails()) {
//
//            return response()->json($validator->errors(), 417);
//        }
//        $credentials = $request->only(['email', 'password']);
//        if (Auth::attempt($credentials)) {
//
//            $user = Auth::user();
//            $success['token'] = $user->createToken('MyApp', ['*'])->accessToken;
//            return response()->json(['success' => $success], 200);
//        }
//        else {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
//    }
//    /**
//     * admin register API
//     * @return \Illuminate\Http\Response
//     */
//    public function adminRegister(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email',
//            'password' => 'required',
//            'c_password' => 'required|same:password',
//        ]);
//        if ($validator->fails()) {
//
//            return response()->json($validator->errors(), 417);
//        }
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => bcrypt($request->password),
//        ]);
//        $success['name'] = $user->name;
//        $success['token'] = $user->createToken('MyApp', ['*'])->accessToken;
//        return response()->json(['success' => $success], 200);
//    }
}

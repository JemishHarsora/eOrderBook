<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Notifications\EmailVerificationNotification;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
            $user->email_verified_at = date('Y-m-d H:m:s');
        }
        else {
            $user->notify(new EmailVerificationNotification());
        }
        $user->save();

        $customer = new Customer;
        $customer->user_id = $user->id;
        $customer->save();
        return response()->json([
            'message' => 'Registration Successful. Please verify and log in to your account.'
        ], 201);
    }

    public function login(Request $request)
    {
        if($request->email =='' || $request->password == ''){
            return response()->json(['status' => false,'message' => 'Email or password is required','data' =>[]], 401);
        }

        $credentials = $request->only('email', 'password');
        $token = $this->guard()->attempt($credentials);
        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Email or password does not match','data' =>[]], 401);
        }
        $user =User::where('email', $request->email)->first();
        if(isset($user) && $user->email_verified_at == null){
            return response()->json(['status' => false,'message' => 'Please verify your account', 'data' => []], 401);
        }
        return $this->loginSuccess($token, $user);
    }

    protected function guard()
    {
        return Auth::guard('api');
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function socialLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        if (User::where('email', $request->email)->first() != null) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'provider_id' => $request->provider,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    protected function loginSuccess($token, $user)
    {

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $this->guard()->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'type' => $user->user_type,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_original' => $user->avatar_original,
                    'address' => $user->address,
                    'country'  => $user->country,
                    'city' => $user->city,
                    'postal_code' => $user->postal_code,
                    'phone' => $user->phone
                ]
            ]
        ]);
    }
}

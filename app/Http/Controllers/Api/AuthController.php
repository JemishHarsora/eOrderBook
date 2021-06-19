<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\WelcomeMail;
use App\Customer_shop;
use App\Models\Seller;
use App\Models\Shop;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    function uniqueStr($length)
    {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDERFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randomString = "";
        for ($i = 0; $i <= $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        if (User::where('referral_code', $randomString)->first() != null) {
            $this->uniqueStr(8);
        }
        return $randomString;
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'user_type' => 'required',
            'email' => "unique:users,email",
            'phone' => "unique:users,phone",
            'business_category' => 'required',
            'city' => 'required',
            'area' => 'required',
            'address' => 'required|string',
            'contact_name' => 'required|string'
        ]);

        if ($validator->fails()) {
			return response()->json(['status' => false,'message' => implode(',', $validator->messages()->all())], 200);
		}

        if ($request->is_shop == '1') {
            if (User::where('licence_no', $request->licence_no)->first() != null) {
                return response()->json(['status' => false,'message' => 'Shop Licence already exists.'], 200);
            }
        }

        $proof1 = '';
        $proof2 = '';
        $proof3 = '';
        if ($request->hasFile('proof1')) {
            $proof1 = $request->file('proof1')->store('uploads/shopVerificationData');
        }
        if ($request->hasFile('proof2')) {
            $proof2 = $request->file('proof2')->store('uploads/shopVerificationData');
        }
        if ($request->hasFile('proof3')) {
            $proof3 = $request->file('proof3')->store('uploads/shopVerificationData');
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = $request->user_type;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->business_category = $request->business_category;
        $user->contact_name = $request->contact_name;
        $user->licence_no = $request->licence_no;
        $user->gst_no = $request->gst_no;
        $user->referral_code = $this->uniqueStr(8);
        if ($request->city) {
            $user->city = $request->city;
        }
        if ($request->area) {
            $user->area = $request->area;
        }
        if ($request->address) {
            $user->address = $request->address;
        }
        $user->save();

        if ($request->user_type === "customer") {
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->verification_status = 1;
            $customer->save();

            if (Customer_shop::where('user_id', $user->id)->first() == null) {
                $shop = new Customer_shop;
                $shop->user_id = $user->id;
                $shop->name = $request->shop_name;
                if ($proof1) {
                    $shop->proof_1 = $proof1;
                }
                if ($proof2) {
                    $shop->proof_2 = $proof2;
                }
                if ($proof3) {
                    $shop->proof_3 = $proof3;
                }

                $shop->slug = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
                $shop->save();
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                } else {
                    $user->notify(new EmailVerificationNotification());
                }
                // flash(translate('Your Shop has been created successfully!'))->success();
            }
        }

        if ($request->user_type === "seller") {
            $seller = new Seller();
            $seller->user_id = $user->id;
            $seller->verification_status = 1;
            $seller->save();

            if (Shop::where('user_id', $user->id)->first() == null) {
                $shop = new Shop();
                $shop->user_id = $user->id;
                $shop->name = $request->shop_name;
                if ($proof1) {
                    $shop->proof_1 = $proof1;
                }
                if ($proof2) {
                    $shop->proof_2 = $proof2;
                }
                if ($proof3) {
                    $shop->proof_3 = $proof3;
                }
                $shop->slug = preg_replace('/\s+/', '-', $request->shop_name) . '-' . $shop->id;

                if ($shop->save()) {
                    if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                        $user->email_verified_at = date('Y-m-d H:m:s');
                        $user->save();
                    } else {
                        $user->notify(new EmailVerificationNotification());
                    }
                    // flash(translate('Your Shop has been created successfully!'))->success();
                }
            }
        }

        $referred_by_user = User::where('referral_code', $request->referred_by)->first();
        if ($referred_by_user != null) {
            $user->referred_by = $referred_by_user->id;
            $user->save();
        }

        unset($request->proof1);
        unset($request->proof2);
        unset($request->proof3);

        try {
            Mail::to($request->email)->queue(new WelcomeMail($request));
        } catch (\Exception $e) {
        }


        return response()->json([
            'status' => true,
            'message' => 'Registration Successful. Please log in to your account.'
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
			return response()->json(['status' => false,'message' => implode(',', $validator->messages()->all()),'data' =>[]], 200);
        }

        $credentials = $request->only('email', 'password');
        $token = $this->guard()->attempt($credentials);
        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Email or password does not match','data' =>[]], 200);
        }
        $user =User::where('email', $request->email)->first();
        if(isset($user) && $user->email_verified_at == null){
            return response()->json(['status' => false,'message' => 'Please verify your account', 'data' => []], 200);
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

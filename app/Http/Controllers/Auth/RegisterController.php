<?php

namespace App\Http\Controllers\Auth;

use App\Area;
use App\User;
use App\Customer;
use App\BusinessSetting;
use App\City;
use App\Customer_shop;
use App\OtpConfiguration;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use App\Mail\WelcomeMail;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Cookie;
use App\Models\Seller;
use App\Models\Shop;
use App\Notifications\EmailVerificationNotification;
use App\Upload;
use Illuminate\Support\Facades\Auth;
use Nexmo;
use Twilio\Rest\Client;
use Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required',
            'email' => "unique:users,email",
            'phone' => "unique:users,phone",
            'business_category' => 'required',
            'city' => 'required',
            'area' => 'required',
            'address' => 'required|string',
            'contact_name' => 'required|string'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

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

    protected function create(array $data, $proof1, $proof2, $proof3)
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            // dd($data);
            $user = new User;

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->user_type = $data['user_type'];
            $user->phone = $data['phone'];
            $user->password = Hash::make($data['password']);

            $user->business_category = implode(",", $data['business_category']);
            $user->contact_name = $data['contact_name'];
            $user->licence_no = $data['licence_no'];
            $user->gst_no = $data['gst_no'];
            $user->referral_code = $this->uniqueStr(8);


            if ($data['city']) {
                $user->city = $data['city'];
            }
            if ($data['area']) {
                $user->area = $data['area'];
            }
            if ($data['address']) {
                $user->address = $data['address'];
            }
            $user->save();
            if ($data['user_type'] === "customer") {
                $customer = new Customer;
                $customer->user_id = $user->id;
                $customer->verification_status = 1;
                $customer->save();

                if (Customer_shop::where('user_id', $user->id)->first() == null) {
                    $shop = new Customer_shop;
                    $shop->user_id = $user->id;
                    $shop->name = $data['shop_name'];
                    if ($proof1) {
                        $shop->proof_1 = $proof1;
                    }
                    if ($proof2) {
                        $shop->proof_2 = $proof2;
                    }
                    if ($proof3) {
                        $shop->proof_3 = $proof3;
                    }

                    $shop->slug = preg_replace('/\s+/', '-', $data['name']) . '-' . $shop->id;
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
            if ($data['user_type'] === "seller") {
                $seller = new Seller;
                $seller->user_id = $user->id;
                $seller->verification_status = 1;
                $seller->save();

                if (Shop::where('user_id', $user->id)->first() == null) {
                    $shop = new Shop;
                    $shop->user_id = $user->id;
                    $shop->name = $data['shop_name'];
                    // $shop->address = $data['shopAddress'];
                    // $shop->proof_1 = $data['proof1'];
                    // $shop->proof_2 = $data['proof2'];
                    if ($proof1) {
                        $shop->proof_1 = $proof1;
                    }
                    if ($proof2) {
                        $shop->proof_2 = $proof2;
                    }
                    if ($proof3) {
                        $shop->proof_3 = $proof3;
                    }
                    $shop->slug = preg_replace('/\s+/', '-', $data['shop_name']) . '-' . $shop->id;

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
        } else {
            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated) {
                $user = User::create([
                    'name' => $data['name'],
                    'phone' => '+' . $data['country_code'] . $data['phone'],
                    'password' => Hash::make($data['password']),
                    'verification_code' => rand(100000, 999999)
                ]);

                $customer = new Customer;
                $customer->user_id = $user->id;
                $customer->save();

                $otpController = new OTPVerificationController;
                $otpController->send_code($user);
            }
        }


        if (Cookie::has('referral_code') && $data['referred_by'] == '') {
            $referral_code = Cookie::get('referral_code');
        } else {
            $referral_code = $data['referred_by'];
        }

        $referred_by_user = User::where('referral_code', $referral_code)->first();
        if ($referred_by_user != null) {
            $user->referred_by = $referred_by_user->id;
            $user->save();
        }

        Mail::to($data['email'])->queue(new WelcomeMail($data));

        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        if ($request->user_type != "0") {
            // if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            //     if (User::where('email', $request->email)->first() != null) {
            //         flash(translate('Email or Phone already exists.'));
            //         return back();
            //     }
            // } if (User::where('phone', $request->phone)->first() != null) {
            //     flash(translate('Phone already exists.'));
            //     return back();
            // }
            // dd($request->all());
            if ($request->is_shop == '1') {
                if (User::where('licence_no', $request->licence_no)->first() != null) {
                    flash(translate('Shop Licence already exists.'));
                    return back();
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

            $user = $this->create($request->all(), $proof1, $proof2, $proof3);

            $this->guard()->login($user);

            if ($user->email != null) {
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                    flash(translate('Registration successfull.'))->success();
                } else {
                    event(new Registered($user));
                    flash(translate('Registration successfull. Please verify your email.'))->success();
                }
                $this->user_shop_verificaion_store_data($request->all(), $proof1, $proof2, $proof3);
            }

            return $this->registered($request, $user)
                ?: redirect($this->redirectPath());
        } else {
            flash(translate('Please select user type.'));
            return back();
        }
    }

    protected function user_shop_verificaion_store_data(array $data, $proof1, $proof2, $proof3)
    {
        $form = array();
        unset($data['_token'],  $data['password'], $data['password_confirmation'], $data['terms'], $data['is_shop'], $data['name'], $data['email'], $data['proof1'], $data['proof2'], $data['proof3']);
        $data['shop_image'] = $proof1;
        $data['shop_licence'] = $proof2;
        $data['gst_proof'] = $proof3;
        $categories = array();
        if ($data["business_category"]) {
            foreach ($data["business_category"] as $key => $val) {
                $cat = Category::find($val);
                array_push($categories, $cat->name);
            }
            $data["business_category"] = implode(",", $categories);
        }
        if ($data["city"] != '') {
            $city = City::find($data["city"]);
            $data["city"] = $city->name;
        }
        if ($data["area"] != '') {
            $area = Area::find($data["area"]);
            $data["area"] = $area->name;
        }
        foreach ($data as $key => $part) {

            $item['type'] = $key === 'shop_image' || $key == 'shop_licence' || $key == 'gst_proof' ? 'file' : 'text';
            $item['label'] = $key;
            $item['value'] = $part;
            array_push($form, $item);
        }

        $user = $data['user_type'] === 'seller' ? Auth::user()->seller : Auth::user()->customer;

        $user->verification_info = json_encode($form);

        $user->save();
        // flash(translate('Your shop verification request has been submitted successfully!'))->success();
        // return redirect()->route('dashboard');

    }

    protected function registered(Request $request, $user)
    {
        if ($user->email == null) {
            return redirect()->route('verification');
        } else {
            return redirect()->route('home');
        }
    }
}

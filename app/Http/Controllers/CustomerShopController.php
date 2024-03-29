<?php

namespace App\Http\Controllers;

use App\Customer_shop;
use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Notifications\EmailVerificationNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->customerShop;
        return view('frontend.user.customer.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = null;
        if (!Auth::check()) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email already exists!'))->error();
                return back();
            }
            if ($request->password == $request->password_confirmation) {
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = "customer";
                $user->password = Hash::make($request->password);
                $user->save();
            } else {
                flash(translate('Sorry! Password did not match.'))->error();
                return back();
            }
        } else {
            $user = Auth::user();
            if ($user->customer != null) {
                $user->customer->delete();
            }
            $user->user_type = "seller";
            $user->save();
        }

        $seller = new Customer;
        $seller->user_id = $user->id;
        $seller->save();

        if (Customer_shop::where('user_id', $user->id)->first() == null) {
            $shop = new Customer_shop;
            $shop->user_id = $user->id;
            $shop->name = $request->name;
            $shop->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;

            if ($shop->save()) {
                auth()->login($user, false);
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                } else {
                    $user->notify(new EmailVerificationNotification());
                }

                flash(translate('Your Shop has been created successfully!'))->success();
                return redirect()->route('shops.index');
            }
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer_shop  $customer_shop
     * @return \Illuminate\Http\Response
     */
    public function show(Customer_shop $customer_shop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\customer_shop  $customer_shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer_shop $customer_shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\customer_shop  $customer_shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $shop = Customer_shop::find($id);
        $user = Auth::user();
        if ($request->has('name') && $request->has('address')) {
            $shop->name = $request->name;
            $user->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
            $shop->meta_title = $request->meta_title;
            $shop->meta_description = $request->meta_description;
            $shop->logo = $request->logo;
        } elseif ($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')) {
            $shop->facebook = $request->facebook;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        } else {
            $shop->sliders = $request->sliders;
        }

        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\customer_shop  $customer_shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer_shop $customer_shop)
    {
        //
    }


    public function verify_form(Request $request)
    {
        if (Auth::user()->customer->verification_info == null) {
            $shop = Auth::user()->customerShop;
            return view('frontend.user.customer.verify_form', compact('shop'));
        } else {
            flash(translate('Sorry! You have sent verification request already.'))->error();
            return back();
        }
    }

    public function verify_form_store(Request $request)
    {
        $data = array();
        $i = 0;
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();
            if ($element->type == 'text') {
                $item['type'] = 'text';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_' . $i]);
            } elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $customer = Auth::user()->customer;
        $customer->verification_info = json_encode($data);
        if ($customer->save()) {
            flash(translate('Your shop verification request has been submitted successfully!'))->success();
            return redirect()->route('dashboard');
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\User;
use App\Order;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $customers = Customer::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::whereIn('user_type', ['customer', 'salesman', 'delivery'])->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $customers = $customers->where(function ($customer) use ($user_ids) {
                $customer->whereIn('user_id', $user_ids);
            });
        }
        $customers = $customers->paginate(15);
        return view('backend.customer.customers.index', compact('customers', 'sort_search'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id)->user->id;
        Order::where('user_id', Customer::findOrFail($id)->user->id)->delete();
        $blockUsers = DB::select("SELECT `block_users`.* FROM `block_users` WHERE `user_id` =" . $customer . " OR `blocker_id` =" . $customer);
        if ($blockUsers) {
            DB::table('block_users')->where('user_id', $customer)->delete();
            DB::table('block_users')->where('blocker_id', $customer)->delete();
        }
        User::destroy(Customer::findOrFail($id)->user->id);
        if (Customer::destroy($id)) {
            flash(translate('Customer has been deleted successfully'))->success();
            return redirect()->route('customers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function login($id)
    {
        $customer = Customer::findOrFail(decrypt($id));

        $user  = $customer->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->user->banned == 1) {
            $customer->user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $customer->user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $customer->user->save();

        return back();
    }

    public function updateApproved(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $customer->verification_status = $request->status;
        if ($customer->save()) {
            return 1;
        }
        return 0;
    }
    public function approve_customer($id)
    {
        $seller = Customer::findOrFail($id);
        $seller->verification_status = 1;
        if ($seller->save()) {
            flash(translate('Customer has been approved successfully'))->success();
            return redirect()->route('customers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function reject_customer($id)
    {
        $seller = Customer::findOrFail($id);
        $seller->verification_status = 0;
        $seller->verification_info = null;
        if ($seller->save()) {
            flash(translate('Customer verification request has been rejected successfully'))->success();
            return redirect()->route('customers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function show_verification_request($id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.customer.customers.verification', compact('customer'));
    }
}

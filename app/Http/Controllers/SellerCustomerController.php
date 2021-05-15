<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Seller;
use App\SellerCustomer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = null;
        if ($request->has('search')) {
            $search = $request->search;
            $customers = DB::table('seller_customers')
            ->orderBy('seller_customers.id', 'desc')
            ->leftjoin('users', 'users.id', '=', 'seller_customers.customer_id')
            ->leftjoin('customer_shops', 'seller_customers.customer_id', '=', 'customer_shops.user_id')
            ->where('seller_customers.seller_id', Auth::user()->id)
            ->select('users.*','seller_customers.status','seller_customers.id as cust_id','customer_shops.name as shop_name')
            ->where('users.name', 'like', '%' . $search . '%')
            ->orWhere('customer_shops.name', 'like', '%' . $search . '%')
            ->paginate(10);
        }
        else{
            $customers = DB::table('seller_customers')
            ->orderBy('seller_customers.id', 'desc')
            ->leftjoin('users', 'users.id', '=', 'seller_customers.customer_id')
            ->leftjoin('customer_shops', 'seller_customers.customer_id', '=', 'customer_shops.user_id')
            ->where('seller_customers.seller_id', Auth::user()->id)
            ->select('users.*','seller_customers.status','seller_customers.id as cust_id','customer_shops.name as shop_name')
            ->paginate(10);
        }

        return view('frontend.user.seller.sellerCustomers.index', compact('customers', 'search'));
    }

    public function mySellers(Request $request)
    {
        $search = null;
        if ($request->has('search')) {
            $search = $request->search;
            $customers = DB::table('seller_customers')
            ->orderBy('seller_customers.id', 'desc')
            ->join('users', 'users.id', '=', 'seller_customers.seller_id')
            ->join('customer_shops', 'seller_customers.customer_id', '=', 'customer_shops.user_id')
            ->whereIn('seller_id',function($query){
                $query->select('seller_id')->from('seller_customers')->where('seller_customers.customer_id', Auth::user()->id);
             })
            ->select('users.*','seller_customers.status','seller_customers.id as cust_id','customer_shops.name as shop_name')
            ->where('users.name', 'like', '%' . $search . '%')
            ->orWhere('customer_shops.name', 'like', '%' . $search . '%')
            ->paginate(10);
        }
        else{
            $customers = DB::table('seller_customers')
            ->orderBy('seller_customers.id', 'desc')
            ->join('users', 'users.id', '=', 'seller_customers.seller_id')
            ->join('customer_shops', 'seller_customers.customer_id', '=', 'customer_shops.user_id')
            ->whereIn('seller_id',function($query){
                $query->select('seller_id')->from('seller_customers')->where('seller_customers.customer_id', Auth::user()->id);
             })
            ->select('users.*','seller_customers.status','seller_customers.id as cust_id','customer_shops.name as shop_name')
            ->paginate(10);
        }

        return view('frontend.user.my_sellers', compact('customers', 'search'));
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
    public function changeStatus(Request $request)
    {
        $sellerCustomer = SellerCustomer::findOrFail($request->id);
        $sellerCustomer->status = $request->status;
        $sellerCustomer->save();
        return 1;
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
        //
    }
}

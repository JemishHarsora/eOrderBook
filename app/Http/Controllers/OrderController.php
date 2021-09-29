<?php

namespace App\Http\Controllers;

use App\BlockUser;
use Illuminate\Http\Request;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\Order;
use App\Product;
use App\ProductStock;
use App\Color;
use App\OrderDetail;
use App\CouponUsage;
use App\OtpConfiguration;
use App\User;
use App\BusinessSetting;
use App\City;
use App\Exports\ordersExport;
use Auth;
use Session;
use DB;
use PDF;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\ProductPrice;
use App\SellersBrand;
use CoreComponentRepository;
use Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sellerId = null;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $party_filter = null;
        $area_filter = null;
        $date_filter = null;
        // $$orders = null;
        $seller_id = "";
        if (Auth::user()->user_type === 'salesman' || Auth::user()->user_type === 'delivery') {

            $seller_id = Auth::user()->created_by;
        } else {
            $seller_id = Auth::user()->id;
        }
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', $seller_id)
            ->select('orders.id')
            ->distinct();
        if ($request->payment_status != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        if ($request->party != null) {
            $orders = $orders->where('user_id', $request->party);
            $party_filter = $request->party;
        }
        if ($request->area != null) {
            $orders = $orders->where('area_id', $request->area);
            $area_filter = $request->area;
        }

        if ($request->date_filter != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $request->date_filter)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $request->date_filter)[1])));
            $date_filter = $request->date_filter;
        }
        $orders = $orders->orderBy('orders.id', 'desc');
        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = \App\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }
        $buyers = Order::where('seller_id', $seller_id)->with(['user'])->groupBy('user_id')->get();

        $areas = Order::where('seller_id', $seller_id)->with(['area'])->groupBy('area_id')->get();
        if (Auth::user()->user_type === 'salesman' || Auth::user()->user_type === 'delivery') {
            return view('frontend.user.sellerStaff.salesMan.manageOrders.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'buyers', 'areas', 'party_filter', 'area_filter', 'date_filter'));
        }

        return view('frontend.user.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'buyers', 'areas', 'party_filter', 'area_filter', 'date_filter'));
    }

    public function bulk_status_update(Request $request)
    {
        $order_ids = json_decode($request->order_id);

        $order = Order::whereIn('id', $order_ids);
        if ($request->payment_status != '') {
            $order->update(['payment_status' => $request->payment_status]);
        }
        if ($request->delivery_status != '') {
            $order->update(['delivery_status' => $request->delivery_status]);
        }

        $order = OrderDetail::whereIn('order_id', $order_ids);
        if ($request->payment_status != '') {
            $order->update(['payment_status' => $request->payment_status]);
        }
        if ($request->delivery_status != '') {
            $order->update(['delivery_status' => $request->delivery_status]);
        }

        return response()->json(['success' => true, 'message' => 'Status update successfully.'], 200);
    }
    public function orders_export(Request $request)
    {
        $order_ids = '';
        $date = $request->date;
        $seller = $request->seller;
        $buyer = $request->buyer;
        $orders = Order::orderBy('code', 'asc');

        if ($buyer != null) {
            $orders = $orders->where('orders.user_id', $buyer);
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        if ($seller != null) {
            $orders = $orders->with(['user', 'orderDetails' => function ($query) use ($seller) {
                $query->where('order_details.seller_id', '=', $seller);
            }]);
        }
        $orders = $orders->get();

        foreach ($orders as $i => $value) {
            if (!empty($value->orderDetails['0'])) {
                $order_ids .= ',' . $value->id;
            }
        }
        $order_ids = explode(',', ltrim($order_ids, ","));

        $mainorders = Order::whereIn('id', $order_ids)->orderBy('code', 'asc')->with(['user', 'orderDetails'])->get();
        if (!empty($mainorders['0'])) {
            return Excel::download(new ordersExport($mainorders), 'orders-' . now() . '.xlsx');
        } else {
            flash(translate('No any order found'))->warning();
            return back();
        }
    }

    // All Orders
    public function all_orders(Request $request)
    {
        // CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $seller = $request->seller;
        $buyer = $request->buyer;
        $sort_search = null;
        $orders = Order::orderBy('code', 'desc');

        if ($seller != null) {
            $orders = $orders->with(['orderDetails' => function ($query) use ($seller) {
                $query->where('order_details.seller_id', '=', $seller);
            }]);
        }
        if ($buyer != null) {
            $orders = $orders->where('orders.user_id', $buyer);
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        $orders = $orders->paginate(15);

        $sellers = user::where('user_type', 'seller')->get();
        $buyers = user::where('user_type', 'customer')->get();
        return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'date', 'buyers', 'sellers', 'seller', 'buyer'));
    }

    public function all_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $products = ProductPrice::with(['product'])->where('published', 1)->get();
        return view('backend.sales.all_orders.show', compact('order', 'products'));
    }

    // Inhouse Orders
    public function admin_orders(Request $request)
    {
        // CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', $admin_user_id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.inhouse_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.inhouse_orders.show', compact('order'));
    }

    // Seller Orders
    public function seller_orders(Request $request)
    {
        // CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('users', 'order_details.seller_id', '=', 'users.id')
            ->where('order_details.seller_id', '!=', $admin_user_id)
            ->select('orders.id', 'users.name AS sellername')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.seller_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function seller_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.seller_orders.show', compact('order'));
    }


    // Pickup point orders
    public function pickup_point_order_index(Request $request)
    {
        $date = $request->date;
        $sort_search = null;

        if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
            //$orders = Order::where('pickup_point_id', Auth::user()->staff->pick_up_point->id)->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.pickup_point_id', Auth::user()->staff->pick_up_point->id)
                ->select('orders.id')
                ->distinct();

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders'));
        } else {
            //$orders = Order::where('shipping_type', 'Pick-up Point')->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.shipping_type', 'pickup_point')
                ->select('orders.id')
                ->distinct();

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        }
    }

    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        } else {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $selected_brand = '';
        $seller_id = (Auth::user()->user_type == 'seller') ? Auth::user()->id : Auth::user()->created_by;
        $areas=[];
      
        $products = ProductPrice::where('seller_id', $seller_id)->where('published', 1);
        if ($request->has('brand')) {
            $selected_brand = explode(",",$request->brand);
            $product_id = Product::whereIn('brand_id', $selected_brand)->get()->pluck(['id']);
            $products = $products->whereIn('product_id', $product_id);
            $areas = SellersBrand::with(['areas'])->where('seller_id', $seller_id)->whereIn('brand_id', $selected_brand)->groupBy('area_id')->get();
        }
        $products = $products->get();
        $brands = SellersBrand::where('seller_id', $seller_id)
            ->with(['brands'])
            ->groupBy('brand_id')
            ->get();
        return view('frontend.user.sellerStaff.salesMan.manageOrders.create_order', compact('areas', 'products', 'brands', 'selected_brand'));
    }

    public function getUsers(Request $request)
    {
        $area = explode(",",$request->area_id);
        $data['users'] = User::whereIn("area", $area)->where("user_type", "customer")->where('banned', 0)->get(["name", "id"]);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new Order;
        if (Auth::check()) {
            $order->user_id = Auth::user()->id;
        } else {
            $order->guest_id = mt_rand(100000, 999999);
        }

        $order->shipping_address = json_encode($request->session()->get('shipping_info'));

        $order->payment_type = $request->payment_option;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->date = strtotime('now');

        if ($order->save()) {
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;

            //calculate shipping is to get shipping costs of different types
            $admin_products = array();
            $seller_products = array();

            //Order Details Storing
            foreach (Session::get('cart')->where('owner_id', Session::get('owner_id')) as $key => $cartItem) {
                $product = Product::find($cartItem['id']);

                if ($product->added_by == 'admin') {
                    array_push($admin_products, $cartItem['id']);
                } else {
                    $product_ids = array();
                    if (array_key_exists($product->user_id, $seller_products)) {
                        $product_ids = $seller_products[$product->user_id];
                    }
                    array_push($product_ids, $cartItem['id']);
                    $seller_products[$product->user_id] = $product_ids;
                }

                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];

                $product_variation = $cartItem['variant'];

                if ($product_variation != null) {
                    $product_stock = $product->stocks->where('variant', $product_variation)->first();
                    if ($product->digital != 1 &&  $cartItem['quantity'] > $product_stock->qty) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }
                } else {
                    if ($product->digital != 1 && $cartItem['quantity'] > $product->current_stock) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product->current_stock -= $cartItem['quantity'];
                        $product->save();
                    }
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id  = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                $order_detail->shipping_type = $cartItem['shipping_type'];
                $order_detail->product_referral_code = $cartItem['product_referral_code'];

                //Dividing Shipping Costs
                if ($cartItem['shipping_type'] == 'home_delivery') {
                    $order_detail->shipping_cost = getShippingCost($key);
                } else {
                    $order_detail->shipping_cost = 0;
                }

                $shipping += $order_detail->shipping_cost;

                if ($cartItem['shipping_type'] == 'pickup_point') {
                    $order_detail->pickup_point_id = $cartItem['pickup_point'];
                }
                //End of storing shipping cost

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale++;
                $product->save();
            }

            $order->grand_total = $subtotal + $tax + $shipping;

            if (Session::has('coupon_discount')) {
                $order->grand_total -= Session::get('coupon_discount');
                $order->coupon_discount = Session::get('coupon_discount');

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Session::get('coupon_id');
                $coupon_usage->save();
            }

            $order->save();

            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_USERNAME');
            $array['order'] = $order;

            foreach ($seller_products as $key => $seller_product) {
                try {
                    Mail::to(\App\User::find($key)->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {
                }
            }

            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_order_code($order);
                } catch (\Exception $e) {
                }
            }

            //sends email to customer with the invoice pdf attached
            if (env('MAIL_USERNAME') != null) {
                try {
                    Mail::to(auth()->user()->email)->queue(new InvoiceEmailManager($array));
                    Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {
                }
            }

            $request->session()->put('order_id', $order->id);
        }
    }

    //direct order from cart page
    function group_assoc($array, $key)
    {
        $return = array();
        foreach ($array as $v) {
            $return[$v[$key]][] = $v;
        }
        return $return;
    }

    public function add_order(Request $request)
    {
        //Group the owner_id
        if (empty(Session::get('cart'))) {
            flash(translate("Your cart is empty"))->error();
            return back();
        }
        $user = User::find(Auth::user()->id);
        $cart_data = $this->group_assoc(Session::get('cart'), 'owner_id');
        foreach ($cart_data as $key => $cart_product) {
            foreach ($cart_product as $key => $cartItem) {
                $product = ProductPrice::with(['product'])->find($cartItem['id']);
                if($product->current_stock < $cartItem['quantity']){
                    flash(translate('your cart quentity is gretar then available item for '. $product->product->name))->warning();
                    return back();
                }
            }
        }
        
        foreach ($cart_data as $key => $cart_product) {
            $order = new Order;
            if (Auth::check()) {
                $order->user_id = $user->id;
            } else {
                $order->guest_id = mt_rand(100000, 999999);
            }
            $orders= Order::where('seller_id',$key)->orderBy('id','desc')->limit(1)->first();
            $invoice_prefix = user::where('id',$key)->first()->invoice_prefix;
            if($orders){
                $int = (int) (preg_replace('/[^0-9.]+/', '', $orders->invoice_id))+1;
                $order->invoice_id = $invoice_prefix.$int;
            }else
            {
                $order->invoice_id = $invoice_prefix.'1';
            }
            $address_data['name'] = $user->name;
            $address_data['email'] = $user->email;
            $address_data['address'] = $user->address;
            $address_data['city'] = isset($user->areas->city->name) ? $user->areas->city->name : '';
            $address_data['area'] = isset($user->areas->name) ? $user->areas->name : '';
            $address_data['phone'] = $user->phone;

            $order->shipping_address = json_encode($address_data);

            $order->payment_type = 'as_per_yours_terms';
            $order->area_id = $user->area;
            $order->seller_id = $cart_product[0]['owner_id'];
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');

            if ($order->save()) {
                $subtotal = 0;
                $tax = 0;
                $shipping = 0;

                //calculate shipping is to get shipping costs of different types
                $admin_products = array();
                $seller_products = array();

                //Order Details Storing
                foreach ($cart_product as $key => $cartItem) {
                    $product = ProductPrice::with(['product'])->find($cartItem['id']);
                    if ($product->added_by == 'admin') {
                        array_push($admin_products, $cartItem['id']);
                    } else {
                        $product_ids = array();
                        if (array_key_exists($product->seller_id, $seller_products)) {
                            $product_ids = $seller_products[$product->seller_id];
                        }
                        array_push($product_ids, $cartItem['id']);
                        $seller_products[$product->seller_id] = $product_ids;
                    }

                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];
                    $product_variation = $cartItem['variant'];

                    if ($product_variation != null) {
                        $product_stock = $product->product->stocks->where('variant', $product_variation)->first();
                        if ($product->product->digital != 1 &&  $cartItem['quantity'] > $product_stock->qty) {
                            flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                            $order->delete();
                            return redirect()->route('cart')->send();
                        } else {
                            $product_stock->qty -= $cartItem['quantity'];
                            $product_stock->save();
                        }
                    } else {
                        if ($product->product->digital != 1 && $cartItem['quantity'] > $product->current_stock) {
                            flash(translate('The requested quantity is not available for ') . $product->product->getTranslation('name'))->warning();
                            $order->delete();
                            return redirect()->route('cart')->send();
                        } else {
                            $product->current_stock -= $cartItem['quantity'];
                            $product->save();
                        }
                    }

                    $shipping_type = (isset($cartItem['shipping_type'])) ? $cartItem['shipping_type'] : 'home_delivery';


                    $order_detail = new OrderDetail;
                    $order_detail->order_id  = $order->id;
                    $order_detail->seller_id = $product->seller_id;
                    $order_detail->product_id = $product->id;
                    $order_detail->variation = $product_variation;
                    $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    $order_detail->shipping_type = $shipping_type;
                    $order_detail->product_referral_code = $cartItem['product_referral_code'];

                    //Dividing Shipping Costs
                    if ($shipping_type == 'home_delivery') {
                        $order_detail->shipping_cost = getShippingCost($key);
                    } else {
                        $order_detail->shipping_cost = 0;
                    }

                    $shipping += $order_detail->shipping_cost;

                    if ($shipping_type == 'pickup_point') {
                        $order_detail->pickup_point_id = $cartItem['pickup_point'];
                    }
                    //End of storing shipping cost

                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->save();

                    $product->num_of_sale++;
                    $product->save();
                }

                $order->grand_total = $subtotal + $tax + $shipping;

                if (Session::has('coupon_discount')) {
                    $order->grand_total -= Session::get('coupon_discount');
                    $order->coupon_discount = Session::get('coupon_discount');

                    $coupon_usage = new CouponUsage;
                    $coupon_usage->user_id = Auth::user()->id;
                    $coupon_usage->coupon_id = Session::get('coupon_id');
                    $coupon_usage->save();
                }

                $order->save();

                $array['view'] = 'emails.invoice';
                $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
                $array['from'] = env('MAIL_USERNAME');
                $array['order'] = $order;

                foreach ($seller_products as $key => $seller_product) {
                    try {
                        Mail::to(\App\User::find($key)->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {
                    }
                }

                if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                    try {
                        $otpController = new OTPVerificationController;
                        $otpController->send_order_code($order);
                    } catch (\Exception $e) {
                    }
                }

                //sends email to customer with the invoice pdf attached
                if (env('MAIL_USERNAME') != null) {
                    try {
                        Mail::to(auth()->user()->email)->queue(new InvoiceEmailManager($array));
                        Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {
                    }
                }
                $request->session()->put('order_id', $order->id);
            }
        }
        Session::forget('cart');
        flash(translate("Your order has been placed successfully"))->success();
        return redirect()->route('order_confirmed');
    }

    public function addSellerOrder(Request $request)
    {
        $seller_id = "";
        $invoice_prefix="";
        if (Auth::user()->user_type === 'salesman' || Auth::user()->user_type === 'delivery') {
            $seller_id = Auth::user()->created_by;
            $invoice_prefix = User::where('id',$seller_id)->first()->invoice_prefix;
        } else {
            $seller_id = Auth::user()->id;
            $invoice_prefix = Auth::user()->invoice_prefix;
        }

        $addIds = request('add_id');
        $add_qty = request('add_qty');
        foreach ($addIds as $key => $id) {
            $product = ProductPrice::find($id);
            if ($product->current_stock <= intval($add_qty[$key])) {
                flash(translate($product->sku . ' not more Qty available'))->error();
                return back()->withInput($request->all());
                // return response()->json(['error' => 1, 'msg' => trans('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
            }
        }

        $user = User::find($request->user_id);
        $order = new Order;
        $address_data['name'] = $user->name;
        $address_data['email'] = $user->email;
        $address_data['address'] = $user->address;
        $address_data['city'] = isset($user->areas->city->name) ? $user->areas->city->name : '';
        $address_data['area'] = isset($user->areas->name) ? $user->areas->name : '';
        $address_data['phone'] = $user->phone;

        $orders= Order::where('seller_id',$seller_id)->orderBy('id','desc')->limit(1)->first();
        if($orders){
            $int = (int) (preg_replace('/[^0-9.]+/', '', $orders->invoice_id))+1;
            $order->invoice_id = $invoice_prefix.$int;
        }else
        {
            $order->invoice_id = $invoice_prefix.'1';
        }
        $order->shipping_address = json_encode($address_data);
        $order->user_id = $user->id;
        $order->payment_type = 'as_per_yours_terms';
        $order->area_id = $user->area;
        $order->seller_id = $seller_id;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->date = strtotime('now');

        $order->save();

        $request['order_id'] = $order->id;
        $request['shipping_type'] = 'home_delivery';

        try {
            $this->add_seller_item($request);    
            flash(translate('Order has been added successfully'))->success();
        } catch (\Throwable $th) {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function add_seller_item(Request $request)
    {
        // dd($request->all());
        $addIds = request('add_id');
        $add_qty = request('add_qty');
        $add_price = request('add_price');
        $add_total = request('add_total');
        $orderId = request('order_id');
        $Order = Order::find($orderId);
        $shipping_type = isset($request->shipping_type) ? $request->shipping_type : $Order->orderDetails[0]->shipping_type;
        $shipping = 0;
        $subtotal = 0;

        foreach ($addIds as $key => $id) {
            //where exits id and qty > 0
            if ($id && $add_qty[$key]) {
                $product = ProductPrice::find($id);
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => trans('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                }
                $price = $add_total[$key];
                $subtotal += $price;
               
                $order_detail = new OrderDetail;
                $order_detail->order_id  = $orderId;
                $order_detail->seller_id = $product->seller_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = '';
                $order_detail->price = $price;
                $order_detail->tax = 0;
                $order_detail->shipping_type = $shipping_type;
                $order_detail->shipping_cost = 0;
                $order_detail->quantity = $add_qty[$key];
                $order_detail->save();
                $product->num_of_sale = $product->num_of_sale + $add_qty[$key];
                $product->current_stock = $product->current_stock - $add_qty[$key];
                $product->save();
            }
            $Order->grand_total = $Order->grand_total + $subtotal + $shipping;
            $Order->save();
        }
        $array['view'] = 'emails.invoice';
        $array['subject'] = translate('Your order has been placed') . ' - ' . $Order->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['order'] = $Order;
        try {
            Mail::to($Order->seller->email)->queue(new InvoiceEmailManager($array));
            Mail::to(\App\User::find($request->user_id)->email)->queue(new InvoiceEmailManager($array));
        } catch (\Exception $e) {
            dd('catch');
        }

        return response()->json(['error' => 0, 'msg' => translate('Order Item has been added successfully')]);
    }

    public function add_item(Request $request)
    {
        // dd($request->all());
        $addIds = request('add_id');
        $add_qty = request('add_qty');
        $orderId = request('order_id');
        $Order = Order::find($orderId);
        $shipping_type = isset($request->shipping_type) ? $request->shipping_type : $Order->orderDetails[0]->shipping_type;
        $shipping = 0;
        $subtotal = 0;

        foreach ($addIds as $key => $id) {
            //where exits id and qty > 0
            if ($id && $add_qty[$key]) {
                $product = ProductPrice::find($id);
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => trans('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                }

                $price = $product->purchase_price;

                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }

                if ($product->tax_type == 'percent') {
                    $tax = ($price * $product->tax) / 100;
                } elseif ($product->tax_type == 'amount') {
                    $tax = $product->tax;
                }

                $subtotal += $price * $add_qty[$key];
                $subtotal += $tax * $add_qty[$key];

                $order_detail = new OrderDetail;
                $order_detail->order_id  = $orderId;
                $order_detail->seller_id = $product->seller_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = '';
                $order_detail->price = $price * $add_qty[$key];
                $order_detail->tax = $tax * $add_qty[$key];
                $order_detail->shipping_type = $shipping_type;
                $order_detail->shipping_cost = 0;
                $order_detail->quantity = $add_qty[$key];
                $order_detail->save();
                $product->num_of_sale = $product->num_of_sale + $add_qty[$key];
                $product->current_stock = $product->current_stock - $add_qty[$key];
                $product->save();
            }
            $Order->grand_total = $Order->grand_total + $subtotal + $shipping;
            $Order->save();
        }
        $array['view'] = 'emails.invoice';
        $array['subject'] = translate('Your order has been placed') . ' - ' . $Order->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['order'] = $Order;
        try {
            Mail::to($Order->seller->email)->queue(new InvoiceEmailManager($array));
            Mail::to(\App\User::find($request->user_id)->email)->queue(new InvoiceEmailManager($array));
        } catch (\Exception $e) {
            dd('catch');
        }

        return response()->json(['error' => 0, 'msg' => translate('Order Item has been added successfully')]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


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
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    if ($orderDetail->variantion != null) {
                        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variantion)->first();
                        if ($product_stock != null) {
                            $product_stock->qty += $orderDetail->quantity;
                            $product_stock->save();
                        }
                    } else {
                        $product = $orderDetail->product;
                        $product->current_stock += $orderDetail->quantity;
                        $product->save();
                    }
                } catch (\Exception $e) {
                }

                $orderDetail->delete();
            }
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        if (Auth::user()->user_type === "salesman" || Auth::user()->user_type === "delivery") {
            $products = Product::where('user_id', Auth::user()->created_by)->get();
            return view('frontend.user.sellerStaff.salesMan.manageOrders.order_details_seller', compact('order', 'products'));
        } else {
            // $products = Product::where('user_id',Auth::user()->id)->get();
            // return view('frontend.user.seller.order_details_seller', compact('order','products'));
            $products = Product::where('user_id', Auth::user()->id)->get();
            $isCustomerBlockShop = [];
            $isCustomerBlockShop = BlockUser::where('user_id', Auth::user()->id)
                ->whereIn('blocker_id', array($order->user_id))->first();
            // dd($isCustomerBlockShop);
            return view('frontend.user.seller.order_details_seller', compact('order', 'products', 'isCustomerBlockShop'));
        }
    }

    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();
        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();
            }
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_delivery_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_delivery_status($order);
            } catch (\Exception $e) {
            }
        }

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                if ($order->payment_type == 'as_per_yours_terms') {
                    if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                $seller->save();
                            }
                        }
                    } else {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                $seller->save();
                            }
                        }
                    }
                } elseif ($order->manual_payment) {
                    if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                $seller->save();
                            }
                        }
                    } else {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                $seller->save();
                            }
                        }
                    }
                }
            }

            if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliatePoints($order);
            }

            if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                if ($order->user != null) {
                    $clubpointController = new ClubPointController;
                    $clubpointController->processClubPoints($order);
                }
            }

            $order->commission_calculated = 1;
            $order->save();
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_paid_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_payment_status($order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    public function getInfoProduct()
    {
        $id = request('id');
        $product = ProductPrice::with(['product'])->where('id', $id)->first();

        $product->discounted_price = substr(home_discounted_price($product->id), 2);
        $price = $product->unit_price;
        if ($product->discount_type == 'percent') {
            $price -= ($price * $product->discount) / 100;
        } elseif ($product->discount_type == 'amount') {
            $price -= $product->discount;
        }
        if ($product->tax_type == 'percent') {
            $tax = ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $tax = $product->tax;
        }
        $product->taxs = $tax;
        if (!$product) {
            return response()->json(['error' => 1, 'msg' => trans('admin.data_not_found_detail', ['msg' => '#product:' . $id]), 'detail' => '']);
        }
        $arrayReturn = $product->toArray();
        return response()->json($arrayReturn);
    }

    public function postDeleteItem($id)
    {
        $orderDetail = OrderDetail::findOrFail($id);
        if ($orderDetail != null) {

            if ($orderDetail->variantion != null) {
                $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variantion)->first();
                if ($product_stock != null) {
                    $product_stock->qty += $orderDetail->quantity;
                    $product_stock->save();
                }
            } else {
                $product = $orderDetail->product;
                $product->current_stock += $orderDetail->quantity;
                $product->save();
            }
            $orderDetail->delete();

            flash(translate('Order Item has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }
}

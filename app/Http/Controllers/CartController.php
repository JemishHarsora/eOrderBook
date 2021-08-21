<?php

namespace App\Http\Controllers;

use App\BlockUser;
use Illuminate\Http\Request;
use App\Product;
use App\ProductPrice;
use App\SubSubCategory;
use App\Category;
use Session;
use App\Color;
use Cookie;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        //dd($cart->all());
        $categories = Category::all();
        return view('frontend.view_cart', compact('categories'));
    }

    public function showCartModal(Request $request)
    {
        $isblock='';
        $sellersData=[];
        $product = ProductPrice::with(['product','user'])->find($request->id);
        $ProductSeller = ProductPrice::with(['product','user'])->where('product_id', $product->product_id)->where('seller_id','!=', $product->seller_id)->get();

        if (isset(Auth::user()->id)) {
            $isblock = BlockUser::where([['user_id', '=', Auth::user()->id], ['blocker_id', '=', $product->seller_id]])->orWhere([['blocker_id', '=', Auth::user()->id], ['user_id', '=', $product->seller_id]])->first();
            foreach($ProductSeller as $sellers){
                $sellers->isblock = BlockUser::where([['user_id', '=', Auth::user()->id], ['blocker_id', '=', $sellers->seller_id]])->orWhere([['blocker_id', '=', Auth::user()->id], ['user_id', '=', $sellers->seller_id]])->first();

                array_push($sellersData,$sellers);
            }
        }
        else{
            foreach($ProductSeller as $sellers){
                $sellers->isblock = false;
                array_push($sellersData,$sellers);
            }
        }

        return view('frontend.partials.addToCart', compact('product','isblock','sellersData'));
    }

    public function updateNavCart(Request $request)
    {
        return view('frontend.partials.cart');
    }

    public function addToCart(Request $request)
    {
        $product = ProductPrice::with(['product'])->find($request->id);
        $isblock='';
        if(isset(Auth::user()->id)){
            $isblock = BlockUser::where([['user_id', '=', Auth::user()->id],['blocker_id', '=', $product->seller_id]])->orWhere([['blocker_id', '=', Auth::user()->id],['user_id', '=', $product->seller_id]])->first();
        }

        $data = array();
        if(!$isblock){
            $data['id'] = $product->id;
            $data['owner_id'] = $product->seller_id;
            $str = '';
            $tax = 0;
            if($product->product->digital != 1 && $request->quantity < $product->min_qty) {

                return array('status' => 0, 'message' => 'You need to add minimum '.$product->min_qty.' products! ');

                // return array('status' => 0, 'view' => view('frontend.partials.minQtyNotSatisfied', [
                //     'min_qty' => $product->product->min_qty
                // ])->render());
            }


            //check the color enabled or disabled for the product
            if($request->has('color')){
                $data['color'] = $request['color'];
                $str = Color::where('code', $request['color'])->first()->name;
            }

            if ($product->product->digital != 1) {
                //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
                foreach (json_decode(Product::find($product->product_id)->choice_options) as $key => $choice) {
                    if($str != null){
                        $str .= '-'.str_replace(' ', '', $request['attribute_id_'.$choice->attribute_id]);
                    }
                    else{
                        $str .= str_replace(' ', '', $request['attribute_id_'.$choice->attribute_id]);
                    }
                }
            }

            $data['variant'] = $str;

            if($str != null && $product->product->variant_product){
                $product_stock = $product->product->stocks->where('variant', $str)->first();
                $price = $product_stock->price;
                $quantity = $product_stock->qty;

                if($quantity < $request['quantity']){
                    return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                }
            }
            else{
                $price = $product->purchase_price;
            }

            //discount calculation based on flash deal and regular discount
            //calculation of taxes
            $flash_deals = \App\FlashDeal::where('status', 1)->get();
            $inFlashDeal = false;
            foreach ($flash_deals as $flash_deal) {
                if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                    $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                    if($flash_deal_product->discount_type == 'percent'){
                        $price -= ($price*$flash_deal_product->discount)/100;
                    }
                    elseif($flash_deal_product->discount_type == 'amount'){
                        $price -= $flash_deal_product->discount;
                    }
                    $inFlashDeal = true;
                    break;
                }
            }
            if (!$inFlashDeal) {
                if($product->discount_type == 'percent'){
                    $price -= ($price*$product->discount)/100;
                }
                elseif($product->discount_type == 'amount'){
                    $price -= $product->discount;
                }
            }

            if($product->tax_type == 'percent'){
                $tax = ($price*$product->tax)/100;
            }
            elseif($product->tax_type == 'amount'){
                $tax = $product->tax;
            }

            $data['quantity'] = $request['quantity'];
            $data['price'] = $price;
            $data['tax'] = $tax;
            $data['shipping'] = 0;
            $data['product_referral_code'] = null;
            $data['digital'] = $product->digital;

            if ($request['quantity'] == null){
                $data['quantity'] = 1;
            }

            if(Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
                $data['product_referral_code'] = Cookie::get('product_referral_code');
            }
            // dd($data);
            if($request->session()->has('cart')){
                $foundInCart = false;
                $cart = collect();

                foreach ($request->session()->get('cart') as $key => $cartItem){
                    if($cartItem['id'] == $request->id){
                        if($cartItem['variant'] == $str && $str != null){
                            $product_stock = $product->product->stocks->where('variant', $str)->first();
                            $quantity = $product_stock->qty;
                            if($quantity < $cartItem['quantity'] + $request['quantity']){
                                return array('status' => 0, 'message' => 'Out of stock');
                            }
                            else{
                                $foundInCart = true;
                                $cartItem['quantity'] += $request['quantity'];
                            }
                        }
                        else
                        {
                            $foundInCart = true;
                            $cartItem['quantity'] += $request['quantity'];
                        }
                    }
                    $cart->push($cartItem);
                }

                if (!$foundInCart) {
                    $cart->push($data);
                }
                $request->session()->put('cart', $cart);
            }
            else{
                $cart = collect([$data]);
                $request->session()->put('cart', $cart);
            }
            return array('status' => 1, 'message' => 'Product added sucessfully.');
            // return array('status' => 1, 'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render());
        }
        else{
            return array('status' => 1, 'message' => 'Product add fail.');
            // return array('status' => 0, 'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render());
        }
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if($request->session()->has('cart')){
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return view('frontend.partials.cart_details');
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                $product = \App\ProductPrice::with(['product'])->find($object['id']);
                if($object['variant'] != null && $product->variant_product){
                    $product_stock = $product->product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->qty;
                    if($quantity >= $request->quantity){
                        if($request->quantity >= $product->min_qty){
                            $object['quantity'] = $request->quantity;
                        }
                    }
                }
                elseif ($product->current_stock >= $request->quantity) {
                    if($request->quantity >= $product->min_qty){
                        $object['quantity'] = $request->quantity;
                    }
                }
            }
            return $object;
        });
        $request->session()->put('cart', $cart);

        return view('frontend.partials.cart_details');
    }
}

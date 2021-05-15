<?php

namespace App\Http\Controllers;

use App\BlockUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlockUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $q2 ="SELECT `users`.`name` FROM `block_users` JOIN `users` ON `users`.`id`=`block_users`.`blocker_id` WHERE `block_users`.`status` = '0'";
        $blockUsers = DB::select("SELECT `block_users`.*,`users`.`name`,($q2) AS `blocked` FROM `block_users` JOIN `users` ON `users`.`id`=`block_users`.`user_id` WHERE `block_users`.`status` = '0'");
        return view('backend.blockUsers.index', compact('blockUsers'));
    }

    public function blockUserList(Request $request)
    {
        $q2 ="SELECT `users`.`name` FROM `block_users` JOIN `users` ON `users`.`id`=`block_users`.`blocker_id` WHERE `block_users`.`status` = '1'";
        $blockUsers = DB::select("SELECT `block_users`.*,`users`.`name`,($q2) AS `blocked` FROM `block_users` JOIN `users` ON `users`.`id`=`block_users`.`user_id` WHERE `block_users`.`status` = '1'");
        return view('backend.blockUsers.unBlock_reqList', compact('blockUsers'));
    }

    public function sellerList(Request $request)
    {
        $q2 ="SELECT `users`.`name` FROM `block_users` JOIN `users` ON `users`.`id`=`block_users`.`blocker_id` WHERE `block_users`.`status` = '0'";
        $blockUsers = DB::select("SELECT `block_users`.*,`users`.`name`,($q2) AS `blocked` FROM `block_users` JOIN `users` ON `users`.`id`=`block_users`.`user_id` WHERE `block_users`.`status` = '0'");
        return view('frontend.user.seller.staff.blockUser.index', compact('blockUsers'));
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
        try {
            $blockUser = new BlockUser();
            $blockUser->user_id =Auth::user()->id;
            $blockUser->blocker_id = $request->shop_id;
            if($blockUser->save()){
                return 1;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return 0;
        }
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
    public function update(Request $request)
    {
        $blockUser = BlockUser::findOrFail($request->unblockShopReqId);
        if($blockUser->status === '1'){
            flash(translate('You have already sent unblock request to admin.'))->warning();
            return back();
        }else{
            $blockUser->reason = $request->reason;
            $blockUser->status = '1';
            $blockUser->save();
            flash(translate('Unblock request has been successfully submited to admin.'))->success();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try {
            $blockUser = BlockUser::findOrFail($id);
            if($blockUser){
                BlockUser::destroy($id);
                flash(translate('Request has been approved successfully.'))->success();
                return back();
            }
        } catch (Exception $e) {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
}

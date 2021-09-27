<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\City;
use App\Area;
use App\Route;
use App\User;
use Carbon\Carbon;
use Auth;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = null;
        $routes = Route::orderBy('created_at', 'desc');
        $routes = $routes->where('seller_id', Auth::user()->id);
        if ($request->has('search')) {
            $search = $request->search;
            $routes = $routes->where('name', 'like', '%' . $search . '%');
        }
        $routes = $routes->paginate(15);
        return view('frontend.user.seller.route.index', compact('routes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('here');
        $cities = City::where('status', 1)->get();
        $users = User::where('created_by', Auth::user()->id)->get();
        return view('frontend.user.seller.route.create', compact('cities', 'users'));
    }

    public function getAreas(Request $request)
    {
        $data['states'] = Area::where("city_id", $request->city_id)
            ->get(["name", "id"]);
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
        foreach($request->date as $key => $value){
            $route = new Route;
            $route->name = ucfirst($request->name);
            $route->city_id = $request->city_id;
            // $route->user_id = implode(",", $request->user_id);
            $route->area_id = implode(",", $request->area_id);
            $route->day = $value;
            $route->seller_id = Auth::user()->id;
            $route->save();
        }
        flash(translate('Route has been inserted successfully'))->success();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $route  = Route::findOrFail($id);
        $areas = Area::get();
        $cities = City::where('status', 1)->get();
        $users = User::where('created_by', Auth::user()->id)->get();
        return view('frontend.user.seller.route.edit', compact('cities', 'route', 'areas', 'users'));
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
        $route = Route::findOrFail($id);
        $route->city_id = $request->city_id;
        $route->area_id = implode(",", $request->area_id);
        $route->name = ucfirst($request->name);
        $route->day = $request->date;
        // $route->user_id = implode(",", $request->user_id);
        $route->save();


        flash(translate('Route has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Route::destroy($id);
        flash(translate('Route has been deleted successfully'))->success();
        return redirect()->route('routes.index');
    }
}

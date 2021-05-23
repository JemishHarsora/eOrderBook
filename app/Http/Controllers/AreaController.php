<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\City;
use App\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $areas = Area::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $areas = $areas->where('name', 'like', '%' . $sort_search . '%');
        }
        $areas = $areas->paginate(15);
        $cities = City::where('status', 1)->get();
        return view('backend.location.area.index', compact('areas', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $area = new Area;

        $area->name = $request->name;
        $area->city_id = $request->city_id;

        $area->save();

        flash(translate('Area has been inserted successfully'))->success();

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
        $lang  = $request->lang;
        $area  = Area::findOrFail($id);
        $cities = City::where('status', 1)->get();
        return view('backend.location.area.edit', compact('area', 'lang', 'cities'));
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
        $area = Area::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $area->name = $request->name;
        }
        $area->city_id = $request->city_id;
        $area->save();


        flash(translate('Area has been updated successfully'))->success();
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
        Area::destroy($id);

        flash(translate('Area has been deleted successfully'))->success();
        return redirect()->route('areas.index');
    }
}

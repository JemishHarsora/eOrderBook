<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;
use App\Country;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $sort_search= null;
        $states = State::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $states = $states->where('name', 'like', '%'.$sort_search.'%');
        }
        $states = $states->paginate(15);
        $countries = Country::where('status', 1)->get();
        return view('backend.location.state.index', compact('states', 'countries'));
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
        $state = new State;

        $state->name = $request->name;
        $state->country_id = $request->country_id;

        $state->save();

        flash(translate('State has been inserted successfully'))->success();

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
         $state  = State::findOrFail($id);
         $countries = Country::where('status', 1)->get();
         return view('backend.location.state.edit', compact('state', 'lang', 'countries'));
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
        $state = State::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $state->name = $request->name;
        }
        $state->country_id = $request->country_id;

        $state->save();

        flash(translate('State has been updated successfully'))->success();
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
        State::destroy($id);

        flash(translate('State has been deleted successfully'))->success();
        return redirect()->route('states.index');
    }

    public function updateStatus(Request $request){
        $state = State::findOrFail($request->id);
        $state->status = $request->status;
        if($state->save()){
            return 1;
        }
        return 0;
    }
}

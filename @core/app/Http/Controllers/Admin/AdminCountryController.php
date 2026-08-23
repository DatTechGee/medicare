<?php

namespace App\Http\Controllers\Admin;


use App\Country;
use App\Helpers\FlashMsg;
use App\Testimonial;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $all_countries = Country::select('id','name','status')->get();
        return view('backend.pages.country')->with([
            'all_countries' => $all_countries,
        ]);
    }
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'status' => 'required',
        ]);
        Country::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        return redirect()->back()->with(FlashMsg::item_new('New Country Added'));
    }

    public function update(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'status' => 'string|max:191',
        ]);
        Country::find($request->id)->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with(FlashMsg::item_update('Country Updated'));
    }

    public function delete(Request $request,$id){

        Country::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_delete('Country Deleted'));
    }

    public function bulk_action(Request $request){
        $all = Country::find($request->ids);
        foreach($all as $item){
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }



}

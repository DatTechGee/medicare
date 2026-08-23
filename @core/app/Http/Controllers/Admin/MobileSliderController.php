<?php

namespace App\Http\Controllers\Admin;
use App\Cause;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\MobileSlider;
use Illuminate\Http\Request;

class MobileSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:mobile-slider-list|mobile-slider-create|mobile-slider-edit|mobile-slider-delete',['only' => ['index']]);
        $this->middleware('permission:mobile-slider-create',['only' => ['store']]);
        $this->middleware('permission:mobile-slider-edit',['only' => ['update','clone']]);
        $this->middleware('permission:mobile-slider-delete',['only' => ['delete','bulk_action']]);
    }
    public function index(){
        $all_testimonials = MobileSlider::select('id','title','image','subtitle','donation_id')->get();
        $all_causes = Cause::select('id','title')->where('status','publish')->get();
        return view('backend.pages.mobile-slider')->with([
            'all_testimonials' => $all_testimonials,
            'all_causes' => $all_causes,
        ]);
    }
    public function store(Request $request){
        $this->validate($request,[
            'title' => 'nullable|string|max:191',
            'image' => 'required|string|max:191',
            'donation_id' => 'required',
        ]);
        MobileSlider::create([
            'donation_id' => $request->donation_id,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $request->image
        ]);
        return redirect()->back()->with(FlashMsg::item_new('New Testimonial Added'));
    }

    public function update(Request $request){

        $this->validate($request,[
            'title' => 'nullable|string|max:191',
            'image' => 'required|string|max:191',
        ]);

        MobileSlider::find($request->id)->update([
            'donation_id' => $request->donation_id,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $request->image
        ]);

        return redirect()->back()->with(FlashMsg::item_update('Testimonial Updated'));
    }

    public function delete(Request $request,$id){

        MobileSlider::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_delete('Testimonial Deleted'));
    }

    public function bulk_action(Request $request){
        $all = MobileSlider::find($request->ids);
        foreach($all as $item){
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }
}

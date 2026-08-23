<?php

namespace App\Http\Controllers\Admin;

use App\CauseCategory;
use App\Gift;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CausesGiftController extends Controller
{
    private const BASE_PATH = 'backend.donations.';

    public function __construct(){
        $this->middleware('auth:admin');
        $this->middleware('permission:donation-gift-list|donation-gift-create|donation-gift-edit|donation-gift-delete',['only' => ['all_donation_category']]);
        $this->middleware('permission:donation-gift-create',['only' => ['store_donation_gift']]);
        $this->middleware('permission:donation-gift-edit',['only' => ['update_donation_gift']]);
        $this->middleware('permission:donation-gift-delete',['only' => ['delete_donation_gift','bulk_action']]);
    }

    public function all_donation_gift(){
        $all_gifts = Gift::where(['creator_type' => 'admin','creator_id' => Auth::guard('admin')->id()])->latest()->get();
        return view(self::BASE_PATH.'gifts.gift-index')->with(['all_gifts' => $all_gifts]);
    }

    public function create_donation_gift()
    {
        return view(self::BASE_PATH.'gifts.gift-create');
    }

    public function store_donation_gift(Request $request){

        $this->validate($request,[
            'title' => 'required|string',
            'amount' => 'required|alpha_num',
            'image' => 'nullable|string',
            'delivery_date' => 'required',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'gifts' => 'required|array',
            'gifts.*' => 'required|string|distinct'
        ],
            [
                'gifts.*.required' => 'Gift item is required'
            ]

        );

        Gift::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'image' => $request->image,
            'delivery_date' => $request->delivery_date,
            'description' => $request->description,
            'status' => $request->status,
            'gifts' => json_encode($request->gifts) ?? [],
            'creator_id' => Auth::guard('admin')->id(),
            'creator_type' => 'admin',
        ]);

        return redirect()->back()->with(['msg' => __('New gift item added'),'type' => 'success']);
    }

    public function edit_donation_gift($id)
    {
        $gift = Gift::findOrFail($id);
        return view(self::BASE_PATH.'gifts.gift-edit',compact('gift'));
    }

    public function update_donation_gift(Request $request){


        $this->validate($request,[
            'title' => 'required|string',
            'amount' => 'required|alpha_num',
            'image' => 'nullable|string',
            'delivery_date' => 'required',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'gifts' => 'required|array',
            'gifts.*' => 'required|string|distinct'
        ],
            [
                'gifts.*.required' => 'Gift item is required'
            ]

        );

        Gift::findOrFail($request->gift_id)->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'image' => $request->image,
            'delivery_date' => $request->delivery_date,
            'description' => $request->description,
            'status' => $request->status,
            'gifts' => json_encode($request->gifts) ?? [],
            'creator_id' => Auth::guard('admin')->id(),
            'creator_type' => 'admin',
        ]);

        return redirect()->back()->with(FlashMsg::item_update());
    }

    public function delete_donation_gift(Request $request,$id){
        Gift::findOrFail($id)->delete();
        return redirect()->back()->with(['msg' => __('item deleted'),'type' => 'danger']);
    }

    public function bulk_action(Request $request){
        $all = Gift::findOrFail($request->ids);
        foreach($all as $item){
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }

}

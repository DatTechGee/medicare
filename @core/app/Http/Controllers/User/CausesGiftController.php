<?php

namespace App\Http\Controllers\User;

use App\CauseCategory;
use App\Gift;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CausesGiftController extends Controller
{
    public const BASE_PATH = 'frontend.user.dashboard.campaigns.cause-gifts.';

    public function __construct(){
        $this->middleware('auth');
    }

    private function auth_id()
    {
        return Auth::guard('web')->id();
    }

    public function all_donation_gift(){
        $all_gifts = Gift::where(['creator_id' => $this->auth_id(), 'creator_type' => 'user'])->latest()->get();
        return view(self::BASE_PATH.'all-gifts')->with(['all_gifts' => $all_gifts]);
    }

    public function create_donation_gift()
    {
        return view(self::BASE_PATH.'new-gift');
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
            'creator_id' => Auth::guard('web')->id(),
            'creator_type' => 'user',
        ]);

        return redirect()->back()->with(['msg' => __('New gift item added'),'type' => 'success']);
    }

    public function edit_donation_gift($id)
    {
        $gift = Gift::findOrFail($id);
        return view(self::BASE_PATH.'edit-gift',compact('gift'));
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
            'creator_id' => Auth::guard('web')->id(),
            'creator_type' => 'user',
        ]);

        return redirect()->back()->with(FlashMsg::item_update());
    }

    public function delete_donation_gift(Request $request,$id){
        Gift::findOrFail($id)->delete();
        return redirect()->back()->with(['msg' => __('item deleted'),'type' => 'danger']);
    }


}

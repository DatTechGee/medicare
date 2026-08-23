<?php

namespace App\Http\Controllers\Api;

use App\Admin;
use App\Cause;
use App\CauseCategory;
use App\CauseLogs;
use App\CauseUpdate;
use App\Comment;
use App\Events;
use App\EventsCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Language;
use App\MobileSlider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HomeController extends Controller
{

    public function all_slider()
    {
        
             $all_mobile_sliders = MobileSlider::select('id','title','image','subtitle','donation_id')->get()->transform(function($item) {

                $image_url = null;
                if (!empty($item->image)) {
                    $img_details = get_attachment_image_by_id($item->image);
                    $item->image = $img_details['img_url'] ?? null;
                    $item->campaign_id = $item->donation_id;
                    //get donation deadline by donation id
                    $item->deadline = Cause::select('deadline')->find($item->donation_id)?->deadline;
                    
                    unset($item->donation_id);
                }
                return $item;
            });
            
            return response()->json(['data' => $all_mobile_sliders]);
    }

    public function multiple_donation_data()
    {
        $all_donation_category = CauseCategory::select('id','title','image')->where(['status' => 'publish'])->paginate(10)->withQueryString();
        $filter_donation_category = $all_donation_category->through(function($item){
            $image_url = null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });

        $donation_list = Cause::select('id','title')->where('status','publish')->whereDate("deadline", '>', \Carbon\Carbon::now())->get();

        $feature_cause = Cause::select('id','title','raised','amount','image','deadline')->where(['status' => 'publish','featured' => 'on'])->whereDate("deadline", '>', \Carbon\Carbon::now())->paginate(20)->withQueryString();
        $filter_donation_feature = $feature_cause->through(function($item){
            $image_url = null;
            $item->reamaining_time = $item->deadline ?? null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });


        $all_recent_donation = Cause::select('id','title','amount','raised','image','deadline')->where(['status' => 'publish'])->whereDate("deadline", '>', \Carbon\Carbon::now())->orderBY('id','desc')->paginate(20)->withQueryString();
            $filter_donation_recent = $all_recent_donation->through(function($item){
                $image_url = null;
                $item->reamaining_time = $item->deadline ?? null;
                if(!empty($item->image)){
                    $img_details = get_attachment_image_by_id($item->image);
                    $item->image = $img_details['img_url'] ?? null;
                }
                return $item;
            });

        // check get method type
        if(request()->type == "category"){

            $pagination = [
                "current_page" => $all_donation_category->currentPage(),
                "last_page" => $all_donation_category->lastPage(),
                "per_page" => $all_donation_category->perPage(),
                "path" => $all_donation_category->path(),
                "links" => $all_donation_category->getUrlRange(0,$all_donation_category->lastPage()),
                "data" => $filter_donation_category
            ];
            return response()->json([
                'donation_category' => $pagination
            ]);
        }elseif(request()->type == "list"){
            return response()->json([
                'donation_list' => $donation_list
            ]);
        }elseif(request()->type == "feature"){

         $pagination = [
             "current_page" => $feature_cause->currentPage(),
             "last_page" => $feature_cause->lastPage(),
             "per_page" => $feature_cause->perPage(),
             "path" => $feature_cause->path(),
             "links" => $feature_cause->getUrlRange(0,$feature_cause->lastPage()),
             "data" => $filter_donation_feature
         ];
            return response()->json([
                'donation_feature' => $pagination
            ]);

        }elseif(request()->type == "recent"){

            $pagination = [
                "current_page" => $feature_cause->currentPage(),
                "last_page" => $feature_cause->lastPage(),
                "per_page" => $feature_cause->perPage(),
                "path" => $feature_cause->path(),
                "links" => $feature_cause->getUrlRange(0,$feature_cause->lastPage()),
                "data" => $filter_donation_recent
            ];

            return response()->json([
                'donation_recent' => $pagination
            ]);
        }

    }


    public function donation_details($id){

        if(empty($id)){
            abort(404);
        }

        $details = Cause::with(['admin','user'])->findOrFail($id);

        $user = '';
        if($details->created_by == 'user'){
            $user = $details->user;
        }else{
            $user = $details->admin;
        }

        $image = get_attachment_image_by_id($details->image) ?? null;
        $donation_image_url = $image['img_url'] ?? null;
        $user_image = get_attachment_image_by_id($user->image) ?? null;
        $user_image_url = $user_image['img_url'] ?? null;
        $faq_items = !empty($details->faq) ? unserialize($details->faq,['class' => false]) : ['title' => []];

        $cause_updates = CauseUpdate::select('id','image','cause_id','description','title')->where('cause_id', $details->id)->get()->transform(function($item){
            $image_url = null;
            if(!empty($item->image)){
                $image = get_attachment_image_by_id($item->image);
                $item->image = $image['img_url'] ?? null;
            }
            return $item;
        });

        $causeComments = Comment::select('id','cause_id','user_id','commented_by','comment_content')->where('cause_id', $details->id)->paginate(10)->withQueryString();

        return response()->json([
            'title' => $details->title,
            'description' => purify_html($details->cause_content),
            'image' => $donation_image_url,
            'created_by' => $details->created_by,
            'user_image' => $user_image_url,
            'campaign_owner_id' => $user->id,
            'user_name' => $user->name,
            'created_at' => $details->created_at->diffForHumans(),
            'category' => optional($details->category)->title,
            'remaining_time' => !empty($details->deadline) ? $details->deadline : null,
            'raised_amount' => $details->raised,
            'goal_amount' => $details->amount,
            'faqs' => $faq_items,
            'updates' => $cause_updates,
            'comments' => $causeComments,
        ]);
    }

    public function people_who_donated($cause_id)
    {
        if(empty($cause_id)){
            abort(404);
        }

        $all_donors = CauseLogs::select('id','name','amount','created_at','anonymous')->where(['cause_id' => $cause_id,'status' => 'complete'])->paginate(10)->withQueryString()->through(function($item){
			$item->name = $item-> anonymous === 1 ? __('anonymous') : $item->name ;
			return $item;
		});

        return response()->json([
            'people_who_donated' => $all_donors,
        ]);

    }

    public function related_campaigns($cause_id)
    {
        if(empty($cause_id)){
            abort(404);
        }

        $details = Cause::findOrFail($cause_id);

        $all_related_cause = Cause::select('id','title','image','amount','raised')->where(['status' => 'publish' , 'categories_id' => $details->categories_id])->whereDate("deadline", '>', \Carbon\Carbon::now())->paginate(10)->withQueryString();
        $filter_related_cause = $all_related_cause->transform(function($item){
            $image_url = null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });

        $related_items_pagination = [
            "current_page" => $all_related_cause->currentPage(),
            "last_page" => $all_related_cause->lastPage(),
            "per_page" => $all_related_cause->perPage(),
            "path" => $all_related_cause->path(),
            "links" => $all_related_cause->getUrlRange(0,$all_related_cause->lastPage()),
            "data" => $filter_related_cause
        ];

        return response()->json([
            'related_campaigns' => $related_items_pagination,
        ]);

    }

    public function currencies()
    {
        return response()->json([
            'currency'=> [
                "site_default_currency_symbol" => site_currency_symbol(),
                "position" => get_static_option('site_currency_symbol_position'),
                "idr_exchange_rate" => getenv('IDR_EXCHANGE_RATE'),
                "inr_exchange_rate" => getenv('INR_EXCHANGE_RATE'),
                "ngn_exchange_rate" => getenv('NGN_EXCHANGE_RATE'),
                "zar_exchange_rate" => getenv('ZAR_EXCHANGE_RATE'),
                "brl_exchange_rate" => getenv('BRL_EXCHANGE_RATE'),
                "myr_exchange_rate" => getenv('MYR_EXCHANGE_RATE'),
            ],
        ]);
    }


    public function language_rtl_support()
    {
        $user_lang_slug = get_user_lang();
        $defult_language = Language::where('slug',$user_lang_slug)->pluck('name');

        return response()->json([
            'language'=> [
                "default_language_slug" => $user_lang_slug,
                "default_language_name" => $defult_language,
                "defualt_language_direction" => get_user_lang_direction(),
            ],
        ]);

    }

    public function custom_amounts()
    {
        $custom_amounts_once  =  get_static_option('donation_custom_amount_once');
        $custom_amounts_once = !empty($custom_amounts_once) ? explode(',',$custom_amounts_once) : [50,100,150,200];

        return response()->json([
          "custom_amounts" => $custom_amounts_once,
        ]);
    }


    public function event_list()
    {
        $all_events = Events::select('id','title','image','date','time','cost','venue_location')->where(['status' => 'publish'])->paginate(10)->withQueryString();
        $filter_event = $all_events->transform(function($item){
            $image_url = null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });

        $event_pagination = [
            "current_page" => $all_events->currentPage(),
            "last_page" => $all_events->lastPage(),
            "per_page" => $all_events->perPage(),
            "path" => $all_events->path(),
            "links" => $all_events->getUrlRange(0,$all_events->lastPage()),
            "data" => $filter_event
        ];

        return response()->json([
            'event_list' => $event_pagination,
        ]);

    }

    public function event_single($id)
    {
        $event = Events::with('category:id,title')->findOrFail($id);
        if (empty($event)) {
            return redirect_404_page();
        }

        $event->content = purify_html($event->content);
        $image = get_attachment_image_by_id($event->image) ?? null;
        $image_url = $image['img_url'] ?? null;

        return response()->json([
            'event' => $event,
            'image' => $image_url
        ]);
    }


    public function campaign_categories()
    {
        $campaign_categories =  CauseCategory::select('id','title','image')->where(['status' => 'publish'])->paginate(6);

        $filter_category = $campaign_categories->transform(function($item){
            $image_url = null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });

        $campain_category_pagination = [
            "current_page" => $campaign_categories->currentPage(),
            "last_page" => $campaign_categories->lastPage(),
            "per_page" => $campaign_categories->perPage(),
            "path" => $campaign_categories->path(),
            "links" => $campaign_categories->getUrlRange(0,$campaign_categories->lastPage()),
            "data" => $filter_category
        ];

        return response()->json([
            'campaign_categories' => $campain_category_pagination,
        ]);

    }


    public function campaign_by_category($id)
    {
        $all_donations = Cause::select('id','title','amount','raised','image','deadline')->where(['categories_id' =>$id, 'status' => 'publish'])->paginate(10)->withQueryString();
        $filter_donation_recent = $all_donations->transform(function($item){
            $image_url = null;
            $item->reamaining_time = $item->deadline ?? null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });

        $campain_category_pagination = [
            "current_page" => $all_donations->currentPage(),
            "last_page" => $all_donations->lastPage(),
            "per_page" => $all_donations->perPage(),
            "path" => $all_donations->path(),
            "links" => $all_donations->getUrlRange(0,$all_donations->lastPage()),
            "data" => $filter_donation_recent
        ];

        return response()->json(['data' => $campain_category_pagination]);
    }

    //social login
    public function social_login(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
        ]);
        if ($validate->fails()){
            return response()->error([
                'validation_errors' => $validate->messages()
            ]);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->error([
                'message' => __('invalid Email'),
            ]);
        }
        $username = $request->isGoogle === 0 ?  'fb_'.Str::slug($request->displayName) : 'gl_'.Str::slug($request->displayName);
        $user = User::select('id', 'email', 'username')
            ->where('email', $request->email)
            ->Orwhere('username', $username)
            ->first();

        if (is_null($user)) {
            $user = User::create([
                'name' => $request->displayName,
                'email' => $request->email,
                'username' => $username,
                'password' => Hash::make(Str::random(8)),
                'google_id' => $request->isGoogle == 1 ? $request->id : null,
                'facebook_id' => $request->isGoogle == 0 ? $request->id : null
            ]);
        }

        $token = $user->createToken(Str::slug(get_static_option('site_title', 'fundorex')) . 'api_keys')->plainTextToken;
        return response()->json([
            'users' => $user,
            'token' => $token,
        ]);
    }


    public function donation_admin_settings()
    {
        $donation_charge_from = get_static_option('donation_charge_form');
        $allow_user_to_add_custom_tip_in_donation = get_static_option('allow_user_to_add_custom_tip_in_donation');
        $donation_default_amount = get_static_option('donation_default_amount');
        $minimum_donation_amount = get_static_option('minimum_donation_amount');
        $donation_charge_active_deactive_button = get_static_option('donation_charge_active_deactive_button');
        $charge_amount_type = get_static_option('charge_amount_type'); //fixed or percentage
        $charge_amount = get_static_option('charge_amount');

        return response()->json([
            'donation_charge_from' => $donation_charge_from,
            'allow_user_to_add_custom_tip_in_donation' => $allow_user_to_add_custom_tip_in_donation,
            'minimum_donation_amount' => $minimum_donation_amount,
            'donation_charge_active_deactive_button' => $donation_charge_active_deactive_button,
            'charge_amount_type' => $charge_amount_type,
            'charge_amount' => $charge_amount,
            'donation_default_amount' => $donation_default_amount,
        ]);

    }



}

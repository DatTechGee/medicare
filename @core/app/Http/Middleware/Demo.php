<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $not_allow_path = [
          'admin-home',
          'user-home'
        ];
        $allow_path = [
            'admin-home/chart',
            'admin-home/chart/day'
            ];
        $contains = Str::contains($request->path(), $not_allow_path);
        if($request->isMethod('POST') || $request->isMethod('PUT')) {
            
            if($contains && !in_array($request->path(),$allow_path)){
                if ($request->ajax()){
                    return response()->json(['type' => 'warning' , 'msg' => 'This is demonstration purpose only, you may not able to change few settings, after purchase the script you will get acceass to all settings']);
                }
                return redirect()->back()->with(['type' => 'warning' , 'msg' => 'This is demonstration purpose only, you may not able to change few settings, after purchase the script you will get acceass to all settings']);
            }
            
        }
        
        return $next($request);
    }
}

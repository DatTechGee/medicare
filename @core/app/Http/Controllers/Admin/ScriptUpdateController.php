<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScriptUpdateController extends Controller
{
    public function index()
    {
        return view('backend.general-settings.update-script');
    }

    public function update_script(Request $request)
    {
        return redirect()->back()->with(['msg' => __('You are already running the latest version of the script.'), 'type' => 'success']);
    }
}

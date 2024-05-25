<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanaguageController extends Controller
{

    public function changeLang(Request $request)
    {
        try {
            $lang = $request->get("lang");
            if (in_array($lang, config("app.languages"))) {
                App::setLocale($lang);
                session()->put('locale', $request->lang);
            }
            return back();
        } catch (\Throwable $th) {
            return back()->with("error", "something went wrong.please try again.");

        }


    }
}
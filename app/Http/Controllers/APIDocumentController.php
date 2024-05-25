<?php

namespace App\Http\Controllers;

use Auth;
use URL;
use Input;
use File;
use View;
use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class APIDocumentController extends Controller
{
    public function index(Request $request)
    {
        return view('front.apidoc.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Store;
use Validator;
use Illuminate\Support\Facades\Auth;
use URL;
use Session;
use Exception;
use Redirect;
use Input;
use View;
use Mail;
use DB;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moduleTitleS = 'Store';
        $this->moduleTitleP = 'front.store';

        $this->store = new Store;

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ?  Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->store->getAllStores($input, $noList);
        return view($this->moduleTitleP . '.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->moduleTitleP . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        // banner dimension 700*500
        $this->validate(
            $request,
            [
                'name' => 'required|max:50|regex:/^[a-z\d\-_\s\.]+$/i',
                'currency' => 'required',
                'description' => 'required',
                // 'banner_image_1' => 'required|mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                // 'banner_image_2' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                // 'banner_image_3' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                'banner_text_1' => 'required',
                'template_id' => 'required',
                // 'about_banner_image' => 'required|mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                'about_us' => 'required',
                'contact_us_email' => 'required',
                // 'contact_banner_image' => 'required|mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                'contact_us_description' => 'required',
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'name.required' => 'This field is required.',
                'currency.required' => 'This field is required.',
                'description.required' => 'This field is required.',
                'banner_image_1.required' => 'This field is required.',
                'banner_image_1.dimensions' => 'The Banner Image 1 should be at least 700px by 500px.',
                'banner_image_1.max' => 'The Banner Image 1 should be less than 4 MB.',
                'banner_image_1.mimes' => 'The Banner Image 1 should be image only (jpg,jpeg,png)',
                'banner_image_2.dimensions' => 'The Banner Image 2 should be at least 700px by 500px.',
                'banner_image_2.max' => 'The Banner Image 2 should be less than 4 MB.',
                'banner_image_2.mimes' => 'The Banner Image 2 should be image only (jpg,jpeg,png)',
                'banner_image_3.dimensions' => 'The Banner Image 3 should be at least 700px by 500px.',
                'banner_image_3.max' => 'The Banner Image 3 should be less than 4 MB.',
                'banner_image_3.mimes' => 'The Banner Image 3 should be image only (jpg,jpeg,png)',
                'template_id.required' => 'This field is required.',
                'about_banner_image.required' => 'This field is required.',
                'about_us.required' => 'This field is required.',
                'contact_us_email.required' => 'This field is required.',
                'contact_banner_image.required' => 'This field is required.',
                'contact_us_description.required' => 'This field is required.',
                'mimes' => 'Please insert image only (jpg,jpeg,png)',
                'max'   => 'Image should be less than 4 MB.',
                'dimensions'   => 'The Image should be at least 700px by 500px.',
            ]
        );
        
        $user = auth()->user();
        $input['user_id'] = $user->id;
        $input['slug'] = \Str::slug($input['name']).'-'.time();
        // if ($request->hasFile('banner_image_1')) {
        //     $imagebanner_image_1 = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagebanner_image_1 = $imagebanner_image_1 . '.' . $request->file('banner_image_1')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagebanner_image_1;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('banner_image_1')->getRealPath()));
        //     $input['banner_image_1'] = $filePath;
        // }
        // if ($request->hasFile('banner_image_2')) {
        //     $imagebanner_image_2 = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagebanner_image_2 = $imagebanner_image_2 . '.' . $request->file('banner_image_2')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagebanner_image_2;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('banner_image_2')->getRealPath()));
        //     $input['banner_image_2'] = $filePath;
        // }
        // if ($request->hasFile('banner_image_3')) {
        //     $imagebanner_image_3 = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagebanner_image_3 = $imagebanner_image_3 . '.' . $request->file('banner_image_3')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagebanner_image_3;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('banner_image_3')->getRealPath()));
        //     $input['banner_image_3'] = $filePath;
        // }
        // if ($request->hasFile('about_banner_image')) {
        //     $imageabout_banner_image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imageabout_banner_image = $imageabout_banner_image . '.' . $request->file('about_banner_image')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imageabout_banner_image;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('about_banner_image')->getRealPath()));
        //     $input['about_banner_image'] = $filePath;
        // }
        // if ($request->hasFile('contact_banner_image')) {
        //     $imagecontact_banner_image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagecontact_banner_image = $imagecontact_banner_image . '.' . $request->file('contact_banner_image')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagecontact_banner_image;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('contact_banner_image')->getRealPath()));
        //     $input['contact_banner_image'] = $filePath;
        // }
        
        DB::beginTransaction();
        try {
            $this->store->storeData($input);
            DB::commit();
            notificationMsg('success', "Store Created Successfully.");
            return redirect()->route('store.index');
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            notificationMsg('error', 'Store not created. Try Again.');
            return redirect()->back()->withInput($request->all());
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $id = idDecode($id);
        $data = $this->store->findData($id);
    
        if($data->id != $id){
            return redirect()->back();
        }
        return view($this->moduleTitleP.'.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        // banner dimension 700*500
        $this->validate(
            $request,
            [
                'name' => 'required|max:50|regex:/^[a-z\d\-_\s\.]+$/i',
                'currency' => 'required',
                'description' => 'required',
                // 'banner_image_1' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                // 'banner_image_2' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                // 'banner_image_3' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                'banner_text_1' => 'required',
                'template_id' => 'required',
                // 'about_banner_image' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                'about_us' => 'required',
                'contact_us_email' => 'required',
                // 'contact_banner_image' => 'mimes:jpg,jpeg,png|max:4096|dimensions:min_width=700,min_height=500',
                'contact_us_description' => 'required',
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters..',
                'name.required' => 'This field is required.',
                'currency.required' => 'This field is required.',
                'description.required' => 'This field is required.',
                'banner_image_1.required' => 'This field is required.',
                'banner_image_1.dimensions' => 'The Banner Image 1 should be at least 700px by 500px.',
                'banner_image_1.max' => 'The Banner Image 1 should be less than 4 MB.',
                'banner_image_1.mimes' => 'The Banner Image 1 should be image only (jpg,jpeg,png)',
                'banner_image_2.dimensions' => 'The Banner Image 2 should be at least 700px by 500px.',
                'banner_image_2.max' => 'The Banner Image 2 should be less than 4 MB.',
                'banner_image_2.mimes' => 'The Banner Image 2 should be image only (jpg,jpeg,png)',
                'banner_image_3.dimensions' => 'The Banner Image 3 should be at least 700px by 500px.',
                'banner_image_3.max' => 'The Banner Image 3 should be less than 4 MB.',
                'banner_image_3.mimes' => 'The Banner Image 3 should be image only (jpg,jpeg,png)',
                'template_id.required' => 'This field is required.',
                'about_banner_image.required' => 'This field is required.',
                'about_us.required' => 'This field is required.',
                'contact_us_email.required' => 'This field is required.',
                'contact_banner_image.required' => 'This field is required.',
                'contact_us_description.required' => 'This field is required.',
                'mimes' => 'Please insert image only (jpg,jpeg,png)',
                'max'   => 'Image should be less than 4 MB.',
                'dimensions'   => 'The Image should be at least 700px by 500px.',
            ]
        );

        $user = auth()->user();
        $input['user_id'] = $user->id;
        // if ($request->hasFile('banner_image_1')) {
        //     $imagebanner_image_1 = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagebanner_image_1 = $imagebanner_image_1 . '.' . $request->file('banner_image_1')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagebanner_image_1;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('banner_image_1')->getRealPath()));
        //     $input['banner_image_1'] = $filePath;
        // }
        // if ($request->hasFile('banner_image_2')) {
        //     $imagebanner_image_2 = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagebanner_image_2 = $imagebanner_image_2 . '.' . $request->file('banner_image_2')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagebanner_image_2;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('banner_image_2')->getRealPath()));
        //     $input['banner_image_2'] = $filePath;
        // }
        // if ($request->hasFile('banner_image_3')) {
        //     $imagebanner_image_3 = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagebanner_image_3 = $imagebanner_image_3 . '.' . $request->file('banner_image_3')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagebanner_image_3;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('banner_image_3')->getRealPath()));
        //     $input['banner_image_3'] = $filePath;
        // }
        // if ($request->hasFile('about_banner_image')) {
        //     $imageabout_banner_image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imageabout_banner_image = $imageabout_banner_image . '.' . $request->file('about_banner_image')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imageabout_banner_image;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('about_banner_image')->getRealPath()));
        //     $input['about_banner_image'] = $filePath;
        // }
        // if ($request->hasFile('contact_banner_image')) {
        //     $imagecontact_banner_image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
        //     $imagecontact_banner_image = $imagecontact_banner_image . '.' . $request->file('contact_banner_image')->getClientOriginalExtension();
        //     $filePath = 'uploads/store-front-' . $user->id . '/' . $imagecontact_banner_image;
        //     Storage::disk('s3')->put($filePath, file_get_contents($request->file('contact_banner_image')->getRealPath()));
        //     $input['contact_banner_image'] = $filePath;
        // }
        
        DB::beginTransaction();
        try {
            $this->store->updateData($id, $input);
            DB::commit();
            notificationMsg('success', "Store Updated Successfully.");
            return redirect()->route('store.index');
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            notificationMsg('error', 'Store not updated. Try Again.');
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = idDecode($id);
        $this->store->destroyData($id);
        notificationMsg('success', 'Store Deleted Successfully!');
        return redirect()->route('store.index');
    }
}

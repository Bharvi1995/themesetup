<?php

namespace App\Http\Controllers\Admin;

use App\AWSTest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AwsTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = AWSTest::orderBy("id", "desc")->paginate(10);
        return view("admin.awsTest.index", compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "title" => 'required|min:2|max:150',
            "file" => 'required|file|mimes:png,jpg,svg,jpeg|max:2048'
        ]);
        try {
            $file = $request->file('file');
            $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageName = $imageName . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/testFiles/' . $imageName;
            Storage::disk('s3')->put($filePath, file_get_contents($file->getRealPath()));
            $payload['file'] = $filePath;

            AWSTest::create($payload);
            return back()->with('success', 'File uploaded successfully!');
        } catch (\Exception $err) {
            return back()->with('error', $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $date = AWSTest::find($id);
            $success = Storage::disk('s3')->delete($date->file);

            if ($success) {
                $date->delete();
                return redirect()->route('aws-s3-test.index')->with("success", "File Deleted successfully!");
            } else {
                return redirect()->route('aws-s3-test.index')->with("error", "Something went wrong!");
            }
        } catch (\Exception $err) {
            return redirect()->route('aws-s3-test.index')->with("error", $err->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class FilesUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    public function downloadFilesUploaded(Request $request)
    {
        return Storage::disk('s3')->download('uploads/tickets/'.$request->file, $request->file);
        // $path = storage_path('uploads/tickets/'.$request->file);
        // if (!File::exists($path)) {
        //     echo "file not available";
        // }
        //return response()->download($path);
    }

    public function viewFilesUploaded(Request $request)
    {
        
        $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();
        $command = $client->getCommand('GetObject', [
            'Bucket' => 'cryptomatixx-data',
            'Key' => $request->file  // file name in s3 bucket which you want to access
        ]);
        $request = $client->createPresignedRequest($command, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        return response($request->getUri())->header('Content-Type', 'image/jpg');
    }
}

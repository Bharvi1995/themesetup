<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use File;

class CommonController extends Controller
{
    public function readLogs(Request $request, $id = "")
    {
        if($request->filename){
            
            if($request->filename == "today"){
                $Current_Date = date("Y-m-d");
                $file_name = storage_path()."/logs/tmp/Custom_Log_".$Current_Date.".log";
            } else {
                $file_name = storage_path()."/logs/tmp/".$request->filename.".log";
            }
            
        } else {
            $file_name = storage_path()."/logs/laravel.log";
        }
        $fileData = file_get_contents($file_name);
        echo '<pre>';
        print_r($fileData);
        exit;
    }

    public function checkS3Bucket()
    {
        
        $filePath = 'uploads/aws_s3_test_file1.txt';
        $existFileUrl = getS3Url($filePath);
        
        // Check if file exists
        $check_File_Exist = @fopen($existFileUrl, 'r');
        
        if(!$check_File_Exist){

            $data = "AWS S3 BUCKET CHECK";
            $file = 'aws_s3_test_file.txt';
            $destinationPath = storage_path()."/samples/";
            if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
            File::put($destinationPath.$file,$data);
            
            $uploadPath = 'uploads/aws_s3_test_file1.txt';
            Storage::disk('s3')->put($uploadPath, file_get_contents($destinationPath.$file));

            $exist_file = getS3Url($uploadPath);
            $check_File_Exist = @fopen($exist_file, 'r');
            if(!$check_File_Exist){
                echo 'Aws S3 bucket is not working';
            } else {
                echo 'Aws S3 bucket is working';
            }
        }else{
            echo 'Aws S3 bucket is working';
        }
    }
}

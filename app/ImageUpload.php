<?php 
namespace App;

use DB;
use File;
use Zip;

class ImageUpload
{
    // Upload in local public folder
    public static function upload($path, $image, $pathName='test')
    {
        $imageName = time().rand(100000, 100000).pathinfo(rand(1000000,1000000), PATHINFO_FILENAME);
        $imageName = $imageName.'.'.$image->getClientOriginalExtension();
        $image->move(public_path($path),$imageName);

        return $imageName;
    }
    // Upload in Live
    public static function uploadLive($path, $image)
    {
        $imageName = time().rand(0, 1000000).'.'.$image->getClientOriginalName();
        $imageName = $imageName.'.'.$image->getClientOriginalExtension();
        
        //local
        $live_path = public_path();
        $live_path = substr($live_path, 0,-7);
        $image->move($live_path.$path,$imageName);
        
        // for live
        // $live_path = app_path();
        // $live_path = substr($live_path, 0,-4);
        // $image->move($live_path.'/'.$path,$imageName);

        return $imageName;
    }
    // Create Directory In local public folder
    public static function createDirectory($path)
    {
        $directoryPath = public_path($path);

        if(!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, $mode = 0777, true, true);
        }

        return true;
    }

    // CopyDeractory for local
    public static function copyDirectory($path, $destination)
    {
        \File::copyDirectory(public_path().$path, public_path().$destination);

        return true;
    }
    // CopyDeractory for Live
    public static function copyDirectoryLive($path, $destination)
    {
        \File::copyDirectory($path, $destination);

        return true;
    }

    public static function createDirectoryLive($path)
    {
        $directoryPath = $path;

        if(!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, $mode = 0777, true, true);
        }

        return true;
    }

    public static function removeFile($path)
    {
        if(File::exists(public_path($path))){
            File::delete(public_path($path));
        }
    }
    public static function massUpload($path, $image, $pathName='test')
    {
        $extenstion = $image->getClientOriginalExtension();
        if ($extenstion=='zip') {
            $zip = Zip::open($image);
            $files = $zip->listFiles();
            $zip->extract(public_path($path));
        
            return $files;
        }

        $imageName = $pathName.'.'.$image->getClientOriginalExtension();
        $image->move(public_path($path),$imageName);

        return array($imageName);
    }
    
}

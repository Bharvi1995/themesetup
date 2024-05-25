<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WPPluginController extends Controller
{
    public function index()
    {
        return view('front.wpPlugin.index');
    }

    public function download()
    {
        $file = storage_path(). '/app/public/plugin/woocommerce-monetize-payment-gateway.zip';

        if (file_exists($file)) {
            $headers = [
                'Content-Type' => 'application/zip',
            ];

            return response()->download($file, 'woocommerce-monetize-payment-gateway.zip', $headers);
        } else {
            return response()->json(['error' => 'File not found.']);
        }
    }
}

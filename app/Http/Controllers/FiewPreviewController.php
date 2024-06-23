<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FiewPreviewController extends Controller
{
    public function preview($path){
        $filePath = base64_decode($path);
        $storagePath = storage_path('app\\'.$filePath);
        return response()->file($storagePath);
    }
}

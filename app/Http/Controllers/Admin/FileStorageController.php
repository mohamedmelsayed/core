<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileStorageController extends Controller {
    public function ftp() {
        $pageTitle = "FTP Setting";
        return view('admin.storage.ftp', compact('pageTitle'));
    }

    public function aws() {
        $pageTitle = "Amazon AWS CDN Setting";
        return view('admin.storage.aws_cdn', compact('pageTitle'));
    }
    public function updateAwsCdn(Request $request) {
        $validatedData = $request->validate([
            'aws_cdn' => 'required|array',
            'aws_cdn.domain' => 'nullable|string',
            'aws_cdn.access_key' => 'nullable|string',
            'aws_cdn.secret_key' => 'nullable|string',
        ]);
    
        // Convert the array to JSON
        $awsCdnJson = json_encode($validatedData['aws_cdn']);
    
        // Update the AWS CDN configuration in the database
        $setting      = gs();
        $setting->aws_cdn = $request->aws_cdn;
        $setting->save();
    
       
        $notify[] = ['success', 'AWS CDN credentials Updated'];
        return back()->withNotify($notify);
    }

    public function ftpUpdate(Request $request) {
        $request->validate([
            'ftp.host'     => 'required',
            'ftp.username' => 'required',
            'ftp.password' => 'required',
            'ftp.port'     => 'required',
            'ftp.root'     => 'required',
            'ftp.domain'   => 'required',
        ]);
        $setting      = gs();
        $setting->ftp = $request->ftp;
        $setting->save();

        $notify[] = ['success', 'FTP credentials Updated'];
        return back()->withNotify($notify);
    }
    public function wasabi() {
        $pageTitle = "Wasabi Setting";
        return view('admin.storage.wasabi', compact('pageTitle'));
    }

    public function wasabiUpdate(Request $request) {
        $request->validate([
            'wasabi.driver'   => 'required',
            'wasabi.key'      => 'required',
            'wasabi.secret'   => 'required',
            'wasabi.region'   => 'required',
            'wasabi.bucket'   => 'required',
            'wasabi.endpoint' => 'required',
        ]);
        $setting         = gs();
        $setting->wasabi = $request->wasabi;
        $setting->save();

        $notify[] = ['success', 'Wasabi credentials updated'];
        return back()->withNotify($notify);
    }
    public function digitalOcean() {
        $pageTitle = "Digital Ocean Setting";
        $setting   = gs();
        return view('admin.storage.digital_ocean', compact('pageTitle', 'setting'));
    }

    public function digitalOceanUpdate(Request $request) {
        $request->validate([
            'digital_ocean.driver'   => 'required',
            'digital_ocean.key'      => 'required',
            'digital_ocean.secret'   => 'required',
            'digital_ocean.region'   => 'required',
            'digital_ocean.bucket'   => 'required',
            'digital_ocean.endpoint' => 'required',
        ]);
        $setting                = gs();
        $setting->digital_ocean = $request->digital_ocean;
        $setting->save();

        $notify[] = ['success', 'Digital ocean credentials updated'];
        return back()->withNotify($notify);
    }
}

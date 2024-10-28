<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller {
    public function index() {
        $pageTitle       = 'General Setting';
        $timezones       = json_decode(file_get_contents(resource_path('views/admin/partials/timezone.json')));
        $currentTimezone = array_search(config('app.timezone'), $timezones);
        return view('admin.setting.general', compact('pageTitle', 'timezones', 'currentTimezone'));
    }

    public function update(Request $request) {
        $request->validate([
            'site_name'       => 'required|string|max:40',
            'cur_text'        => 'required|string|max:40',
            'cur_sym'         => 'required|string|max:40',
            'base_color'      => 'nullable|regex:/^[a-f0-9]{6}$/i',
            'secondary_color' => 'nullable|regex:/^[a-f0-9]{6}$/i',
            'timezone'        => 'required|integer',
            'tmdb_api'        => 'nullable|string|max:255',
            'skip_time'       => 'required|integer|max:60',
            'file_server'     => 'required|in:current,custom-ftp,wasabi,digital_ocean,aws',
        ]);
dd($request->file_server);
        $timezones = json_decode(file_get_contents(resource_path('views/admin/partials/timezone.json')));
        $timezone  = @$timezones[$request->timezone] ?? 'UTC';

        $general                  = gs();
        $general->site_name       = $request->site_name;
        $general->cur_text        = $request->cur_text;
        $general->cur_sym         = $request->cur_sym;
        $general->base_color      = str_replace('#', '', $request->base_color);
        $general->secondary_color = str_replace('#', '', $request->secondary_color);
        $general->server          = $request->file_server;
        $general->tmdb_api        = $request->tmdb_api;
        $general->skip_time       = $request->skip_time;

        $pusherConfiguration = [
            'app_id'         => $request->app_id,
            'app_key'        => $request->app_key,
            'app_secret_key' => $request->app_secret_key,
            'cluster'        => $request->cluster,
        ];
        $general->pusher_config = $pusherConfiguration;
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content      = '<?php $timezone = "' . $timezone . '" ?>';
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function systemConfiguration() {
        $pageTitle = 'System Configuration';
        return view('admin.setting.configuration', compact('pageTitle'));
    }

    public function systemConfigurationSubmit(Request $request) {
        $general                    = gs();
        $general->kv                = $request->kv ? Status::ENABLE : Status::DISABLE;
        $general->ev                = $request->ev ? Status::ENABLE : Status::DISABLE;
        $general->en                = $request->en ? Status::ENABLE : Status::DISABLE;
        $general->sv                = $request->sv ? Status::ENABLE : Status::DISABLE;
        $general->sn                = $request->sn ? Status::ENABLE : Status::DISABLE;
        $general->force_ssl         = $request->force_ssl ? Status::ENABLE : Status::DISABLE;
        $general->secure_password   = $request->secure_password ? Status::ENABLE : Status::DISABLE;
        $general->registration      = $request->registration ? Status::ENABLE : Status::DISABLE;
        $general->agree             = $request->agree ? Status::ENABLE : Status::DISABLE;
        $general->pn                = $request->pn ? Status::ENABLE : Status::DISABLE;
        $general->multi_language    = $request->multi_language ? Status::ENABLE : Status::DISABLE;
        $general->ad_show_mobile    = $request->ad_show_mobile ? Status::ENABLE : Status::DISABLE;
        $general->device_limit      = $request->device_limit ? Status::ENABLE : Status::DISABLE;
        $general->watch_party       = $request->watch_party ? Status::ENABLE : Status::DISABLE;
        $general->watch_party_users = $request->watch_party_users ? Status::ENABLE : Status::DISABLE;
        $general->app_purchase      = $request->app_purchase ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $notify[] = ['success', 'System configuration updated successfully'];
        return back()->withNotify($notify);
    }

    public function logoIcon() {
        $pageTitle = 'Logo & Favicon';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request) {
        $request->validate([
            'logo'    => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'favicon' => ['image', new FileTypeValidate(['png'])],
        ]);
        if ($request->hasFile('logo')) {
            try {
                fileUploader($request->logo, $path, filename: 'logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                fileUploader($request->favicon, $path, filename: 'favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Logo & favicon updated successfully'];
        return back()->withNotify($notify);
    }

    public function customCss() {
        $pageTitle   = 'Custom CSS';
        $file        = activeTemplate(true) . 'css/custom.css';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.custom_css', compact('pageTitle', 'fileContent'));
    }

    public function customCssSubmit(Request $request) {
        $file = activeTemplate(true) . 'css/custom.css';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->css);
        $notify[] = ['success', 'CSS updated successfully'];
        return back()->withNotify($notify);
    }

    public function maintenanceMode() {
        $pageTitle   = 'Maintenance Mode';
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        return view('admin.setting.maintenance', compact('pageTitle', 'maintenance'));
    }

    public function maintenanceModeSubmit(Request $request) {
        $request->validate([
            'description' => 'required',
        ]);
        $general                   = gs();
        $general->maintenance_mode = $request->status ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $maintenance              = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        $maintenance->data_values = [
            'description' => $request->description,
        ];
        $maintenance->save();

        $notify[] = ['success', 'Maintenance mode updated successfully'];
        return back()->withNotify($notify);
    }

    public function cookie() {
        $pageTitle = 'GDPR Cookie';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        return view('admin.setting.cookie', compact('pageTitle', 'cookie'));
    }

    public function cookieSubmit(Request $request) {
        $request->validate([
            'short_desc'  => 'required|string|max:255',
            'description' => 'required',
        ]);
        $cookie              = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $cookie->data_values = [
            'short_desc'  => $request->short_desc,
            'description' => $request->description,
            'status'      => $request->status ? Status::ENABLE : Status::DISABLE,
        ];
        $cookie->save();
        $notify[] = ['success', 'Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }

    public function socialiteCredentials() {
        $pageTitle = 'Social Login Credentials';
        return view('admin.setting.socialite', compact('pageTitle'));
    }

    public function updateSocialiteCredential(Request $request, $key) {
        $general     = gs();
        $credentials = $general->socialite_credentials;
        try {
            @$credentials->$key->client_id     = $request->client_id;
            @$credentials->$key->client_secret = $request->client_secret;
        } catch (\Throwable $th) {
            abort(404);
        }
        $general->socialite_credentials = $credentials;
        $general->save();

        $notify[] = ['success', ucfirst($key) . ' credential updated successfully'];
        return back()->withNotify($notify);
    }

    public function socialiteCredentialStatus($key) {
        $general     = gs();
        $credentials = $general->socialite_credentials;
        try {
            $credentials->$key->status = $credentials->$key->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        } catch (\Throwable $th) {
            abort(404);
        }

        $general->socialite_credentials = $credentials;
        $general->save();

        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }

    public function appPurchaseCredentials() {
        $pageTitle = 'App Purchase Credentials';
        return view('admin.setting.app_purchase', compact('pageTitle'));
    }

    public function appPurchaseConfigure($type) {
        $pageTitle = ucfirst($type) . ' Pay configuration';
        $data      = null;
        if (file_exists(getFilePath('appPurchase') . '/' . gs('app_purchase_credentials')->$type->file)) {
            $data = file_get_contents(getFilePath('appPurchase') . '/' . gs('app_purchase_credentials')->$type->file);
        }
        return view('admin.setting.app_purchase_configure', compact('pageTitle', 'type', 'data'));
    }

    public function updateAppPurchaseCredentials(Request $request, $type) {
        $request->validate([
            'file' => ['required', new FileTypeValidate(['json'])],
        ], [
            'file' => 'json format file is required',
        ]);

        $general = gs();

        $googleFile = $general->app_purchase_credentials->google->file;
        $appleFile  = $general->app_purchase_credentials->apple->file;

        if ($request->hasFile('file')) {
            try {
                $old  = @$general->app_purchase_credentials->$type->file;
                $file = fileUploader($request->file, getFilePath('appPurchase'), null, @$old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your file'];
                return back()->withNotify($notify);
            }
        }

        $data = [
            'google' => [
                'file' => $type == 'google' ? $file : $googleFile,
            ],
            'apple'  => [
                'file' => $type == 'apple' ? $file : $appleFile,
            ],
        ];

        $general->app_purchase_credentials = $data;
        $general->save();

        $notify[] = ['success', 'File updated successfully'];
        return back()->withNotify($notify);
    }

}

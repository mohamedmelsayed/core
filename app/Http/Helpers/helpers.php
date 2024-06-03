<?php

use App\Constants\Status;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Advertise;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Notify\Notify;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

function systemDetails() {
    $system['name']          = 'playlab';
    $system['version']       = '2.8';
    $system['build_version'] = '4.4.11';
    return $system;
}

function slug($string) {
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length) {
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8) {
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function activeTemplate($asset = false) {
    $general  = gs();
    $template = session('template') ?? $general->active_template;
    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $general  = gs();
    $template = session('template') ?? $general->active_template;
    return $template;
}

function siteLogo($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}
function siteFavicon() {
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha() {
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003') {
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha() {
    return Captcha::verify();
}

function loadExtension($key) {
    $analytics = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12) {
    $characters       = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2) {
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false) {
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}

function removeElement($array, $value) {
    return array_diff($array, (is_array($value) ? $value : [$value]));
}

function cryptoQR($wallet) {
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

function keyToTitle($text) {
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text) {
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10) {
    return Str::limit($title, $length);
}

function getIpInfo() {
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser() {
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getTemplates() {
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website']      = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url                   = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response              = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}

function getPageSections($arr = false) {
    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null, $isAvatar = null) {
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($isAvatar) {
        return asset('assets/images/avatar.png');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $clickValue = null) {
    $general          = gs();
    $globalShortCodes = [
        'site_name'       => $general->site_name,
        'site_currency'   => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify               = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes   = $shortCodes;
    $notify->user         = $user;
    $notify->createLog    = $createLog;
    $notify->userColumn   = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->clickValue   = $clickValue;
    $notify->send();
}

function getPaginate($paginate = 20) {
    return $paginate;
}

function paginateLinks($data) {
    return $data->appends(request()->all())->links();
}

function menuActive($routeName, $type = null, $param = null) {
    if ($type == 3) {
        $class = 'side-menu--open';
    } else if ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }

        }
    } else if (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) {
                return $class;
            } else {
                return;
            }

        }
        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null) {
    $fileManager           = new FileManager($file);
    $fileManager->path     = $location;
    $fileManager->size     = $size;
    $fileManager->old      = $old;
    $fileManager->thumb    = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager() {
    return new FileManager();
}

function getFilePath($key) {
    return fileManager()->$key()->path;
}

function getFileSize($key) {
    return fileManager()->$key()->size;
}

function getFileExt($key) {
    return fileManager()->$key()->extensions;
}

function diffForHumans($date) {
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A') {
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {
    $template = activeTemplate();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $template)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $template);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function gatewayRedirectUrl($type = false) {
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit.index';
    }
}

function urlPath($routeName, $routeParam = null) {
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path     = str_replace($basePath, '', $url);
    return $path;
}

function showMobileNumber($number) {
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email) {
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP() {
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value) {
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b) {
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr) {
    usort($arr, "dateSort");
    return $arr;
}

function showAd() {

    $ad = Advertise::where('device', 1)->where('ads_show', 2)->inRandomOrder()->first();
    if (!$ad) {
        return false;
    }
    $ad->increment('impression');
    if ($ad) {
        if ($ad->ads_type == 'banner') {
            $final = '<a href="' . $ad->content->link . '" class="ad advertise" data-id="' . $ad->id . '" target="_blank"><img src="' . getImage(getFilePath('ads') . '/' . $ad->content->image) . '" ></a>';
        } else {
            $final = '<div class="advertise" data-id="' . $ad->id . '">' . $ad->content->script . '</div>';
        }
    } else {
        $final = '';
    }

    return $final;
}

function getVideoFile($video, $quality="seven_twenty") {
    if (!$video) {
        return false;
    }
    $serverName   = 'server_' . $quality;
    $videoQuality = $quality . '_video';

    $content = $video->$videoQuality;
    $server  = $video->$serverName;
    if (!$content) {
        return false;
    }
    if($server==Status::LINK){
        return $content;
    }
   // dd( 'assets/videos/' . $content);
    if ($server == Status::CURRENT_SERVER) {
        $videoFile = asset('assets/videos/' . $content);
    } else if ($server == Status::FTP_SERVER) {
        $general   = gs();
        $videoFile = $general->ftp->domain . '/' . $content;
    } else if ($server == Status::WASABI_SERVER) {
        $general = gs();
        $s3      = new S3Client([
            'endpoint'    => $general->wasabi->endpoint,
            'region'      => $general->wasabi->region,
            'version'     => 'latest',
            'credentials' => [
                'key'    => $general->wasabi->key,
                'secret' => $general->wasabi->secret,
            ],
        ]);

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $general->wasabi->bucket,
            'Key'    => $content,
            'ACL'    => 'public-read',
        ]);

        $request   = $s3->createPresignedRequest($cmd, '+20 minutes');
        $videoFile = (string) $request->getUri();
    } else if ($server == Status::DIGITAL_OCEAN_SERVER) {
        $general = gs();
        $s3      = new S3Client([
            'endpoint'    => $general->digital_ocean->endpoint,
            'region'      => $general->digital_ocean->region,
            'version'     => 'latest',
            'credentials' => [
                'key'    => $general->digital_ocean->key,
                'secret' => $general->digital_ocean->secret,
            ],
        ]);

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $general->digital_ocean->bucket,
            'Key'    => $content,
            'ACL'    => 'public-read',
        ]);

        $request   = $s3->createPresignedRequest($cmd, '+20 minutes');
        $videoFile = (string) $request->getUri();
    } else {
        $videoFile = $content;
    }
    return $videoFile;
}

function getAudioFile($audio) {
    if (!$audio) {
        return false;
    }
    $serverName   = 1;
    $AudioQuality = '$content';

    $content = $audio->$AudioQuality;
    $server  = $audio->$serverName;
    if (!$content) {
        return false;
    }
    if($server==Status::LINK){
        return $content;
    }
    // dd( 'assets/videos/' . $content);
    if ($server == Status::CURRENT_SERVER) {
        $videoFile = asset('assets/audios/' . $content);
    } else if ($server == Status::FTP_SERVER) {
        $general   = gs();
        $videoFile = $general->ftp->domain . '/' . $content;
    } else if ($server == Status::WASABI_SERVER) {
        $general = gs();
        $s3      = new S3Client([
            'endpoint'    => $general->wasabi->endpoint,
            'region'      => $general->wasabi->region,
            'version'     => 'latest',
            'credentials' => [
                'key'    => $general->wasabi->key,
                'secret' => $general->wasabi->secret,
            ],
        ]);

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $general->wasabi->bucket,
            'Key'    => $content,
            'ACL'    => 'public-read',
        ]);

        $request   = $s3->createPresignedRequest($cmd, '+20 minutes');
        $videoFile = (string) $request->getUri();
    } else if ($server == Status::DIGITAL_OCEAN_SERVER) {
        $general = gs();
        $s3      = new S3Client([
            'endpoint'    => $general->digital_ocean->endpoint,
            'region'      => $general->digital_ocean->region,
            'version'     => 'latest',
            'credentials' => [
                'key'    => $general->digital_ocean->key,
                'secret' => $general->digital_ocean->secret,
            ],
        ]);

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $general->digital_ocean->bucket,
            'Key'    => $content,
            'ACL'    => 'public-read',
        ]);

        $request   = $s3->createPresignedRequest($cmd, '+20 minutes');
        $videoFile = (string) $request->getUri();
    } else {
        $videoFile = $content;
    }
    return $videoFile;
}

function numFormat($num) {
    if ($num > 1000) {

        $x               = round($num);
        $x_number_format = number_format($x);
        $x_array         = explode(',', $x_number_format);
        $x_parts         = ['k', 'm', 'b', 't'];
        $x_count_parts   = count($x_array) - 1;
        $x_display       = $x;
        $x_display       = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;

    }

    return $num;
}

function short_string($string, $length = null) {
    if ($length == null) {
        $length = 20;
    }
    return strLimit($string, $length);
}

function gs($key = null) {
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) {
        return @$general->$key;
    }

    return $general;
}
function isImage($string) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string) {
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

function checkLockStatus($episode, $userHasSubscribed, $hasSubscribedItem) {
    if ($userHasSubscribed) {
        if ($episode->version == Status::FREE_VERSION) {
            $status = true;
        } else if ($episode->version == Status::PAID_VERSION) {
            $status = $userHasSubscribed ? true : false;
        } else {
            if (@$episode->item->exclude_plan) {
                $status = $hasSubscribedItem ? true : false;
            } else {
                $status = $userHasSubscribed || $hasSubscribedItem ? true : false;
            }
        }
    } else {
        $status = $episode->version == Status::FREE_VERSION ? true : false;
    }
    return $status;
}

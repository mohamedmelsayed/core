<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory,GlobalStatus;
    public function getAds() {
        $adsTime = [];
        $user    = auth()->user();
        if ($user && $user->plan && !$user->plan->show_ads) {
            return $adsTime;
        }

        if ($this->seconds) {
            $duration = $this->seconds;
            $videoAds = VideoAdvertise::get();
            for ($i = 0; $i < count($duration); $i++) {
                $videoAd = $videoAds->shuffle()->first();
                if ($videoAd) {
                    $general = gs();

                    if (@$videoAd->server == Status::FTP_SERVER) {
                        $video = $general->ftp->domain . '/' . @$videoAd->content->video;
                    } else if (@$videoAd->server == Status::WASABI_SERVER) {
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
                            'Key'    => @$videoAd->content->video,
                            'ACL'    => 'public-read',
                        ]);
                        $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                        $video   = (string) $request->getUri();

                    } else if (@$videoAd->server == Status::DIGITAL_OCEAN_SERVER) {
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
                            'Key'    => @$videoAd->content->video,
                            'ACL'    => 'public-read',
                        ]);

                        $request = $s3->createPresignedRequest($cmd, '+20 minutes');
                        $video   = (string) $request->getUri();
                    } else {
                        $video = getImage(getFilePath('ads') . '/' . @$videoAd->content->video);
                    }
                    $adsTime[$duration[$i]] = @$videoAd->content->link ? @$videoAd->content->link : $video;
                }
            }
        }

        return $adsTime;
    }
}

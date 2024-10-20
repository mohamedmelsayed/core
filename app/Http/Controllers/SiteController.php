<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Advertise;
use App\Models\Category;
use App\Models\ContentTranslation;
use App\Models\DeviceToken;
use App\Models\Episode;
use App\Models\Frontend;
use App\Models\History;
use App\Models\Item;
use App\Models\Language;
use App\Models\LiveTelevision;
use App\Models\Plan;
use App\Models\Playlist;
use App\Models\Slider;
use App\Models\SubCategory;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\VideoReport;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index()
    {
        $currentLang = session()->get('lang', 'ar');
        $pageTitle = 'Home';
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();
        $sliders = Slider::orderBy('id', 'desc')->where('status', 1)->with('item', 'item.category', 'item.video')->get();
        $featuredMovies = Item::active()->hasVideo()->where('featured', 1)->orderBy('id', 'desc')->get();
        $advertise = Advertise::where('device', 1)->where('ads_show', 1)->where('ads_type', 'banner')->inRandomOrder()->first();
        return view($this->activeTemplate . 'home', compact('pageTitle', 'allPlaylists', 'sliders', 'featuredMovies', 'advertise', 'currentLang'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();
        $user = auth()->user();
        return view($this->activeTemplate . 'contact', compact('pageTitle', 'user', 'allPlaylists'));
    }

    public function contactSubmit(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function cookieAccept()
    {
        $general = gs();
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie', 'allPlaylists'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX = ($imgWidth - $textWidth) / 2;
        $textY = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance', 'allPlaylists'));
    }

    public function getSection(Request $request)
    {
        $data = [];
        if ($request->sectionName == 'end') {
            return response('end');
        }
        $items = Item::hasVideoOrAudio();
        if ($request->sectionName == 'recent_added') {
            $data['recent_added'] = (clone $items)->where('item_type', Status::SINGLE_ITEM)->orderBy('id', 'desc')->limit(18)->get();
        } else if ($request->sectionName == 'latest_series') {
            $data['playlists'] = Playlist::with('items')->limit(18)->get();
        } else if ($request->sectionName == 'latest_series1') {
            $data['latestSerieses'] = (clone $items)->orderBy('id', 'desc')->where('item_type', Status::EPISODE_ITEM)->limit(12)->get();
        } else if ($request->sectionName == 'single') {
            $data['single'] = (clone $items)->orderBy('id', 'desc')->where('single', 1)->with('category')->first();
        } else if ($request->sectionName == 'latest_trailer') {
            $data['latest_trailers'] = (clone $items)->where('item_type', Status::SINGLE_ITEM)->where('is_trailer', 1)->orderBy('id', 'desc')->limit(12)->get();
        } else if ($request->sectionName == 'free_zone') {
            $data['frees'] = (clone $items)->free()->orderBy('id', 'desc')->limit(12)->get();
        } else if ($request->sectionName == 'top') {
            $data['mostViewsTrailer'] = (clone $items)->where('item_type', Status::SINGLE_ITEM)->where('is_trailer', 1)->orderBy('view', 'desc')->first();
            $data['topRateds'] = (clone $items)->orderBy('ratings', 'desc')->limit(4)->get();
            $data['trendings'] = (clone $items)->orderBy('view', 'desc')->where('trending', 1)->limit(4)->get();
        } else if ($request->sectionName == 'single1' || $request->sectionName == 'single2' || $request->sectionName == 'single3') {
            $data['single'] = (clone $items)->orderBy('id', 'desc')->where('single', Status::YES)->with('category')->get();
        }
        return view($this->activeTemplate . 'sections.' . $request->sectionName, $data);
    }



    // Method to view playlist and play items
    public function play(Playlist $playlist)
    {
        $pageTitle = 'PlayList Details';
        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();
        // Retrieve all items in the playlist that have video or audio
        $playlistItems = $playlist->type == 'video'
            ? $playlist->items()->whereHas('video')->with('video', 'translations')->get()
            : $playlist->items()->whereHas('audio')->with('audio', 'translations')->get();

        // Get the first item to play by default
        $item = $playlist->type == 'video'
            ? $playlist->items()->whereHas('video')->with('video')->first()
            : $playlist->items()->whereHas('audio')->with('audio')->first();

        if (!$item) {
            // Handle the case where no video or audio is found
            return back()->withNotify(['error', 'No playable item found in this playlist.']);
        }

        if (!$item->hasVideoOrAudio()) {
            // Handle the case where no video or audio is found
            return back()->withNotify(['error', 'No playable item found in this playlist.']);
        }
        $this->storeHistory($item->id);
        $this->storeVideoReport($item->id);
        $seoContents = $this->getItemSeoContent($item);
        $checkWatchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        $watchEligable = $checkWatchEligable[0];
        $videos = $playlist->type == 'video' ? $this->videoList($item->video ?? null) : [];
        $audios = $playlist->type == 'audio' ? $this->audioList($item->audio ?? null) : [];

        $adsTime = [];
        $subtitles = [];
        if ($playlist->type == 'video') {
            $adsTime = $item->video->getAds() ?? [];
            $subtitles = $item->video->subtitles;
        }
        // dd($item);

        return view($this->activeTemplate . 'playlists.play', compact(
            'pageTitle',
            'playlist',
            'allPlaylists',
            'item',
            'playlistItems',
            'seoContents',
            'watchEligable',
            'videos',
            'audios',
            'adsTime',
            'subtitles'
        ));
    }

    // Method to play a specific item from a playlist
    public function playItem(Playlist $playlist, $itemSlug)
    {
        $pageTitle = 'PlayList Details';
        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        // Find the item by slug based on whether the playlist is video or audio type
        $item = $playlist->type == 'video'
            ? Item::whereHas('video')->with('video')->where('slug', $itemSlug)->firstOrFail()
            : Item::whereHas('audio')->with('audio')->where('slug', $itemSlug)->firstOrFail();

        // Retrieve all items in the playlist that have video or audio
        $playlistItems = $playlist->type == 'video'
            ? $playlist->items()->whereHas('video')->with('video', 'translations')->get()
            : $playlist->items()->whereHas('audio')->with('audio', 'translations')->get();

        $seoContents = $this->getItemSeoContent($item);
        $checkWatchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        $watchEligable = $checkWatchEligable[0];
        $videos = $playlist->type == 'video' ? $this->videoList($item->video ?? null) : [];
        $audios = $playlist->type == 'audio' ? $this->audioList($item->audio ?? null) : [];

        $this->storeHistory($item->id);
        $this->storeVideoReport($item->id);
        $adsTime = [];
        $subtitles = [];
        if ($playlist->type == 'video') {
            $adsTime = $item->video->getAds() ?? [];
            $subtitles = $item->video->subtitles;
        }

        return view($this->activeTemplate . 'playlists.play', compact(
            'pageTitle',
            'playlist',
            'allPlaylists',
            'item',
            'playlistItems',
            'seoContents',
            'watchEligable',
            'adsTime',
            'videos',
            'audios',
            'subtitles'
        ));
    }

    public function watchVideo($slug, $episodeId = null)
    {
        $item = Item::active()->where('slug', $slug)->with('video.subtitles')->firstOrFail();
        $item->increment('view');


        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;

        if ($item->item_type == Status::EPISODE_ITEM) {
            $episodes = Episode::hasVideo()->with(['video', 'item'])->where('item_id', $item->id)->get();
            $relatedItems = $this->relatedItems($item->id, Status::EPISODE_ITEM, $item->tags, "video");
            $relatedAudios = $this->relatedItems($item->id, Status::EPISODE_ITEM, $item->tags, "audio");
            $pageTitle = 'Episode Details';

            if ($episodes->isEmpty()) {
                $notify[] = ['error', 'Oops! There is no video'];
                return back()->withNotify($notify);
            }

            $subscribedUser = auth()->check() && (auth()->user()->exp > now());
            if ($episodeId) {
                $episode = Episode::hasVideo()->findOrFail($episodeId);
                $firstVideo = $episode->video;
                $isPaidItem = $episode->version ? Status::ENABLE : Status::DISABLE;
                $activeEpisode = $episode;
            } else {
                $firstVideo = $episodes[0]->video;
                $activeEpisode = $episodes[0];
                $isPaidItem = $activeEpisode->version ? Status::ENABLE : Status::DISABLE;
                $episodeId = $activeEpisode->id;
            }

            $this->storeHistory(episodeId: $activeEpisode->id);
            $this->storeVideoReport(episodeId: $activeEpisode->id);

            $video = $firstVideo;
            $checkWatchEligable = $this->checkWatchEligableEpisode($activeEpisode, $userHasSubscribed);
        } else {
            $this->storeHistory($item->id);
            $this->storeVideoReport($item->id);

            $pageTitle = 'Movie Details';
            $relatedAudios =  $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "audio");
            $relatedItems = $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "video");
            $episodes = [];
            $video = $item->video;
            $checkWatchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        }

        $watchEligable = $checkWatchEligable[0];
        $hasSubscribedItem = $checkWatchEligable[1];

        if (!$video) {
            $notify[] = ['error', 'There are no videos for this item'];
            return back()->withNotify($notify);
        }

        $adsTime = $video->getAds() ?? [];
        $playlists = $item->playlists()->get(); // Or however you're fetching playlists related to the item
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        $subtitles = $video->subtitles;
        $videos = $this->videoList($video);
        $seoContents = $this->getItemSeoContent($item);
        return view($this->activeTemplate . 'watch', compact('pageTitle', 'item', 'allPlaylists', 'playlists', 'relatedAudios', 'relatedItems', 'seoContents', 'adsTime', 'subtitles', 'videos', 'episodes', 'episodeId', 'watchEligable', 'userHasSubscribed', 'hasSubscribedItem'));
    }

    public function watchLive($slug)
    {
        // dd($slug);

        $item = Item::active()->where('slug', $slug)->firstOrFail();
        $item->increment('view');


        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;

        $this->storeHistory($item->id);
        $this->storeVideoReport($item->id);

        $pageTitle = 'Live Details';
        $relatedAudios =  $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "audio");
        $relatedItems = $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "video");
        $episodes = [];
        $stream = $item->stream;
        $checkWatchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);

        $watchEligable = $checkWatchEligable[0];
        $hasSubscribedItem = $checkWatchEligable[1];

        if (!$stream) {
            $notify[] = ['error', 'There are no stream for this item'];
            return back()->withNotify($notify);
        }
        $adsTime = $stream->getAds() ?? [];
        $seoContents = $this->getItemSeoContent($item);
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'live_stream', compact('pageTitle', 'allPlaylists', 'item', 'relatedAudios', 'relatedItems', 'seoContents', 'adsTime', 'watchEligable', 'userHasSubscribed', 'hasSubscribedItem'));
    }
    private function relatedItems($itemId, $itemType, $keyword, $type)
    {
        $lang = app()->getLocale();


        if ($keyword != null) {
            // Get matching items based on keywords and item type
            $items = $this->getMatchingItems($keyword, $type, $itemType, $itemId);
            // Apply additional filters before executing the query
            // $itemstoreturn = $items->where('item_type', $itemType)
            //     ->where('id', '!=', $itemId)
            //     ->orderBy('id', 'desc')
            //     ->limit(8)
            //     ->get();
            foreach ($items as $item) {
                if ($lang !== 'ar') {
                    $translate = ContentTranslation::where("item_id", $item->id)->where("language", $lang)->first();

                    $item->title = $translate != null ? $translate->translated_title : $item->title;
                    $item->description = $translate != null ? $translate->translated_description : $item->title;
                }
                # code...

            }
            return $items;
        } else {
            // Get items based on item type without keywords

            // $items = $type === "video" ? Item::hasVideo()->orderBy('id', 'desc')
            //     ->where('item_type', $itemType)
            //     ->where('id', '!=', $itemId)
            //     ->limit(8)
            //     ->get() : Item::hasAudio()->orderBy('id', 'desc')
            //     ->where('item_type', $itemType)
            //     ->where('id', '!=', $itemId)
            //     ->limit(8)
            //     ->get();
            foreach ($items as $item) {
                if ($lang !== 'ar') {
                    # code...
                    $translate = ContentTranslation::where("item_id", $item->id)->where("language", $lang)->first();

                    $item->title = $translate != null ? $translate->translated_title : $item->title;
                    $item->description = $translate != null ? $translate->translated_description : $item->title;
                }
            }
            return $items;
        }
    }

    private function getMatchingItems($userKeywords, $type, $itemType, $itemId)
    {
        // Convert user keywords into an array, trim whitespace, and remove empty elements
        $keywordsArray = array_filter(array_map('trim', explode(',', $userKeywords)));

        // Return early if no keywords are provided
        if (empty($keywordsArray)) {
            return collect(); // Return an empty collection if no keywords are provided
        }

        // Initialize the query based on item type
        $query = $type === "video" ? Item::hasVideo()->where('is_audio', 0) : Item::hasAudio()->where('is_audio', 1);

        // Add a condition to match items with any of the keywords
        $query->where(function ($subQuery) use ($keywordsArray) {
            foreach ($keywordsArray as $keyword) {
                // Check each keyword using FIND_IN_SET
                $subQuery->orWhereRaw("FIND_IN_SET(?, tags) > 0", [$keyword]);
            }
        });

        // Apply the orderBy before calling get()
        $items = $query->where('item_type', $itemType)
            ->where('id', '!=', $itemId)->get();

        // Filter items to return only those that have at least 2 matching keywords
        $filteredItems = $items->filter(function ($item) use ($keywordsArray) {
            $tagArray = explode(',', $item->tags);
            $matchCount = 0;

            // Count how many keywords match the tags
            foreach ($keywordsArray as $keyword) {
                if (in_array($keyword, $tagArray)) {
                    $matchCount++;
                }

                // If two or more keywords match, return true
                if ($matchCount >= 2) {
                    return true;
                }
            }

            // If less than two keywords match, return false
            return false;
        });

        return $filteredItems;
    }




    private function storeHistory($itemId = null, $episodeId = null)
    {
        if (auth()->check()) {
            if ($itemId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->item_id != $itemId)) {
                    $history = new History();
                    $history->user_id = auth()->id();
                    $history->item_id = $itemId;
                    $history->save();
                }
            }
            if ($episodeId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->episode_id != $episodeId)) {
                    $history = new History();
                    $history->user_id = auth()->id();
                    $history->episode_id = $episodeId;
                    $history->save();
                }
            }
        }
    }

    protected function storeVideoReport($itemId = null, $episodeId = null)
    {
        $deviceId = md5($_SERVER['HTTP_USER_AGENT']);

        if ($itemId) {
            $report = VideoReport::whereDate('created_at', now())->where('device_id', $deviceId)->where('item_id', $itemId)->exists();
        }

        if ($episodeId) {
            $report = VideoReport::whereDate('created_at', now())->where('device_id', $deviceId)->where('episode_id', $episodeId)->exists();
        }
        if (!$report) {
            $videoReport = new VideoReport();
            $videoReport->device_id = $deviceId;
            $videoReport->item_id = $itemId ?? 0;
            $videoReport->episode_id = $episodeId ?? 0;
            $videoReport->save();
        }
    }

    protected function checkWatchEligableEpisode($episode, $userHasSubscribed)
    {
        if ($episode->version == Status::PAID_VERSION) {
            $watchEligable = $userHasSubscribed ? true : false;
        } else if ($episode->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $episode->item_id)->whereDate('expired_date', '>', now())->exists();
            if (@$episode->item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = ($userHasSubscribed || $hasSubscribedItem) ? true : false;
            }
        } else {
            $watchEligable = true;
        }
        return [$watchEligable, @$hasSubscribedItem ?? 0];
    }

    protected function checkWatchEligableItem($item, $userHasSubscribed)
    {
        if ($item->version == Status::PAID_VERSION) {
            $watchEligable = $userHasSubscribed ? true : false;
        } else if ($item->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = ($userHasSubscribed || $hasSubscribedItem) ? true : false;
            }
        } else {
            $watchEligable = true;
        }
        return [$watchEligable, @$hasSubscribedItem ?? 0];
    }

    private function videoList($video)
    {
        $videoFile = [];
        if ($video->three_sixty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'three_sixty'),
                'size' => 360,
            ];
        }
        if ($video->four_eighty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'four_eighty'),
                'size' => 480,
            ];
        }
        if ($video->seven_twenty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'seven_twenty'),
                'size' => 720,
            ];
        }
        if ($video->thousand_eighty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'thousand_eighty'),
                'size' => 1080,
            ];
        }

        return json_decode(json_encode($videoFile, true));
    }

    private function getItemSeoContent($item)
    {
        $lang = app()->getLocale();


        $translate = ContentTranslation::where("item_id", $item->id)->where("language", $lang)->first();
        if ($translate != null) {
            $seoContents['keywords'] = $translate->translated_tags ?? $item->tags;
            $seoContents['social_title'] = $translate->translated_title;
            $seoContents['description'] = strLimit(strip_tags($translate->translated_description), 150);
            $seoContents['social_description'] = strLimit(strip_tags($translate->translated_description), 150);
        } else {
            $seoContents['keywords'] = $item->meta_keywords ?? [];
            $seoContents['social_title'] = $item->title;
            $seoContents['description'] = strLimit(strip_tags($item->description), 150);
            $seoContents['social_description'] = strLimit(strip_tags($item->description), 150);
        }
        $seoContents['image'] = getImage(getFilePath('item_landscape') . '/' . $item->image->landscape);
        $seoContents['image_size'] = '900x600';
        return $seoContents;
    }

    public function previewAudio($slug, $episodeId = null)
    {
        $item = Item::active()->where('slug', $slug)->firstOrFail();
        $item->increment('view');


        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;

        if ($item->item_type == Status::EPISODE_ITEM) {
            $episodes = Episode::hasAudio()->with(['audio', 'item'])->where('item_id', $item->id)->get();
            $relatedItems = $this->relatedItems($item->id, Status::EPISODE_ITEM, $item->tags, "video");
            $relatedAudios = $this->relatedItems($item->id, Status::EPISODE_ITEM, $item->tags, "audio");
            $pageTitle = 'Episode Details';

            if ($episodes->isEmpty()) {
                $notify[] = ['error', 'Oops! There is no audio'];
                return back()->withNotify($notify);
            }

            $subscribedUser = auth()->check() && (auth()->user()->exp > now());
            if ($episodeId) {
                $episode = Episode::hasAudio()->findOrFail($episodeId);
                $firstAudio = $episode->audio;
                $isPaidItem = $episode->version ? Status::ENABLE : Status::DISABLE;
                $activeEpisode = $episode;
            } else {
                $firstAudio = $episodes[0]->audio;
                $activeEpisode = $episodes[0];
                $isPaidItem = $activeEpisode->version ? Status::ENABLE : Status::DISABLE;
                $episodeId = $activeEpisode->id;
            }

            $this->storeHistory(episodeId: $activeEpisode->id);
            $this->storeVideoReport(episodeId: $activeEpisode->id);

            $audio = $firstAudio;
            $checkWatchEligable = $this->checkWatchEligableEpisode($activeEpisode, $userHasSubscribed);
        } else {
            $this->storeHistory($item->id);
            $this->storeVideoReport($item->id);

            $pageTitle = 'Audio Details';
            $relatedAudios =  $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "audio");
            $relatedItems = $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "video");

            $episodes = [];
            $audio = $item->audio;
            $checkWatchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        }

        $watchEligable = $checkWatchEligable[0];
        $hasSubscribedItem = $checkWatchEligable[1];

        if (!$audio) {
            $notify[] = ['error', 'There are no audio for this item'];
            return back()->withNotify($notify);
        }
        $playlists = $item->playlists()->get(); // Or however you're fetching playlists related to the item
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        $audios = $this->audioList($audio);
        $seoContents = $this->getItemSeoContent($item);
        return view($this->activeTemplate . 'preview-audio', compact('pageTitle', 'allPlaylists', 'playlists', 'item', 'relatedAudios', 'relatedItems', 'seoContents', 'audios', 'episodes', 'episodeId', 'watchEligable', 'userHasSubscribed', 'hasSubscribedItem'));
    }



    public function category($id)
    {
        // Find the main category
        $category = Category::findOrFail($id);

        // Get the subcategories that belong to this main category

        $subcategories = SubCategory::where('category_id', $id)->pluck('id')->toArray();

        // Get playlists that belong to the subcategories
        $playlists = Playlist::whereIn('sub_category_id', $subcategories)->orderBy('id', 'desc')->get();


        // Check the category type (video/audio)
        if ($category->type === "vid") {

            // Get items that have video in the main category
            $items = Item::hasVideo()->where('category_id', $id)->orderBy('id', 'desc')->limit(12)->get();
        }
        if ($category->type === "aud") {
            // Get audio items from the main category
            $items = Item::hasAudio()->where('category_id', $id)->orderBy('id', 'desc')->limit(12)->get();
        }

        // Check if any of the items has a live stream
        $hasStream = $items->contains(function ($value) {
            return $value->is_stream;
        });

        // Set the page title dynamically based on the locale
        $pageTitle = app()->getLocale() == 'ar' ? $category->name : $category->name_en;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        // Return the view with the collected data
        return view($this->activeTemplate . 'items', compact('pageTitle', 'items', 'allPlaylists', 'playlists', 'category', 'hasStream'));
    }

    public function subCategory($id)
    {
        $subcategory = SubCategory::findOrFail($id);

        if ($subcategory->type === "vid") {
            $items = Item::hasVideo()->where('sub_category_id', $id)->orderBy('id', 'desc')->limit(12)->get();
        }
        if ($subcategory->type === "aud") {
            $items = Item::hasAudio()->where('sub_category_id', $id)->orderBy('id', 'desc')->limit(12)->get();
        }
        $playlists = Playlist::where('sub_category_id', $id)->orderBy('id', 'desc')->get();

        $hasStream = false;
        foreach ($items as  $value) {
            if ($value->is_stream) {
                $hasStream = true;
                break;
            }
        }
        $pageTitle = app()->getLocale() == 'ar' ? $subcategory->name : $subcategory->name_en;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'items', compact('pageTitle', 'allPlaylists', 'items', 'subcategory', 'hasStream', 'playlists'));
    }

    public function loadMore(Request $request)
    {
        if (isset($request->category_id)) {
            $data['category'] = Category::find($request->category_id);
            $data['items'] = Item::hasVideo()->where('category_id', $request->category_id)->orderBy('id', 'desc')->where('id', '<', $request->id)->take(6)->get();
        } else if (isset($request->subcategory_id)) {
            $data['sub_category'] = SubCategory::find($request->subcategory_id);
            $data['items'] = Item::hasVideo()->where('sub_category_id', $request->subcategory_id)->orderBy('id', 'desc')->where('id', '<', $request->id)->take(6)->get();
        } else if (isset($request->search)) {
            $data['search'] = $request->search;
            $data['items'] = Item::hasVideo()->search($request->search)->orderBy('id', 'desc')->where('id', '<', $request->id)->take(6)->get();
        } else {
            return response('end');
        }

        if ($data['items']->count() <= 0) {
            return response('end');
        }

        return view($this->activeTemplate . 'item_ajax', $data);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        if (!$search) {
            return redirect()->route('home');
        }

        // Fetch items that match the search criteria
        $items = Item::search($search)
            ->where('status', 1)
            ->orWhereHas('translations', function ($translation) use ($search) {
                $translation->where('translated_title', 'LIKE', "%$search%")
                    ->orWhere('translated_description', 'LIKE', "%$search%")
                    ->orWhere('translated_tags', 'LIKE', "%$search%")
                    ->orWhere('translated_keywords', 'LIKE', "%$search%");
            })
            ->where(function ($query) {
                $query->orWhereHas('video')
                    ->orWhereHas('audio');
            })->with('playlists')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();

        // Fetch playlists where at least one item matches the search criteria

        // Initialize an empty collection to hold unique playlists
        $playlists = collect();

        // Loop through the items and add their playlists to the collection
        foreach ($items as $item) {
            if ($item->playlists) {
                // Merge unique playlists into the collection
                $playlists = $playlists->merge($item->playlists);
            }
        }

        // Get unique playlists by 'id'
        $playlists = $playlists->unique('id');


        // Check if there are any live streams among the items
        $hasStream = false;
        foreach ($items as $value) {
            if ($value->is_stream) {
                $hasStream = true;
                break;
            }
        }

        $pageTitle = "Result Showing For " . $search;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        // Return the items and playlists in the view
        return view($this->activeTemplate . 'items', compact('pageTitle', 'items', 'allPlaylists', 'playlists', 'hasStream', 'search'));
    }



    public function policy($id, $slug)
    {
        $item = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $item->data_values->title;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'links_details', compact('pageTitle', 'allPlaylists', 'item'));
    }

    public function links($id, $slug)
    {
        $item = Frontend::where('id', $id)->where('data_keys', 'short_links.element')->firstOrFail();
        $pageTitle = $item->data_values->title;
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'links_details', compact('pageTitle', 'allPlaylists', 'item'));
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:40|unique:subscribers',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }
        $subscribe = new Subscriber();
        $subscribe->email = $request->email;
        $subscribe->save();
        return response()->json(['success' => 'Subscribe successfully']);
    }

    public function liveTelevision($id = 0)
    {
        $pageTitle = 'Live TV list';
        $tvs = LiveTelevision::where('status', 1)->get();
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'live_tvs', compact('pageTitle', 'allPlaylists', 'tvs'));
    }

    public function watchTelevision($id)
    {
        $tv = LiveTelevision::active()->findOrFail($id);
        $pageTitle = $tv->title;
        $otherTvs = LiveTelevision::active()->where('id', '!=', $id)->get();
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'watch_tv', compact('pageTitle', 'tv', 'allPlaylists', 'otherTvs'));
    }

    public function addWishList(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be login to add an item to wishlist',
            ]);
        }

        $wishlist = new Wishlist();

        if ($request->type == 'item') {
            $data = Item::where('id', $request->id)->first();
        } else {
            $data = Episode::where('id', $request->id)->first();
            $wishlist->item_id = $data->item_id;
        }
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }
        $column = $request->type . '_id';
        $wishlist->$column = $data->id;
        $exits = Wishlist::where($column, $data->id)->where('user_id', auth()->id())->first();
        if (!$exits) {
            $wishlist->user_id = auth()->id();
            $wishlist->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Video added to wishlist successfully',
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Already in wishlist',
        ]);
    }

    public function removeWishlist(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be login to add an item to wishlist',
            ]);
        }

        $column = $request->type . '_id';
        $wishlist = Wishlist::where($column, $request->id)->where('user_id', auth()->id())->first();
        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Request',
            ]);
        }
        $wishlist->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Video removed from wishlist successfully',
        ]);
    }

    public function addClick(Request $request)
    {
        $ad = Advertise::find($request->id);
        $ad->increment('click');
        return response()->json("Success");
    }

    public function storeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token = $request->token;
        $deviceToken->is_app = 0;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token save successfully'];
    }

    public function templateswitch($name)
    {
        session()->put('templates', $name);

        return redirect()->route('home');
    }

    public function pusher($socketId, $channelName)
    {
        $general = gs();
        $pusherSecret = $general->pusher_config->app_secret_key;
        $str = $socketId . ":" . $channelName;
        $hash = hash_hmac('sha256', $str, $pusherSecret);
        return response()->json([
            'success' => true,
            'message' => "Pusher authentication successfully",
            'auth' => $general->pusher_config->app_key . ":" . $hash,
        ]);
    }

    public function subscription()
    {
        $pageTitle = 'Subscribe';
        $plans = Plan::active()->paginate(getPaginate());
        $allPlaylists = Playlist::whereHas('items', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
        })->limit(12)->get();

        return view($this->activeTemplate . 'subscription', compact('pageTitle', 'allPlaylists', 'plans'));
    }

    private function audioList($audio)
    {
        $audioFile[] = [
            'content' => getAudioFile($audio),
            'size' => 360,
        ];

        return json_decode(json_encode($audioFile, true));
    }
}

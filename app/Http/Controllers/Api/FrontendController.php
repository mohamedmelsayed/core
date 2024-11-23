<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Category;
use App\Models\Episode;
use App\Models\Frontend;
use App\Models\History;
use App\Models\Item;
use App\Models\Language;
use App\Models\LiveTelevision;
use App\Models\Playlist;
use App\Models\Slider;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Models\VideoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{

    public function logo()
    {
        $notify[] = 'Logo Information';
        $logo     = getFilePath('logoIcon') . '/logo.png';
        $favicon  = getFilePath('logoIcon') . '/favicon.png';

        return response()->json([
            'remark'  => 'logo_info',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'logo'    => $logo,
                'favicon' => $favicon,
            ],
        ]);
    }

    public function welcomeInfo()
    {
        $notify[] = 'Welcome Info';
        $welcome  = Frontend::where('data_keys', 'app_welcome.content')->first();
        $path     = 'assets/images/frontend/app_welcome';

        return response()->json([
            'remark'  => 'welcome_info',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'welcome' => $welcome->data_values,
                'path'    => $path,
            ],
        ]);
    }

    public function sliders()
    {
        $sliders  = Slider::with('item', 'item.category', 'item.sub_category')->get();
        $notify[] = 'All Sliders';
        $path     = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'all_sliders',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'sliders'        => $sliders,
                'landscape_path' => $path,
            ],
        ]);
    }

    public function liveTelevision()
    {
        $notify[]    = 'Live Television';
        $televisions = LiveTelevision::where('status', 1)->apiQuery();
        $imagePath   = getFilePath('television');

        return response()->json([
            'remark'  => 'live_television',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'televisions' => $televisions,
                'image_path'  => $imagePath,
            ],
        ]);
    }

    public function featured()
    {
        $notify[]     = 'Featured';
        $featured     = Item::active()->hasVideo()->where('featured', Status::ENABLE)->apiQuery();
        $imagePath    = getFilePath('item_landscape');
        $portraitPath = getFilePath('item_portrait');

        return response()->json([
            'remark'  => 'featured',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'featured'       => $featured,
                'landscape_path' => $imagePath,
                'portrait_path'  => $portraitPath,
            ],
        ]);
    }

    public function listAudio(Request $request)
    {
        $notify[] = 'Recently Added';
        $perPage = $request->query('per_page', 10); // Set a default of 10 items per page if not provided
    
        $audio = Item::active()
            ->hasAudio()
            ->where('item_type', Status::SINGLE_ITEM)
            ->paginate($perPage);
    
        $imagePath = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
    
        return response()->json([
            'remark' => 'recently_added',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'items' => $audio->items(), // Returns only the data for the current page
                'portrait_path' => $imagePath,
                'landscape_path' => $landscapePath,
                'pagination' => [
                    'total' => $audio->total(),
                    'current_page' => $audio->currentPage(),
                    'last_page' => $audio->lastPage(),
                    'per_page' => $audio->perPage(),
                ]
            ],
        ]);
    }
    
    public function listVideo(Request $request)
    {
        $notify[] = 'Recently Added';
        $perPage = $request->query('per_page', 10); // Set a default of 10 items per page if not provided
    
        $videos = Item::active()
            ->hasVideo()
            ->where('item_type', Status::SINGLE_ITEM)
            ->paginate($perPage);
    
        $imagePath = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
    
        return response()->json([
            'remark' => 'recently_added',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'items' => $videos->items(), // Returns only the data for the current page
                'portrait_path' => $imagePath,
                'landscape_path' => $landscapePath,
                'pagination' => [
                    'total' => $videos->total(),
                    'current_page' => $videos->currentPage(),
                    'last_page' => $videos->lastPage(),
                    'per_page' => $videos->perPage(),
                ]
            ],
        ]);
    }
    

    public function recentlyAdded()
    {
        $notify[]      = 'Recently Added';
        $recentlyAdded = Item::active()->hasVideoOrAudio()->where('item_type', Status::SINGLE_ITEM)->apiQuery();
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'recently_added',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'recent'         => $recentlyAdded,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function latestSeries()
    {
        $notify[]      = 'Latest Series';
        $latestSeries  = Item::active()->hasVideoOrAudio()->where('item_type', Status::EPISODE_ITEM)->apiQuery();
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'latest-series',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'latest'         => $latestSeries,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function single()
    {
        $notify[] = 'Single Item';

        $single = Item::active()->hasVideoOrAudio()->where('single', 1)->with('category')->apiQuery();

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'single',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'single'         => $single,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function trailer()
    {
        $notify[] = 'Trailer';
        $trailer  = Item::active()->hasVideoOrAudio()->where('item_type', Status::SINGLE_ITEM)->where('is_trailer', Status::TRAILER)->apiQuery();

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'trailer',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'trailer'        => $trailer,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function rent()
    {
        $notify[] = 'Rent';
        $rent     = Item::active()->hasVideoOrAudio()->where('item_type', Status::SINGLE_ITEM)->where('version', Status::RENT_VERSION)->apiQuery();

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'rent',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'rent'           => $rent,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function freeZone()
    {
        $notify[]      = 'Free Zone';
        $freeZone      = Item::active()->hasVideoOrAudio()->free()->orderBy('id', 'desc')->apiQuery();
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'free_zone',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'free_zone'      => $freeZone,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function categories()
    {
        $notify[]   = 'All Categories';
        $categories = Category::where('status', Status::ENABLE)->apiQuery();
        return response()->json([
            'remark'  => 'all-categories',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'categories' => $categories,
            ],
        ]);
    }

    public function subcategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $notify[]      = 'SubCategories';
        $subcategories = SubCategory::where('category_id', $request->category_id)->where('status', Status::ENABLE)->apiQuery();

        return response()->json([
            'remark'  => 'sub-categories',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'subcategories' => $subcategories,
            ],
        ]);
    }

    public function search(Request $request)
    {
        $notify[] = 'Search';
        $search   = $request->search;

        $items = Item::search($search)->where('status', 1)->where(function ($query) {
            $query->hasVideoOrAudio('video');
        });

        if ($request->category_id) {
            $items = $items->where('category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $items = $items->where('sub_category_id', $request->subcategory_id);
        }

        $items = $items->orderBy('id', 'desc')->paginate(getPaginate());

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'search',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'items'          => $items,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function watchVideo(Request $request)
    {
        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->with('category', 'sub_category')->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        $relatedItems = Item::hasVideoOrAudio()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get();

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;
      

        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);

        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item'           => $item,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'related_items'  => $relatedItems,
                ],
            ]);
        }

        $this->storeHistory($item->id, 0);
        $this->storeVideoReport($item->id, 0);

        $notify[] = 'Item Video';

        return response()->json([
            'remark'  => 'item_video',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'item'           => $item,
                'related_items'  => $relatedItems,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'episode_path'   => $episodePath,
                'watchEligable'  => $watchEligable[0],
                'type'           => $watchEligable[1],
            ],
        ]);
    }


    public function viewAudio(Request $request)
    {
        $item = Item::hasAudio()->where('status', 1)->where('id', $request->item_id)->with('category', 'sub_category')->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        $relatedItems = Item::hasVideoOrAudio()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get();

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;
     
        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);

        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item'           => $item,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'related_items'  => $relatedItems,
                ],
            ]);
        }

        $this->storeHistory($item->id, 0);
        $this->storeVideoReport($item->id, 0);

        $notify[] = 'Item Video';

        return response()->json([
            'remark'  => 'item_video',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'item'           => $item,
                'related_items'  => $relatedItems,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'episode_path'   => $episodePath,
                'watchEligable'  => $watchEligable[0],
                'type'           => $watchEligable[1],
            ],
        ]);
    }

    protected function checkWatchEligableItem($item, $userHasSubscribed)
    {
        if ($item->version == Status::PAID_VERSION) {
            $watchEligable = $userHasSubscribed ? true : false;
            $type          = 'paid';
        } else if ($item->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = ($userHasSubscribed || $hasSubscribedItem) ? true : false;
            }
            $type = 'rent';
        } else {
            $watchEligable = true;
            $type          = 'free';
        }
        return [$watchEligable, $type];
    }

    protected function checkWatchEligableEpisode($episode, $userHasSubscribed)
    {
        if ($episode->version == Status::PAID_VERSION) {
            $watchEligable = $userHasSubscribed ? true : false;
            $type          = 'paid';
        } else if ($episode->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $episode->item_id)->whereDate('expired_date', '>', now())->exists();
            if (@$episode->item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = ($userHasSubscribed || $hasSubscribedItem) ? true : false;
            }
            $type = 'rent';
        } else {
            $watchEligable = true;
            $type          = 'free';
        }
        return [$watchEligable, $type];
    }

    public function playVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->first();
        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

   

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;



        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item' => $item,
                ],
            ]);
        }

        $video    = $item->video;
        $remark   = 'item_video';
        $notify[] = 'Item Video';


        $videoFile    = $this->videoList($video);
        $subtitles    = $video->subtitles()->get();
        $adsTime      = $video->getAds();
        $subtitlePath = getFilePath('subtitle');

        return response()->json([
            'remark'  => $remark,
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'video'         => $videoFile,
                'subtitles'     => !blank($subtitles) ? $subtitles : null,
                'adsTime'       => !blank($adsTime) ? $adsTime : null,
                'subtitlePath'  => $subtitlePath,
                'watchEligable' => $watchEligable[0],
                'type'          => $watchEligable[1],
            ],
        ]);
    }

    public function playlists(Request $request)
    {
        $category_id = $request->query('category_id');
        $type = $request->query('type');
    
        $allPlaylists = Playlist::whereHas('items', function ($query) use ($category_id,$type) {
            $query->where(function ($q) {
                $q->whereHas('video')->orWhereHas('audio');
            });
    
            if ($category_id) {
                $query->where('sub_category_id', $category_id);
            }
            if ($type) {
                $query->where('type', $type);
            }
        })->limit(12)->get();
    
        $notify[] = 'Play Lists';
        $remark = 'play_lists';
    
        return response()->json([
            'remark' => $remark,
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'playLists' => $allPlaylists,
            ],
        ]);
    }

    public function playlist( $id)
    {
        $category_id = $request->query('category_id');
        $type = $request->query('type');

        $playlist=playlist::where("id",$id)->with('items')->first();




    
       
    
        $notify[] = 'Play List details';
        $remark = 'play_list details';
    
        return response()->json([
            'remark' => $remark,
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'playList' => $playlist??[],
            ],
        ]);
    }
    

    public function playAudio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $item = Item::hasAudio()->where('status', 1)->where('id', $request->item_id)->first();
        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

    

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;



        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item' => $item,
                ],
            ]);
        }

        $audio    = $item->audio;
        $remark   = 'item_audio';
        $notify[] = 'Item Audio';


        $audioFile    = $this->audioList($audio);
        $subtitlePath = getFilePath('subtitle');

        return response()->json([
            'remark'  => $remark,
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'audio'         => $audioFile,
                'subtitlePath'  => $subtitlePath,
                'watchEligable' => $watchEligable[0],
                'type'          => $watchEligable[1],
            ],
        ]);
    }

    private function audioList($audio)
    {
        $audioFile[] = [
            'content' => getAudioFile($audio),
            'size' => 360,
        ];

        return json_decode(json_encode($audioFile, true));
    }

    private function videoList($video)
    {
        $videoFile = [];
        if ($video->three_sixty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'three_sixty'),
                'size'    => 360,
            ];
        }
        if ($video->four_eighty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'four_eighty'),
                'size'    => 480,
            ];
        }
        if ($video->seven_twenty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'seven_twenty'),
                'size'    => 720,
            ];
        }
        if ($video->thousand_eighty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'thousand_eighty'),
                'size'    => 1080,
            ];
        }

        return json_decode(json_encode($videoFile, true));
    }

    protected function storeHistory($itemId = null, $episodeId = null)
    {
        if (auth()->check()) {
            if ($itemId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->item_id != $itemId)) {
                    $history          = new History();
                    $history->user_id = auth()->id();
                    $history->item_id = $itemId;
                    $history->save();
                }
            }
            if ($episodeId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->episode_id != $episodeId)) {
                    $history             = new History();
                    $history->user_id    = auth()->id();
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
            $videoReport             = new VideoReport();
            $videoReport->device_id  = $deviceId;
            $videoReport->item_id    = $itemId ?? 0;
            $videoReport->episode_id = $episodeId ?? 0;
            $videoReport->save();
        }
    }

    public function policyPages()
    {
        $notify[]    = 'Policy Page';
        $policyPages = Frontend::where('data_keys', 'policy_pages.element')->get();

        return response()->json([
            'remark'  => 'policy',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'policy_pages' => $policyPages,
            ],
        ]);
    }

    public function movies()
    {
        $notify[]      = 'All Movies';
        $movies        = Item::active()->hasVideo()->where('item_type', Status::SINGLE_ITEM)->apiQuery();
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'all_movies',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'movies'         => $movies,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function episodes()
    {
        $notify[]      = 'All Episodes';
        $episodes      = Item::active()->hasVideo()->where('item_type', Status::EPISODE_ITEM)->apiQuery();
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'all_episodes',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'episodes'       => $episodes,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function watchTelevision($id = 0)
    {
        $tv = LiveTelevision::where('id', $id)->where('status', Status::ENABLE)->first();

        if (!$tv) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Television not found'],
            ]);
        }

        $notify[]  = $tv->title;
        $relatedTv = LiveTelevision::where('id', '!=', $id)->where('status', 1)->limit(8)->orderBy('id', 'desc')->get();
        $imagePath = getFilePath('television');
        return response()->json([
            'remark'  => 'tv_details',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'tv'         => $tv,
                'related_tv' => $relatedTv,
                'image_path' => $imagePath,
            ],
        ]);
    }

    public function language($code = 'en')
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            $code = 'en';
        }
        $languageData = json_decode(file_get_contents(resource_path('lang/' . $code . '.json')));
        $languages    = Language::get();
        $notify[]     = 'Language Data';
        return response()->json([
            'remark'  => 'language_data',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'language_data' => $languageData,
                'languages'     => $languages,
            ],
        ]);
    }
    public function popUpAds()
    {
        $advertise = Advertise::where('device', 2)->where('ads_show', 1)->where('ads_type', 'banner')->inRandomOrder()->first();
        if (!$advertise) {
            return response()->json([
                'remark'  => 'advertise_not_found',
                'status'  => 'error',
                'message' => ['error' => 'Advertise not found'],
            ]);
        }
        $imagePath = getFilePath('ads');
        return response()->json([
            'remark' => 'pop_up_ad',
            'status' => 'success',
            'data'   => [
                'advertise' => $advertise,
                'imagePath' => $imagePath,
            ],
        ]);
    }
}

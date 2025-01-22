<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Category;
use App\Models\ContentTranslation;
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

    public function streamlist()
    {
        $notify[]    = 'Live Television';
        $streamlist = Item::getStream()->apiQuery();
        $imagePath   = getFilePath('television');

        return response()->json([
            'remark'  => 'live_television',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'streamlist' => $streamlist,
                'image_path'  => $imagePath,
            ],
        ]);
    }

    public function featured(Request $request)
    {
        $notify[] = 'Featured';
        $featured = Item::active()->hasVideoOrAudio()->where('featured', Status::ENABLE)->apiQuery();
        $imagePath = getFilePath('item_landscape');
        $portraitPath = getFilePath('item_portrait');
    
        $featured = $featured->map(function ($item) use ($request) {
            return $this->getTranslatedContent($item, $request);
    
           
    
        });
    
        return response()->json([
            'remark' => 'featured',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'featured' => $featured,
                'landscape_path' => $imagePath,
                'portrait_path' => $portraitPath,
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


    public function recentlyAdded(Request $request)
    {
        $notify[] = 'Recently Added';
        $recentlyAdded = Item::active()->hasVideoOrAudio()->where('item_type', Status::SINGLE_ITEM)->apiQuery();
        $imagePath = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
    
        $recentlyAdded = $recentlyAdded->map(function ($item) use ($request) {
            $translatedItem = $this->getTranslatedContent($item, $request);
    
            // Assign translated values to the original item fields
            $item->title = $translatedItem->title;
            $item->tags = $translatedItem->tags;
            $item->description = $translatedItem->description;
    
            return $item; // Return the modified item
        });
    
        return response()->json([
            'remark' => 'recently_added',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'recent' => $recentlyAdded,
                'portrait_path' => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }
    


    public function latestSeries(Request $request)
    {
        $notify[] = 'Latest Series';
        $latestSeries = Item::active()->hasVideoOrAudio()->where('item_type', Status::EPISODE_ITEM)->apiQuery();
        $imagePath = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
    
        $latestSeries = $latestSeries->map(function ($item) use ($request) {
            $translatedItem = $this->getTranslatedContent($item, $request);
    
            // Assign translated values to the original item fields
            $item->title = $translatedItem->title;
            $item->tags = $translatedItem->tags;
            $item->description = $translatedItem->description;
    
            return $item; // Return the modified item
        });
    
        return response()->json([
            'remark' => 'latest-series',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'latest' => $latestSeries,
                'portrait_path' => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }
    


    public function single(Request $request)
    {
        $notify[] = 'Single Item';
    
        $single = Item::active()->hasVideoOrAudio()->where('single', 1)->with('category')->apiQuery();
    
        $single = $single->map(function ($item) use ($request) {
            $translatedItem = $this->getTranslatedContent($item, $request);
    
            // Assign translated values while keeping original properties
            $item->name = $translatedItem->title;
            $item->tags = $translatedItem->tags;
            $item->description = $translatedItem->description;
            $item->type = $translatedItem->type;
    
            return $item;
        });
    
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
    

    public function trailer(Request $request)
{
    $notify[] = 'Trailer';
    $trailer  = Item::active()->hasVideoOrAudio()->where('item_type', Status::SINGLE_ITEM)->where('is_trailer', Status::TRAILER)->apiQuery();

    $trailer = $trailer->map(function ($item) use ($request) {
        $translatedItem = $this->getTranslatedContent($item, $request);

        $item->name = $translatedItem->title;
        $item->tags = $translatedItem->tags;
        $item->description = $translatedItem->description;
        $item->type = $translatedItem->type;

        return $item;
    });

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


    public function rent(Request $request)
    {
        $notify[] = 'Rent';
        $rent     = Item::active()->hasVideoOrAudio()->where('item_type', Status::SINGLE_ITEM)->where('version', Status::RENT_VERSION)->apiQuery();
    
        $rent = $rent->map(function ($item) use ($request) {
            $translatedItem = $this->getTranslatedContent($item, $request);
    
            $item->name = $translatedItem->title;
            $item->tags = $translatedItem->tags;
            $item->description = $translatedItem->description;
            $item->type = $translatedItem->type;
    
            return $item;
        });
    
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
    

    public function freeZone(Request $request)
    {
        $notify[]      = 'Free Zone';
        $freeZone      = Item::active()->hasVideoOrAudio()->free()->orderBy('id', 'desc')->apiQuery();
    
        $freeZone = $freeZone->map(function ($item) use ($request) {
            $translatedItem = $this->getTranslatedContent($item, $request);
    
            $item->name = $translatedItem->title;
            $item->tags = $translatedItem->tags;
            $item->description = $translatedItem->description;
            $item->type = $translatedItem->type;
    
            return $item;
        });
    
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
        $categories = Category::with('subcategories')->where('status', Status::ENABLE)->apiQuery();
        return response()->json([
            'remark'  => 'all-categories',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'categories' => $categories->map(function ($category) {
                    return [
                        'id'           => $category->id,
                        'name'         => $category->dynamic_name,
                        'subcategories' => $category->subcategories->map(function ($subcategory) {
                            return [
                                'id'   => $subcategory->id,
                                'name' => $subcategory->dynamic_name,
                            ];
                        }),

                    ];
                }),
                'pagination' => [
                    'total'        => $categories->total(),
                    'per_page'     => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    'last_page'    => $categories->lastPage(),
                    'from'         => $categories->firstItem(),
                    'to'           => $categories->lastItem(),
                ],
            ],
        ]);
    }
    public function subcategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $notify = ['SubCategories'];
        $subcategories = SubCategory::where('category_id', $request->category_id)
            ->where('status', Status::ENABLE)
            ->apiQuery()
            ->get(); // Ensure you execute the query

        return response()->json([
            'remark'  => 'sub-categories',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'subcategories' => $subcategories->map(function ($subcategory) {
                    return [
                        'id'   => $subcategory->id,
                        'name' => $subcategory->dynamic_name,
                    ];
                }),
                'pagination'    => [
                    'total'        => $subcategories->total(),
                    'per_page'     => $subcategories->perPage(),
                    'current_page' => $subcategories->currentPage(),
                    'last_page'    => $subcategories->lastPage(),
                    'from'         => $subcategories->firstItem(),
                    'to'           => $subcategories->lastItem(),
                ],
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
        $lang = $request->header('Language', 'en');
        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->with('category', 'sub_category')->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        // $relatedItems = Item::hasVideoOrAudio()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get();
        $relatedAudios =  $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "audio",$lang);
        $relatedVideos = $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "video",$lang);

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;


        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        $item=$this->getTranslatedContent($item, $request);


        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item'           => $item,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'related_audios'  => $relatedAudios,
                    'related_videos'  => $relatedVideos,
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
                'related_audios'  => $relatedAudios,
                'related_videos'  => $relatedVideos,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'episode_path'   => $episodePath,
                'watchEligable'  => $watchEligable[0],
                'type'           => $watchEligable[1],
            ],
        ]);
    }

    public function watchStream(Request $request)
    {
        $lang = $request->header('Language', 'en');
        $item = Item::with('stream')->getStream()->where('status', 1)->where('id', $request->item_id)->with('category', 'sub_category')->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        // $relatedItems = Item::hasVideoOrAudio()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get();
        $relatedAudios =  $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "audio",$lang);
        $relatedVideos = $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "video",$lang);
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;


        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);

        $item=$this->getTranslatedContent($item, $request);

        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item'           => $item,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'related_audios'  => $relatedAudios,
                    'related_videos'  => $relatedVideos,
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
                'related_audios'  => $relatedAudios,
                'related_videos'  => $relatedVideos,
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
        $lang = $request->header('Language', 'en');
        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        // $relatedItems = Item::hasVideoOrAudio()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get();
        $relatedAudios =  $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "audio",$lang);
        $relatedVideos = $this->relatedItems($item->id, Status::SINGLE_ITEM, $item->tags, "video",$lang);
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;

        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
        $item=$this->getTranslatedContent($item, $request);

        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item'           => $item,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'related_audios'  => $relatedAudios,
                    'related_videos'  => $relatedVideos,
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
                'related_audios'  => $relatedAudios,
                'related_videos'  => $relatedVideos,
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

        // Fetch playlists with related items
        $allPlaylists = Playlist::with('items')
            ->whereHas('items', function ($query) use ($category_id, $type) {
                $query->where(function ($q) {
                    $q->whereHas('video')->orWhereHas('audio');
                });

                if ($category_id) {
                    $query->where('sub_category_id', $category_id);
                }
                if ($type) {
                    $query->where('type', $type);
                }
            })
            ->limit(12)
            ->get();

        // Map the playlists to include dynamic title, description, and related items
        $playlists = $allPlaylists->map(function ($playlist) use ($request) {
            return [
                'id'           => $playlist->id,
                'title'        => $playlist->dynamic_title,
                'description'  => $playlist->dynamic_description,
                'type'         => $playlist->type,
                'cover_image'  => $playlist->cover_image,
                'items'        => $playlist->items->map(function ($item) use ($request) {
                    $translatedItem = $this->getTranslatedContent($item, $request);
                    return [
                        'id'          => $translatedItem->id,
                        'name'        => $translatedItem->title,
                        'tags'        => $translatedItem->tags,
                        'description' => $translatedItem->description,
                        'type'        => $translatedItem->type, // Assuming the item has a 'type' field
                    ];
                }),
            ];
        });

        $notify[] = 'Play Lists';
        $remark = 'play_lists';

        return response()->json([
            'remark' => $remark,
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'playLists' => $playlists,
            ],
        ]);
    }



    public function playlist($id, Request $request)
    {
        $playlist = Playlist::where("id", $id)->with('items')->first();

        if ($playlist) {
            $playlist->items = $playlist->items->map(function ($item) use ($request) {
                return $this->getTranslatedContent($item, $request);
            });
        }

        $notify[] = 'Play List details';
        $remark = 'play_list details';

        return response()->json([
            'remark' => $remark,
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'playList' => [
                    'id'          => $playlist->id,
                    'title'       => $playlist->dynamic_title,
                    'description' => $playlist->dynamic_description,
                    'items'       => $playlist->items,
                ],
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

    public function movies(Request $request)
    {
        $notify[]      = 'All Movies';
        $perPage       = $request->input('per_page', 10); // Get per_page from request or default to 10
        $movies        = Item::active()->hasVideo()->where('item_type', Status::SINGLE_ITEM)->paginate($perPage);
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
    
        return response()->json([
            'remark'  => 'all_movies',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'movies'         => $movies->items(),
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'pagination'     => [
                    'total'        => $movies->total(),
                    'current_page' => $movies->currentPage(),
                    'last_page'    => $movies->lastPage(),
                    'per_page'     => $movies->perPage(),
                ],
            ],
        ]);
    }
    public function audios(Request $request)
    {
        $notify[]      = 'All Audios';
        $perPage       = $request->input('per_page', 10); // Get per_page from request or default to 10
        $audios        = Item::active()->hasAudio()->where('item_type', Status::SINGLE_ITEM)->paginate($perPage);
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
    
        return response()->json([
            'remark'  => 'all_audios',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'audios'         => $audios->items(),
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'pagination'     => [
                    'total'        => $audios->total(),
                    'current_page' => $audios->currentPage(),
                    'last_page'    => $audios->lastPage(),
                    'per_page'     => $audios->perPage(),
                ],
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

    private function getTranslatedContent($item, $request)
    {
        $lang = $request->header('Language', 'en'); // Default to 'en'        $language = $request->header('Accept-Language', 'en'); // Default to 'en'

        // Get translated content for category (if exists)
        if ($item->category) {
            $item->category->name = $item->category->dynamic_name; 
        }

        // Get translated content for sub_category (if exists)
        if ($item->sub_category) {
            $item->sub_category->name =   $item->sub_category->dynamic_name;
        }
        $translate = ContentTranslation::where("item_id", $item->id)->where("language", $lang)->first();
        if ($translate != null) {
            $item->tags = $translate->translated_tags ?? $item->tags;
            $item->title = $translate->translated_title??$item->title;
            $item->description = $translate->translated_description??$item->description;
        } 
        if (!empty($item->team)) {
            $item->team = [
                'director' => $this->translateCommaSeparatedValues($item->team->director ?? ''),
                'producer' => $this->translateCommaSeparatedValues($item->team->producer ?? ''),
                
                'casts'    => __($item->team->casts ?? ''),
                'genres'   => __($item->team->genres ?? ''),
                'language' => __($item->team->language ?? ''),
            ];
        }
        return $item;
    }

    /**
 * Translate comma-separated values.
 */
private function translateCommaSeparatedValues($values)
{
    if (empty($values)) {
        return '';
    }

    // Split the string by commas, translate each part, and join them back
    return collect(explode(',', $values))
        ->map(function ($value) {
            return __(trim($value));
        })
        ->implode(', ');
}

    private function relatedItems($itemId, $itemType, $keyword, $type,$lang)
    {
        


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
             
                    $translate = ContentTranslation::where("item_id", $item->id)->where("language", $lang)->first();

                    $item->title = $translate != null ? $translate->translated_title : $item->title;
                    $item->description = $translate != null ? $translate->translated_description : $item->title;
                
            }
            return $items;
        } else {

            foreach ($items as $item) {
              
                    # code...
                    $translate = ContentTranslation::where("item_id", $item->id)->where("language", $lang)->first();

                    $item->title = $translate != null ? $translate->translated_title : $item->title;
                    $item->description = $translate != null ? $translate->translated_description : $item->title;
                
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

        return $filteredItems->values();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\MultiVideoUploader;
use App\Models\Episode;
use App\Models\Item;
use App\Models\Subtitle;
use App\Models\Video;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EpisodeController extends Controller {
    public function episodes($id) {
        $item = Item::findOrFail($id);
        if ($item->item_type != 2) {
            $notify[] = ['error', 'Something Wrong'];
            return redirect()->route('admin.dashboard')->withNotify($notify);
        }
        $pageTitle = "All Episode of : " . $item->title;
        $episodes  = Episode::with('video','item')->where('item_id', $item->id)->paginate(getPaginate());
        return view('admin.item.episode.index', compact('pageTitle', 'item', 'episodes'));
    }

    public function addEpisode(Request $request, $id) {
        $request->validate([
            'title'   => 'required',
            'image'   => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'version' => 'required|in:0,1,2',
        ]);

        $item  = Item::findOrFail($id);
        $image = $this->uploadImage($request);

        $episode          = new Episode();
        $episode->item_id = $item->id;
        $episode->title   = $request->title;
        $episode->image   = $image;
        $episode->version = $item->version == Status::RENT_VERSION ? Status::RENT_VERSION : $request->version;
        $episode->save();

        $notify[] = ['success', 'Episode added successfully'];
        return to_route('admin.item.episode.addVideo', $episode->id)->withNotify($notify);
    }

    public function updateEpisode(Request $request, $id) {
        $request->validate([
            'title'   => 'required',
            'image'   => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'version' => 'required|in:0,1',
        ]);

        $episode = Episode::findOrFail($id);

        $item = $episode->item;
        if (!$item) {
            $notify[] = ['error', 'Item not found'];
            return back()->withNotify($notify);
        }

        $image = $this->uploadImage($request, $episode->image);

        $episode->title   = $request->title;
        $episode->image   = $image;
        $episode->version = $request->version;
        $episode->status  = $request->status ? 1 : 0;
        $episode->save();

        $notify[] = ['success', 'Episode updated successfully'];
        return back()->withNotify($notify);
    }

    private function uploadImage($request, $image = null) {
        if ($request->hasFile('image')) {
            $maxSize = $request->image->getSize() / 3145728;
            if ($maxSize > 3) {
                $notify[0] = ['error', 'Image size could not be greater than 3mb'];
                return back()->withInput($request->all())->withNotify($notify);
            }
            try {
                $date = date('Y') . '/' . date('m') . '/' . date('d');
                $image ? fileManager()->removeFile(getFilePath('episode') . $image) : '';
                $image = $date . '/' . fileUploader($request->image, getFilePath('episode') . $date);
            } catch (\Exception $e) {
                $notify[0] = ['error', 'Image could not be uploaded'];
                return back()->withInput($request->all())->withNotify($notify);
            }
        }

        return $image;
    }

    public function addEpisodeVideo($id) {
        $episode   = Episode::findOrFail($id);
        $pageTitle = "Add Video To : " . $episode->title;
        $video     = $episode->video;
        $prevUrl   = route('admin.item.episodes', $episode->item_id);
        $item      = $episode->item;
        $route     = route('admin.item.episode.upload', $episode->id);
        return view('admin.item.video.upload', compact('pageTitle', 'episode', 'video', 'prevUrl', 'item','route'));
    }

    public function storeEpisodeVideo(Request $request, $id) {
        $episode = Episode::findOrFail($id);
        ini_set('memory_limit', '-1');

        $video = $episode->video;
        if ($video) {
            $sevenTwentyLink  = 'nullable';
            $sevenTwentyVideo = 'nullable';
        } else {
            $sevenTwentyLink  = 'required_if:video_type,0';
            $sevenTwentyVideo = 'required_if:video_type,1';
        }

        ini_set('memory_limit', '-1');
        $validator = Validator::make($request->all(), [

            'video_type'    => 'required',
            'seven_twenty_link'          => "$sevenTwentyLink",
            'video'         => ["$sevenTwentyVideo", new FileTypeValidate(['mp4', 'mkv', '3gp'])],


        ], [

            'seven_twenty_link'          => 'Video file 720P link is required',
            'video'         => 'Video file 720P video is required',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }


        $sizeValidation = MultiVideoUploader::checkSizeValidation();
        if ($sizeValidation['error']) {
            return response()->json(['error' => $sizeValidation['message']]);
        }

        if (!$video) {
            $video             = new Video();
            $video->episode_id = $episode->id;
        }




        if ($request->hasFile('seven_twenty_video') || $request->seven_twenty_link) {
            $uploadSevenTwenty = MultiVideoUploader::multiQualityVideoUpload($video, 'seven_twenty');
            if ($uploadSevenTwenty['error']) {
                return response()->json(['error' => $sizeValidation['message']]);
            }
            $video->video_type_seven_twenty = @$request->video_type_seven_twenty;
            $video->seven_twenty_video      = @$uploadSevenTwenty['seven_twenty_video'];
            $video->server_seven_twenty     = @$uploadSevenTwenty['server'] ?? 0;
        }


        $video->save();
        return response()->json('success');
    }

    public function updateEpisodeVideo($id) {
        $episode = Episode::findOrFail($id);
        $video   = $episode->video;
        if (!$video) {
            $notify[] = ['error', 'Video not found'];
            return back()->withNotify($notify);
        }
        $general     = gs();
        $pageTitle   = "Update video of: " . $episode->title;
        $posterImage = getImage(getFilePath('episode') . $episode->image);

        $videoFile['three_sixty']     = getVideoFile($video, 'three_sixty');
        $videoFile['four_eighty']     = getVideoFile($video, 'four_eighty');
        $videoFile['seven_twenty']    = getVideoFile($video, 'seven_twenty');
        $videoFile['thousand_eighty'] = getVideoFile($video, 'thousand_eighty');

        $route   = route('admin.item.episode.upload', $episode->id);
        $prevUrl = route('admin.item.episodes', $episode->item_id);
        return view('admin.item.video.update', compact('pageTitle', 'video', 'posterImage', 'videoFile', 'prevUrl', 'route'));
    }

    public function subtitles($id, $videoId = 0) {
        $item = Episode::with('video')->findOrFail($id);
        if ($videoId == 0) {
            $videoId = $item->video->id;
        }
        $subtitles = Subtitle::where('episode_id', $id)->where('video_id', $videoId)->paginate(getPaginate());
        $pageTitle = 'Subtitles for - ' . $item->title;
        return view('admin.item.video.subtitles', compact('pageTitle', 'item', 'subtitles', 'videoId'));
    }

    public function subtitleStore(Request $request, $itemId, $videoId, $id = 0) {

        $validate = $id ? 'nullable' : 'required';
        $request->validate([
            'language' => 'required|string|max:40',
            'code'     => 'required|string|max:40',
            'file'     => [$validate, new FileTypeValidate(['vtt'])],
        ]);

        if ($id) {
            $subtitle     = Subtitle::findOrFail($id);
            $oldFile      = $subtitle->file;
            $notification = 'Subtitle updated successfully';
        } else {
            $subtitle     = new Subtitle();
            $notification = 'Subtitle created successfully';
            $oldFile      = null;
        }

        $subtitle->item_id  = $itemId;
        $subtitle->video_id = $videoId;
        $subtitle->language = $request->language;
        $subtitle->code     = $request->code;
        if ($request->file) {
            $subtitle->file = fileUploader($request->file, getFilePath('subtitle'), null, $oldFile);
        }
        $subtitle->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}

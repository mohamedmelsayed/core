<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Item;

use Illuminate\Http\Request;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class MediaMetadataController extends Controller
{
    /**
     * Retrieve metadata for a given audio or video file.
     * Accepts a file path on the server.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMediaMetadata(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string', // Path of the file on the server
        ]);

        $item=Item::where("id",$request->item_id)->first();
        if($item->meta!=null){
            return response()->json([
                'success' => true,
                'data' => json_decode($item->meta)
            ]);
        }
        if($item->is_audio){
            $filePath='assets/audio/'.$item->audio->content;
        }
        else{
            $filePath='assets/video/'.$item->video->seven_twenty_video;
            
        }

     
        // Verify if the file exists on the server
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        try {
            // Initialize FFProbe to analyze the file
            $ffprobe = FFProbe::create();
            $format = $ffprobe->format($filePath);
            $streams = $ffprobe->streams($filePath);

            // Basic metadata
            $duration = $format->get('duration');
            $codec = $streams->videos()->first()?->get('codec_name') ?? $streams->audios()->first()?->get('codec_name');
            $bitrate = $format->get('bit_rate');
            $size = filesize($filePath);

            // Generate waveform data for audio files
            $waveform = [];
            if ($streams->audios()->count()) {
                // Example for waveform generation (pseudo)
                $waveform = $this->generateWaveform($filePath,$duration);
            }
            $meta=[
                'duration' => $duration,
                'codec' => $codec,
                'bitrate' => $bitrate,
                'size' => $size,
                'waveform' => $waveform,
            ];
            $item->meta = json_encode($meta); // Store waveform data in a text column
            $item->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'duration' => $duration,
                    'codec' => $codec,
                    'bitrate' => $bitrate,
                    'size' => $size,
                    'waveform' => $waveform,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving metadata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate waveform data for the given audio file.
     *
     * @param string $filePath
     * @return array
     */
    private function generateWaveform($filePath, $duration)
    {
        // Set the number of points you want to return in the waveform (higher number = more detailed waveform)
        $numPoints = 1000; // You can adjust this based on how detailed you want the waveform to be
        $sampleRate = $duration / $numPoints;  // Sample rate for extracting points
    
        $waveform = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $time = $i * $sampleRate;
            $amplitude = $this->getAmplitudeAtTime($filePath, $time);
            $waveform[] = $amplitude;
        }
    
        return $waveform;
    }
    
    // This function uses FFmpeg to extract the amplitude at a specific time in the audio file
    private function getAmplitudeAtTime($filePath, $time)
    {
        // Use FFmpeg to get the raw audio data at the given time
        $cmd = "ffmpeg -i $filePath -filter_complex 'sine=frequency=1000' -t 0.1 -ss $time -f wav -";
        $output = shell_exec($cmd);
    
        // Process the output to get the amplitude (this step depends on your specific FFmpeg output)
        // You can use tools like `sox` or `ffmpeg` with proper audio filters to extract the amplitude.
        // For simplicity, here we're assuming an amplitude is being extracted from the raw audio stream.
    
        // Placeholder: simulate amplitude data, replace with your processing logic
        return rand(0, 100); // This is a placeholder, replace with actual data extraction logic
    }
}

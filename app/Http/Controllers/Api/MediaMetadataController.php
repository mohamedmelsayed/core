<?php

namespace App\Http\Controllers;

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
            'file_path' => 'required|string', // Path of the file on the server
        ]);

        $filePath = $request->input('file_path');

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
                $waveform = $this->generateWaveform($filePath);
            }

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
    private function generateWaveform(string $filePath): array
    {
        $waveformData = [];

        // Using FFmpeg to generate waveform points (example logic)
        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open($filePath);

        $frameRate = 100; // Extract points at intervals
        $audio->waveform($frameRate, function ($data) use (&$waveformData) {
            $waveformData[] = $data; // Simplified example
        });

        return $waveformData;
    }
}

<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessVideoHLS implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected \App\Models\UnitMedia $media) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->media->update(['processing_status' => 'processing']);

        try {
            $ffmpeg = \ProtoneMedia\LaravelFFMpeg\Support\FFMpeg::fromDisk('public')
                ->open($this->media->url);

            $hlsExport = $ffmpeg->exportForHLS()
                ->addFormat(new \FFMpeg\Format\Video\X264('aac', 'libx264'), function ($media) {
                    $media->scale(1280, 720);
                })
                ->addFormat(new \FFMpeg\Format\Video\X264('aac', 'libx264'), function ($media) {
                    $media->scale(640, 360);
                });

            $hlsPath = 'units/hls/' . $this->media->id . '/playlist.m3u8';

            $hlsExport->toDisk('public')->save($hlsPath);

            $this->media->update([
                'processed_url' => $hlsPath,
                'processing_status' => 'completed'
            ]);
        } catch (\Exception $e) {
            $this->media->update(['processing_status' => 'failed']);
            \Illuminate\Support\Facades\Log::error("HLS Conversion Failed for Media ID: {$this->media->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }
}

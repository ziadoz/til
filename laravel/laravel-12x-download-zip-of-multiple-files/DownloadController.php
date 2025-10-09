<?php

class DownloadController extends Controller
{
    public function download(Request $request): StreamedResponse
    {
        $filename = 'unique-filename.zip';
        $checksum = base64_encode(hash('xxh128', $filename, true));

        if (in_array($checksum, $request->getETags())) {
            abort(304);
        }        
        
        $tmpPath = stream_get_meta_data(tmpfile())['uri'];

        $zip = new ZipArchive;
        $zip->open($tmpPath, ZipArchive::CREATE);

        foreach ($files as $filePath) {
            $zip->addFromString(basename($filePath), Storage::disk('cloud')->get($filePath));
        }

        $zip->close();

        // Delete the temp file after the request has been served.
        defer(function () use ($tmpPath) {
            @unlink($tmpPath);
        });

        return response()->streamDownload(
            fn () => fpassthru(fopen($tmpPath, 'rb')),
            $filename,
            ['Content-Type' => 'application/zip', 'ETag' => $checksum],
        );
    } 
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    public function stream(Request $request, $filename)
    {
        $filePath = Storage::path("videos/{$filename}");

        if (!Storage::exists("videos/{$filename}")) {
            abort(404);
        }

        $fileSize = Storage::size("videos/{$filename}");
        
        return new StreamedResponse(function() use ($filePath, $fileSize, $request) {
            $start = 0;
            $end = $fileSize - 1;
            
            if ($request->headers->has('Range')) {
                $range = $request->headers->get('Range');
                [$unit, $range] = explode('=', $range);
                
                if ($unit === 'bytes') {
                    [$start, $end] = explode('-', $range);
                    $start = intval($start);
                    $end = $end ? intval($end) : $fileSize - 1;
                    $end = min($end, $fileSize - 1);
                    
                    header('HTTP/1.1 206 Partial Content');
                    header("Content-Range: bytes {$start}-{$end}/{$fileSize}");
                }
            }
            
            header('Content-Type: video/mp4');
            header('Content-Length: ' . ($end - $start + 1));
            header('Accept-Ranges: bytes');
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            
            $file = fopen($filePath, 'rb');
            fseek($file, $start);
            $buffer = 8192;
            $currentPosition = $start;
            
            while (!feof($file) && $currentPosition <= $end) {
                $bytesToRead = min($buffer, $end - $currentPosition + 1);
                echo fread($file, $bytesToRead);
                $currentPosition += $bytesToRead;
                flush();
            }
            
            fclose($file);
        });
    }
}
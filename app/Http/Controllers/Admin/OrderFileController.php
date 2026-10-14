<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Preuzimanje fajlova iz upita — samo za administratore. */
class OrderFileController extends Controller
{
    public function __invoke(OrderFile $file): StreamedResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);
        abort_unless(Storage::disk(OrderFile::DISK)->exists($file->path), 404);

        return Storage::disk(OrderFile::DISK)->download($file->path, $file->original_name, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

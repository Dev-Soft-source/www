<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $languages = Language::all();

        $data = ['languages' => $languages];
        return $this->successResponse($data, 'Languages get successfully');
    }

    public function flagIcon(Request $request, string $filename)
    {
        return $this->servePublicAsset($request, 'flag_icons', $filename);
    }

    public function userImage(Request $request, string $filename)
    {
        return $this->servePublicAsset($request, 'users_images', $filename);
    }

    public function studentCard(Request $request, string $filename)
    {
        return $this->servePublicAsset($request, 'student_cards', $filename);
    }

    public function homePageIcon(Request $request, string $filename)
    {
        return $this->servePublicAsset($request, 'home_page_icons', $filename);
    }

    public function driverLiscense(Request $request, string $filename)
    {
        return $this->servePublicAsset($request, 'driver_liscenses', $filename);
    }

    public function carImage(Request $request, string $filename)
    {
        return $this->servePublicAsset($request, 'car_images', $filename);
    }

    private function servePublicAsset(Request $request, string $directory, string $filename)
    {
        $safeName = basename(urldecode($filename));
        $path = public_path(trim($directory, '/') . '/' . $safeName);

        if (!File::exists($path) || !File::isFile($path)) {
            abort(404);
        }

        $mimeType = File::mimeType($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Access-Control-Allow-Origin' => $request->headers->get('Origin', '*'),
            'Vary' => 'Origin',
        ]);
    }
}

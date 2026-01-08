<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\StatusResponser;

class LanguageController extends Controller
{
    use StatusResponser;

    public function index(){
        $languages = Language::all();

        $data = ['languages' => $languages];
        return $this->successResponse($data, 'Languages get successfully');
    }
}

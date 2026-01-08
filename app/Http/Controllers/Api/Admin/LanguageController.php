<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LanguageResource;
use App\Models\Language;
use App\Traits\StatusResponser;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    use StatusResponser;
    use FileUploadTrait;

    public function index()
    {
        $languages = Language::query();

        $languages = $this->whereClause($languages);
        $languages = $this->loadRelations($languages);
        $languages = $this->sortingAndLimit($languages);

        return $this->apiSuccessResponse(LanguageResource::collection($languages), 'Data Get Successfully!');
    }

    public function show(Language $language)
    {
        return $this->apiSuccessResponse(new LanguageResource($language), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {        
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'abbreviation' => ['required', 'string', 'max:10'],
            'native_name' => ['required', 'string', 'max:50'],
            'is_default' => ['required', 'boolean'],
            'direction' => ['required', 'in:ltr,rtl'],
            'flag_icon' => ['required'],
        ];
        $this->validate($request, $rules, $this->customMessages);

        $language = Language::create([
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'native_name' => $request->native_name,
            'is_default' => $request->is_default,
            'direction' => $request->direction,
            'flag_icon' => $request->flag_icon,
        ]);

        if ($language) {
            if ($request->is_default == true) {
                $this->removeDefaultLanguage($language);
            }
            if (!file_exists(lang_path($request->abbreviation))) {
                File::makeDirectory(lang_path($request->abbreviation));
            }
            $abbreviation = !isset($lang->abbreviation) ? 'en' : $lang->abbreviation;
            foreach (glob(lang_path($abbreviation) . '/*.*') as $file) {
                if (app()->isProduction()) {
                    $file_to_go = str_replace('/' . $abbreviation . '/', '/' . $request->abbreviation . '/', $file);
                } else {
                    $file_to_go = str_replace($abbreviation . '/', $request->abbreviation . '/', $file);
                }
                if (!file_exists($file_to_go)) {
                    copy($file, $file_to_go);
                }
            }
            return $this->apiSuccessResponse(new LanguageResource($language), 'Language has been added successfully.');
        }
        return $this->errorResponse();
    }


    public function update(Request $request, Language $language)
    {
        $rules = [
            'id' => ['required', 'exists:App\Models\Language,id'],
            'name' => ['required', 'string', 'max:50'],
            'abbreviation' => ['required', 'string', 'max:10'],
            'native_name' => ['required', 'string', 'max:50'],
            'is_default' => ['required', 'boolean'],
            'direction' => ['required', 'in:ltr,rtl'],
            'flag_icon' => ['required'],
        ];
        $this->validate($request, $rules, $this->customMessages);
        
        $result = Language::whereId($request->id)->update([
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'native_name' => $request->native_name,
            'is_default' => $request->is_default,
            'direction' => $request->direction,
            'flag_icon' => $request->flag_icon,
        ]);

        if ($result) {
            if ($request->is_default == true) {
                $this->removeDefaultLanguage($language);
            }
            if (!file_exists(lang_path($request->abbreviation))) {
                File::makeDirectory(lang_path($request->abbreviation));
            }
            $abbreviation = !isset($lang->abbreviation) ? 'en' : $lang->abbreviation;
            foreach (glob(lang_path($abbreviation) . '/*.*') as $file) {
                if (app()->isProduction()) {
                    $file_to_go = str_replace('/' . $abbreviation . '/', '/' . $request->abbreviation . '/', $file);
                } else {
                    $file_to_go = str_replace($abbreviation . '/', $request->abbreviation . '/', $file);
                }
                if (!file_exists($file_to_go)) {
                    copy($file, $file_to_go);
                }
            }
            return $this->apiSuccessResponse(new LanguageResource($language), 'Language has been updated successfully.');
        }
        return $this->errorResponse();
    }


    public function destroy(Language $language)
    {
        if ($language->delete()) {
            return $this->apiSuccessResponse(new LanguageResource($language), 'Language has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    private $customMessages = [
        'in' => 'The field must be either "ltr" or "rtl"',
    ];

    protected function removeDefaultLanguage($language)
    {
        Language::where('id', '!=', $language->id)->update([
            'is_default' => 0
        ]);
    }

    protected function loadRelations($languages)
    {
        return $languages;
    }

    protected function sortingAndLimit($languages)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $languages->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'name', 'abbreviation', 'native_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $languages = $languages->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $languages->paginate($limit);
    }

    protected function whereClause($languages)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $languages = $languages->where('name', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('abbreviation', 'LIKE', '%' . $_GET['searchParam'] . '%')->orWhere('native_name', 'LIKE', '%' . $_GET['searchParam'] . '%');
        }
        return $languages;
    }
}

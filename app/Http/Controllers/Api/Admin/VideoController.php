<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\VideoResource;
use Illuminate\Support\Str;
use App\Models\Video;
use App\Models\VideoDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $videos = Video::query()->orderBy('page', 'asc');

        $videos = $this->whereClause($videos);
        $videos = $this->loadRelations($videos);
        $videos = $this->sortingAndLimit($videos);

        return $this->apiSuccessResponse(VideoResource::collection($videos), 'Data Get Successfully!');
    }


    public function show(Video $Video)
    {
        if (isset($_GET['withVideoDetail']) && $_GET['withVideoDetail'] == '1') {
            $Video = $Video->loadMissing('videoDetail');
        }

        return $this->apiSuccessResponse(new VideoResource($Video), 'Data Get Successfully!');
    }


    public function store(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['link.link_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['link.link_' . $language->id . '.required' => 'Link in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['page' => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['page' . '.required' => 'Page is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );
        $Video = Video::create([
            'page' => $request->page,
        ]);


        if ($Video) {
            foreach ($languages as $language) {
                VideoDetail::create([
                    'video_id' => $Video->id,
                    'language_id' => $language->id,
                    'link' => $request['link']['link_' . $language->id],
                ]);
            }
            return $this->apiSuccessResponse(new VideoResource($Video), 'Video has been added successfully.');
        }
        return $this->errorResponse();
    }


    public function update(Request $request, Video $Video)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['link.link_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['link.link_' . $language->id . '.required' => 'Link in ' . $language->name . ' is required.']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );


        $Video->update([
            'page' => $request->page,
        ]);
        foreach ($languages as $language) {
            $VideoDetail = VideoDetail::whereLanguageId($language->id)->whereVideoId($Video->id)->exists();
            if ($VideoDetail) {
                VideoDetail::whereLanguageId($language->id)->whereVideoId($Video->id)->update([
                    'video_id' => $Video->id,
                    'language_id' => $language->id,
                    'link' => $request['link']['link_' . $language->id],
                ]);
            } else {
                VideoDetail::create([
                    'video_id' => $Video->id,
                    'language_id' => $language->id,
                    'link' => $request['link']['link_' . $language->id],
                ]);
            }
        }

        if ($Video) {
            return $this->apiSuccessResponse(new VideoResource($Video), 'Video has been updated successfully.');
        }
        return $this->errorResponse();
    }


    public function destroy(Video $Video)
    {
        if ($Video->videoDetail()->delete() && $Video->delete()) {
            return $this->apiSuccessResponse(new VideoResource($Video), 'Video has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    protected function loadRelations($videos)
    {
        $defaultLang = getDefaultLanguage();
        $videos = $videos->with(['videoDetail' => function ($q) use ($defaultLang) {
            $q->where('language_id', $defaultLang->id);
        }]);
        if (isset($_GET['withVideoDetail']) && $_GET['withVideoDetail'] == '1') {
            $videos = $videos->with('videoDetail');
        }
        return $videos;
    }

    protected function sortingAndLimit($videos)
    {
        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $videos = $videos->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $videos->paginate($limit);
    }

    protected function whereClause($videos)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $videos = $videos->whereHas('videoDetail', function ($q) {
                $q->where('id', 'LIKE', '%' . $_GET['searchParam'] . '%');
            });
        }
        return $videos;
    }
}

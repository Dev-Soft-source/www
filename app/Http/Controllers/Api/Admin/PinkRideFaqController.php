<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PinkRideFaqResource;
use App\Models\PinkRideFaq;
use App\Models\PinkRideFaqDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class PinkRideFaqController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $pinkRideFaqs = PinkRideFaq::query()->orderBy('id', 'asc');

        $pinkRideFaqs = $this->whereClause($pinkRideFaqs);
        $pinkRideFaqs = $this->loadRelations($pinkRideFaqs);
        $pinkRideFaqs = $this->sortingAndLimit($pinkRideFaqs);

        return $this->apiSuccessResponse(PinkRideFaqResource::collection($pinkRideFaqs), 'Data Get Successfully!');
    }

    public function show($id)
    {
        $pinkRideFaq = PinkRideFaq::whereId($id)->with('pinkRideFaqDetail')->first();
        return $this->apiSuccessResponse(new PinkRideFaqResource($pinkRideFaq), 'Data Get Successfully!');
    }
    
    public function store(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['question.question_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['question.question_' . $language->id . '.required' => 'Question in ' . $language->name . ' is required']);
                $validationRule = array_merge($validationRule, ['answer.answer_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['answer.answer_' . $language->id . '.required' => 'Answer in ' . $language->name . ' is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );

        $pinkRideFaq = PinkRideFaq::create();

        foreach ($languages as $language) {
            $pinkRideFaqDetail = PinkRideFaqDetail::whereLanguageId($language->id)->wherePinkRideFaqId($pinkRideFaq['id'])->first();

            $pinkRideFaqDetailData = [
                'pink_ride_faq_id' => $pinkRideFaq['id'],
                'language_id' => $language->id,
                'question' => $request['question']['question_' . $language->id] ?? null,
                'answer' => $request['answer']['answer_' . $language->id] ?? null,
            ];

            if ($pinkRideFaqDetail) {
                $pinkRideFaqDetail->update($pinkRideFaqDetailData);
            } else {
                PinkRideFaqDetail::create($pinkRideFaqDetailData);
            }
        }

        if ($pinkRideFaq) {
            return $this->apiSuccessResponse(new PinkRideFaqResource($pinkRideFaq), 'Pink-ride FAQ has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, PinkRideFaq $pinkRideFaq)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['question.question_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['question.question_' . $language->id . '.required' => 'Question in ' . $language->name . ' is required']);
                $validationRule = array_merge($validationRule, ['answer.answer_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['answer.answer_' . $language->id . '.required' => 'Answer in ' . $language->name . ' is required']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );

        $result = PinkRideFaq::whereId($request->id)->first();

        foreach ($languages as $language) {
            $pinkRideFaqDetail = PinkRideFaqDetail::whereLanguageId($language->id)->wherePinkRideFaqId($result['id'])->first();

            $pinkRideFaqDetailData = [
                'pink_ride_faq_id' => $result['id'],
                'language_id' => $language->id,
                'question' => $request['question']['question_' . $language->id] ?? null,
                'answer' => $request['answer']['answer_' . $language->id] ?? null,
            ];

            if ($pinkRideFaqDetail) {
                $pinkRideFaqDetail->update($pinkRideFaqDetailData);
            } else {
                PinkRideFaqDetail::create($pinkRideFaqDetailData);
            }
        }

        if ($result) {
            return $this->apiSuccessResponse(new PinkRideFaqResource($pinkRideFaq), 'Pink-ride FAQ has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy($id)
    {
        $pinkRideFaq = PinkRideFaq::whereId($id)->delete();
        if ($pinkRideFaq) {
            $pinkRideFaqs = PinkRideFaq::query()->orderBy('id', 'asc')->get();
            return $this->apiSuccessResponse(PinkRideFaqResource::collection($pinkRideFaqs), 'Pink-ride FAQ has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    protected function loadRelations($pinkRideFaqs)
    {

        $defaultLang = getDefaultLanguage();
        $pinkRideFaqs = $pinkRideFaqs->with(['pinkRideFaqDetail' => function ($q) use ($defaultLang) {
            $q->where('language_id', $defaultLang->id);
        }]);
        if (isset($_GET['withPinkRideFaqDetail']) && $_GET['withPinkRideFaqDetail'] == '1') {
            $pinkRideFaqs = $pinkRideFaqs->with('pinkRideFaqDetail');
        }
        return $pinkRideFaqs;
    }

    protected function sortingAndLimit($pinkRideFaqs)
    {


        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'reward_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            if ($_GET['sortBy'] == 'question') {
                $pinkRideFaqs = $pinkRideFaqs->orderBy(function ($q) {
                    $q->select('question')
                      ->from('pink_ride_faq_details')
                      ->whereColumn('pink_ride_faq_details.pink_ride_faq_id', 'pink_ride_faqs.id')
                      ->limit(1);
                }, $_GET['sortType']);
            } else {
                $pinkRideFaqs = $pinkRideFaqs->OrderBy($_GET['sortBy'], $_GET['sortType']);
            }
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $pinkRideFaqs->paginate($limit);
    }

    protected function whereClause($pinkRideFaqs)
    {

        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $pinkRideFaqs = $pinkRideFaqs->whereHas('pinkRideFaqDetail', function ($q) {
                $q->where('question', 'LIKE', '%' . $_GET['searchParam'] . '%');
            });
        }
        return $pinkRideFaqs;
    }
}

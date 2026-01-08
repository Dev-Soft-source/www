<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ExtraCareFaqResource;
use App\Models\ExtraCareFaq;
use App\Models\ExtraCareFaqDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class ExtraCareFaqController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $extraCareFaqs = ExtraCareFaq::query()->orderBy('id', 'asc');

        $extraCareFaqs = $this->whereClause($extraCareFaqs);
        $extraCareFaqs = $this->loadRelations($extraCareFaqs);
        $extraCareFaqs = $this->sortingAndLimit($extraCareFaqs);

        return $this->apiSuccessResponse(ExtraCareFaqResource::collection($extraCareFaqs), 'Data Get Successfully!');
    }

    public function show($id)
    {
        $extraCareFaq = ExtraCareFaq::whereId($id)->with('extraCareFaqDetail')->first();
        return $this->apiSuccessResponse(new ExtraCareFaqResource($extraCareFaq), 'Data Get Successfully!');
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

        $extraCareFaq = ExtraCareFaq::create();

        foreach ($languages as $language) {
            $extraCareFaqDetail = ExtraCareFaqDetail::whereLanguageId($language->id)->whereExtraCareFaqId($extraCareFaq['id'])->first();

            $extraCareFaqDetailData = [
                'extra_care_faq_id' => $extraCareFaq['id'],
                'language_id' => $language->id,
                'question' => $request['question']['question_' . $language->id] ?? null,
                'answer' => $request['answer']['answer_' . $language->id] ?? null,
            ];

            if ($extraCareFaqDetail) {
                $extraCareFaqDetail->update($extraCareFaqDetailData);
            } else {
                ExtraCareFaqDetail::create($extraCareFaqDetailData);
            }
        }

        if ($extraCareFaq) {
            return $this->apiSuccessResponse(new ExtraCareFaqResource($extraCareFaq), 'Extra-care FAQ has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, ExtraCareFaq $extraCareFaq)
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

        $result = ExtraCareFaq::whereId($request->id)->first();

        foreach ($languages as $language) {
            $extraCareFaqDetail = ExtraCareFaqDetail::whereLanguageId($language->id)->whereExtraCareFaqId($result['id'])->first();

            $extraCareFaqDetailData = [
                'extra_care_faq_id' => $result['id'],
                'language_id' => $language->id,
                'question' => $request['question']['question_' . $language->id] ?? null,
                'answer' => $request['answer']['answer_' . $language->id] ?? null,
            ];

            if ($extraCareFaqDetail) {
                $extraCareFaqDetail->update($extraCareFaqDetailData);
            } else {
                ExtraCareFaqDetail::create($extraCareFaqDetailData);
            }
        }

        if ($result) {
            return $this->apiSuccessResponse(new ExtraCareFaqResource($extraCareFaq), 'Extra-care FAQ has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy($id)
    {
        $extraCareFaq = ExtraCareFaq::whereId($id)->delete();
        if ($extraCareFaq) {
            $extraCareFaqs = ExtraCareFaq::query()->orderBy('id', 'asc')->get();
            return $this->apiSuccessResponse(ExtraCareFaqResource::collection($extraCareFaqs), 'Extra-care FAQ has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    protected function loadRelations($extraCareFaqs)
    {

        $defaultLang = getDefaultLanguage();
        $extraCareFaqs = $extraCareFaqs->with(['extraCareFaqDetail' => function ($q) use ($defaultLang) {
            $q->where('language_id', $defaultLang->id);
        }]);
        if (isset($_GET['withExtraCareFaqDetail']) && $_GET['withExtraCareFaqDetail'] == '1') {
            $extraCareFaqs = $extraCareFaqs->with('extraCareFaqDetail');
        }
        return $extraCareFaqs;
    }

    protected function sortingAndLimit($extraCareFaqs)
    {


        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id', 'reward_name'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            if ($_GET['sortBy'] == 'question') {
                $extraCareFaqs = $extraCareFaqs->orderBy(function ($q) {
                    $q->select('question')
                      ->from('extra_care_faq_details')
                      ->whereColumn('extra_care_faq_details.extra_care_faq_id', 'extra_care_faqs.id')
                      ->limit(1);
                }, $_GET['sortType']);
            } else {
                $extraCareFaqs = $extraCareFaqs->OrderBy($_GET['sortBy'], $_GET['sortType']);
            }
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $extraCareFaqs->paginate($limit);
    }

    protected function whereClause($extraCareFaqs)
    {

        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $extraCareFaqs = $extraCareFaqs->whereHas('extraCareFaqDetail', function ($q) {
                $q->where('question', 'LIKE', '%' . $_GET['searchParam'] . '%');
            });
        }
        return $extraCareFaqs;
    }
}

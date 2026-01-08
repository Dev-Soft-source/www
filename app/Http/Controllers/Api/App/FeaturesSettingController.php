<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class FeaturesSettingController extends Controller
{
    use StatusResponser;

    public function postRideFeaturesOptions(Request $request){
        $featuresLabels = [];
        if ($request->lang && $request->lang != 0) {
            $featuresOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.features_option1', 'post_ride_page_setting_detail.features_option2', 'post_ride_page_setting_detail.features_option3', 'post_ride_page_setting_detail.features_option4', 'post_ride_page_setting_detail.features_option5', 'post_ride_page_setting_detail.features_option6', 'post_ride_page_setting_detail.features_option7', 'post_ride_page_setting_detail.features_option8', 'post_ride_page_setting_detail.features_option9', 'post_ride_page_setting_detail.features_option10', 'post_ride_page_setting_detail.features_option11', 'post_ride_page_setting_detail.features_option12', 'post_ride_page_setting_detail.features_option13', 'post_ride_page_setting_detail.features_option14', 'post_ride_page_setting_detail.features_option15', 'post_ride_page_setting_detail.features_option16')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();

            if ($featuresOptions->features_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $featuresLabels[] = $name1 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option1 is null
            }

            if ($featuresOptions->features_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $featuresLabels[] = $name2 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option2 is null
            }

            if ($featuresOptions->features_option3) {
                $name3 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option3)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name3) {
                    $featuresLabels[] = $name3 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option3 is null
            }

            if ($featuresOptions->features_option4) {
                $name4 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option4)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name4) {
                    $featuresLabels[] = $name4 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option4 is null
            }

            if ($featuresOptions->features_option5) {
                $name5 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option5)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name5) {
                    $featuresLabels[] = $name5 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option5 is null
            }

            if ($featuresOptions->features_option6) {
                $name6 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option6)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name6) {
                    $featuresLabels[] = $name6 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option6 is null
            }

            if ($featuresOptions->features_option7) {
                $name7 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option7)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name7) {
                    $featuresLabels[] = $name7 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option7 is null
            }

            if ($featuresOptions->features_option8) {
                $name8 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option8)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name8) {
                    $featuresLabels[] = $name8 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option8 is null
            }

            if ($featuresOptions->features_option9) {
                $name9 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option9)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name9) {
                    $featuresLabels[] = $name9 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option9 is null
            }

            if ($featuresOptions->features_option10) {
                $name10 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option10)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name10) {
                    $featuresLabels[] = $name10 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option10 is null
            }

            if ($featuresOptions->features_option11) {
                $name11 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option11)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name11) {
                    $featuresLabels[] = $name11 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option11 is null
            }

            if ($featuresOptions->features_option12) {
                $name12 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option12)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name12) {
                    $featuresLabels[] = $name12 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option12 is null
            }

            if ($featuresOptions->features_option13) {
                $name13 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option13)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name13) {
                    $featuresLabels[] = $name13 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option13 is null
            }

            if ($featuresOptions->features_option14) {
                $name14 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option14)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name14) {
                    $featuresLabels[] = $name14 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option14 is null
            }

            if ($featuresOptions->features_option15) {
                $name15 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option15)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name15) {
                    $featuresLabels[] = $name15 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option15 is null
            }

            if ($featuresOptions->features_option16) {
                $name16 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option16)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name16) {
                    $featuresLabels[] = $name16 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if features_option16 is null
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $featuresOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.features_option1', 'post_ride_page_setting_detail.features_option2', 'post_ride_page_setting_detail.features_option3', 'post_ride_page_setting_detail.features_option4', 'post_ride_page_setting_detail.features_option5', 'post_ride_page_setting_detail.features_option6', 'post_ride_page_setting_detail.features_option7', 'post_ride_page_setting_detail.features_option8', 'post_ride_page_setting_detail.features_option9', 'post_ride_page_setting_detail.features_option10', 'post_ride_page_setting_detail.features_option11', 'post_ride_page_setting_detail.features_option12', 'post_ride_page_setting_detail.features_option13', 'post_ride_page_setting_detail.features_option14', 'post_ride_page_setting_detail.features_option15', 'post_ride_page_setting_detail.features_option16')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($featuresOptions->features_option1) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $featuresLabels[] = $name1 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option1 is null
                }
        
                if ($featuresOptions->features_option2) {
                    $name2 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name2) {
                        $featuresLabels[] = $name2 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option2 is null
                }
        
                if ($featuresOptions->features_option3) {
                    $name3 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name3) {
                        $featuresLabels[] = $name3 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option3 is null
                }
        
                if ($featuresOptions->features_option4) {
                    $name4 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name4) {
                        $featuresLabels[] = $name4 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option4 is null
                }
        
                if ($featuresOptions->features_option5) {
                    $name5 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name5) {
                        $featuresLabels[] = $name5 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option5 is null
                }
        
                if ($featuresOptions->features_option6) {
                    $name6 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name6) {
                        $featuresLabels[] = $name6 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option6 is null
                }
        
                if ($featuresOptions->features_option7) {
                    $name7 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name7) {
                        $featuresLabels[] = $name7 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option7 is null
                }
        
                if ($featuresOptions->features_option8) {
                    $name8 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name8) {
                        $featuresLabels[] = $name8 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option8 is null
                }
        
                if ($featuresOptions->features_option9) {
                    $name9 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name9) {
                        $featuresLabels[] = $name9 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option9 is null
                }
        
                if ($featuresOptions->features_option10) {
                    $name10 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name10) {
                        $featuresLabels[] = $name10 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option10 is null
                }
        
                if ($featuresOptions->features_option11) {
                    $name11 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name11) {
                        $featuresLabels[] = $name11 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option11 is null
                }
        
                if ($featuresOptions->features_option12) {
                    $name12 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name12) {
                        $featuresLabels[] = $name12 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option12 is null
                }
        
                if ($featuresOptions->features_option13) {
                    $name13 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name13) {
                        $featuresLabels[] = $name13 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option13 is null
                }
        
                if ($featuresOptions->features_option14) {
                    $name14 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name14) {
                        $featuresLabels[] = $name14 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option14 is null
                }
        
                if ($featuresOptions->features_option15) {
                    $name15 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name15) {
                        $featuresLabels[] = $name15 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option15 is null
                }
        
                if ($featuresOptions->features_option16) {
                    $name16 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name16) {
                        $featuresLabels[] = $name16 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if features_option16 is null
                }
            }
        }

        if ($featuresOptions) {
            $data = ['featuresOptions' => array_values($featuresOptions->toArray()), 'featuresLabels' => $featuresLabels];
        } else {
            $data = ['featuresOptions' => $featuresOptions, 'featuresLabels' => $featuresLabels];
        }
        
        return $this->successResponse($data, 'Get features options successfully');
    }

    public function findRideFeaturesOptions(Request $request){
        $featuresLabels = [];
        $passengerRatingLabels = [];
        if ($request->lang && $request->lang != 0) {
            $featuresOptions = FindRidePageSettingDetail::select('find_ride_page_setting_detail.ride_features_option1', 'find_ride_page_setting_detail.ride_features_option2', 'find_ride_page_setting_detail.ride_features_option3', 'find_ride_page_setting_detail.ride_features_option4', 'find_ride_page_setting_detail.ride_features_option5', 'find_ride_page_setting_detail.ride_features_option6', 'find_ride_page_setting_detail.ride_features_option7', 'find_ride_page_setting_detail.ride_features_option8', 'find_ride_page_setting_detail.ride_features_option9', 'find_ride_page_setting_detail.ride_features_option10', 'find_ride_page_setting_detail.ride_features_option11', 'find_ride_page_setting_detail.ride_features_option12')
                ->join('languages', 'languages.id', '=', 'find_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();

            if ($featuresOptions->ride_features_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $featuresLabels[] = $name1 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option1 is null
            }

            if ($featuresOptions->ride_features_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $featuresLabels[] = $name2 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option2 is null
            }

            if ($featuresOptions->ride_features_option3) {
                $name3 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option3)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name3) {
                    $featuresLabels[] = $name3 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option3 is null
            }

            if ($featuresOptions->ride_features_option4) {
                $name4 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option4)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name4) {
                    $featuresLabels[] = $name4 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option4 is null
            }

            if ($featuresOptions->ride_features_option5) {
                $name5 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option5)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name5) {
                    $featuresLabels[] = $name5 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option5 is null
            }

            if ($featuresOptions->ride_features_option6) {
                $name6 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option6)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name6) {
                    $featuresLabels[] = $name6 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option6 is null
            }

            if ($featuresOptions->ride_features_option7) {
                $name7 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option7)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name7) {
                    $featuresLabels[] = $name7 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option7 is null
            }

            if ($featuresOptions->ride_features_option8) {
                $name8 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option8)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name8) {
                    $featuresLabels[] = $name8 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option8 is null
            }

            if ($featuresOptions->ride_features_option9) {
                $name9 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option9)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name9) {
                    $featuresLabels[] = $name9 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option9 is null
            }

            if ($featuresOptions->ride_features_option10) {
                $name10 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option10)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name10) {
                    $featuresLabels[] = $name10 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option10 is null
            }

            if ($featuresOptions->ride_features_option11) {
                $name11 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option11)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name11) {
                    $featuresLabels[] = $name11 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option11 is null
            }

            if ($featuresOptions->ride_features_option12) {
                $name12 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option12)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name12) {
                    $featuresLabels[] = $name12 ?? null;
                }
            } else {
                $featuresLabels[] = null; // Add `null` if ride_features_option12 is null
            }

            $passengerRatingOptions = FindRidePageSettingDetail::select('find_ride_page_setting_detail.ride_features_option13', 'find_ride_page_setting_detail.ride_features_option14', 'find_ride_page_setting_detail.ride_features_option15', 'find_ride_page_setting_detail.ride_features_option16')
                ->join('languages', 'languages.id', '=', 'find_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();

            if ($passengerRatingOptions->ride_features_option13) {
                $name13 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option13)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name13) {
                    $passengerRatingLabels[] = $name13 ?? null;
                }
            } else {
                $passengerRatingLabels[] = null; // Add `null` if ride_features_option13 is null
            }

            if ($passengerRatingOptions->ride_features_option14) {
                $name14 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option14)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name14) {
                    $passengerRatingLabels[] = $name14 ?? null;
                }
            } else {
                $passengerRatingLabels[] = null; // Add `null` if ride_features_option14 is null
            }

            if ($passengerRatingOptions->ride_features_option15) {
                $name15 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option15)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name15) {
                    $passengerRatingLabels[] = $name15 ?? null;
                }
            } else {
                $passengerRatingLabels[] = null; // Add `null` if ride_features_option15 is null
            }

            if ($passengerRatingOptions->ride_features_option16) {
                $name16 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option16)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name16) {
                    $passengerRatingLabels[] = $name16 ?? null;
                }
            } else {
                $passengerRatingLabels[] = null; // Add `null` if ride_features_option16 is null
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $featuresOptions = FindRidePageSettingDetail::select('find_ride_page_setting_detail.ride_features_option1', 'find_ride_page_setting_detail.ride_features_option2', 'find_ride_page_setting_detail.ride_features_option3', 'find_ride_page_setting_detail.ride_features_option4', 'find_ride_page_setting_detail.ride_features_option5', 'find_ride_page_setting_detail.ride_features_option6', 'find_ride_page_setting_detail.ride_features_option7', 'find_ride_page_setting_detail.ride_features_option8', 'find_ride_page_setting_detail.ride_features_option9', 'find_ride_page_setting_detail.ride_features_option10', 'find_ride_page_setting_detail.ride_features_option11', 'find_ride_page_setting_detail.ride_features_option12')
                    ->join('languages', 'languages.id', '=', 'find_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($featuresOptions->ride_features_option1) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $featuresLabels[] = $name1 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option1 is null
                }
        
                if ($featuresOptions->ride_features_option2) {
                    $name2 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name2) {
                        $featuresLabels[] = $name2 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option2 is null
                }
        
                if ($featuresOptions->ride_features_option3) {
                    $name3 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name3) {
                        $featuresLabels[] = $name3 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option3 is null
                }
        
                if ($featuresOptions->ride_features_option4) {
                    $name4 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name4) {
                        $featuresLabels[] = $name4 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option4 is null
                }
        
                if ($featuresOptions->ride_features_option5) {
                    $name5 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name5) {
                        $featuresLabels[] = $name5 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option5 is null
                }
        
                if ($featuresOptions->ride_features_option6) {
                    $name6 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option6)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name6) {
                        $featuresLabels[] = $name6 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option6 is null
                }
        
                if ($featuresOptions->ride_features_option7) {
                    $name7 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option7)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name7) {
                        $featuresLabels[] = $name7 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option7 is null
                }
        
                if ($featuresOptions->ride_features_option8) {
                    $name8 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option8)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name8) {
                        $featuresLabels[] = $name8 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option8 is null
                }
        
                if ($featuresOptions->ride_features_option9) {
                    $name9 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option9)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name9) {
                        $featuresLabels[] = $name9 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option9 is null
                }
        
                if ($featuresOptions->ride_features_option10) {
                    $name10 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option10)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name10) {
                        $featuresLabels[] = $name10 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option10 is null
                }
        
                if ($featuresOptions->ride_features_option11) {
                    $name11 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option11)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name11) {
                        $featuresLabels[] = $name11 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option11 is null
                }
        
                if ($featuresOptions->ride_features_option12) {
                    $name12 = FeaturesSettingDetail::whereFeaturesSettingId($featuresOptions->ride_features_option12)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name12) {
                        $featuresLabels[] = $name12 ?? null;
                    }
                } else {
                    $featuresLabels[] = null; // Add `null` if ride_features_option12 is null
                }

                $passengerRatingOptions = FindRidePageSettingDetail::select('find_ride_page_setting_detail.ride_features_option13', 'find_ride_page_setting_detail.ride_features_option14', 'find_ride_page_setting_detail.ride_features_option15', 'find_ride_page_setting_detail.ride_features_option16')
                    ->join('languages', 'languages.id', '=', 'find_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($passengerRatingOptions->ride_features_option13) {
                    $name13 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option13)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name13) {
                        $passengerRatingLabels[] = $name13 ?? null;
                    }
                } else {
                    $passengerRatingLabels[] = null; // Add `null` if ride_features_option13 is null
                }
        
                if ($passengerRatingOptions->ride_features_option14) {
                    $name14 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option14)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name14) {
                        $passengerRatingLabels[] = $name14 ?? null;
                    }
                } else {
                    $passengerRatingLabels[] = null; // Add `null` if ride_features_option14 is null
                }
        
                if ($passengerRatingOptions->ride_features_option15) {
                    $name15 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option15)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name15) {
                        $passengerRatingLabels[] = $name15 ?? null;
                    }
                } else {
                    $passengerRatingLabels[] = null; // Add `null` if ride_features_option15 is null
                }
        
                if ($passengerRatingOptions->ride_features_option16) {
                    $name16 = FeaturesSettingDetail::whereFeaturesSettingId($passengerRatingOptions->ride_features_option16)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name16) {
                        $passengerRatingLabels[] = $name16 ?? null;
                    }
                } else {
                    $passengerRatingLabels[] = null; // Add `null` if ride_features_option16 is null
                }
            }
        }

        if ($featuresOptions && $passengerRatingOptions) {
            $data = ['featuresOptions' => array_values($featuresOptions->toArray()), 'featuresLabels' => $featuresLabels, 'passengerRatingOptions' => array_values($passengerRatingOptions->toArray()), 'passengerRatingLabels' => $passengerRatingLabels];
        } else {
            $data = ['featuresOptions' => $featuresOptions, 'featuresLabels' => $featuresLabels, 'passengerRatingOptions' => $passengerRatingOptions, 'passengerRatingLabels' => $passengerRatingLabels];
        }
        
        return $this->successResponse($data, 'Get features options successfully');
    }
}

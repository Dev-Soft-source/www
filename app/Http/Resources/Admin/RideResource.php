<?php

namespace App\Http\Resources\Admin;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RideResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->resource === null) {
            return [];
        }

        // Fetch the default language ID
        $defaultLanguageId = Language::where('is_default', 1)->value('id');

        // Fetch feature names based on IDs and the default language ID
        $featureNames = [];
        if ($this->features && $defaultLanguageId) {
            $featureIds = explode('=', $this->features);
            $featureNames = FeaturesSetting::whereIn('id', $featureIds)
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                    $query->where('language_id', $defaultLanguageId);
                }])
                ->get()
                ->flatMap(function ($feature) {
                    return $feature->featuresSettingDetail->pluck('name');
                });
        }

        return [
            'id' => $this->id,
            'departure_city' => $this->rideDetail[0]->departure,
            'random_id' => $this->random_id,
            'destination_city' => $this->rideDetail[0]->destination,
            'driver_first_name' => $this->driver->first_name ?? null,
            'driver_last_name' => $this->driver->last_name ?? null,
            'driver_email' => $this->driver->email ?? null,
            'driver_phone' => $this->driver->phone ?? null,
            'date' => $this->date,
            'time' => $this->time,
            'price' => $this->rideDetail[0]->price,
            'payment_method' => $this->payment_method,
            'payment_method_name' => FeaturesSettingDetail::where('features_setting_id',$this->payment_method)->first()->name,
            'seats' => $this->seats,
            'status' => $this->status,
            'details' => $this->details,
            'model' => $this->model,
            'vehicle_type' => $this->vehicle_type,
            'year' => $this->year,
            'color' => $this->color,
            'license_no' => $this->license_no,
            'features' => $featureNames,
            'luggage' => FeaturesSetting::whereId($this->luggage)
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                    $query->where('language_id', $defaultLanguageId);
                }])
                ->first()?->featuresSettingDetail->first()?->name,
            'smoke' => FeaturesSetting::whereId($this->smoke)
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                    $query->where('language_id', $defaultLanguageId);
                }])
                ->first()?->featuresSettingDetail->first()?->name,
            'animal_friendly' => FeaturesSetting::whereId($this->animal_friendly)
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                    $query->where('language_id', $defaultLanguageId);
                }])
                ->first()?->featuresSettingDetail->first()?->name,
            'booking_method' => FeaturesSetting::whereId($this->booking_method)
                ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                    $query->where('language_id', $defaultLanguageId);
                }])
                ->first()?->featuresSettingDetail->first()?->name,
            'notes' => $this->notes,
            'suspand' => $this->suspand,
            'bookings' => BookingResource::collection($this->bookings ?? null),
            'post_ride_logs' => $this->postRideLogs->map(function ($log) use ($defaultLanguageId) {
                $logArray = $log->toArray();

                // Decode the changes to access the features
                if (isset($logArray['changes'])) {
                    $changes = json_decode($logArray['changes'], true);

                    if (isset($changes['features'])) {
                        $featureIds = explode('=', $changes['features']);
                        $featureNames = FeaturesSetting::whereIn('id', $featureIds)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->get()
                            ->flatMap(function ($feature) {
                                return $feature->featuresSettingDetail->pluck('name');
                            })
                            ->toArray();

                        // Replace feature IDs with their names
                        $changes['features'] = $featureNames;
                    }

                    if (isset($changes['luggage'])) {
                        $luggageId = $changes['luggage'];
                        $luggageName = FeaturesSetting::whereId($luggageId)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->first()?->featuresSettingDetail->first()?->name;

                        // Replace feature IDs with their names
                        $changes['luggage'] = $luggageName;
                    }

                    if (isset($changes['smoke'])) {
                        $smokeId = $changes['smoke'];
                        $smokeName = FeaturesSetting::whereId($smokeId)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->first()?->featuresSettingDetail->first()?->name;

                        // Replace feature IDs with their names
                        $changes['smoke'] = $smokeName;
                    }

                    if (isset($changes['animal_friendly'])) {
                        $animal_friendlyId = $changes['animal_friendly'];
                        $animal_friendlyName = FeaturesSetting::whereId($animal_friendlyId)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->first()?->featuresSettingDetail->first()?->name;

                        // Replace feature IDs with their names
                        $changes['animal_friendly'] = $animal_friendlyName;
                    }

                    if (isset($changes['booking_method'])) {
                        $booking_methodId = $changes['booking_method'];
                        $booking_methodName = FeaturesSetting::whereId($booking_methodId)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->first()?->featuresSettingDetail->first()?->name;

                        // Replace feature IDs with their names
                        $changes['booking_method'] = $booking_methodName;
                    }

                    if (isset($changes['booking_type'])) {
                        $booking_typeId = $changes['booking_type'];
                        $booking_typeName = FeaturesSetting::whereId($booking_typeId)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->first()?->featuresSettingDetail->first()?->name;

                        // Replace feature IDs with their names
                        $changes['booking_type'] = $booking_typeName;
                    }

                    if (isset($changes['payment_method'])) {
                        $payment_methodId = $changes['payment_method'];
                        $payment_methodName = FeaturesSetting::whereId($payment_methodId)
                            ->with(['featuresSettingDetail' => function ($query) use ($defaultLanguageId) {
                                $query->where('language_id', $defaultLanguageId);
                            }])
                            ->first()?->featuresSettingDetail->first()?->name;

                        // Replace feature IDs with their names
                        $changes['payment_method'] = $payment_methodName;
                    }

                    if (isset($changes['added_by'])) {
                        $added_byId = $changes['added_by'];
                        $added_by = User::whereId($added_byId)->first();
                        $added_byName = $added_by?->first_name . ' ' . $added_by?->last_name;

                        // Replace feature IDs with their names
                        $changes['added_by'] = $added_byName;
                    }

                    if (isset($changes['accept_more_luggage'])) {
                        $accept_more_luggageId = $changes['accept_more_luggage'];
                        if ($accept_more_luggageId == '0') {
                            $accept_more_luggageName = 'No';
                        } elseif ($accept_more_luggageId == '1') {
                            $accept_more_luggageName = 'Yes';
                        }

                        // Replace feature IDs with their names
                        $changes['accept_more_luggage'] = $accept_more_luggageName;
                    }

                    if (isset($changes['open_customized'])) {
                        $open_customizedId = $changes['open_customized'];
                        if ($open_customizedId == '0') {
                            $open_customizedName = 'No';
                        } elseif ($open_customizedId == '1') {
                            $open_customizedName = 'Yes';
                        }

                        // Replace feature IDs with their names
                        $changes['open_customized'] = $open_customizedName;
                    }

                    // Encode the changes back to JSON
                    $logArray['changes'] = json_encode($changes);
                }

                return $logArray;
            }),
        ];
    }
}
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CoffeeWalletResource;
use App\Models\CoffeeWallet;
use App\Traits\StatusResponser;
use Carbon\Carbon;

class CoffeeWalletController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{
            $coffeeWallets = CoffeeWallet::get();

            return $this->apiSuccessResponse(CoffeeWalletResource::collection($coffeeWallets), 'Data Get Successfully!');
        } catch (\Exception $e) {
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function totalAmount()
    {
        $getCoffeeCrBalance = CoffeeWallet::sum('cr_amount');
        $getCoffeeDrBalance = CoffeeWallet::sum('dr_amount');
        return response()->json(['data' => round(($getCoffeeDrBalance - $getCoffeeCrBalance), 2)]);
    }
}

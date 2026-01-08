<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ride;
use App\Models\Transaction;
use App\Models\BankDetail;
use App\Models\PhoneVerification;
use App\Models\ClaimReward;
use App\Models\TopUpBalance;
use App\Models\Payout;
use App\Models\CoffeeWallet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UpdateRandomIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Array of models and their corresponding tables
        $models = [
            'rides' => Ride::class,
            'transactions' => Transaction::class,
            'bank_details' => BankDetail::class,
            'phone_verifications' => PhoneVerification::class,
            'claim_rewards' => ClaimReward::class,
            'top_up_balances' => TopUpBalance::class,
            'payouts' => Payout::class,
            'coffee_wallets' => CoffeeWallet::class,
        ];

        foreach ($models as $table => $model) {
            $records = $model::all();

            foreach ($records as $record) {
                if (empty($record->random_id)) {

                    $randomStr = strtoupper(Str::random(4));
                    $randomId = $randomStr . '-' . $record->id;
                    $record->random_id = $randomId;
                    $record->save();
                }
            }
        }
    }
}

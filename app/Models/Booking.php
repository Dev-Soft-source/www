<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    public $timestamps  = false;
    protected $fillable = ['user_id', 'ride_id', 'seats', 'type', 'booked_on', 'status', 'booking_credit', 'fare', 'secured_cash', 'secured_cash_code', 'expires_at', 'removed_permanently', 'uuid', 'block_days', 'block_date_time', 'tax_amount', 'ride_detail_id', 'departure', 'destination', 'price', 'conversation_sid', 'participant_sid', 'phone_number'];


    public function passenger()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'posted_to');
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'booking_id');
    }

    public function transaction_no_coffee_sum()
    {
        return $this->hasMany(Transaction::class, 'booking_id', 'id')->select('booking_id', DB::raw('SUM(booking_fee) AS booking_transaction_sum'))->where('type', '1')->where('coffee_from_wall', '0')
            ->groupBy('booking_id');
    }

    public function booking_transaction_sum()
    {
        return $this->hasMany(Transaction::class, 'booking_id', 'id')
            ->select('booking_id', DB::raw('SUM(price) AS booking_transaction_sum'))->where('type', '1')
            ->groupBy('booking_id');
    }

    public function booking_cancel_transaction_sum()
    {
        return $this->hasMany(Transaction::class, 'booking_id', 'id')
            ->select('booking_id', DB::raw('SUM(price) AS booking_cancel_transaction_sum'))->where('type', '3')
            ->where(function ($query) {
                $query->whereNotNull('paypal_id')
                    ->orWhereNotNull('stripe_id');
            })
            ->groupBy('booking_id');
    }

    public function booking_credit_sum()
    {
        return $this->hasMany(Transaction::class, 'booking_id', 'id')
            ->select('booking_id', DB::raw('SUM(booking_fee) AS booking_credit_sum'))->where('type', '1')
            ->groupBy('booking_id');
    }

    public function booking_credit_cancel_sum()
    {
        return $this->hasMany(Transaction::class, 'booking_id', 'id')
            ->select('booking_id', DB::raw('SUM(booking_fee) AS booking_credit_cancel_sum'))->where('type', '3')
            ->where(function ($query) {
                $query->whereNotNull('paypal_id')
                    ->orWhereNotNull('stripe_id');
            })
            ->groupBy('booking_id');
    }

    public function seatDetail()
    {
        return $this->hasMany(SeatDetail::class, 'booking_id');
    }
}

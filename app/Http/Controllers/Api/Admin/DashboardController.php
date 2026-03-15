<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ride;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\StatusResponser;

class DashboardController extends Controller
{
    use StatusResponser;

    public function statistics()
    {
        try {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();
            $last30Days = Carbon::now()->subDays(30);

            // Total Users (handle soft deletes - SoftDeletes automatically excludes trashed)
            $totalUsers = User::where('deleted', '0')->count();
            $drivers = User::where('driver', '1')->where('deleted', '0')->count();
            $passengers = User::where('driver', '0')->where('student', '0')->where('deleted', '0')->count();
            $students = User::where('student', '1')->where('deleted', '0')->count();

            // Active Rides (rides with status = 1 or upcoming)
            $activeRides = Ride::where('status', '1')
                ->where(function($query) {
                    $query->where('date', '>=', Carbon::today()->toDateString())
                        ->orWhere(function($q) {
                            $q->where('date', Carbon::today()->toDateString())
                              ->where('time', '>=', Carbon::now()->format('H:i:s'));
                        });
                })
                ->count();

            // Today's Bookings
            $bookingsToday = Booking::whereDate('booked_on', $today)->count();
            $completedToday = Booking::where('status', '2')
                ->whereHas('ride', function($query) use ($today) {
                    $query->whereDate('completed_date', $today);
                })
                ->count();
            $cancelledToday = Booking::where('status', '3')
                ->whereDate('booked_on', $today)
                ->count();

            // Revenue
            $revenueToday = Transaction::where('type', '1')
                ->whereDate('on_date', $today)
                ->get()
                ->sum(function($transaction) {
                    return (float) ($transaction->booking_fee ?? 0);
                });
            $revenueThisWeek = Transaction::where('type', '1')
                ->whereBetween('on_date', [$thisWeek, Carbon::now()])
                ->get()
                ->sum(function($transaction) {
                    return (float) ($transaction->booking_fee ?? 0);
                });
            $revenueThisMonth = Transaction::where('type', '1')
                ->whereBetween('on_date', [$thisMonth, Carbon::now()])
                ->get()
                ->sum(function($transaction) {
                    return (float) ($transaction->booking_fee ?? 0);
                });

            // Ride Activity (Last 30 Days)
            $rideActivity = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                $postedRides = Ride::whereBetween('added_on', [$dayStart, $dayEnd])->count();
                $bookings = Booking::whereBetween('booked_on', [$dayStart, $dayEnd])->count();

                $rideActivity[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('M d'),
                    'posted_rides' => $postedRides,
                    'bookings' => $bookings
                ];
            }

            // Ride Type Distribution
            $rideTypes = [
                'Common Ride' => 0,
                'Pink Ride' => 0,
                'Extra Ride' => 0,
                'Proxima Ride' => 0,
                'Student Ride' => 0,
                'Folk Ride' => 0
            ];

            $allRides = Ride::where('status', '1')->get();
            foreach ($allRides as $ride) {
                $featuresArray = explode('=', $ride->features ?? '');
                if (in_array('1', $featuresArray)) {
                    $rideTypes['Pink Ride']++;
                } elseif (in_array('2', $featuresArray)) {
                    $rideTypes['Extra Ride']++;
                } elseif (in_array('3', $featuresArray)) {
                    $rideTypes['Proxima Ride']++;
                } elseif (in_array('4', $featuresArray)) {
                    $rideTypes['Folk Ride']++;
                } else {
                    $rideTypes['Common Ride']++;
                }
            }

            // Check if student ride (based on booking_type or other criteria)
            $studentRides = Ride::where('booking_type', 'student')->count();
            $rideTypes['Student Ride'] = $studentRides;

            // Booking Funnel
            try {
                $totalSearches = DB::table('recent_searches')
                    ->where(function($query) use ($last30Days) {
                        $query->whereBetween('created_at', [$last30Days, Carbon::now()])
                            ->orWhereBetween('updated_at', [$last30Days, Carbon::now()]);
                    })
                    ->count();
            } catch (\Exception $e) {
                $totalSearches = 0;
            }
            
            try {
                $viewedRides = DB::table('ride_views')
                    ->where(function($query) use ($last30Days) {
                        $query->whereBetween('created_at', [$last30Days, Carbon::now()])
                            ->orWhereBetween('updated_at', [$last30Days, Carbon::now()]);
                    })
                    ->count();
            } catch (\Exception $e) {
                $viewedRides = 0;
            }
            $bookingRequests = Booking::whereBetween('booked_on', [$last30Days, Carbon::now()])->count();
            $confirmed = Booking::whereIn('status', ['1', '2'])->whereBetween('booked_on', [$last30Days, Carbon::now()])->count();
            $completed = Booking::where('status', '2')->whereBetween('booked_on', [$last30Days, Carbon::now()])->count();

            // Popular Routes
            $popularRoutes = Booking::select(
                'departure',
                'destination',
                DB::raw('COUNT(*) as ride_count')
            )
                ->whereBetween('booked_on', [$last30Days, Carbon::now()])
                ->groupBy('departure', 'destination')
                ->orderBy('ride_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function($route) {
                    return [
                        'route' => $route->departure . ' → ' . $route->destination,
                        'count' => $route->ride_count
                    ];
                });

            // User Growth (Last 30 Days)
            $userGrowth = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                try {
                    $newDrivers = User::where('driver', '1')
                        ->whereBetween('created_at', [$dayStart, $dayEnd])
                        ->where('deleted', '0')
                        ->count();
                } catch (\Exception $e) {
                    $newDrivers = 0;
                }
                
                try {
                    $newPassengers = User::where('driver', '0')
                        ->where('student', '0')
                        ->whereBetween('created_at', [$dayStart, $dayEnd])
                        ->where('deleted', '0')
                        ->count();
                } catch (\Exception $e) {
                    $newPassengers = 0;
                }
                
                try {
                    $newStudents = User::where('student', '1')
                        ->whereBetween('created_at', [$dayStart, $dayEnd])
                        ->where('deleted', '0')
                        ->count();
                } catch (\Exception $e) {
                    $newStudents = 0;
                }

                $userGrowth[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('M d'),
                    'drivers' => $newDrivers,
                    'passengers' => $newPassengers,
                    'students' => $newStudents
                ];
            }

            // Safety & Trust Metrics
            $driverVerificationPending = User::where('driver', '2')->where('deleted', '0')->count();
            try {
                $reportedTrips = DB::table('reports')->count();
            } catch (\Exception $e) {
                $reportedTrips = 0;
            }
            $blockedUsers = User::where('suspand', '1')->where('deleted', '0')->count();
            $suspiciousActivity = 0; // Implement based on your logic

            // Driver Ratings (ratings given TO drivers via bookings)
            try {
                $driverRatings = Rating::select(DB::raw('AVG(average_rating) as avg_rating'))
                    ->join('bookings', 'ratings.posted_to', '=', 'bookings.id')
                    ->join('rides', 'bookings.ride_id', '=', 'rides.id')
                    ->join('users', 'rides.added_by', '=', 'users.id')
                    ->where('users.driver', '1')
                    ->first();
                $avgDriverRating = $driverRatings ? $driverRatings->avg_rating : 0;
            } catch (\Exception $e) {
                $avgDriverRating = 0;
            }

            // Passenger Ratings (ratings given TO passengers via bookings)
            try {
                $passengerRatings = Rating::select(DB::raw('AVG(average_rating) as avg_rating'))
                    ->join('bookings', 'ratings.posted_to', '=', 'bookings.id')
                    ->join('users', 'bookings.user_id', '=', 'users.id')
                    ->where('users.driver', '0')
                    ->first();
                $avgPassengerRating = $passengerRatings ? $passengerRatings->avg_rating : 0;
            } catch (\Exception $e) {
                $avgPassengerRating = 0;
            }

            // Top Drivers
            $topDrivers = User::where('driver', '1')
                ->where('deleted', '0')
                ->withCount('rides')
                ->get()
                ->map(function($driver) {
                    $trips = $driver->rides_count ?? 0;
                    // Get average rating for this driver from bookings
                    try {
                        $avgRating = Rating::join('bookings', 'ratings.posted_to', '=', 'bookings.id')
                            ->join('rides', 'bookings.ride_id', '=', 'rides.id')
                            ->where('rides.added_by', $driver->id)
                            ->avg('ratings.average_rating') ?? 0;
                    } catch (\Exception $e) {
                        $avgRating = 0;
                    }
                    
                    return [
                        'id' => $driver->id,
                        'name' => ($driver->first_name ?? '') . ' ' . ($driver->last_name ?? ''),
                        'trips' => $trips,
                        'rating' => round($avgRating, 1)
                    ];
                })
                ->sortByDesc('trips')
                ->take(10)
                ->values();

            // Recent Activity
            $recentActivity = [];
            
            try {
                // Recent rides
                $recentRides = Ride::with('driver')
                    ->orderBy('added_on', 'desc')
                    ->limit(5)
                    ->get();
                foreach ($recentRides as $ride) {
                    try {
                        $recentActivity[] = [
                            'type' => 'ride_posted',
                            'message' => 'Driver ' . ($ride->driver->first_name ?? 'Unknown') . ' posted ride ' . ($ride->departure ?? '') . ' → ' . ($ride->destination ?? ''),
                            'time' => $ride->added_on ? Carbon::parse($ride->added_on)->diffForHumans() : 'Recently'
                        ];
                    } catch (\Exception $e) {
                        // Skip invalid ride
                    }
                }
            } catch (\Exception $e) {
                // Handle error
            }

            try {
                // Recent bookings
                $recentBookings = Booking::with(['passenger', 'ride'])
                    ->orderBy('booked_on', 'desc')
                    ->limit(5)
                    ->get();
                foreach ($recentBookings as $booking) {
                    try {
                        $recentActivity[] = [
                            'type' => 'booking',
                            'message' => 'Passenger ' . ($booking->passenger->first_name ?? 'Unknown') . ' booked seat',
                            'time' => $booking->booked_on ? Carbon::parse($booking->booked_on)->diffForHumans() : 'Recently'
                        ];
                    } catch (\Exception $e) {
                        // Skip invalid booking
                    }
                }
            } catch (\Exception $e) {
                // Handle error
            }

            // Sort by time and limit
            usort($recentActivity, function($a, $b) {
                try {
                    $timeA = strtotime($a['time']);
                    $timeB = strtotime($b['time']);
                    return ($timeB ?: 0) - ($timeA ?: 0);
                } catch (\Exception $e) {
                    return 0;
                }
            });
            $recentActivity = array_slice($recentActivity, 0, 10);

            // Average commission per ride
            $totalTransactions = Transaction::where('type', '1')->count();
            $avgCommission = $totalTransactions > 0 ? ($revenueThisMonth / $totalTransactions) : 0;
            
            // Ensure all numeric values are properly formatted
            $revenueToday = (float) ($revenueToday ?? 0);
            $revenueThisWeek = (float) ($revenueThisWeek ?? 0);
            $revenueThisMonth = (float) ($revenueThisMonth ?? 0);

            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => [
                    'kpi' => [
                        'total_users' => $totalUsers,
                        'drivers' => $drivers,
                        'passengers' => $passengers,
                        'students' => $students,
                        'active_rides' => $activeRides,
                        'bookings_today' => $bookingsToday,
                        'completed_today' => $completedToday,
                        'cancelled_today' => $cancelledToday,
                        'revenue_today' => round($revenueToday, 2),
                        'revenue_this_week' => round($revenueThisWeek, 2),
                        'revenue_this_month' => round($revenueThisMonth, 2),
                        'avg_commission' => round($avgCommission, 2)
                    ],
                    'ride_activity' => $rideActivity,
                    'ride_type_distribution' => $rideTypes,
                    'booking_funnel' => [
                        'searches' => $totalSearches,
                        'views' => $viewedRides,
                        'requests' => $bookingRequests,
                        'confirmed' => $confirmed,
                        'completed' => $completed
                    ],
                    'popular_routes' => $popularRoutes,
                    'user_growth' => $userGrowth,
                    'safety_metrics' => [
                        'driver_verification_pending' => $driverVerificationPending,
                        'reported_trips' => $reportedTrips,
                        'blocked_users' => $blockedUsers,
                        'suspicious_activity' => $suspiciousActivity,
                        'avg_driver_rating' => round($avgDriverRating, 1),
                        'avg_passenger_rating' => round($avgPassengerRating, 1)
                    ],
                    'top_drivers' => $topDrivers,
                    'recent_activity' => $recentActivity
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Dashboard statistics error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->apiErrorResponse($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 500);
        }
    }
}

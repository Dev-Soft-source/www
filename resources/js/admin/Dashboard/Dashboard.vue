<template>
    <AppLayout>
        <div class="p-4 space-y-6">
            <!-- Page Header -->
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-primary">Dashboard</h3>
                <p class="text-gray-600">Welcome back! Here's what's happening on your platform.</p>
            </div>

            <!-- KPI Cards Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.total_users || 0) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Drivers</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.drivers || 0) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Passengers</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.passengers || 0) }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Students</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.students || 0) }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Active Rides</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.active_rides || 0) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Cards Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Bookings Today</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.bookings_today || 0) }}</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-teal-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Completed Today</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.completed_today || 0) }}</p>
                        </div>
                        <div class="bg-teal-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-6m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Cancelled</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.kpi?.cancelled_today || 0) }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">${{ formatNumber(stats.kpi?.revenue_today || 0) }}</p>
                        </div>
                        <div class="bg-emerald-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Ride Activity Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Ride Activity (Last 30 Days)</h4>
                    <div class="h-64">
                        <Line :data="rideActivityData" :options="lineChartOptions" v-if="stats.ride_activity" />
                    </div>
                </div>

                <!-- Ride Type Distribution -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Ride Type Distribution</h4>
                    <div class="h-64">
                        <Doughnut :data="rideTypeData" :options="doughnutChartOptions" v-if="stats.ride_type_distribution" />
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Growth Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">User Growth (Last 30 Days)</h4>
                    <div class="h-64">
                        <Line :data="userGrowthData" :options="lineChartOptions" v-if="stats.user_growth" />
                    </div>
                </div>

                <!-- Booking Funnel -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Booking Funnel</h4>
                    <div class="space-y-4">
                        <div v-for="(step, index) in bookingFunnelSteps" :key="index" class="flex items-center">
                            <div class="w-32 text-sm text-gray-600">{{ step.label }}</div>
                            <div class="flex-1 bg-gray-200 rounded-full h-8 relative overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-full rounded-full flex items-center justify-end pr-2"
                                    :style="{ width: step.percentage + '%' }">
                                    <span class="text-white text-xs font-semibold">{{ formatNumber(step.value) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Routes & Driver Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Popular Routes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Popular Routes</h4>
                    <div class="space-y-3">
                        <div v-for="(route, index) in stats.popular_routes" :key="index"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-400 mr-3">#{{ index + 1 }}</span>
                                <span class="text-gray-900 font-medium">{{ route.route }}</span>
                            </div>
                            <span class="text-gray-600 font-semibold">{{ route.count }} rides</span>
                        </div>
                    </div>
                </div>

                <!-- Driver Performance -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Top Drivers</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">Driver</th>
                                    <th class="text-center py-2 px-3 text-sm font-semibold text-gray-700">Trips</th>
                                    <th class="text-center py-2 px-3 text-sm font-semibold text-gray-700">Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="driver in stats.top_drivers" :key="driver.id" class="border-b">
                                    <td class="py-2 px-3 text-sm text-gray-900">{{ driver.name }}</td>
                                    <td class="py-2 px-3 text-sm text-center text-gray-600">{{ driver.trips }}</td>
                                    <td class="py-2 px-3 text-sm text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⭐ {{ driver.rating }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Revenue Analytics & Safety Panel -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Analytics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Revenue Overview</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-medium">Today</span>
                            <span class="text-xl font-bold text-green-600">${{ formatNumber(stats.kpi?.revenue_today || 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-medium">This Week</span>
                            <span class="text-xl font-bold text-green-600">${{ formatNumber(stats.kpi?.revenue_this_week || 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-medium">This Month</span>
                            <span class="text-xl font-bold text-green-600">${{ formatNumber(stats.kpi?.revenue_this_month || 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="text-gray-700 font-medium">Avg Commission per Ride</span>
                            <span class="text-lg font-bold text-blue-600">${{ formatNumber(stats.kpi?.avg_commission || 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Safety & Trust Metrics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Safety & Trust Metrics</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <span class="text-gray-700 font-medium">Driver Verification Pending</span>
                            <span class="text-lg font-bold text-yellow-600">{{ stats.safety_metrics?.driver_verification_pending || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
                            <span class="text-gray-700 font-medium">Reported Trips</span>
                            <span class="text-lg font-bold text-red-600">{{ stats.safety_metrics?.reported_trips || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <span class="text-gray-700 font-medium">Blocked Users</span>
                            <span class="text-lg font-bold text-orange-600">{{ stats.safety_metrics?.blocked_users || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <span class="text-gray-700 font-medium">Suspicious Activity</span>
                            <span class="text-lg font-bold text-purple-600">{{ stats.safety_metrics?.suspicious_activity || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
                            <span class="text-gray-700 font-medium">Avg Driver Rating</span>
                            <span class="text-lg font-bold text-green-600">⭐ {{ stats.safety_metrics?.avg_driver_rating || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="text-gray-700 font-medium">Avg Passenger Rating</span>
                            <span class="text-lg font-bold text-blue-600">⭐ {{ stats.safety_metrics?.avg_passenger_rating || 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h4>
                <div class="space-y-3">
                    <div v-for="(activity, index) in stats.recent_activity" :key="index"
                        class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                        <div class="flex-1">
                            <p class="text-gray-900">{{ activity.message }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ activity.time }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Line, Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import axios from 'axios';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
);

export default {
    components: {
        Line,
        Doughnut
    },
    data() {
        return {
            stats: {
                kpi: {},
                ride_activity: [],
                ride_type_distribution: {},
                booking_funnel: {},
                popular_routes: [],
                user_growth: [],
                safety_metrics: {},
                top_drivers: [],
                recent_activity: []
            },
            loading: true
        };
    },
    computed: {
        rideActivityData() {
            if (!this.stats.ride_activity || this.stats.ride_activity.length === 0) {
                return {
                    labels: [],
                    datasets: []
                };
            }
            return {
                labels: this.stats.ride_activity.map(item => item.day),
                datasets: [
                    {
                        label: 'Posted Rides',
                        data: this.stats.ride_activity.map(item => item.posted_rides),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Bookings',
                        data: this.stats.ride_activity.map(item => item.bookings),
                        borderColor: 'rgb(168, 85, 247)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4
                    }
                ]
            };
        },
        rideTypeData() {
            if (!this.stats.ride_type_distribution) {
                return {
                    labels: [],
                    datasets: []
                };
            }
            const distribution = this.stats.ride_type_distribution;
            const labels = Object.keys(distribution).filter(key => distribution[key] > 0);
            const data = labels.map(key => distribution[key]);
            const colors = [
                'rgb(59, 130, 246)',
                'rgb(236, 72, 153)',
                'rgb(168, 85, 247)',
                'rgb(34, 197, 94)',
                'rgb(251, 191, 36)',
                'rgb(249, 115, 22)'
            ];
            
            return {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };
        },
        userGrowthData() {
            if (!this.stats.user_growth || this.stats.user_growth.length === 0) {
                return {
                    labels: [],
                    datasets: []
                };
            }
            return {
                labels: this.stats.user_growth.map(item => item.day),
                datasets: [
                    {
                        label: 'Drivers',
                        data: this.stats.user_growth.map(item => item.drivers),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Passengers',
                        data: this.stats.user_growth.map(item => item.passengers),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Students',
                        data: this.stats.user_growth.map(item => item.students),
                        borderColor: 'rgb(251, 191, 36)',
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        tension: 0.4
                    }
                ]
            };
        },
        bookingFunnelSteps() {
            const funnel = this.stats.booking_funnel || {};
            const maxValue = funnel.searches || 1;
            return [
                { label: 'Search Ride', value: funnel.searches || 0, percentage: 100 },
                { label: 'View Ride', value: funnel.views || 0, percentage: ((funnel.views || 0) / maxValue) * 100 },
                { label: 'Booking Request', value: funnel.requests || 0, percentage: ((funnel.requests || 0) / maxValue) * 100 },
                { label: 'Confirmed', value: funnel.confirmed || 0, percentage: ((funnel.confirmed || 0) / maxValue) * 100 },
                { label: 'Completed', value: funnel.completed || 0, percentage: ((funnel.completed || 0) / maxValue) * 100 }
            ];
        },
        lineChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            };
        },
        doughnutChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            };
        }
    },
    created() {
        this.fetchStatistics();
    },
    methods: {
        async fetchStatistics() {
            try {
                this.loading = true;
                const response = await axios.get('/api/admin/dashboard/statistics');
                if (response.data.status === 'success' || response.data.status === 'Success') {
                    this.stats = response.data.data;
                }
            } catch (error) {
                console.error('Error fetching dashboard statistics:', error);
                this.$swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load dashboard statistics. Please try again.'
                });
            } finally {
                this.loading = false;
            }
        },
        formatNumber(num) {
            return new Intl.NumberFormat('en-US').format(num);
        }
    }
};
</script>

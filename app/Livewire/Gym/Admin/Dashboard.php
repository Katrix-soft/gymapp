<?php

namespace App\Livewire\Gym\Admin;

use App\Models\User;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\AttendanceRecord;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        // 1. KPI Stats
        $totalMembers = User::role('member')->count();
        $activeMemberships = Membership::active()->count();
        
        // Sum of successful payments in the last 30 days
        $monthlyRevenue = Payment::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('amount');
            
        // Attendance today
        $attendanceToday = AttendanceRecord::where('date', now()->toDateString())->count();

        // Recent Payments
        $recentPayments = Payment::with('user')
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent checkins
        $recentCheckins = AttendanceRecord::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 2. Chart data: Revenue trend for the last 6 months (Dynamic driver support)
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';
        $selectRaw = $isPgsql 
            ? "TO_CHAR(created_at, 'YYYY-MM') as month, SUM(amount) as total" 
            : "DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total";

        $revenueTrend = Payment::select(DB::raw($selectRaw))
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => (float) $item->total];
            })
            ->toArray();

        // Ensure 6 months are filled (even if 0)
        $revenueTrendData = [];
        $revenueTrendLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $monthLabel = now()->subMonths($i)->translatedFormat('M Y');
            $revenueTrendLabels[] = ucfirst($monthLabel);
            $revenueTrendData[] = $revenueTrend[$monthKey] ?? 0.0;
        }

        // 3. Chart data: Plan distribution
        $planDistribution = Membership::select('plans.name', DB::raw('count(*) as count'))
            ->join('plans', 'memberships.plan_id', '=', 'plans.id')
            ->where('memberships.status', 'active')
            ->groupBy('plans.name')
            ->get();

        $planLabels = $planDistribution->pluck('name')->toArray();
        $planCounts = $planDistribution->pluck('count')->map(fn($c) => (int)$c)->toArray();

        return view('livewire.gym.admin.dashboard', [
            'totalMembers' => $totalMembers,
            'activeMemberships' => $activeMemberships,
            'monthlyRevenue' => $monthlyRevenue,
            'attendanceToday' => $attendanceToday,
            'recentPayments' => $recentPayments,
            'recentCheckins' => $recentCheckins,
            'revenueTrendLabels' => $revenueTrendLabels,
            'revenueTrendData' => $revenueTrendData,
            'planLabels' => $planLabels,
            'planCounts' => $planCounts,
        ])->layout('layouts.tenant-app');
    }
}

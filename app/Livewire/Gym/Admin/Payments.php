<?php

namespace App\Livewire\Gym\Admin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\Membership;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Payments extends Component
{
    use WithPagination;

    // View tabs: 'transactions' or 'plans'
    public $activeTab = 'transactions';

    // Transactions filtering
    public $search = '';
    public $statusFilter = '';

    // Plan Templates management
    public $showPlanModal = false;
    public $editingPlanId = null;
    public $plan_name = '';
    public $plan_description = '';
    public $plan_price = '';
    public $plan_duration_months = 1;

    // Membership assignment state
    public $showAssignModal = false;
    public $selectedMemberId = '';
    public $memberSearch = '';
    public $selectedPlanId = '';
    public $membership_start_date = '';
    public $payment_method = 'Cash';
    public $payment_status = 'paid';

    protected $queryString = [
        'activeTab' => ['except' => 'transactions'],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->membership_start_date = now()->toDateString();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Modal triggers for Plan Template
    public function openPlanCreateModal()
    {
        $this->resetValidation();
        $this->resetPlanForm();
        $this->editingPlanId = null;
        $this->showPlanModal = true;
    }

    public function openPlanEditModal($planId)
    {
        $this->resetValidation();
        $this->resetPlanForm();
        $this->editingPlanId = $planId;

        $plan = Plan::findOrFail($planId);
        $this->plan_name = $plan->name;
        $this->plan_description = $plan->description;
        $this->plan_price = $plan->price;
        $this->plan_duration_months = $plan->duration_months;

        $this->showPlanModal = true;
    }

    public function savePlan()
    {
        $validated = $this->validate([
            'plan_name' => 'required|string|max:255',
            'plan_description' => 'nullable|string',
            'plan_price' => 'required|numeric|min:0',
            'plan_duration_months' => 'required|integer|min:1',
        ]);

        $data = [
            'name' => $this->plan_name,
            'description' => $this->plan_description,
            'price' => $this->plan_price,
            'duration_months' => $this->plan_duration_months,
        ];

        if ($this->editingPlanId) {
            $plan = Plan::findOrFail($this->editingPlanId);
            $plan->update($data);
            session()->flash('message', 'Plantilla de plan actualizada exitosamente.');
        } else {
            Plan::create($data);
            session()->flash('message', 'Plantilla de plan creada exitosamente.');
        }

        $this->showPlanModal = false;
        $this->resetPlanForm();
    }

    public function deletePlan($planId)
    {
        $plan = Plan::findOrFail($planId);
        $plan->delete();
        session()->flash('message', 'Plantilla de plan eliminada exitosamente.');
    }

    // Modal triggers for assigning membership
    public function openAssignModal()
    {
        $this->resetValidation();
        $this->selectedMemberId = '';
        $this->memberSearch = '';
        $this->selectedPlanId = '';
        $this->membership_start_date = now()->toDateString();
        $this->payment_method = 'Cash';
        $this->payment_status = 'paid';
        $this->showAssignModal = true;
    }

    public function selectMember($memberId, $memberName)
    {
        $this->selectedMemberId = $memberId;
        $this->memberSearch = $memberName;
    }

    public function assignMembership()
    {
        $this->validate([
            'selectedMemberId' => 'required|exists:users,id',
            'selectedPlanId' => 'required|exists:plans,id',
            'membership_start_date' => 'required|date',
            'payment_method' => 'required|string',
            'payment_status' => 'required|in:paid,pending',
        ]);

        $plan = Plan::findOrFail($this->selectedPlanId);
        $member = User::findOrFail($this->selectedMemberId);

        // Deactivate past active memberships for this member to keep it clean
        Membership::where('user_id', $member->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Calculate end date
        $start = Carbon::parse($this->membership_start_date);
        $end = $start->copy()->addMonths($plan->duration_months);

        // Create membership
        $membership = Membership::create([
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'status' => 'active',
        ]);

        // Create payment record
        Payment::create([
            'user_id' => $member->id,
            'membership_id' => $membership->id,
            'amount' => $plan->price,
            'status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'external_reference' => 'Manual-' . strtoupper(uniqid()),
        ]);

        // Ensure user status is active
        $member->update(['status' => 'active']);

        session()->flash('message', 'Membresía asignada y pago registrado exitosamente.');
        $this->showAssignModal = false;
    }

    private function resetPlanForm()
    {
        $this->plan_name = '';
        $this->plan_description = '';
        $this->plan_price = '';
        $this->plan_duration_months = 1;
        $this->editingPlanId = null;
    }

    public function render()
    {
        // 1. Fetch transactions query
        $paymentsQuery = Payment::with('user')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $paymentsQuery->whereHas('user', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== '') {
            $paymentsQuery->where('status', $this->statusFilter);
        }

        $payments = $paymentsQuery->paginate(10);

        // 2. Fetch plans
        $plans = Plan::orderBy('price', 'asc')->get();

        // 3. Fetch members for membership assign search autocomplete
        $searchMembers = [];
        if (strlen($this->memberSearch) >= 2 && !$this->selectedMemberId) {
            $searchMembers = User::role('member')
                ->where(function($q) {
                    $q->where('first_name', 'like', '%' . $this->memberSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->memberSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->memberSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.gym.admin.payments', [
            'payments' => $payments,
            'plans' => $plans,
            'searchMembers' => $searchMembers,
        ])->layout('layouts.tenant-app');
    }
}

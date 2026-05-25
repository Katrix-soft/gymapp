<?php

namespace App\Livewire\Central;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class TenantsManager extends Component
{
    use WithPagination;

    public $search = '';

    // Create Modal state
    public $showCreateModal = false;
    public $tenantId = ''; // slug e.g. "gymdemo"
    public $gymName = '';  // gym name

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->tenantId = '';
        $this->gymName = '';
        $this->showCreateModal = true;
    }

    public function saveTenant()
    {
        $this->validate([
            'tenantId' => 'required|string|alpha_dash|min:3|max:50|unique:tenants,id',
            'gymName' => 'required|string|max:100',
        ]);

        // Clean slug
        $slug = Str::lower($this->tenantId);

        // 1. Create tenant record (this triggers stancl/tenancy auto DB creation/migration events)
        $tenant = Tenant::create([
            'id' => $slug,
            'name' => $this->gymName,
        ]);

        // 2. Assign domain
        $centralDomain = config('tenancy.central_domains')[0] ?? 'gym.test';
        $tenant->domains()->create([
            'domain' => $slug . '.' . $centralDomain,
        ]);

        // Seed default parameters inside the tenant context:
        // We can execute code inside the tenant database using the tenancy manager
        tenancy()->initialize($tenant);
        
        // Seed default data
        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();
        
        tenancy()->end();

        session()->flash('message', "SaaS Gym Tenant '{$this->gymName}' creado y configurado exitosamente.");
        $this->showCreateModal = false;
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete(); // Automatically drops database & domains
        session()->flash('message', "Gym Tenant '{$id}' eliminado permanentemente.");
    }

    public function render()
    {
        $tenants = Tenant::with('domains')
            ->where('id', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.central.tenants-manager', [
            'tenants' => $tenants,
        ])->layout('layouts.app'); // Uses default central breeze layout
    }
}

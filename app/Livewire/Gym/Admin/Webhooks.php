<?php

namespace App\Livewire\Gym\Admin;

use App\Models\MercadoPagoWebhook;
use Livewire\Component;
use Livewire\WithPagination;

class Webhooks extends Component
{
    use WithPagination;

    public $selectedWebhookId = null;
    public $showPayloadModal = false;

    protected $listeners = ['refreshWebhooks' => '$refresh'];

    public function showPayload($id)
    {
        $this->selectedWebhookId = $id;
        $this->showPayloadModal = true;
    }

    public function closePayloadModal()
    {
        $this->selectedWebhookId = null;
        $this->showPayloadModal = false;
    }

    public function clearAllWebhooks()
    {
        MercadoPagoWebhook::truncate();
        session()->flash('message', 'Historial de webhooks vaciado.');
    }

    public function render()
    {
        $webhooks = MercadoPagoWebhook::orderBy('created_at', 'desc')->paginate(15);
        $selectedWebhook = $this->selectedWebhookId ? MercadoPagoWebhook::find($this->selectedWebhookId) : null;

        return view('livewire.gym.admin.webhooks', [
            'webhooks' => $webhooks,
            'selectedWebhook' => $selectedWebhook,
        ])->layout('layouts.tenant-app');
    }
}

<?php

namespace App\Livewire\Gym\Trainer;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;

class Chat extends Component
{
    public $activeUserId = null;
    public $activeUser = null;
    public $newMessage = '';
    public $chatSearch = '';
    public $messages = [];

    public function selectChat($userId)
    {
        $this->activeUserId = $userId;
        $this->activeUser = User::findOrFail($userId);
        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadMessages()
    {
        if (!$this->activeUserId) {
            $this->messages = [];
            return;
        }

        $myId = auth()->id();

        $this->messages = Message::where(function($q) use ($myId) {
                $q->where('sender_id', $myId)
                  ->where('receiver_id', $this->activeUserId);
            })
            ->orWhere(function($q) use ($myId) {
                $q->where('sender_id', $this->activeUserId)
                  ->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function markAsRead()
    {
        if (!$this->activeUserId) return;

        $myId = auth()->id();

        Message::where('sender_id', $this->activeUserId)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage()
    {
        if (trim($this->newMessage) === '' || !$this->activeUserId) return;

        $myId = auth()->id();

        Message::create([
            'sender_id' => $myId,
            'receiver_id' => $this->activeUserId,
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        $myId = auth()->id();

        $contactsQuery = User::where('id', '!=', $myId);

        if ($this->chatSearch !== '') {
            $contactsQuery->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->chatSearch . '%')
                  ->orWhere('last_name', 'like', '%' . $this->chatSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->chatSearch . '%');
            });
        }

        $contactsRaw = $contactsQuery->get();

        $contacts = [];
        foreach ($contactsRaw as $contact) {
            $lastMsg = Message::where(function($q) use ($myId, $contact) {
                    $q->where('sender_id', $myId)->where('receiver_id', $contact->id);
                })
                ->orWhere(function($q) use ($myId, $contact) {
                    $q->where('sender_id', $contact->id)->where('receiver_id', $myId);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = Message::where('sender_id', $contact->id)
                ->where('receiver_id', $myId)
                ->whereNull('read_at')
                ->count();

            $contacts[] = [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'role' => $contact->roles->first()->name ?? 'member',
                'last_message' => $lastMsg ? $lastMsg->message : null,
                'last_message_time' => $lastMsg ? $lastMsg->created_at : null,
                'unread_count' => $unreadCount,
            ];
        }

        usort($contacts, function($a, $b) {
            if ($a['last_message_time'] && $b['last_message_time']) {
                return $b['last_message_time']->timestamp <=> $a['last_message_time']->timestamp;
            }
            if ($a['last_message_time']) return -1;
            if ($b['last_message_time']) return 1;
            return strcmp($a['name'], $b['name']);
        });

        return view('livewire.gym.trainer.chat', [
            'contacts' => $contacts,
        ])->layout('layouts.tenant-app');
    }
}

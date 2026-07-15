<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;
use Livewire\WithPagination;

class AdminAnnouncements extends Component
{
    use WithPagination;

    public $search = '';
    public $status = ''; // all, pending, paid
    public $type = ''; // all, funeral, general
    public $media = ''; // all, tv, radio, both
    public $approved = ''; // all, 1 (approved), 0 (pending)

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
        'media' => ['except' => ''],
        'approved' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleApproval($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'is_approved' => !$announcement->is_approved
        ]);

        // Trigger user email notification
        \App\Support\Mailer::sendAnnouncementStatus($announcement);

        session()->flash('message', 'Announcement approval status updated.');
    }

    public function markAsPaid($id)
    {
        $announcement = Announcement::findOrFail($id);

        $commissionAmount = 0;
        if ($announcement->agent_id) {
            $agent = \App\Models\Agent::find($announcement->agent_id);
            if ($agent) {
                $commissionAmount = (int) round(($announcement->total_amount * $agent->commission_percentage) / 100);
            }
        }

        $announcement->update([
            'payment_status' => 'paid',
            'payment_reference' => $announcement->payment_reference ?? 'MANUAL-' . strtoupper(uniqid()),
            'commission_amount' => $commissionAmount,
        ]);

        // Trigger user email notification
        \App\Support\Mailer::sendAnnouncementStatus($announcement);

        \App\Support\Sms::sendAdminNotification(
            "[Getembe News] Announcement ID {$announcement->id} manually marked as Paid by Admin. Submitter: {$announcement->visitor_name}. Amount: KSh {$announcement->total_amount}."
        );

        session()->flash('message', 'Announcement marked as Paid.');
    }

    public function deleteAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        session()->flash('message', 'Announcement deleted successfully.');
    }

    public function render()
    {
        $query = Announcement::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('visitor_name', 'like', '%' . $this->search . '%')
                  ->orWhere('visitor_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->status)) {
            $query->where('payment_status', $this->status);
        }

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }

        if ($this->media !== '') {
            $query->where('media', $this->media);
        }

        if ($this->approved !== '') {
            $query->where('is_approved', (bool) $this->approved);
        }

        // Calculate dynamic dashboard financial statistics
        $stats = [
            'total_paid' => Announcement::where('payment_status', 'paid')->sum('total_amount'),
            'total_pending' => Announcement::where('payment_status', 'pending')->sum('total_amount'),
            'total_commissions' => Announcement::where('payment_status', 'paid')->sum('commission_amount'),
            'pending_approval' => Announcement::where('is_approved', false)->count(),
        ];

        $announcements = $query->latest()->paginate(10);

        return view('livewire.admin-announcements', [
            'announcements' => $announcements,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}

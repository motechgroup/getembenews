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

    public function exportRevenueReport()
    {
        $query = Announcement::query()->where('payment_status', 'paid');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('visitor_name', 'like', '%' . $this->search . '%')
                  ->orWhere('visitor_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
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

        $announcements = $query->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=revenue_report_' . now()->format('Ymd_His') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($announcements) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Announcement ID',
                'Visitor Name',
                'Visitor Phone',
                'Announcement Type',
                'Media Target',
                'Airing Date',
                'Expiry Date',
                'Words Count',
                'Days Count',
                'Total Paid (KSh)',
                'Agent Name',
                'Agent Commission (KSh)',
                'Payment Reference',
                'Approved Status'
            ]);

            foreach ($announcements as $ann) {
                fputcsv($file, [
                    $ann->id,
                    $ann->visitor_name,
                    $ann->visitor_phone,
                    ucfirst($ann->type),
                    strtoupper($ann->media),
                    $ann->airing_date ? $ann->airing_date->format('Y-m-d') : 'N/A',
                    $ann->expiry_date ? $ann->expiry_date->format('Y-m-d') : 'N/A',
                    $ann->word_count,
                    $ann->days_count,
                    $ann->total_amount,
                    $ann->agent ? $ann->agent->name : 'N/A',
                    $ann->commission_amount,
                    $ann->payment_reference,
                    $ann->is_approved ? 'Approved' : 'Pending Approval'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'tv_revenue' => Announcement::where('media', 'tv')->where('payment_status', 'paid')->sum('total_amount'),
            'tv_count' => Announcement::where('media', 'tv')->count(),
            'radio_revenue' => Announcement::where('media', 'radio')->where('payment_status', 'paid')->sum('total_amount'),
            'radio_count' => Announcement::where('media', 'radio')->count(),
            'both_revenue' => Announcement::where('media', 'both')->where('payment_status', 'paid')->sum('total_amount'),
            'both_count' => Announcement::where('media', 'both')->count(),
        ];

        $announcements = $query->latest()->paginate(10);

        return view('livewire.admin-announcements', [
            'announcements' => $announcements,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}

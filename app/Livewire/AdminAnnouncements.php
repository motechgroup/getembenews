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
    public $showFilters = false;
    public $date_from = '';
    public $date_to = '';
    public $selected_month = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
        'media' => ['except' => ''],
        'approved' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'selected_month' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getMonthsProperty()
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        return $months;
    }

    // View Modal State
    public $showViewModal = false;
    public $viewingAnnouncement = null;

    // Edit Modal State
    public $showEditModal = false;
    public $editingAnnouncementId = null;
    public $edit_visitor_name = '';
    public $edit_visitor_phone = '';
    public $edit_visitor_email = '';
    public $edit_type = 'funeral';
    public $edit_media = 'tv';
    public $edit_content = '';
    public $edit_airing_date = '';
    public $edit_days_count = 1;
    public $edit_rate_per_word = 5;
    public $edit_word_count = 0;
    public $edit_total_amount = 0;
    public $edit_payment_status = 'pending';
    public $edit_payment_reference = '';
    public $edit_is_approved = false;

    public function openViewModal($id)
    {
        $this->viewingAnnouncement = Announcement::with('agent')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingAnnouncement = null;
    }

    public function openEditModal($id)
    {
        $ann = Announcement::findOrFail($id);
        $this->editingAnnouncementId = $ann->id;
        $this->edit_visitor_name = $ann->visitor_name;
        $this->edit_visitor_phone = $ann->visitor_phone;
        $this->edit_visitor_email = $ann->visitor_email ?? '';
        $this->edit_type = $ann->type;
        $this->edit_media = $ann->media;
        $this->edit_content = $ann->content;
        $this->edit_airing_date = $ann->airing_date ? $ann->airing_date->format('Y-m-d') : now()->toDateString();
        $this->edit_days_count = $ann->days_count;
        $this->edit_rate_per_word = $ann->rate_per_word;
        $this->edit_word_count = $ann->word_count;
        $this->edit_total_amount = $ann->total_amount;
        $this->edit_payment_status = $ann->payment_status;
        $this->edit_payment_reference = $ann->payment_reference ?? '';
        $this->edit_is_approved = (bool) $ann->is_approved;

        $this->updateEditCalculations();
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingAnnouncementId = null;
        $this->resetValidation();
    }

    public function updatedEditContent()
    {
        $this->updateEditCalculations();
    }

    public function updatedEditMedia()
    {
        $this->updateEditCalculations();
    }

    public function updatedEditDaysCount()
    {
        $this->updateEditCalculations();
    }

    public function updateEditCalculations()
    {
        if ($this->edit_media === 'tv') {
            $this->edit_rate_per_word = (int) \App\Models\Setting::get('announcement_rate_tv', 5);
        } elseif ($this->edit_media === 'radio') {
            $this->edit_rate_per_word = (int) \App\Models\Setting::get('announcement_rate_radio', 3);
        } else {
            $this->edit_rate_per_word = (int) \App\Models\Setting::get('announcement_rate_both', 7);
        }

        if (empty(trim($this->edit_content))) {
            $this->edit_word_count = 0;
        } else {
            $this->edit_word_count = count(array_filter(explode(' ', preg_replace('/\s+/', ' ', trim($this->edit_content)))));
        }

        $this->edit_total_amount = $this->edit_word_count * $this->edit_rate_per_word * max(1, (int) $this->edit_days_count);
    }

    public function saveAnnouncement()
    {
        $this->validate([
            'edit_visitor_name' => 'required|string|max:255',
            'edit_visitor_phone' => 'required|string|max:20',
            'edit_visitor_email' => 'nullable|email|max:255',
            'edit_type' => 'required|in:funeral,general',
            'edit_media' => 'required|in:tv,radio,both',
            'edit_content' => 'required|string',
            'edit_days_count' => 'required|integer|min:1|max:30',
            'edit_airing_date' => 'required|date',
            'edit_payment_status' => 'required|in:pending,paid',
            'edit_is_approved' => 'required|boolean',
        ]);

        $announcement = Announcement::findOrFail($this->editingAnnouncementId);

        $commissionAmount = $announcement->commission_amount;
        if ($this->edit_payment_status === 'paid' && $announcement->agent_id) {
            $agent = \App\Models\Agent::find($announcement->agent_id);
            if ($agent) {
                $commissionAmount = (int) round(($this->edit_total_amount * $agent->commission_percentage) / 100);
            }
        }

        $paymentRef = $this->edit_payment_reference;
        if ($this->edit_payment_status === 'paid' && empty($paymentRef)) {
            $paymentRef = 'MANUAL-' . strtoupper(uniqid());
        }

        $announcement->update([
            'visitor_name' => strip_tags(trim($this->edit_visitor_name)),
            'visitor_phone' => strip_tags(trim($this->edit_visitor_phone)),
            'visitor_email' => $this->edit_visitor_email ? strip_tags(trim(strtolower($this->edit_visitor_email))) : null,
            'type' => $this->edit_type,
            'media' => $this->edit_media,
            'content' => strip_tags(trim($this->edit_content)),
            'airing_date' => $this->edit_airing_date,
            'days_count' => (int) $this->edit_days_count,
            'word_count' => $this->edit_word_count,
            'rate_per_word' => $this->edit_rate_per_word,
            'total_amount' => $this->edit_total_amount,
            'payment_status' => $this->edit_payment_status,
            'payment_reference' => $paymentRef,
            'is_approved' => (bool) $this->edit_is_approved,
            'commission_amount' => $commissionAmount,
        ]);

        $this->showEditModal = false;
        $this->editingAnnouncementId = null;

        session()->flash('message', 'Announcement details updated successfully.');
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

        if (!empty($this->date_from)) {
            $query->whereDate('airing_date', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('airing_date', '<=', $this->date_to);
        }

        if (!empty($this->selected_month)) {
            $parts = explode('-', $this->selected_month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])
                      ->whereMonth('created_at', $parts[1]);
            }
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

        if (!empty($this->date_from)) {
            $query->whereDate('airing_date', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('airing_date', '<=', $this->date_to);
        }

        if (!empty($this->selected_month)) {
            $parts = explode('-', $this->selected_month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])
                      ->whereMonth('created_at', $parts[1]);
            }
        }

        // Base query for statistics calculations, matching all active query filters
        $statsQuery = Announcement::query();

        if (!empty($this->search)) {
            $statsQuery->where(function($q) {
                $q->where('visitor_name', 'like', '%' . $this->search . '%')
                  ->orWhere('visitor_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->status)) {
            $statsQuery->where('payment_status', $this->status);
        }

        if (!empty($this->type)) {
            $statsQuery->where('type', $this->type);
        }

        if ($this->media !== '') {
            $statsQuery->where('media', $this->media);
        }

        if ($this->approved !== '') {
            $statsQuery->where('is_approved', (bool) $this->approved);
        }

        if (!empty($this->date_from)) {
            $statsQuery->whereDate('airing_date', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $statsQuery->whereDate('airing_date', '<=', $this->date_to);
        }

        if (!empty($this->selected_month)) {
            $parts = explode('-', $this->selected_month);
            if (count($parts) === 2) {
                $statsQuery->whereYear('created_at', $parts[0])
                           ->whereMonth('created_at', $parts[1]);
            }
        }

        // Calculate dynamic dashboard financial statistics based on the filtered query
        $stats = [
            'total_paid' => (clone $statsQuery)->where('payment_status', 'paid')->sum('total_amount'),
            'total_pending' => (clone $statsQuery)->where('payment_status', 'pending')->sum('total_amount'),
            'total_commissions' => (clone $statsQuery)->where('payment_status', 'paid')->sum('commission_amount'),
            'pending_approval' => (clone $statsQuery)->where('is_approved', false)->count(),
            'tv_revenue' => (clone $statsQuery)->where('media', 'tv')->where('payment_status', 'paid')->sum('total_amount'),
            'tv_count' => (clone $statsQuery)->where('media', 'tv')->count(),
            'radio_revenue' => (clone $statsQuery)->where('media', 'radio')->where('payment_status', 'paid')->sum('total_amount'),
            'radio_count' => (clone $statsQuery)->where('media', 'radio')->count(),
            'both_revenue' => (clone $statsQuery)->where('media', 'both')->where('payment_status', 'paid')->sum('total_amount'),
            'both_count' => (clone $statsQuery)->where('media', 'both')->count(),
        ];

        $announcements = $query->latest()->paginate(10);

        return view('livewire.admin-announcements', [
            'announcements' => $announcements,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}

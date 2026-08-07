<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;
use App\Models\Agent;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnnouncementReports extends Component
{
    use WithPagination;

    public $period = 'this_month';
    public $date_from = '';
    public $date_to = '';
    public $media = '';
    public $type = '';
    public $status = '';
    public $agent_id = '';
    public $search = '';

    protected $queryString = [
        'period' => ['except' => 'this_month'],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'media' => ['except' => ''],
        'type' => ['except' => ''],
        'status' => ['except' => ''],
        'agent_id' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->applyPeriodDates();
    }

    public function updatedPeriod()
    {
        $this->applyPeriodDates();
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedMedia()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedAgentId()
    {
        $this->resetPage();
    }

    public function setPeriod($preset)
    {
        $this->period = $preset;
        $this->applyPeriodDates();
        $this->resetPage();
    }

    protected function applyPeriodDates()
    {
        switch ($this->period) {
            case 'today':
                $this->date_from = Carbon::today()->toDateString();
                $this->date_to = Carbon::today()->toDateString();
                break;
            case 'yesterday':
                $this->date_from = Carbon::yesterday()->toDateString();
                $this->date_to = Carbon::yesterday()->toDateString();
                break;
            case 'this_week':
                $this->date_from = Carbon::now()->startOfWeek()->toDateString();
                $this->date_to = Carbon::now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->date_from = Carbon::now()->startOfMonth()->toDateString();
                $this->date_to = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->date_from = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $this->date_to = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'this_year':
                $this->date_from = Carbon::now()->startOfYear()->toDateString();
                $this->date_to = Carbon::now()->endOfYear()->toDateString();
                break;
            case 'all_time':
                $this->date_from = '';
                $this->date_to = '';
                break;
            case 'custom':
                // keep current custom date_from / date_to
                break;
        }
    }

    public function exportCsv()
    {
        $query = $this->buildFilteredQuery();
        $announcements = $query->with('agent')->latest()->get();

        $filename = 'announcement_report_' . ($this->period ?: 'custom') . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($announcements) {
            $file = fopen('php://output', 'w');

            // Write CSV Header
            fputcsv($file, [
                'Report Generated Date',
                now()->toDateTimeString(),
            ]);
            fputcsv($file, []); // blank row

            fputcsv($file, [
                'ID',
                'Submitter Name',
                'Submitter Phone',
                'Submitter Email',
                'Type',
                'Media Target',
                'Airing Date',
                'Expiry Date',
                'Words',
                'Days',
                'Amount (KSh)',
                'Payment Status',
                'Payment Ref',
                'Agent ID',
                'Agent Name',
                'Agent Business',
                'Agent Phone',
                'Commission (KSh)',
                'Approval Status',
                'Created Date',
            ]);

            foreach ($announcements as $ann) {
                fputcsv($file, [
                    $ann->id,
                    $ann->visitor_name,
                    $ann->visitor_phone,
                    $ann->visitor_email ?? '',
                    ucfirst($ann->type),
                    strtoupper($ann->media),
                    $ann->airing_date ? $ann->airing_date->format('Y-m-d') : 'N/A',
                    $ann->expiry_date ? $ann->expiry_date->format('Y-m-d') : 'N/A',
                    $ann->word_count,
                    $ann->days_count,
                    $ann->total_amount,
                    ucfirst($ann->payment_status),
                    $ann->payment_reference ?? 'N/A',
                    $ann->agent_id ?? 'Direct',
                    $ann->agent ? $ann->agent->name : 'Direct (Website)',
                    $ann->agent ? ($ann->agent->business_name ?? 'N/A') : 'N/A',
                    $ann->agent ? ($ann->agent->phone ?? 'N/A') : 'N/A',
                    $ann->commission_amount ?? 0,
                    $ann->is_approved ? 'Approved' : 'Pending',
                    $ann->created_at ? $ann->created_at->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function buildFilteredQuery()
    {
        $query = Announcement::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('visitor_name', 'like', '%' . $this->search . '%')
                  ->orWhere('visitor_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%')
                  ->orWhere('payment_reference', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->status)) {
            $query->where('payment_status', $this->status);
        }

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }

        if (!empty($this->media)) {
            $query->where('media', $this->media);
        }

        if ($this->agent_id !== '') {
            if ($this->agent_id === 'direct') {
                $query->whereNull('agent_id');
            } else {
                $query->where('agent_id', $this->agent_id);
            }
        }

        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return $query;
    }

    public function render()
    {
        $baseQuery = $this->buildFilteredQuery();

        // 1. KPI Overview Metrics
        $totalSubmissions = (clone $baseQuery)->count();
        $totalPaidSubmissions = (clone $baseQuery)->where('payment_status', 'paid')->count();
        $totalPendingSubmissions = (clone $baseQuery)->where('payment_status', 'pending')->count();

        $grossRevenue = (clone $baseQuery)->where('payment_status', 'paid')->sum('total_amount');
        $pendingRevenue = (clone $baseQuery)->where('payment_status', 'pending')->sum('total_amount');
        $totalCommissions = (clone $baseQuery)->where('payment_status', 'paid')->sum('commission_amount');
        $netPlatformRevenue = $grossRevenue - $totalCommissions;
        $avgTicketSize = $totalPaidSubmissions > 0 ? (int) round($grossRevenue / $totalPaidSubmissions) : 0;

        $stats = [
            'total_submissions' => $totalSubmissions,
            'paid_count' => $totalPaidSubmissions,
            'pending_count' => $totalPendingSubmissions,
            'gross_revenue' => $grossRevenue,
            'pending_revenue' => $pendingRevenue,
            'total_commissions' => $totalCommissions,
            'net_revenue' => $netPlatformRevenue,
            'avg_ticket_size' => $avgTicketSize,
        ];

        // 2. Top Performing Agents Leaderboard
        $agentStats = Announcement::query()
            ->whereNotNull('agent_id')
            ->where('payment_status', 'paid');

        if (!empty($this->date_from)) {
            $agentStats->whereDate('created_at', '>=', $this->date_from);
        }
        if (!empty($this->date_to)) {
            $agentStats->whereDate('created_at', '<=', $this->date_to);
        }

        $topAgentRecords = $agentStats->select('agent_id', 
                DB::raw('COUNT(*) as total_announcements'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(commission_amount) as total_commission')
            )
            ->groupBy('agent_id')
            ->orderBy('total_revenue', 'desc')
            ->take(10)
            ->get();

        $agentIds = $topAgentRecords->pluck('agent_id')->toArray();
        $agentsMap = Agent::whereIn('id', $agentIds)->get()->keyBy('id');

        $topAgents = $topAgentRecords->map(function ($rec) use ($agentsMap) {
            $rec->agent = $agentsMap->get($rec->agent_id);
            return $rec;
        })->filter(function ($rec) {
            return $rec->agent !== null;
        });

        // 3. Media Target Breakdown
        $mediaBreakdown = [
            'tv' => [
                'count' => (clone $baseQuery)->where('media', 'tv')->count(),
                'revenue' => (clone $baseQuery)->where('media', 'tv')->where('payment_status', 'paid')->sum('total_amount'),
            ],
            'radio' => [
                'count' => (clone $baseQuery)->where('media', 'radio')->count(),
                'revenue' => (clone $baseQuery)->where('media', 'radio')->where('payment_status', 'paid')->sum('total_amount'),
            ],
            'both' => [
                'count' => (clone $baseQuery)->where('media', 'both')->count(),
                'revenue' => (clone $baseQuery)->where('media', 'both')->where('payment_status', 'paid')->sum('total_amount'),
            ],
        ];

        // 4. Type Breakdown
        $typeBreakdown = [
            'funeral' => [
                'count' => (clone $baseQuery)->where('type', 'funeral')->count(),
                'revenue' => (clone $baseQuery)->where('type', 'funeral')->where('payment_status', 'paid')->sum('total_amount'),
            ],
            'general' => [
                'count' => (clone $baseQuery)->where('type', 'general')->count(),
                'revenue' => (clone $baseQuery)->where('type', 'general')->where('payment_status', 'paid')->sum('total_amount'),
            ],
        ];

        // 5. Filtered Announcements Table
        $announcements = (clone $baseQuery)->with('agent')->latest()->paginate(15);
        $allAgents = Agent::orderBy('name')->get();

        return view('livewire.admin-announcement-reports', [
            'stats' => $stats,
            'topAgents' => $topAgents,
            'mediaBreakdown' => $mediaBreakdown,
            'typeBreakdown' => $typeBreakdown,
            'announcements' => $announcements,
            'allAgents' => $allAgents,
        ])->layout('layouts.admin');
    }
}

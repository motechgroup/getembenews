<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Agent;
use App\Models\Announcement;
use Livewire\WithPagination;

class AgentDashboard extends Component
{
    use WithPagination;

    public $agent;
    public $activeTab = 'announcements'; // announcements, payouts, disputes

    // Dispute Form inputs
    public $dispute_subject = '';
    public $dispute_description = '';
    public $dispute_announcement_id = '';

    public function mount()
    {
        $agentId = session('agent_logged_in');
        if (!$agentId) {
            return $this->redirect('/announcements');
        }

        $this->agent = Agent::find($agentId);
        if (!$this->agent) {
            session()->forget('agent_logged_in');
            return $this->redirect('/announcements');
        }
    }

    public function logoutAgent()
    {
        session()->forget('agent_logged_in');
        return $this->redirect('/announcements');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->reset(['dispute_subject', 'dispute_description', 'dispute_announcement_id']);
        $this->resetPage('payoutsPage');
        $this->resetPage('disputesPage');
        $this->resetPage();
    }

    public function fileDispute()
    {
        $this->validate([
            'dispute_subject' => 'required|string|max:255',
            'dispute_description' => 'required|string|min:5',
            'dispute_announcement_id' => 'nullable|integer|exists:announcements,id',
        ]);

        \App\Models\Dispute::create([
            'agent_id' => $this->agent->id,
            'announcement_id' => $this->dispute_announcement_id ?: null,
            'subject' => $this->dispute_subject,
            'description' => $this->dispute_description,
            'status' => 'open',
        ]);

        $this->reset(['dispute_subject', 'dispute_description', 'dispute_announcement_id']);
        
        // Refresh agent data
        $this->agent = Agent::find($this->agent->id);
        
        session()->flash('dispute_message', 'Dispute ticket submitted successfully. Support team will review it.');
    }

    public function render()
    {
        // Load the agent's announcements
        $announcements = Announcement::where('agent_id', $this->agent->id)
            ->latest()
            ->paginate(10, ['*'], 'page');

        // Load the agent's payouts
        $payouts = $this->agent->payouts()->latest()->paginate(10, ['*'], 'payoutsPage');

        // Load the agent's disputes
        $disputes = $this->agent->disputes()->latest()->paginate(10, ['*'], 'disputesPage');

        return view('livewire.agent-dashboard', [
            'announcements' => $announcements,
            'payouts' => $payouts,
            'disputes' => $disputes,
        ])->layout('layouts.news');
    }
}


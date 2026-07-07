<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Agent;
use App\Models\Announcement;

class AgentDashboard extends Component
{
    public $agent;

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

    public function render()
    {
        // Load the agent's announcements
        $announcements = Announcement::where('agent_id', $this->agent->id)
            ->latest()
            ->paginate(10);

        return view('livewire.agent-dashboard', [
            'announcements' => $announcements
        ])->layout('layouts.news');
    }
}

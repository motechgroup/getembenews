<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Agent;
use Livewire\WithPagination;

class AdminAgents extends Component
{
    use WithPagination;

    public $search = '';

    // Form inputs
    public $agentId = null;
    public $name = '';
    public $location = '';
    public $pin = '';
    public $commission_percentage = 10;

    public $isFormOpen = false;

    // Details state
    public $selectedAgentForDetails = null;
    public $isDetailsOpen = false;

    protected $messages = [
        'pin.regex' => 'The PIN must consist of exactly 4 digits.',
        'pin.size' => 'The PIN must be exactly 4 characters.',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'pin' => 'required|string|size:4|regex:/^[0-9]{4}$/|unique:agents,pin,' . $this->agentId,
            'commission_percentage' => 'required|integer|min:0|max:100',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->agentId = $id;

        if ($id) {
            $agent = Agent::findOrFail($id);
            $this->name = $agent->name;
            $this->location = $agent->location;
            $this->pin = $agent->pin;
            $this->commission_percentage = $agent->commission_percentage;
        } else {
            $this->name = '';
            $this->location = '';
            $this->pin = '';
            $this->commission_percentage = 10;
        }

        $this->isFormOpen = true;
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function saveAgent()
    {
        $this->validate();

        if ($this->agentId) {
            $agent = Agent::findOrFail($this->agentId);
            $agent->update([
                'name' => $this->name,
                'location' => $this->location,
                'pin' => $this->pin,
                'commission_percentage' => (int) $this->commission_percentage,
            ]);
            session()->flash('message', 'Agent updated successfully.');
        } else {
            Agent::create([
                'name' => $this->name,
                'location' => $this->location,
                'pin' => $this->pin,
                'commission_percentage' => (int) $this->commission_percentage,
            ]);
            session()->flash('message', 'Agent created successfully.');
        }

        $this->closeForm();
    }

    public function deleteAgent($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();

        if ($this->selectedAgentForDetails && $this->selectedAgentForDetails->id === $id) {
            $this->closeDetails();
        }

        session()->flash('message', 'Agent deleted successfully.');
    }

    public function viewDetails($id)
    {
        $this->selectedAgentForDetails = Agent::findOrFail($id);
        $this->isDetailsOpen = true;
    }

    public function closeDetails()
    {
        $this->isDetailsOpen = false;
        $this->selectedAgentForDetails = null;
    }

    public function render()
    {
        $query = Agent::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('pin', 'like', '%' . $this->search . '%');
            });
        }

        $agents = $query->latest()->paginate(10);

        return view('livewire.admin-agents', [
            'agents' => $agents
        ])->layout('layouts.admin');
    }
}

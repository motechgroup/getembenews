<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Agent;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Support\AgentImporter;

class AdminAgents extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // Form inputs
    public $agentId = null;
    public $name = '';
    public $business_name = '';
    public $location = '';
    public $pin = '';
    public $commission_percentage = 10;

    public $isFormOpen = false;

    // Import state
    public $isImportOpen = false;
    public $importFile = null;
    public $importResults = null;

    // Details state
    public $selectedAgentForDetails = null;
    public $isDetailsOpen = false;
    public $activeDetailsTab = 'announcements'; // announcements, payouts, disputes

    // Payout Form inputs
    public $payout_amount = '';
    public $payout_method = 'M-Pesa';
    public $payout_reference = '';

    // Dispute Resolution inputs
    public $dispute_resolution = '';

    protected $messages = [
        'pin.regex' => 'The PIN must consist of exactly 4 digits.',
        'pin.size' => 'The PIN must be exactly 4 characters.',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
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
            $this->business_name = $agent->business_name;
            $this->location = $agent->location;
            $this->pin = $agent->pin;
            $this->commission_percentage = $agent->commission_percentage;
        } else {
            $this->name = '';
            $this->business_name = '';
            $this->location = '';
            $this->pin = Agent::generateUniquePin();
            $this->commission_percentage = 10;
        }

        $this->isFormOpen = true;
    }

    public function regeneratePin()
    {
        $this->pin = Agent::generateUniquePin();
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function saveAgent()
    {
        if (empty($this->pin)) {
            $this->pin = Agent::generateUniquePin();
        }

        $this->validate();

        if ($this->agentId) {
            $agent = Agent::findOrFail($this->agentId);
            $agent->update([
                'name' => $this->name,
                'business_name' => $this->business_name ?: null,
                'location' => $this->location,
                'pin' => $this->pin,
                'commission_percentage' => (int) $this->commission_percentage,
            ]);
            session()->flash('message', 'Agent updated successfully.');
        } else {
            Agent::create([
                'name' => $this->name,
                'business_name' => $this->business_name ?: null,
                'location' => $this->location,
                'pin' => $this->pin,
                'commission_percentage' => (int) $this->commission_percentage,
            ]);
            session()->flash('message', 'Agent created successfully.');
        }

        $this->closeForm();
    }

    public function openImport()
    {
        $this->resetValidation();
        $this->importFile = null;
        $this->importResults = null;
        $this->isImportOpen = true;
    }

    public function closeImport()
    {
        $this->isImportOpen = false;
        $this->importFile = null;
        $this->importResults = null;
    }

    public function importAgents()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'importFile.required' => 'Please select a CSV or Excel file to upload.',
            'importFile.mimes' => 'The file must be a valid CSV, TXT, XLS, or XLSX file.',
            'importFile.max' => 'The file size must not exceed 10MB.',
        ]);

        $path = $this->importFile->getRealPath();
        $originalExt = $this->importFile->getClientOriginalExtension();

        $results = AgentImporter::importFromFile($path, $originalExt);
        $this->importResults = $results;

        if ($results['success'] > 0) {
            session()->flash('message', "Bulk Import Complete: Successfully imported {$results['success']} agent(s).");
        }
    }

    public function downloadSampleCsv()
    {
        $csvHeader = "Name,Business Name,Location,Commission Percentage,PIN\n";
        $sampleData = "Samuel Mogaka,Mogaka Enterprises,Kisii Town,15,1234\nFaith Kerubo,Kerubo Traders,Nyamira,10,\nDavid Omwamba,,Ogembo,20,5678\n";

        return response()->streamDownload(function () use ($csvHeader, $sampleData) {
            echo $csvHeader . $sampleData;
        }, 'agents_sample_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function regenerateAgentPin($agentId)
    {
        $agent = Agent::findOrFail($agentId);
        $newPin = Agent::generateUniquePin();
        $agent->update(['pin' => $newPin]);

        if ($this->selectedAgentForDetails && $this->selectedAgentForDetails->id === $agent->id) {
            $this->selectedAgentForDetails = $agent->fresh();
        }

        session()->flash('message', "Security PIN for agent '{$agent->name}' regenerated successfully: {$newPin}");
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
        $this->activeDetailsTab = 'announcements';
        $this->isDetailsOpen = true;
        $this->reset(['payout_amount', 'payout_reference', 'dispute_resolution']);
    }

    public function closeDetails()
    {
        $this->isDetailsOpen = false;
        $this->selectedAgentForDetails = null;
    }

    public function setDetailsTab($tab)
    {
        $this->activeDetailsTab = $tab;
        $this->reset(['payout_amount', 'payout_reference', 'dispute_resolution']);
    }

    public function recordPayout()
    {
        $this->validate([
            'payout_amount' => 'required|integer|min:1',
            'payout_method' => 'required|string',
            'payout_reference' => 'nullable|string|max:100',
        ]);

        \App\Models\Payout::create([
            'agent_id' => $this->selectedAgentForDetails->id,
            'amount' => (int) $this->payout_amount,
            'payment_method' => $this->payout_method,
            'reference' => $this->payout_reference ?: 'MANUAL-' . strtoupper(uniqid()),
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->reset(['payout_amount', 'payout_reference']);
        $this->selectedAgentForDetails = Agent::findOrFail($this->selectedAgentForDetails->id);
        session()->flash('payout_message', 'Payout recorded successfully.');
    }

    public function deletePayout($payoutId)
    {
        $payout = \App\Models\Payout::findOrFail($payoutId);
        $payout->delete();

        $this->selectedAgentForDetails = Agent::findOrFail($this->selectedAgentForDetails->id);
        session()->flash('payout_message', 'Payout removed/voided successfully.');
    }

    public function resolveDispute($disputeId, $resolutionStatus)
    {
        $this->validate([
            'dispute_resolution' => 'required|string|min:3',
        ]);

        $dispute = \App\Models\Dispute::findOrFail($disputeId);
        $dispute->update([
            'status' => $resolutionStatus,
            'resolution' => $this->dispute_resolution,
        ]);

        $this->reset('dispute_resolution');
        $this->selectedAgentForDetails = Agent::findOrFail($this->selectedAgentForDetails->id);
        session()->flash('dispute_message', 'Dispute ticket status updated.');
    }

    public function render()
    {
        $query = Agent::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('business_name', 'like', '%' . $this->search . '%')
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

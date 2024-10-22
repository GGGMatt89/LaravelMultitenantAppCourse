<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;

class DepartmentForm extends Component
{
    public $name="Mattia";

    public function mount($departmentId = null)
    {
        if($departmentId) {
            $this->name = Department::findOrFail($departmentId)->name;
        }

    }
    public function render()
    {
        return view('livewire.department-form');
    }
}

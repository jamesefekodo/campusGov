<?php

use Livewire\Component;

new class extends Component {
    public $username;
    public $academic_status = 'student';
    public $program;
    public $program_type;
    public $department;
    public $admission_session;
    public $graduation_session;
    public $matric_number;

    public function save()
    {
        // Add artificial delay or standard validation/save logic
        $this->validate([
            'username' => 'required|exists:users,username',
            'academic_status' => 'required',
            'matric_number' => 'nullable|string',
        ]);

        MemberAcademicInfo::updateOrCreate(
            ['username' => $this->username],
            [
                'academic_status' => $this->academic_status,
                'program' => $this->program,
                'program_type' => $this->program_type,
                'department' => $this->department,
                'admission_session' => $this->admission_session,
                'graduation_session' => $this->graduation_session,
                'matric_number' => $this->matric_number,
            ],
        );

        session()->flash('success', 'Academic information saved successfully.');
    }

    public function render()
    {
        return view('livewire.member-academic-form', [
            'programs' => Program::all(),
            'programTypes' => ProgramType::all(),
            'departments' => Department::all(),
        ]);
    }
};
?>

<div>
    {{-- Because you are alive, everything is possible. - Thich Nhat Hanh --}}
</div>

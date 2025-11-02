<?php

namespace App\Http\Livewire;

use App\Models\StudentRecord as Student;
use App\User;
use App\Models\MyClass;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalStudents;
    public $totalTeachers;
    public $totalAdmins;
    public $totalParents;
    public $studentsPerGrade;
    public $userTypesData;

    public function mount()
    {
        $this->totalStudents = User::where('user_type', 'student')->count();
        $this->totalTeachers = User::where('user_type', 'teacher')->count();
        $this->totalAdmins = User::where('user_type', 'admin')->count();
        $this->totalParents = User::where('user_type', 'parent')->count();

        // Students per grade data for chart
        $this->studentsPerGrade = MyClass::withCount('student_record')->get()->map(function($class) {
            return [
                'name' => $class->name,
                'students_count' => $class->student_record_count
            ];
        });

        // User types distribution data for chart
        $this->userTypesData = [
            ['label' => __('msg.students'), 'count' => $this->totalStudents],
            ['label' => __('msg.teachers'), 'count' => $this->totalTeachers],
            ['label' => __('msg.administrators'), 'count' => $this->totalAdmins],
            ['label' => __('msg.parents'), 'count' => $this->totalParents],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

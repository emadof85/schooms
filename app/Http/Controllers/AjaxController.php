<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Models\StudentRecord;
use App\Repositories\MyClassRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected $my_class;

    public function __construct(MyClassRepo $my_class)
    {
        $this->my_class = $my_class;
    }

    public function get_class_sections($class_id)
    {
        $sections = $this->my_class->getClassSections($class_id);
        return $sections = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function get_class_subjects($class_id)
    {
        $sections = $this->my_class->getClassSections($class_id);
        $subjects = $this->my_class->findSubjectByClass($class_id);

        if(Qs::userIsTeacher()){
            $subjects = $this->my_class->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }

        $d['sections'] = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
        $d['subjects'] = $subjects->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();

        return $d;
    }

    public function get_educational_stage_classes($educational_stage_id)
    {
        $classes = $this->my_class->all()->where('class_type_id', $educational_stage_id);

        return $classes->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function filterStudents(Request $request)
    {
        $grade = $request->grade;
        $class_id = $request->class_id;
        $section = $request->section;
        $recipientType = $request->recipient_type ?? 'students';

        $html = '';

        // Handle students
        if ($recipientType === 'students' || $recipientType === 'both') {
            $query = StudentRecord::with(['user', 'my_class', 'section']);

            if ($grade) {
                $query->whereHas('my_class', function ($q) use ($grade) {
                    $q->where('class_type_id', $grade);
                });
            }

            if ($class_id) {
                $query->where('my_class_id', $class_id);
            }

            if ($section) {
                $query->where('section_id', $section);
            }

            $students = $query->get();

            if(count($students) > 0) {
                $html .= '<div class="mb-3"><h6 class="text-primary">Students</h6>';
                foreach($students as $student) {
                    $html .= '<div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="selectedStudents" value="' . $student->id . '" id="student' . $student->id . '">
                        <label class="form-check-label" for="student' . $student->id . '">
                            <strong>' . $student->user->name . '</strong> - ' . $student->adm_no . ' (' . $student->my_class->name . ' - ' . $student->section->name . ')
                            <br>
                            ' . ($student->user->email ? '<small class="text-muted">' . $student->user->email . '</small>' : '<small class="text-danger">No email address</small>') . '
                        </label>
                    </div>';
                }
                $html .= '</div>';
            }
        }

        // Handle parents
        if ($recipientType === 'parents' || $recipientType === 'both') {
            $query = StudentRecord::with(['my_parent', 'my_class', 'section']);

            if ($grade) {
                $query->whereHas('my_class', function ($q) use ($grade) {
                    $q->where('class_type_id', $grade);
                });
            }

            if ($class_id) {
                $query->where('my_class_id', $class_id);
            }

            if ($section) {
                $query->where('section_id', $section);
            }

            $parents = $query->whereNotNull('my_parent_id')
                ->with(['my_parent'])
                ->get()
                ->filter(function ($student) {
                    return $student->my_parent;
                })
                ->unique('my_parent_id');

            if(count($parents) > 0) {
                $html .= '<div class="mb-3"><h6 class="text-success">Parents</h6>';
                foreach($parents as $parent) {
                    $html .= '<div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="selectedParents" value="' . $parent->id . '" id="parent' . $parent->id . '">
                        <label class="form-check-label" for="parent' . $parent->id . '">
                            <strong>' . $parent->my_parent->name . '</strong> (Parent of ' . $parent->user->name . ')
                            <br>
                            ' . ($parent->my_parent->email ? '<small class="text-muted">' . $parent->my_parent->email . '</small>' : '<small class="text-danger">No email address</small>') . '
                        </label>
                    </div>';
                }
                $html .= '</div>';
            }
        }

        if(empty($html)) {
            $html = '<p class="text-muted">No recipients found for the selected filters.</p>';
        }

        return response()->json(['html' => $html]);
    }

}

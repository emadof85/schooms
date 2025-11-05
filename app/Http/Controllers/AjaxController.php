<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Models\StudentRecord;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected $loc, $my_class;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class)
    {
        $this->loc = $loc;
        $this->my_class = $my_class;
    }

    public function get_lga($state_id)
    {
//        $state_id = Qs::decodeHash($state_id);
//        return ['id' => Qs::hash($q->id), 'name' => $q->name];

        $lgas = $this->loc->getLGAs($state_id);
        return $data = $lgas->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
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

        $html = '';
        foreach($students as $student) {
            $html .= '<div class="form-check">
                <input class="form-check-input" type="checkbox" value="' . $student->id . '" id="student' . $student->id . '">
                <label class="form-check-label" for="student' . $student->id . '">
                    ' . $student->user->name . ' - ' . $student->adm_no . ' (' . $student->my_class->name . ' - ' . $student->section->name . ')
                    ' . ($student->user->phone ? '<small class="text-muted">' . $student->user->phone . '</small>' : '<small class="text-danger">No phone number</small>') . '
                </label>
            </div>';
        }

        if(empty($html)) {
            $html = '<p class="text-muted">No students found for the selected filters.</p>';
        }

        return response()->json(['html' => $html]);
    }

}

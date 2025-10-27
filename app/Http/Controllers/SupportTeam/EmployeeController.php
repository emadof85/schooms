<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\Bus\EmployeeRequest;
use App\Repositories\EmployeeRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employee;

    public function __construct(EmployeeRepo $employee)
    {
        $this->employee = $employee;
    }

    /************* EMPLOYEES ***********/

    public function index()
    {
        $d['employees'] = $this->employee->getAllEmployees();
        return view('pages.support_team.employees.index', $d);
    }

    public function create()
    {
        return view('pages.support_team.employees.create');
    }

    public function store(EmployeeRequest $req)
    {
        $data = $req->all();
        $this->employee->createEmployee($data);
        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['employee'] = $this->employee->findEmployee($id);
        return view('pages.support_team.employees.edit', $d);
    }

    public function update(EmployeeRequest $req, $id)
    {
        $data = $req->all();
        $this->employee->updateEmployee($id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->employee->deleteEmployee($id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
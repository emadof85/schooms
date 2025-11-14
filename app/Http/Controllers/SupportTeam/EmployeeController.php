<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\Bus\EmployeeRequest;
use App\Repositories\EmployeeRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;

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

    /************* ADDITIONAL METHODS ***********/

    public function drivers()
    {
        $d['drivers'] = $this->employee->getDrivers();
        return view('pages.support_team.employees.drivers', $d);
    }

    public function expiredLicenses()
    {
        $d['employees'] = $this->employee->getEmployeesWithExpiredLicenses();
        return view('pages.support_team.employees.expired_licenses', $d);
    }

    public function activate($id)
    {
        $success = $this->employee->activateEmployee($id);
        
        if (!$success) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        
        return back()->with('flash_success', __('Employee activated successfully'));
    }

    public function deactivate($id)
    {
        $success = $this->employee->deactivateEmployee($id);
        
        if (!$success) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        
        return back()->with('flash_success', __('Employee deactivated successfully'));
    }
}
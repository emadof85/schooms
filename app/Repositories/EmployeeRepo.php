<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepo
{
    /************* EMPLOYEES ***********/

    public function getAllEmployees()
    {
        return Employee::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getActiveEmployees()
    {
        return Employee::active()->with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getDrivers()
    {
        return Employee::drivers()->active()->with('user')->orderBy('created_at', 'desc')->get();
    }

    public function createEmployee($data)
    {
        return Employee::create($data);
    }

    public function updateEmployee($id, $data)
    {
        return Employee::find($id)->update($data);
    }

    public function deleteEmployee($id)
    {
        return Employee::destroy($id);
    }

    public function findEmployee($id)
    {
        return Employee::with('user')->findOrFail($id);
    }
}
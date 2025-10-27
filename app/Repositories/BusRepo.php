<?php

namespace App\Repositories;

use App\Models\Bus;
use App\Models\BusDriver;
use App\Models\BusRoute;
use App\Models\BusStop;
use App\Models\BusAssignment;
use App\Models\StudentBusAssignment;

class BusRepo
{
    /************* BUSES ***********/

    public function getAllBuses()
    {
        return Bus::with(['currentDriver.employee'])->orderBy('bus_number')->get();
    }

    public function getActiveBuses()
    {
        return Bus::active()->with(['currentDriver.employee'])->orderBy('bus_number')->get();
    }

    public function createBus($data)
    {
        return Bus::create($data);
    }

    public function updateBus($id, $data)
    {
        return Bus::find($id)->update($data);
    }

    public function deleteBus($id)
    {
        return Bus::destroy($id);
    }

    public function findBus($id)
    {
        return Bus::with(['currentDriver.employee', 'busAssignments.busRoute'])->findOrFail($id);
    }


    /************* BUS DRIVERS ***********/

    public function getAllBusDrivers()
    {
        return BusDriver::with(['employee', 'bus'])->orderBy('assignment_date', 'desc')->get();
    }

    public function getActiveBusDrivers()
    {
        return BusDriver::active()->with(['employee', 'bus'])->orderBy('assignment_date', 'desc')->get();
    }

    public function createBusDriver($data)
    {
        return BusDriver::create($data);
    }

    public function updateBusDriver($id, $data)
    {
        return BusDriver::find($id)->update($data);
    }

    public function deleteBusDriver($id)
    {
        return BusDriver::destroy($id);
    }

    public function findBusDriver($id)
    {
        return BusDriver::with(['employee', 'bus'])->findOrFail($id);
    }

    public function getDrivers()
    {
        return $this->getActiveBusDrivers();
    }

    /************* BUS ROUTES ***********/

    public function getAllBusRoutes()
    {
        return BusRoute::with('busStops')->orderBy('route_name')->get();
    }

    public function getActiveBusRoutes()
    {
        return BusRoute::active()->with('busStops')->orderBy('route_name')->get();
    }

    public function createBusRoute($data)
    {
        return BusRoute::create($data);
    }

    public function updateBusRoute($id, $data)
    {
        return BusRoute::find($id)->update($data);
    }

    public function deleteBusRoute($id)
    {
        return BusRoute::destroy($id);
    }

    public function findBusRoute($id)
    {
        \Log::info("Finding bus route with ID: " . $id);
        return BusRoute::with('busStops')->find($id);
    }

    /************* BUS STOPS ***********/

    public function getAllBusStops()
    {
        return BusStop::with('busRoute')->orderBy('bus_route_id')->orderBy('order')->get();
    }

    public function getBusStopsByRoute($routeId)
    {
        return BusStop::where('bus_route_id', $routeId)->orderBy('order')->get();
    }

    public function createBusStop($data)
    {
        return BusStop::create($data);
    }

    public function updateBusStop($id, $data)
    {
        return BusStop::find($id)->update($data);
    }

    public function deleteBusStop($id)
    {
        return BusStop::destroy($id);
    }

    public function findBusStop($id)
    {
        return BusStop::with('busRoute')->findOrFail($id);
    }

    /************* BUS ASSIGNMENTS ***********/

    public function getAllBusAssignments()
    {
        return BusAssignment::with(['bus.currentDriver.employee', 'busRoute'])->orderBy('assignment_date', 'desc')->get();
    }

    public function getActiveBusAssignments()
    {
        return BusAssignment::active()->whereNotNull('bus_route_id')->with(['bus', 'busRoute'])->orderBy('assignment_date', 'desc')->get();
    }

    public function createBusAssignment($data)
    {
        return BusAssignment::create($data);
    }

    public function updateBusAssignment($id, $data)
    {
        return BusAssignment::find($id)->update($data);
    }

    public function deleteBusAssignment($id)
    {
        return BusAssignment::destroy($id);
    }

    public function findBusAssignment($id)
    {
        return BusAssignment::with(['bus', 'busRoute'])->findOrFail($id);
    }

    /************* STUDENT BUS ASSIGNMENTS ***********/

    public function getAllStudentBusAssignments()
    {
        return StudentBusAssignment::with(['student.user', 'busAssignment.bus', 'busAssignment.busRoute', 'busStop'])->orderBy('created_at', 'desc')->get();
    }

    public function getActiveStudentBusAssignments()
    {
        return StudentBusAssignment::active()->with(['student', 'busAssignment.bus', 'busStop.busRoute'])->orderBy('created_at', 'desc')->get();
    }

    public function getStudentBusAssignmentsByStudent($studentId)
    {
        return StudentBusAssignment::where('student_record_id', $studentId)->with(['busAssignment.bus', 'busStop.busRoute'])->orderBy('created_at', 'desc')->get();
    }

    public function createStudentBusAssignment($data)
    {
        return StudentBusAssignment::create($data);
    }

    public function updateStudentBusAssignment($id, $data)
    {
        return StudentBusAssignment::find($id)->update($data);
    }

    public function deleteStudentBusAssignment($id)
    {
        return StudentBusAssignment::destroy($id);
    }

    public function findStudentBusAssignment($id)
    {
        return StudentBusAssignment::with(['student', 'busAssignment.bus', 'busStop.busRoute'])->findOrFail($id);
    }
}

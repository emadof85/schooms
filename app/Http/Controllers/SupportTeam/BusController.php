<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\Bus\BusRequest;
use App\Http\Requests\Bus\BusDriverRequest;
use App\Http\Requests\Bus\BusRouteRequest;
use App\Http\Requests\Bus\BusStopRequest;
use App\Http\Requests\Bus\BusAssignmentRequest;
use App\Http\Requests\Bus\StudentBusAssignmentRequest;
use App\Repositories\BusRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusController extends Controller
{
    protected $bus, $my_class, $student;

    public function __construct(BusRepo $bus, MyClassRepo $mc, StudentRepo $student)
    {
        $this->bus = $bus;
        $this->my_class = $mc;
        $this->student = $student;
    }

    /************* BUSES ***********/

    public function index()
    {
        $d['buses'] = $this->bus->getAllBuses();
        return view('pages.support_team.buses.index', $d);
    }

    public function create()
    {
        $d['drivers'] = $this->bus->getDrivers();
        return view('pages.support_team.buses.create', $d);
    }

    public function store(BusRequest $req)
    {
        $data = $req->all();
        $this->bus->createBus($data);
        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        Log::info("Editing bus with ID: $id");
        $d['bus'] = $this->bus->findBus($id);
        $d['drivers'] = $this->bus->getDrivers();
        return view('pages.support_team.buses.edit', $d);
    }

    public function update(BusRequest $req, $id)
    {
        $data = $req->all();
        $this->bus->updateBus($id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->bus->deleteBus($id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }


    /************* BUS DRIVERS ***********/

    public function bus_drivers()
    {
        $d['bus_drivers'] = $this->bus->getAllBusDrivers();
        $d['employees'] = app(\App\Repositories\EmployeeRepo::class)->getDrivers();
        $d['buses'] = $this->bus->getActiveBuses();
        return view('pages.support_team.bus_drivers.index', $d);
    }

    public function create_bus_driver()
    {
        $d['employees'] = app(\App\Repositories\EmployeeRepo::class)->getDrivers();
        $d['buses'] = $this->bus->getActiveBuses();
        return view('pages.support_team.bus_drivers.create', $d);
    }

    public function store_bus_driver(BusDriverRequest $req)
    {
        $data = $req->all();
        $this->bus->createBusDriver($data);
        return Qs::jsonStoreOk();
    }

    public function edit_bus_driver(\App\Models\BusDriver $bus_driver)
    {
        Log::info("Editing bus driver with ID: $bus_driver->id");
        $d['bus_driver'] = $bus_driver;
        $d['employees'] = app(\App\Repositories\EmployeeRepo::class)->getDrivers();
        $d['buses'] = $this->bus->getActiveBuses();
        return view('pages.support_team.bus_drivers.edit', $d);
    }

    public function update_bus_driver(BusDriverRequest $req, \App\Models\BusDriver $bus_driver)
    {
        $data = $req->all();
        $this->bus->updateBusDriver($bus_driver->id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy_bus_driver(\App\Models\BusDriver $bus_driver)
    {
        $this->bus->deleteBusDriver($bus_driver->id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /************* BUS ROUTES ***********/

    public function bus_routes()
    {
        $d['bus_routes'] = $this->bus->getAllBusRoutes();
        return view('pages.support_team.bus_routes.index', $d);
    }

    public function create_bus_route()
    {
        return view('pages.support_team.bus_routes.create');
    }

    public function store_bus_route(BusRouteRequest $req)
    {
        $data = $req->all();
        $this->bus->createBusRoute($data);
        return Qs::jsonStoreOk();
    }

    public function edit_bus_route(\App\Models\BusRoute $bus_route)
    {
        $d['bus_route'] = $bus_route;
        return view('pages.support_team.bus_routes.edit', $d);
    }

    public function update_bus_route(BusRouteRequest $req, \App\Models\BusRoute $bus_route)
    {
        $data = $req->all();
        $this->bus->updateBusRoute($bus_route->id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy_bus_route(\App\Models\BusRoute $bus_route)
    {
        $this->bus->deleteBusRoute($bus_route->id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /************* BUS STOPS ***********/

    public function all_bus_stops()
    {
        $d['bus_stops'] = $this->bus->getAllBusStops()->load('busRoute');
        //Log::info("Loaded bus stops with routes: ");
        //Log::info(print_r($d['bus_stops'], true));
        $d['bus_routes'] = $this->bus->getAllBusRoutes();
        return view('pages.support_team.bus_stops.index', $d);
    }

    public function bus_stops($route_id)
    {
        $d['bus_route'] = $this->bus->findBusRoute($route_id);
        $d['bus_stops'] = $this->bus->getBusStopsByRoute($route_id);
        $d['bus_routes'] = $this->bus->getAllBusRoutes();
        return view('pages.support_team.bus_stops.index', $d);
    }

    public function create_bus_stop($route_id)
    {
        $d['bus_route'] = $this->bus->findBusRoute($route_id);
        return view('pages.support_team.bus_stops.create', $d);
    }

    public function store_bus_stop(BusStopRequest $req)
    {
        $data = $req->all();
        $this->bus->createBusStop($data);
        return Qs::jsonStoreOk();
    }

    public function edit_bus_stop($stop_id)
    {
        $d['bus_stop'] = $this->bus->findBusStop($stop_id);
        $d['bus_routes'] = $this->bus->getAllBusRoutes();
        return view('pages.support_team.bus_stops.edit', $d);
    }

    public function update_bus_stop(BusStopRequest $req, $stop_id)
    {
        $data = $req->all();
        $this->bus->updateBusStop($stop_id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy_bus_stop($stop_id)
    {
        $this->bus->deleteBusStop($stop_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /************* BUS ASSIGNMENTS ***********/

    public function bus_assignments()
    {
        $d['bus_assignments'] = $this->bus->getAllBusAssignments();
        $d['buses'] = $this->bus->getActiveBuses();
        $d['bus_routes'] = $this->bus->getActiveBusRoutes();
        $d['bus_drivers'] = $this->bus->getAllBusDrivers();
        return view('pages.support_team.bus_assignments.index', $d);
    }

    public function create_bus_assignment()
    {
        $d['buses'] = $this->bus->getActiveBuses();
        $d['bus_routes'] = $this->bus->getActiveBusRoutes();
        return view('pages.support_team.bus_assignments.create', $d);
    }

    public function store_bus_assignment(BusAssignmentRequest $req)
    {
        $data = $req->all();
        $this->bus->createBusAssignment($data);
        return Qs::jsonStoreOk();
    }

    public function edit_bus_assignment($assignment_id)
    {
        $d['bus_assignment'] = $this->bus->findBusAssignment($assignment_id);
        $d['buses'] = $this->bus->getActiveBuses();
        $d['bus_routes'] = $this->bus->getActiveBusRoutes();
        $d['bus_drivers'] = $this->bus->getAllBusDrivers();
        return view('pages.support_team.bus_assignments.edit', $d);
    }

    public function update_bus_assignment(BusAssignmentRequest $req, $assignment_id)
    {
        $data = $req->all();
        $this->bus->updateBusAssignment($assignment_id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy_bus_assignment($assignment_id)
    {
        $this->bus->deleteBusAssignment($assignment_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /************* STUDENT BUS ASSIGNMENTS ***********/

    public function student_bus_assignments()
    {
        $d['student_bus_assignments'] = $this->bus->getAllStudentBusAssignments();
        $d['students'] = $this->student->getAll();
        $d['bus_assignments'] = $this->bus->getActiveBusAssignments();
        $d['bus_stops'] = $this->bus->getBusStopsByRoute(null); // Will be loaded via AJAX
        return view('pages.support_team.student_bus_assignments.index', $d);
    }

    public function create_student_bus_assignment()
    {
        $d['students'] = $this->student->getAll();
        $d['buses'] = $this->bus->getActiveBuses();
        $d['bus_stops'] = $this->bus->getBusStopsByRoute(null); // Will be loaded via AJAX
        return view('pages.support_team.student_bus_assignments.create', $d);
    }

    public function store_student_bus_assignment(StudentBusAssignmentRequest $req)
    {
        $data = $req->all();
        $this->bus->createStudentBusAssignment($data);
        return Qs::jsonStoreOk();
    }

    public function edit_student_bus_assignment($student_assignment_id)
    {
        $d['student_bus_assignment'] = $this->bus->findStudentBusAssignment($student_assignment_id);
        $d['students'] = $this->student->getAll();
        $d['bus_assignments'] = $this->bus->getActiveBusAssignments();
        $d['bus_stops'] = $this->bus->getAllBusStops(); // Load all bus stops for now
        return view('pages.support_team.student_bus_assignments.edit', $d);
    }

    public function update_student_bus_assignment(StudentBusAssignmentRequest $req, $student_assignment_id)
    {
        $data = $req->all();
        $this->bus->updateStudentBusAssignment($student_assignment_id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy_student_bus_assignment($student_assignment_id)
    {
        $this->bus->deleteStudentBusAssignment($student_assignment_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    public function student_bus_assignments_by_student($student_id)
    {
        $d['student'] = $this->student->findStudent($student_id);
        $d['student_bus_assignments'] = $this->bus->getStudentBusAssignmentsByStudent($student_id);
        return view('pages.support_team.student_bus_assignments.student_assignments', $d);
    }
}

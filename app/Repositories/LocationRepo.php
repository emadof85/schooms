<?php

namespace App\Repositories;

use App\Models\Nationality;
use App\Models\State;
use App\Models\Lga;

class LocationRepo
{
    public function getStates()
    {
        return State::all();
    }

    public function getAllStates()
    {
        return State::orderBy('name', 'asc')->get();
    }

    public function getAllNationals()
    {
        // Order by the JSON translation for the current app locale when possible
        $locale = app()->getLocale();

        try {
            // MySQL JSON_EXTRACT requires proper quoting; build the expression safely
            $path = "$.\"" . $locale . "\""; // produces $."en" or similar
            $expr = "JSON_UNQUOTE(JSON_EXTRACT(name, '" . $path . "')) ASC";

            return Nationality::orderByRaw($expr)->get();
        } catch (\Exception $e) {
            // Fallback to default ordering (may order by JSON blob)
            return Nationality::orderBy('name', 'asc')->get();
        }
    }

    public function getLGAs($state_id)
    {
        return Lga::where('state_id', $state_id)->orderBy('name', 'asc')->get();
    }

}
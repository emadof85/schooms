<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class MigrateNationalityNameToJson extends Migration
{
    public function up()
    {
        // Some older installs may have stored plain strings in the json column.
        // This migration will convert plain string values into JSON objects with 'en' key.

        $rows = DB::table('nationalities')->select('id', 'name')->get();

        foreach ($rows as $r) {
            $name = $r->name;

            // If name is already JSON object/array, skip
            if (is_null($name)) continue;

            // If name is a string that starts with { or [ assume JSON already
            $trim = ltrim($name);
            if (strlen($trim) > 0 && ($trim[0] === '{' || $trim[0] === '[')) {
                continue;
            }

            // Otherwise, write JSON with en key
            $json = json_encode(['en' => $name], JSON_UNESCAPED_UNICODE);
            DB::table('nationalities')->where('id', $r->id)->update(['name' => $json]);
        }
    }

    public function down()
    {
        // No-op: reversing would be lossy, so we leave data as-is
    }
}

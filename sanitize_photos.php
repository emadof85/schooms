<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$users = DB::table('users')->where('photo', 'like', '%APP_URL=%')->get();
foreach ($users as $u) {
    $old = $u->photo;
    $new = preg_replace('#https?://[^/]+/APP_URL=https?://[^/]+/#', '', $old);
    if ($new === $old) {
        // fallback: if it contains APP_URL= remove up to the last slash
        $new = preg_replace('#.*APP_URL=#', '', $old);
    }
    if ($new === $old) {
        // last fallback: set to default public asset
        $new = 'global_assets/images/user.png';
    }
    echo "Fixing user {$u->id}: \n  old: {$old}\n  new: {$new}\n";
    DB::table('users')->where('id', $u->id)->update(['photo' => $new]);
}
echo "Done.\n";

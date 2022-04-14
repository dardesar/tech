<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class CheckSystem
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $dbTableError = "You did not run migration. Run `php artisan migrate` command from CLI";
        $dbSeederError = "You did not run db seeder. Run `php artisan db:seed` command from CLI";

        try {

            $hasMigration = Schema::hasTable('roles');

            if(!$hasMigration) {
                die($dbTableError);
            }

        } catch (\Exception $e) {
            die('You did not run migration. Run `php artisan migrate` command from CLI');
        }

        try {

            if(!Role::get()->first()) {
                die($dbSeederError);
            }

        } catch (\Exception $e) {
            die($dbSeederError);
        }

        return $next($request);
    }
}

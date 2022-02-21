<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseTransaction
{

    public function handle(Request $request, Closure $next)
    {
        return DB::transaction(function () use ($request, $next) {
            $result = $next($request);
            DB::commit();
            return $result;
        });
    }
}

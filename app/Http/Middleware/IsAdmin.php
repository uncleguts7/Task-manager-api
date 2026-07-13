<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_role = $request->user()->role;

        if($user_role === 'admin')
        {
            return $next($request);
        }else{
            return response()->json(["message"=> "unauthrized!"], 403);
        }
    }
}

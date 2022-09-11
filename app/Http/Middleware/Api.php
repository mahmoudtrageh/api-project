<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Api
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token');
        if (!$token) {
            return response()->json(
                [
                    'status' => [
                        'type' => '0',
                        'title' => 'no token'
                    ]
                ]
            );
        } elseif ($user = User::where('api_token', $token)->first()) {
            return $next($request);           
        }
        return response()->json(
            [
                'status' => [
                    'type' => '0',
                    'title' => 'invalid token'
                ]
            ]
        );

    }
}

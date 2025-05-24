<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $role = $user->role;

            // Redirect based on the role if not on the correct route
            if ($role === 'Admin' && !$request->is('admin/*')) {
                return redirect()->route('adminDashboard');
            } elseif ($role === 'President' && !$request->is('president/*')) {
                return redirect()->route('presidentDashboard');
            } elseif ($role === 'Facilities In-charge' && !$request->is('department/*')) {
                return redirect()->route('departmentDashboard');
            } elseif ($role === 'Equipment In-charge' && !$request->is('equipment/*')) {
                return redirect()->route('equipmentDashboard');
            } elseif ($role === 'Custodian' && !$request->is('custodian/*')) {
                return redirect()->route('custodianDashboard');
            } elseif ($role === 'Employee' && !$request->is('employee/*')) {
                return redirect()->route('employeeDashboard');
            }

            // Allow request to proceed if the user is on the correct route
            return $next($request);
        }

        // Redirect to login if not authenticated
        return redirect()->route('viewLogin');
    }
}

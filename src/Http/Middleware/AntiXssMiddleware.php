<?php

namespace Kabeer\LaravelAntiXss\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kabeer\LaravelAntiXss\Facades\AntiXss;

class AntiXssMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $excludedRoutes = config('anti-xss.middleware.excluded_routes', []);
        $excludedParameters = config('anti-xss.middleware.excluded_parameters', []);

        // Check if current route is excluded
        foreach ($excludedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        $input = $request->all();
        $cleanInput = $this->cleanInput($input, $excludedParameters);
        $request->merge($cleanInput);

        return $next($request);
    }

    private function cleanInput(array $input, array $excludedParameters): array
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $excludedParameters)) {
                continue;
            }

            if (is_array($value)) {
                $input[$key] = $this->cleanInput($value, $excludedParameters);
            } else {
                $input[$key] = AntiXss::clean($value);
            }
        }

        return $input;
    }
}

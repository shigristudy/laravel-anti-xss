<?php

namespace Kabeer\LaravelAntiXss\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Kabeer\LaravelAntiXss\Facades\AntiXss;

class XssProtection
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value) && !$this->isExcludedParameter($key)) {
                $value = AntiXss::clean($value);
            }
        });

        $request->merge($input);

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        $excludedRoutes = config('anti-xss.middleware.excluded_routes', []);

        foreach ($excludedRoutes as $route) {
            if (Str::is($route, $request->path())) {
                return true;
            }
        }

        return false;
    }

    protected function isExcludedParameter(string $key): bool
    {
        $excludedParameters = config('anti-xss.middleware.excluded_parameters', []);

        return in_array($key, $excludedParameters, true);
    }
}

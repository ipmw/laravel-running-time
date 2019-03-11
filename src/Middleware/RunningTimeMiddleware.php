<?php

namespace RunningTime\Middleware;

use Closure;

class RunningTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response =  $next($request);

        $path = storage_path('logs/runningtime/');
        if (!is_dir($path)) {
            mkdir($path);
        }

        if (!app()->runningInConsole()) {
            $log = [
                'time' => round(microtime(true) - LARAVEL_START, 2),
                'path' => $request->path(),
                'params' => $request->all(),
                't' => date('Y-m-d H:i:s'),
            ];

            $logJson = json_encode($log, JSON_UNESCAPED_UNICODE);
            $logFile = date("Y-m-d") . '.log';

            file_put_contents($path . '/' . $logFile, $logJson . "\n", FILE_APPEND);
        }

        return $response;
    }
}

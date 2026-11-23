<?php

namespace App;

use Illuminate\Foundation\Application;

/**
 * Custom Application class for Vercel deployment.
 * Vercel's filesystem is read-only except /tmp,
 * so we redirect bootstrap/cache to /tmp/bootstrap/cache.
 */
class VercelApplication extends Application
{
    public function bootstrapPath($path = '')
    {
        $base = '/tmp/bootstrap';
        return $base . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

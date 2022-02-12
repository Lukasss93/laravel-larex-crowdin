<?php

if (! function_exists('lang_path')) {
    /**
     * Get the path to the language folder.
     *
     * @param  string  $path
     * @return string
     */
    function lang_path($path = '')
    {
        return app()->langPath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if(! function_exists('csv_path')){
    function csv_path($relative = false): string
    {
        $path = config('larex.csv.path');

        if ($relative) {
            $path = str_replace(base_path(), '', $path);
            $path = ltrim($path, '/\\');

            return $path;
        }

        return $path;
    }
}

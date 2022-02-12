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

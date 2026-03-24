<?php

namespace App\Services;

class VersionService
{
    /**
     * Get the current application version from composer.json
     * 
     * @return string
     */
    public static function currentVersion()
    {
        $path = base_path('composer.json');
        if (!file_exists($path)) {
            return '1.0.0';
        }
        
        $composer = json_decode(file_get_contents($path), true);
        return $composer['version'] ?? '1.0.0';
    }

    /**
     * Get the structured changelog data
     * 
     * @return array
     */
    public static function getChangelog()
    {
        $path = base_path('changelog.json');
        if (!file_exists($path)) {
            return [];
        }
        
        return json_decode(file_get_contents($path), true);
    }
}

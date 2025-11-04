<?php

namespace CCSCalculator\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Autoloader
{
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    private static function autoload($class)
    {
        // Only autoload our namespace
        if (strpos($class, 'CCSCalculator\\') !== 0) {
            return;
        }

        // Get base directory
        $base_dir = defined('CCS_CALCULATOR_PLUGIN_DIR') 
            ? CCS_CALCULATOR_PLUGIN_DIR 
            : dirname(__DIR__) . '/';
            
        $relative_class = substr($class, strlen('CCSCalculator\\'));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
}

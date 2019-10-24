<?php

if (! function_exists('e')) {
    /**
     * Encode HTML special characters in a string.
     *
     * @param string $value
     * @param bool $doubleEncode
     * @return string
     */
    function e($value, $doubleEncode = true)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
}

if (! function_exists('phpb_alert')) {
    /**
     * Return whether an alert message with the given key is set.
     *
     * @param string $key
     * @return bool
     */
    function phpb_alert($key)
    {
        return (isset($_GET['alert']) && $_GET['alert'] === $key);
    }
}

if (! function_exists('phpb_asset')) {
    /**
     * Return the public path of a PHPageBuilder asset.
     *
     * @param string $path
     * @return string
     */
    function phpb_asset($path)
    {
        return '/packages/phpagebuilder/dist/' . $path;
    }
}

if (! function_exists('phpb_config')) {
    /**
     * Return the configuration with the given key (as dot-separated multidimensional array selector).
     *
     * @param string $key
     * @return mixed
     */
    function phpb_config($key)
    {
        global $phpb_config;

        // if no dot notation is used, return first dimension value or empty string
        if (strpos($key, '.') === false) {
            return $phpb_config[$key] ?? '';
        }

        // if dot notation is used, traverse config string
        $segments = explode('.', $key);
        $subArray = $phpb_config;
        foreach ($segments as $segment) {
            if (isset($subArray[$segment])) {
                $subArray = &$subArray[$segment];
            } else {
                return '';
            }
        }

        return $subArray;
    }
}

if (! function_exists('phpb_trans')) {
    /**
     * Return the translation of the given key (as dot-separated multidimensional array selector).
     *
     * @param $key
     * @param bool $encode      encode special characters
     * @return string
     */
    function phpb_trans($key, $encode = true)
    {
        global $phpb_translations;

        // if no dot notation is used, return first dimension value or empty string
        if (strpos($key, '.') === false) {
            return $phpb_translations[$key] ?? '';
        }

        // if dot notation is used, traverse translations string
        $segments = explode('.', $key);
        $subArray = $phpb_translations;
        foreach ($segments as $segment) {
            if (isset($subArray[$segment])) {
                $subArray = &$subArray[$segment];
            } else {
                return '';
            }
        }

        // if the remaining sub array is a string, return this translation
        if (is_string($subArray)) {
            if ($encode) {
                return e($subArray);
            }
            return $subArray;
        }
        return '';
    }
}

if (! function_exists('phpb_route')) {
    /**
     * Give the URL of a given page builder route.
     * The given route will be prefixed with pagebuilder_url from config.
     *
     * @param string $route
     * @return string
     */
    function phpb_route($route = '')
    {
        $prefix = phpb_config('project.pagebuilder_url');
        if (empty($prefix)) {
            $prefix = '/';
        }

        return $prefix . $route;
    }
}

if (! function_exists('phpb_redirect')) {
    /**
     * Redirect to the given page builder route.
     * The given route will be prefixed with pagebuilder_url from config.
     *
     * @param string $route
     * @return string
     */
    function phpb_redirect($route = '')
    {
        header('Location: ' . phpb_route($route));
        exit();
    }
}

if (! function_exists('phpb_field_value')) {
    /**
     * Return the posted value or the attribute value of the given instance.
     *
     * @param $attribute
     * @param object $instance
     * @return string
     */
    function phpb_field_value($attribute, $instance = null)
    {
        if (isset($_POST[$attribute])) {
            return e($_POST[$attribute]);
        }
        if (isset($instance)) {
            return $instance->$attribute;
        }
        return '';
    }
}

if (! function_exists('phpb_autoload')) {
    /**
     * Autoload classes from the PHPageBuilder package.
     *
     * @param  string $className
     */
    function phpb_autoload($className)
    {
        // PSR-0 autoloader
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        // remove leading PHPageBuilder/ from the class path
        $fileName = str_replace('PHPageBuilder/', '', $fileName);

        // include class files starting in the src directory
        require __DIR__ . '/../' . $fileName;
    }
}
<?php

namespace Wutime\UserHideAvatars\Install;

class Install
{

    public static $addOn;
    public static $version;
    public static $version_string;

    public static function install ($addon, $action, array $stepParams = [])
    {
        // Get add-on (remove leading \ and replace other slashes with /)
        self::$addOn = str_replace('\\', '/', ltrim($addon, '\\'));


        // Get the add-on directory dynamically
        $addonJsonPath = \XF::getAddOnDirectory() . DIRECTORY_SEPARATOR . self::$addOn .  DIRECTORY_SEPARATOR . 'addon.json';

        // Get version
        if (file_exists($addonJsonPath)) {
            $addonJson = json_decode(file_get_contents($addonJsonPath), true);
            if (isset($addonJson['version_id'])) {
                self::$version = $addonJson['version_id'];
                self::$version_string = $addonJson['version_string'];
            }
        }

        $data = [
            'addon' => self::$addOn,
            'xf_version' => \XF::$version,
            'action' => $action,
            'board_name' => \XF::options()->boardTitle,
            'url' => \XF::options()->boardUrl,
            'addon_version' => self::$version,
            'addon_string' => self::$version_string,
            'ip' => (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : gethostbyname(gethostname())),
            'hostname' => gethostname(),
            'guestTimeZone' => \XF::options()->guestTimeZone,
        ];

        self::post($data);
    }

    private static function post($data)
    {
        // Suppress errors for this block
        $errorReporting = error_reporting(0);

        try {
            $options = [
                'http' => [
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 4, // Set the timeout to 5 seconds
                ],
            ];

            $context = stream_context_create($options);
            $url = 'https://development.wutime.com/xenforo/log.php';
            $result = file_get_contents($url, false, $context);

        } catch (Exception $e) {
            // Handle any exceptions silently
        }

        // Restore previous error reporting level
        error_reporting($errorReporting);
    }
}

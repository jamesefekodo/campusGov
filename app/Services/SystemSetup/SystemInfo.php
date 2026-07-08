<?php

if (!function_exists('getSystenInfo')) {
    function getSystemInfo()
    {
        return [
            'product_name' => 'CampusGov',
            'description' => 'Management system for student associations',
            'system_type' => 'Web Script',
            'version_number' => '0.1',

            'requirements' => [
                'php_version' => 8.2,
                'mysql_version' => 10,
            ],

        ];
    }
}

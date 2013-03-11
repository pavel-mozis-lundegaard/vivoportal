<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overridding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will be typically INCLUDED in your source
 * control, so do NOT include passwords or other sensitive information in this
 * file.
 */

return array(
    'phpSettings'   => array(
        'display_startup_errors'        => false,
        'display_errors'                => false,
        'error_reporting'               => E_ALL | E_STRICT,
//        'max_execution_time'		    => 60,
        'date.timezone'                 => 'Europe/Prague',
        'mbstring.internal_encoding'    => 'UTF-8',
    ),
);

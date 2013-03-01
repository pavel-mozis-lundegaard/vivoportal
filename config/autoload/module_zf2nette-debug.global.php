<?php
/**
 * Default configuration for module ZF2NetteDebug.
 *
 * Nette Debug is disabled.
 *
 */
return array(
    'nette_debug' => array(
        'enabled'      => false,
        'mode'         => true,        // true = production|false = development|null = autodetect|IP address(es) csv/array
        'bar'          => true,        // bool = enabled|Toggle nette diagnostics bar.
        'strict'       => true,        // bool = cause immediate death|int = matched against error severity
        'log'          => "",          // bool = enabled|Path to directory eg. data/logs
        'email'        => "",          // in production mode notifies the recipient
        'max_depth'    => 3,           // nested levels of array/object
        'max_len'      => 150,         // max string display length
    ),
);

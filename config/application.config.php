<?php
return array(
    'modules' => array(
        'DluPhpSettings',
        'Vivo',
        'ZF2NetteDebug',
        'ZendSearch',
        'ApacheSolr',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);

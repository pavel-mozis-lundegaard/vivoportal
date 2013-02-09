<?php
/**
 * Bootstrap module.
 *
 */
return array(
    'ui' => array (
        //configuration of ui components
        'Vivo\UI\Page' => array (
            'links' => array (
                array(
                    'rel'  => 'stylesheet',
                    'href' => '/.Bootstrap2_3_0.resource/css/bootstrap.css',
                    'type' => 'text/css',
                    'media' => 'screen'
                ),
            ),
            'scripts' => array (
                array(
                    'src' => '/.Bootstrap2_3_0.resource/js/bootstrap.js',
                    'type' => 'text/javascript',
                ),
            ),
        ),
    ),
);

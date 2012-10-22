<?php
/**
 * Main CMS config, can be splited to the topic related files in future.  
 * 
 * @author kormik
 */

return array(
    'router' => array(
        'routes' => array(
        	// routes are checked in reverse order
        	'cms' => array(
           				'type' => 'Zend\Mvc\Router\Http\Regex',
        				'options' => array(
        						'regex'	=> '/(?<path>.*)',
        						'spec'	=> '/%path%',
        						'defaults' => array(
        								'controller' => 'Vivo\Controller\CMSFront',
        								'path' => '',
                                        //TODO Remove host default
                                        'host'    => 'www.my-site-alias.com',
        						),
                ),
            ),
        	'resources' => array(
        				'type' => 'Zend\Mvc\Router\Http\Regex',
        				'options' => array(
        						'regex'	=> '/resources/(?<module>.*?)/(?<path>.*)',
        						'spec'	=> '/resources/%module%/%path%',
        						'defaults' => array(
        								'controller' => 'Vivo\Controller\ResourceFront',
        								'path' => '',
        								'module' => '',
        						),
        				),
       		),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Vivo\Controller\CMSFront' => 'Vivo\Controller\CMSFrontController',
            'Vivo\Controller\ResourceFront' => 'Vivo\Controller\ResourceFrontController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'vivo/index/index' => __DIR__ . '/../view/vivo/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'vivo'      => array(
        //Vmodules configuration
        'modules'  => array(
            //Storage config
            'storage'   => array(
                'class'     => '\Vivo\Storage\LocalFileSystemStorage',
                'options'   => array(
                    'root'      => __DIR__ . '/../../../vmodule',
                ),
            ),
            //Name of stream (protocol) which will be registered for Vmodule source file access in Storage
            'stream_name'   => 'vmodule',
            //Vmodule paths in Vmodule Storage
            'module_paths'             => array(
                '/',
            ),
        ),
    ),
);

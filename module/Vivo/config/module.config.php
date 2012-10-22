<?php
/**
 * Main CMS config, can be splited to the topic related files in future.
 *
 */
return array(
    'router' => array(
        'routes' => array(
            'vivo' => array(
                //only add hostname to routermatch
                'type' => 'Vivo\Router\Hostname',
                'may_terminate' => false,
                'child_routes' => array(

                    'cms' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/(?<path>.*)',
                            'spec'    => '/%path%',
                            'defaults' => array(
                                'controller' => 'CMSFront',
                                'path' => '',
                            ),
                        ),
                    ),

                    'resources' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/resources/(?<module>.*?)/(?<path>.*)',
                            'spec'    => '/resources/%module%/%path%',
                            'defaults' => array(
                                'controller' => 'ResourceFront',
                                'path' => '',
                                'module' => '',
                            ),
                        ),
                    ),
                    'backend' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/system/manager/(?<path>.*)',
                            'spec'    => '/system/manager/%path%',
                            'defaults' => array(
                                'controller' => 'CMSFront',
                                'path' => '',
                                'module' => '',
                            ),
                        ),
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
            'CMSFront' => 'Vivo\Controller\CMSFrontController',
            'ResourceFront' => 'Vivo\Controller\ResourceFrontController'
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
    
    'di' => array(
    	'instance' => array (
    		'alias' => array (
    			'cms' => 'Vivo\Fake\CMS',
    			'viewModel' =>  'Vivo\View\Model\UIViewModel',
    		),
    		
    		'viewModel' => array (
    				'shared' => false,
    		),
    		'Vivo\CMS\ComponentFactory' => array (
	    		'parameters' => array (
	    			'cms' => 'cms',
	    		),
    		),
	    	'Vivo\CMS\UI\Page' => array (
	    			'parameters' => array (
	    					'options' => array (
	    						'doctype' => '<!DOCTYPE html>',
							),
	    			),
	    	),
	    	'Vivo\CMS\UI\Content\Sample' => array (
	    			'parameters' => array (
	    					'options' => array (
	    							'template' => 'someTemplate.phtml',
	    					),
	    			),
			),
 			'Vivo\UI\Component' => array (
 	    			'parameters' => array (
 	    					'view' => 'viewModel',
 	    			),
 	    	),
    	), 
    ),

    'vivo'      => array(
        //Vivo Modules configuration
        'modules'  => array(
            //Storage config
            'storage'   => array(
                'class'     => 'Vivo\Storage\LocalFileSystemStorage',
                'options'   => array(
                    'root'      => __DIR__ . '/../../../vmodule',
                ),
            ),
            //Name of stream (protocol) which will be registered for Vivo Module source file access in Storage
            'stream_name'   => 'vmodule',
            //Vivo Module paths in Vivo Module Storage
            'module_paths'             => array(
                '/',
            ),
        ),
        'templates' => array (
        		'templateMap' => array(
        		//    			'Vivo\CMS\UI\Content\Sample' => 'page.phtml',
        		),
        )
        
    ),
);

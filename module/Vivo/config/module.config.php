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
    
    'di' => array(
    	'instance' => array (
    		'alias' => array (
    			'cms' => 'Vivo\Fake\CMS',
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
// 	    	'Zend\View\Resolver\AggregateResolver' => array(
// 	    			'injections' => array(
// //	    					'Zend\View\Resolver\TemplateMapResolver',
// 	    					'Zend\View\Resolver\TemplatePathStack',
// 	    			),
// 	    	),
	    	
// // 	    	'Zend\View\Resolver\TemplateMapResolver' => array(
// // 	    			'parameters' => array(
// // 	    					'map'  => array(
// // 	    							'page'      => __DIR__ . '/view/layout.phtml',
// // 	    					),
// // 	    			),
// // 	    	),
// 	    	'Zend\View\Resolver\TemplatePathStack' => array(
// 	    			'parameters' => array(
// 	    					'paths'  => array(
// 	    							'application' => __DIR__ . '/../view',
// 	    					),
// 	    			),
// 	    	),
// 	    	'Zend\View\Renderer\PhpRenderer' => array(
// 	    			'parameters' => array(
// 	    					'resolver' => 'Zend\View\Resolver\TemplatePathStack',
// 	    			),
// 	    	),
	    	
    	) 
    )
);

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
                            'regex'    => '/((?<path>.*)/)?',
                            'spec'    => '/%path%/',
                            'defaults' => array(
                                'controller' => 'CMSFront',
                                'path' => '',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'query' => array(
                                'type' => 'Query',
                            ),
                        ),
                    ),

                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.(?<source>.+)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'ResourceFront',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),

                    'resource_entity' => array(
                            'type' => 'Zend\Mvc\Router\Http\Regex',
                            'options' => array(
                                    'regex'    => '/\.entity/(?<entity>.+?)((\.path(?<path>.+)))',
                                    'spec'    => '/.entity/%entity%/.path/%path%',
                                    'defaults' => array(
                                            'controller' => 'ResourceFront',
                                            'path' => '',
                                            'source' => 'entity',
                                    ),
                            ),
                    ),

                    'backend' => array(
                    //TODO config routing for backend
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
        'allow_override' => true,
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'response' => 'Vivo\Service\ResponseFactory',
            'di' => 'Vivo\Service\DiFactory',
        ),
        'aliases' => array(
                'Vivo\SiteManager\Event\SiteEvent' => 'site_event',
                'Zend\Http\Response' => 'response',
                'Zend\View\HelperPluginManager' => 'view_helper_manager',
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
            'CLI\Indexer' => 'Vivo\Controller\CLI\IndexerController',
            'CLI\Info' => 'Vivo\Controller\CLI\InfoController',
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

    'view_helpers' => array(
            'invokables' => array(
            ),
    ),

    'di' => array(
    ),

    'vivo'      => array(
        //Vivo Modules configuration
        'modules'  => array(
            //Name of stream (protocol) which will be registered for Vivo Module source file access in Storage
            'stream_name'   => 'vmodule',
            //Vivo Module paths in Vivo Module Storage
            'module_paths'              => array(
                '/',
            ),
            'descriptor_name'       => 'vivo_module.json',
            //Default path where new modules will be added (in the module storage)
            'default_install_path'  => '/',
            //List of core modules loaded for all sites
            'core_modules'          => array(
            ),
            //Module resource manager configuration options
            'resource_manager'      => array(
                //Mapping of resource types to folders within modules
                'type_map'      => array(
                    'view'      => 'view',
                    'layout'    => 'view/layout',
                    'resource'  => 'resource',
                ),
                //Default resource type
                'default_type'  => 'resource',
            ),
        ),
        'cms'       => array(
            'repository'    => array(
            ),
        ),
        'templates' => array (
            'template_map' => array(
                'Vivo\UI\Page' => __DIR__.'/../view/Vivo/UI/Page.phtml',
                'Vivo\CMS\UI\Content\Layout' => __DIR__.'/../view/Vivo/CMS/UI/Content/Layout.phtml',
                'Vivo\CMS\UI\Content\File:html' => __DIR__.'/../view/Vivo/CMS/UI/Content/File.html.phtml',
                'Vivo\CMS\UI\Content\File:plain' => __DIR__.'/../view/Vivo/CMS/UI/Content/File.plain.phtml',
                'Vivo\CMS\UI\Content\File:flash' => __DIR__.'/../view/Vivo/CMS/UI/Content/File.flash.phtml',
                'Vivo\CMS\UI\Content\File:image' => __DIR__.'/../view/Vivo/CMS/UI/Content/File.image.phtml',
                'Vivo\CMS\UI\Content\File' => __DIR__.'/../view/Vivo/CMS/UI/Content/File.phtml',
                'Vivo\CMS\UI\Content\Overview' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.phtml',
                'Vivo\UI\ComponentContainer' => __DIR__.'/../view/Vivo/UI/ComponentContainer.phtml',
            ),
        ),
        'component_mapping' => array (
            'front_component' => array (
                'Vivo\CMS\Model\Content\Layout' => 'Vivo\CMS\UI\Content\Layout',
                'Vivo\CMS\Model\Content\File' => 'Vivo\CMS\UI\Content\File',
                'Vivo\CMS\Model\Content\Overview' => 'Vivo\CMS\UI\Content\Overview',
            ),
            'editor_component' => array (

            ),
        ),
        'service_manager' => array (
        //configuration of modules service manager
        ),
        'di' => array (
            'instance' => array (
                'alias' => array (
                    'viewModel' =>  'Vivo\View\Model\UIViewModel',
                ),
                'viewModel' => array (
                    'shared' => false, //new viewModel for each UI/component
                ),
                'Vivo\UI\Component' => array (
                    'parameters' => array (
                        'view' => 'viewModel',
                    ),
                ),
                'Vivo\UI\Page' => array (
                    'parameters' => array (
                        'doctype' => 'HTML5',
                        //globaly defined links and scripts
                        'links' => array (
                            array(
                                'rel'  => 'stylesheet',
                                'href' => '/.ModuleName.resource/css/definedInVivoConfig.css',
                                'type' => 'text/css',
                                'media' => 'screen'
                            ),
                        ),
                        'scripts' => array (
                            array(
                                'src' => '/.ModuleName.resource/js/front.js',
                                'type' => 'text/javascript',
                            ),
                        ),
                        'metas' => array (
                            array (
                                'name' => 'Robots',
                                'content' => 'INDEX,FOLLOW',
                            ),
                            array (
                                'charset' => 'UTF-8',
                            ),
                        ),
                        'viewHelpers' => 'Zend\View\HelperPluginManager',
                    ),
                ),
            ),
        ),

    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'info' => array(
                    'options' => array(
                        'route'    => 'info [<action>]',
                        'defaults' => array(
                            'controller' => 'CLI\Info',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'module' => array(
                    'options' => array(
                        'route'    => 'module [<action>]',
                        'defaults' => array(
                            'controller' => 'CLI\Module',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'module_add' => array(
                    'options' => array(
                        'route'    => 'module add <module_url> [--force|-f]',
                        'defaults' => array(
                            'controller' => 'CLI\Module',
                            'action'     => 'add',
                        ),
                    ),
                ),
                'indexer' => array(
                    'options' => array(
                        'route'    => 'indexer [<action>]',
                        'defaults' => array(
                            'controller' => 'CLI\Indexer',
                            'action'     => 'default',
                        ),
                    ),
                ),
            ),
        ),
    ),
);

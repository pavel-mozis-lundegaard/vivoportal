<?php
/**
 * Main CMS config, can be splited to the topic related files in the future.
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
                                'controller' => 'cms_front_controller',
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
                                'controller' => 'resource_front_controller',
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
                                            'controller' => 'resource_front_controller',
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
                                'controller' => 'cms_front_controller',
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
        'invokables'    => array(
            'uuid_generator'            => 'Vivo\Uuid\Generator',
            'storage_factory'           => 'Vivo\Storage\Factory',
            'site_event'                => 'Vivo\SiteManager\Event\SiteEvent',
            'indexer_helper'            => 'Vivo\Repository\IndexerHelper',
            'io_util'                   => 'Vivo\IO\IOUtil',
        ),
        'factories' => array(
            'translator'                => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'response'                  => 'Vivo\Service\ResponseFactory',
            'dependencyinjector'        => 'Vivo\Service\DiFactory',
            'db_service_manager'        => 'Vivo\Service\DbServiceManagerFactory',
            'uuid_convertor'            => 'Vivo\Service\UuidConvertorFactory',
            'module_storage'            => 'Vivo\Service\ModuleStorageFactory',
            'remote_module'             => 'Vivo\Service\RemoteModuleFactory',
            'module_storage_manager'    => 'Vivo\Service\ModuleStorageManagerFactory',
            'module_manager_factory'    => 'Vivo\Service\ModuleManagerFactoryFactory',
            'site_manager'              => 'Vivo\Service\SiteManagerFactory',
            'run_site_manager_listener' => 'Vivo\Service\RunSiteManagerListenerFactory',
            'lucene'                    => 'Vivo\Service\LuceneFactory',
            'storage_util'              => 'Vivo\Service\StorageUtilFactory',
            'indexer_adapter_lucene'    => 'Vivo\Service\IndexerAdapterLuceneFactory',
            'indexer'                   => 'Vivo\Service\IndexerFactory',
            'repository'                => 'Vivo\Service\RepositoryFactory',
            'cms'                       => 'Vivo\Service\CmsFactory',
            'module_resource_manager'   => 'Vivo\Service\ModuleResourceManagerFactory',
            'module_install_manager'    => 'Vivo\Service\ModuleInstallManagerFactory',
            'cms_api_module'            => 'Vivo\Service\CmsApiModuleFactory',
            'db_provider_factory'       => 'Vivo\Service\DbProviderFactoryFactory',
            'pdo_abstract_factory'      => 'Vivo\Service\PdoAbstractFactoryFactory',
            'zdb_abstract_factory'      => 'Vivo\Service\ZdbAbstractFactoryFactory',
            'path_builder'              => 'Vivo\Service\PathBuilderFactory',
            'component_factory'         => 'Vivo\Service\ComponentFactoryFactory',
            'phtml_rendering_strategy'  => 'Vivo\Service\PhtmlRenderingStrategyFactory',
        ),
        'aliases' => array(
                'Vivo\SiteManager\Event\SiteEvent'  => 'site_event',
                'Vivo\Repository\Repository'        => 'repository',
                'Zend\Http\Response'                => 'response',
                'Zend\View\HelperPluginManager'     => 'view_helper_manager',
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
            'cli_indexer' => 'Vivo\Controller\CLI\IndexerController',
            'cli_info' => 'Vivo\Controller\CLI\InfoController',
        ),
        'factories' => array(
            'cms_front_controller' => 'Vivo\Service\Controller\CMSFrontControllerFactory',
            'resource_front_controller' => 'Vivo\Service\Controller\ResourceFrontControllerFactory',
            'cli_module' => 'Vivo\Service\Controller\CLI\CLIModuleControllerFactory',
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
        'db_service'    => array(
            'abstract_factory'  => array(
                //PDO
                'pdo'       => array(
                    'service_identifier'    => 'pdo',
                    //The PDO connections are defined in a local config
                    /*
                    'config'                => array(
                        'config_name'    => array(
                            'dsn'       => '',
                            'username'  => '',
                            'password'  => '',
                            'options'   => array(
                            ),
                        ),
                    ),
                    */
                ),
                //Doctrine
                'dem'  => array(
                    'service_identifier'    => 'dem',
                ),
                //Zend DB Adapter
                'zdb'  => array(
                    'service_identifier'    => 'zdb',
                ),
            ),
        ),
        'module_install_manager'    => array(
            //Default db source is configured in a local config
            //'default_db_source'     => '',
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
                'Vivo\UI\TabContainer' => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
                'Vivo\UI\Ribbon' => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
                'Vivo\UI\Ribbon\Tab' => __DIR__.'/../view/Vivo/UI/Ribbon/Tab.phtml',
                'Vivo\UI\Ribbon\Group' => __DIR__.'/../view/Vivo/UI/Ribbon/Group.phtml',
                'Vivo\UI\Ribbon\Item' => __DIR__.'/../view/Vivo/UI/Ribbon/Item.phtml',
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
                                'href' => '/.vivo.resource/css/front.css',
                                'type' => 'text/css',
                                'media' => 'screen'
                            ),
                        ),
                        'scripts' => array (
                            array(
                                'src' => '/.vivo.resource/js/front.js',
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
                            'controller' => 'cli_info',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'module' => array(
                    'options' => array(
                        'route'    => 'module [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'module_add' => array(
                    'options' => array(
                        'route'    => 'module add <module_url> [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'add',
                        ),
                    ),
                ),
                'module_install' => array(
                    'options' => array(
                        'route'    => 'module install <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'install',
                        ),
                    ),
                ),
                'module_uninstall' => array(
                    'options' => array(
                        'route'    => 'module uninstall <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'uninstall',
                        ),
                    ),
                ),
                'module_enable' => array(
                    'options' => array(
                        'route'    => 'module enable <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'enable',
                        ),
                    ),
                ),
                'module_disable' => array(
                    'options' => array(
                        'route'    => 'module disable <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'disable',
                        ),
                    ),
                ),
                'module_is_installed' => array(
                    'options' => array(
                        'route'    => 'module isinstalled <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'isInstalled',
                        ),
                    ),
                ),
                'module_is_enabled' => array(
                    'options' => array(
                        'route'    => 'module isenabled <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'isEnabled',
                        ),
                    ),
                ),
                'module_get_installed' => array(
                    'options' => array(
                        'route'    => 'module getinstalled [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'getInstalled',
                        ),
                    ),
                ),
                'module_get_enabled' => array(
                    'options' => array(
                        'route'    => 'module getenabled [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'getEnabled',
                        ),
                    ),
                ),
                'indexer' => array(
                    'options' => array(
                        'route'    => 'indexer [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_indexer',
                            'action'     => 'default',
                        ),
                    ),
                ),
            ),
        ),
    ),
);

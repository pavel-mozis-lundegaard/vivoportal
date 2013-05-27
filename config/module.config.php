<?php
/**
 * Main Vivo config, this config can not be overwritten by sites, and modules.
 */
return array(
    'router' => array(
        'routes' => array(
            //routes for frontend
            'vivo' => array(
                //only add hostname to routematch
                'type' => 'Vivo\Router\Hostname',
                'may_terminate' => false,
                'child_routes' => array(
                    //route for frontend documents
                    //@example http://<sitehost>/<path>/
                    'cms' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '(?<path>/.*)',
                            'spec'    => '%path%',
                            'defaults' => array(
                                'controller' => 'cms_front_controller',
                                'path' => '',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    //route for frontend resources
                    //@example http://<sitehost>/.<moduleName>.<resourceType>/<path>
                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.(?<source>.+?)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),
                    //route for entity resources
                    //@example http://<sitehost>/.entity/<entityPath>/.path/<resourcePath>
                    'resource_entity' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.entity(?<entity>.+?)((\.path/(?<path>.+)))',
                            'spec'    => '/.entity%entity%.path/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'path' => '',
                                'source' => 'entity',
                            ),
                        ),
                    ),
                ),
            ),

            //routes configuration for backend
            'backend' => array(
                'type' => 'Vivo\Backend\Hostname',
                'may_terminate' => false,
                'options' => array (
                    'hosts' => array (
                    ),
                ),
                'child_routes' => array(
                    //route for everything else on backend hostname - controller redirects
                    'other' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'may_terminate' => true,
                        'options' => array(
                            'regex'    => '.*',
                            'spec'    => '/',
                            'defaults' => array(
                                'controller' => 'backend_controller',
                            ),
                        ),
                    ),
                    //default backend route
                    //@example http://<backendhost>/
                    'default' => array (
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/',
                            'defaults' => array(
                                'controller' => 'backend_controller',
                            ),
                        ),
                    ),
                    //route for backend modules
                    //@example http://<backendhost>/<sitehost>/<moduleName>/
                    'modules' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'may_terminate' => true,
                        'options' => array(
                            'regex'    => '/(?<host>.+?)/(?<module>.+?)/(?<path>.*)',
                            'spec'    => '/%host%/%module%/%path%',
                            'defaults' => array(
                                'controller' => 'backend_controller',
                                'path'   => '',
                                'module' => 'explorer',
                                'host' => '',
                            ),
                        ),
                    ),
                    //route for viewing site in backend
                    //@example http://<backendhost>/<sitehost>/view/<pathWithinSite>
                    'cms' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'may_terminate' => true,
                        'options' => array(
                            'regex'    => '/(?<host>.*)/view(?<path>/.*)?',
                            'spec'    => '/%host%/view%path%',
                            'defaults' => array(
                                'controller' => 'cms_front_controller',
                                'path'   => '',
                            ),
                        ),
                    ),
                    //route for site resources in backend view
                    //@example http://<backendhost>/<sitehost>/view/.<moduleName>.<resourceType>/<path>
                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/(?<host>.+)/view/\.(?<source>.+?)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/%host%/view/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),
                    //route for site entity resources in backend view
                    'resource_entity' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/(?<host>.+)/view/\.entity(?<entity>.+?)((\.path/(?<path>.+)))',
                            'spec'    => '/%host%/view/.entity%entity%.path/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'path' => '',
                                'source' => 'entity',
                            ),
                        ),
                    ),

                    //route for resources for backend
                    'backend_resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.(?<source>.+?)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
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
            'cms_event'                 => 'Vivo\CMS\Event\CMSEvent',
            'io_util'                   => 'Vivo\IO\IOUtil',
            'indexer_query_builder'     => 'Vivo\Indexer\QueryBuilder',
            'indexer_document_builder'  => 'Vivo\Indexer\DocumentBuilder',
            'view_model'                => 'Zend\View\Model\ViewModel',
            'session_manager'           => 'Zend\Session\SessionManager',
            'Vivo\Http\Filter\OutputFilterListener' => 'Vivo\Http\Filter\OutputFilterListener',
        ),
        'factories' => array(
            'RoutePluginManager'        => 'Vivo\Service\RoutePluginManagerFactory',
            'translator'                => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'response'                  => 'Vivo\Service\ResponseFactory',
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
            'repository'                => 'Vivo\Repository\RepositoryFactory',
            'repository_events'         => 'Vivo\Repository\EventManagerFactory',
            'indexer_helper'            => 'Vivo\Service\IndexerHelperFactory',
            'Vivo\CMS\Api\Module'       => 'Vivo\CMS\Api\ModuleFactory',
            'Vivo\CMS\Api\CMS'          => 'Vivo\CMS\Api\CMSFactory',
            'Vivo\CMS\Api\Document'     => 'Vivo\CMS\Api\DocumentFactory',
            'Vivo\CMS\Api\Indexer'      => 'Vivo\CMS\Api\IndexerFactory',
            'Vivo\CMS\Api\Site'         => 'Vivo\CMS\Api\SiteFactory',
            'module_resource_manager'   => 'Vivo\Service\ModuleResourceManagerFactory',
            'module_install_manager'    => 'Vivo\Service\ModuleInstallManagerFactory',
            'db_provider_factory'       => 'Vivo\Service\DbProviderFactoryFactory',
            'db_provider_core'          => 'Vivo\Service\DbProviderCoreFactory',
            'pdo_abstract_factory'      => 'Vivo\Service\PdoAbstractFactoryFactory',
            'zdb_abstract_factory'      => 'Vivo\Service\ZdbAbstractFactoryFactory',
            'path_builder'              => 'Vivo\Service\PathBuilderFactory',
            'component_factory'         => 'Vivo\Service\ComponentFactoryFactory',
            'indexer_adapter'           => 'Vivo\Service\IndexerAdapterFactory',
            'indexer_field_helper'      => 'Vivo\Service\IndexerFieldHelperFactory',
            'indexer_query_parser'      => 'Vivo\Service\IndexerQueryParserFactory',
            'module_name_resolver'      => 'Vivo\Service\ModuleNameResolverFactory',
            'metadata_manager'          => 'Vivo\Service\MetadataManagerFactory',
            'lookup_data_manager'       => 'Vivo\LookupData\LookupDataManagerFactory',
            'redirector'                => 'Vivo\Util\RedirectorFactory',
            'logger'                    => 'Vivo\Log\LoggerFactory',
            'template_resolver'         => 'Vivo\Service\TemplateResolverFactory',
            'di_proxy'                  => 'Vivo\Service\DiProxyFactory',
            'module_db_provider'        => 'Vivo\Service\ModuleDbProviderFactory',
            'db_table_name_provider'    => 'Vivo\Service\DbTableNameProviderFactory',
            'db_table_gateway_provider' => 'Vivo\Service\DbTableGatewayProviderFactory',
            'Vivo\CMS\Api\Manager\Manager' => 'Vivo\CMS\Api\Manager\ManagerFactory',
            'component_tree_controller' => 'Vivo\UI\ComponentTreeControllerFactory',
            'Vivo\CMS\AvailableContentsProvider' => 'Vivo\CMS\AvailableContentsProviderFactory',
            'Vivo\Metadata\Provider\SelectableTemplatesProvider' => 'Vivo\Metadata\Provider\SelectableTemplatesProviderFactory',
            'Vivo\Util\UrlHelper'       =>  'Vivo\Util\UrlHelperFactory',
            'Vivo\Http\HeaderHelper'    => 'Vivo\Http\HeaderHelperFactory',
            'Vivo\Transliterator\Path'  => 'Vivo\Transliterator\PathFactory',
            'Vivo\Transliterator\Url'   => 'Vivo\Transliterator\UrlFactory',
            'Vivo\Transliterator\DocTitleToPath'    => 'Vivo\Transliterator\DocTitleToPathFactory',
            'sym_ref_convertor'         => 'Vivo\CMS\RefInt\SymRefConvertorFactory',
            'ref_int_listener'          => 'Vivo\CMS\RefInt\ListenerFactory',
            'mail_simple_renderer'      => 'Vivo\Mail\View\SimpleRendererFactory',
            'log_writer_plugin_manager' => 'Vivo\Log\WriterPluginManagerFactory',
            'input_filter_factory'      => 'Vivo\InputFilter\InputFilterFactoryFactory',
            'input_filter_conditions'   => 'Vivo\InputFilter\Condition\ConditionPluginManagerFactory',
            'form_factory'              => 'Vivo\Form\FactoryFactory',
            'cache_manager'             => 'Vivo\Cache\CacheManagerFactory',
        ),
        'aliases' => array(
            'Vivo\SiteManager\Event\SiteEvent'  => 'site_event',
            'Vivo\Repository\Repository'        => 'repository',
            'Zend\Http\Response'                => 'response',
            'Zend\Http\Request'                 => 'request',
            'Zend\View\HelperPluginManager'     => 'view_helper_manager',
            'Vivo\Util\Redirector'              => 'redirector',
            'Zend\View\Model\ViewModel'         => 'view_model',
            'Zend\Session\SessionManager'       => 'session_manager',
        ),
        'shared' => array(
            'view_model'                => false,
            'component_tree_controller' => false,
            'input_filter_factory'      => false,
        ),
        'initializers' => array(
            'component' => 'Vivo\Service\Initializer\ComponentInitializer',
            'default'   => 'Vivo\Service\Initializer\DefaultInitializer',
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
            'cli_info'      => 'Vivo\Controller\CLI\InfoController',
        ),
        'factories' => array(
            'cms_front_controller'      => 'Vivo\CMS\FrontControllerFactory',
            'resource_front_controller' => 'Vivo\Controller\ResourceFrontControllerFactory',
            'cli_module'                => 'Vivo\Service\Controller\CLI\CLIModuleControllerFactory',
            'cli_repository'            => 'Vivo\Service\Controller\CLI\CLIRepositoryControllerFactory',
            'cli_cms'                   => 'Vivo\Service\Controller\CLI\CLICmsControllerFactory',
            'cli_indexer'               => 'Vivo\Service\Controller\CLI\CLIIndexerControllerFactory',
            'cli_setup'                 => 'Vivo\Service\Controller\CLI\CLISetupControllerFactory',
            'backend_controller'         => 'Vivo\Backend\BackendControllerFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => false,
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
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'action'                => 'Vivo\View\Helper\Action',
            'action_link'           => 'Vivo\View\Helper\ActionLink',
            'action_url'            => 'Vivo\View\Helper\ActionUrl',
            'vivoform'              => 'Vivo\View\Helper\VivoForm',
            'vivoformfieldset'      => 'Vivo\View\Helper\VivoFormFieldset',
            'container_component'   => 'Vivo\View\Helper\ContainerComponent',
            'overview_title'        => 'Vivo\View\Helper\OverviewTitle',
          //  'url' => 'Vivo\View\Helper\Url',
        ),
        'factories' => array(
            'url'               => 'Vivo\View\Helper\UrlFactory',
            'resource'          => 'Vivo\View\Helper\ResourceFactory',
            'document'          => 'Vivo\View\Helper\DocumentFactory',
            'cms'               => 'Vivo\View\Helper\CmsFactory',
            'vivo_head_title'   => 'Vivo\View\Helper\VivoHeadTitleFactory',
            'render_document'   => 'Vivo\View\Helper\RenderDocumentFactory',
        ),
    ),
    //Plugin manager configuration for navigation view helpers
    'navigation_view_helpers'   => array(
        'invokables'        => array(
            'vivo_menu'         => 'Vivo\View\Helper\Navigation\Menu',
        ),
    ),
    'validators'    => array(
        'invokables' => array(
            'conditional'   => 'Vivo\Validator\Conditional',
            'vivo_invalid'  => 'Vivo\Validator\VivoInvalid',
        ),
        'initializers'      => array(
            'validator_initializer'     => 'Vivo\Validator\Initializer',
        ),
    ),
    //Input filter conditions plugin manager config
    'input_filter_conditions'   => array(
        'invokables'    => array(
            'input'         => 'Vivo\InputFilter\Condition\Input',
            'notEmpty'      => 'Vivo\InputFilter\Condition\NotEmpty',
            'equals'        => 'Vivo\InputFilter\Condition\Equals',
            'allEmpty'      => 'Vivo\InputFilter\Condition\AllEmpty',
        ),
        'initializers'  => array(
            'condition_initializer' => 'Vivo\InputFilter\Condition\Initializer',
        ),
    ),
    'di' => array(
    ),
    'metadata_manager' => array(
        'config_path' => __DIR__ . '/../config/metadata',
    ),

    'logger' => array(
        'listener' => array (
            'attach' => array (
                //array('*', 'log'), //log 'log' events
                //array('*', '*'), //log all events
            ),
        ),
        'writers' => array (
//            writers from writer plugin manager
//            'default_log'   => array(
//                'priority'  => 1,
//                'options'   => array(
//                    'log_dir'   => '',
//                ),
//            ),
//            'firephp'       => array(),
        ),
        'writer_plugin_manager' => array(
            'factories'     => array(
                'default_log'               => 'Vivo\Log\LogFileWriterFactory',
            ),
        ),
    ),

    'transliterator'    => array(
        'path'              => array(
            'options'           => array(
                //Transliteration map
                'map'               => array(
                    //Cyrillic
                    'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
                    'з' => 'z', 'и' =>'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
                    'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c', 'ч' => 'ch',
                    'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'eh', 'ю' => 'ju', 'я' => 'ja',
                    'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'JO', 'Ж' => 'ZH',
                    'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P',
                    'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'C', 'Ч' => 'CH',
                    'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'EH', 'Ю' => 'JU', 'Я' => 'JA',
                    //Doubles
                    'ß' => 'ss', 'æ' => 'ae', 'Æ' => 'AE', 'œ' => 'oe', 'Œ' => 'OE',
                    //A
                    'á' => 'a', 'Á' => 'A', 'ä' => 'a', 'Ä' => 'A', 'ą' => 'a', 'à' => 'a', 'À' => 'A', 'â' => 'a', 'Â' => 'A',
                    'å' => 'a', 'Å' => 'A', 'ă' => 'a', 'Ă' => 'A',
                    //C
                    'č' => 'c', 'Č' => 'C', 'ć' => 'c', 'Ć' => 'C', 'ç' => 'c', 'Ç' => 'C',
                    //D
                    'ď' => 'd', 'Ď' => 'D', 'ð' => 'd', 'Ð' => 'D',
                    //E
                    'é' => 'e', 'É' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ę' => 'e', 'Ę' => 'E',
                    'è' => 'e', 'È' => 'E', 'ê' => 'e', 'Ê' => 'E',
                    //I
                    'í' => 'i', 'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'î' => 'i', 'Î' => 'I',
                    //L
                    'ľ' => 'l', 'Ľ' => 'L', 'ĺ' => 'l', 'Ĺ' => 'L', 'ł' => 'l', '£' => 'L',
                    //N
                    'ň' => 'n', 'Ň' => 'N', 'ń' => 'n', 'Ń' => 'N', 'ñ' => 'n', 'Ñ' => 'N',
                    //O
                    'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O',
                    //R
                    'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R',
                    //S
                    'š' => 's', 'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ş' => 's', 'Ş' => 'S',
                    //T
                    'ť' => 't', 'Ť' => 'T', 'ţ' => 't', 'Ţ' => 'T',
                    //U
                    'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u', 'Ü' => 'U', 'ű' => 'u', 'Ű' => 'U',
                    'û' => 'u', 'Û' => 'U', 'ù' => 'u',
                    //Y
                    'ý' => 'y', 'Ý' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
                    //Z
                    'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z', 'ż' => 'z', 'Ż' => 'Z',
                    //Symbols
                    '\\' => '/',
                ),
                //String with all allowed characters
                'allowedChars'      => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_/.',
                //Character used to replace illegal characters
                'replacementChar'   => '-',
                //Change case before processing
                'caseChangePre'     => \Vivo\Transliterator\Transliterator::CASE_CHANGE_NONE,
                //Change case after processing
                'caseChangePost'    => \Vivo\Transliterator\Transliterator::CASE_CHANGE_NONE,
            ),
        ),
        'url'               => array(
            'options'           => array(
                //Transliteration map
                'map'               => array(
                    //Cyrillic
                    'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
                    'з' => 'z', 'и' =>'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
                    'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c', 'ч' => 'ch',
                    'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'eh', 'ю' => 'ju', 'я' => 'ja',
                    //Doubles
                    'ß' => 'ss', 'æ' => 'ae', 'œ' => 'oe',
                    //A
                    'á' => 'a', 'ä' => 'a', 'ą' => 'a', 'à' => 'a', 'â' => 'a', 'å' => 'a', 'ă' => 'a',
                    //C
                    'č' => 'c', 'ć' => 'c', 'ç' => 'c',
                    //D
                    'ď' => 'd', 'ð' => 'd',
                    //E
                    'é' => 'e', 'ě' => 'e', 'ë' => 'e', 'ę' => 'e', 'è' => 'e', 'ê' => 'e',
                    //I
                    'í' => 'i', 'ï' => 'i', 'î' => 'i',
                    //L
                    'ľ' => 'l', 'ĺ' => 'l', 'ł' => 'l',
                    //N
                    'ň' => 'n', 'ń' => 'n', 'ñ' => 'n',
                    //O
                    'ó' => 'o', 'ö' => 'o', 'ô' => 'o', 'ő' => 'o',
                    //R
                    'ř' => 'r', 'ŕ' => 'r',
                    //S
                    'š' => 's', 'ś' => 's', 'ş' => 's',
                    //T
                    'ť' => 't', 'ţ' => 't',
                    //U
                    'ú' => 'u', 'ů' => 'u', 'ü' => 'u', 'ű' => 'u', 'û' => 'u', 'ù' => 'u',
                    //Y
                    'ý' => 'y', 'ÿ' => 'y',
                    //Z
                    'ž' => 'z', 'ź' => 'z', 'ż' => 'z',
                ),
                //String with all allowed characters
                'allowedChars'      => 'abcdefghijklmnopqrstuvwxyz-/',
                //Character used to replace illegal characters
                'replacementChar'   => '-',
                //Change case before processing
                'caseChangePre'     => \Vivo\Transliterator\Transliterator::CASE_CHANGE_TO_LOWER,
                //Change case after processing
                'caseChangePost'    => \Vivo\Transliterator\Transliterator::CASE_CHANGE_NONE,
            ),
        ),
        'doc_title_to_path' => array(
            'options'           => array(
                //Transliteration map
                'map'               => array(
                    //Cyrillic
                    'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
                    'з' => 'z', 'и' =>'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
                    'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c', 'ч' => 'ch',
                    'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'eh', 'ю' => 'ju', 'я' => 'ja',
                    'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'JO', 'Ж' => 'ZH',
                    'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P',
                    'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'C', 'Ч' => 'CH',
                    'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'EH', 'Ю' => 'JU', 'Я' => 'JA',
                    //Doubles
                    'ß' => 'ss', 'æ' => 'ae', 'Æ' => 'AE', 'œ' => 'oe', 'Œ' => 'OE',
                    //A
                    'á' => 'a', 'Á' => 'A', 'ä' => 'a', 'Ä' => 'A', 'ą' => 'a', 'à' => 'a', 'À' => 'A', 'â' => 'a', 'Â' => 'A',
                    'å' => 'a', 'Å' => 'A', 'ă' => 'a', 'Ă' => 'A',
                    //C
                    'č' => 'c', 'Č' => 'C', 'ć' => 'c', 'Ć' => 'C', 'ç' => 'c', 'Ç' => 'C',
                    //D
                    'ď' => 'd', 'Ď' => 'D', 'ð' => 'd', 'Ð' => 'D',
                    //E
                    'é' => 'e', 'É' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ę' => 'e', 'Ę' => 'E',
                    'è' => 'e', 'È' => 'E', 'ê' => 'e', 'Ê' => 'E',
                    //I
                    'í' => 'i', 'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'î' => 'i', 'Î' => 'I',
                    //L
                    'ľ' => 'l', 'Ľ' => 'L', 'ĺ' => 'l', 'Ĺ' => 'L', 'ł' => 'l', '£' => 'L',
                    //N
                    'ň' => 'n', 'Ň' => 'N', 'ń' => 'n', 'Ń' => 'N', 'ñ' => 'n', 'Ñ' => 'N',
                    //O
                    'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O',
                    //R
                    'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R',
                    //S
                    'š' => 's', 'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ş' => 's', 'Ş' => 'S',
                    //T
                    'ť' => 't', 'Ť' => 'T', 'ţ' => 't', 'Ţ' => 'T',
                    //U
                    'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u', 'Ü' => 'U', 'ű' => 'u', 'Ű' => 'U',
                    'û' => 'u', 'Û' => 'U', 'ù' => 'u',
                    //Y
                    'ý' => 'y', 'Ý' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
                    //Z
                    'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z', 'ż' => 'z', 'Ż' => 'Z',
                    //Symbols
                    '\\' => '/',
                ),
                //String with all allowed characters
                'allowedChars'      => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_',
                //Character used to replace illegal characters
                'replacementChar'   => '-',
                //Change case before processing
                'caseChangePre'     => \Vivo\Transliterator\Transliterator::CASE_CHANGE_TO_LOWER,
                //Change case after processing
                'caseChangePost'    => \Vivo\Transliterator\Transliterator::CASE_CHANGE_NONE,
            ),
        ),
    ),

    'response' => array (
        'headers' => array (
            'mime_type_expiration' => array (
                //define specific expiration time for content type
                'image/*' => 86400,
                'audio/*' => 86400,
                'text/*' => 86400,
                'font/*' => 86400,
                'application/x-shockwave-flash' => 86400,
                '*/*' => 86400, // other mime types
            ),
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
    //Core setup
    'setup'         => array(
        'db'    => array(
            //Mapping of symbolic core table names to real names used in db
            'table_names'   => array(
                'vivo_users'     => 'vivo_users',
            ),
        ),
    ),
    'indexer'   => array(
        'adapter'   => array(
            'type'      => 'dummy',
                //Solr options
//                'options'   => array(
//                    'id_field'      => 'uuid',
//                ),
        ),
        'default_indexing_options'  => array(
            'type'          => \Vivo\Indexer\IndexerInterface::FIELD_TYPE_STRING,
            'indexed'       => true,
            'stored'        => true,
            'tokenized'     => false,
            'multi'         => false,
        ),
        'presets'                   => array(
        ),
    ),
    //Vivo Modules configuration
    'modules'  => array(
        //Storage for modules - configure in global/local config
        'storage'              => array(
            'class'     => '',
            'options'   => array(),
        ),
        //Name of stream (protocol) which will be registered for Vivo Module source file access in Storage
        'stream_name'   => 'vivo.module',
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
                'metadata'  => 'config/metadata',
            ),
            //Default resource type
            'default_type'  => 'resource',
        ),
        //Default db source used for modules
        //Configure in local config
        //'default_db_source'     => '',
    ),
    //Cache manager configuration - define in local config
    'cache_manager'         => array(
//        'cache_name'    => array(
            //Options to pass to StorageFactory::factory(), e.g.:
//            'adapter'   => array(
//                'name'      => 'filesystem',
//                'options'   => array(
//                    'cache_dir' => 'path/to/cache/dir',
//                    'namespace' => 'cache_namespace',
//                ),
//            ),
//            'plugins'   => array(
//                'serializer'    => array(),
//            ),
//        ),
    ),
    //Repository configuration
    'repository'    => array(
        //Storage for repository - configure in global/local config
        'storage'       => array(
            'class'         => '',
            'options'       => array(),
        ),
        //Cache - cache name or null for no cache; see cache_manager config
        //'cache'         => <cache_name>,
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
                'indexer_host_action' => array(
                    'options' => array(
                        'route'    => 'indexer <action> <host>',
                        'defaults' => array(
                            'controller' => 'cli_indexer',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'indexer_clear' => array(
                    'options' => array(
                        'route'    => 'indexer clear',
                        'defaults' => array(
                            'controller' => 'cli_indexer',
                            'action'     => 'clear',
                        ),
                    ),
                ),
                'cms' => array(
                    'options' => array(
                        'route'    => 'cms [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'cms_host_action' => array(
                    'options' => array(
                        'route'    => 'cms <action> <host>',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'cms_unique_uuids' => array(
                    'options' => array(
                        'route'    => 'cms uniqueuuids <host> [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'unique-uuids',
                        ),
                    ),
                ),
                'cms_create_site'   => array(
                    'options' => array(
                        'route'    => 'cms createsite <name> <secdomain> <hosts> [<title>]',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'create-site',
                        ),
                    ),
                ),
                'setup' => array(
                    'options' => array(
                        'route'    => 'setup [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_setup',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'setup_db' => array(
                    'options' => array(
                        'route'    => 'setup db [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_setup',
                            'action'     => 'db',
                        ),
                    ),
                ),

            ),
        ),
    ),
    'cms'      => array(
        //this config key is reserved for merged cms configuration
        //default values and structure are in cms.config.php
        //do not access to this key directly - use service 'cms_config'
    ),
);

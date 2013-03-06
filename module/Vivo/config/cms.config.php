<?php
/**
 * CMS config (cms_config), this configuration could be overwritten
 * by configuration in modules and sites.
 * This config is registered as 'cms_config' service, when all configuration
 * sources (site, modules) are merged.
 */
return array(
    'templates' => array (
        'template_map' => array(
            'Vivo\UI\Page'                      => __DIR__.'/../view/Vivo/UI/Page.phtml',
            'Vivo\CMS\UI\Content\Layout'        => __DIR__.'/../view/Vivo/CMS/UI/Content/Layout.phtml',
            'Vivo\CMS\UI\Content\File:html'     => __DIR__.'/../view/Vivo/CMS/UI/Content/File.html.phtml',
            'Vivo\CMS\UI\Content\File:plain'    => __DIR__.'/../view/Vivo/CMS/UI/Content/File.plain.phtml',
            'Vivo\CMS\UI\Content\File:flash'    => __DIR__.'/../view/Vivo/CMS/UI/Content/File.flash.phtml',
            'Vivo\CMS\UI\Content\File:image'    => __DIR__.'/../view/Vivo/CMS/UI/Content/File.image.phtml',
            'Vivo\CMS\UI\Content\File'          => __DIR__.'/../view/Vivo/CMS/UI/Content/File.phtml',
            'Vivo\CMS\UI\Content\Overview'      => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.phtml',
            'Vivo\CMS\UI\Content\Overview:Inline'      => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Inline.phtml',
            'Vivo\CMS\UI\Content\Overview:NavInline'      => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.NavInline.phtml',
            'Vivo\CMS\UI\Content\Overview:NavBlock'      => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.NavBlock.phtml',
            'Vivo\CMS\UI\Content\Overview:Desc' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Desc.phtml',
            'Vivo\CMS\UI\Content\Overview:Date' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Date.phtml',
            'Vivo\CMS\UI\Content\Overview:DateDesc' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.DateDesc.phtml',
            'Vivo\CMS\UI\Content\Overview:DateDescThumb' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.DateDescThumb.phtml',
            'Vivo\CMS\UI\Content\Overview:Thumb' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Thumb.phtml',
            'Vivo\CMS\UI\Content\Overview:ThumbDesc' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.ThumbDesc.phtml',
            'Vivo\CMS\UI\Content\Overview:Carousel' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Carousel.phtml',
            'Vivo\CMS\UI\Content\Overview:CarouselTouch' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.CarouselTouch.phtml',
            'Vivo\CMS\UI\Content\Logon'         => __DIR__.'/../view/Vivo/CMS/UI/Content/Logon.phtml',
            'Vivo\UI\ComponentContainer'        => __DIR__.'/../view/Vivo/UI/ComponentContainer.phtml',
            'Vivo\UI\TabContainer'              => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
            'Vivo\UI\Ribbon'                    => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
            'Vivo\UI\Ribbon\Tab'                => __DIR__.'/../view/Vivo/UI/Ribbon/Tab.phtml',
            'Vivo\UI\Ribbon\Group'              => __DIR__.'/../view/Vivo/UI/Ribbon/Group.phtml',
            'Vivo\UI\Ribbon\Item'               => __DIR__.'/../view/Vivo/UI/Ribbon/Item.phtml',
            'Vivo\UI\Alert'                     => __DIR__.'/../view/Vivo/UI/Alert.phtml',

            'Vivo\CMS\UI\Content\Editor\File' => __DIR__.'/../view/Vivo/CMS/UI/Content/Editor/File.phtml',
            'Vivo\CMS\UI\Content\Editor\Layout' => __DIR__.'/../view/Vivo/CMS/UI/Content/Editor/Layout.phtml',
            'Vivo\CMS\UI\Content\Editor\Overview' => __DIR__.'/../view/Vivo/CMS/UI/Content/Editor/Overview.phtml',

        ),
        'custom_templates' => array (
            // custom selectable templates
            'Vivo\CMS\Model\Content\Overview' => array (
                'Vivo\CMS\UI\Content\Overview',
                'Vivo\CMS\UI\Content\Overview:Inline',
                'Vivo\CMS\UI\Content\Overview:NavInline',
                'Vivo\CMS\UI\Content\Overview:NavBlock',
                'Vivo\CMS\UI\Content\Overview:Desc',
                'Vivo\CMS\UI\Content\Overview:Date',
                'Vivo\CMS\UI\Content\Overview:DateDesc',
                'Vivo\CMS\UI\Content\Overview:DateDescThumb',
                'Vivo\CMS\UI\Content\Overview:Thumb',
                'Vivo\CMS\UI\Content\Overview:Carousel',
                'Vivo\CMS\UI\Content\Overview:ThumbDesc',
                'Vivo\CMS\UI\Content\Overview:CarouselTouch'
            ),
        ),
    ),
    'component_mapping' => array (
        'front_component' => array (
            'Vivo\CMS\Model\Content\Layout'     => 'Vivo\CMS\UI\Content\Layout',
            'Vivo\CMS\Model\Content\File'       => 'Vivo\CMS\UI\Content\File',
            'Vivo\CMS\Model\Content\Overview'   => 'Vivo\CMS\UI\Content\Overview',
            'Vivo\CMS\Model\Content\Hyperlink'  => 'Vivo\CMS\UI\Content\Hyperlink',
            'Vivo\CMS\Model\Content\Logon'      => 'Vivo\CMS\UI\Content\Logon',
        ),
        'editor_component' => array (
            'Vivo\CMS\Model\Content\File'       => 'Vivo\CMS\UI\Content\Editor\File',
            'Vivo\CMS\Model\Content\Overview'   => 'Vivo\CMS\UI\Content\Editor\Overview',
            'Vivo\CMS\Model\Content\Layout'     => 'Vivo\CMS\UI\Content\Editor\Layout',
        ),
    ),
    'contents' => array (
        'available_contents' => array (
            'Vivo\CMS\Model\Content\File',
            'Vivo\CMS\Model\Content\Overview',
          //  'Vivo\CMS\Model\Content\Link',
          //  'Vivo\CMS\Model\Content\Hyperlink',
          //  'Vivo\CMS\Model\Content\Component',
            'Vivo\CMS\Model\Content\Layout',
        ),
        'restrictions' => array (
            'document_type' => array (
                'Vivo\CMS\Model\Document' => array(
                ),
            ),
            'document_path' => array (
                '/Layouts' => array (
                    'Vivo\CMS\Model\Content\Layout',
                ),
            ),
            'user_role' => array (
                'managers' => array (
                ),
            ),
            'site' => array (
               //Whitelist of allowed contents in current site.
               //If empty all available contents are allowed.
            ),
        ),
    ),
    'workflow' => array (
        'states' => array(
            100 => array(
                'state'=> 'NEW',
                'groups' => array(/* 'Anyone' */),
            ),
            200 => array(
                'state'=> 'PUBLISHED',
                'groups' => array(),
            ),
            300 => array(
                'state'=> 'ARCHIVED',
                'groups' => array(),
            ),
        ),
    ),
    'backend' => array (
        'plugins' =>  array (
        ),
    ),
    'service_manager' => array (
        //configuration of service manager, services defined here should not override
        //services defined in Vivo config
        'invokables' => array (
            'Vivo\CMS\UI\Blank'             => 'Vivo\CMS\UI\Blank',
            'Vivo\CMS\UI\Root'              => 'Vivo\CMS\UI\Root',
            'Vivo\UI\ComponentContainer'    => 'Vivo\UI\ComponentContainer',
            'Vivo\UI\TabContainer'          => 'Vivo\UI\TabContainer',
            'Vivo\CMS\UI\Manager\Explorer\Ribbon'  => 'Vivo\CMS\UI\Manager\Explorer\Ribbon',
            'layout_empty_panel'            => 'Vivo\UI\Text',
        ),
        'factories' => array (
            'Vivo\CMS\UI\Content\File'      => 'Vivo\CMS\Service\UI\Content\FileFactory',
            'Vivo\CMS\UI\Content\Hyperlink' => 'Vivo\CMS\Service\UI\Content\HyperlinkFactory',
            'Vivo\CMS\UI\Content\Layout'    => 'Vivo\CMS\Service\UI\Content\LayoutFactory',
            'Vivo\CMS\UI\Content\Overview'  => 'Vivo\CMS\Service\UI\Content\OverviewFactory',
            'Vivo\CMS\UI\Content\Logon'     => 'Vivo\CMS\Service\UI\Content\LogonFactory',
            'Vivo\UI\Page'                  => 'Vivo\Service\UI\PageFactory',
            'Vivo\UI\Alert'                 => 'Vivo\UI\AlertFactory',
            'security_manager'              => 'Vivo\Service\SimpleSecurityManagerFactory',
//            'security_manager'              => 'Vivo\Service\DbSecurityManagerFactory',

            //backend
            //TODO move to own config
            'Vivo\Backend\UI\Backend'           => 'Vivo\Backend\UI\BackendFactory',
            'Vivo\Backend\UI\SiteSelector'      => 'Vivo\Backend\UI\SiteSelectorFactory',
            'Vivo\Backend\UI\Explorer\Explorer' => 'Vivo\Backend\UI\Explorer\ExplorerFactory',
            'Vivo\Backend\UI\Explorer\Editor'   => 'Vivo\Backend\UI\Explorer\EditorFactory',
            'Vivo\Backend\UI\Explorer\Editor\Content' => 'Vivo\Backend\UI\Explorer\Editor\ContentFactory',
            'Vivo\Backend\UI\Explorer\Editor\ContentTab' => 'Vivo\Backend\UI\Explorer\Editor\ContentTabFactory',
            'Vivo\Backend\UI\Explorer\Editor\Resource' => 'Vivo\Backend\UI\Explorer\Editor\ResourceFactory',
            'Vivo\Backend\UI\Explorer\Finder'   => 'Vivo\Backend\UI\Explorer\FinderFactory',
            'Vivo\Backend\UI\Explorer\Delete'   => 'Vivo\Backend\UI\Explorer\DeleteFactory',
            'Vivo\Backend\UI\Explorer\Creator'  => 'Vivo\Backend\UI\Explorer\CreatorFactory',
            'Vivo\Backend\UI\Explorer\Copy'     => 'Vivo\Backend\UI\Explorer\CopyFactory',
            'Vivo\Backend\UI\Explorer\Move'     => 'Vivo\Backend\UI\Explorer\MoveFactory',
            'Vivo\Backend\UI\Explorer\Viewer'   => 'Vivo\Backend\UI\Explorer\ViewerFactory',
            'Vivo\Backend\UI\Explorer\Browser'  => 'Vivo\Backend\UI\Explorer\BrowserFactory',
            'Vivo\Backend\UI\Logon'             => 'Vivo\Backend\UI\LogonFactory',
            'Vivo\Backend\ModuleResolver'       => 'Vivo\Backend\ModuleResolverFactory',

            //TODO: content editor factories
            'Vivo\CMS\UI\Content\Editor\File'     => 'Vivo\CMS\UI\Content\Editor\FileFactory',
            'Vivo\CMS\UI\Content\Editor\Overview' => 'Vivo\CMS\UI\Content\Editor\OverviewFactory',
            'Vivo\CMS\UI\Content\Editor\Layout'   => 'Vivo\CMS\UI\Content\Editor\LayoutFactory',
        ),
        'aliases' => array(
        ),
        'shared' => array(
        ),
        'initializers' => array(
        ),
    ),
    'di' => array (
        'instance' => array (
            'alias' => array (
            ),
            'Vivo\UI\Component' => array (
                'injection' => array (
                ),
                'parameters' => array (
                    'view' => 'Zend\View\Model\ViewModel',
                ),
            ),
        ),
    ),

    'ui' => array (
        //configuration of ui components
        'Vivo\UI\Page' => array (
            'doctype' => 'HTML5',
                'links' => array (
                    array(
                        'rel'  => 'stylesheet',
                        'href' => '/.Vivo.resource/css/front.css',
                        'type' => 'text/css',
                        'media' => 'screen'
                    ),
                    array(
                        'rel'  => 'apple-touch-icon-precomposed',
                        'sizes' => '144x144',
                        'href' => '/.Vivo.resource/apple-touch-icon-144.png'
                    ),
                    array(
                        'rel'  => 'apple-touch-icon-precomposed',
                        'sizes' => '114x114',
                        'href' => '/.Vivo.resource/apple-touch-icon-114.png'
                    ),
                    array(
                        'rel'  => 'apple-touch-icon-precomposed',
                        'sizes' => '72x72',
                        'href' => '/.Vivo.resource/apple-touch-icon-72.png'
                    ),
                    array(
                        'rel'  => 'apple-touch-icon-precomposed',
                        'href' => '/.Vivo.resource/apple-touch-icon-57.png'
                    ),
                    array(
                        'rel'  => 'shortcut icon',
                        'href' => '/.Vivo.resource/favicon.ico'
                    ),
                ),
                'scripts' => array (
                    /*array(
                        'src' => '/.Vivo.resource/js/jquery/1-9-1/jquery.js',
                        'type' => 'text/javascript',
                    ),
                    */
                ),
            'metas' => array (
                    array (
                        'name' => 'Robots',
                        'content' => 'INDEX,FOLLOW',
                    ),
                    array (
                        'charset' => 'UTF-8',
                    ),
                // array (
                //     'http-equiv' => 'Content-Type',
                //     'content' => 'text/html',
                //     'charset' => 'utf-8',
                // ),
            ),
        ),
    ),
    'output_filters' => array (
        //register output filters
        //'Vivo\Http\Filter\UpperCase',
    ),
    'security_manager_simple'  => array(
        //Define your options in your local.php config
        'options'           => array(
            //Security domain - if not set, the security domain of the active site will be used
            'security_domain'   => 'VIVO',
            'username'          => 'vivo.user',
            'password'          => 'password',
        ),
    ),
    'security_manager_db'  => array(
        //Define your options in your local.php config
        'options'           => array(
            //'super_password'        => 'Vivo.super.Pwd.497',
            'super_access_networks' => array(
                //'127.0.0.1',
            ),
        ),
    ),
);

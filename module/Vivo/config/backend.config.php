<?php
/**
 * config for backend
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
            'Vivo\CMS\UI\Content\Overview:Description' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Description.phtml',
            'Vivo\CMS\UI\Content\Logon'         => __DIR__.'/../view/Vivo/CMS/UI/Content/Logon.phtml',
            'Vivo\UI\ComponentContainer'        => __DIR__.'/../view/Vivo/UI/ComponentContainer.phtml',
            'Vivo\UI\TabContainer'              => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
            'Vivo\UI\Ribbon'                    => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
            'Vivo\UI\Ribbon\Tab'                => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Tab.phtml',
            'Vivo\UI\Ribbon\Group'              => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Group.phtml',
            'Vivo\UI\Ribbon\Item'               => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Item.phtml',
            'Vivo\Backend\UI\Explorer\Tree'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.phtml',
            'Vivo\Backend\UI\Explorer\Tree:Subtree'       => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.Subtree.phtml',
        ),
    ),

    'component_mapping' => array (
        'front_component' => array (
        ),
        'editor_component' => array (

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
            'Vivo\CMS\UI\Manager\Explorer\Explorer'  => 'Vivo\CMS\UI\Manager\Explorer\ExplorerFactory',
            'Vivo\CMS\UI\Manager\Explorer\Editor'  => 'Vivo\CMS\UI\Manager\Explorer\EditorFactory',
            'Vivo\UI\Page'                  => 'Vivo\Service\UI\PageFactory',
            'Vivo\UI\Alert'                  => 'Vivo\UI\AlertFactory',
            'security_manager'              => 'Vivo\Service\SimpleSecurityManagerFactory',
//            'security_manager'              => 'Vivo\Service\DbSecurityManagerFactory',

        //backend
        //TODO move to own config
            'Vivo\Backend\UI\Backend'    => 'Vivo\Backend\UI\BackendFactory',
            'Vivo\Backend\UI\Explorer\Explorer'    => 'Vivo\Backend\UI\Explorer\ExplorerFactory',
        ),
        'aliases' => array(
        ),
        'shared' => array(
        ),
        'initializers' => array(
        ),
    ),

    'ui' => array (
        //configuration of ui components
        'Vivo\UI\Page' => array (
            'doctype' => 'HTML5',
                'links' => array (
                    array(
                        'rel'  => 'stylesheet',
                        'href' => '.Bootstrap2_3_0.resource/css/bootstrap.css',
                        'type' => 'text/css',
                        'media' => 'screen'
                    ),
                    array(
                        'rel'  => 'stylesheet',
                        'href' => '.Vivo.resource/backend/css/manager.css',
                        'type' => 'text/css',
                        'media' => 'screen'
                    ),
                ),
                'scripts' => array (
                    array(
                        'src' => '.Bootstrap2_3_0.resource/js/bootstrap.js',
                        'type' => 'text/javascript',
                    ),
                    array(
                        'src' => '/.TinyMCE3_5_8.resource/js/tiny_mce/tiny_mce.js',
                        'type' => 'text/javascript',
                    ),
                    array(
                        'src' => '/.TinyMCE3_5_8.resource/js/init_specific_textareas.js',
                        'type' => 'text/javascript',
                    ),
                ),
            'metas' => array (
//                     array (
//                         'name' => 'Robots',
//                         'content' => 'INDEX,FOLLOW',
//                     ),
//                     array (
//                         'charset' => 'UTF-8',
//                     ),
                array (
                    'http-equiv' => 'Content-Type',
                    'content' => 'text/html',
                    'charset' => 'utf-8',
                ),
            ),
        ),
    ),
    'output_filters' => array (
        //register output filters
        //'Vivo\Http\Filter\UpperCase',
    ),
    //Backend config
    'backend'       => array(
        //Backend modules
        'modules'       => array(
            'Bootstrap2_3_0'    => array(
                'enabled'           => true,
            ),
            'TinyMCE3_5_8'      => array(
                'enabled'           => true,
            ),
            'TinyMCE3_5_6_Vivo'      => array(
                'enabled'           => true,
            ),
        ),
    ),
);

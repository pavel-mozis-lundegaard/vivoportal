<?php
/**
 * config for backend
 */
return array(
    'templates' => array (
        'template_map' => array(
            'Vivo\UI\Ribbon\Tab'                            => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Tab.phtml',
            'Vivo\UI\Ribbon\Group'                          => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Group.phtml',
            'Vivo\UI\Ribbon\Item'                           => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Item.phtml',
            'Vivo\Backend\UI\Backend'                       => __DIR__.'/../view/Vivo/Backend/UI/Backend.phtml',
            'Vivo\Backend\UI\Explorer\Browser'              => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Browser.phtml',
            'Vivo\Backend\UI\Explorer\Copy'                 => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Copy.phtml',
            'Vivo\Backend\UI\Explorer\Creator'              => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Creator.phtml',
            'Vivo\Backend\UI\Explorer\Editor'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Editor.phtml',
            'Vivo\Backend\UI\Explorer\Editor\Content'       => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Editor/Content.phtml',
            'Vivo\Backend\UI\Explorer\Editor\ContentTab'    => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Editor/ContentTab.phtml',
            'Vivo\Backend\UI\Explorer\Explorer'             => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Explorer.phtml',
            'Vivo\Backend\UI\Explorer\Delete'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Delete.phtml',
            'Vivo\Backend\UI\Explorer\Finder'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Finder.phtml',
            'Vivo\Backend\UI\Explorer\Inspect'                 => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Inspect.phtml',
            'Vivo\Backend\UI\Explorer\Move'                 => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Move.phtml',
            'Vivo\Backend\UI\Explorer\Tree'                 => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.phtml',
            'Vivo\Backend\UI\Explorer\Tree:Subtree'         => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.Subtree.phtml',
            'Vivo\Backend\UI\Explorer\Viewer'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Viewer.phtml',
            'Vivo\Backend\UI\FooterBar'                     => __DIR__.'/../view/Vivo/Backend/UI/FooterBar.phtml',
            'Vivo\Backend\UI\HeaderBar'                     => __DIR__.'/../view/Vivo/Backend/UI/HeaderBar.phtml',
            'Vivo\Backend\UI\Logon'                         => __DIR__.'/../view/Vivo/Backend/UI/Logon.phtml',
            'Vivo\Backend\UI\ModulesPanel'                  => __DIR__.'/../view/Vivo/Backend/UI/ModulesPanel.phtml',
            'Vivo\Backend\UI\SiteSelector'                  => __DIR__.'/../view/Vivo/Backend/UI/SiteSelector.phtml',
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
                        'href' => '.Vivo.resource/bootstrap/css/bootstrap.css',
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
                        'src' => '.Vivo.resource/bootstrap/js/bootstrap.js',
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
);

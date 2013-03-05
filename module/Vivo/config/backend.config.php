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
            'Vivo\Backend\UI\Explorer\Inspect'              => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Inspect.phtml',
            'Vivo\Backend\UI\Explorer\Move'                 => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Move.phtml',
            'Vivo\Backend\UI\Explorer\Tree'                 => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.phtml',
            'Vivo\Backend\UI\Explorer\Tree:Subtree'         => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.Subtree.phtml',
            'Vivo\Backend\UI\Explorer\Viewer'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Viewer.phtml',
            'Vivo\Backend\UI\FooterBar'                     => __DIR__.'/../view/Vivo/Backend/UI/FooterBar.phtml',
            'Vivo\Backend\UI\HeaderBar'                     => __DIR__.'/../view/Vivo/Backend/UI/HeaderBar.phtml',
            'Vivo\Backend\UI\Logon'                         => __DIR__.'/../view/Vivo/Backend/UI/Logon.phtml',
            'Vivo\Backend\UI\ModulesPanel'                  => __DIR__.'/../view/Vivo/Backend/UI/ModulesPanel.phtml',
            'Vivo\Backend\UI\SiteSelector'                  => __DIR__.'/../view/Vivo/Backend/UI/SiteSelector.phtml',
            'Vivo\Backend\Form\Element'                     => __DIR__.'/../view/Vivo/Backend/Form/Element.phtml',
        ),
    ),

    'service_manager' => array (
        'invokables' => array (
        ),
        'factories' => array (
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
        'plugins' => array (
            'explorer' => array (
                'name' => 'explorer',
                'title' => 'Explorer',
                'componentClass' => 'Vivo\Backend\UI\Explorer\Explorer',
                'icon'  => '.Vivo.resource/backend/img/icons/48x48/explorer.png',
            ),
            'site' => array (
                'name' => 'site',
                'title' => 'Site',
                'componentClass' => 'Vivo\CMS\UI\Blank',
                'icon'  => '.Vivo.resource/backend/img/icons/48x48/site.png',
            ),
            'blank' => array (
                'name' => 'blank',
                'title' => 'Blank',
                'componentClass' => 'Vivo\CMS\UI\Blank',
                'icon'  => '.Vivo.resource/backend/img/icon/explorer.png',
            ),
        ),

    //Backend modules
        'modules'       => array(
            'Bootstrap2_3_0'    => array(
                'enabled'           => true,
            ),
            'TinyMCE3_5_6_Vivo' => array(
                'enabled'           => true,
            ),
        ),
    ),
);

<?php
/**
 * config for backend
 */
return array(
    'templates' => array (
        'template_map' => array(
            'Vivo\UI\Ribbon\Tab'                => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Tab.phtml',
            'Vivo\UI\Ribbon\Group'              => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Group.phtml',
            'Vivo\UI\Ribbon\Item'               => __DIR__.'/../view/Vivo/Backend/UI/Ribbon/Item.phtml',
            'Vivo\Backend\UI\Explorer\Tree'               => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.phtml',
            'Vivo\Backend\UI\Explorer\Tree:Subtree'       => __DIR__.'/../view/Vivo/Backend/UI/Explorer/Tree.Subtree.phtml',
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

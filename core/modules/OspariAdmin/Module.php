<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Module
 *
 * @author fon-pah
 */
namespace OspariAdmin;
class Module {
     public function init ( \NZ\ControllerContainer $container) {
        $conf = $container->getNZ_Config();
       
        $conf->headTPL = self::getViewPath().'/tpl/head.php';
        $conf->tailTPL = self::getViewPath().'//tpl/tail.php';
        
    }
    
    public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getRoutes() {
        $nameSpace = '\''.__NAMESPACE__;
        return array(
            '/'.OSPARI_ADMIN_PATH => array( __NAMESPACE__.'\Controller\DraftController',  'indexAction' ),
            '/'.OSPARI_ADMIN_PATH.'/drafts' => array( __NAMESPACE__.'\Controller\DraftController',  'listAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/create' => array( __NAMESPACE__.'\Controller\DraftController',  'createAction' ),
            '/'.OSPARI_ADMIN_PATH.'/tags' => array( __NAMESPACE__.'\Controller\TagController',  'listAction' ),
            '/'.OSPARI_ADMIN_PATH.'/tag/add' => array( __NAMESPACE__.'\Controller\TagController',  'addAction' ),
            '/'.OSPARI_ADMIN_PATH.'/tag/delete' => array( __NAMESPACE__.'\Controller\TagController',  'deleteAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/auto-save' => array( __NAMESPACE__.'\Controller\DraftController',  'autoSaveAction' ),
            '/'.OSPARI_ADMIN_PATH.'/media/upload' => array( __NAMESPACE__.'\Controller\MediaController',  'uploadAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/edit/{draft_id}' => array( __NAMESPACE__.'\Controller\DraftController',  'editAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/delete/{draft_id}' => array( __NAMESPACE__.'\Controller\DraftController',  'deleteAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/unpublish/{draft_id}' => array( __NAMESPACE__.'\Controller\DraftController',  'unpublishAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/publish' => array( __NAMESPACE__.'\Controller\DraftController',  'publishDraftAction' ),
             '/'.OSPARI_ADMIN_PATH.'/draft/meta/{draft_id}' => array( __NAMESPACE__.'\Controller\DraftController',  'metaAction' ),
            '/'.OSPARI_ADMIN_PATH.'/user' => array( __NAMESPACE__.'\Controller\UserController',  'editAction' ),
            '/'.OSPARI_ADMIN_PATH.'/media/upload' => array( __NAMESPACE__.'\Controller\MediaController',  'uploadAction' ),
            '/'.OSPARI_ADMIN_PATH.'/setting' => array( __NAMESPACE__.'\Controller\SettingController',  'editAction' ),
            '/'.OSPARI_ADMIN_PATH.'/login' => array( __NAMESPACE__.'\Controller\AuthController',  'loginAction' ),
            '/'.OSPARI_ADMIN_PATH.'/logout' => array( __NAMESPACE__.'\Controller\AuthController',  'logoutAction' ),
            '/'.OSPARI_ADMIN_PATH.'/draft/edit-slug' => array( __NAMESPACE__.'\Controller\DraftController',  'updateSlugAction' ),
            '/'.OSPARI_ADMIN_PATH.'/password/reset' => array( __NAMESPACE__.'\Controller\AuthController',  'passwordResetAction' ),
            '/'.OSPARI_ADMIN_PATH.'/password/forgotten' => array( __NAMESPACE__.'\Controller\AuthController',  'passwordForgottenAction' ),
            '/'.OSPARI_ADMIN_PATH.'/update' => array( __NAMESPACE__.'\Controller\UpdateController',  'updateAction' ),
            
             '/'.OSPARI_ADMIN_PATH.'/editor' => array( __NAMESPACE__.'\Controller\DraftController',  'renderEditorAction' ),
             '/'.OSPARI_ADMIN_PATH.'/draft/{draft_id}/add-component' => array( __NAMESPACE__.'\Controller\ComponentController',  'addAction' ),
             '/'.OSPARI_ADMIN_PATH.'/draft/{draft_id}/edit-component' => array( __NAMESPACE__.'\Controller\ComponentController',  'editAction' ),
            
            '/'.OSPARI_ADMIN_PATH.'/component-{component_id}.json' => array( __NAMESPACE__.'\Controller\ComponentController',  'getJSONAction' ),
             '/'.OSPARI_ADMIN_PATH.'/component/embed/{component_id}' => array( __NAMESPACE__.'\Controller\ComponentController',  'embedAction' ),
            
            '/install' => array( __NAMESPACE__.'\Controller\InstallController',  'installAction' ),
            
            
        );
    }
    
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    //for nz2
    public function getClassMap() {
        return __DIR__ . '/autoload_classmap.php';
    }

    //for nz2
    public function getViewPath() {
        return __DIR__ . '/src/'.__NAMESPACE__.'/View';
    }
    
}

?>

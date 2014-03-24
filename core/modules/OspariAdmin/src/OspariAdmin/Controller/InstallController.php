<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

/**
 * InstallController doesn't extend base controller, because BaseController:__construct fetches user information
 */
class InstallController {

    public function installAction(HttpRequest $req, HttpResponse $res) {
        $view = $res->getView();
        $this->setViewParts($view);
        $bs = \Ospari\Bootstrap::getInstance();
        if(!$this->checkPHPVersion()){
            throw new \Exception('A PHP version >= 5.4.0 is required');
        }
        if (!$bs->hasDBConfig()) {
            throw new \Exception('Invalid configuration');
        }

        if (!$this->databaseExist()) {
            throw new \Exception('Please create a database first');
        }

        if ($req->installed) {
            return $res->buildBody('install/installed.php');
        }



        $form = $this->createForm($view, $req);

        if ($req->isPOST()) {
            if ($this->validateForm($form, $req)) {
                $this->install($req);
                return $res->redirect(OSPARI_URL . '/install?installed=1');
            } else {
                $res->setViewVar('error_msg', 'Please fill out all required fields');
            }
        }

        $res->setViewVar('form', $form);


        return $res->buildBody('install/install.php');
    }
    
    protected function validateForm(\NZ\BootstrapForm $form, $req) {
        return $form->validate($req);
    }
    
    private function checkPHPVersion(){
        if(version_compare(PHP_VERSION, '5.4.0')>=0){
            return true;
        }
        else {
            return false;
        }
    }

    private function setViewParts(\NZ\View $view) {
        $view->head = __DIR__ . '/../View/tpl/head_mini.php';
        $view->tail = __DIR__ - '/../View/tpl/tail_mini.php';
    }

    public function databaseExist() {
        $confg = \NZ\Config::getInstance();
        $db_read = $confg->get('db_read');

        $db = \NZ\DB_Adapter::getInstance();
        $sql = "SHOW DATABASES LIKE  '" . $db_read['database'] . "'";
        $result = $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $r = $result->count();
        return (1 == $r);
    }

    protected function install($req) {
        $db = \NZ\DB_Adapter::getInstance();

        foreach ($this->getSql() as $sql) {
            $result = $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        }

        $settting = new \OspariAdmin\Model\Setting();
        $settting->set('email', $req->email);
        $settting->set('title', $req->title);
        $settting->set('ospari_version', '0.1');
        $settting->save();

        $user = new \OspariAdmin\Model\User(array('email' => $req->email));
        $user->email = $req->email;
        $user->save();
        $user->changePassword($req->password);
        return TRUE;
    }

    /**
     * 
     * @param \NZ\View $view
     * @param \NZ\HttpRequest $req
     * @return \NZ\BootstrapForm
     */
    protected function createForm(\NZ\View $view, HttpRequest $req) {

        $form = new \NZ\BootstrapForm($view, $req);
        $form->setID('setting-edit-form');
        $form->setCssClass('form-horizontal');
        $form->addSubmitClass('btn btn-primary');

        $form->setSubmitValue('Install');

        $form->createElement('title')
                ->setLabelText('Blog Title')
                ->setRequired()
        ;

        $form->createElement('email')
                ->setLabelText('Email Address')
                ->setType('email')
                ->setRequired();

        $form->createElement('password')
                ->setLabelText('Password')
                ->setType('password')
                ->setRequired();


        return $form;
    }

    protected function getSql() {
        return __DIR__.'/../data/sql.php';
    }

}

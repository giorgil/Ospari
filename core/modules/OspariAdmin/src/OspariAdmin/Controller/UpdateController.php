<?php

/**
 * Description of DefaultController
 *
 * @author 28h.eu
 */

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use \OspariAdmin\Model;

class UpdateController extends BaseController {

    public function updateAction(HttpRequest $req, HttpResponse $res) {
        $setting = new Model\Setting();
        $isUptoDate = ( $setting->ospari_version == OSPARI_VERSION );

        if ($req->isPost()) {
            try {
                $this->update($setting);
                return $res->sendSuccessMessageJSON('Ospari successfully updated.');
            } catch (\Exception $e) {
                return $res->sendErrorMessageJSON($e->getMessage());
            }
        }
        return $res->buildBody('install/update.php');
    }

    protected function update($setting) {

        $db = \NZ\DB_Adapter::getInstance();

        foreach ($this->getSql() as $sql) {
            $result = $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        }


        $setting->ospari_version = OSPARI_VERSION;
        $setting->save();
    }

    protected function getSql() {
        return include __DIR__ . '/../data/sql.php';
    }

}

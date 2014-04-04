<?php
/**
 * Description of DraftComponent
 *
 * @author fon-pah
 */
class DraftComponent extends \NZ\ActiveRecord{
    public function getTableName() {
       return OSPARI_DB_PREFIX.'draft_compontents';
    }
}

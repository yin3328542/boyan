<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-1-24
 * Time: 下午5:42
 */

class Admin_model extends KR_Model {
    public function _delete($id)
    {
        $res = $this->find($id);
        if($res['role_id'] == 0) {
            return false;
        }
        $this->where(array('id' => $id))->delete();
        return true;
    }
}
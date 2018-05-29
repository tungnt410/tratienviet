<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Audio
 *
 * @author YINLONG
 */
Zend_Loader::loadClass('Model_Application');

class Model_Club extends Model_Application {

    //put your code here
    protected $_name = "club";
    protected $_primary = "id";
    protected $_itemPerPage = 5;
    protected $_rowClass = 'Model_Row_Club';

    public function fetchByCategory($category = 0, $page = 0) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()->from(array('p' => $this->_name), array("p.*"));
        if ($category > 0) {
            $sql = $sql->join(array("cp" => "club_category_has_club"), "cp.club_id = p.id", array());
        }

        $sql = $sql->where("p.active = 1");
        $sql = $sql->limit($this->_itemPerPage, $page * $this->_itemPerPage);
        return $db->fetchAll($sql);
    }

}

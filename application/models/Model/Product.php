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

class Model_Product extends Model_Application {

    //put your code here
    protected $_name = "product";
    protected $_primary = "id";
    protected $_itemPerPage = 10;

    public function fetchByCategory($status = -1, $category = 0, $page = 0) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()->from(array('p' => $this->_name), array("*"));
        if ($category > 0) {
            $sql = $sql->join(array("cp" => "product_category_has_product"), "cp.product_id = p.id", array());
        }
        if ($status > -1) {
            $sql = $sql->where("p.status = $status");
        }
        $sql = $sql->limit($this->_itemPerPage, $page * $this->_itemPerPage);
        return $db->fetchAll($sql);
    }

}

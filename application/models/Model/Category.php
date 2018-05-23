<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author YINLONG
 */
Zend_Loader::loadClass('Model_Application');

class Model_Category extends Model_Application {

    protected $_name = "category";
    protected $_primary = "id";

    public function loadCategoryWithNews($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('n' => 'news'))
                ->join(array('c' => 'category'), 'c.id = n.category_id', array('category_id' => 'c.id', 'category' => 'c.name'))
                ->join(array('u' => 'user'), 'u.id = n.user_id', array('user_id' => 'u.id', 'name' => 'u.name'))
                ->where('c.id = ?', $id)
                ->order(array('updated_at DESC'));
        $result = $db->fetchAll($select);
        return $result;
    }

}

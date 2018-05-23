<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewsController
 *
 * @author tungx
 */
class NewsController extends Amobi_Controller_Action {

    public function init() {
        $this->_action_non_auth = array('show', 'index');
        parent::init();
        Zend_Loader::loadClass('Model_News');
        $this->_model = new Model_News();
    }

    public function predisPatch() {
        parent::predisPatch();
        if (!in_array($this->_action, $this->_action_non_auth) && $this->_user['type'] < 2) {
            $this->_helper->redirector('logout', 'user', 'default', array());
        }
    }

    public function indexAction() {
        $this->view->newspapers = $this->_model->loadNewspapers();
        $this->view->introduction = $this->_model->find(1)->current();
    }

    public function newAction() {
        $this->view->categories = $this->loadCategories();
        $this->view->news = array('title' => '', 'type' => 0, 'category_id' => 0, 'content' => '');
    }

    public function adminAction() {
        $this->view->newspapers = $this->_model->loadNewspapers();
    }

    public function showAction() {
        $param = $this->_arrParam;
        $this->view->categories = $this->loadCategories();
        try {
            $this->view->news = $this->_model->loadNewspapers($param['id'])[0];
        } catch (Exception $exc) {
            $this->_helper->redirector('index', 'news', 'default', array());
        }
    }

    public function createAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $param['id'] = null;
        $param['user_id'] = $this->_user['id'];

        $currentTimeStamp = date('Y-m-d H:i:s');
        $param['created_at'] = $currentTimeStamp;
        $param['updated_at'] = $currentTimeStamp;
        $id = $this->_model->save($param);
        if ($id == -1) {
            $this->view->result = json_encode(array('status' => 2, 'message' => 'Has error when save data'));
        } else {
            $this->view->result = json_encode(array('status' => 1, 'id' => $id));
        }
    }

    public function editAction() {
        $param = $this->_arrParam;
        $this->view->categories = $this->loadCategories();
        $newspapers = $this->_model->find($param['id']);
        try {
            $this->view->news = $newspapers->current();
        } catch (Exception $exc) {
            $this->view->news = array('title' => 'Non newspaper with id ' . $param['id'], 'type' => 0, 'category_id' => 0, 'content' => '');
        }
    }

    public function updateAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $id = $this->_model->save($param);
        if ($id == -1) {
            $this->view->result = json_encode(array('status' => 2, 'message' => 'Has error when save data'));
        } else {
            $this->view->result = json_encode(array('status' => 1, 'id' => $id));
        }
    }

    public function destroyAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->delete($param)));
    }

    private function loadCategories() {
        Zend_Loader::loadClass('Model_Category');
        $categoryModel = new Model_Category();
        return $categoryModel->fetchAll();
    }

}

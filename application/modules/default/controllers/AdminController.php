<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AudioController
 *
 * @author YINLONG
 */
class AdminController extends Amobi_Controller_Action {

	public function init() {
		parent::init();
		Zend_Loader::loadClass('Model_User');
		$this->_model = new Model_User();
	}

	public function predisPatch() {
		parent::predisPatch();
	}

	public function indexAction() {
	}

	public function usersAction(){

	}

	public function updateUserAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		$params['choose_type'] = '';
		$id = $this->_model->save($params);

		$this->_helper->redirector('index', 'users', 'admin', array());
	}

	public function controlAction(){		
		$this->view->user = $this->_model->find($_SESSION["id"])->current();
		$this->view->users = $this->_model->fetchAll();
	}

	public function papersAction(){

	}

	public function createUserAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		$id = $this->_model->save($params);
		$this->view->result = json_encode(array('status' => 1, 'id' => $id));
	}

	public function destroyAction() {
		$this->_helper->layout()->disableLayout();
		$param = $this->_arrParam;
		$this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->delete($param)));
	}
}


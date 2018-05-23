<?php

class StoreController extends Amobi_Controller_Action {

	public function init() {
		parent::init();
		Zend_Loader::loadClass('Model_Store');
		$this->_model = new Model_Store();
	}

	public function predisPatch() {
		parent::predisPatch();
		$this->view->errors = array();
	}

	public function indexAction() {
		$this->view->store = $this->_model->fetchAll();
		$this->view->stores = $this->_model->find($_SESSION['id'])->current();
	}

	public function createAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		$store_params = array();
		foreach ($params['store'] as $key => $value) {
			$store_params[$key] = $value;
		}
		$store_params['user_id'] = $_SESSION['id'];
		$store_id = $this->_model->save($store_params);
		$this->_helper->redirector('index', 'store', 'default', array());
	}
	public function newAction() {

	}

	public function updateAction() {
		$this->_helper->layout()->disableLayout();
		$param = $this->_arrParam;
		$paper_params = array();
		$id = $this->_model->save($param);
		if ($id == -1) {
			$this->view->result = json_encode(array('status' => 2, 'message' => 'Tên cửa hàng đã tồn tại'));
		} else {
			$this->view->result = json_encode(array('status' => 1, 'id' => $id));
		}
		$this->_helper->redirector('index', 'store', 'default', array());
	}

	public function editAction() {
		$param = $this->_arrParam;
		$store = $this->_model->find($param['id']);
		if (count($store) > 0) {
			$this->view->store = $store->current();
		}
		$this->view->updated_content = '';
		if(isset($_SESSION['updated_content'])){
			$this->view->updated_content = $_SESSION['updated_content'];
			unset($_SESSION['updated_content']);   
		}

		Zend_Loader::loadClass('Model_User');
		$userModel = new Model_User();
	}

	public function destroyAction() {
		$param = $this->_arrParam;
		$this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->delete($param)));
	}

	public function searchAction() {
		parent::searchAction();
		$result = array();
		foreach ($this->view->result as $key => $server) {
			$result[$key] = $server->toArray();
		}
		$this->view->result = json_encode($result);
	}

}

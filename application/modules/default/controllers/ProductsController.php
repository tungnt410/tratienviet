<?php

class ProductsController extends Amobi_Controller_Action {

	private $_status;

	public function init() {
		parent::init();
		Zend_Loader::loadClass('Model_Products');
		$this->_model = new Model_Products();
	}

	public function predisPatch() {
		parent::predisPatch();
	}

	public function indexAction() {
		$this->view->products = $this->_model->fetchAll();
		$this->view->product = $this->_model->find($_SESSION['id'])->current();
	}

	public function createAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		$products_params = array();
		foreach ($params['products'] as $key => $value) {
			$products_params[$key] = $value;
		}
		if(!file_exists("uploads")) mkdir("uploads", 0777, true);
		$user_folder = "uploads/" . $_SESSION['id'];
		if(!file_exists("uploads")) mkdir($user_folder, 0777, true);
		foreach ($_FILES['products']['name'] as $key => $value) {
			$filePath = $user_folder . "/" . $value;
			move_uploaded_file($_FILES['products']['tmp_name'][$key], $filePath);
			$products_params[$key] = '/' . $filePath;
		}
		$products_params['user_id'] = $_SESSION['id'];

		$products_id = $this->_model->save($products_params);
		$this->_helper->redirector('index', 'products', 'default', array());
	}

	public function newAction() {

	}

	public function editAction() {
		$param = $this->_arrParam;
		$products = $this->_model->find($param['id']);
		if (count($products) > 0) {
			$this->view->products = $products->current();
		}
		$this->view->updated_content = '';
		if(isset($_SESSION['updated_content'])){
			$this->view->updated_content = $_SESSION['updated_content'];
			unset($_SESSION['updated_content']);   
		}

		Zend_Loader::loadClass('Model_User');
		$userModel = new Model_User();

	}

	public function updateAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		if (!empty($_FILES['image']['name'])) {
			
			$user_folder = "uploads/" . $_SESSION['id'];
			if(!file_exists("uploads")) mkdir("uploads", 0777, true);
			if(!file_exists($user_folder)) mkdir($user_folder, 0777, true);
			$filePath = $user_folder . "/" . $_FILES['image']['name'];
			move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
			$params['images'] = '/' . $filePath;
		}
		$this->_model->save($params);
		$this->_helper->redirector('index', 'products', 'default', array());
	}

	public function showAction() {
		$this->_helper->layout()->disableLayout();
		$param = $this->_arrParam;
		$products = $this->_model->find($param['id']);
		Zend_Loader::loadClass('Model_User');
		$userModel = new Model_User();
	}

	public function destroyAction() {
		$this->_helper->layout()->disableLayout();
		$param = $this->_arrParam;
		try {
			$products = $this->_model->find($param['id'])->current();
			$this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->delete($param)));
		} catch (Exception $exc) {
			$this->view->result = json_encode(array('status' => 2, 'message' => 'you are not author'));
		}
	}

}

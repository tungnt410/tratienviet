<?php

class ClubsController extends Amobi_Controller_Action {

    public function init() {
        $this->_action_non_auth = array('api-list');
        parent::init();
        Zend_Loader::loadClass('Model_Club');
        $this->_model = new Model_Club();
    }

    public function predisPatch() {
        parent::predisPatch();
        $this->view->errors = array();
    }

    public function indexAction() {
        $this->view->clubs = $this->_model->fetchAll();
        $this->view->club = $this->_model->find($_SESSION['id'])->current();
    }

    public function createAction() {
        $param = $this->_arrParam;
        $param['id'] = null;
        $id = $this->_model->save($param);

        if ($id == -1) {
            $this->view->message = 'Tên CLB đã tồn tại. Vui lòng nhập tên khác.';
            $this->render('new');
        } else {
            if ($this->_user['type'] == 2) {
                $this->view->result = json_encode(array('status' => 1, 'id' => $id));
            } else if ($this->_user['type'] == 1) {
                $this->view->result = json_encode(array('status' => 2, 'id' => $id));
            }
            $this->_helper->redirector('index', 'clubs', 'default', array());
        }
    }

    public function newAction() {
        
    }

    public function updateAction() {
        $this->_helper->layout()->disableLayout();
        $params = $this->_arrParam;
        $this->_model->save($params);
        $this->_helper->redirector('index', 'clubs', 'default', array());
    }

    public function editAction() {
        $param = $this->_arrParam;
        $clubs = $this->_model->find($param['id']);
        if (count($clubs) > 0) {
            $this->view->clubs = $clubs->current();
        }
        $this->view->updated_content = '';
        if (isset($_SESSION['updated_content'])) {
            $this->view->updated_content = $_SESSION['updated_content'];
            unset($_SESSION['updated_content']);
        }

        Zend_Loader::loadClass('Model_User');
        $userModel = new Model_User();
    }

    public function showAction() {
        $this->view->club_has_user = $this->_model->fetchAll("user_id = '" . $_SESSION['id'] . "'");
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

    public function apiListAction() {
        $this->_helper->layout()->disableLayout();
        $params = $this->_arrParam;
        $_clubs = $this->_model->fetchByCategory();
        $clubs = array();
        Zend_Loader::loadClass("Model_Product");
        $productModel = new Model_Product();
        foreach ($_clubs as $value) {
            $club = $value;
            unset($club['active']);
            unset($club['priority']);
            unset($club['user_id']);
            unset($club['created_at']);
            unset($club['updated_at']);
            $imgs = explode("|", $value['images']);
            $club['images'] = $imgs;
            $_products = $productModel->fetchByClub($value['id']);
            $products = array();
            foreach ($_products as $p) {
                $imgs = explode("|", $p['images']);
                $p['images'] = $imgs;
                $products[] = $p;
            }
            $club['products'] = $products;
            $clubs[] = $club;
        }
        $this->view->result = json_encode($clubs);
    }

}

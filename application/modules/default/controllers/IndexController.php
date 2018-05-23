<?php

class IndexController extends Amobi_Controller_Action {

    public function init() {
        $this->_action_non_auth = array('index');
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

    public function newAction() {
        
    }

    public function editAction() {        
        $param = $this->_arrParam;
        $papers = $this->_model->find($param['id']);
        if (count($papers) > 0) {
            $this->view->paper = $papers->current();
        }
        Zend_Loader::loadClass('Model_AuthorPaper');
        $authorPaperModel = new Model_AuthorPaper();

        $authorPapers = $authorPaperModel->fetchAll("paper_id = '" . $param['id'] . "'");

        Zend_Loader::loadClass('Model_Author');
        $authorMode = new Model_Author();
        $authors = array();
        foreach ($authorPapers as $authorPaper) {
            try {
                $author = $authorMode->find($authorPaper['author_id'])->current();
                $authors[] = $author;
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        if (count($authors) > 0) {
            $this->view->author = $authors[0];
        } else {
            $this->view->author = array();
        }
        $this->view->user = $this->_user;
        $this->view->disable = $this->_user['id'] == $this->view->paper['user_id'] ? '':'disabled';
        $this->view->updated_content = '';
        if(isset($_SESSION['updated_content'])){
            $this->view->updated_content = $_SESSION['updated_content'];
            unset($_SESSION['updated_content']);   
        }

        Zend_Loader::loadClass('Model_User');
        $userModel = new Model_User();
        $this->view->reviewers = $userModel->fetchAll("type = '1'");
		
    }

    public function createAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $param['id'] = null;
        $this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->save($param)));
    }

    public function updateAction() {
        $this->_helper->layout()->disableLayout();

        $param = $this->_arrParam;
        if (array_key_exists("manager_action", $param)) {
            $server = $this->_model->find($param['id']);
            if (count($server) > 0) {
                $server = $server[0];
                $this->view->result = json_encode(array('status' => 1, 'message' => $server->{$param['manager_action']}()));
            } else {
                $this->view->result = json_encode(array('status' => 2, 'message' => 'Server is not exist!'));
            }
        } else {
            $this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->save($param)));
        }
    }

    public function destroyAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->delete($param)));
    }

    public function searchAction() {
        $status = NULL;
        if (array_key_exists('status', $this->_arrParam)) {
            $status = $this->_arrParam['status'];
            unset($this->_arrParam['status']);
        }
        parent::searchAction();
        $result = array();
        foreach ($this->view->result as $key => $server) {
            $result[$key] = $server->toArray();
            $result[$key]['status'] = $server->checkStatus();
        }
        if ($status != NULL) {
            $result_status = array();
            foreach ($result as $value) {
                if ($value['status'] == $status) {
                    $result_status[] = $value;
                }
            }
            $result = $result_status;
        }
        $this->view->result = json_encode($result);
    }

}

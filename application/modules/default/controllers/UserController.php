<?php

class UserController extends Amobi_Controller_Action {

    public function init() {
        $this->_action_non_auth = array('login', 'forgot', 'resetpassword',
            'updatepassword', 'signup', 'create',
            'api-create', 'api-active', 'api-update-password', 'api-login');
        parent::init();
        Zend_Loader::loadClass('Model_User');
        $this->_model = new Model_User();
    }

    public function predisPatch() {
        parent::predisPatch();
        $this->view->errors = array();
    }

    public function indexAction() {
        $this->view->users = $this->_model->fetchAll();
        $this->view->user = $this->_model->find($_SESSION['id'])->current();
    }

    public function showAction() {
        $param = $this->_arrParam;
        try {
            $this->view->user = $this->_model->find($param['id'])->current();
        } catch (Exception $exc) {
            $this->_helper->redirector('index', 'user', 'default', array());
        }
    }

    public function createAction() {
        $param = $this->_arrParam;
        $param['id'] = null;

        $password = $this->_model->generateRandomString(5) . time();
        $param['password_by_system'] = md5($password);
        unset($param['reset_password']);
        $id = $this->_model->save($param);

        if ($id == -1) {
            $this->view->message = 'The email has been already been registered in TraTienViet system. <br>
            Please use another email or ask the system to <a href="/user/forgot">reset your password</a>.';
            $this->render('signup');
        } else {
            Zend_Loader::loadClass('Model_Mail');
            $mailModel = new Model_Mail();
            $mailModel->sendEmail($param['email'], 'Active account', $this->createEmailForActiveEmail($password, $param['password_by_system']));
            $this->view->result = json_encode(array('status' => 1, 'id' => $id));
            $this->_helper->redirector('index', 'index', 'default', array());
        }
    }

    public function apiCreateAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $param['id'] = null;
        $time = time() . "";
        $password = $this->_model->generateRandomString(3) . substr($time, strlen($time) - 3);
        $param['created_at'] = date("Y-m-d H:i:s");
        $param['password_by_system'] = md5($password);
        unset($param['reset_password']);
        $id = $this->_model->save($param);
        if ($id == -1) {
            $this->view->result = json_encode(array('status' => 2, 'message' => 'Email đã tồn tại'));
        } else {
            try {
                Zend_Loader::loadClass('Model_Mail');
                $mailModel = new Model_Mail();
                $mailModel->sendEmail($param['email'], 'Active account', $this->createEmailForActiveEmail($password, $param['password_by_system']));
            } catch (Exception $e) {
                
            }
            $this->view->result = json_encode(array('status' => 1, 'id' => $id));
        }
    }

    public function signupAction() {
        
    }

    public function editAction() {
        $param = $this->_arrParam;
        $user = $this->_model->find($param['id']);
        if (count($user) > 0) {
            $this->view->user = $user->current();
        }
        $this->view->updated_content = '';
        if (isset($_SESSION['updated_content'])) {
            $this->view->updated_content = $_SESSION['updated_content'];
            unset($_SESSION['updated_content']);
        }
    }

    public function updateAction() {
        $param = $this->_arrParam;

        $this->_helper->layout()->disableLayout();
        if ($param['reset_password'] == 1) {
            $password = $this->_model->generateRandomString(5) . time();
            $param['password_by_system'] = md5($password);
            $param['password'] = md5($this->_model->generateRandomString(5) . time());
        }
        unset($param['reset_password']);
        $id = $this->_model->save($param);
        if ($id == -1) {
            $this->view->result = json_encode(array('status' => 2,
                'message' => 'Email is existed'));
        } else {
            $this->view->result = json_encode(array('status' => 1, 'id' => $id));
        }
        if (key_exists('password_by_system', $param)) {
            Zend_Loader::loadClass('Model_Mail');
            $mailModel = new Model_Mail();
            $mailModel->sendEmail($param['email'], 'Reset password', $this->createEmailForActiveEmail($password, $param['password_by_system']));
        }
        $this->_helper->redirector('index', 'user', 'default', array());
    }

    public function updatepasswordAction() {
        $param = $this->_arrParam;
        $this->checkPasswordParam($param);
        if (count($this->view->errors) == 0) {
            $new_param = array();
            $new_param['password_by_system'] = $param['password'];
            $new_param['password'] = md5($param['new_password']);
            $this->_model->updatePassword($new_param);
            $this->_helper->redirector('index', 'index', 'default', array());
        } else {
            $this->view->password = $param['password'];
            $this->render('resetpassword');
        }
    }

    public function destroyAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $this->view->result = json_encode(array('status' => 1,
            'id' => $this->_model->delete($param)));
    }

    public function searchAction() {
        parent::searchAction();
        $result = array();
        foreach ($this->view->result as $key => $server) {
            $result[$key] = $server->toArray();
        }
        $this->view->result = json_encode($result);
    }

    public function resetpasswordAction() {
        $this->logout();
        $param = $this->_arrParam;
        $this->view->password = $param['password'];
        $user = $this->_model->fetchAll("password_by_system = '"
                . $param['password'] . "'");
        if (count($user) == 0) {
            $this->view->errors[] = 'Password is changed';
        }
    }

    public function apiActiveAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        if (!key_exists('password', $param)) {
            $this->view->result = json_encode(array('status' => 0,
                'message' => 'Thiếu tham số password'));
        }
        $users = $this->_model->fetchAll("password_by_system = '"
                . md5($param['password']) . "'");
        if (count($users) == 0) {
            $this->view->result = json_encode(array('status' => 2,
                'message' => 'Tài khoản không tồn tại hoặc đã đước kích hoạt trước đó'));
        } else {
            $this->view->result = json_encode(array('status' => 1, 'id' => $users[0]['id']));
        }
    }

    public function apiUpdatePasswordAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        if (!key_exists('current_password', $param)) {
            $this->view->result = json_encode(array('status' => 0,
                'message' => 'Thiếu tham số current password'));
        }

        if (!key_exists('password', $param)) {
            $this->view->result = json_encode(array('status' => 0,
                'message' => 'Thiếu tham số password'));
        }

        if (!key_exists('id', $param)) {
            $this->view->result = json_encode(array('status' => 0,
                'message' => 'Thiếu tham số ID'));
        }

        $users = $this->_model->find($param['id']);
        if (count($users) == 0) {
            $this->view->result = json_encode(array('status' => 2,
                'message' => 'Tài khoản không tồn tại'));
            return;
        }
        $user = $users[0];
        if ($user['password_by_system'] != md5($param['current_password'])) {
            $this->view->result = json_encode(array('status' => 2,
                'message' => 'Tài khoản đã được kích hoạt trước đó'));
            return;
        }
        $params = array('id' => $user['id'],
            'password_by_system' => NULL,
            'password' => md5($param['password']));
        $this->_model->save($params);
        $this->view->result = json_encode(array('status' => 1));
    }

    public function apiLoginAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_arrParam;
        $password = NULL;
        $key = 'email';
        $value = NULL;

        if (key_exists('password', $param)) {
            $password = $param['password'];
        }

        if (key_exists('email', $param)) {
            $key = 'email';
        }

        if (key_exists('telephone', $param)) {
            $key = 'telephone';
        }

        if (key_exists($key, $param)) {
            $value = $param[$key];
        }

        if ($password == NULL || $value == NULL) {
            $this->view->result = json_encode(array('status' => 0,
                'message' => 'Bạn phải nhập đầy đủ các trường yêu cầu'));
            return;
        }
        $users = $this->_model->fetchAll("password = '"
                . md5($param['password']) . "' and $key = '$value'");
        if (count($users) == 0) {
            $this->view->result = json_encode(array('status' => 0,
                'message' => "$key sai hoăc password sai"));
            return;
        }
        $user = $users[0];
        $session = $this->_model->generateRandomString();
        $this->view->result = json_encode(array('status' => 1,
            'user' => array(
                'id' => $user['id'],
                'name' => $user['name'],
                'type' => $user['type'],
                'session' => $session
        )));
        $this->_model->save(array('id' => $user['id'], 'session' => md5($session)));
    }

    public function loginAction() {
        $param = $this->_arrParam;
        if (key_exists('email', $param) && key_exists('password', $param)) {
            $user = $this->_model->fetchAll("password = '"
                    . md5($param['password']) . "' and email= '"
                    . $param['email'] . "'");
            if (count($user) == 0) {
                $this->view->errors[] = 'Email hoặc password is not correct!';
            } else {
                $user = $user[0];
                $_SESSION['id'] = $user['id'];
                $_SESSION['type'] = $user['type'];
                $_SESSION['session'] = $user['session'];
                // var_dump($params);die();
                $this->_helper->redirector('index', 'index', 'default', array());
            }
        }
    }

    public function forgotAction() {
        $param = $this->_arrParam;
        if (key_exists('email', $param)) {
            $user = $this->_model->fetchAll("email= '" . $param['email'] . "'");
            if (count($user) == 0) {
                $this->view->errors[] = 'Email not matches!';
            } else {
                $this->view->email = $param['email'];
                $param = array();
                $time = time() . "";
                $password = $this->_model->generateRandomString(3)
                        . substr($time, strlen($time) - 3);

                $param['password_by_system'] = md5($password);
                $param['password'] = md5($this->_model->generateRandomString(1)
                        . substr($time, strlen($time) - 3));
                $param['id'] = $user[0]['id'];
                $this->_model->save($param);
                Zend_Loader::loadClass('Model_Mail');
                $mailModel = new Model_Mail();
                $mailModel->sendEmail($this->view->email, 'Reset password', $this->createEmailForActiveEmail($password, $param['password_by_system']));
                $this->render('resultforgot');
            }
        }
    }

    public function resultforgotAction() {
        
    }

    public function logoutAction() {
        $this->logout();
        $this->_helper->redirector('login', 'user', 'default', array());
    }

    private function logout() {
        unset($_SESSION['id']);
        unset($_SESSION['session']);
        unset($_SESSION['type']);
    }

    private function createEmailForActiveEmail($password, $md5) {
        $fullBaseUrl = $this->view->serverUrl() . $this->view->baseUrl();

        $fullBaseUrl = "http://tratienviet.vn";
        $html = 'Your account has been created and is awaiting activation, please click the ';
        $html .= '<a href="' . $fullBaseUrl
                . '/user/resetpassword?password='
                . $md5 . '">Link</a> to activate<br>';
        $html .= 'Your password: ' . $password . '<br>';
        $html .= '<strong>Thanks!</strong>';
        return $html;
    }

    private function checkPasswordParam($param) {
        $this->view->errors = array();
        if (md5($param['current_password']) != $param['password']) {
            $this->view->errors[] = 'The password is not correct';
        }
        if ($param['current_password'] == $param['new_password']) {
            $this->view->errors[] = 'New password is the same old password';
        }
        if ($param['repeat_password'] != $param['new_password']) {
            $this->view->errors[] = 'The new password is another password that is confirm';
        }
    }

}

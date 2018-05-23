<?php

class PaperController extends Amobi_Controller_Action {

	private $_type;

	public function init() {
		parent::init();
		Zend_Loader::loadClass('Model_Paper');
		$this->_model = new Model_Paper();
	}

	public function predisPatch() {
		parent::predisPatch();
	}

	public function indexAction() {
		if ($this->_user['type'] == 2) {
			Zend_Loader::loadClass('Model_User');
			$userModel = new Model_User();
			$users = $userModel->fetchAll('type = 0');
			$usersView = array();
			foreach ($users as $user) {
				$userInfo = array();
				$userInfo['id'] = $user['id'];
				$userInfo['name'] = $user['firstname'] . ' ' . $user['lastname'];
				$userInfo['email'] = $user['email'];
				$userInfo['papers'] = $this->_model->fetchAll("user_id = '" . $user['id'] . "'");
				$papers = array();
				foreach ($userInfo['papers'] as $paper) {
					$apaper['id'] = $paper['id'];
					$apaper['title'] = $paper['title'];
					$apaper['type'] = $paper['type'];
					$apaper['fullPaper'] = $paper['fullPaper'];
					$apaper['user_id'] = $paper['user_id'];
					$reviewer1 = $userModel->find($paper['review_1'])->current();	
					$apaper['review1_name'] = $reviewer1['firstname'].' '.$reviewer1['lastname'];
					$reviewer2 = $userModel->find($paper['review_2'])->current();	
					$apaper['review2_name'] = $reviewer2['firstname'].' '.$reviewer2['lastname'];
					$papers[] = $apaper;
				}
				$userInfo['papers'] = $papers;
				$usersView[] = $userInfo;
			}
			$this->view->users = $usersView;           
		} else if ($this->_user['type'] == 1) {
			Zend_Loader::loadClass('Model_User');
			$userModel = new Model_User();
			$users = $userModel->fetchAll();
			$usersView = array();
			foreach ($users as $user) {
				$userInfo = array();
				$userInfo['id'] = $user['id'];
				$userInfo['name'] = $user['firstname'] . ' ' . $user['lastname'];
				$userInfo['email'] = $user['email'];
				$userInfo['papers'] = $this->_model->fetchAll("user_id = '" . $user['id'] . "'");
				$papers = array();

				foreach ($userInfo['papers'] as $paper) {
					$apaper['id'] = $paper['id'];
					$apaper['title'] = $paper['title'];
					$apaper['type'] = $paper['type'];
					$apaper['fullPaper'] = $paper['fullPaper'];
					$apaper['user_id'] = $paper['user_id'];
					if($paper['review_1'] == $this->_user['id'] || $paper['review_2'] == $this->_user['id']){
						$papers[] = $apaper;
					}
				}
				if(count($papers) > 0){
					$userInfo['papers'] = $papers;
					$usersView[] = $userInfo;
				}

			}
			$this->view->users = $usersView;
		} else {
			$this->_helper->redirector('logout', 'user', 'default', array());
		}

	}

	public function createAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		$paper_params = array();
		$author_params = array();
		foreach ($params['author'] as $key => $value) {
			$author_params[$key] = $value;
		}
		foreach ($params['paper'] as $key => $value) {
			$paper_params[$key] = $value;
		}
		mkdir("uploads", 0777, true);
		mkdir("uploads/papers", 0777, true);
		$user_folder = "uploads/papers/" . $_SESSION['id'];
		mkdir($user_folder, 0777, true);
		foreach ($_FILES['paper']['name'] as $key => $value) {
			$filePath = $user_folder . "/" . $value;
			move_uploaded_file($_FILES['paper']['tmp_name'][$key], $filePath);
			$paper_params[$key] = '/' . $filePath;
		}

		Zend_Loader::loadClass('Model_Author');
		$authorMode = new Model_Author();
		$author_id = $authorMode->save($author_params);

		$paper_params['user_id'] = $_SESSION['id'];

		$paper_id = $this->_model->save($paper_params);

		Zend_Loader::loadClass('Model_AuthorPaper');
		$authorPaperModel = new Model_AuthorPaper();
		$authorPaperModel->save(array('author_id' => $author_id, 'paper_id' => $paper_id));
		$this->_helper->redirector('index', 'index', 'default', array());
	}

	public function signupAction() {

	}

	public function updateAction() {
		$this->_helper->layout()->disableLayout();
		$params = $this->_arrParam;
		$paper_params = array();
		$author_params = array();

		$uploaded = array();
		if(!file_exists("uploads"))
			mkdir("uploads", 0777, true);
		if(!file_exists("uploads"))
			mkdir("uploads/papers", 0777, true);
		$user_folder = "uploads/papers/" . $_SESSION['id'];
		if(!file_exists($user_folder))
			mkdir($user_folder, 0777, true);
		$file_types = array('file' => 'Abstract uploaded successfully', 'copyright' => 'Copyright uploaded successfully', 'fullPaper'=>'Full paper uploaded successfully', 'presen' =>'Presentation uploaded successfully', 'inviLetter'=>'Invitation Letter uploaded successfully', 'biography'=>'Biography uploaded successfully');

		foreach ($_FILES['paper']['name'] as $key => $value) {
			if (empty($value)) {
				unset($paper_params[$key]);
				continue;
			}
			$filePath = $user_folder . "/" . $value;
			if(isset($file_types[$key]))
				$uploaded[] = $file_types[$key];
			move_uploaded_file($_FILES['paper']['tmp_name'][$key], $filePath);
			$paper_params[$key] = '/' . $filePath;
		}

		if (empty($params['paper']['type']) && empty($params['review_1']) && empty($params['review_2'])) {

			foreach ($params['author'] as $key => $value) {
				$author_params[$key] = $value;
			}
			foreach ($params['paper'] as $key => $value) {
				$paper_params[$key] = $value;
			}
			try {
				$paper = $this->_model->find($paper_params['id'])->current();

			} catch (Exception $exc) {
				$this->_helper->redirector('logout', 'user', 'default', array());
			}			
			Zend_Loader::loadClass('Model_Author');
			$authorMode = new Model_Author();
			$authorMode->save($author_params);
			if($this->_user['type'] != 1)
				$paper_params['user_id'] = $_SESSION['id'];
		} else {

			if ($this->_user['type'] != 2 && $params['paper']['type'] != 3 && $params['paper']['review_type_1'] != 1 && $params['paper']['review_type_1'] != 2 && $params['paper']['review_type_2'] != 1 && $params['paper']['review_type_2'] != 2) {
				$this->_helper->redirector('logout', 'user', 'default', array());
			}
			

			$paper_params['id'] = $params['paper']['id'];
			$paper_params['type'] = $params['paper']['type'];
			$paper_params['review_type_1'] = $params['paper']['review_type_1'];
			$paper_params['review_type_2'] = $params['paper']['review_type_2'];
			$paper_params['notifi'] = $params['paper']['notifi'];
			if(!empty($params['review_1'])){
				$paper_params['review_1'] = $params['review_1'];
			}
			if(!empty($params['review_2'])){
				$paper_params['review_2'] = $params['review_2'];
			}
		}
		try {
			$this->_model->save($paper_params); 
			Zend_Loader::loadClass('Model_User');
			$userModal = new Model_User();
			$user = $userModal->find($paper['user_id'])->current();
			Zend_Loader::loadClass('Model_Mail');
			$mailModel = new Model_Mail();
			if (!empty($params['user']['type']) && ($paper_params['type'] == 1 || $paper_params['type'] == 2)){
				$status = $paper_params['type'] == 1 ? 'approved' : 'rejected';				
				$mailModel->sendEmail($user['email'], 'Notification from MMMS2018', $this->createEmailForUpdateStatus($paper['title'], $status, $paper_params['notifi']));            
			} elseif ($params['paper']['type'] == 3){
				$mailModel->sendEmail('mmms@hust.edu.vn', 'Notification from MMMS2018', $this->createEmailForRefuseReview($paper['title']));
			} elseif ($params['paper']['type'] == 4){
				Zend_Loader::loadClass('Model_User');
				$userModel = new Model_User();
				$mailModel->sendEmail($userModel->find($paper_params['review_1'])->current()['email'], 'Notification from MMMS2018', $this->createEmailForAssignReviewer($paper['title']));
				$mailModel->sendEmail($userModel->find($paper_params['review_2'])->current()['email'], 'Notification from MMMS2018', $this->createEmailForAssignReviewer($paper['title']));							
			}
		} catch (Exception $exc) {
			echo $exc->getTraceAsString();
		}

		if(!empty($uploaded)){
			$updated_content = '';
			foreach ($uploaded as $value) {				
				$updated_content =$updated_content. '- ' . $value.'|';
			}
			$_SESSION['updated_content'] = $updated_content;				
		}
		
		$this->_helper->redirector('edit', 'index', 'default', array('id'=>$paper_params['id']));
	}

	public function destroyAction() {
		$this->_helper->layout()->disableLayout();
		$param = $this->_arrParam;
		try {
			$paper = $this->_model->find($param['id'])->current();
			if ($paper['user_id'] != $this->_user['id']) {
				$this->view->result = json_encode(array('status' => 2, 'message' => 'you are not author'));
			}
			$this->view->result = json_encode(array('status' => 1, 'id' => $this->_model->delete($param)));
		} catch (Exception $exc) {
			$this->view->result = json_encode(array('status' => 2, 'message' => 'you are not author'));
		}
	}

	private function createEmailForUpdateStatus($title, $status, $notifi) {
		if ($status == rejected) {
			$html = '<strong>Dear Authors,</strong><br><br>';
			$html .= 'Your paper abstract for MMMS2018 has been reviewed.<br><br>';
			$html .= 'Result: <strong>Rejected</strong>.<br><br>';
			$html .= '<strong>Note from the Scientific Committee</strong>: ' . $notifi . '<br><br>';
			$html .= '<strong>MMMS2018 Online System</strong><br><br><br>';
			$html .= '<span style="color:#a4a4a4;">[mmms2018]</span><br>';
			$html .= '<span style="color:#a4a4a4;">website: <a href="http://mmms2018.hust.edu.vn">http://mmms2018.hust.edu.vn</a>  |  email: <a href="mailto:mmms@hust.edu.vn">mmms@hust.edu.vn</a>  |  hotline: <a href="#">+84 982837465</a> </span><br>';
			$html .= '<span style="color:#a4a4a4;">FB: <a href="facebook.com/mmms2018/">facebook.com/mmms2018/</a> </span><br><br>';
			$html .= '<span style="color:#a4a4a4;">Science Secretary: Assoc. Prof. Nguyen Duc Toan | <a href="mailto:toan.nguyenduc@hust.edu.vn">toan.nguyenduc@hust.edu.vn</a><br>';
			$html .= '<span style="color:#a4a4a4;">Local Organizer: Assoc. Prof. Nguyen Thi Hong Minh | <a href="mailto:minh.nguyenthihong@hust.edu.vn">minh.nguyenthihong@hust.edu.vn</a><br>';
			return $html;
		} else {
			$html = '<strong>Dear Authors,</strong><br><br>';
			$html .= 'Your paper abstract for MMMS2018 has been reviewed.<br><br>';
			$html .= 'Result: <strong>Accepted</strong>.<br><br>';
			$html .= '<strong>Note from the Scientific Committee</strong>: <br><br>';
			$html .= 'Sincere apologies from the MMMS2018 organizer. Due to the overwhelming number of abstracts submitted, the abstract review process for MMMS2018 took longer than expected. Your understanding is highly appreciated.<br><br>';
			$html .= '<strong>Authors of this submission are kindly requested by the Scientific Committee to revise the abstract and write the full paper to show the significance of the research in views of sustainable development. Academic English style should be observed</strong>.<br><br>';
			$html .= 'Submitted papers will be reviewed by the members of the International Scientific Committee and, if accepted, published in <strong>Journal of Applied Mechanics and Materials - Scientific.net Switzerland</strong>.<br><br>';
			$html .= 'For the submission of final manuscript, please follow strictly the requirements:<br><br><br>';
			$html .= '1- Style of manuscripts: Abstracts and full papers should be prepared according MMMS2018 <a href="http://mmms2018.hust.edu.vn/category/show?id=11">templates</a>.<br><br>';
			$html .= '2- Full paper deadline: February 01, 2018.<br><br>';
			$html .= '3- Number of pages: 6 to 8 pages.<br><br>';
			$html .= '4- English: High level is required. Proof reading by a native speaker is strongly recommended for the submission to be included in MMMS2018 Proceedings.<br><br>';
			$html .= '5- Publication ethics: Please also observe and follow the <a href="http://mmms2018.hust.edu.vn/category/show?id=10">publication ethics</a> as instructed in MMMS2018 website.<br><br>';
			$html .= '6- Way of submission: Abstracts and full papers prepared in <strong>PDF format</strong> to be <a href="http://mmms2018.hust.edu.vn/paper">submitted only via home page</a> of MMMS2018. No email submission.<br><br>';
			$html .= '7- Notification of paper acceptance: by March 15, 2018.<br><br>';
			$html .= '8- Payment of <a href="http://mmms2018.hust.edu.vn/category/show?id=13">registration fee</a> is open by March 16, 2018. The accepted paper is automatically removed from the system if no payment by April 05, 2018.<br><br>';
			$html .= '9- One of the authors must present at MMMS2018 for each paper. If the presenting author is a student sponsored by a VASE member, the participation of the VASE member in the conference is desired.<br><br>';
			$html .= '<strong>MMMS2018 Online System</strong><br><br><br>';
			$html .= '<span style="color:#a4a4a4;">[mmms2018]</span><br>';
			$html .= '<span style="color:#a4a4a4;">website: <a href="http://mmms2018.hust.edu.vn">http://mmms2018.hust.edu.vn</a>  |  email: <a href="mailto:mmms@hust.edu.vn">mmms@hust.edu.vn</a>  |  hotline: <a href="#">+84 982837465</a> </span><br>';
			$html .= '<span style="color:#a4a4a4;">FB: <a href="facebook.com/mmms2018/">facebook.com/mmms2018/</a> </span><br><br>';
			$html .= '<span style="color:#a4a4a4;">Science Secretary: Assoc. Prof. Nguyen Duc Toan | <a href="mailto:toan.nguyenduc@hust.edu.vn">toan.nguyenduc@hust.edu.vn</a><br>';
			$html .= '<span style="color:#a4a4a4;">Local Organizer: Assoc. Prof. Nguyen Thi Hong Minh | <a href="mailto:minh.nguyenthihong@hust.edu.vn">minh.nguyenthihong@hust.edu.vn</a><br>';
			return $html;
		}

	}

	private function createEmailForRefuseReview($title) {
		$html = '<strong>Dear Admin,</strong><br><br>';
		$html .= 'Reviewer <strong>' . $this->_user['firstname'] . ' ' . $this->_user['lastname'] . '</strong> refused to review paper <strong>' . $title . '</strong>.<br>';
		$html .= 'Please <a href="http://mmms2018.hust.edu.vn/user/login">login with your account</a> to assign a new reviewer.<br>';
		$html .= 'We appreciate to receive your suggestion on reviewer before 20/12/2017.<br><br>';
		$html .= '<strong>MMMS2018 Online System</strong><br><br><br>';
		$html .= '<span style="color:#a4a4a4;">[mmms2018]</span><br>';
		$html .= '<span style="color:#a4a4a4;">website: <a href="http://mmms2018.hust.edu.vn">http://mmms2018.hust.edu.vn</a>  |  email: <a href="mailto:mmms@hust.edu.vn">mmms@hust.edu.vn</a>  |  hotline: <a href="#">+84 982837465</a> </span><br>';
		$html .= '<span style="color:#a4a4a4;">FB: <a href="facebook.com/mmms2018/">facebook.com/mmms2018/</a> </span><br><br>';
		$html .= '<span style="color:#a4a4a4;">Science Secretary: Assoc. Prof. Nguyen Duc Toan | <a href="mailto:toan.nguyenduc@hust.edu.vn">toan.nguyenduc@hust.edu.vn</a><br>';
		$html .= '<span style="color:#a4a4a4;">Local Organizer: Assoc. Prof. Nguyen Thi Hong Minh | <a href="mailto:minh.nguyenthihong@hust.edu.vn">minh.nguyenthihong@hust.edu.vn</a><br>';
		return $html;
	}

	private function createEmailForAssignReviewer($title) {
		$html = '<strong>Dear Reviewer,</strong><br><br>';
		$html .= 'The Scientific Committee of MMMS2018 kindly requests your opinion as a reviewer for the following paper:<br>';
		$html .= 'Paper name: <strong>' . $title . '</strong>. <br>';
		$html .= 'Please <a href="http://mmms2018.hust.edu.vn/user/login">login with your account</a> to view the paper and give your comment.<br>';
		$html .= 'We appreciate to receive your comment and suggestion about the paper before 28/12/2017.<br><br>';
		$html .= '<strong>MMMS2018 Online System</strong><br><br><br>';
		$html .= '<span style="color:#a4a4a4;">[mmms2018]</span><br>';
		$html .= '<span style="color:#a4a4a4;">website: <a href="http://mmms2018.hust.edu.vn">http://mmms2018.hust.edu.vn</a>  |  email: <a href="mailto:mmms@hust.edu.vn">mmms@hust.edu.vn</a>  |  hotline: <a href="#">+84 982837465</a> </span><br>';
		$html .= '<span style="color:#a4a4a4;">FB: <a href="facebook.com/mmms2018/">facebook.com/mmms2018/</a> </span><br><br>';
		$html .= '<span style="color:#a4a4a4;">Science Secretary: Assoc. Prof. Nguyen Duc Toan | <a href="mailto:toan.nguyenduc@hust.edu.vn">toan.nguyenduc@hust.edu.vn</a><br>';
		$html .= '<span style="color:#a4a4a4;">Local Organizer: Assoc. Prof. Nguyen Thi Hong Minh | <a href="mailto:minh.nguyenthihong@hust.edu.vn">minh.nguyenthihong@hust.edu.vn</a><br>';
		return $html;
	}

}

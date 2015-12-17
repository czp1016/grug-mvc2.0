<?php

class TestController extends Controller {
	private $test_model;
	public function init() {
		$this->test_model = new Movie_TestModel();
	}
	public function index() {
		$test = $this->test_model->getData();
		//var_dump(phpinfo());
		$test = 'nihao';
		$haha = 'hahahaha';
		var_dump($_SERVER['REQUEST_URI']);
		$this->assign(array(
			'test' => $test,
			'haha' => $haha
			));
		$this->display();
	}

	public function thanks() {
		//$name = $model->getData();
		$name = $this->test_model->getInfo();
		//$params = $this->getParam("param");
		$params = $this->getParam();
		$param = $params['p1'];
		$list = $this->test_model->getData();
		$this->assign(array(
			'name' => $name,
			'weibo' => 'http://weibo.com/chenzeping1016',
			'blog' => 'http://grugblog.sinaapp.com/',
			'param' => $param,
			'list' => $list
			));
		$this->display();
	}

	public function add() {
		$params = $this->getParam();
		if (($params['name']) == '') {
			$info = "添加失败，姓名不能为空！";
		} else {
			$name = $params['name'];
			$data = array(
				'name' => $name,
				'mobile' => $params['mobile'],
				'regtime' => date("Y-m-d H:i:s")
			);
			if ($this->test_model->addUser($data)) {
				$info = "添加成功！";
			} else {
				$info = "添加失败！";
			}
		}
		$this->assign(array(
			'info' => $info
		));
		$this->display();
	}

	public function ajaxDelete() {
		$uid = $this->getParam('uid');
		if ($this->test_model->deleteUser($uid)) {
			$ret = array('err' => 0);
		} else {
			$ret = array('err' => 1);
		}
		echo json_encode($ret);
		exit();
	}
}
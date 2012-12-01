<?php

class homeController extends Controller
{

	public function index ($object = '', $params = '') {
		$this->view->set('FW_NAME', 'SpitfirePHP');
		$this->view->set('controller', __CLASS__ . '&gt' . $object . '&gt' . $params);
	}

	public function detail($object, $params) {
		//DO nothing
	}

	public function save ($object, $params) {
		$this->view->set('FW_NAME', 'Spitfire - ' . memory_get_peak_usage()/1024);
		$this->view->set('name', $this->post->name->value());
		$this->view->set('age',  $this->post->age->toInt());
		$this->view->set('pass', $this->post->pass->toPassword());
		
		$t = new Email();
		$t->setFrom("cesar@magic3w.com");
		$t->setTo("flunsidelacal@msn.com");
		//$t->setTo("cesarbretschneider@gmail.com");
		$t->setSubject("Test");
		$t->setHTML("test.php");
		$t->bind('test', date('d/M/Y h:i:s'));
		$this->view->set('test', $t->send());
		
		$query = $this->model->content->get('page', 'index')
			->addRestriction(new _SF_Restriction('content', 'This is a sample article.  All of this text you can change in the script admin control panel.Hello world'));
		
		$pagination = new Pagination($query);
		
		$this->view->set('pagination', $pagination);
		$this->view->set('test', print_r($query, true));/**/
	}
	
}
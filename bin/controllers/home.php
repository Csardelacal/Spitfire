<?php

class homeController extends Controller
{

	public function index ($object = '', $params = '') {
		
		$test = ComponentManager::get('M3W', 'testing');
		
		$this->view->set('FW_NAME', 'SpitfirePHP');
		$this->view->set('helloworld', $test->helloWorld());
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
		
		$query = $this->model->test->get('unique', 'test' );
		//$query = $this->model->test->like('content', 'some%' );
		$query->setPage($this->get->page->toInt());
		$query->setResultsPerPage(1);
		
		$pagination = new Pagination($query);
		
		$this->view->set('pagination', $pagination);
		$this->view->set('test', print_r($query->fetch(), true));/**/
	}
	
}
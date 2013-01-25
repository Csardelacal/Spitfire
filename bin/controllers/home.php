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
		//$this->view->set('FW_NAME', 'Spitfire - ' . memory_get_peak_usage()/1024);
		$this->view->set('FW_NAME', 'Spitfire - ' . $_SERVER['DOCUMENT_ROOT']);
		$this->view->set('name', $this->post->name->value());
		$this->view->set('age',  $this->post->age->toInt());
		$this->view->set('pass', $this->post->pass->toPassword());
		
		$query = $this->model->test->get('unique', 'test' );
		//$query = $this->model->test->like('content', 'some%' );
		$query->setPage($this->get->page->toInt());
		$query->setResultsPerPage(1);
		
		$data = $query->fetch();
		//$data['content'] = 'áéë ' . date('d/m/Y H:i:s', time());
		//$this->model->test->set($data);
		
		$pagination = new Pagination($query);
		
		$this->view->set('pagination', $pagination);
		$this->view->set('test', print_r($data, true));/**/
	}
	
	public function test2 () {
//		print_r($this->model);
//		print_r($this->model->test);
//		print_r($this->model->test->get('unique', 'test'));
//		print_r($this->model->test->get('unique', 'test2')->fetch()->content);
//		print_r($this->model);
		
		$field = 'unique';
		$this->model->escapeFieldName($field);
		echo $field;
		
		$t = new databaseRecord($this->model->test);
		$t->content = 'Hello';
		$t->unique  = 'Hello World';
		$t->id      = 3;
		$t->store();
		
		$p = new databaseRecord($this->model->test);
		$p->content = 'Hello';
		$p->unique  = 'Hello World';
		$p->id      = $t->id;
		
		try {
			$p->store();
		}
		catch (Exception $e) {
			echo 'Error: repeated id';
		}
		
		$t = $this->model->test->get('id', $t->id)->fetch();
		$t->content.= 't';
		$t->store();
		$t->increment('id', -1);
		
		echo '-----';
		$p = $this->model->test->get('id', $t->id)
			->group()
				->addRestriction(new _SF_Restriction('unique', 'test'))
				->addRestriction(new _SF_Restriction('content', 'test2'))
			->endGroup()
			->group()
				->addRestriction(new _SF_Restriction('unique', 'hello'))
			->endGroup()
			->fetch();
		echo '----';
		
		$t = $this->model->test->get('id', $t->id)->fetch();
		echo $t->content;
		$t->delete();
	}
	
	public function dbTest() {
		/*$rec = new databaseRecord($this->model->dependant);
		$rec->test_id = 3;
		$rec->content ="_SF_";
		$rec->store();/**/
		$rec = $this->model->test->get('id', 3)->fetch();
		
		echo( new Pagination($this->model->test->get('id', 3)) );
		
		$r = Array(
		    new _SF_Restriction('id', 1)
		);
		
		print_r($rec->getChildren($this->model->dependant, $r));
	}
	
	public function beans() {
		
		$user = CoffeeBean::getBean('user')->insertIntoDBRecord(new databaseRecord($this->model->test));
		print_r($user);
		$user->store();
	}
}
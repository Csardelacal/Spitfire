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
		
		
		$t = new databaseRecord($this->model->test);
		//$t->content = 'Hello';
		//$t->title   = 'Hello World';
		$t->id1     = 3;
		$t->id2     = 3;
		//$t->store();
		
		$p = new databaseRecord($this->model->test);
		//$t->content = 'Hello';
		//$t->title   = 'Hello World';
		$t->id1     = 3;
		$t->id2     = 3;
		
		try {
			$p->store();
		}
		catch (Exception $e) {
			echo 'Error: repeated id';
		}
		
		$t = $this->model->test->get('id1', $t->id)->fetch();
		$t->content.= 't';
		$t->store();
		$t->increment('id1', -1);
		
		echo '-----';
		$p = $this->model->test->get('id1', $t->id)
			->group()
				->addRestriction('id1', '1')
				->addRestriction('id2', '2')
			->endGroup()
			->group()
				->addRestriction('id1', '3')
			->endGroup()
			->fetch();
		echo '----';
		
		$t = $this->model->test->get('id1', $t->id)->fetch();
		echo $t->content;
		$t->delete();
		
		print_r(\spitfire\SpitFire::$debug->getMessages());
	}
	
	public function dbTest() {
		
		try {

			$rec = db()->table('test')->get('id1', 1)->fetch();
			
			if (!$rec->id1) {
				$rec = $this->model->test->newRecord();
				$rec->id1 = 1;
				$rec->id2 = 1;
				$rec->content = "El1";
				$rec->store();
			}
			//else echo 'Record exists \n';

			$rec2 = db()->table('test2')->get('id1', 1)->fetch();
			
			if (!$rec2->id1) {
				$rec2 = $this->model->test2->newRecord();
				$rec2->id1 = 1;
				$rec2->id2 = 1;
				$rec2->content = "El1";
				$rec2->store();
			}
			//else echo 'Record exists \n';

			try {
				$child = $this->model->dependant->newRecord();
				$child->test = $rec;
				$child->test2 = $rec2;
				$child->title    = time();
				$child->content = '_SF_';
				$child->store();
			}
			catch(Exception $e) {
				print_r($this->model->dependant->getErrors());
			}

			try {
				$child = db()->table('dependant')->newRecord();
				$child->test = $rec;
				$child->test2 = $rec2;
				$child->title    = time() . 'i';
				$child->content  = '_SF_';
				$child->store();

				$child->content.= time();
				$child->store();
				echo $child->id;
				$child->increment('numeric');
			}
			catch (Exception $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			$q = $rec->getChildren($this->model->dependant)->fetchAll();
			
			foreach ($q as $e) echo $e->title . "\n";
			
			if ($rec->getChildren($this->model->dependant)->count() > 10) $rec->delete();
			
			print_r(spitfire()->getMessages());
		}
		catch(Exception $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
			echo 'Exception: end...';
		}
	}
	
	public function register() {
		
		$user = CoffeeBean::getBean('user');//->insertIntoDBRecord(new databaseRecord($this->model->test));
		print_r($user);
		echo $user->makeForm(new URL('home', 'createUser'));
		//$user->store();
	}
	
	public function createUser() {
		$user = CoffeeBean::getBean('user');//->insertIntoDBRecord(new databaseRecord($this->model->test));
		print_r($user);
		print_r($user->makeDBRecord());
		$user->makeDBRecord()->store();
		
		print_r(\spitfire\SpitFire::$debug->getMessages());
	}
	
	public function writetodb() {
		$t = new databaseRecord($this->model->dependant);
		$t->test_id1 = 1;
		$t->test_id2 = 2;
		
		$t->test = 1;
		$t->content = "Some text " . time();
		$t->content2 = "Some other text " . time();
		$t->store();
		
		print_r(\spitfire\SpitFire::$debug->getMessages());
	}
	
	public function fields($table) {
		
		$this->model = db(Array(
		    'schema' => 'test'
		));
		
		$model = "{$table}Model";
		print_r(new $model ());
		echo("Model ------------------- \n");
		print_r($this->model);
		echo("Table ------------------- \n");
		echo "{$this->model->$table->getTableName()} \n";
		echo("Field ------------------- \n");
		$fields = $this->model->{$table}->getFields();
		foreach($fields as $field) echo "{$field->getName()}\n";
		echo("Row   ------------------- \n");
		$row = $this->model->{$table}->get('id', 1)
			->group()
				->addRestriction('id', 2)
				->addRestriction('id', 3)
			->endGroup()
			->count();
		echo $row;
		echo("Field ------------------- \n");
		$fields = $this->model->{$table}->getFields();
		foreach($fields as $field) echo "{$field->getName()}\n";
		
		
		print_r(\spitfire\SpitFire::$debug->getMessages());
	}
	
	public function error() {
		throw new privateException('User caused error');
	}
}
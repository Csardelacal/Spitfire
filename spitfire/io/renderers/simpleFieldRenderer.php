<?php namespace spitfire\io\renderers;

class SimpleFieldRenderer {
	
	public function renderForm(RenderableField$field) {
		
		$array = $field instanceof RenderableFieldArray;
		$type  = null;
		
		if ($field instanceof RenderableFieldDateTime) { $type = 'DateTime'; }
		if ($field instanceof RenderableFieldDouble)   { $type = 'Double'; }
		if ($field instanceof RenderableFieldFile)     { $type = 'File'; }
		if ($field instanceof RenderableFieldGroup)    { $type = 'Group'; }
		if ($field instanceof RenderableFieldInteger)  { $type = 'Integer'; }
		if ($field instanceof RenderableFieldRTF)      { $type = 'RTF'; }
		if ($field instanceof RenderableFieldString)   { $type = 'String'; }
		if ($field instanceof RenderableFieldText)     { $type = 'Text'; }
		
		$method = 'renderForm' . $type . ($array?'Array':'');
		
		if (!method_exists($this, $method) || !$type) {
			throw new \privateException('Renderer does not support this kind of field');
		}
		
		return $this->method($field);
	}
	
	public function renderList($field) {
		return __(strip_tags(strval($field)), 100);
	}
	
	public function renderBasicField($field) {
		if ($field instanceof TextField) {
			$input = new HTMLInput('text', $field->getPostId(), $field->getValue());
			$label = new HTMLLabel($input, $field->getCaption());
			return new HTMLDiv($label, $input, Array('class' => 'field'));
		}
		elseif ($field instanceof LongTextField) {
			$input = new HTMLTextArea('text', $field->getPostId(), $field->getValue());
			$label = new HTMLLabel($input, $field->getCaption());
			return new HTMLDiv($label, $input, Array('class' => 'field'));
		}
		elseif ($field instanceof FileField) {
			$input = new HTMLInput('file', $field->getPostId(), $field->getValue());
			$label = new HTMLLabel($input, $field->getCaption());
			$file  = '<small>' . $field->getValue() . '</small>';
			return new HTMLDiv($label, $input, $file, Array('class' => 'field'));
		}
		elseif ($field instanceof DateTimeField) {
			$input = new \spitfire\io\html\dateTimePicker($field->getValue());
			$input->setInputName($field->getPostId());
			$label = new HTMLLabel($input, $field->getCaption());
			return new HTMLDiv($label, $input, Array('class' => 'field'));
		}
		elseif ($field instanceof BooleanField) {
			$input = new HTMLInput('checkbox', $field->getPostId(), 'true');
			if ($field->getValue()) $input->setParameter('checked', 'checked');
			$label = new HTMLLabel($input, $field->getCaption());
			return new HTMLDiv($label, $input, Array('class' => 'field'));
		}
		//TODO: Add more options
		else return $field;
	}
	
	public function renderEnumField(EnumField$field) {
		$value   = $field->getValue();
		$options = $field->getField()->getOptions();
		
		$select  = new HTMLSelect($field->getPostId(), $value);
		$label   = new HTMLLabel($select, $field->getCaption());
		
		$select->addChild(new HTMLOption(null, 'Pick'));
		
		foreach ($options as $possibility) {
			$select->addChild(new HTMLOption($possibility, strval($possibility)));
		}
		
		return new HTMLDiv($label, $select, Array('class' => 'field'));
	}
	
	public function renderReferencedField($field) {
		$record = $field->getValue();
		$selected = ($record)? implode(':',$record->getPrimaryData()) : '';
		$select = new HTMLSelect($field->getPostId(), $selected);
		$label = new HTMLLabel($select, $field->getCaption());
		
		$reference = $field->getField()->getTarget();
		$query = db()->table($reference)->getAll();
		$query->setPage(-1);
		$possibilities = $query->fetchAll();
		
		$select->addChild(new HTMLOption(null, 'Pick'));
		
		foreach ($possibilities as $possibility) {
			$select->addChild(new HTMLOption(implode(':', $possibility->getPrimaryData()), strval($possibility)));
		}
		
		return new HTMLDiv($label, $select, Array('class' => 'field'));
	}
	
	public function renderMultiReferencedField($field) {
		$records = $field->getValue();
		
		$reference = $field->getField()->getTarget();
		$query = db()->table($reference)->getAll();
		$query->setPage(-1);
		$possibilities = $query->fetchAll();
		
		$_return = Array();
		
		//@todo Replace this when better ways are found.
		if ($records instanceof \spitfire\model\adapters\ManyToManyAdapter) $records = $records->toArray();
		
		foreach ($records as $record) {
			$selected = ($record)? implode(':',$record->getPrimaryData()) : '';
			$select = new HTMLSelect($field->getPostId() . '[]', $selected);
			$label = new HTMLLabel($select, $field->getCaption());

			$select->addChild(new HTMLOption(null, 'Pick'));

			foreach ($possibilities as $possibility) {
				$select->addChild(new HTMLOption(implode(':', $possibility->getPrimaryData()), strval($possibility)));
			}
			
			$_return[] = new HTMLDiv($label, $select, Array('class' => 'field'));
		}
		
		#Empty additional one
		//todo: Stop cpying code
		$selected = '';
		$select = new HTMLSelect($field->getPostId() . '[]', $selected);
		$label  = new HTMLLabel($select, $field->getCaption());

		$select->addChild(new HTMLOption(null, 'Pick'));

		foreach ($possibilities as $possibility) {
			$select->addChild(new HTMLOption(implode(':', $possibility->getPrimaryData()), strval($possibility)));
		}

		$_return[] = new HTMLDiv($label, $select, Array('class' => 'field'));
		
		return implode('', $_return);
		
	}
	
	public function renderChildBean($field) {
		$childmodel = $field->getField()->getTarget();
		$childbean  = $childmodel->getTable()->getBean(true);
		$childbean->setParent($field);
		
		$fields = $childbean->getFields();
		
		if ($field->getBean()->getRecord()) {
			$children  = $field->getBean()->getRecord()->{$field->getName()};
		}
		
		$ret = new HTMLDiv();
		
		if (!empty($children)) {
			foreach ($children as $record) {
				$childbean->setDBRecord($record);
				$ret->addChild($subform = new HTMLDiv());
				$subform->addChild('<h1>' . $record->getTable()->getModel()->getName() . ' - ' . $record . '</h1>');
				foreach ($fields as $f) 
						$subform->addChild ($this->renderForm($f));
			}
		}
		
		$count = (empty($children))? 0 : count($children);
		do {
			$childbean->setDBRecord(null);
			$ret->addChild('<h1>' . $childbean->getTable()->getModel()->getName() . ' - ' . 'New record</h1>');
			foreach ($fields as $f) 
						$ret->addChild ($this->renderForm($f));
			$count++;
		} while ($count < $field->getMinimumEntries());
		
		return $ret;
	}
}
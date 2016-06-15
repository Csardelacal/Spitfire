<?php namespace spitfire\model\adapters;

use EnumField;

/**
 * This adapter is in charge of ensuring that only values that are inside the 
 * range defined by the field are stored. If the user tries to set data that is
 * not valid the adapter will throw an exception.
 */
class EnumAdapter extends BaseAdapter
{
	/**
	 * Verifies the data this method receives is inside the defined values by the
	 * field.
	 * 
	 * @param string $data
	 * @throws \spitfire\exceptions\PrivateException
	 */
	public function usrSetData($data) {
		$field   = $this->getField();
		
		if ( !$field instanceof EnumField) {
			throw new \spitfire\exceptions\PrivateException('An enum field is required for this adapter');
		}
		
		$options = $field->getOptions();
		
		if ( !in_array($data, $options) ) {
			$error = $field . ' only accepts the values ' . implode(', ', $options) ;
			throw new \spitfire\exceptions\PrivateException($error);
		}
		
		parent::usrSetData($data);
	}
}


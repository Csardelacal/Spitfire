<?php namespace spitfire\model\adapters;

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
	 * @throws \privateException
	 */
	public function usrSetData($data) {
		$field   = $this->getField();
		$options = $field->getOptions();
		
		if ( !in_array($data, $options) ) {
			$error = $field . ' only accepts the values ' . implode(', ', $options) ;
			throw new \privateException($error);
		}
		
		parent::usrSetData($data);
	}
}


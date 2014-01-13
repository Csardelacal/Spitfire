<?php namespace spitfire\model\adapters;

/**
 * This adapter allows to verify that data that is inside the adapter is boolean
 * only. You can set whatever data you want but the adapter will just keep it's 
 * boolean conversion.
 */
class BooleanAdapter extends BaseAdapter
{
	
	/**
	 * This adapter should only receive boolean data. It doesn't actually check 
	 * if this is the case so you can define whatever data you want. The adapter
	 * will convert it to boolean though, so the data you enter may be lost if 
	 * setting incorrect data.
	 * 
	 * @param boolean $data
	 */
	public function usrSetData($data) {
		parent::usrSetData(!!$data);
	}
}
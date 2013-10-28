<?php

/**
 * Description of BaseDataModel
 *
 * @author hfdend
 */
class BaseDataModel extends ModelCoreLib{
	
	const IS_OK = 1;

	const IS_NO = 0;
    
    const SEX_BOY = 1;
    
    const SEX_GIRL = 2;

	public $Id;
	
	public function __construct() {
		parent::__construct();
		$this->setPrimaryKey('Id');
	}
}


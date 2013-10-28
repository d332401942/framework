<?php

/**
 * Description of GategoryDataModel
 *
 * @author hfdend
 */
class AreaDataModel extends BaseDataModel {

    public $Name;
    public $Path;
    public $Pid;
    public $Level;
    public $Children = array();

    public function __construct() {
        parent::__construct();
        $this->setIgoneFields('Children');
    }
}

?>

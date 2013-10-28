<?php


class IndexIndexView extends ViewCoreLib
{
	//protected $isAjax = true;
	
	public function index()
    {
        $sphinx = new SphinxDbLib();
        //$sphinx->UpdateAttributes ( "test1", array("group_id"), array(1=>array(456)) );
        //$sphinx->UpdateAttributes('area', array('level'), array(86101010 => array('邓鸿风')));
        
        //$result = $sphinx->query('@Name (\'北京\')');
    }
}

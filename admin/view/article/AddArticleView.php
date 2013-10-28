<?php

/**
 * Description of IndexArticleView
 *
 * @author hfdend
 */
class AddArticleView extends BaseView{
	
	public function index() {
		
		$business = new CategoryBusiness();
		$cateGroyModels = $business->getAll();
		$this->assign('cateGroyModels', $cateGroyModels);
		$edit = FormCommonPublic::editor('content', FormCommonPublic::EDIT_THEME_DESC);
		$date = FormCommonPublic::date('d12', 'dd');
		$this->assign('edit', $edit);
		$this->assign('date', $date);
	}
}

?>

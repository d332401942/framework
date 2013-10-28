<?php

/**
 * Description of HeaderControllerView
 *
 * @author hfdend
 */
class HeaderControllerView extends ViewCoreLib {

	public function index($parameters) {
		$mark = !empty($parameters['mark']) ? $parameters['mark'] : '';
		$mark = explode('.', $mark);
		$menu = array(
			'article' => array(
				'name' => '文章管理',
				'url' => 'article',
				'children' => array(
					'add' => array(
						'name' => '发布',
						'url' => 'article/add',
					),
					'list' => array(
						'name' => '文章列表',
						'url' => 'article/list',
					),
				),
			),
			'article2' => array(
				'name' => '我的成长2',
				'url' => 'article2',
				'children' => array(
					'addcategory2' => array(
						'key' => '',
						'name' => '添加分类2',
						'url' => 'article/addcategory2',
					),
					'list2' => array(
						'name' => '文章列表2',
						'url' => 'article/list2',
					),
				),
			),
		);
		$mark1 = !empty($mark[0]) ? $mark[0] : '';
		$mark2 = !empty($mark[1]) ? $mark[1] : '';
		$this->assign('mark1', $mark1);
		$this->assign('mark2', $mark2);
		$this->assign('menu', $menu);
	}

}

?>

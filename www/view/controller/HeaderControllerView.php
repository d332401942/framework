<?php

/**
 * Description of HeaderControllerView
 *
 * @author hfdend
 */
class HeaderControllerView extends BaseControllerView {

    public function index($parameters) {
        $css = empty($parameters['css']) ? '' : $parameters['css'];
        $js = empty($parameters['js']) ? '' : $parameters['js'];
        $css = explode(',', $css);
        $js = explode(',', $js);
        
        $this->assign('css', $css);
        $this->assign('js', $js);
    }

}

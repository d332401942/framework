<?php

class FormCommonPublic {

    const EDIT_THEME_BASIC = 1;
    const EDIT_THEME_FULL = 2;
    const EDIT_THEME_DESC = 3;

    private static $isLoad = false;
    private static $isLoadDate = false;

    /**
     * 编辑器
     * @param int $textareaid
     * @param int $toolbar    有basic full 和desc三种
     * @param int $color 编辑器颜色
     * @param string $alowuploadexts 允许上传类型
     * @param string $height 编辑器高度
     * @param string $disabled_page 是否禁用分页和子标题
     */
    public static function editor($textareaid = 'content', $toolbar = self::EDIT_THEME_BASIC, $up = true) {
        $str = '';
        if (!self::$isLoad) {
            $str .= '<script charset="utf-8" src="' . APP_ROOT . '/resource/kindeditor/kindeditor-min.js"></script>';
            $str .= '<script charset="utf-8" src="' . APP_ROOT . '/resource/kindeditor/lang/zh_CN.js"></script>';
            self::$isLoad = true;
        }
        $items = array();
        switch ($toolbar) {
            case self::EDIT_THEME_BASIC:
                break;
            case self::EDIT_THEME_DESC:
                $items = array(
                    'fullscreen',
                    'justifyleft',
                    'justifycenter',
                    'justifyright',
                    'justifyfull',
                    'insertorderedlist',
                    'clearhtml',
                    'removeformat',
                    'link',
                    'unlink',
                    'image',
                    'baidumap',
                );
                if (!$up) {
                    unset($items[10]);
                }
                break;
            case self::EDIT_THEME_FULL:
                $items = false;
                break;
        }
        $str .= '<script>';
        $str .= 'var editor;';
        $str .= 'KindEditor.ready(function(K) {';
        $str .= 'editor = K.create(\'#' . $textareaid . '\', {';
        $str .= 'themeType : \'simple\'';
        //$str .= '});';
        //$str .= 'K.create("' . $textareaid . '",{';
        //$str .= ',width:"700px;"';
        if (is_array($items)) {
            $str .= ',items:["' . implode('","|","', $items) . '"]';
        }
        $str .= '});';
        $str .= '});';

        $str .= '</script>';
        return $str;
    }

    public static function date($id, $iptId = '', $dateFmt = 'yyyy-MM-dd HH:mm:ss', $options = array(), $type = 'click') {
        if (empty($options['dateFmt'])) {
            $options['dateFmt'] = $dateFmt;
        }
        if ($iptId) {
            $options['el'] = $iptId;
        }
        $opt = json_encode($options);
        $str = '';
        if (!self::$isLoadDate) {
            $str .= '<script charset="utf-8" src="' . APP_ROOT . '/resource/My97DatePicker/WdatePicker.js"></script>';
            self::$isLoadDate = true;
        }
        $str .= <<<TOC
<script>
$(function(){
	var obj = $('#{$id}');
	obj.bind('{$type}', function(){
		WdatePicker({$opt})
	});		
});	
</script>		
TOC;
        return $str;
    }

}

<?php

/**
 * Description of DemoIndexView
 *
 * @author hfdend
 */
class DemoIndexView extends ViewCoreLib {

    public function index() {
        $demo = new AreaDbLib();
        $id = '111.122.156.23';
        $area = $demo->ip2Area($id);
        P($demo->getUA($id));
        echo $area;exit;
    }
    
    
    public function index2() {
        $str = include 'area.php';
        //echo $str;exit;
        $result = array();
        $tmpArr = explode('@', $str);
        foreach ($tmpArr as $val) {
            $dataArr = explode(',', $val);
            if (isset($dataArr[2]) && $dataArr[2] == 'a') {
                //continue;
            }
            else {
                continue;
            }
            $name = $dataArr[0];
            $ids = $dataArr[1];
            $idsArr = explode('_', $ids);
            $level = count($idsArr);
            $model = new AreaDataModel();
            
            $model->Id = end($idsArr);
            $model->Name = $name;
            $model->Path = str_replace('_', ',', $ids);
            $model->Pid = 0;
            $model->Level = $level;
            if ($level > 1) {
                $model->Pid = $idsArr[$level - 2];
            }
            $result[$model->Id] = $model;
        }
        P(count($result));exit;
        $sql = 'insert into `pet`.`Area` (`Id`, `Name`, `Path`, `Pid`, `Level`) values (1,1,1,1,1)';
        foreach ($result as $model) {
            $sql .= ',('.$model->Id.',"'.$model->Name.'","'.$model->Path.'","'.$model->Pid.'", "'.$model->Level.'")';
        }
        echo $sql;exit;
        $data = IndustryData::getInstance();
        //P(count($result));
        $data = $data->add($result);

        exit;
    }

}


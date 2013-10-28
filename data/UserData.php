<?php

class UserData extends BaseData {

    /**
     * 
     * @return UserData
     */
    public function getInstance() {
        return parent::getInstance();
    }

    /**
     * 修改用户信息
     * @param UserDataModel
     */
    public function updateUserInfo($userModel) {
        $fields = array(
            'NickName', 'Birthday', 'Industries', 'Sex', 'Signature', 'Sites', 'UpdateTime'
        );
        $userModel->setWorkFields($fields);
        $this->updateModel($userModel);
        $userModel->clearWorkFields();
    }

    /**
     * @param type $id
     * @return UserDataModel
     */
    public function getOneById($id) {
        return parent::getOneById($id);
    }

    public function getByEmail($email) {
        $result = $this->where(array('Email' => strtolower($email)))->findOne();
        return $result;
    }

    public function changePassword($password, $userId) {
        $sql = 'update User set Password = "' . $password . '" where Id = ' . (int) $userId;
        $this->exec($sql);
    }

    /**
     * 添加一个用户
     * @param type $userModel
     */
    public function addUser($userModel) {
        $this->add($userModel);
    }

}
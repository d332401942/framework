<?php

class UserBusiness extends BaseBusiness {
    
    /**
     * @return UserBusiness
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    
    public function getByEmail($email) {
        if (empty($email)) {
            $this->throwException('Email不能为空', CodeException::USER_EMAIL_ERROR);
        }
        $userData = UserData::getInstance();
        $userModel = $userData->getByEmail($email);
        return $userModel;
    }
    
    /**
     * 修改用户信息
     * @param UserDataModel
     */
    public function updateUserInfo($userModel) {
        $userData = UserData::getInstance();
        $userData->updateUserInfo($userModel);
    }
    
    /**
     * 注册用户
     * @param UserDataModel $userModel
     */
    public function addUser($userModel) {
        $userData = UserData::getInstance();
        if (!RuleUtil::checkEmailFmt($userModel->Email)) {
            $this->throwException('Email格式不正确', CodeException::USER_EMAIL_ERROR);
        }
        if (empty($userModel->Password)) {
            $this->throwException('密码不能为空', CodeException::USER_PASSWORD_EMPTY);
        }
        $userModel->Password = $this->encryption($userModel->Password);
        $userModel->CreateTime = time();
        $userModel->UpdateTime = time();
        $userModel->Status = UserDataModel::IS_OK;
        $userModel->Sex = UserDataModel::SEX_BOY;
        $userData->addUser($userModel);
    }
    
    public function checkPassword($password, $userId) {
        $userData = UserData::getInstance();
        $tureUser = $userData->getOneById($userId);
        if ($tureUser->Password != $this->encryption($password)) {
            $this->throwException('密码错误', CodeException::USER_PASSWORD_EMPTY);
        }
        $userData->changePassword($this->encryption($password), $userId);
    }
    
    public function encryption($password) {
        return md5(md5($password));
    }
}
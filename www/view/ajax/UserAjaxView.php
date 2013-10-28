<?php
class UserAjaxView extends BaseAjaxView{
    
    
    /**
     * 注册用户
     */
    public function register() {
        $userModel = new UserDataModel();
        $userModel->Email = $this->postParam('email');
        $userModel->Password = $this->postParam('password');
        $business = UserBusiness::getInstance();
        $business->addUser($model);
        UserUtil::setUserCookie($userModel);
        $this->response(true);
    }
    
    /**
     * 登陆
     */
    public function login() {
        $email = $this->postParam('email');
        $email = strtolower($email);
        $password = $this->postParam('password');
        if (!$password) {
            $this->responseError('请填写密码');
        }
        $business = UserBusiness::getInstance();
        $userModel = $business->getByEmail($email);
        if (!$userModel) {
            $this->responseError('Email不存在');
        }
        UserUtil::Encryption($password);
        if ($business->encryption($password) != $userModel->Password) {
            $this->responseError('密码错误');
        }
        //设置cookie
        UserUtil::setUserCookie($userModel);
        $fromUrl = $this->postParam('fromurl');
        if (!$fromUrl) {
            $fromUrl = '/';
        }
        $this->response($fromUrl);
    }
    
    /**
     * 退出登陆
     */
    public function loginOut() {
        UserUtil::delUserCookie();
        $this->response(true);
    }
    
    /**
     * 修改资料
     */
    public function editUserInfo() {
        $userModel = $this->currentUserModel;
        $userModel->NickName = $this->postParam('nickname', true); 
        $userModel->Birthday = $this->postInt('birthday');
        $userModel->Industries = $this->postParam('industries', true);
        $userModel->Sex = $this->postInt('sex');
        $userModel->Signature = $this->postParam('signature', true);
        $userModel->Sites = $this->postParam('sites', true);
        $userModel->UpdateTime = time();
        $business = UserBusiness::getInstance();
        $business->updateUserInfo($userModel);
        $this->response(true);
    }
    
    /**
     * 修改密码
     */
    public function changePassword() {
        $oldPassword = $this->postParam('oldpassword');
        $business = UserBusiness::getInstance();
        $business->checkPassword($oldPassword, $this->currentUserModel->Id);
        $this->response(true);
    }
}


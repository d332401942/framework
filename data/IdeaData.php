<?php

class IdeaData extends BaseData {

    /**
     * @return IdeaData
     */
    public static function getInstance() {
        return parent::getInstance();
    }

    /**
     * @param type $id
     * @return IdeaDataModel
     */
    public function getOneById($id) {
        return parent::getOneById($id);
    }

    /**
     * 添加一个Idea
     * @param IdeaDataModel $ideaModel
     */
    public function addIdea($ideaModel) {
        $this->add($ideaModel);
    }

    /**
     * 删除一个Idea
     * @param int $ideaId
     * @param int $userId
     */
    public function delIdea($ideaId, $userId) {
        $this->where(array('Id' => (int) $ideaId, 'UserId' => (int) $userId))->delete();
    }

    /**
     * 喜欢一个Idea
     * @param int $ideaId
     * @param int $userId
     */
    public function likeIdea($ideaId, $userId) {
        
    }

    /**
     * 修改Idea
     * @param IdeaDataModel $ideaModel
     */
    public function updateIdea($ideaModel) {
        $this->updateModel($ideaModel);
    }

    /**
     * 修改Idea 状态
     * @param int $ideaId
     * @param int $userId
     * @param int $status
     */
    public function updateIdeaStatus($ideaId, $userId, $status) {
        $where = array('Id' => (int) $ideaId, 'UserId' => (int) $userId);
        $data = array();
        $data['Status'] = (int) $status;
        $data['UpdateTime'] = time();
        $this->where($where)->updateData($data);
    }

}

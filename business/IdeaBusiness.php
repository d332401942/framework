<?php 

class IdeaBusiness extends BaseBusiness {
    
    /**
     * 添加一个Idea
     * @param IdeaDataModel $ideaModel
     */
    public function addIdea($ideaModel) {
        $ideaData = IdeaData::getInstance();
        $ideaData->addIdea($ideaModel);
    }
    
    /**
     * 删除一个Idea
     * @param int $ideaId
     * @param int $userId
     */
    public function delIdea($ideaId, $userId) {
        $data = IdeaData::getInstance();
        $data->delIdea($ideaId, $userId);
    }
    
    /**
     * 喜欢一个Idea
     * @param int $ideaId
     * @param int $userId
     */
    public function likeIdea($ideaId, $userId) {
        $data = IdeaData::getInstance();
        $data->likeIdea($ideaId, $userId);
    }
}
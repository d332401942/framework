<?php

class IdeaDataModel extends BaseDataModel {
    
    /**
     * 用户Id
     * @var type 
     */
    public $UserId;
    
    /**
     * 发展城市
     * @var type 
     */
    public $Sites;
    
    /**
     * 所在行业
     * @var type 
     */
    public $Industries;
    
    /**
     * 标题
     * @var type 
     */
    public $Title;
    
    /**
     * 资金
     * @var type 
     */
    public $Funds;
    
    /**
     * 资金是否在页面显示
     * @var type 
     */
    public $FundsIsDisplay;
    
    /**
     * 盈利模式
     * @var int
     */
    public $ProfitModel;
    
    /**
     * 盈利模式是否在页面显示
     * @var type 
     */
    public $ProfitModelIsDisplay;
    
    /**
     * 已有的优势
     * @var type 
     */
    public $HasSuperiority;
    
    /**
     * 希望合作人具备
     * @var type 
     */
    public $HopeSuperiority;
    
    /**
     * 预计成员个数
     * @var type 
     */
    public $MemberNum;
    
    /**
     * 详细介绍
     * @var type 
     */
    public $Description;
    
    /**
     * 创建时间
     * @var type 
     */
    public $CreateTime;
    
    /**
     * 修改时间
     * @var type 
     */
    public $UpdateTime;
    
    /**
     * 状态
     * @var type 
     */
    public $Status;
    
    
    public function __construct() {
        parent::__construct();
    }
}


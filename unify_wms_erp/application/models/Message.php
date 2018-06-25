<?php

class MessageModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Constructor
     *
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    //获取近一个月的未读消息
    public function getMessageList($params) 
    {
        
        $where = '';

        if (isset($params['viewstatus']) && $params['viewstatus'] != '') {
            $where .= 'viewstatus='.$params['viewstatus'].' AND ';
        }
        if($params['type']==1){
            $where .= " isdel = 0 and status = 1 and send_to_id = ".intval($params['cusomer_sysno'])." and " . date('Y-m-d', strtotime('-30 days')) ." <= `created_at` <= " .date('Y-m-d', time()) ." order by viewstatus ,created_at DESC";
        }else{
            $where .= "isdel = 0 and status = 1 and send_to_id = ".intval($params['cusomer_sysno'])." and " . date('Y-m-d', strtotime('-30 days')) ." <= `created_at` <= " .date('Y-m-d', time()) ." order by viewstatus ,created_at DESC";
        }

        
        //未读消息条数
        $sql = "SELECT count(*) from ".DB_PREFIX."doc_message where viewstatus = 1 and {$where}";
        $result['count'] = $this->dbh->select_one($sql);

        if($result['count']){
            $result['count'] = $result['count'] >= 100 ? '99+' : $result['count'];
        }
        $sql = "SELECT * ,if(viewstatus = 1,'未读','已读') as type from ".DB_PREFIX."doc_message where  {$where}";

        $result['list'] = $this->dbh->select($sql);

        return $result;
    }

    //更新消息
    public function updateMessage($id,$params)
    {
        return $this->dbh->update(DB_PREFIX.'doc_message',$params,'sysno = '.intval($id));
    }

    //获取消息
    public function getMessageById($id)
    {
        $sql = "SELECT * from ".DB_PREFIX."doc_message where sysno = " . intval($id);
        return $this->dbh->select_row($sql);
    }
    

    
}
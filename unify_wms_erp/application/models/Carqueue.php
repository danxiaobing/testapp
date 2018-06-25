<?php

/**
 * User: HR
 * Date: 2017/07/26 
 * Time: 11:34
 */
Class CarqueueModel
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


    public function getCarqueueList($params)
    {
    	$filter = array();
    	if(isset($params['doc_type']) && $params['doc_type']!='')
    	{
    		$filter[] = " doc_type = {$params['doc_type']} "; 
    	}
    	if(isset($params['tp_sysno']) && $params['tp_sysno']!='')
    	{
    		$filter[] = " tp_sysno = {$params['tp_sysno']} "; 
    	}

    	$where = ' WHERE isdel = 0 AND status=1 AND carstatus = 1 AND queuestatus = 1';

        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = ' ORDER BY isup DESC,orderno ASC,updated_at DESC';
        $sql = "SELECT count(sysno) FROM `".DB_PREFIX."doc_car_queue` {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT * FROM `".DB_PREFIX."doc_car_queue` {$where} {$order}";

        if(boolval($params['page'])=='false'){

            $data = $this->dbh->select($sql);
            $result['list'] = $data;

        }else{

            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            
            $data = $this->dbh->select_page($sql);

            $result['list']=$data;
        }

        $queuetype = $params['doc_type']==1 ? 2 : 1; 
        for ($i=0; $i <count($data) ; $i++) { 
            $sql = "SELECT queuetime FROM ".DB_PREFIX."base_queue WHERE queuetype={$queuetype} AND sysno={$data[$i]['tp_sysno']}";
            $queuetime = $this->dbh->select_one($sql);

            $data[$i]['queuetime'] = ($i+1)*$queuetime.'分钟';
            $data[$i]['queuestatus'] = $i==0 ? '通知作业' : '排队中';
        }
        $result['list'] = $data;
        return $result;

    }


    public function updateCarqueue($data, $action, $key)
    {
        if($action=='top')
        {
            $params = array(
                'isup'       => 1,
                'updated_at' => '=NOW()',
                );
            $this->dbh->update(DB_PREFIX.'doc_car_queue', array('isup'=>0), 'isdel=0');
            return $this->update($params, $data['sysno']); 
        }elseif ($action=='up') {
            $upparams = array(
                'orderno'    => $data['uporderno'],
                'updated_at' => '=NOW()',
                );

            $upres =  $this->update($upparams, $data['sysno']);

            $params = array(
                'orderno'    => $data['orderno'],
                'updated_at' => '=NOW()',
                );
            if($key==0){
                $params['isup'] = 0;
            }
            $res =  $this->update($params, $data['upsysno']);

            return ($upres && $res);
        }elseif ($action=='down') {
            $downparams = array(
                'orderno'    => $data['downorderno'],
                'isup'       => 0,
                'updated_at' => '=NOW()',
                );
            $downres =  $this->update($downparams, $data['sysno']);

            $params = array(
                'orderno'    => $data['orderno'],
                'updated_at' => '=NOW()',
                );
            $res =  $this->update($params, $data['downsysno']);

            return ($downres && $res);
        }
        
    }


    public function update($data,$id)
    {
        return  $this->dbh->update(DB_PREFIX.'doc_car_queue', $data, 'sysno='.intval($id));
    }

    public function getCarrecordList($params)
    {
        $filter = array();
        if(isset($params['doc_type']) && $params['doc_type']!='')
        {
            $filter[] = " cq.doc_type = {$params['doc_type']} "; 
        }
        if(isset($params['carstatus']) && $params['carstatus']!='')
        {
            $filter[] = " cq.carstatus in ({$params['carstatus']}) "; 
        }
        if(isset($params['carid']) && $params['carid']!='')
        {
            $filter[] = " cq.carid like '%{$params['carid']}%' ";
        }
        if(isset($params['begin_time']) && $params['begin_time']!='')
        {
            $filter[] = " cq.updated_at >= '{$params['begin_time']}' "; 
        }
        if(isset($params['end_time']) && $params['end_time']!='')
        {
            $filter[] = " cq.updated_at <= '{$params['end_time']} 23:59:59' "; 
        }
        $where = ' WHERE cq.isdel = 0 AND cq.status=1  ';

        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = ' ORDER BY cq.updated_at DESC';
        $sql = "SELECT * from (SELECT if(cq.doc_type=1,pi.poundsinstatus,po.poundsoutstatus ) as docstatus ,cq.* FROM `".DB_PREFIX."doc_car_queue` cq LEFT JOIN `".DB_PREFIX."doc_pounds_in` pi ON (cq.pounds_sysno = pi.sysno)
LEFT JOIN `".DB_PREFIX."doc_pounds_out` po ON (cq.pounds_sysno = po.sysno) {$where} {$order} ) a where a.docstatus in (2,3,4,6) or a.docstatus is null";

        $result['totalRow'] = count($this->dbh->select($sql));

        $sql = "select a.* from (SELECT if(cq.doc_type=1,pi.poundsinstatus,po.poundsoutstatus ) as docstatus ,cq.* FROM `".DB_PREFIX."doc_car_queue`cq LEFT JOIN `".DB_PREFIX."doc_pounds_in` pi ON (cq.pounds_sysno = pi.sysno)
LEFT JOIN `".DB_PREFIX."doc_pounds_out` po ON (cq.pounds_sysno = po.sysno) {$where} {$order} ) a where a.docstatus in (2,3,4,6) or a.docstatus is null";

        if(boolval($params['page'])==false && isset($params['page'])){

            $data = $this->dbh->select($sql);
            $result['list'] = $data;

        }else{

            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            
            $data = $this->dbh->select_page($sql);

            $result['list']=$data;
        }

        return $result;
    }


    public function updateCarrecord($data, $id)
    {
        return $this->dbh->update(DB_PREFIX.'doc_car_queue', $data, 'sysno='.intval($id));
    }

    public function getCarRecodeById($sysno){
        $sql ="SELECT * FROM `".DB_PREFIX."doc_car_queue` WHERE sysno =".intval($sysno);
        $res = $this -> dbh -> select_row($sql);
        if(!$res){
            return [];
        }
        //查询该类型的每次车辆的作业时间
        $sql = "SELECT queuetime FROM ".DB_PREFIX."base_queue WHERE queuetype = {$res['tp_sysno']} AND queuetype_sysno= ".intval($res['queuetype_sysno']);
        $queuetime = $this->dbh->select_one($sql);
        $queuetime = $queuetime ? $queuetime : 10;

        //查询该车次在该排队列表里是第几位
        $upNumSql = "SELECT count(*) as upNum FROM `".DB_PREFIX."doc_car_queue` WHERE tp_sysno = {$res['tp_sysno']} AND queuetype_sysno={$res['queuetype_sysno']} AND doc_type = {$res['doc_type']} AND orderno > {$res['sysno']}";
        $upNum = $this -> dbh -> select_one($upNumSql);
        $res['num'] = $upNum;
        $res['queuetime'] = $upNum * $queuetime;
        $res['car_queue_status'] = $upNum==0 ? '通知作业' : '排队中';
        return $res;
    }
}
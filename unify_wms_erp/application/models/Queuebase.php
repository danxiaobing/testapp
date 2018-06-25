<?php
/**
 * User: HR
 * Date: 2017/07/25 
 * Time: 10:20
 */

class QueuebaseModel
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

    /**
     * 根据条件检索片区
     * @return 数组
     */
    public function getQueuebaseList($params)
    {
        $filter = array();
        if (isset($params['queuetype']) && $params['queuetype'] != '') {
            $filter[] = " `queuetype`='{$params['queuetype']}'";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " AND " . implode(' AND ', $filter);
        }

        $order = "order by `updated_at` desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_queue` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {

        	$sql = "SELECT * FROM `".DB_PREFIX."base_queue` {$where} {$order} ";
            if( isset($params['page'] ) && $params['page'] == false){

                $data = $this->dbh->select($sql);

                $result['list'] = $data;

            }else{

                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );

                $data = $this->dbh->select_page($sql);

                $result['list'] = $data;
            }

        }

        return $result; //返回查询数据
    }


    public function addQueuebase($data)
    {
    	return $this->dbh->insert(DB_PREFIX.'base_queue', $data);
    }



    public function getQueuebaseByid($id)
    {
    	$sql = "SELECT * FROM `".DB_PREFIX."base_queue` where sysno={$id}";
    	return $this->dbh->select_row($sql);
    }

    /**
     * 根据排号类型获取编号
     * @param  [type] $queuetype [description]
     * @return [type]            [description]
     */
    public function getQueueno($queuetype,$id='')
    {	
    	if ($queuetype==1) 
    	{
	        if (isset($id) && $id != '') {
	            $filter = " AND `sysno`= {$id} ";
	        }

	        $sql = "SELECT cranename as name,sysno,goodsname,goods_sysno FROM `".DB_PREFIX."base_crane` where status=1 AND isdel=0 {$filter} order by `updated_at` desc";

	        $result = $this->dbh->select($sql);
    	}else
    	{
	        if (isset($id) && $id != '') {
	            $filter = " AND bs.`sysno`= {$id} ";
	        }

    		$sql = "SELECT bs.storagetankname as name,bs.sysno,bg.goodsname,bs.goods_sysno  from `".DB_PREFIX."base_storagetank` bs
    		LEFT JOIN ".DB_PREFIX."base_goods bg ON bs.goods_sysno=bg.sysno
    		where bs.status=1 AND bs.isdel=0 {$filter} ORDER BY bs.area_sysno ,bs.storagetankname ASC";
    		$result = $this->dbh->select($sql);
    	}

    	return $result;
    }


    public function updateQueuebase($data, $id)
    {
    	return $this->dbh->update(DB_PREFIX.'base_queue', $data, 'sysno='.intval($id));
    }



    public function is_existence($queuetype, $queuetype_sysno)
    {
    	$sql = "SELECT * FROM ".DB_PREFIX."base_queue where queuetype={$queuetype} AND queuetype_sysno = {$queuetype_sysno}";

    	return $this->dbh->select_row($sql);
    }


    public function getBaseinfo($params)
    {
        $filter = array();
        if (isset($params['queuetype']) && $params['queuetype'] != '') {
            $filter[] = " `queuetype`='{$params['queuetype']}'";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " AND " . implode(' AND ', $filter);
        }

        $order = "order by `updated_at` desc";

        $sql = "SELECT * FROM `".DB_PREFIX."base_queue` {$where} {$order} ";

        $queuebasedata = $this->dbh->select($sql);

        $data = [];
        foreach ($queuebasedata as $k => $value) {
            $sql = "SELECT COUNT(*) FROM `".DB_PREFIX."doc_car_queue` WHERE tp_sysno={$value['sysno']} AND isdel = 0 AND status=1 AND carstatus = 1 AND queuestatus = 1 ";
            $countcars = $this->dbh->select_one($sql);
            $value['countcars'] = $countcars;
            if($countcars!=0){
                $data[] = $value;
            }
        }

        return $data;
    }
}
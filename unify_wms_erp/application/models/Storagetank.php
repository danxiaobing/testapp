<?php

/**
 * Storage Model
 *
 */
class StoragetankModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存类实例
     *
     * @var object
     */
    public $mch = null;


    /**
     * Constructor
     *
     * @param   object $dbh
     * @param   object $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

    public function searchStoragetank($params)
    {
        $filter = array();
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " s.`storagetankname` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_categoryid']) && $params['bar_categoryid'] != '-100') {
            $filter[] = " c.`sysno`='{$params['bar_categoryid']}'";
        }
        if (isset($params['bar_areaid']) && $params['bar_areaid'] != '-100') {
            $filter[] = " a.`sysno`='{$params['bar_areaid']}'";
        }
        if (isset($params['bar_typeid']) && $params['bar_typeid'] != '-100') {
            $filter[] = " s.`storagetanknature`='{$params['bar_typeid']}'";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " s.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " s.`isdel`='{$params['bar_isdel']}'";
        }
        if (isset($params['storagetankname']) && $params['storagetankname'] != '') {
            $filter[] = " s.`storagetankname`='{$params['storagetankname']}'";
        }
        if (isset($params['storagetankbg']) && $params['storagetankbg'] != '') {
            $filter[] = " s.`storagetankbg`={$params['storagetankbg']}";
        }
        if (isset($params['tank_stockqty']) && $params['tank_stockqty'] != '') {
            $filter[] = " s.`tank_stockqty`={$params['tank_stockqty']}";
        }


        $where = 's.isdel = 0';
        $order = 'ORDER BY s.storagetankname ASC';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from `".DB_PREFIX."base_storagetank` s
        LEFT JOIN `".DB_PREFIX."base_area` a ON (s.`area_sysno` = a.`sysno`)
        LEFT JOIN `".DB_PREFIX."base_storagetank_category` c ON (s.`storagetank_category_sysno` = c.`sysno`)
        where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select s.*,a.areaname,c.storagetank_categoryname,bg.goodsname
                        from ".DB_PREFIX."base_storagetank s
                        left join ".DB_PREFIX."base_area a on (s.area_sysno = a.sysno)
                        left join ".DB_PREFIX."base_storagetank_category c on (s.storagetank_category_sysno = c.sysno)
                        LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = s.goods_sysno
                        where {$where} $order";
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "select s.sysno,s.storagetankname,s.storagetanknature,s.theoreticalcapacity,s.height,s.diameter,FORMAT(s.actualcapacity,3) AS actualcapacity,s.status,a.areaname,c.storagetank_categoryname,s.goods_sysno,bg.goodsname
                        from ".DB_PREFIX."base_storagetank s 
                        left join ".DB_PREFIX."base_area a on (s.area_sysno = a.sysno) 
                        left join ".DB_PREFIX."base_storagetank_category c on (s.storagetank_category_sysno = c.sysno)
                        LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = s.goods_sysno
                        where {$where} $order";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 获取可入库储罐
     *
     */
    public function getStoragetank()
    {
        //$sql = "select s.* from ".DB_PREFIX."base_storagetank s where s.status = 1 and isdel =0 ";
        #@author wu xianneng
//        $sql = "select sysno,storagetankname,theoreticalcapacity,actualcapacity,ifnull(stockqty,0) as stockqty
//                from ".DB_PREFIX."base_storagetank a
//                left join (select storagetank_sysno,sum(stockqty) as stockqty from ".DB_PREFIX."storage_stock where isclearstock=0 and iscurrent=1 and isdel=0  group by storagetank_sysno) b on a.sysno=b.storagetank_sysno
//                WHERE sysno NOT IN ( SELECT DISTINCT storagetank_sysno FROM ".DB_PREFIX."doc_contract_storagetank cs LEFT JOIN ".DB_PREFIX."doc_contract co ON co.sysno = cs.contract_sysno WHERE co.contractenddate>NOW()) HAVING stockqty = 0 ";
        #@author wu xianneng
        $sql = "select bs.sysno,bs.storagetankname,bs.theoreticalcapacity,bs.actualcapacity,bs.tank_stockqty
                from ".DB_PREFIX."base_storagetank bs
                WHERE bs.actualcapacity>bs.tank_stockqty AND bs.status = 1 AND bs.isdel = 0 AND bs.sysno NOT IN
                ( SELECT DISTINCT ifnull(storagetank_sysno,0) FROM ".DB_PREFIX."doc_contract_goods cg
                LEFT JOIN ".DB_PREFIX."doc_contract co ON co.sysno = cg.contract_sysno
                WHERE co.contractenddate>NOW() AND co.status = 1 AND co.isdel = 0 AND co.contractstatus != 7) ";
        return $this->dbh->select($sql);
    }

    /*
     * 车入库获取可入库储罐
     *
     */
    public function getStoragetank2()
    {
        $sql = "select bs.sysno,bs.storagetankname,bs.theoreticalcapacity,bs.actualcapacity,bs.tank_stockqty
                from ".DB_PREFIX."base_storagetank bs
                WHERE bs.actualcapacity>bs.tank_stockqty AND bs.status = 1 AND bs.isdel = 0 ";
        return $this->dbh->select($sql);
    }

    /*
     * 获取可包罐储罐
     *@author wu xianneng
     */
    public function getCanbgStoragetank()
    {
        $sql = "select bs.sysno,bs.storagetankname,bs.theoreticalcapacity,bs.actualcapacity,bs.tank_stockqty
                from ".DB_PREFIX."base_storagetank bs
                WHERE bs.tank_stockqty = 0 AND bs.sysno NOT IN
                ( SELECT DISTINCT ifnull(storagetank_sysno,0) FROM ".DB_PREFIX."doc_contract_goods cg
                LEFT JOIN ".DB_PREFIX."doc_contract co ON co.sysno = cg.contract_sysno
                WHERE co.contractenddate>NOW()) ";
        return $this->dbh->select($sql);
    }

    public function getStoragetankById($id)
    {
        $sql = "SELECT s.*,a.`areaname`,c.`storagetank_categoryname`,bg.goodsno,bg.goodsname,s.actualcapacity
                    FROM `".DB_PREFIX."base_storagetank` s
                    LEFT JOIN `".DB_PREFIX."base_area` a ON (s.`area_sysno` = a.`sysno`)
                    LEFT JOIN `".DB_PREFIX."base_storagetank_category` c ON (s.`storagetank_category_sysno` = c.`sysno`)
                    LEFT JOIN ".DB_PREFIX."base_goods bg ON  bg.sysno = s.goods_sysno
                    WHERE s.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addStoragetank($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_storagetank', $data);
    }

    public function updateStoragetank($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'base_storagetank', $data, 'sysno=' . intval($id));
    }

    public function searchStoragetankcategory($params)
    {
        $filter = array();
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " c.`storagetank_categoryname` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " c.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " c.`isdel`='{$params['bar_isdel']}'";
        }

        $where = 'isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."base_storagetank_category c where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select c.* from ".DB_PREFIX."base_storagetank_category c where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];

                $arr = $this->dbh->select($sql);


                $result['list'] = $arr;
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "select c.* from ".DB_PREFIX."base_storagetank_category c where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }

                $arr = $this->dbh->select_page($sql);

                $result['list'] = $arr;
            }
        }

        return $result;
    }

    public function getStoragetankcategorylistById($id = 0)
    {
        $arr = array();

        $sql = "select * from ".DB_PREFIX."base_storagetank_category where isdel = 0 and status = 1";
        if ($pid > -1) {
            $sql .= " and sysno = $pid ";
        }

        $arr = $this->dbh->select($sql);
        return $arr;
    }

    public function getStoragetankcategoryById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."base_storagetank_category` WHERE `sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addStoragetankcategory($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_storagetank_category', $data);
    }

    public function updateStoragetankcategory($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'base_storagetank_category', $data, 'sysno=' . intval($id));
    }

    /*
     *
     */
    public function addStoragetankGoods($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_storagetank_goods', $data);
    }

    /**
     * 获取储罐材质基本信息
     */

    public function getStoragetankcategory()
    {
        $sql = "SELECT sysno,storagetank_categoryname FROM  ".DB_PREFIX."base_storagetank_category WHERE `status` = 1 AND isdel = 0";
        return $this->dbh->select($sql);
    }

    /**
     * 查询合同储罐信息
     */
    public function storagecontractByid($id)
    {
        $sql = "SELECT bs.sysno,bs.storagetankname,bs.storagetanknature,area.areaname,sc.storagetank_categoryname,cg.invoice_company_sysno,
	            bs.actualcapacity,cg.yearqty,cg.yearamount,cg.exyearrate,cg.memo,com.companyname,com.bank,com.bank_account
	            FROM ".DB_PREFIX."doc_contract_goods cg
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON cg.storagetank_sysno = bs.sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank_category sc on bs.storagetank_category_sysno =sc.sysno
                LEFT JOIN ".DB_PREFIX."base_area area on bs.area_sysno = area.sysno
                LEFT JOIN ".DB_PREFIX."base_company com on cg.invoice_company_sysno = com.sysno
                where cg.contract_sysno = {$id}";
        return $this->dbh->select($sql);
    }

    /*
     *根据储罐id查询是否包罐
     * @return array(
        'istank_bg'=>是否包罐：0否1是
        ‘contractdate’=>合同到期日：有到期日返回日期格式，没有到期返回空
        ‘customer_sysno’=>客户id：有返回sysno，没有返回空
        ‘customername’=>客户名称：有返回字符串，没有返回空
        )
     */
    public function isbgById($id){
        $result = array();
        $sql = "SELECT * FROM ".DB_PREFIX."base_storagetank WHERE sysno = $id";
        $res = $this->dbh->select_row($sql);
        if($res['storagetankbg'] ==2){
            $result = array(
                'istank_bg'=>0,
                'contractdate'=>null,
                'customer_sysno'=>null,
                'customername'=>null
            );
        }elseif($res['storagetankbg'] ==1){
            $sql = "SELECT * FROM ".DB_PREFIX."doc_contract_goods WHERE storagetank_sysno = $id";
            $res = $this->dbh->select_row($sql);

            if($res){
                $sql = "SELECT * FROM ".DB_PREFIX."doc_contract WHERE sysno = {$res['contract_sysno']}";

                $res = $this->dbh->select_row($sql);

                if($res['contractenddate']>date('Y-m-d',time())){
                    $result = array(
                        'istank_bg'=>1,
                        'contractdate'=>$res['contractenddate'],
                        'customer_id'=>$res['customer_id'],
                        'customername'=>$res['customername']
                    );
                }else{
                    $result = array(
                        'istank_bg'=>0,
                        'contractdate'=>null,
                        'customer_sysno'=>null,
                        'customername'=>null
                    );
                }
            }else{
                $result = array(
                    'istank_bg'=>0,
                    'contractdate'=>null,
                    'customer_sysno'=>null,
                    'customername'=>null
                );
            }
        }
        return $result;
    }

    public function storagetankgoodsbyid($storagetank_sysno)
    {
        if (!$storagetank_sysno) {
            return false;
        }
        $sql = "SELECT goods_sysno FROM `".DB_PREFIX."base_storagetank` where status = 1 and sysno =" . intval($storagetank_sysno);
        $res = $this->dbh->select_row($sql);
        return $res['goods_sysno'];
    }

    /**
     *查询储罐的信息，需要（实际容量、可存放吨数，剩余容量百分比、储罐编号）
     * @author zhaoshiyu
     */
    public function getStorageData()
    {
        $sql = "SELECT bs.area_sysno,bs.sysno,bs.storagetankname,bs.storagetanknature,bs.theoreticalcapacity,round((bs.tank_stockqty/bga.density),2) AS havecapacity,
                round((bs.theoreticalcapacity-bs.tank_stockqty/bga.density),2) AS leftcapacity,
                bs.actualcapacity,bs.tank_stockqty as stockqty,round((bs.actualcapacity-bs.tank_stockqty)) AS leftqty,bg.goodsname
                FROM ".DB_PREFIX."base_storagetank bs
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = bg.sysno
                WHERE bs.`status` = 1 AND bs.isdel = 0 AND bga.isdel = 0 AND bga.status = 1 AND bg.isdel = 0 AND bg.status = 1";
        return $this->dbh->select($sql);
    }


    //查询单个罐的可放罐容
    public function getStoragetankavailable($params)
    {
        $filter = array();
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " `sysno`='{$params['storagetank_sysno']}'";
        }

        $where = "where status = 1 AND isdel = 0";
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        // $sql = "select sysno,storagetankname,theoreticalcapacity,actualcapacity,area_sysno,ifnull(stockqty,0) as stockqty from ".DB_PREFIX."base_storagetank a left join 
        //     (select storagetank_sysno,sum(stockqty) as stockqty from ".DB_PREFIX."storage_stock where isclearstock=0 and iscurrent=1 and isdel=0  group by storagetank_sysno) b on a.sysno=b.storagetank_sysno $where ";
        $sql = "select * from ".DB_PREFIX."base_storagetank as s $where";
        $res = $this->dbh->select_row($sql);
        if (!$res) {
            return 0;
        }
        $available = $res['actualcapacity'] - $res['tank_stockqty'] > 0 ? $res['actualcapacity'] - $res['tank_stockqty'] : 0;
        return $available;
    }

    /*
	 * 获取货品清罐信息
	 * @author wu xianneng
	 */
    public function getGoodsStorageInfo($params)
    {
        $filter = array();
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " bsg.`storagetank_sysno`='{$params['storagetank_sysno']}'";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' AND ', $filter);
        }
        $order = " ORDER BY created_at DESC ";
        $sql = "select bsg.*,bg.goodsname
                from ".DB_PREFIX."base_storagetank_goods bsg
                LEFT JOIN ".DB_PREFIX."base_goods bg ON  bg.sysno = bsg.goods_sysno  $where $order
                limit 0,3";
        return $this->dbh->select($sql);
    }

    /*
     *获取可包罐储罐储罐信息
     * @author wuxianneng
     */
    public function getstoragetankinfo()
    {
        $sql = "select s.sysno as storagetank_sysno,s.storagetankname,s.theoreticalcapacity,ba.areaname,sc.storagetank_categoryname
	            from ".DB_PREFIX."base_storagetank s
	            LEFT JOIN ".DB_PREFIX."base_area ba ON ba.sysno = s.area_sysno
				LEFT JOIN ".DB_PREFIX."base_storagetank_category sc ON sc.sysno = s.storagetank_category_sysno
				LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = s.goods_sysno
                where s.status = 1 and s.isdel = 0 ";//AND s.tank_stockqty = 0

        return $this->dbh->select($sql);
    }

    /**
     * 公共库存操作
     * @param array() type：1入库榜码增加罐容 2 减少罐容  3 出库榜码减少库存 8 锁定罐出库容量 9释放罐出库锁定容量 10退回罐出库锁定容量 12船出库作废||
     * @example
     * $S = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
     * $params = array('type'=>8);
     * $data = array(
     * );
     * $params['data'] = $data;
     * $ret = $S->pubstoragetankoperation($params);
     * if(!$ret) {
     * $this->dbh->rollback();
     * return false;
     * }
     */
    public function pubstoragetankoperation($params = array())
    {
        $data = $params['data'];

        if (!is_array($data)) {
            return false;
        }

        #罐容量
        $totalorderoutqty = $data['orderoutqty'] ? $data['orderoutqty'] : 0;
        $totaltobeqty = $data['tobeqty'] ? $data['tobeqty'] : 0;
        #根据搜索条件,先获取储罐表记录
        $filter = array();

        if (isset($data['sysno']) && $data['sysno'] != '') {
            $filter[] = " `sysno` = '" . $data['sysno'] . "' ";
        }


        $where = 'isdel = 0 and status = 1';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $sql = "select * from `".DB_PREFIX."base_storagetank` where {$where}";
        $storagetankinfo = $this->dbh->select_row($sql);

        switch ($params['type']) {
            case 1:
                //储罐信息更改
                if(!$storagetankinfo){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }

                $update_storagetank['tank_stockqty'] = floatval($storagetankinfo['tank_stockqty']) + floatval($totalorderoutqty) - $data['ullage'];
                //强控超罐容情况
//                if( $update_storagetank['tank_stockqty'] >= $storagetankinfo['actualcapacity'] ){
//                    $msgcode = array(
//                        'code' => 300,
//                        'message' => '该储罐已满，请检查',
//                    );
//                    return $msgcode;
//                }
                $update_storagetank['qualityname'] = $data['qualityname'] ? $data['qualityname']  : '';
                $update_storagetank['goods_quality_sysno'] = $data['goods_quality_sysno'] ? $data['goods_quality_sysno'] : 0;
                $sql_edit_storagetank = $this->dbh->update(DB_PREFIX.'base_storagetank', $update_storagetank, 'sysno=' . intval($storagetankinfo['sysno']));
                if(!$sql_edit_storagetank){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '更新储罐信息失败',
                    );
                    return $msgcode;
                }
                $msgcode = array(
                    'code' => 200,
                    'message' => '修改储罐成功',
                );
                return $msgcode;
            case 2:
                // if ($totalorderoutqty <= 0) {
                //     $msgcode = array(
                //         'code' => 300,
                //         'message' => '参数错误',
                //     );
                //     return $msgcode;
                // }

                if (!empty($storagetankinfo)) {

                    $update['updated_at'] = '=NOW()';
                    // $update['orderoutqty'] = $storagetankinfo['orderoutqty'] - $totalorderoutqty;
                    $update['tank_stockqty'] = floatval($storagetankinfo['tank_stockqty']) - floatval($totaltobeqty);
                    if($update['tank_stockqty'] < 0){
                        $msgcode = array(
                            'code' => 300,
                            'message' => '储罐'.$storagetankinfo['storagetankname'].'罐容不足,不允许审核通过',
                        );
                        return $msgcode;
                    }
                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($storagetankinfo['sysno']));

                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
            case 3:
                //储罐信息更改
                if(!$storagetankinfo){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
                $update_storagetank['tank_stockqty'] = floatval($storagetankinfo['tank_stockqty']) - floatval($totalorderoutqty);
                if($update_storagetank['tank_stockqty'] < 0){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐罐容不足',
                    );
                    return $msgcode;
                }
                $sql_edit_storagetank = $this->dbh->update(DB_PREFIX.'base_storagetank', $update_storagetank, 'sysno=' . intval($storagetankinfo['sysno']));
                if(!$sql_edit_storagetank){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '更新储罐信息失败',
                    );
                    return $msgcode;
                }
                $msgcode = array(
                    'code' => 200,
                    'message' => '修改储罐成功',
                );
                return $msgcode;
            case 8:
                if ($totalorderoutqty <= 0) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '预约待出量参数错误',
                    );
                    return $msgcode;
                }

                if (!empty($storagetankinfo)) {

                    $update['orderoutqty'] = floatval($storagetankinfo['orderoutqty']);
                    $update['sysno'] = $storagetankinfo['sysno'];
                    $update['updated_at'] = '=NOW()';
                    $update['tank_stockqty'] = floatval($storagetankinfo['tank_stockqty']);

                    if ($update['tank_stockqty'] - floatval($update['orderoutqty']) - floatval($totalorderoutqty) < 0) {
                        $msgcode = array(
                            'code' => 300,
                            'message' => '预约储罐可用余量不足',
                        );
                        return $msgcode;
                    }
 
                    $update['orderoutqty'] = floatval($update['orderoutqty']) + floatval($totalorderoutqty);
                    unset($update['tank_stockqty']);

                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params) . "\n", 3, './logs/stock_err.log');
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
            case 9:
                if($totalorderoutqty<0){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '预约待出量参数错误',
                    );
                    return $msgcode;
                }
                
                if(!empty($storagetankinfo))
                {

                    $update['orderoutqty'] = floatval($storagetankinfo['orderoutqty']);
                    $update['sysno'] = floatval($storagetankinfo['sysno']);

                    if(floatval($update['orderoutqty']) - floatval($totalorderoutqty) < 0)
                    {
                        $msgcode = array(
                            'code' => 300,
                            'message' => '预约出库数量错误',
                        );
                        return $msgcode;
                    }

                    $update['orderoutqty'] = floatval($update['orderoutqty']) - floatval($totalorderoutqty);
                    
                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($update['sysno']));
                    if(!$res){
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }
                    
                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                }
                else
                {
                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params) . "\n", 3, './logs/stock_err.log');
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
            case 10:
                if($totalorderoutqty<0){
                    $msgcode = array(
                        'code' => 300,
                        'message' => '预约待出量参数错误',
                    );
                    return $msgcode;
                }

                if(!empty($storagetankinfo))
                {

                    $update['orderoutqty'] = $storagetankinfo['orderoutqty'];
                    $update['sysno'] = $storagetankinfo['sysno'];

                    $update['orderoutqty'] = $update['orderoutqty'] + $totalorderoutqty;

                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($update['sysno']));
                    if(!$res){
                        echo "更新罐容失败";die();
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                }
                else
                {
                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params) . "\n", 3, './logs/stock_err.log');
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
                case 12:
                // if ($totalorderoutqty <= 0) {
                //     $msgcode = array(
                //         'code' => 300,
                //         'message' => '参数错误',
                //     );
                //     return $msgcode;
                // }

                if (!empty($storagetankinfo)) {

                    $update['updated_at'] = '=NOW()';
                    // $update['orderoutqty'] = $storagetankinfo['orderoutqty'] + $totalorderoutqty;
                    $update['tank_stockqty'] = floatval($storagetankinfo['tank_stockqty']) + floatval($totaltobeqty);

                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($storagetankinfo['sysno']));

                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
            case 13:
                if ($totalorderoutqty <= 0) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '预约待出量参数错误',
                    );
                    return $msgcode;
                }

                if (!empty($storagetankinfo)) {

                    $update['orderoutqty'] = floatval($storagetankinfo['orderoutqty']);
                    $update['sysno'] = $storagetankinfo['sysno'];
                    $update['updated_at'] = '=NOW()';
                    $update['tank_stockqty'] = floatval($storagetankinfo['tank_stockqty']);

                    // if (floatval($update['tank_stockqty']) - floatval($update['orderoutqty']) - floatval($totalorderoutqty) < 0) {
                    //     $msgcode = array(
                    //         'code' => 300,
                    //         'message' => '预约储罐可用余量不足',
                    //     );
                    //     return $msgcode;
                    // }

                    $update['orderoutqty'] = floatval($update['orderoutqty']) + floatval($totalorderoutqty);
                    unset($update['tank_stockqty']);

                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params) . "\n", 3, './logs/stock_err.log');
                    $msgcode = array(
                        'code' => 300,
                        'message' => '储罐记录不存在',
                    );
                    return $msgcode;
                }
            default:
                return false;
        }
    }


    /**
     * ".DB_PREFIX."doc_storagetank_log
     * @param $data
     * @return mixed
     */
    public function addStoragetankLog($data){
        if(!is_array($data)){
            $msgcode = array(
                'code' => 300,
                'message' => '非法调用 参数必需为数组',
            );
            return $msgcode;
        }
        if(!isset($data['storagetank_sysno']) || empty($data['storagetank_sysno'])){
            $msgcode = array(
                'code' => 300,
                'message' => '必需传入储罐号',
            );
            return $msgcode;
        }
        if(!isset($data['beqty']) || empty($data['beqty'])){
            $data['beqty'] = floatval($data['beqty']);
            $msgcode = array(
                'code' => 300,
                'message' => '必需传入实际数量',
            );
            return $msgcode;
        }
        if(!isset($data['doctype']) || empty($data['doctype'])){
            $msgcode = array(
                'code' => 300,
                'message' => '必需传入操作类型',
            );
            return $msgcode;
        }
        $sql = "SELECT * FROM `".DB_PREFIX."base_storagetank` WHERE  sysno = '".intval($data['storagetank_sysno'])."'";
        $res = $this->dbh -> select_row($sql);
        if(!$res){
            $msgcode = array(
                'code' => 300,
                'message' => '请检查是否存在该储罐',
            );
            return $msgcode;
        }
        //注意这个值是修改之前的罐容实际存放量

        $data['beforebeqty'] = floatval($res['tank_stockqty']) - floatval($data['beqty']);
        $data['status'] = 1;
        $data['isdel'] = 0;
        $data['version'] = 1;
        $data['created_at'] = '=NOW()';
        $data['updated_at'] = '=NOW()';
        $result  =  $this->dbh->insert(DB_PREFIX.'doc_storagetank_log', $data);
        if(!$result){
            $msgcode = array(
                'code' => 300,
                'message' => '新增储罐记录失败',
            );
            return $msgcode;
        }
        $msgcode = array(
            'code' => 200,
            'message' => $result,
        );
        return $msgcode;
    }

    /**
     * 根据商品获取储罐
     * @param int $goods_sysno 商品ID
     * @return array
     */
    public function getStoragetankBygoods($goods_sysno)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."base_storagetank WHERE goods_sysno = {$goods_sysno}";

        return $this->dbh->select($sql);
    }
}

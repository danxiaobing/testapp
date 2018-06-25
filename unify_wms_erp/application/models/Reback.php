<?php
/**
 * Stockout Model
 *jp
 */

class RebackModel
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
    //列表
    public function searchReback($params)
    {
            $filter = array();

            if (isset($params['begin_time']) && $params['begin_time'] != '') {
                $filter[] = " r.`stockrebackdate`>='{$params['begin_time']}'";
            }
            if (isset($params['end_time']) && $params['end_time'] != '') {
                $filter[] = " r.`stockrebackdate`<='{$params['end_time']}'";
            }

            if (isset($params['carid']) && $params['carid'] != '') {

                $filter[] = " r.`carid` LIKE '%".$params['carid']."%' ";
            }

            if (isset($params['stockinstatus']) && $params['stockinstatus'] != '') {
                $filter[] = " r.`stockinstatus`='{$params['stockinstatus']}'";
            }

            if (isset($params['sysno']) && $params['sysno'] != '') {
                $filter[] = " r.`sysno`='{$params['sysno']}'";
            }

            $where = 'r.isdel=0';
            if (1 <= count($filter)) {
                $where .= ' AND ' . implode(' AND ', $filter);
            }
            $order = " order by r.created_at desc";

            $sql = "SELECT COUNT(*) FROM (SELECT r.sysno  FROM `".DB_PREFIX."doc_stock_reback` r
                    LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON rd.`stockreback_sysno`=r.`sysno` WHERE {$where} GROUP BY r.`sysno`) t ";

            $result = $params;
            $result['totalRow'] = $this->dbh->select_one($sql);

            $result['list'] = array();
            if ($result['totalRow']) {
                $sql = "SELECT r.*,sum(rd.rebacknumber) as rebacknumber,rd.stockreback_sysno,rd.stockout_sysno,rd.stockoutno,rd.stockoutdetail_sysno,rd.stocktype,rd.doc_sysno,rd.customer_sysno,rd.customername,rd.realnumber,rd.bucketnumber,rd.takegoodsno,rd.takegoodscompany,rd.unitname,rd.memo,rd.auditreason
                        FROM `".DB_PREFIX."doc_stock_reback` r
                        LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON (rd.`stockreback_sysno`=r.`sysno`)
                        WHERE {$where} GROUP BY r.`sysno` $order";

                if (isset($params['page']) && $params['page'] == false) {

                    $result['list'] = $this->dbh->select($sql);
                } else {
                    $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                    $this->dbh->set_page_num($params['pageCurrent']);
                    $this->dbh->set_page_rows($params['pageSize']);

                    $result['list'] = $this->dbh->select_page($sql);
                }
            }
         //   echo $sql;die;
            return $result;
            }
    //获取磅码单主表
    public function getPounds($params)
    {
        $filter = array();
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " p.`sysno` = '{$params['sysno']}' ";
        }



        $where = ' p.isdel=0 and p.status < 2 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_pounds_out` p
                LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pd on (pd.`pounds_out_sysno`=p.`sysno`)
                LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd on (sd.`sysno`=pd.`stockoutdetail_sysno`)
                LEFT JOIN `".DB_PREFIX."doc_stock_out` s on (s.`sysno`=sd.`stockout_sysno`) and s.isdel = 0
                WHERE p.poundsoutstatus =4  and s.stockoutstatus =3 and p.issavereback=0 and {$where} group by p.sysno ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT p.sysno,p.sysno as poundsout_sysno,p.poundsoutno,p.cartype,p.carid,p.poundsoutstatus,p.cartype,p.beqty,pd.goods_sysno,pd.goodsname,pd.customer_sysno,pd.customername,pd.realnumber,pd.bucketnumber,pd.stockout_sysno,pd.stockoutno,pd.stockoutdetail_sysno,pd.takegoodsno,pd.takegoodscompany,pd.unitname,sd.stocktype,sd.stock_sysno,pd.inshipname
                    FROM `".DB_PREFIX."doc_pounds_out` p
                    LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pd on (pd.`pounds_out_sysno`=p.`sysno`)
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd on (sd.`sysno`=pd.`stockoutdetail_sysno`)
                    LEFT JOIN `".DB_PREFIX."doc_stock_out` s on (s.`sysno`=sd.`stockout_sysno`) and s.isdel = 0
                    WHERE p.poundsoutstatus =4  and s.stockoutstatus =3 and p.issavereback=0 and {$where} group by p.sysno ";
            if ($params['orders'] != '') {
                $sql .= " order by p.".$params['orders'];
            } else {
                $sql .= " order by p.created_at desc";
            }
            if (isset($params['page']) && $params['page'] == false) {

                $result['list'] = $this->dbh->select($sql);

            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
 //       echo $sql;die;
        return $result;
    }


    //获取磅码单明细,reload明细表

    public function getPoundDetail($params)
    {
        $filter = array();
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " p.`sysno` = '{$params['sysno']}' ";
        }

        if (isset($params['pd_sysno']) && $params['pd_sysno'] != '') {
            $filter[] = " pd.`sysno` = '{$params['pd_sysno']}' ";
        }

        $where = ' p.isdel=0 and p.status < 2 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)
                FROM `".DB_PREFIX."doc_pounds_out` p
                LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pd on (pd.`pounds_out_sysno`=p.`sysno`)
                LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd on (sd.`sysno`=pd.`stockoutdetail_sysno`)
                LEFT JOIN `".DB_PREFIX."doc_stock_out` s on (s.`sysno`=sd.`stockout_sysno`)
                WHERE  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT p.sysno,p.poundsoutno,p.cartype,p.carid,p.poundsoutstatus,p.cartype,p.beqty,pd.sysno as pounddetail_sysno,pd.goods_sysno,pd.goodsname,pd.customer_sysno,pd.customername,pd.realnumber,pd.bucketnumber,pd.stockout_sysno,pd.stockoutno,pd.stockoutdetail_sysno,pd.takegoodsno,pd.takegoodscompany,pd.unitname,sd.stocktype,sd.stock_sysno,pd.inshipname,0 rebacknumber
                    FROM `".DB_PREFIX."doc_pounds_out` p
                    LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pd on (pd.`pounds_out_sysno`=p.`sysno`)
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd on (sd.`sysno`=pd.`stockoutdetail_sysno`)
                     LEFT JOIN `".DB_PREFIX."doc_stock_out` s on (s.`sysno`=sd.`stockout_sysno`)
                    WHERE  {$where} ";

            if (isset($params['page']) && $params['page'] == false) {

                $result['list'] = $this->dbh->select($sql);

            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        //  echo $sql;die;
        return $result;
    }

    //获取磅码单单条明细

    public function getPoundDetailByid($params)
    {
        $filter = array();
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " p.`sysno` = '{$params['sysno']}' ";
        }

        if (isset($params['pd_sysno']) && $params['pd_sysno'] != '') {
            $filter[] = " pd.`sysno` = '{$params['pd_sysno']}' ";
        }

        $where = ' p.isdel=0 and p.status < 2 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)
                FROM `".DB_PREFIX."doc_pounds_out` p
                LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pd on (pd.`pounds_out_sysno`=p.`sysno`)
                LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd on (sd.`sysno`=pd.`stockoutdetail_sysno`)
                LEFT JOIN `".DB_PREFIX."doc_stock_out` s on (s.`sysno`=sd.`stockout_sysno`)
                WHERE  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

            $sql = "SELECT p.sysno,p.poundsoutno,p.cartype,p.carid,p.poundsoutstatus,p.cartype,p.beqty,pd.sysno as pounddetail_sysno,pd.goods_sysno,pd.goodsname,pd.customer_sysno,pd.customername,pd.realnumber,pd.bucketnumber,pd.stockout_sysno,pd.stockoutno,pd.stockoutdetail_sysno,pd.takegoodsno,pd.takegoodscompany,pd.unitname,sd.stocktype,sd.stock_sysno,pd.inshipname
                    FROM `".DB_PREFIX."doc_pounds_out` p
                    LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pd on (pd.`pounds_out_sysno`=p.`sysno`)
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd on (sd.`sysno`=pd.`stockoutdetail_sysno`)
                     LEFT JOIN `".DB_PREFIX."doc_stock_out` s on (s.`sysno`=sd.`stockout_sysno`)
                    WHERE  {$where} ";

                $result['list'] = $this->dbh->select_row($sql);


        //  echo $sql;die;
           return $result;
    }


        /*
        * 添加退货单
         * jp
        */
    public function addReback($data,$detaildata,$status)
    {
        $this->dbh->begin();
        try {         
            if($status ==2){
                $data['stockinstatus'] = 2;
            }elseif($status ==3){
                $data['stockinstatus'] = 3;
            }
            $res = $this->dbh->insert(DB_PREFIX.'doc_stock_reback', $data);

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'新增主表信息失败'];
            }

            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_reback_detail', 'stockreback_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除明细表信息失败'];
            }

                foreach ($detaildata as $value) {
                    $input = array(
                        'stockreback_sysno' => $id,
                        'stockout_sysno' => $value['stockout_sysno'],
                        'stockoutno' => $value['stockoutno'],
                        'stockoutdetail_sysno' => $value['stockoutdetail_sysno'],
                        'stocktype' => $value['stocktype'],
                        'doc_sysno'=>$value['stock_sysno'],
                        'customer_sysno' => $value['customer_sysno'],
                        'customername' => $value['customername'],
                        'realnumber'=>$value['realnumber'],
                        'bucketnumber'=>$value['bucketnumber'],
                        'rebacknumber'=>$value['rebacknumber'],
                        'takegoodsno'=>$value['takegoodsno'],
                        'takegoodscompany'=>$value['takegoodscompany'],
                        'unitname'=>$value['unitname'],
                        'memo'=>$value['memo'],
                        'inshipname'=>$value['inshipname'],
                        'status' => 1,
                        'isdel' => 0,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );  
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_reback_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>'新增明细表信息失败'];
                    }
                }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 31,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' =>'新建退货单'
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'新增日志失败'];
            }
            if($status ==2){  //暂存时操作日志
                $input['opertype'] = 2;
                $input['operdesc'] = '暂存退货单';
            }elseif($status ==3){
                $input['opertype'] = 3;
                $input['operdesc'] = '提交退货单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'新增-日志-失败'];
            }
            //将出库磅码单退货单状态及退货磅码单状态改为1
            $poundsoutData = array(
                'issavereback'=>1,
                'updated_at' => '=NOW()',
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_pounds_out', $poundsoutData, 'sysno=' . intval($data['poundsout_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新磅码单退货单状态失败'];
            }

            $this->dbh->commit();
            return ['code'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'msg'=>'新建失败'];
        }
    }

    /*
       * 更新退货单
       */
    public function updateReback($id,$data,$detaildata,$stockinstatus,$status)
    {
        $this->dbh->begin();
        try {
            if($status==2){
                $data['stockinstatus'] =2;
                if($stockinstatus==6){
                    $data['stockinstatus'] =6;
                }
            }else{
                $data['stockinstatus'] =3;
            }


            $res = $this->dbh->update(DB_PREFIX.'doc_stock_reback', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新主表失败'];
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_reback_detail', 'stockreback_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除明细表信息失败'];
            }

            foreach ($detaildata as $value) {
                $input = array(
                    'stockreback_sysno' => $id,
                    'stockout_sysno' => $value['stockout_sysno'],
                    'stockoutno' => $value['stockoutno'],
                    'stockoutdetail_sysno' => $value['stockoutdetail_sysno'],
                    'stocktype' => $value['stocktype'],
                    'doc_sysno'=>$value['stock_sysno'],
                    'customer_sysno' => $value['customer_sysno'],
                    'customername' => $value['customername'],
                    'realnumber'=>$value['realnumber'],
                    'bucketnumber'=>$value['bucketnumber'],
                    'rebacknumber'=>$value['rebacknumber'],
                    'takegoodsno'=>$value['takegoodsno'],
                    'takegoodscompany'=>$value['takegoodscompany'],
                    'unitname'=>$value['unitname'],
                    'memo'=>$value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_reback_detail', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'更新明细表信息失败'];
                }
            }

            #退货操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 31,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($status ==2) {
                $input['opertype'] = 2;
                $input['operdesc'] = '保存退货单';
            }elseif($status ==3){
                $input['opertype'] = 3;
                $input['operdesc'] = '提交待审核';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新日志失败'];
            }

            $this->dbh->commit();
            return ['code'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'msg'=>'更新失败'];
        }
    }

    //审核方法
    public function auditReback($id,$data,$detaildata,$status,$auditreason){
        $this->dbh->begin();
        try {
            if($status==4){
                $data['stockinstatus'] =4;
            }elseif($status==6){
                $data['stockinstatus'] =6;
                $data['auditreason'] = $auditreason;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_stock_reback', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新主表失败'];
            }
            //将磅码单状态改为作废

            $poundsoutData = array(
                'poundsoutstatus'=>5,
                'created_at' => '=NOW()',
            );

            $res = $this->dbh->update(DB_PREFIX.'doc_pounds_out', $poundsoutData, 'sysno=' . intval($data['poundsout_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新磅码单状态失败'];
            }

            $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            foreach ($detaildata as $value) {
                $input = array(
                    'stockreback_sysno' => $value['sysno'],
                    'stockout_sysno' => $value['stockout_sysno'],
                    'stockoutno' => $value['stockoutno'],
                    'stockoutdetail_sysno' => $value['stockoutdetail_sysno'],
                    'stocktype' => $value['stocktype'],
                    'doc_sysno'=>$value['doc_sysno'],
                    'customer_sysno' => $value['customer_sysno'],
                    'customername' => $value['customername'],
                    'realnumber'=>$value['realnumber'],
                    'bucketnumber'=>$value['bucketnumber'],
                    'rebacknumber'=>$value['rebacknumber'],
                );

                if(!in_array($input['stocktype'],[1,2])){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'库存类型不存在'];
                }

                if(!$input['doc_sysno']){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'库存主键丢失'];
                }

                $rebacknumber = floatval($input['rebacknumber']/1000);
                if($input['stocktype']==1){

                    $params = [ 'type' => 16 ];

                    $searchdata = [
                        'instockqty'=>$rebacknumber,
                        'sysno'=>$input['doc_sysno'],
                    ];
                    $params['data'] = $searchdata;

                    $result = $stock->pubstockoperation($params);
                    if ($result['code'] == 200) {
                        $res_stockid = $result['message'];
                    }else{
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>$result['message'].'更新库存失败'];
                    }
                }

                if($input['stocktype']==2){
                    //查询介绍信明细库存
                    $sql = "select * from ".DB_PREFIX."doc_introduction_detail ind
                            where ind.isdel = 0  and ind.status < 2 and ind.sysno = {$input['doc_sysno']} ";
                    $introduction = $this->dbh->select_row($sql);
                    if(!$introduction){
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>'获取介绍信库存失败'];
                    }

                    $updatedata = array(
                        'untakegoodsnum'=>$rebacknumber + floatval($introduction['untakegoodsnum']),
                        'takegoodsqty'=>floatval($introduction['takegoodsqty'])-$rebacknumber,
                        'created_at' => '=NOW()',
                    );

                    $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail', $updatedata, 'sysno=' . intval($input['doc_sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>'更新介绍信库存失败'];
                    }
                }
                 //更新出库明细实提数量
                  $sql = "select * from ".DB_PREFIX."doc_stock_out_detail sd
                          where sd.isdel = 0  and sd.status < 2 and sd.sysno = {$input['stockoutdetail_sysno']} ";
                 $stockoutDetail = $this->dbh->select_row($sql);
                 if(!$stockoutDetail || empty($stockoutDetail)){
                     $this->dbh->rollback();
                     return ['code'=>300,'msg'=>'获取出库明细失败'];
                 }

                 $stockoutDate = array(
                     'beqty'=>floatval($stockoutDetail['beqty']) - $rebacknumber,
                     'takeqty'=>floatval($stockoutDetail['takeqty']) + $rebacknumber,
                     'created_at' => '=NOW()',
                 );

                $res = $this->dbh->update(DB_PREFIX.'doc_stock_out_detail', $stockoutDate, 'sysno=' . intval($input['stockoutdetail_sysno']));
                if(!$res){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'更新出库明细实提数量失败'];
                }

            }

            #退货操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 31,
                'opertype' =>4,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($status ==4) {
                $input['opertype'] = 4;
                $input['operdesc'] = '审核通过退货单';
            }elseif($status ==6){
                $input['opertype'] = 6;
                $input['operdesc'] = '审核不通过退货单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新日志失败'];
            }

            $this->dbh->commit();
            return ['code'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'msg'=>'更新失败'];
        }
}
    //删除方法
       public function delReback($id,$input,$poundsout_sysno){
           $this->dbh->begin();
           try {
               $res = $this->dbh->update(DB_PREFIX . 'doc_stock_reback', $input, 'sysno=' . intval($id));
               if (!$res) {
                   $this->dbh->rollback();
                   return ['code' => 300, 'msg' => '删除主表失败'];
               }
               //将出库磅码单退货单状态及退货磅码单状态改为0
               $poundsoutData = array(
                   'issavereback'=>0,
                   'updated_at' => '=NOW()',
               );
               $poundsinfo = $this->getrebackpoundsInfoBystockinsysno($id);
               if($poundsinfo){
                   $poundsoutData['issavepoundsreback'] = 0;
               }
               $res = $this->dbh->update(DB_PREFIX.'doc_pounds_out', $poundsoutData, 'sysno=' . intval($poundsout_sysno));
               if (!$res) {
                   $this->dbh->rollback();
                   return ['code'=>300,'msg'=>'更新磅码单退货单状态失败'];
               }
               $this->dbh->commit();
               return ['code'=>200,'msg'=>$id];
           }catch (Exception $e) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除失败'];
           }
       }



    //获取主表信息
        public function getrebackInfoById($id)
        {
            if($id)
            {
                $sql = " SELECT * FROM `".DB_PREFIX."doc_stock_reback`  WHERE sysno = {$id}";
                return $this->dbh->select_row($sql);
            }

        }

    /*
     * 获取明细数据,编辑页dataurl
     */
    public function getRebackdetailById($params)
    {
        $filter = array();

        if (isset($params['stockinstatus']) && $params['stockinstatus'] != '') {
            $filter[] = " r.`stockinstatus`='{$params['stockinstatus']}'";
        }

        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " r.`sysno`='{$params['sysno']}'";
        }

        if (isset($params['detail_sysno']) && $params['detail_sysno'] != '') {
            $filter[] = " rd.`sysno`='{$params['detail_sysno']}'";
        }

        $where = 'r.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by r.created_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_reback` r
                    LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON rd.`stockreback_sysno`=r.`sysno` WHERE {$where}  ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT rd.*
                        FROM `".DB_PREFIX."doc_stock_reback` r
                        LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON (rd.`stockreback_sysno`=r.`sysno`)
                        WHERE {$where} ";

            if (isset($params['page']) && $params['page'] == false) {

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        //   echo $sql;die;
        return $result;
    }

    //作废方法
    public function cancellationReback($id,$input,$data){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_reback', $input, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新主表失败'];
            }
            $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            foreach ($data as $value) {
                $input = array(
                    'stockreback_sysno' => $value['sysno'],
                    'stockout_sysno' => $value['stockout_sysno'],
                    'stockoutno' => $value['stockoutno'],
                    'stockoutdetail_sysno' => $value['stockoutdetail_sysno'],
                    'stocktype' => $value['stocktype'],
                    'doc_sysno'=>$value['doc_sysno'],
                    'customer_sysno' => $value['customer_sysno'],
                    'customername' => $value['customername'],
                    'realnumber'=>$value['realnumber'],
                    'bucketnumber'=>$value['bucketnumber'],
                    'rebacknumber'=>$value['rebacknumber'],
                );
             
                if(!in_array($input['stocktype'],[1,2])){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'库存类型不存在'];
                }

                if(!$input['doc_sysno']){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'库存主键丢失'];
                }

                $rebacknumber = floatval($input['rebacknumber']/1000);
                if($input['stocktype']==1){

                    $params = [ 'type' => 16 ];

                    $searchdata = [
                        'outstockqty'=>$rebacknumber,
                        'sysno'=>$input['doc_sysno'],
                    ];
                    $params['data'] = $searchdata;

                    $result = $stock->pubstockoperation($params);
                    if ($result['code'] == 200) {
                        $res_stockid = $result['message'];
                    }else{
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>$result['message'].'更新库存失败'];
                    }
                }

                if($input['stocktype']==2){
                    //查询介绍信明细库存
                    $sql = "select * from ".DB_PREFIX."doc_introduction_detail ind
                            where ind.isdel = 0  and ind.status < 2 and ind.sysno = {$input['doc_sysno']} ";
                    $introduction = $this->dbh->select_row($sql);
                    if(!$introduction){
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>'获取介绍信库存失败'];
                    }

                    $updatedata = array(
                        'untakegoodsnum'=>floatval($introduction['untakegoodsnum'])-$rebacknumber,
                        'takegoodsqty'=>floatval($introduction['takegoodsqty'])+$rebacknumber,
                        'created_at' => '=NOW()',
                    );

                    $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail', $updatedata, 'sysno=' . intval($input['doc_sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>'更新介绍信库存失败'];
                    }
                }
                //更新出库明细实提数量
                $sql = "select * from ".DB_PREFIX."doc_stock_out_detail sd
                          where sd.isdel = 0  and sd.status < 2 and sd.sysno = {$input['stockoutdetail_sysno']} ";
                $stockoutDetail = $this->dbh->select_row($sql);
                if(!$stockoutDetail || empty($stockoutDetail)){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'获取出库明细失败'];
                }

                $stockoutDate = array(
                    'beqty'=>floatval($stockoutDetail['beqty']) + $rebacknumber,
                    'takeqty'=>floatval($stockoutDetail['takeqty']) - $rebacknumber,
                    'created_at' => '=NOW()',
                );

                $res = $this->dbh->update(DB_PREFIX.'doc_stock_out_detail', $stockoutDate, 'sysno=' . intval($input['stockoutdetail_sysno']));
                if(!$res){
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'更新出库明细实提数量失败'];
                }

            }

            #退货作废操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 31,
                'opertype' =>5,
                'opertype' =>'作废退货单',
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新日志失败'];
            }

            $this->dbh->commit();
            return ['code'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'msg'=>'更新失败'];
        }
    }
    //完成退货方法
    public function doneReback($id,$input){
        $res = $this->dbh->update(DB_PREFIX.'doc_stock_reback', $input, 'sysno=' . intval($id));
        if(!$res){
            return ['code'=>300,'msg'=>'完成退货失败'];
        }
        return ['code'=>200,'msg'=>'完成退货成功'];

    }

    //审核方法
    public function auditReback1($id,$data,$status,$auditreason){
        if($status==4){
            $data['stockinstatus'] =4;
            $data['auditreason'] = $auditreason;
        }elseif($status==6){
            $data['stockinstatus'] =6;
            $data['auditreason'] = $auditreason;
        }
        $res = $this->dbh->update(DB_PREFIX.'doc_stock_reback', $data, 'sysno=' . intval($id));
        if (!$res) {
            return ['code'=>300,'msg'=>'审核失败'];
        }
        return ['code'=>200,'msg'=>'审核成功'];

    }

    //作废方法
    public function cancellationReback1($id,$input){
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_reback', $input, 'sysno=' . intval($id));
            if (!$res) {
                return ['code'=>300,'msg'=>'更新主表失败'];
            }
            return ['code'=>200,'msg'=>'作废成功'];
    }
    //获取退货磅码单信息
    public function getrebackpoundsInfoBystockinsysno($sysno)
    {
        if($sysno) {
            $sql = " SELECT sysno FROM `".DB_PREFIX."doc_pounds_reback`  WHERE poundsinstatus in(2,3,4) and status=1 and isdel=0 and stockin_sysno = {$sysno}";
            return $this->dbh->select($sql)?$this->dbh->select($sql):[];
        }

    }
    //获取退货磅码单状态
    public function getStatusRebackpoundsById($sysno)
    {
        if($sysno) {
            $sql = " SELECT sysno FROM `".DB_PREFIX."doc_pounds_reback`  WHERE poundsinstatus=4 and status=1 and isdel=0 and stockin_sysno = {$sysno}";
            return $this->dbh->select_one($sql)?$this->dbh->select_one($sql):'';
        }

    }
    //获取退货订单状态
    public function getStatusRebackById($sysno)
    {
        if($sysno) {
            $sql = " SELECT sysno FROM `".DB_PREFIX."doc_stock_reback`  WHERE stockinstatus in(4,7,8) and status=1 and isdel=0 and sysno = {$sysno}";
            return $this->dbh->select($sql)?$this->dbh->select_one($sql):'';
        }

    }
}
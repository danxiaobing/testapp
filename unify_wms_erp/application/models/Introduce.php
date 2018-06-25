<?php
/**
 * Stockout Model
 *
 */

class IntroduceModel
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
     * @param   object  $dbh
     * @param   object  $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

    public function searchIntroduce($params) {
        $filter = array();
        if (isset($params['takegoodsno']) && $params['takegoodsno'] != '') {
            $filter[] = " i.`takegoodsno` LIKE '%{$params['takegoodsno']}%' ";
        }
        if (isset($params['introductiondate']) && $params['introductiondate'] != '') {
            $filter[] = " i.`introductiondate` = '{$params['introductiondate']}' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " i.`customer_sysno` = '{$params['customer_sysno']}'";
        }
        if (isset($params['buy_customer_sysno']) && $params['buy_customer_sysno'] != '') {
            $filter[] = " i.`buy_customer_sysno` = '{$params['buy_customer_sysno']}'";
        }
        if (isset($params['introductiontype']) && $params['introductiontype'] != '') {
            $filter[] = " i.`introductiontype`='{$params['introductiontype']}'";
        }
        if (isset($params['introductionstatus']) && $params['introductionstatus'] != '') {
            $filter[] = " i.`introductionstatus`='{$params['introductionstatus']}'";
        }

        $where ='i.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = $params;
        $result['list'] = array();

        $sql = "SELECT i.sysno,i.introductiondate,i.introductionno,i.introductiontype,i.customername,i.sale_customername,i.buy_customername,i.takegoodsno,i.receivestart,i.receiveend,i.freecostdate,i.introductionstatus,sum(takegoodsnum) as takegoodsnum,sum(untakegoodsnum) as untakegoodsnum,sum(takegoodsqty) as takegoodsqty,sum(bookingqty) as bookingqty,sum(outqty) as outqty ,group_concat(DISTINCT shipname Separator' ') as shipname FROM `".DB_PREFIX."doc_introduction` i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where {$where} group by id.introduction_sysno";

        if ($params['orders'] != '') {
            $sql .= " order by i." . $params['orders'];
        }
        $result['list'] = $this->dbh->select($sql);
        if (!isset($params['page']) || $params['page'] == false) {
            if(!empty($result['list']) && (isset($params['shipname']) && $params['shipname'] != '')){
                $tempArr = [];
                foreach ($result['list'] as $key => $value) {
                    $res = mb_strpos($value['shipname'],$params['shipname']);
                    $test = '---' . $res;
                    if($res || $res === 0){
                        $tempArr[] = $value;
                    }
                }
                $result['list'] = $tempArr;
            }
            $result['totalRow'] = count($result['list']);
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $datalist=array_chunk($result['list'],$params['pageSize'],false);
            $result['list']= $datalist[$params['pageCurrent']-1] ? $datalist[$params['pageCurrent']-1] : [];
        }
        return $result;
    }

     public function searchStockIn($params) {
        $filter = array();
        if (isset($params['stockinno']) && $params['stockinno'] != '') {
            $filter[] = " si.`stockinno` LIKE '%{$params['stockinno']}%' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " sid.`goods_sysno` = '{$params['goods_sysno']}' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " si.`customer_sysno` = '{$params['customer_sysno']}'";
        }
        if (isset($params['stockin_sysno']) && $params['stockin_sysno'] != '') {
            $filter[] = " si.`sysno` = '{$params['stockin_sysno']}'";
        }

        $where ='si.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = $params;
        $result['list'] = array();
        $sql = "SELECT id.stockin_sysno FROM `".DB_PREFIX."doc_introduction` i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where i.isdel = 0 and i.introductionstatus < 7 and stocktype = 1";
        $stockinSysnoArr = $this->dbh->select($sql);
        if(!$stockinSysnoArr){
            return $result;
        }
        $range = [];
        foreach ($stockinSysnoArr as $key => $value) {
            if(in_array($value['stockin_sysno'], $range)){
                continue;
            }
            $range[] = $value['stockin_sysno'];
        }

        $range = implode(',',$range);

        $sql = "SELECT COUNT(sysno)  FROM `".DB_PREFIX."doc_stock_in` where sysno in ('{$range}') ";
        $result['totalRow'] = $this->dbh->select_one($sql);

        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT si.sysno,si.stockinno,sid.shipname,bs.storagetankname,si.customername,si.stockindate,sid.goodsname,gq.qualityname,sid.goodsnature,sid.unitname,sum(beqty) as beqty ,i.takegoodsnum,i.untakegoodsnum,i.takegoodsqty,sid.shipname FROM `".DB_PREFIX."doc_stock_in` si
                    LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid on si.sysno = sid.stockin_sysno
                    LEFT JOIN ".DB_PREFIX."base_goods_quality gq on gq.sysno = sid.goods_quality_sysno
                    LEFT JOIN (SELECT id.stockin_sysno,sum(takegoodsnum) as takegoodsnum,sum(untakegoodsnum) as untakegoodsnum,sum(takegoodsqty) as takegoodsqty FROM `".DB_PREFIX."doc_introduction` i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where i.isdel = 0 and i.introductionstatus < 7 and stocktype =1 and id.stockin_sysno in({$range}) group by id.stockin_sysno) i on i.stockin_sysno = si.sysno
                    LEFT JOIN ".DB_PREFIX."base_storagetank bs on bs.sysno = sid.storagetank_sysno
                    where si.sysno in ({$range}) and {$where} group by sid.stockin_sysno";

                if ($params['orders'] != '')
                    $sql .= " order by si." . $params['orders'];

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT si.sysno,si.stockinno,si.customername,si.stockindate,sid.goodsname,gq.qualityname,sid.goodsnature,sid.unitname,sum(beqty) as beqty ,i.takegoodsnum,i.untakegoodsnum,i.takegoodsqty,sid.shipname FROM `".DB_PREFIX."doc_stock_in` si
                    LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid on si.sysno = sid.stockin_sysno
                    LEFT JOIN ".DB_PREFIX."base_goods_quality gq on gq.sysno = sid.goods_quality_sysno
                    LEFT JOIN (SELECT id.stockin_sysno,sum(takegoodsnum) as takegoodsnum,sum(untakegoodsnum) as untakegoodsnum,sum(takegoodsqty) as takegoodsqty FROM `".DB_PREFIX."doc_introduction` i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where i.isdel = 0 and i.introductionstatus < 7 and stocktype =1 and id.stockin_sysno in({$range}) group by id.stockin_sysno) i on i.stockin_sysno = si.sysno
                    where si.sysno in ({$range}) and {$where} group by sid.stockin_sysno";

                if ($params['orders'] != '') {
                    $sql .= " order by si." . $params['orders'];
                }

                $result['list'] = $this->dbh->select_page($sql);
            }
        }

        return $result;
    }

    //通过入库单号获取提单明细
    public function getIntroduceDetailData($stockin_sysno,$params){
        $filter = array();
        if (isset($params['stockin_sysno']) && $params['stockin_sysno'] != '') {
            $filter[] = " id.`stockin_sysno` = '{$params['stockin_sysno']}' ";
        }
        if (isset($params['takegoodsno']) && $params['takegoodsno'] != '') {
            $filter[] = " i.`takegoodsno` = '{$params['takegoodsno']}' ";
        }
        if (isset($params['introductiontype']) && $params['introductiontype'] != '') {
            $filter[] = " i.`introductiontype` = '{$params['introductiontype']}'";
        }
        if (isset($params['buy_customer_sysno']) && $params['buy_customer_sysno'] != '') {
            $filter[] = " i.`buy_customer_sysno` = '{$params['buy_customer_sysno']}'";
        }
        if (isset($params['sale_customer_sysno']) && $params['sale_customer_sysno'] != '') {
            $filter[] = " i.`sale_customer_sysno` = '{$params['sale_customer_sysno']}'";
        }

        $where ='i.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT i.introductiondate,i.introductiontype,i.sale_customername,i.buy_customername,i.takegoodsno,i.receivestart,i.receiveend,i.freecostdate,id.takegoodsnum,id.untakegoodsnum,id.takegoodsqty,id.outqty,id.ullage FROM `".DB_PREFIX."doc_introduction` i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where {$where} and id.stocktype = 1";

        $result = $this->dbh->select($sql);
        if(!$result){
            return [];
        }

        $key = count($result);
        $tempArr = ['introductiondate' => '合计','takegoodsqty' => 0,'untakegoodsnum' => 0];
        foreach ($result as $key => $value) {
            $tempArr['takegoodsqty'] += $value['takegoodsqty'];
            $tempArr['untakegoodsnum'] += $value['untakegoodsnum'];
        }
        $tempArr['takegoodsqty'] = sprintf("%.3f", $tempArr['takegoodsqty']);
        $tempArr['untakegoodsnum'] = sprintf("%.3f", $tempArr['untakegoodsnum']);
        $result[] = $tempArr;
        return $result;
    }

    public function getIntroduceDetailList($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_introduction_detail` where isdel = 0 AND  introduction_sysno = " .intval($id);
        return $this->dbh->select($sql);
    }

    public function getIntroduceById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_introduction` where isdel=0 and sysno = " . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function getIntroduceDetailById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_introduction_detail` where sysno = " .intval($id);
        return $this->dbh->select_row($sql);
    }

    public function getStockinById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_in` where isdel=0 and sysno = " . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addIntroduce($data,$detaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_introduction', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $id = $res;

            foreach ($detaildata as $value) {
                $input = array(
                    'introduction_sysno' => $id,
                    'introductiondetail_sysno' => $value['introductiondetail_sysno'],
                    'stock_sysno' => $value['stock_sysno'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'instockqty' => $value['instockqty'] == '--' ? $value['introduceqty'] : $value['instockqty'],
                    'shipname' => $value['shipname'],
                    'goodsname' => $value['goodsname'],
                    'goods_sysno' => $value['goods_sysno'],
                    'goodsqualityname' => $value['goodsqualityname'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'storagetankname' => $value['storagetankname'],
                    'unitname' => $value['unitname'],
                    'takegoodsnum' => $value['takegoodsnum'],
                    'untakegoodsnum' => $value['takegoodsnum'],
                    'takegoodsqty' => 0,
                    'goodsnature' => $value['goodsnature'],
                    'memo' => $value['memo'],
                    'introductiondetailstatus' => $data['introductionstatus'],
                    'stocktype' => $value['stocktype'],
                    'firstdate' => $value['firstdate'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_introduction_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $B = new BookoutModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

            if ($data['introductionstatus'] == 3) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '提单(介绍信)'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%introduce%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $B->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'提单待审核',
                            'content'=>$data['sale_customername']."的提单".$data['introductionno']."待审核",
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'auditlist',
                            'control'=>'introduce',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
            }

            #库存管理业务操作日志
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  29,
                'opertype'  => 0 ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['introductionstatus'] == 3) {
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateIntroduce($id,$data,$detaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_introduction', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_introduction_detail', 'introduction_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($detaildata as $value) {
                $input = array(
                    'introduction_sysno' => $id,
                    'introductiondetail_sysno' => $value['introductiondetail_sysno'],
                    'stock_sysno' => $value['stock_sysno'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'instockqty' => $value['instockqty'] == '--' ? $value['introduceqty'] : $value['instockqty'],
                    'shipname' => $value['shipname'],
                    'goodsname' => $value['goodsname'],
                    'goods_sysno' => $value['goods_sysno'],
                    'goodsqualityname' => $value['goodsqualityname'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'storagetankname' => $value['storagetankname'],
                    'unitname' => $value['unitname'],
                    'takegoodsnum' => $value['takegoodsnum'],
                    'untakegoodsnum' => $value['takegoodsnum'],
                    'takegoodsqty' => 0,
                    'goodsnature' => $value['goodsnature'],
                    'memo' => $value['memo'],
                    'introductiondetailstatus' => $data['introductionstatus'],
                    'stocktype' => $value['stocktype'],
                    'firstdate' => $value['firstdate'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_introduction_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  29,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );
            if ($data['introductionstatus'] == 3) {
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }



    public function updateIntroduceData($id)
    {
        $this->dbh->begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_introduction', array('introductionstatus' => 6), 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail', array('introductiondetailstatus' => 6), 'introduction_sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            $msg = '数据库操作异常';
            return false;
        }

    }

     public function delIntroduceData($id)
    {
        $this->dbh->begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_introduction', array('isdel' => 1), 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail', array('isdel' => 1), 'introduction_sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            $msg = '数据库操作异常';
            return false;
        }

    }

    public function examIntroduce($id,&$msg = null){
        $this->dbh->begin();
        try{
            $sql = 'select * from `'.DB_PREFIX.'doc_introduction` where sysno=' . intval($id) ." for update";
            $data = $this->dbh->select_row($sql);

            if (!$data) {
                $msg = '数据为空';
                $this->dbh->rollback();
                return false;
            }

            if($data['introductionstatus'] != '3'){
                $msg = '提单不是待审核状态，不能审核';
                $this->dbh->rollback();
                return false;
            }

            $param = array(
                'introductionstatus' => 4
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_introduction', $param, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                $msg = '数据更新出错';
                return false;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail', array('introductiondetailstatus' => 4), 'isdel=0 and introduction_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                $msg = '数据更新出错';
                return false;
            }

            $sql = 'select * from `'.DB_PREFIX.'doc_introduction_detail` where introduction_sysno=' . intval($id);
            $detaildata = $this->dbh->select($sql);

            if (!$detaildata) {
                $this->dbh->rollback();
                $msg = '列表详情为空';
                return false;
            }
            $SG = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $Log = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            /*
            *顶级提单  更新库存可用量stockqty和介绍信数量introductionqty
            *次级提单  更新父级拆单量outqty
            */
            if($data['father_introduction_sysno'] != 0){
                //次级提单
                foreach ($detaildata as $key => $value) {
                    $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where sysno = " .intval($value['introductiondetail_sysno']);
                    $fInfo = $this->dbh->select_row($sql);
                    $update = array(
                        'outqty' => $fInfo['outqty'] + $value['takegoodsnum'],
                        'untakegoodsnum' => $fInfo['untakegoodsnum'] - $value['takegoodsnum'],
                        );
                    if($update['untakegoodsnum'] < 0){
                        $this->dbh->rollback();
                        $msg = '上级结存量不足';
                        return false;
                    }
                    if($update['untakegoodsnum'] == 0){
                        $update['introductiondetailstatus'] = 5;
                    }

                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",$update,'sysno = ' . intval($value['introductiondetail_sysno']));

                    if (!$res) {
                        $this->dbh->rollback();
                        $msg = '数据更新出错';
                        return false;
                    }

                    $sql = "SELECT sum(untakegoodsnum) as untakegoodsnum ,i.introductionstatus from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where i.sysno = " .intval($fInfo['introduction_sysno']);
                    $fintroduceInfo = $this->dbh->select_row($sql);
                    if($fintroduceInfo['untakegoodsnum'] == 0 && $fintroduceInfo['introductionstatus'] == 4){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 5),'sysno = ' . intval($fInfo['introduction_sysno']));

                        if (!$res) {
                            $this->dbh->rollback();
                            $msg = '数据更新出错';
                            return false;
                        }
                    }
                    if($data['introductiontype'] == 2){
                        //货物进出记录
                        $recordlog = array(
                            'shipname'          => $value['shipname'],
                            'goods_sysno'       => $value['goods_sysno'],
                            'goodsname'         => $value['goodsname'],
                            'storagetank_sysno' => $value['storagetank_sysno'],
                            'storagetankname'   => $value['storagetankname'],
                            'customer_sysno'    => $data['sale_customer_sysno'],
                            'customername'      => $data['sale_customername'],
                            'beqty'             => -$value['takegoodsnum'],
                            'tobeqty'           => $value['takegoodsnum'],
                            'stockin_sysno'     => $value['stockin_sysno'],
                            'stockinno'         => $value['stockin_no'],
                            'doc_sysno'         => $id,
                            'docno'             => $data['introductionno'],
                            'accountstoragetank_sysno' => $value['storagetank_sysno'],
                            'accountstoragetankname'   => $value['storagetankname'],
                            'doc_type'          => 14,       //提单出
                            'stock_sysno'       => $value['stock_sysno'],
                            'goodsnature'       => $value['goodsnature'],
                            'takegoodscompany'  => $data['buy_customername'],
                            'takegoodsno'       => $data['takegoodsno'],
                            'stocktype'         => 2,
                            'introduction_sysno' => $id,
                            'introductionno'    => $data['introductionno'],
                            );

                        $res = $Log->addGoodsRecordLog($recordlog);

                        if(!$res){
                            $msg = '插入货物进出日志失败';
                            $this->dbh->rollback();
                            return false;
                        }

                        $recordlog['beqty'] = $value['takegoodsnum'];
                        $recordlog['doc_type'] = 13;        //提单入
                        $recordlog['customer_sysno'] = $data['buy_customer_sysno'];
                        $recordlog['customername'] = $data['buy_customername'];

                        $res = $Log->addGoodsRecordLog($recordlog);
                        if(!$res){
                            $msg = '插入货物进出日志失败';
                            $this->dbh->rollback();
                            return false;
                        }
                    }
                }
            }else{
                //顶级提单
                $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                foreach ($detaildata as $key => $value) {
                    $sql = "SELECT stockqty,introductionqty from `".DB_PREFIX."storage_stock` where isdel=0 and status=1 and iscurrent=1 and sysno = ".intval($value['stock_sysno']);
                    $stockInfo = $this->dbh->select_row($sql);
                    if (!$stockInfo) {
                        $this->dbh->rollback();
                        $msg = '库存数据错误';
                        return false;
                    }
                    $update['type'] = 15;
                    $update['data'] = array(
                        'sysno' => $value['stock_sysno'],
                        'introductionqty' => $value['takegoodsnum'],
                        );
                    $res = $S->pubstockoperation($update);

                    if ($res['code'] != 200) {
                        $this->dbh->rollback();
                        $msg = $res['msg'];
                        return false;
                    }

                    if($data['introductiontype'] == 2){
                        //货物进出记录
                        $recordlog = array(
                            'shipname'          => $value['shipname'],
                            'goods_sysno'       => $value['goods_sysno'],
                            'goodsname'         => $value['goodsname'],
                            'storagetank_sysno' => $value['storagetank_sysno'],
                            'storagetankname'   => $value['storagetankname'],
                            'customer_sysno'    => $data['customer_sysno'],
                            'customername'      => $data['customername'],
                            'beqty'             => -$value['takegoodsnum'],
                            'tobeqty'           => $value['takegoodsnum'],
                            'stockin_sysno'     => $value['stockin_sysno'],
                            'stockinno'         => $value['stockin_no'],
                            'doc_sysno'         => $id,
                            'docno'             => $data['introductionno'],
                            'accountstoragetank_sysno' => $value['storagetank_sysno'],
                            'accountstoragetankname'   => $value['storagetankname'],
                            'doc_type'          => 14,       //提单出
                            'stock_sysno'       => $value['stock_sysno'],
                            'goodsnature'       => $value['goodsnature'],
                            'takegoodscompany'  => $data['buy_customername'],
                            'takegoodsno'       => $data['takegoodsno'],
                            'stocktype'         => 1,
                            'introduction_sysno' => $id,
                            'introductionno'    => $data['introductionno'],
                            );

                        $res = $Log->addGoodsRecordLog($recordlog);

                        if(!$res){
                            $msg = '插入货物进出日志失败';
                            $this->dbh->rollback();
                            return false;
                        }

                        $recordlog['beqty'] = $value['takegoodsnum'];
                        $recordlog['doc_type'] = 13;        //提单入
                        $recordlog['customer_sysno'] = $data['buy_customer_sysno'];
                        $recordlog['customername'] = $data['buy_customername'];

                        $res = $Log->addGoodsRecordLog($recordlog);
                        if(!$res){
                            $msg = '插入货物进出日志失败';
                            $this->dbh->rollback();
                            return false;
                        }
                    }

                    //更新储罐待出量
                    $updatesgres = $SG ->pubstoragetankoperation(
                        array(
                            'type' => 10,
                            'data' => array(
                                'sysno' => $value['storagetank_sysno'],
                                'orderoutqty' => $value['takegoodsnum'],
                            ),
                         ));

                    if($updatesgres['code'] != '200'){
                        $this->dbh->rollback();
                        $msg = '更新罐容失败';
                        return false;
                    }
                }
            }

            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            $msg = '数据库操作异常';
            return false;
        }
    }

    public function updateIntroduceInfo($id,$data)
    {
        $this->dbh->begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_introduction', $data, 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return array('code' => 200,'msg' => '更新成功');
        }catch (Exception $e) {
            $this->dbh->rollback();
            return array('code' => 300,'msg' => '数据库操作异常');
        }

    }
    //提单作废
    public function cancelIntroduce($id)
    {
        $this->dbh->begin();
        try{
            //判断有没有子级
            $sql = "SELECT sysno from ".DB_PREFIX."doc_introduction where introductionstatus not in (7,9) and father_introduction_sysno = " . intval($id);
            $sonData = $this->dbh->select($sql);
            if($sonData){
                return array('code' => 300,'msg' => '存在向下货转单据，不能作废');
            }
            //查询明细
            $sql = "SELECT id.* from `".DB_PREFIX."doc_introduction_detail` id left join `".DB_PREFIX."doc_introduction` i on id.introduction_sysno = i.sysno where i.isdel = 0 and i.sysno = " . intval($id);
            $detailData = $this->dbh->select($sql);
            if(!$detailData){
                return array('code' => 300,'msg' => '提单信息错误');
            }
            foreach ($detailData as $key => $value) {
                //判断是否扣过损耗
                if($value['ullage'] != 0){
                    return array('code' => 300,'msg' => '已经扣过损耗，不能作废');
                }
                //判断有没有预约单
                $sql = "SELECT * from `".DB_PREFIX."doc_booking_out` bo left join `".DB_PREFIX."doc_booking_out_detail` bod on bo.sysno=bod.bookingout_sysno where bo.isdel = 0 and bod.stocktype=2 and bod.stockin_sysno = ".intval($value['sysno']);
                $res = $this->dbh->select($sql);
                if($res && count($res)>0){
                    return array('code' => 300,'msg' => '存在出库预约单，不能作废');
                }
            }

            $SG = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $Log = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $introductionInfo = $this->getIntroduceById($id);
            //插入record记录
            foreach ($detailData as $key => $value) {
                if($introductionInfo['introductiontype'] == 2){
                    //货物进出记录
                    $recordlog = array(
                        'shipname'          => $value['shipname'],
                        'goods_sysno'       => $value['goods_sysno'],
                        'goodsname'         => $value['goodsname'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'storagetankname'   => $value['storagetankname'],
                        'customer_sysno'    => $introductionInfo['buy_customer_sysno'],
                        'customername'      => $introductionInfo['buy_customername'],
                        'beqty'             => -$value['takegoodsnum'],
                        'tobeqty'           => $value['takegoodsnum'],
                        'stockin_sysno'     => $value['stockin_sysno'],
                        'stockinno'         => $value['stockin_no'],
                        'doc_sysno'         => $id,
                        'docno'             => $introductionInfo['introductionno'],
                        'accountstoragetank_sysno' => $value['storagetank_sysno'],
                        'accountstoragetankname'   => $value['storagetankname'],
                        'doc_type'          => 23,       //提单作废出
                        'stock_sysno'       => $value['stock_sysno'],
                        'goodsnature'       => $value['goodsnature'],
                        'takegoodscompany'  => $introductionInfo['buy_customername'],
                        'takegoodsno'       => $introductionInfo['takegoodsno'],
                        'introduction_sysno' => $id,
                        'introductionno'    => $introductionInfo['introductionno'],
                        'stocktype'         => 2,
                        );

                    $res = $Log->addGoodsRecordLog($recordlog);

                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '插入货物进出日志失败');
                    }

                    //父级入
                    $recordlog['beqty'] = $value['takegoodsnum'];
                    $recordlog['doc_type'] = 24;        //提单撤销入
                    $recordlog['customer_sysno'] = $introductionInfo['sale_customer_sysno'];
                    $recordlog['customername'] = $introductionInfo['sale_customername'];
                    $res = $Log->addGoodsRecordLog($recordlog);

                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '插入货物进出日志失败');
                    }

                }
            }
            foreach ($detailData as $key => $value) {
                if($value['introductiondetail_sysno'] != 0){
                    $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where sysno = " .intval($value['introductiondetail_sysno']);
                    $introduceDetailInfo = $this->dbh->select_row($sql);
                    $update = array(
                        'untakegoodsnum' => $introduceDetailInfo['untakegoodsnum'] + $value['takegoodsnum'],
                        'outqty' => $introduceDetailInfo['outqty'] - $value['takegoodsnum'],
                        );
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",$update,'sysno='.intval($value['introductiondetail_sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '更新父级提单失败');
                    }

                    //更新父级明细状态
                    if($update['untakegoodsnum'] != 0){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' =>4),'introduction_sysno = ' . intval($introduceDetailInfo['sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return array('code' => 300,'msg' => '数据更新出错');
                        }
                    }
                    //更新父级主表状态
                    $sql = "SELECT * from `".DB_PREFIX."doc_introduction` where sysno = " .intval($introduceDetailInfo['introduction_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);
                    if($update['untakegoodsnum'] != 0 && $introduceInfo['introductionstatus'] == 5){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' =>4),'sysno = ' . intval($introduceDetailInfo['introduction_sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return array('code' => 300,'msg' => '数据更新出错');
                        }
                    }
                }else{
                    //更新储罐待出量
                    if($value['takegoodsnum']>0){
                        $updatesgres = $SG ->pubstoragetankoperation(
                            array(
                                'type' => 9,
                                'data' => array(
                                    'sysno' => $value['storagetank_sysno'],
                                    'orderoutqty' => $value['takegoodsnum'],
                                ),
                             ));

                        if($updatesgres['code'] != '200'){
                            $this->dbh->rollback();
                            return array('code' => 300,'msg' => '更新储罐带储量失败失败');
                        }
                    }

                    $sql = "SELECT stockqty,introductionqty from `".DB_PREFIX."storage_stock` where isdel =0 and status = 1 and iscurrent = 1 and sysno = ".intval($value['stock_sysno']);
                    $stockInfo = $this->dbh->select_row($sql);
                    if(!$stockInfo){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '库存信息错误');
                    }
                    $update = array(
                        'stockqty' => $stockInfo['stockqty'] + $value['takegoodsnum'],
                        'introductionqty' => $stockInfo['introductionqty'] - $value['takegoodsnum'],
                        );
                    $res = $this->dbh->update(DB_PREFIX."storage_stock",$update,'sysno='.intval($value['stock_sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '更新库存失败');
                    }


                }

                //更新自己明细状态为已作废
                $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' => 9),'sysno='.intval($value['sysno']));
                if(!$res){
                    $this->dbh->rollback();
                    return array('code' => 300,'msg' => '更新提单状态失败');
                }
            }

            $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 9),'sysno='.intval($id));
            if(!$res){
                $this->dbh->rollback();
                return array('code' => 300,'msg' => '更新提单状态失败');
            }
            $this->dbh->commit();
            return array('code' => 200,'msg' => '作废成功');
        }catch (Exception $e) {
            $this->dbh->rollback();
            return array('code' => 300,'msg' => '数据库操作异常');
        }
    }
    //提单撤销
    public function stopIntroduce($id)
    {
        $this->dbh->begin();
        try{
            $sql = "SELECT id.* from `".DB_PREFIX."doc_introduction_detail` id left join `".DB_PREFIX."doc_introduction` i on id.introduction_sysno = i.sysno where i.isdel = 0 and i.sysno = " . intval($id);
            $detailData = $this->dbh->select($sql);

            if(!$detailData){
                return array('code' => 300,'msg' => '提单信息错误');
            }
            $SG = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

            //插入撤销记录
            $res = $this->insertRecord($detailData,$id);
            if($res['code'] == 300){
                return $res;
            }
            $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where introductiondetailstatus in (4,5) and isdel = 0";
            $data = $this->dbh->select($sql);

            foreach ($detailData as $key => $value) {
                $son_tree = $this->getAllIntroduceDetail($value['sysno'],$data);
                $result = [];
                if($son_tree){
                    //获取子级结存量、实提量
                    $result = $this->getSonUntakegoodsnum($son_tree);
                }

                //获取下一级提单量
                $sql = "SELECT sum(takegoodsnum) as takegoodsnum from `".DB_PREFIX."doc_introduction_detail` where introductiondetailstatus in(4,5) and isdel=0 and introductiondetail_sysno = " .intval($value['sysno']);
                $takegoodsnum = $this->dbh->select_one($sql);

                //加上自己数据回写父级数据
                $untakegoodsnum = isset($result['untakegoodsnum']) ? $result['untakegoodsnum'] + $value['untakegoodsnum'] : $value['untakegoodsnum'];
                $takegoodsqty = isset($result['takegoodsqty']) ? $result['takegoodsqty'] + $value['takegoodsqty'] : $value['takegoodsqty'];
                $outqty = $value['outqty'] - $takegoodsnum;

                if($value['introductiondetail_sysno'] != 0){
                    $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where sysno = " .intval($value['introductiondetail_sysno']);
                    $introduceDetailInfo = $this->dbh->select_row($sql);

                    $update = array(
                        'untakegoodsnum' => $introduceDetailInfo['untakegoodsnum'] + $untakegoodsnum,
                        'outqty' => $introduceDetailInfo['outqty'] - $value['takegoodsnum'],
                        'takegoodsqty' => $introduceDetailInfo['takegoodsqty'] + $takegoodsqty,
                        );
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",$update,'sysno='.intval($value['introductiondetail_sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '更新父级结存量失败');
                    }

                    //更新父级明细状态
                    if($update['untakegoodsnum'] != 0 && $introduceDetailInfo['introductiondetailstatus'] == 5){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' =>4),'sysno = ' . intval($introduceDetailInfo['sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return array('code' => 300,'msg' => '数据更新出错');
                        }
                    }
                    //更新父级主表状态
                    $sql = "SELECT * from `".DB_PREFIX."doc_introduction` where sysno = " .intval($introduceDetailInfo['introduction_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);
                    if($update['untakegoodsnum'] != 0 && $introduceInfo['introductionstatus'] == 5){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' =>4),'sysno = ' . intval($introduceDetailInfo['introduction_sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return array('code' => 300,'msg' => '数据更新出错');
                        }
                    }

                }else{
                    //更新储罐待出量
                    if($untakegoodsnum>0){
                        $updatesgres = $SG ->pubstoragetankoperation(
                            array(
                                'type' => 9,
                                'data' => array(
                                    'sysno' => $value['storagetank_sysno'],
                                    'orderoutqty' => $untakegoodsnum,
                                ),
                             ));

                        if($updatesgres['code'] != '200'){
                            $this->dbh->rollback();
                            return array('code' => 300,'msg' => '更新罐容失败');
                        }
                    }

                    $sql = "SELECT stockqty,introductionqty from `".DB_PREFIX."storage_stock` where isdel =0 and status = 1 and iscurrent = 1 and sysno = ".intval($value['stock_sysno']);
                    $stockInfo = $this->dbh->select_row($sql);
                    if(!$stockInfo){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '库存信息错误');
                    }
                    $update = array(
                        'stockqty' => $stockInfo['stockqty'] + $untakegoodsnum,
                        'introductionqty' => $stockInfo['introductionqty'] - $untakegoodsnum,
                        );
                    $res = $this->dbh->update(DB_PREFIX."storage_stock",$update,'sysno='.intval($value['stock_sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '更新库存失败');
                    }
                }
                //删除相关单据
                $son_tree[] = $value;
                $res = $this->delBookingData($son_tree);
                if($res['code'] == 300){
                    $this->dbh->rollback();
                    return $res;
                }
                //更新自己明细状态为已撤销
                $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' => 7,'untakegoodsnum' => 0,'outqty' => 0,'bookingqty' => 0),'sysno='.intval($value['sysno']));
                if(!$res){
                    $this->dbh->rollback();
                    return array('code' => 300,'msg' => '更新状态失败');
                }
            }
            $sql = "SELECT * from `".DB_PREFIX."doc_introduction` where isdel = 0 and introductionstatus not in (7,9)";
            $data = $this->dbh->select($sql);
            if($data){
                $tree = $this->getAllIntroduce($id,$data);
                if(!empty($tree)){
                    //更新子级状态为已撤销
                    $res = $this->changeStatus($tree);
                    if($res['code'] == 300){
                        $this->dbh->rollback();
                        return $res;
                    }
                }
            }

            $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 7),'sysno='.intval($id));
            if(!$res){
                $this->dbh->rollback();
                return array('code' => 300,'msg' => '更新状态失败');
            }
            $this->dbh->commit();
            return array('code' => 200,'msg' => '撤销成功');
        }catch (Exception $e) {
            $this->dbh->rollback();
            return array('code' => 300,'msg' => '数据库操作异常');
        }
    }
    //插入撤销记录
    public function insertRecord($detailData,$id)
    {
        $Log = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where introductiondetailstatus in(4,5) and isdel = 0";
        $data = $this->dbh->select($sql); //用来判断子级有没有出库中的订单

        foreach ($detailData as $key => $value) {
            //判断自己本身有没有出库中的订单
            $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` so left join `".DB_PREFIX."doc_stock_out_detail` sod on so.sysno=sod.stockout_sysno where so.isdel = 0 and so.stockoutstatus =3 and so.stockouttype=2 and sod.stocktype=2 and sod.stockin_sysno = ".intval($value['sysno']);
            $res = $this->dbh->select($sql);
            if($res && count($res)>0){
                return array('code' => 300,'msg' => '存在出库中的订单不允许撤销');
            }
            //处理自己相关的预约单和订单
            $res = $this->releaseClockqty($value['sysno']);
            if($res['code'] == 300){
                $this->dbh->rollback();
                return $res;
            }
            //判断子级有没有出库中的订单
            $sonTree = $this->getAllIntroduceDetail($value['sysno'],$data);

            if(!empty($sonTree)){
                $res = $this->isExistStockout($sonTree);
                if ($res['code'] == 300) {
                    $this->dbh->rollback();
                    return $res;
                }
            }
            if (!empty($sonTree)) {
                $res = $this->insertGoodsRecords($sonTree,$Log);
                if ($res['code'] == 300) {
                    $this->dbh->rollback();
                    return $res;
                }
            }

            //获取子级和自己总的结存量
            $result = $this->getSonUntakegoodsnum($sonTree);
            $untakegoodsnum = $value['untakegoodsnum'] + $result['untakegoodsnum'];
            $introductionInfo = $this->getIntroduceById($id);

            if($introductionInfo['introductiontype'] == 2){
                //货物进出记录
                $recordlog = array(
                    'shipname'          => $value['shipname'],
                    'goods_sysno'       => $value['goods_sysno'],
                    'goodsname'         => $value['goodsname'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'storagetankname'   => $value['storagetankname'],
                    'customer_sysno'    => $introductionInfo['buy_customer_sysno'],
                    'customername'      => $introductionInfo['buy_customername'],
                    'beqty'             => -$untakegoodsnum,
                    'tobeqty'           => $untakegoodsnum,
                    'stockin_sysno'     => $value['stockin_sysno'],
                    'stockinno'         => $value['stockin_no'],
                    'doc_sysno'         => $id,
                    'docno'             => $introductionInfo['introductionno'],
                    'accountstoragetank_sysno' => $value['storagetank_sysno'],
                    'accountstoragetankname'   => $value['storagetankname'],
                    'doc_type'          => 17,       //提单撤销出
                    'stock_sysno'       => $value['stock_sysno'],
                    'goodsnature'       => $value['goodsnature'],
                    'takegoodscompany'  => $introductionInfo['buy_customername'],
                    'takegoodsno'       => $introductionInfo['takegoodsno'],
                    'introduction_sysno' => $id,
                    'introductionno'    => $introductionInfo['introductionno'],
                    'stocktype'         => 2,
                    );

                $res = $Log->addGoodsRecordLog($recordlog);

                if(!$res){
                    $this->dbh->rollback();
                    return array('code' => 300,'msg' => '插入货物进出日志失败');
                }

                //父级入
                $recordlog['beqty'] = $untakegoodsnum;
                $recordlog['doc_type'] = 16;        //提单撤销入
                $recordlog['customer_sysno'] = $introductionInfo['sale_customer_sysno'];
                $recordlog['customername'] = $introductionInfo['sale_customername'];
                $res = $Log->addGoodsRecordLog($recordlog);

                if(!$res){
                    $this->dbh->rollback();
                    return array('code' => 300,'msg' => '插入货物进出日志失败');
                }
            }
        }
    }
    //判断提单有没有已审核,已完成的预约单（如果预约单是已完成状态，订单不能是已审核状态）,如果有，则退回库存锁货量，提单预约出货量
    public function releaseClockqty($sysno)
    {
        $sql = "SELECT * from `".DB_PREFIX."doc_booking_out` bo left join `".DB_PREFIX."doc_booking_out_detail` bod on bo.sysno=bod.bookingout_sysno where bo.isdel = 0 and bo.bookingoutstatus in(5,6) and bod.stocktype=2 and bod.stock_sysno = ".intval($sysno);
        $res = $this->dbh->select($sql);

        if($res && count($res)>0){
            $SG = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $S = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $sysnoArr = [];
            foreach ($res as $key => $value) {
                if(in_array($value['bookingout_sysno'],$sysnoArr)){
                    continue;
                }
                $sysnoArr[] = $value['bookingout_sysno'];
            }

            foreach ($sysnoArr as $key => $value) {
                $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` where isdel = 0 and stockoutstatus =4 and booking_out_sysno = ".intval($value);
                $stockoutInfo = $this->dbh->select_row($sql);
                if($stockoutInfo){
                    continue;
                }
                $sql = "SELECT stocktype,stock_sysno,bookingoutqty,storagetank_sysno from `".DB_PREFIX."doc_booking_out` bo left join `".DB_PREFIX."doc_booking_out_detail` bod on bo.sysno=bod.bookingout_sysno where bo.isdel = 0 and bo.bookingoutstatus =5 and bod.stocktype=2 and bo.sysno = ".intval($value);
                $bookoutData = $this->dbh->select($sql);
                if($bookoutData){
                    foreach ($bookoutData as $bvalue) {
                        //更新库存锁货量
                        if($bvalue['stocktype'] == 1){
                            //更新储罐待出量
                            $updatesgres = $SG ->pubstoragetankoperation(
                                array(
                                    'type' => 9,
                                    'data' => array(
                                        'sysno' => $bvalue['storagetank_sysno'],
                                        'orderoutqty' => $bvalue['bookingoutqty'],
                                    ),
                                 ));

                            if($updatesgres['code'] != '200'){
                                return array('code'=>300,'msg'=>'更新罐容失败');
                            }
                            $updatestock = $S->pubstockoperation(
                                array(
                                    'type' => 13,
                                    'data' => array(
                                        'sysno' => $bvalue['stock_sysno'],
                                        'clockqty' => $bvalue['bookingoutqty'],
                                    ),
                                ));

                            if($updatestock['code'] != '200'){
                                return array('code'=>300,'msg'=>'更新库容失败');
                            }
                        }elseif ($bvalue['stocktype'] == 2) {
                            $sql = "SELECT bookingqty from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($bvalue['stock_sysno']);
                            $introduceInfo = $this->dbh->select_row($sql);

                            $bookingqty = $introduceInfo['bookingqty'] - $bvalue['bookingoutqty'];
                            $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('bookingqty' => $bookingqty),'sysno='.intval($bvalue['stock_sysno']));
                            if(!$res){
                                return array('code'=>300,'msg'=>'更新介绍信预约量失败');
                            }
                        }
                    }
                }
            }
        }
    }

    //提单撤销后，删除未完成的订单
    public function delBookingData($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->delBookingData($value);
                $sql = "SELECT bo.sysno from `".DB_PREFIX."doc_booking_out` bo left join `".DB_PREFIX."doc_booking_out_detail` bod on bo.sysno=bod.bookingout_sysno where bo.isdel = 0 and bod.stocktype=2 and bod.stock_sysno = ".intval($value['sysno']);
                $bookoutData = $this->dbh->select($sql);

                if($bookoutData && count($bookoutData) > 0){
                    foreach ($bookoutData as $v) {
                        $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` where isdel = 0 and (stockoutstatus =4 or (stockoutstatus = 3 and stockouttype=2)) and booking_out_sysno = ".intval($v['sysno']);

                        $stockoutInfo = $this->dbh->select_row($sql);
                        if($stockoutInfo){
                            continue;
                        }
                        $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` where isdel = 0 and booking_out_sysno = ".intval($v['sysno']);
                        $stockoutInfo = $this->dbh->select_row($sql);
                        if($stockoutInfo){
                            $res = $this->dbh->update(DB_PREFIX."doc_stock_out",array('isdel' => 1),'booking_out_sysno='.intval($v['sysno']));
                            if(!$res){
                                return array('code'=>300,'msg'=>'删除预约单失败');
                            }
                        }

                        $res = $this->dbh->update(DB_PREFIX."doc_booking_out",array('isdel' => 1),'sysno='.intval($v['sysno']));
                        if(!$res){
                            return array('code'=>300,'msg'=>'删除订单失败');
                        }

                    }
                }
            }
        }
    }
    //获取子级所有提单明细
    public function getAllIntroduceDetail($sysno,$data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if($value['introductiondetail_sysno'] == $sysno){
                $value['children'] = $this->getAllIntroduceDetail($value['sysno'],$data);
                if($value['children'] == null){
                    unset($value['children']);
                }
                $result[] = $value;
            }
        }
        return $result;
    }
    //获取子级所有提单
    public function getAllIntroduce($sysno,$data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if($value['father_introduction_sysno'] == $sysno){
                $value['children'] = $this->getAllIntroduce($value['sysno'],$data);
                if($value['children'] == null){
                    unset($value['children']);
                }
                $result[] = $value;
            }
        }
        return $result;
    }
    //判断是否存在出库中的订单
    public function isExistStockout($data)
    {
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $this->isExistStockout($value);
                if (isset($value['sysno'])) {
                    $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` so left join `".DB_PREFIX."doc_stock_out_detail` sod on so.sysno=sod.stockout_sysno where so.isdel = 0 and so.stockoutstatus =3 and so.stockouttype=2 and sod.stocktype=2 and sod.stockin_sysno = ".intval($value['sysno']);
                    $res = $this->dbh->select($sql);
                    if($res && count($res)>0){
                        return array('code' => 300,'msg' => '存在出库中的订单不允许撤销');
                    }
                    //处理子级相关的预约单和订单
                    $res = $this->releaseClockqty($value['sysno']);
                    if($res['code'] == 300){
                        $this->dbh->rollback();
                        return $res;
                    }
                }

            }
        }
    }
    //插入记录
    public function insertGoodsRecords($sonTree,$Log)
    {
        $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where introductiondetailstatus in(4,5) and isdel = 0";
        $data = $this->dbh->select($sql);

        foreach ($sonTree as $key => $value) {
            if (is_array($value)) {
                $this->insertGoodsRecords($value,$Log);
                $sql = "SELECT i.sysno,customer_sysno,customername,buy_customer_sysno,buy_customername,sale_customer_sysno,sale_customername,introductionno,takegoodsno,introductiontype from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` did on i.sysno = did.introduction_sysno where did.sysno = ".intval($value['sysno']);

                $introductionInfo = $this->dbh->select_row($sql);
                $tree = $this->getAllIntroduceDetail($value['sysno'],$data);
                $untakegoodsnum = $value['untakegoodsnum'] + $result['untakegoodsnum'];
                if(!empty($tree)){
                    //获取子级和自己的结存量之和
                    $result = $this->getSonUntakegoodsnum($tree);
                    $untakegoodsnum += $result['untakegoodsnum'];
                }

                if($introductionInfo['introductiontype'] == 2){
                    //货物进出记录
                    $recordlog = array(
                        'shipname'          => $value['shipname'],
                        'goods_sysno'       => $value['goods_sysno'],
                        'goodsname'         => $value['goodsname'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'storagetankname'   => $value['storagetankname'],
                        'customer_sysno'    => $introductionInfo['buy_customer_sysno'],
                        'customername'      => $introductionInfo['buy_customername'],
                        'beqty'             => -$untakegoodsnum,
                        'tobeqty'           => $untakegoodsnum,
                        'stockin_sysno'     => $value['stockin_sysno'],
                        'stockinno'         => $value['stockin_no'],
                        'doc_sysno'         => $introductionInfo['sysno'],
                        'docno'             => $introductionInfo['introductionno'],
                        'accountstoragetank_sysno' => $value['storagetank_sysno'],
                        'accountstoragetankname'   => $value['storagetankname'],
                        'doc_type'          => 17,       //提单撤销出
                        'stock_sysno'       => $value['stock_sysno'],
                        'goodsnature'       => $value['goodsnature'],
                        'takegoodscompany'  => $introductionInfo['buy_customername'],
                        'takegoodsno'       => $introductionInfo['takegoodsno'],
                        'stocktype'         => 2,
                        );

                    $res = $Log->addGoodsRecordLog($recordlog);

                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '插入货物进出日志失败');
                    }

                    //父级入
                    $recordlog['beqty'] = $untakegoodsnum;
                    $recordlog['doc_type'] = 16;        //提单撤销入
                    $recordlog['customer_sysno'] = $introductionInfo['sale_customer_sysno'];
                    $recordlog['customername'] = $introductionInfo['sale_customername'];

                    $res = $Log->addGoodsRecordLog($recordlog);
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '插入货物进出日志失败');
                    }
                }
            }
        }
    }
    //获取子级总结存量
    public function getSonUntakegoodsnum($data)
    {
        $result['untakegoodsnum'] = 0;
        $result['takegoodsqty'] = 0;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sonResult = $this->getSonUntakegoodsnum($value);
                $result['untakegoodsnum'] += floatval($value['untakegoodsnum']) + $sonResult['untakegoodsnum'];
                $result['takegoodsqty'] += floatval($value['takegoodsqty']) + $sonResult['takegoodsqty'];
            }
        }
        return $result;
    }
    //更改单据状态
    public function changeStatus($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->changeStatus($value);
                if(isset($value['sysno'])){
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' => 7,'untakegoodsnum' => 0,'outqty' => 0,'bookingqty' => 0),'introduction_sysno='.intval($value['sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '更新状态失败');
                    }

                    $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 7),'sysno='.intval($value['sysno']));
                    if(!$res){
                        $this->dbh->rollback();
                        return array('code' => 300,'msg' => '更新状态失败');
                    }
                }
            }
        }
    }
    //获取父级提单信息
    public function getIntroduceTree($father_introduction_sysno)
    {
        $sql = "SELECT * from ".DB_PREFIX."doc_introduction  where isdel = 0";

        $data = $this->dbh->select($sql);
        return $this->_getIntroduceTree($data,$father_introduction_sysno);
    }

    private function _getIntroduceTree($data, $father_introduction_sysno)
    {
        static $list = array();
        foreach ($data as $k => $v) {
            if ($v['sysno'] == $father_introduction_sysno) {
                $list[] = $v;
                $this->_getIntroduceTree($data,$v['father_introduction_sysno']);
            }
        }
        return $list;
    }

    //获取明细上下级
    public function getIntroduceDetailTree($introductiondetail_sysno)
    {
        $sql = "SELECT sysno,introductiondetail_sysno,takegoodsqty from ".DB_PREFIX."doc_introduction_detail";

        $data = $this->dbh->select($sql);
        return $this->_getIntroduceDetailTree($data,$introductiondetail_sysno);
    }

    private function _getIntroduceDetailTree($data, $introductiondetail_sysno)
    {
        static $list = array();
        foreach ($data as $k => $v) {
            if ($v['sysno'] == $introductiondetail_sysno) {
                $list[] = $v;
                $this->_getIntroduceDetailTree($data,$v['introductiondetail_sysno']);
            }
        }
        return $list;
    }

    //获取所有可撤销提单子级的出库单
    private function getIntroduceStockoutData($data)
    {
        $list = [];
        $outArr = [];
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $tempArr = $this->getIntroduceStockoutData($v);
                $outArr = $this->getStockoutData($v['sysno'],$tempArr);
                if(!empty($outArr)){
                    foreach ($outArr as $key => $value) {
                        $list[] = $value;
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 修改介绍信库存
     * @param $outstockqty 实际发出的数量
     * @param $sysno 介绍信详情SYSNO
     * @param $back 是否是退货
     * @return array
     */
    public function updateIntroductionDetail($outstockqty, $sysno, $back = false)
    {
        $sql = "SELECT untakegoodsnum,takegoodsqty,bookingqty,introductiondetailstatus FROM ".DB_PREFIX."doc_introduction_detail WHERE sysno = ".intval($sysno);
        $result = $this-> dbh -> select_row($sql);
        if(!$result){
            return ['code' => 300, 'message' =>'未找到该介绍信的详情信息'];
        }
        if(!$back){
            if($result['introductiondetailstatus'] != 4){
                return ['code' => 319, 'message' =>'非提货中状态,禁止出货'];
            }
        }
        if($result['untakegoodsnum'] - $outstockqty < 0){
            return ['code' => 319, 'message' =>'介绍信库存不能为负'];
        }
        $update['untakegoodsnum'] = $result['untakegoodsnum'] - $outstockqty;
        $update['takegoodsqty'] = $result['takegoodsqty'] + $outstockqty;
        $update['bookingqty'] = ($result['bookingqty'] - $outstockqty < 0) ? 0 :($result['bookingqty'] - $outstockqty);
        $update['updated_at'] = '=NOW()';
        if($back){
            $update['introductiondetailstatus'] == 4;
        }

        $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail', $update, 'sysno=' . intval($sysno));
        if (!$res) {
            return ['code' => 319, 'message' =>'介绍信库存信息修改失败'];
        }
        return  ['code' => 200, 'message' =>'介绍信库存信息修改成功'];
    }

    //跑批用   都是可撤销提单，不用插记录
    public function recycleEndingStocks()
    {
        // $sql = "SELECT id.* from ".DB_PREFIX."doc_introduction i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where receiveend < CURDATE() and receiveend != '0000-00-00' and introductiondetailstatus != 7 and i.introductiontype = 1";
        $sql = "SELECT id.* from ".DB_PREFIX."doc_introduction i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where receiveend < CURDATE() and receiveend != '0000-00-00' and introductiondetailstatus != 7 and i.introductiontype = 1 order by i.created_at asc";
        $detailData = $this->dbh->select($sql);

        // $newDetailData = $detailData;
        if(!$detailData){
            return true;
        }
        $SG = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        foreach ($detailData as $key => $value) {
            //判断自己本身有没有出库中的订单
            $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` so left join `".DB_PREFIX."doc_stock_out_detail` sod on so.sysno=sod.stockout_sysno where so.isdel = 0 and so.stockoutstatus =3 and so.stockouttype=2 and sod.stocktype=2 and sod.stockin_sysno = ".intval($value['sysno']);
            $res = $this->dbh->select($sql);
            if($res && count($res)>0){
                continue;
            }
            //处理自己相关的预约单和订单
            $res = $this->releaseClockqty($value['sysno']);
            if($res['code'] == 300){
                continue;
            }
            //判断子级有没有出库中的订单
            $list = $this->getIntroduceDetail($value['sysno']);
            if(count($list) > 0){
                foreach ($list as $lkey => $lvalue) {
                    $sql = "SELECT * from `".DB_PREFIX."doc_stock_out` so left join `".DB_PREFIX."doc_stock_out_detail` sod on so.sysno=sod.stockout_sysno where so.isdel = 0 and so.stockoutstatus =3 and so.stockouttype=2 and sod.stocktype=2 and sod.stockin_sysno = ".intval($lvalue['sysno']);
                    $res = $this->dbh->select($sql);
                    if($res && count($res)>0){
                        break;
                    }
                    //处理子级相关的预约单和订单
                    $this->releaseClockqty($lvalue['sysno']);
                }
            }

            //获取子级结存量、实提量
            $result = $this->getIntroduceNum($value['sysno']);
            $arr = array('untakegoodsnum' => 0,'takegoodsqty' => 0,);
            if($result){
                foreach ($result as $rvalue) {
                    $res = $this->getIntroduceNum($rvalue['sysno']);
                    if($res){
                        foreach ($res as $val) {
                            $arr['untakegoodsnum'] += $val['untakegoodsnum'];
                            $arr['takegoodsqty'] += $val['takegoodsqty'];
                        }
                    }
                    $arr['untakegoodsnum'] +=$rvalue['untakegoodsnum'];
                    $arr['takegoodsqty'] += $rvalue['takegoodsqty'];
                }
            }
            //获取下一级提单量
            $sql = "SELECT sum(takegoodsnum) as takegoodsnum from `".DB_PREFIX."doc_introduction_detail` where introductiondetailstatus in(4,5) and isdel=0 and introductiondetail_sysno = " .intval($value['sysno']);
            $takegoodsnum = $this->dbh->select_one($sql);

            //加上自己数据回写父级数据
            $untakegoodsnum = $arr['untakegoodsnum'] + $value['untakegoodsnum'];
            // $takegoodsnum = $value['takegoodsnum'];
            $takegoodsqty = $arr['takegoodsqty'] + $value['takegoodsqty'];
            $outqty = $value['outqty'] - $takegoodsnum;

            if($value['introductiondetail_sysno'] != 0){
                $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where sysno = " .intval($value['introductiondetail_sysno']);
                $introduceDetailInfo = $this->dbh->select_row($sql);
                $update = array(
                    'untakegoodsnum' => $introduceDetailInfo['untakegoodsnum'] + $untakegoodsnum,
                    'outqty' => $introduceDetailInfo['outqty'] - $value['takegoodsnum'],
                    'takegoodsqty' => $introduceDetailInfo['takegoodsqty'] + $takegoodsqty,
                    );
                $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",$update,'sysno='.intval($value['introductiondetail_sysno']));
                if(!$res){
                    continue;
                }
                //释放自己提单的罐子待出量
                if($value['untakegoodsnum']>0){
                    $updatesgres = $SG ->pubstoragetankoperation(
                        array(
                            'type' => 9,
                            'data' => array(
                                'sysno' => $value['storagetank_sysno'],
                                'orderoutqty' => $value['untakegoodsnum'],
                            ),
                         ));

                    if($updatesgres['code'] != '200'){
                        continue;
                    }
                }
                if(count($list) > 0){
                    foreach ($list as $lkey => $lvalue) {
                        //释放子级提单的罐子待出量
                        if($lvalue['untakegoodsnum']>0){
                            $updatesgres = $SG ->pubstoragetankoperation(
                                array(
                                    'type' => 9,
                                    'data' => array(
                                        'sysno' => $lvalue['storagetank_sysno'],
                                        'orderoutqty' => $lvalue['untakegoodsnum'],
                                    ),
                                 ));

                            if($updatesgres['code'] != '200'){
                                break;
                            }
                        }
                    }
                }
                if($untakegoodsnum > 0){
                    $storagetankdata = array(
                        'sysno' => $introduceDetailInfo['storagetank_sysno'],
                        'orderoutqty' => $untakegoodsnum
                    );
                    $params['data'] = $storagetankdata;
                    $params['type'] = 10;
                    $storagetankres = $SG->pubstoragetankoperation($params);

                    if ($storagetankres['code'] !='200') {
                        continue;
                    }
                }

                //更新父级明细状态
                if(($introduceDetailInfo['untakegoodsnum'] + $untakegoodsnum) != 0){
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' =>4),'introduction_sysno = ' . intval($introduceDetailInfo['sysno']));
                    if (!$res) {
                        continue;
                    }
                }
                //更新父级主表状态
                $sql = "SELECT * from `".DB_PREFIX."doc_introduction` where sysno = " .intval($introduceDetailInfo['introduction_sysno']);
                $introduceInfo = $this->dbh->select_row($sql);
                if(($introduceDetailInfo['untakegoodsnum'] + $untakegoodsnum) != 0 && $introduceInfo['introductionstatus'] == 5){
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' =>4),'sysno = ' . intval($introduceDetailInfo['introduction_sysno']));
                    if (!$res) {
                        continue;
                    }
                }

            }else{
                //更新储罐待出量
                if($untakegoodsnum>0){
                    $updatesgres = $SG ->pubstoragetankoperation(
                        array(
                            'type' => 9,
                            'data' => array(
                                'sysno' => $value['storagetank_sysno'],
                                'orderoutqty' => $untakegoodsnum,
                            ),
                         ));
                    if($updatesgres['code'] != '200'){
                        continue;
                    }
                }

                if($value['stocktype'] == 1){
                    $sql = "SELECT stockqty,introductionqty from `".DB_PREFIX."storage_stock` where isdel =0 and status = 1 and iscurrent = 1 and sysno = ".intval($value['stock_sysno']);
                    $stockInfo = $this->dbh->select_row($sql);
                    if(!$stockInfo){
                        continue;
                    }
                    $update = array(
                        'stockqty' => $stockInfo['stockqty'] + $untakegoodsnum,
                        'introductionqty' => $stockInfo['introductionqty'] - $untakegoodsnum,
                        );
                    $res = $this->dbh->update(DB_PREFIX."storage_stock",$update,'sysno='.intval($value['stock_sysno']));
                    if(!$res){
                        continue;
                    }
                }elseif($value['stocktype'] == 2){
                    $sql = "SELECT * from `".DB_PREFIX."doc_introduction_detail` where sysno = " .intval($value['stock_sysno']);
                    $introduceDetailInfo = $this->dbh->select_row($sql);
                    $update = array(
                        'outqty' => $introduceDetailInfo['outqty'] - $value['takegoodsnum'],
                        'untakegoodsnum' => $introduceDetailInfo['untakegoodsnum'] + $untakegoodsnum,
                        );
                    if($update['untakegoodsnum'] != 0 && $introduceDetailInfo == 5){
                        $update['introductiondetailstatus'] = 4;
                    }
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",$update,'sysno = ' . intval($value['stock_sysno']));

                    if (!$res) {
                        continue;
                    }
                    //如果来源提单结存量不为0状态置为提货中
                    $sql = "SELECT sum(untakegoodsnum) as untakegoodsnum,i.introductionstatus from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where i.sysno = " .intval($introduceDetailInfo['introduction_sysno']);
                    $info = $this->dbh->select_row($sql);
                    if($info['untakegoodsnum'] != 0 && $info['introductionstatus'] == 5){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' =>4),'sysno = ' . intval($introduceDetailInfo['introduction_sysno']));
                        if (!$res) {
                            continue;
                        }
                    }
                }

            }

            //更新自己明细状态为已撤销
            $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' => 7,'untakegoodsnum' => 0,'outqty' => $outqty),'sysno='.intval($value['sysno']));
            if(!$res){
                continue;
            }
            $list = $this->getIntroduceDetails($value['sysno']);
            //更新子级状态为已撤销
            if(count($list) > 0){
                foreach ($list as $lkey => $lvalue) {
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('introductiondetailstatus' => 7,'untakegoodsnum' => 0),'sysno='.intval($lvalue['sysno']));
                    if(!$res){
                        continue;
                    }

                    $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 7),'sysno='.intval($lvalue['introduction_sysno']));
                    if(!$res){
                        continue;
                    }
                }
            }
            //$detailData重新赋值

            // $sql = "SELECT id.* from ".DB_PREFIX."doc_introduction i LEFT JOIN ".DB_PREFIX."doc_introduction_detail id on i.sysno = id.introduction_sysno where receiveend < CURDATE() and receiveend != '0000-00-00' and introductiondetailstatus != 7";
            // $detailData = $this->dbh->select($sql);
        }

        foreach ($detailData as $key => $value) {
            $res = $this->delBookingData($value['sysno']);
            if($res['code'] == 300){
                continue;
            }
        }
        // $sql = "SELECT * from ".DB_PREFIX."doc_introduction  where receiveend < CURDATE() and receiveend != '0000-00-00' and introductionstatus != 7 and introductiontype = 1";
        $sql = "SELECT * from ".DB_PREFIX."doc_introduction  where receiveend < CURDATE() and receiveend != '0000-00-00' and introductionstatus != 7 and introductiontype = 1";
        $data = $this->dbh->select($sql);

        if(!$data){
            return true;
        }

        foreach ($data as $key => $value) {
            $detaildata = $this->getIntroduceDetailList($value['sysno']);
            foreach ($detaildata as $key => $value) {
                if ($value['introductiondetailstatus'] == 7) {
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 7),'sysno='.intval($value['sysno']));
                    if(!$res){
                        return true;
                    }
                }
            }
        }
    }


    public function searchLadingIntroduce($params) {
        $filter = array();
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = " a.created_at > '{$params['startdate']}'";
        }

        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = " a.created_at < '{$params['enddate']}'";
        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " a.customer_sysno = '{$params['customer_sysno']}'";
        }


        if (isset($params['coststatus']) && $params['coststatus'] != '') {
            $filter[] = " a.coststatus = '{$params['coststatus']}'";
        }

        $where = "WHERE a.costtype = '-2' AND a.status = 1 AND a.isdel = 0";
        if (1 <= count($filter)) {
            $where .= " AND " . implode(' AND ', $filter);
        }

        $sql = "SELECT count(*) FROM (SELECT a.cost_sysno FROM `".DB_PREFIX."doc_finance_cost_detail` as a LEFT JOIN `".DB_PREFIX."doc_stock_in` b on a.instock_sysno=b.sysno ".$where." GROUP BY a.cost_sysno) s";
        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);
        $order = ' order by a.created_at desc';
        $result['list'] = array();
        if ($result['totalRow'])
        {
//            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT a.*,SUM(a.totalprice) sumpricre,b.stockintype as stockintype,(SELECT c.shipname FROM hengyang_doc_stock_in_detail c WHERE b.sysno = c.stockin_sysno LIMIT 1) as shipname FROM `".DB_PREFIX."doc_finance_cost_detail` a
                    LEFT JOIN `".DB_PREFIX."doc_stock_in` b on a.instock_sysno=b.sysno {$where} GROUP BY cost_sysno {$order}";
                if($params['orders'] != '') {
                    $sql .= "," . $params['orders'];
                }

                $arr = 	$this->dbh->select($sql);
//            }else{
//                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
//                $this->dbh->set_page_num($params['pageCurrent'] );
//                $this->dbh->set_page_rows($params['pageSize'] );
//                $sql = "SELECT a.*,SUM(a.totalprice) sumpricre,b.stockintype as stockintype,(SELECT c.shipname FROM hengyang_doc_stock_in_detail c WHERE b.sysno = c.stockin_sysno LIMIT 1) as shipname  FROM `".DB_PREFIX."doc_finance_cost_detail` a
//                    LEFT JOIN `".DB_PREFIX."doc_stock_in` b on a.instock_sysno=b.sysno {$where} GROUP BY cost_sysno {$order}";
//                if($params['orders'] != ''){
//                    $sql .= ",". $params['orders'] ;
//                }
//                $arr = 	$this->dbh->select_page($sql);
//            }

            $newArr = [];
            //船名过滤
            foreach ($arr as $k => $v){
                if($params['shipname'] && strpos($v['shipname'], $params['shipname']) === false){
                    continue;
                }else{
                    $takeNumSql =  "SELECT takegoodsnum,takegoodsqty,(takegoodsnum - takegoodsqty) costqty FROM hengyang_doc_introduction_detail WHERE sysno = {$v['cost_sysno']} ";
                    $resultArr = $this -> dbh -> select_row($takeNumSql);
                    $newArr[] =  array_merge($v,$resultArr);
                }
            }
            //分页
            $data['totalRow'] = count($newArr);
            $data['totalPage'] = ceil($data['totalRow'] / $params['pageSize']);
            $list=array_chunk($newArr, $params['pageSize'], false);
            $data['list']= $list[$params['pageCurrent']-1] ? $list[$params['pageCurrent']-1] : [];
        }
        return $data ? $data : [];
    }

    public function searchLadingDetail($id){
        $sql = "SELECT a.*,c.shipname,s.stockintype FROM `".DB_PREFIX."doc_finance_cost_detail` a
                LEFT JOIN hengyang_doc_introduction_detail c ON a.cost_sysno = c.sysno
                LEFT JOIN hengyang_doc_stock_in s ON s.sysno = c.stock_sysno
                WHERE a.costtype = '-2' AND a.`status` = 1 AND a.isdel = 0 AND a.cost_sysno = ".intval($id);
        return $this -> dbh -> select($sql);
    }

    public function searchLadingDetailBySysno($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_finance_cost_detail` WHERE costtype = '-2' AND `status` = 1 AND isdel = 0 AND sysno = ".intval($id);
        return $this -> dbh -> select_row($sql);
    }

    /**
     * batchRunIntroduce 跑批计算提单费用
     * @author DXB
     */
    public function batchRunIntroduce(){
        $sql = "SELECT di.sysno,di.stock_sysno,di.stockin_no stockinno,di.stockin_sysno instock_sysno,di.goods_sysno,di.goodsname,di.goodsqualityname qualityname,di.storagetank_sysno,di.storagetankname,i.cost_customer_sysno,i.introductiondate,i.introductionno,i.customer_sysno,i.customername,i.sale_customer_sysno,i.sale_customername,i.buy_customer_sysno,i.buy_customername,i.receivestart,i.receiveend,i.freecostdate,i.cost_customername,i.lastamount,di.unitname,di.takegoodsnum,di.untakegoodsnum,di.takegoodsqty,di.firstdate,di.introductiondetail_sysno,di.shipname FROM `".DB_PREFIX."doc_introduction_detail` di LEFT JOIN `".DB_PREFIX."doc_introduction` i ON di.introduction_sysno = i.sysno WHERE di.introductiondetailstatus != 5 AND di.takegoodsnum > 0 AND i.isdel = 0 AND i.`introductionstatus` in ('4','5') AND di.firstdate <'" .date('Y-m-d')."'";
        $result = $this->dbh->select($sql);
        foreach ($result as $index => $item) {
            //如果提单数量为0则直接跳出
            if($item['untakegoodsnum'] <= 0){
                continue;
            }

            $res = self::insertIntroduce($item['sysno'], $item);

            if($res['code'] != 200){
                //如果没有生成提单费直接跳过
                echo json_encode($res)."<br/>";
            }
            echo json_encode($res)."<br/>";
        }
    }

    /**
     * insertIntroduce 插入每条详情的数据
     * @author dxb
     * @param $sysno 费用对应的详情
     * @param $introduceDate 原始数据
     * @return array
     */
    public function insertIntroduce($sysno, $introduceDate, $father_sql = false)
    {
        //error_log(date("Y-m-d H:i:s")  . "\t" . json_encode($sysno) . "\n", 3, './logs/test.log');
        $sql = 'SELECT i.father_introduction_sysno,i.receivestart,i.receiveend,i.costtype,i.lastamount,di.firstdate,i.buy_customername,i.buy_customer_sysno,i.sale_customer_sysno,i.sale_customername,di.stock_sysno,i.lastamount FROM `'.DB_PREFIX.'doc_introduction_detail` di LEFT JOIN `'.DB_PREFIX.'doc_introduction` i ON di.introduction_sysno = i.sysno WHERE di.sysno = '.intval($sysno);
        if($father_sql){
            $sql = 'SELECT i.father_introduction_sysno,i.receivestart,i.receiveend,i.costtype,i.lastamount,di.firstdate,i.buy_customername,i.buy_customer_sysno,i.sale_customer_sysno,i.sale_customername,di.stock_sysno,i.lastamount FROM `'.DB_PREFIX.'doc_introduction_detail` di LEFT JOIN `'.DB_PREFIX.'doc_introduction` i ON di.introduction_sysno = i.sysno WHERE i.sysno = '.intval($sysno);
        }
        $res = $this->dbh-> select_row($sql);
        if(!$res){
            return ['code' => '300', 'message' => '未找到该数据,提单明细主键'.$introduceDate['sysno']];
        }
        $costEndDate = strtotime($res['receiveend']) ?  (strtotime($res['receiveend']) + 87400) : -1 ;
        if($costEndDate < 0){ //未填结束日期
            //error_log(date("Y-m-d H:i:s")  . "\t" ."小于".$sysno.":". json_encode($introduceDate) . "\n", 3, './logs/test.log');
            if ($res['father_introduction_sysno'] == 0){
                if(strtotime($res['firstdate']) > time()){
                    return ['code' => '300', 'message' => '未超首期,提单明细主键'.$introduceDate['sysno']];
                }else{
                    //记录数据
                    return self::addIntroduceFinance($res, $introduceDate, $res['costtype']);
                }
            }else{
                //找上家
                return self::insertIntroduce($res['father_introduction_sysno'], $introduceDate, true);
            }
        }else {//填写了结束日期
            if ($costEndDate >= time()) {//免仓期之内
                //找上家
                //error_log(date("Y-m-d H:i:s")  . "\t" ."return".$sysno.":". json_encode($introduceDate) . "\n", 3, './logs/test.log');
                if($res['father_introduction_sysno'] == 0){ //如果是超期
                    if(strtotime($res['firstdate']) < time()){
                        return self::addIntroduceFinance($res, $introduceDate, 1);
                    }else{
                        return ['code' => '300', 'message'=>'在免仓期之内不需要计算费用,提单明细主键'.json_encode($introduceDate['sysno'])];
                    }
                }else{
                    if($costEndDate >= time()){
                        return self::addIntroduceFinance($res, $introduceDate, 2, true);
                    }else{
                        return self::insertIntroduce($res['father_introduction_sysno'], $introduceDate, true);
                    }
                }
            } else {
                if ($res['costtype'] == 2) { //买家承担
                    return self::addIntroduceFinance($res, $introduceDate, 2);
                } else { //卖家承担
                    //找上家
                    //error_log(date("Y-m-d H:i:s")  . "\t" ."return2:".$sysno.":". json_encode($introduceDate) . "\n", 3, './logs/test.log');
                    return self::insertIntroduce($res['father_introduction_sysno'], $introduceDate, true);
                }
            }
        }
    }

    public function addIntroduceFinance($res, $introduceDate, $bear, $buyPrice = false){
        if($bear == 1){ //上家承担费用
            $insertDate['customer_sysno'] = $res['sale_customer_sysno'];
            $insertDate['customer_name'] = $res['sale_customername'];
            $insertDate['unitprice'] = self::getContractLastamount($res['stock_sysno']); //需要查询合同的超期费用
        }else{ //下家承担费用
            $insertDate['customer_sysno'] = $res['buy_customer_sysno'];
            $insertDate['customer_name'] = $res['buy_customername'];
            $insertDate['unitprice'] = $res['lastamount'];
            if($buyPrice){
                $data =  self::recursionReceiveStart($res);
                if(!empty($data)){
                    $insertDate['customer_sysno'] = $data['customer_sysno'];
                    $insertDate['customer_name'] = $data['customer_name'];
                    $insertDate['unitprice'] = $data['unitprice'];
                }else{
                    $insertDate['unitprice'] = 0;
                }
            }
        }
        if(!$insertDate['unitprice']){
            return ['code' => '200', 'message' => '费用为0不需要插入数据,提单明细主键'.$introduceDate['sysno']];
        }
        $insertDate['costdate'] = date('Y-m-d');
        $insertDate['costno'] = COMMON::getCodeId('F1');
        $insertDate['costdateend'] = $res['receiveend'];
        $insertDate['isexceedfirst'] = 1;
        $insertDate['costtype'] = '-2';
        $insertDate['costname'] = '提单费用';
        $insertDate['coststatus'] = 2;
        $insertDate['created_at'] = '=NOW()';
        $insertDate['updated_at'] = '=NOW()';
        $insertDate['unitname'] = '吨';
        $insertDate['cost_sysno'] = $introduceDate['sysno'];
        $insertDate['shipname'] = $introduceDate['shipname'];
        $insertDate['stock_sysno'] = $introduceDate['stock_sysno'];
        $insertDate['instock_sysno'] = $introduceDate['instock_sysno'];
        $insertDate['stockinno'] = $introduceDate['stockinno'];
        $insertDate['storagetank_sysno'] = $introduceDate['storagetank_sysno'];
        $insertDate['storagetankname'] = $introduceDate['storagetankname'];
        $insertDate['goods_sysno'] = $introduceDate['goods_sysno'];
        $insertDate['goodsname'] = $introduceDate['goodsname'];
        $insertDate['qualityname'] = $introduceDate['qualityname'];
        $insertDate['ullage'] = $this->getIntroduceUllage($introduceDate['sysno']);
        $insertDate['first_customer_sysno'] = $introduceDate['customer_sysno'];
        $insertDate['first_customername'] = $introduceDate['customername'];
        $insertDate['sale_customer_sysno'] = $introduceDate['sale_customer_sysno'];
        $insertDate['sale_customername'] = $introduceDate['sale_customername'];
        $insertDate['buy_customer_sysno'] = $introduceDate['buy_customer_sysno'];
        $insertDate['buy_customername'] = $introduceDate['buy_customername'];
        $insertDate['receivestart'] = $introduceDate['receivestart'];
        $insertDate['receiveend'] = $introduceDate['receiveend'];
        $insertDate['takegoodsnum'] = $introduceDate['takegoodsnum'];
        $insertDate['takegoodsqty'] = $introduceDate['takegoodsqty'];
        $insertDate['costqty'] = $introduceDate['untakegoodsnum'];
        $insertDate['totalprice'] = sprintf('%0.2f', $introduceDate['untakegoodsnum'] * $insertDate['unitprice']);
        $result = $this -> dbh -> insert(DB_PREFIX.'doc_finance_cost_detail', $insertDate);
        if(!$result){
            return ['code' => '301', 'message' => '数据插入失败,插入信息'.json_encode($insertDate)];
        }
        return ['code' => '200', 'message' => '费用计算成功,提单明细主键'.$introduceDate['sysno']];
    }

    /**
     * recursionReceiveStart 递归查询不到开始提货时间的提单  需要递归找到真正的货主
     * @author dxb
     * @param $data
     * @return array
     */
    public function recursionReceiveStart($data){
        $return = [];
        if(is_array($data)){
            $sql = "SELECT i.father_introduction_sysno,i.receivestart,i.receiveend,i.costtype,i.lastamount,di.firstdate,i.buy_customername,i.buy_customer_sysno,i.sale_customer_sysno,i.sale_customername,di.stock_sysno,i.lastamount FROM `".DB_PREFIX."doc_introduction_detail` di LEFT JOIN `".DB_PREFIX."doc_introduction` i ON di.introduction_sysno = i.sysno WHERE i.sysno = ".intval($data['father_introduction_sysno']);
            $res =  $this->dbh ->select_row($sql) ? $this->dbh ->select_row($sql) : [];
            if(strtotime($data['receivestart']) > time()){
                if($data['father_introduction_sysno'] == 0){
                    $return['customer_sysno'] = $res['sale_customer_sysno'];
                    $return['customer_name'] = $res['sale_customername'];
                    $return['unitprice'] = self::getContractLastamount($res['stock_sysno']); //需要查询合同的超期费用
                }else{
                    $return['customer_sysno'] = $res['buy_customer_sysno'];
                    $return['customer_name'] = $res['buy_customername'];
                    $return['unitprice'] = $res['lastamount'];
                    if(strtotime($res['receiveend'])+87400 > time()){
                        if($res['father_introduction_sysno'] != 0){
                            return self::recursionReceiveStart($res);
                        }
                        $return['customer_sysno'] = $res['sale_customer_sysno'];
                        $return['customer_name'] = $res['sale_customername'];
                        $return['unitprice'] = self::getContractLastamount($res['stock_sysno']); //需要查询合同的超期费用
                    }
                }
            }else{
                $return['customer_sysno'] = $res['buy_customer_sysno'];
                $return['customer_name'] = $res['buy_customername'];
                $return['unitprice'] = $res['lastamount'];
                if(strtotime($res['receiveend'])+87400 > time()){
                    if($res['father_introduction_sysno'] != 0){
                        return self::recursionReceiveStart($res);
                    }
                    $return['customer_sysno'] = $res['sale_customer_sysno'];
                    $return['customer_name'] = $res['sale_customername'];
                    $return['unitprice'] = self::getContractLastamount($res['stock_sysno']); //需要查询合同的超期费用
                }
            }
        }
        return $return;
    }

    /**
     * getIntroduceUllage 查询提单的损耗
     * @author DXb
     * @param $sysno
     * @return int
     */
    public function getIntroduceUllage($sysno){
        $sql = "SELECT beqty FROM `".DB_PREFIX."storage_stock_batlog` WHERE introduction_detail_sysno = ".intval($sysno)." AND created_at LIKE '".date('Y-m-d')."%'";
        return $this->dbh ->select_one($sql) ? $this->dbh ->select_one($sql) : 0;
    }

    /**
     * getContractLastamount 获取合同超期费
     * @author ${USER}
     * @param $stock_sysno
     * @return int
     */
    public function getContractLastamount($stock_sysno){
        $sql = "SELECT dcg.lastamount FROM `".DB_PREFIX."storage_stock` ss LEFT JOIN `".DB_PREFIX."doc_contract_goods` dcg ON ss.contract_sysno = dcg.contract_sysno AND dcg.goods_sysno = ss.goods_sysno WHERE ss.sysno = ".intval($stock_sysno);
        return $this->dbh ->select_one($sql) ? $this->dbh ->select_one($sql) : 0;
    }


    //出库查看用
    //通过入库单号获取提单明细
    public function getIntroduceInfo($id){
        if(!$id){
            return array('code' => 300 ,'msg' => '提单信息错误');
        }

        $sql = "SELECT i.introductiondate,i.introductiontype,i.customername,i.buy_customername,i.takegoodsno,CONCAT_WS('至',i.receivestart,i.receiveend) as timerange,i.freecostdate,sum(did.takegoodsnum) as takegoodsnum,did.goodsname FROM `".DB_PREFIX."doc_introduction` i LEFT JOIN ".DB_PREFIX."doc_introduction_detail did on i.sysno = did.introduction_sysno where i.sysno = ".intval($id);

        $result = $this->dbh->select_row($sql);
        if(!$result){
            return array('code' => 300 ,'msg' => '提单信息错误');
        }

        return $result;
    }

    public function outListDetail($id)
    {
        if(!$id){
            return array('code' => 300 ,'msg' => '提单信息错误');
        }
        $sql = "SELECT sum(did.takegoodsnum) as takegoodsnum from ".DB_PREFIX."doc_introduction i left join ".DB_PREFIX."doc_introduction_detail did on i.sysno = did.introduction_sysno where i.sysno=".intval($id);
        $takegoodsnum = $this->dbh->select_one($sql);

        $detailData = $this->getIntroduceDetailList($id);
        if(!$detailData){
            return array('code' => 300 ,'msg' => '提单明细信息错误');
        }
        $outArr = [];
        //所有可撤销提单
        $sql = "SELECT * from ".DB_PREFIX."doc_introduction i left join ".DB_PREFIX."doc_introduction_detail did on i.sysno = did.introduction_sysno where i.introductiontype = 1 and introductiondetailstatus in (4,5)";
        $data = $this->dbh->select($sql);
        foreach ($detailData as $key => $value) {
            $outArr = $this->getStockoutData($value['sysno'],$outArr);
            $sonTree = $this->getAllIntroduceDetail($value['sysno'],$data);

            $list = $this->getIntroduceStockoutData($sonTree); //111111111
            if (!empty($list)) {
                foreach ($list as $v) {
                    $outArr[] = $v;
                }
            }
        }

        //查询不可撤销提单，只查一级
        $sql = "SELECT DATE_FORMAT(i.updated_at,'%Y-%m-%d') as updated_at,sum(takegoodsnum) as takegoodsnum from ".DB_PREFIX."doc_introduction i left join ".DB_PREFIX."doc_introduction_detail did on i.sysno = did.introduction_sysno where i.introductiontype = 2 and introductiondetailstatus in (4,5) and father_introduction_sysno = " .intval($id) . " group by i.sysno";
        $tempList = $this->dbh->select($sql);

        if($tempList){
            foreach ($tempList as $key => $value) {
                $outArr[] = $value;
            }
        }

        if(!empty($outArr)){
            foreach ($outArr as $key => $value) {
                if(!isset($value['takegoodsnum'])){
                    $outArr[$key]['endingstock'] = sprintf('%.3f',$takegoodsnum - $value['beqty']);
                    $takegoodsnum = $takegoodsnum - $value['beqty'];
                }else{
                    $outArr[$key]['endingstock'] = sprintf('%.3f',$takegoodsnum - $value['takegoodsnum']);
                    $takegoodsnum = $takegoodsnum - $value['takegoodsnum'];
                }

            }
        }
        return $outArr;
    }

    public function getStockoutData($stockin_sysno,$outArr)
    {
        //查找自己本身的船出库订单
        $sql = "SELECT DATE_FORMAT(so.updated_at,'%Y-%m-%d') as updated_at,group_concat(DISTINCT sod.shipname Separator' ') as carid,sum(sod.beqty) as beqty from `".DB_PREFIX."doc_stock_out` so left join `".DB_PREFIX."doc_stock_out_detail` sod on so.sysno=sod.stockout_sysno where so.isdel = 0 and so.stockoutstatus = 4 and so.stockouttype in(1,3) and sod.stocktype=2 and sod.stockin_sysno = ".intval($stockin_sysno) . " group by so.sysno";
        $res = $this->dbh->select($sql);
        if($res){
            foreach ($res as $key => $value) {
                $outArr[] = $value;
            }
        }

        //查找自己本身的车出库磅码单
        $sql = "SELECT DATE_FORMAT(po.updated_at,'%Y-%m-%d') as updated_at,po.poundsoutno,po.carid,po.memo,po.create_username,sum(pod.realnumber) as beqty from `".DB_PREFIX."doc_stock_out` so left join `".DB_PREFIX."doc_stock_out_detail` sod on so.sysno=sod.stockout_sysno left join `".DB_PREFIX."doc_pounds_out_detail` pod on sod.sysno=pod.stockoutdetail_sysno left join `".DB_PREFIX."doc_pounds_out` po on po.sysno=pod.pounds_out_sysno where so.isdel = 0 and so.stockoutstatus in (3,4) and so.stockouttype = 2 and sod.stocktype=2 and po.poundsoutstatus = 4 and sod.stockin_sysno = ".intval($stockin_sysno) ." group by po.sysno";

        $res = $this->dbh->select($sql);
        if($res){
            foreach ($res as $pound) {
                $pound['beqty'] = sprintf('%.3f',$pound['beqty']/1000);
                $outArr[] = $pound;
            }
        }
        return $outArr;
    }

}
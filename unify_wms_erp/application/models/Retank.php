<?php
/**
 * 倒罐管理
 * User: Alan
 * Date: 2016-11-23 
 * Time: 18:27:41
 */
class RetankModel
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
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }


    /*
     * 获取倒罐申请单列表
     */
    public function searchapplyretank($params){
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " br.bookingretankno LIKE '" . $params['bar_no']."'";
        }
        if (isset($params['bookingretankdate']) && $params['bookingretankdate'] != '') {
            $filter[] = " br.bookingretankdate >= '" . $params['bookingretankdate']."'";
        }
        if (isset($params['bookingretankdate_end']) && $params['bookingretankdate_end'] != '') {
            $filter[] = " br.bookingretankdate <= '" . $params['bookingretankdate_end']."'";
        }
        if (isset($params['stockretankstatus']) && $params['stockretankstatus'] != ''&& $params['stockretankstatus'] != '0') {
            $filter[] = " br.stockretankstatus = " . $params['stockretankstatus'];
        }
        if(isset($params['issaveorder'])){
            $filter[] = " br.issaveorder = " . $params['issaveorder'];
        }

        $where = 'WHERE br.`status` = 1 AND br.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by br.updated_at desc";
        $sql = "SELECT Count(sysno) FROM ".DB_PREFIX."doc_booking_retank br ".$where;
        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM ".DB_PREFIX."doc_booking_retank br ".$where.$order;
                $arr =  $this->dbh->select($sql);
                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );
                $sql = "SELECT * FROM ".DB_PREFIX."doc_booking_retank br ".$where.$order;
                $arr =  $this->dbh->select_page($sql);
                $result['list'] = $arr;
            }
        }

        if(!empty($result['list'])){
            $sql = "SELECT stockretank_sysno,stockretank_out_no,stockretank_in_no FROM ".DB_PREFIX."doc_booking_retank_detail ";
            $stockretank =  $this->dbh->select($sql);
            foreach($result['list'] as $key => $item){
                foreach($stockretank as $value){
                    if($value['stockretank_sysno'] == $item['sysno']){
                        $result['list'][$key]['stockretank_out_nos'][] = $value['stockretank_out_no'];
                        $result['list'][$key]['stockretank_in_nos'][] = $value['stockretank_in_no'];
                    }
                }
                $result['list'][$key]['stockretank_out_no'] = implode(',',$result['list'][$key]['stockretank_out_nos']);
                $result['list'][$key]['stockretank_in_no'] = implode(',',$result['list'][$key]['stockretank_in_nos']);
            }
        }

        return $result;
    }

    /*
     * 根据id获取倒罐申请单基本信息
     */
    public function getapplyretank($id){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_booking_retank WHERE sysno = $id";
        return  $this->dbh->select_row($sql);
    }

    /*
     * 获取倒罐申请单详情
     */
    public function getapplyretankdetailById($id)
    {
        $sql = "SELECT srd.sysno,srd.customer_sysno,srd.customername,srd.stockin_sysno,srd.stockin_no,srd.shipname,srd.stockretank_out_no,srd.goodsname,srd.qualityname,srd.goodsnature,'吨' AS unitname,srd.stockretank_in_no,
                srd.bookingretankqty,srd.memo,srd.stockretank_out_sysno,srd.stockretank_in_sysno,srd.tank_stockqty,srd.out_stock_sysno,srd.created_at,br.goods_sysno,bs.actualcapacity,sid.release_num,sid.unrelease_num
                FROM ".DB_PREFIX."doc_booking_retank_detail as srd
                LEFT JOIN  ".DB_PREFIX."doc_booking_retank as br ON br.sysno = srd.stockretank_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank as bs ON bs.sysno = srd.stockretank_in_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in_declare as sid ON sid.stockin_sysno = srd.stockin_sysno
                WHERE srd.isdel = 0 AND srd.stockretank_sysno = ".intval($id) ."
                GROUP BY srd.sysno";
        return $this->dbh->select($sql);
    }

    /*
     * 新增倒罐申请单
     */
    public function addapplyRetank($data,$retankdetaildata,$attachment=null)
    {
        $this->dbh->begin();
        try {
            // 添加 倒罐基本信息
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_retank', $data);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            $id = $res;
            //循环插入详情表数据
            foreach ($retankdetaildata as $value) {
                $detail = array(
                    'stockretank_sysno' => $id,
                    'bookingretankqty' => $value['bookingretankqty'],
                    'stockretank_out_sysno'=>$value['stockretank_out_sysno'],
                    'stockretank_out_no'=>$value['stockretank_out_no'],
                    'stockretank_in_sysno'=>$value['stockretank_in_sysno'],
                    'stockretank_in_no'=>$value['stockretank_in_no'],
                    'goodsname'=>$value['goodsname'],
                    'qualityname'=>$value['qualityname'],
                    'unitname'=>$value['unitname'],
                    'goodsnature'=>$value['goodsnature'],
                    'tank_stockqty'=>$value['tank_stockqty'],
                    'stock_sysno'=>$value['stock_sysno'],
                    'memo' => $value['memo'],
                    'isdel' => 0,
                    'created_at' => "=NOW()",
                    'updated_at' => "=NOW()",
                    'customer_sysno'=>$value['customer_sysno'],
                    'customername'=>$value['customername'],
                    'stockin_sysno'=>$value['stockin_sysno'],
                    'stockin_no'=>$value['stockin_no'],
                    'shipname'=>$value['shipname'],
                    'out_stock_sysno'=>$value['sysno']
                );
//                var_dump($detail);die();
                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_retank_detail', $detail);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'添加明细表失败'];
                }

            }

            //操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  30,
                'opertype'  => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '新建倒罐申请单',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'添加日志失败'];
            }

            if($data['stockretankstatus']==2){
                $input['opertype']= 1;
                $input['operdesc']= '暂存倒罐申请单';
            }elseif($data['stockretankstatus']==4){
                $input['opertype']= 2;
                $input['operdesc']= '提交倒罐申请单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'添加日志失败'];
            }

            //回写附件对应转移单的id
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            if(count($attachment) > 0){
                $res = 	$A->addAttachModelSysno($id,$attachment);
                if(!$res){
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写附件失败'];
                }
            }
            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>"失败"];
        }
    }

    /*
     * 更新倒罐申请单
     */
    public function updateapplyRetank($id,$retank,$retankdetaildata,$stockretankstatus,$attachment)
    {
        $this->dbh->begin();
        try {
            $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $retankdata = $R->getapplyretank($id);

            if($stockretankstatus == 4){
                $retank['stockretankstatus'] = 4;
            }elseif($stockretankstatus == 2&&$retankdata['stockretankstatus']==7){
                $retank['stockretankstatus'] = 7;
            }else{
                $retank['stockretankstatus'] = 2;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_retank',$retank,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            // 删除明细表数据
            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_retank_detail', 'stockretank_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'删除明细失败'];
            }
            //循环插入详情表数据
            foreach ($retankdetaildata as $value) {
                $detail = array(
                    'stockretank_sysno' => $id,
                    'bookingretankqty' => $value['bookingretankqty'],
                    'stockretank_out_sysno'=>$value['stockretank_out_sysno'],
                    'stockretank_out_no'=>$value['stockretank_out_no'],
                    'stockretank_in_sysno'=>$value['stockretank_in_sysno'],
                    'stockretank_in_no'=>$value['stockretank_in_no'],
                    'goodsname'=>$value['goodsname'],
                    'qualityname'=>$value['qualityname'],
                    'unitname'=>$value['unitname'],
                    'goodsnature'=>$value['goodsnature'],
                    'tank_stockqty'=>$value['tank_stockqty'],
                    'stock_sysno'=>$value['stock_sysno'],
                    'memo' => $value['memo'],
                    'isdel' => 0,
                    'created_at' => "=NOW()",
                    'updated_at' => "=NOW()",
                    'customer_sysno'=>$value['customer_sysno'],
                    'customername'=>$value['customername'],
                    'stockin_sysno'=>$value['stockin_sysno'],
                    'stockin_no'=>$value['stockin_no'],
                    'shipname'=>$value['shipname'],
                    'out_stock_sysno'=>$value['sysno']
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_retank_detail', $detail);

                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'更新倒罐详细单失败'];
                }
                //添加附件
                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                if(count($attachment)>0){
                    $res = $A->addAttachModelSysno($id,$attachment);
                    if(!$res){
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'更新附件失败'];
                    }
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  30,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );
            if($stockretankstatus==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存倒罐申请单';
            }elseif($stockretankstatus==4){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交倒罐申请单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /*
     * 审核申请单
     */
    public function auditapplyRetank($id,$retank,$stockretankstatus)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_retank', $retank, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'审核失败'];
            }

            if($stockretankstatus==5)
            {
                //获取当前靠泊装卸单的信息，判断是否要生成品质检查单据、管线分配单、泊位分配单
                $data['ispipelineorder'] = $retank['ispipelineorder'];
                $data['sysno'] = $id;
                $data['bookinginno'] = $retank['bookingretankno'];
                $data['bookingindate'] = $retank['bookingretankdate'];

                $bookshipinInstance = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $bookshipinInstance->createThreeBill($data,17);
                if($res['code']!=200){
                    return ['code'=>300, 'message'=>$res];
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  30,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $retank['auditreason'],
            );
            if($stockretankstatus==5)
            {
                $input['opertype'] = 3;
                $input['operdesc'] = '审核通过倒罐申请单';
            }elseif($stockretankstatus==7){
                $input['opertype'] = 5;
                $input['operdesc'] = '审核不通过倒罐申请单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /*
     * 退回倒罐申请单
     */
    public function backRetankapply($id,$retank)
    {
        $this->dbh->begin();
        try {
            //更新倒罐预约单
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_retank',$retank,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新失败'];
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  30,
                'opertype'  => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $retank['backreason'],
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /**
     * 删除倒罐申请单
     */
    public function delapplyRetank($id,$retank)
    {
        return $this->dbh->update(DB_PREFIX.'doc_booking_retank', $retank, 'sysno=' . intval($id));
    }



    /*************************倒罐订单***********************************/
    /*
     * 获取倒罐订单列表
     * */
    public function searchstockretank($params)
    {
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " sr.stockretankno LIKE '" . $params['bar_no']."'";
        }
        if (isset($params['stockretankdate']) && $params['stockretankdate'] != '') {
            $filter[] = " sr.stockretankdate >= '" . $params['stockretankdate']."'";
        }
        if (isset($params['stockretankdate_end']) && $params['stockretankdate_end'] != '') {
            $filter[] = " sr.stockretankdate <= '" . $params['stockretankdate_end']."'";
        }
        if (isset($params['stockretankstatus']) && $params['stockretankstatus'] != ''&& $params['stockretankstatus'] != '0') {
            $filter[] = " sr.stockretankstatus = " . $params['stockretankstatus'];
        }
        $where = 'WHERE sr.`status` = 1 AND sr.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " ORDER BY sr.updated_at desc";
        $sql = "SELECT Count(*) FROM ".DB_PREFIX."doc_stock_retank sr ".$where;
        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT sr.*,SUM(srd.stockretankqty) AS stockretankqty FROM ".DB_PREFIX."doc_stock_retank sr
                        LEFT JOIN ".DB_PREFIX."doc_stock_retank_detail srd ON srd.stockretank_sysno = sr.sysno
                        ".$where."GROUP BY sr.sysno ".$order;
                $arr =  $this->dbh->select($sql);
                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );
                $sql = "SELECT sr.*,SUM(srd.stockretankqty) AS stockretankqty FROM ".DB_PREFIX."doc_stock_retank sr
                        LEFT JOIN ".DB_PREFIX."doc_stock_retank_detail srd ON srd.stockretank_sysno = sr.sysno
                        ".$where." GROUP BY sr.sysno ".$order;
                $arr =  $this->dbh->select_page($sql);
                $result['list'] = $arr;
            }
        }

        if(!empty($result['list'])){
            $sql = "SELECT stockretank_sysno,stockretank_out_no,stockretank_in_no FROM ".DB_PREFIX."doc_stock_retank_detail ";
            $stockretank =  $this->dbh->select($sql);
            foreach($result['list'] as $key => $item){
                foreach($stockretank as $value){
                    if($value['stockretank_sysno'] == $item['sysno']){
                        $result['list'][$key]['stockretank_out_nos'][] = $value['stockretank_out_no'];
                        $result['list'][$key]['stockretank_in_nos'][] = $value['stockretank_in_no'];
                    }
                }
                $result['list'][$key]['stockretank_out_no'] = implode(',',$result['list'][$key]['stockretank_out_nos']);
                $result['list'][$key]['stockretank_in_no'] = implode(',',$result['list'][$key]['stockretank_in_nos']);
            }
        }

        return $result;
    }

    /*
     * 根据id获取倒罐订单基本信息
     */
    public function getstockretank($id){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_stock_retank WHERE sysno = $id";
        return  $this->dbh->select_row($sql);
    }

    /**
     * 获取倒罐订单详情
     */
    public function getRetankdetailById($id)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."doc_stock_retank_detail WHERE stockretank_sysno = ".intval($id);
        return $this->dbh->select($sql);
    }

    /**
     * 添加倒罐订单
     */
    public function addRetank($data,$retankdetaildata,$attachment)
    {
        $this->dbh->begin();
        try {
            // 添加 倒罐基本信息
            $res = $this->dbh->insert(DB_PREFIX.'doc_stock_retank', $data);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }
            $id = $res;
            //循环插入详情表数据
            foreach ($retankdetaildata as $value) {
                $detail = array(
                    'stockretank_sysno' => $id,
                    'bookingretankqty' => $value['bookingretankqty'],
                    'stockretankqty' => $value['stockretankqty'],
                    'out_stock_sysno'=>$value['out_stock_sysno'],
                    'stockretank_out_sysno'=>$value['stockretank_out_sysno'],
                    'stockretank_out_no'=>$value['stockretank_out_no'],
                    'stockretank_in_sysno'=>$value['stockretank_in_sysno'],
                    'stockretank_in_no'=>$value['stockretank_in_no'],
                    'goodsname'=>$value['goodsname'],
                    'qualityname'=>$value['qualityname'],
                    'unitname'=>$value['unitname'],
                    'goodsnature'=>$value['goodsnature'],
                    'tank_stockqty'=>$value['tank_stockqty'],
                    'stock_sysno'=>$value['stock_sysno'],
                    'customer_sysno'=>$value['customer_sysno'],
                    'customername'=>$value['customername'],
                    'stockin_sysno'=>$value['stockin_sysno'],
                    'stockin_no'=>$value['stockin_no'],
                    'shipname'=>$value['shipname'],
                    'memo' => $value['memo'],
                    'isdel' => 0,
                    'created_at' => "=NOW()",
                    'updated_at' => "=NOW()"
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_retank_detail', $detail);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'添加明细表失败'];
                }

                //回写申请单issaveorder
                $bookdata = array(
                    'issaveorder' =>1,
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_booking_retank',$bookdata,'sysno='.$data['bookingretank_sysno']);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写倒罐申请单失败'];
                }

                //倒罐回写移出罐商品规格覆盖基础表的商品规格
                $qulityname['qualityname'] = $value['qualityname'];
                $res = $this->dbh->update(DB_PREFIX.'base_storagetank',$qulityname,'sysno='.$value['stockretank_in_sysno']);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写商品规格失败'];
                }
            }

            if($data['ispipelineorder'] == 1){
                //更新管线单据
                $sql = "SELECT sysno FROM ".DB_PREFIX."doc_pipelineorder WHERE businesstype = 17 AND booking_sysno = {$data['bookingretank_sysno']}";
                $pipid = $this->dbh->select_one($sql);
                if (!$pipid) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'查询管线信息失败'];
                }
                $pip = array(
                    'businesstype'=>18,
                    'stock_sysno' =>$id,
                    'stockno' =>$data['stockretankno'],
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$pip,'sysno='.$pipid);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写管线失败'];
                }
            }

            //操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  12,
                'opertype'  => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '新建倒罐单',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'添加日志失败'];
            }

            if($data['stockretankstatus']==2){
                $input['opertype']= 1;
                $input['operdesc']= '暂存倒罐单';
            }elseif($data['stockretankstatus']==3){
                $input['opertype']= 2;
                $input['operdesc']= '提交倒罐单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'添加日志失败'];
            }


            //回写附件对应转移单的id
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            if(count($attachment) > 0){
                $res = 	$A->addAttachModelSysno($id,$attachment);
                if(!$res){
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写附件失败'];
                }
            }
            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>"失败"];
        }
    }

    /*
     * 更新倒罐订单
     */
    public function updateRetank($id,$retank,$retankdetaildata,$stockretankstatus,$attachment)
    {
        $this->dbh->begin();
        try {
            $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $retankdata = $R->getstockretank($id);
            if($stockretankstatus == 3){
                $retank['stockretankstatus'] = 3;
            }elseif($stockretankstatus == 2&&$retankdata['stockretankstatus']==6){
                $retank['stockretankstatus'] = 6;
            }else{
                $retank['stockretankstatus'] = 2;
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_retank',$retank,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            // 删除明细表数据
            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_retank_detail', 'stockretank_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'删除明细失败'];
            }
            //循环插入详情表数据
            foreach ($retankdetaildata as $value) {
                $detail = array(
                    'stockretank_sysno' => $id,
                    'out_stock_sysno' => $value['out_stock_sysno'],
                    'bookingretankqty' => $value['bookingretankqty'],
                    'stockretankqty' => $value['stockretankqty'],
                    'instoragetank_sysno' => $value['instoragetank_sysno'],
                    'in_stock_sysno' => $value['in_stock_sysno'],
                    'stockretank_out_sysno'=>$value['stockretank_out_sysno'],
                    'stockretank_out_no'=>$value['stockretank_out_no'],
                    'stockretank_in_sysno'=>$value['stockretank_in_sysno'],
                    'stockretank_in_no'=>$value['stockretank_in_no'],
                    'goodsname'=>$value['goodsname'],
                    'qualityname'=>$value['qualityname'],
                    'unitname'=>$value['unitname'],
                    'goodsnature'=>$value['goodsnature'],
                    'tank_stockqty'=>$value['tank_stockqty'],
                    'stock_sysno'=>$value['stock_sysno'],
                    'customer_sysno' => $value['customer_sysno'],
                    'customername' => $value['customername'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'shipname' => $value['shipname'],
                    'memo' => $value['memo'],
                    'isdel' => 0,
                    'created_at' => "=NOW()",
                    'updated_at' => "=NOW()"
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_retank_detail', $detail);

                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'更新倒罐详细单失败'];
                }
                //倒罐回写移出罐商品规格覆盖基础表的商品规格
                $qulityname['qualityname'] = $value['qualityname'];
                $res = $this->dbh->update(DB_PREFIX.'base_storagetank',$qulityname,'sysno='.$value['stockretank_in_sysno']);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写商品规格失败'];
                }
                //添加附件
                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                if(count($attachment)>0){
                    $res = $A->addAttachModelSysno($id,$attachment);
                    if(!$res){
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'更新附件失败'];
                    }
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  12,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );
            if($stockretankstatus==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存倒罐单';
            }elseif($stockretankstatus==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交倒罐单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /*
    * 审核倒罐订单
    */
    public function auditRetank($id,$retank,$retankdetaildata,$stockretankstatus)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX . 'doc_stock_retank', $retank, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '审核失败'];
            }
            //审核通过
            if ($stockretankstatus == 4&&$retank['stocktype'] == 1) {
                foreach ($retankdetaildata as $value) {
                    //获取移入的罐容剩余量
                    $sql = "SELECT actualcapacity-tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = {$value['stockretank_in_sysno']}";
                    $lefttank = $this->dbh->select_one($sql);
                    if (!$lefttank) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询倒入罐剩余量失败'];
                    }
                    //获取移出的罐容剩余量
                    $sql = "SELECT tank_stockqty-orderoutqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = {$value['stockretank_out_sysno']}";
                    $leftouttank = $this->dbh->select_one($sql);
                    if (!$leftouttank) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询倒出罐剩余量失败'];
                    }
                    //获取库存数据
                    $sql = "SELECT * FROM " . DB_PREFIX . "storage_stock WHERE sysno = {$value['out_stock_sysno']}";
                    $stockdata = $this->dbh->select_row($sql);
                    if (!$stockdata) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询库存失败'];
                    }
                    if($value['stockretankqty']>$stockdata['stockqty']-$stockdata['clockqty']){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '库存不够，不能倒罐'];
                    }

                    //对于倒出罐来说如果倒出数量小于实际数量 并且 对于倒入罐来说如果储罐剩余可放数量大于倒入数量 则执行  并且 库存也够
                    if ($value['stockretankqty'] <= $leftouttank && $lefttank >= $value['stockretankqty']&&$value['stockretankqty']<=$stockdata['stockqty']-$stockdata['clockqty']) {
                        //更新倒出罐容
                        $sql = "SELECT tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = " . intval($value['stockretank_out_sysno']);
                        $out_tank_stockqty = $this->dbh->select_one($sql);
                        $outtank = array(
                            'tank_stockqty' => $out_tank_stockqty - $value['stockretankqty']
                        );
                        $res = $this->dbh->update(DB_PREFIX . 'base_storagetank', $outtank, 'sysno=' . intval($value['stockretank_out_sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '更新倒出罐失败'];
                        }
                        $sql = "SELECT tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = " . intval($value['stockretank_in_sysno']);
                        $in_tank_stockqty = $this->dbh->select_one($sql);
                        if (!$in_tank_stockqty) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '查询倒入罐失败'];
                        }
                        //更新倒入罐容
                        $intank = array(
                            'tank_stockqty' => $in_tank_stockqty + $value['stockretankqty']
                        );
                        $res = $this->dbh->update(DB_PREFIX . 'base_storagetank', $intank, 'sysno=' . intval($value['stockretank_in_sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '更新倒入罐失败'];
                        }
//                         var_dump($retankdetaildata);die();
                        //新增倒罐类型库存记录
                        $stockretank = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                        $params['data'] = [
                           'sysno'=>$value['out_stock_sysno'],
                           'instockqty'=>$value['stockretankqty'],
                           'retankdetail_sysno'=>$value['sysno'],
                           'stockretank_in_sysno'=>$value['stockretank_in_sysno'],
                        ];
                        $params['type'] = 4;
                        $res = $stockretank->pubstockoperation($params);
                        $instocksysno = $res;
                        if (!$res) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '新增倒罐库存记录失败'];
                        }

                        //回写出罐容记录表
                        $storagetank = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                        $outRecord = array(
                            'doc_sysno' => $value['stockretank_sysno'],
                            'docno' => $retank['stockretankno'],
                            'storagetank_sysno' => $value['stockretank_out_sysno'],
                            'beqty' => '-' . $value['stockretankqty'],
                            'doctype' => '10',
                        );
                        $resultOut = $storagetank->addStoragetankLog($outRecord);
                        if ($resultOut['code'] != 200) {
                            return ['statusCode' => 300, 'msg' => $resultOut['message']];
                        }
                        //回写入罐容记录表
                        $inRecord = array(
                            'doc_sysno' => $value['stockretank_sysno'],
                            'docno' => $retank['stockretankno'],
                            'storagetank_sysno' => $value['stockretank_in_sysno'],
                            'beqty' => $value['stockretankqty'],
                            'doctype' => '10',
                        );
                        $resIn = $storagetank->addStoragetankLog($inRecord);
                        if ($resIn['code'] != 200) {
                            return ['statusCode' => 300, 'msg' => $resIn['message']];
                        }

                        //根据储罐id获取货品id
                        $sql = "SELECT goods_sysno FROM ".DB_PREFIX."base_storagetank WHERE sysno = {$value['stockretank_in_sysno']}";
                        $goods_sysno = $this->dbh->select_one($sql);
                        if (!$goods_sysno) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '查询倒入罐货品失败'];
                        }
                        //添加储罐货品日志记录
                        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        $recordlogData = array(
                            'shipname' => $value['shipname'],
                            'goods_sysno' => $goods_sysno,
                            'goodsname' => $value['goodsname'],
                            'storagetank_sysno' => $value['stockretank_in_sysno'],
                            'storagetankname' => $value['stockretank_in_no'],
                            'customer_sysno' => $value['customer_sysno'],
                            'customername' => $value['customername'],
                            'beqty' => $value['stockretankqty'],
                            'stockin_sysno' => $value['stockin_sysno'],
                            'stockinno' => $value['stockin_no'],
                            'doc_sysno' => $id,
                            'docno' => $retank['stockretankno'],
                            'accountstoragetank_sysno' => $value['stockretank_in_sysno'],
                            'accountstoragetankname' => $value['stockretank_in_no'],
                            'tobeqty' => $value['stockretankqty'],
                            'doc_type' => 7,
                            'stock_sysno' => $instocksysno,
                            'goodsnature' => $value['goodsnature'],
                        );
                        //入罐
                        $log = $L->addGoodsRecordLog($recordlogData);
                        if ($log['code'] == 300) {
                            return ['statusCode' => 300, 'msg' => '入罐' . $log['message']];
                        }
                        //出罐-记录
                        $recordlogData['beqty'] = '-' . $value['stockretankqty'];
                        $recordlogData['storagetank_sysno'] = $value['stockretank_out_sysno'];
                        $recordlogData['storagetankname'] = $value['stockretank_out_no'];
                        $recordlogData['accountstoragetank_sysno'] = $value['stockretank_out_sysno'];
                        $recordlogData['accountstoragetankname'] = $value['stockretank_out_no'];
                        $recordlogData['doc_type'] = 8;
                        $recordlogData['stock_sysno'] = $value['out_stock_sysno'];

                        $log = $L->addGoodsRecordLog($recordlogData);
                        if ($log['code'] == 300) {
                            return ['statusCode' => 300, 'msg' => '出罐' . $log['message']];
                        }
                    } else {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '移出罐罐容不足或移入罐罐容超出'];
                    }
                }
            }elseif ($stockretankstatus == 4&&$retank['stocktype'] == 2) {
                foreach ($retankdetaildata as $value) {
                    //根据out_stock_sysno查询是倒库存还是倒提单
                    $sql = "SELECT * FROM hengyang_doc_introduction_detail WHERE isdel = 0 AND sysno = {$value['out_stock_sysno']} ";
                    $intsdata = $this ->dbh ->select_row($sql);
                    if(!$intsdata){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询提单明细失败'];
                    }
                    if($intsdata['untakegoodsnum'] - $intsdata['bookingqty'] - $value['stockretankqty'] <0){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '库存不够，不能倒罐'];
                    }

                    $lishi = $intsdata['introduction_sysno'];
                    $lishidetail = $intsdata['introductiondetail_sysno'];
                    $oldestock_sysno = $intsdata['stock_sysno'];

                    //根据stock_sysno 查询库存信息
                    $sql = "SELECT * FROM hengyang_storage_stock WHERE isdel = 0 AND sysno = {$intsdata['stock_sysno']} ";
                    $stockdata = $this ->dbh ->select_row($sql);
                    if(!$stockdata){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询库存失败'];
                    }

                    //更新原库存
                    $sql = "UPDATE hengyang_storage_stock SET instockqty = instockqty - {$value['stockretankqty']},introductionqty = introductionqty - {$value['stockretankqty']} WHERE sysno = {$intsdata['stock_sysno']}";
                    $this ->dbh ->exe($sql);
                    //根据firstfrom_sysno 查询该入库单是否有stockretank_in_sysno的库存
                    $sql = "SELECT sysno FROM hengyang_storage_stock WHERE isdel = 0 AND firstfrom_sysno = {$stockdata['firstfrom_sysno']} AND customer_sysno = {$stockdata['customer_sysno']}  AND storagetank_sysno = {$value['stockretank_in_sysno']}";
                    $newintsstocksyno = $this ->dbh ->select_one($sql);
                    if(!$newintsstocksyno){
                        unset($stockdata['sysno']);
                        unset($stockdata['instockqty']);
                        $stockdata['stockno'] = COMMON::getCodeId('S1') ;
                        $stockdata['doctype'] = 5 ;
                        $stockdata['instockqty'] = $value['stockretankqty'] ;
                        $stockdata['stockqty'] = 0 ;
                        $stockdata['checkqty'] = 0 ;
                        $stockdata['clockqty'] = 0 ;
                        $stockdata['clearqty'] = 0 ;
                        $stockdata['ullage'] = 0 ;
                        $stockdata['introductionqty'] = $value['stockretankqty'] ;
                        $stockdata['beyondqty'] = 0 ;
                        $stockdata['storagetank_sysno'] = $value['stockretank_in_sysno'] ;
                        $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockdata);
                        if (!$res) {
                            $this->dbh->rollback();
                            return  ['statusCode'=>300,'msg'=>'插入库存失败'];
                        }
                        $newintsstocksyno = $res;
                    }

                    //执行倒罐操作
                    //原提单明细减少
                    $sql = "UPDATE hengyang_doc_introduction_detail SET takegoodsnum = takegoodsnum - {$value['stockretankqty']},untakegoodsnum = untakegoodsnum - {$value['stockretankqty']} WHERE sysno = {$value['out_stock_sysno']}";
                    $this ->dbh ->exe($sql);
                    //添加倒罐后的提单明细
                    $data = $this ->getIntroduceDetailTree($intsdata['introductiondetail_sysno']);
                    $data = $this->list_sort_by($data, 'sysno', 'asc');
                    //如果父级没有这个储罐的介绍信 库存 则新建
                    foreach($data as $item) {
                        $sql = "SELECT * FROM hengyang_doc_introduction_detail WHERE isdel = 0 AND sysno = {$item['sysno']} AND storagetank_sysno = {$value['stockretank_in_sysno']}";
                        $res = $this->dbh->select_row($sql);
                        if (!$res) {
                            $sql = "SELECT * FROM hengyang_doc_introduction_detail WHERE isdel = 0 AND sysno = {$item['sysno']}";
                            $res = $this->dbh->select_row($sql);
                            if (!$res) {
                                $this->dbh->rollback();
                                return ['statusCode' => 300, 'msg' => '查询提单明细失败'];
                            }
                            $intsdata['introduction_sysno'] = $res['introduction_sysno'];

                            if($res['introductiondetail_sysno'] != 0){
                                //先找到上一家介绍信id
                                $sql = "SELECT introduction_sysno FROM hengyang_doc_introduction_detail WHERE isdel = 0 AND sysno = {$item['introductiondetail_sysno']}";
                                $intr_sysno = $this->dbh->select_one($sql);
                                if (!$intr_sysno) {
                                    $this->dbh->rollback();
                                    return ['statusCode' => 300, 'msg' => '查询提单明细失败'];
                                }
                                //再找到上一家介绍信 为新库存 新储罐的id
                                $sql = "SELECT * FROM hengyang_doc_introduction_detail did WHERE isdel = 0 AND introduction_sysno = $intr_sysno AND stock_sysno = $newintsstocksyno AND storagetank_sysno = {$value['stockretank_in_sysno']}";
                                $fres = $this->dbh->select_row($sql);
                                if (!$fres) {
                                    $this->dbh->rollback();
                                    return ['statusCode' => 300, 'msg' => '查询提单明细失败'];
                                }

                                $intsdata['introductiondetail_sysno'] = $fres['sysno'];
                            }else{
                                $intsdata['introductiondetail_sysno'] = 0;
                            }

                            $intsdata['takegoodsnum'] = $value['stockretankqty'];
                            $intsdata['untakegoodsnum'] = 0;
                            $intsdata['takegoodsqty'] = 0;
                            $intsdata['ullage'] = 0;
                            $intsdata['bookingqty'] = 0;
                            $intsdata['outqty'] = $value['stockretankqty']; //

                            unset($intsdata['sysno']);
                            $intsdata['stock_sysno'] = $newintsstocksyno;
                            $intsdata['storagetank_sysno'] = $value['stockretank_in_sysno'];
                            $intsdata['storagetankname'] = $value['stockretank_in_no'];
                            $intsdata['created_at'] = '=NOW()';
                            $intsdata['updated_at'] = '=NOW()';
                            $res = $this->dbh->insert(DB_PREFIX . 'doc_introduction_detail', $intsdata);
                            if (!$res) {
                                $this->dbh->rollback();
                                return ['statusCode' => 300, 'msg' => '添加提单明细失败'];
                            }

                            //原倒出罐减少
                            $sql = "UPDATE hengyang_doc_introduction_detail SET takegoodsnum = takegoodsnum - {$value['stockretankqty']},outqty = outqty - {$value['stockretankqty']} WHERE sysno = {$res['sysno']}";
                            $this ->dbh ->exe($sql);


                        }else{
                            $sql = "UPDATE hengyang_doc_introduction_detail SET takegoodsnum = takegoodsnum + {$value['stockretankqty']},outqty = outqty + {$value['stockretankqty']} WHERE sysno = {$res['sysno']}";
                            $this ->dbh ->exe($sql);
                        }
                    }

                    //添加本条倒罐明细
                    unset($intsdata['sysno']);
                    //先找到上一家介绍信id
                    if($lishidetail){
                        $sql = "SELECT introduction_sysno FROM hengyang_doc_introduction_detail WHERE isdel = 0 AND sysno = $lishidetail";
                        $intr_sysno = $this->dbh->select_one($sql);
                        if (!$intr_sysno) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '查询提单明细失败'];
                        }
                        //再找到上一家介绍信 为新库存 新储罐的id
                        $sql = "SELECT * FROM hengyang_doc_introduction_detail did WHERE isdel = 0 AND introduction_sysno = $intr_sysno AND stock_sysno = $newintsstocksyno AND storagetank_sysno = {$value['stockretank_in_sysno']}";
                        $fres = $this->dbh->select_row($sql);
                        if (!$fres) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '查询提单明细失败'];
                        }
                    }else{
                        $fres['sysno'] = 0;
                    }

                    $intsdata['introduction_sysno'] = $lishi;
                    $intsdata['introductiondetail_sysno'] = $fres['sysno'];

                    $intsdata['stock_sysno'] = $newintsstocksyno;
                    $intsdata['storagetank_sysno'] = $value['stockretank_in_sysno'];
                    $intsdata['storagetankname'] = $value['stockretank_in_no'];
                    $intsdata['takegoodsnum'] = $value['stockretankqty'];
                    $intsdata['untakegoodsnum'] = $value['stockretankqty'];
                    $intsdata['takegoodsqty'] = 0;
                    $intsdata['bookingqty'] = 0;
                    $intsdata['ullage'] = 0;
                    $intsdata['outqty'] = 0;
                    $intsdata['created_at'] = '=NOW()';
                    $intsdata['updated_at'] = '=NOW()';

                    $res = $this->dbh->insert(DB_PREFIX.'doc_introduction_detail', $intsdata);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'添加提单明细失败'];
                    }
                    //如果是不可撤销提单 添加record记录
                    //根据介绍信明细id查询改介绍信是可撤销还是不可撤销
                    $sql = "SELECT sysno,introductionno,introductiontype FROM hengyang_doc_introduction WHERE sysno = $lishi";
                    $introductiontype = $this ->dbh ->select_row($sql);
                    if (!$introductiontype) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'查询介绍信失败'];
                    }

                    if($introductiontype['introductiontype'] == 2){
                        $Log = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                        $sql = "SELECT goods_sysno FROM hengyang_storage_stock WHERE isdel = 0 AND sysno = $newintsstocksyno";
                        $goods_sysno = $this ->dbh ->select_one($sql);
                        if(!$goods_sysno){
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '查询库存失败'];
                        }

                        //货物进出记录 倒出罐
                        $recordlog = array(
                            'shipname'          => $value['shipname'],
                            'goods_sysno'       => $goods_sysno,
                            'goodsname'         => $value['goodsname'],
                            'storagetank_sysno' => $value['stockretank_out_sysno'],
                            'storagetankname'   => $value['stockretank_out_no'],
                            'customer_sysno'    => $value['customer_sysno'],
                            'customername'      => $value['customername'],
                            'beqty'             => - $value['stockretankqty'],
                            'tobeqty'           => $value['stockretankqty'],
                            'stockin_sysno'     => $value['sysno'],
                            'stockinno'         => $value['stockin_no'],
                            'doc_sysno'         => $id,
                            'docno'             => $retank['stockretankno'],
                            'accountstoragetank_sysno' => $value['stockretank_out_sysno'],
                            'accountstoragetankname'   => $value['stockretank_out_no'],
                            'doc_type'          => 22,       //提单出
                            'stock_sysno'       => $oldestock_sysno,
                            'goodsnature'       => $value['goodsnature'],
                            'takegoodscompany'  => '',
                            'takegoodsno'       => '',
                            'stocktype'         => 1,
                            'introduction_sysno' => $introductiontype['sysno'],
                            'introductionno'    => $introductiontype['introductionno'],
                        );

                        $res = $Log->addGoodsRecordLog($recordlog);
                        if(!$res){
                            $this->dbh->rollback();
                            return  ['statusCode'=>300,'msg'=>'插入货物进出日志失败'];
                        }
                        //货物进出记录 倒入罐
                        $recordlog = array(
                            'shipname'          => $value['shipname'],
                            'goods_sysno'       => $goods_sysno,
                            'goodsname'         => $value['goodsname'],
                            'storagetank_sysno' => $value['stockretank_in_sysno'],
                            'storagetankname'   => $value['stockretank_in_no'],
                            'customer_sysno'    => $value['customer_sysno'],
                            'customername'      => $value['customername'],
                            'beqty'             => $value['stockretankqty'],
                            'tobeqty'           => $value['stockretankqty'],
                            'stockin_sysno'     => $value['sysno'],
                            'stockinno'         => $value['stockin_no'],
                            'doc_sysno'         => $id,
                            'docno'             => $retank['stockretankno'],
                            'accountstoragetank_sysno' => $value['stockretank_in_sysno'],
                            'accountstoragetankname'   => $value['stockretank_in_no'],
                            'doc_type'          => 21,       //提单出
                            'stock_sysno'       => $newintsstocksyno,
                            'goodsnature'       => $value['goodsnature'],
                            'takegoodscompany'  => '',
                            'takegoodsno'       => '',
                            'stocktype'         => 1,
                            'introduction_sysno' => $introductiontype['sysno'],
                            'introductionno'    => $introductiontype['introductionno'],
                        );

                        $res = $Log->addGoodsRecordLog($recordlog);
                        if(!$res){
                            $this->dbh->rollback();
                            return  ['statusCode'=>300,'msg'=>'插入货物进出日志失败'];
                        }

                        //更新储罐基本信息表
                        $sql = "UPDATE hengyang_base_storagetank SET tank_stockqty = tank_stockqty - {$value['stockretankqty']},orderoutqty = orderoutqty - {$value['stockretankqty']} WHERE sysno = {$value['stockretank_out_sysno']}";
                        $this ->dbh ->exe($sql);
                        $sql = "UPDATE hengyang_base_storagetank SET tank_stockqty = tank_stockqty + {$value['stockretankqty']},orderoutqty = orderoutqty + {$value['stockretankqty']} WHERE sysno = {$value['stockretank_in_sysno']}";
                        $this ->dbh ->exe($sql);
                    }

                }
            }


            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  12,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $retank['auditreason'],
            );
            if($stockretankstatus==4)
            {
                $input['opertype'] = 3;
            }elseif($stockretankstatus==6){
                $input['opertype'] = 5;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    //获取明细上下级
    public function getIntroduceDetailTree($introductiondetail_sysno)
    {
        $sql = "SELECT sysno,introduction_sysno,introductiondetail_sysno,stock_sysno from ".DB_PREFIX."doc_introduction_detail";

        $data = $this->dbh->select($sql);
        return $this->_getIntroduceDetailTree($data,$introductiondetail_sysno);
    }

    private function _getIntroduceDetailTree($data, $introductiondetail_sysno)
    {
        $list = array();
        foreach ($data as $k => $v) {
            if ($v['sysno'] == $introductiondetail_sysno) {
                $list[] = $v;
                $this->_getIntroduceDetailTree($data,$v['introductiondetail_sysno']);

            }
        }
        return $list;
    }


    /*
     * 二维数组排序
     */
    public function list_sort_by($list, $field, $sortby = 'asc')
    {
        if (is_array($list))
        {
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
            {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby)
            {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val)
            {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }

    /*
     * 作废倒罐订单
     */
    public function backRetank($id,$retank,$retankdetaildata,$bookingretankno)
    {
        $this->dbh->begin();
        try {
            //回写倒罐单
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_retank',$retank,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'主表更新失败'];
            }
            //回写倒罐申请单
            $info = array(
                'issaveorder'=>0,
                'abandonreason'=>$retank['abandonreason']
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_retank',$info,'sysno='.$retank['bookingretank_sysno']);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'倒罐申请单更新失败'];
            }

            foreach($retankdetaildata as $key=>$value){
                if($retank['stocktype'] == 1){
                    //回写库存记录表
                    $info = array(
                        'isdel'=>1
                    );
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock',$info,'sysno='.$value['in_stock_sysno']);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'库存记录表更新失败'];
                    }
                    //获取移出罐库存记录信息
                    $sql = "SELECT outstockqty,stockqty FROM ". DB_PREFIX ."storage_stock WHERE sysno={$value['out_stock_sysno']}";
                    $data=$this->dbh->select_row($sql);
                    if (!$data) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询倒出罐剩余量失败'];
                    }

                    $out=array(
                        'outstockqty'=>$data['outstockqty']-$value['stockretankqty'],
                        'stockqty'=>$data['stockqty']+$value['stockretankqty'],
                    );
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock',$out,'sysno='.$value['out_stock_sysno']);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'倒出罐库存记录表更新失败'];
                    }


                    //获取移出的罐容剩余量
                    $sql = "SELECT (actualcapacity-tank_stockqty) AS lefttank,tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = {$value['stockretank_in_sysno']}";
                    $outtank = $this->dbh->select_row($sql);
                    if (!$outtank) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询倒出罐剩余量失败'];
                    }
                    $sql = "SELECT actualcapacity,tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = {$value['stockretank_out_sysno']}";
                    $intank = $this->dbh->select_row($sql);
                    if (!$intank) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '查询倒入罐总量失败'];
                    }
                    //对于倒出罐来说如果倒出数量小于实际数量 并且 对于倒入罐来说如果储罐剩余可放数量大于倒入数量 则执行
                    if ($value['stockretankqty'] <= $outtank['tank_stockqty'] && ($value['stockretankqty'] + $intank['tank_stockqty']) <= $intank['actualcapacity']) {
                        //更新倒出罐容
                        $outtankdata = array(
                            'tank_stockqty' => $outtank['tank_stockqty'] - $value['stockretankqty']
                        );
                        $res = $this->dbh->update(DB_PREFIX . 'base_storagetank', $outtankdata, 'sysno=' . intval($value['stockretank_in_sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '更新倒出罐失败'];
                        }
                        //更新倒入罐容
                        $intankdata = array(
                            'tank_stockqty' => $intank['tank_stockqty'] + $value['stockretankqty']
                        );
                        $res = $this->dbh->update(DB_PREFIX . 'base_storagetank', $intankdata, 'sysno=' . intval($value['stockretank_out_sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return ['statusCode' => 300, 'msg' => '更新倒入罐失败'];
                        }
                        //回写出罐容记录表
                        $storagetank = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                        $outRecord = array(
                            'doc_sysno'=>$value['stockretank_sysno'],
                            'docno'=>$bookingretankno,
                            'storagetank_sysno'=>$value['stockretank_out_sysno'],
                            'beqty'=>$value['stockretankqty'],
                            'doctype'=>'12',
                        );
                        $resultOut = $storagetank->addStoragetankLog($outRecord);
                        if($resultOut['code'] != 200){
                            return  ['statusCode'=>300,'msg'=>$resultOut['message']];
                        }
                        //回写入罐容记录表
                        $inRecord = array(
                            'doc_sysno'=>$value['stockretank_sysno'],
                            'docno'=>$bookingretankno,
                            'storagetank_sysno'=>$value['stockretank_in_sysno'],
                            'beqty'=>'-'.$value['stockretankqty'],
                            'doctype'=>'12',
                        );
                        $resIn = $storagetank->addStoragetankLog($inRecord);
                        if($resIn['code'] != 200){
                            return  ['statusCode'=>300,'msg'=>$resIn['message']];
                        }
                    }else{
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'移出罐罐容不足或移入罐罐容超出'];
                    }
                }else{
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'提单倒罐不能作废'];
                }

            }

            //删除进出货物记录表数据
            $recodeInfo = array(
                'isdel' =>1,
                'updated_at'=>'=NOW()'
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',$recodeInfo,' doc_type in(7,8) and doc_sysno='.$id);
            if(!$res)
            {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'删除进出货物记录失败'];
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  12,
                'opertype'  => 4,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $retank['abandonreason'],
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }
            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>"$id"];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /**
     * 删除倒罐订单
     */
    public function delRetank($id)
    {
        $this->dbh->begin();
        try {
            $sql = "SELECT bookingretank_sysno,stockretankstatus from ".DB_PREFIX."doc_stock_retank where sysno = ".$id;
            $data = $this->dbh->select_row($sql);
            if($data['stockretankstatus']==1 || $data['stockretankstatus']==2||$data['stockretankstatus']==6){
                $params = array(
                    'isdel' => 1,
                    'updated_at' => '=NOW()'
                );
                $res  =  $this->dbh->update(DB_PREFIX.'doc_stock_retank', $params, 'sysno=' . intval($id));
                if(!$res){
                    $this->dbh->rollback();
                    return  false;
                }
            }else{
                return false;
            }

            //更新申请单issaveorder
            $bookdata = array(
                'issaveorder' => 0
            );
            $res  = $this->dbh->update(DB_PREFIX.'doc_booking_retank', $bookdata, 'sysno=' . intval($data['bookingretank_sysno']));
            if(!$res){
                $this->dbh->rollback();
                return  false;
            }

            //更新管线单据
            $sql = "SELECT sysno FROM ".DB_PREFIX."doc_pipelineorder WHERE businesstype = 18 AND booking_sysno = {$data['bookingretank_sysno']}";
            $pipid = $this->dbh->select_one($sql);
            if(!empty($pipid)){
                $pip = array(
                    'businesstype'=>17,
                    'stock_sysno' =>0,
                    'stockno' =>'',
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$pip,'sysno='.$pipid);
                if (!$res) {
                    $this->dbh->rollback();
                    return  false;
//                    return  ['statusCode'=>300,'msg'=>'回写管线失败'];
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  12,
                'opertype'  => 6,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  "删除",
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'日志更新失败'];
            }
            $this->dbh->commit();
            return  $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /**
     *  获取 库存详情信息
     *  @author Alan
     *  @params ID,入库单号,入库日期,货品名,客户名,储罐编号,船名
     */
    public function getStockInfo()
    {
        $sql = "SELECT ss.sysno,ss.instockdate,ss.shipname,ss.customer_sysno,ss.customername,ss.goods_sysno,bg.goodsname,ss.storagetank_sysno,bs.storagetankname,ss.stockqty,gq.qualityname,ss.doctype FROM ".DB_PREFIX."storage_stock AS ss
        LEFT JOIN ".DB_PREFIX."base_goods as bg ON bg.sysno = ss.goods_sysno 
        LEFT JOIN ".DB_PREFIX."base_storagetank as bs ON bs.sysno = ss.storagetank_sysno
        LEFT JOIN ".DB_PREFIX."base_goods_quality as gq ON gq.sysno = ss.goods_quality_sysno
        WHERE ss.`status` = 1 AND ss.isdel = 0 
        AND ss.iscurrent = 1";
        return $this->dbh->select($sql);
    }

    /**
     * 获取 对应品名、客户的倒罐库存记录
     */
    public function getRetankStockInfo($goods_sysno,$customer_sysno)
    {
        $sql="select ss.*,bs.storagetankname,bs.qualityname,'吨' AS unitname,si.release_num,sid.unrelease_num from ".DB_PREFIX."storage_stock ss
        left join ".DB_PREFIX."base_storagetank bs on ss.storagetank_sysno = bs.sysno
        left join ".DB_PREFIX."base_goods bg on bg.sysno = bs.goods_sysno
        left join ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = bg.sysno
        left join ".DB_PREFIX."doc_stock_in si ON si.sysno = ss.firstfrom_sysno
        left join ".DB_PREFIX."doc_stock_in_declare sid ON sid.stockin_sysno = si.sysno
        where ss.stockqty > 0 and ss.goods_sysno = ".$goods_sysno." and ss.customer_sysno = ".$customer_sysno." AND ga.`status` = 1 AND ga.isdel = 0 AND  ss.`status` = 1 AND ss.isdel = 0 AND ss.iscurrent = 1
        group by ss.sysno";
        return $this->dbh->select($sql);
    }

    /**
     * 获取 对应品名、客户的倒罐介绍信库存记录
     */
    public function getRetankintsStockInfo($goods_sysno,$customer_sysno)
    {
        $sql = "SELECT did.sysno AS sysno,di.introductionno AS stockin_no,di.introductiondate AS instockdate,di.buy_customer_sysno customer_sysno,di.buy_customername customername,did.shipname,did.storagetank_sysno,did.storagetankname,did.goods_sysno,did.goodsname,did.goodsqualityname AS qualityname,'吨' AS unitname,did.takegoodsnum AS instockqty,0 AS clockqty,did.untakegoodsnum AS tank_stockqty,did.takegoodsnum AS release_num,did.goodsnature
                FROM hengyang_doc_introduction di
                LEFT JOIN hengyang_doc_introduction_detail did ON did.introduction_sysno = di.sysno
                WHERE di.introductionstatus = 4 AND di.buy_customer_sysno = ".$customer_sysno." AND did.goods_sysno = ".$goods_sysno." and did.untakegoodsnum > 0 and di.`status` = 1 AND di.isdel = 0";
        return $this->dbh->select($sql);
    }

    /**
     *  获取 罐容详情信息
     */
    public function getStockRetankInfo()
    {
        $sql = "SELECT bs.*,bg.goodsname,u.unitname
                FROM ".DB_PREFIX."base_storagetank bs
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = bg.sysno
                LEFT JOIN ".DB_PREFIX."base_unit u ON u.sysno = ga.unit_sysno
                WHERE ga.`status` = 1 AND ga.isdel = 0 AND  bs.`status` = 1 AND bs.isdel = 0 ORDER BY bs.sysno ASC";

        return $this->dbh->select($sql);
    }

    /*
     *获取储罐信息
     */
    public function getStockViceRetankInfo($id){
        if ($id){
            $sql = "SELECT bs.*,bg.goodsname,u.unitname
                FROM ".DB_PREFIX."base_storagetank bs
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = bg.sysno
                LEFT JOIN ".DB_PREFIX."base_unit u ON u.sysno = ga.unit_sysno
                WHERE bs.goods_sysno=".$id." AND ga.`status` = 1 AND ga.isdel = 0 AND  bs.`status` = 1 AND bs.isdel = 0 ORDER BY bs.sysno ASC";

            return $this->dbh->select($sql);
        }else{
            $this->getStockRetankInfo();
        }

    }

    /**
     * 获取入库单号
     * @author Alan 
     * 库存表->明细->所属入库单->获取编号,货物性质
     */
    public function getStockInDetail($id)
    {
        $sql = "SELECT si.stockinno,sid.goodsnature,st.stocktransno,ss.doctype FROM ".DB_PREFIX."storage_stock as ss
        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail as sid ON sid.stock_sysno = ss.sysno 
        LEFT JOIN ".DB_PREFIX."doc_stock_in as si ON si.sysno = sid.stockin_sysno 
        LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail as std ON std.in_stock_sysno = ss.sysno 
        LEFT JOIN ".DB_PREFIX."doc_stock_trans as st ON st.sysno = std.stocktrans_sysno 
        WHERE ss.sysno = ".$id." AND ss.`status` = 1 AND ss.isdel = 0 AND ss.iscurrent = 1";
        return $this->dbh->select_row($sql);
    }

    /**
     * 获取 移入的储罐号
     */
    public function getinstoragetankById($id)
    {
        $sql = "SELECT storagetankname FROM ".DB_PREFIX."base_storagetank WHERE sysno = ".intval($id);
        return $this->dbh->select_row($sql);
    }

    /*
     * 获取货罐基础信息
     * */
    public function getBaseRetank($id)
    {
        if($id){
            $sql = "SELECT * from ".DB_PREFIX."base_storagetank
                    WHERE sysno=".$id;
            return $this->dbh->select_row($sql);
        }else{
            return array();
        }
    }

    /*
     *
     */
    public function getBaseViceRetank($id,$gsysno){
        if($id){
            if ($gsysno){
                $sql = "SELECT * from ".DB_PREFIX."base_storagetank
                    WHERE sysno=".$id." and goods_sysno=".$gsysno;
                return $this->dbh->select_row($sql);
            }else{
                $sql = "SELECT * from ".DB_PREFIX."base_storagetank
                    WHERE sysno=".$id;
                return $this->dbh->select_row($sql);
            }

        }else{
            return array();
        }
    }

    public function getCustomerBygoodssyno($goods_sysno){
        $sql="SELECT customer_sysno,customername FROM `".DB_PREFIX."storage_stock`
              WHERE goods_sysno = $goods_sysno
              GROUP BY customer_sysno
              UNION
              SELECT di.buy_customer_sysno customer_sysno,di.buy_customername customername FROM hengyang_doc_introduction di
              LEFT JOIN hengyang_doc_introduction_detail did ON did.introduction_sysno = di.sysno
              WHERE did.goods_sysno = $goods_sysno GROUP BY di.buy_customer_sysno ";
        return $this->dbh->select($sql);
    }
}
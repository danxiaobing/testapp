<?php
/**
 * Created by PhpStorm.
 * User: ty
 * Date: 2016/11/17 0017
 * Time: 19:52
 */
class StocktransModel
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
     * 获取列表
     */
    public function getList($params=null){
        $pageSize = $params['pageSize'];
        $pageCurrent = $params['pageCurrent'] ;


        //日期
        if (isset($params['stocktransdate_start']) && $params['stocktransdate_start'] != '') {
            $filter[] = " trans.`stocktransdate` >= '{$params['stocktransdate_start']}' ";
        }
        if (isset($params['stocktransdate_end']) && $params['stocktransdate_end'] != '') {
            $filter[] = " trans.`stocktransdate` <= '{$params['stocktransdate_end']}' ";
        }
        //客户
        if (isset($params['sale_customer_sysno']) && $params['sale_customer_sysno'] != '') {
            $filter[] = " (trans.`sale_customer_sysno` = '{$params['sale_customer_sysno']}' OR  trans.`buy_customer_sysno` = '{$params['sale_customer_sysno']}') ";
        }

        //单据状态
        if (isset($params['stocktransstatus']) && $params['stocktransstatus'] != '') {
            $filter[] = " trans.`stocktransstatus` = '{$params['stocktransstatus']}' ";
        }
        //单据来源
        if (isset($params['docsource']) && $params['docsource'] != '') {
            $filter[] = " trans.`docsource` = '{$params['docsource']}' ";
        }
        //转让方
        if (isset($params['sale_customer_sysno']) && $params['sale_customer_sysno'] != '') {
            $filter[] = " trans.`sale_customer_sysno` = '{$params['sale_customer_sysno']}'";
        }
        //受让方
        if (isset($params['buy_customer_sysno']) && $params['buy_customer_sysno'] != '') {
            $filter[] = " trans.`buy_customer_sysno` = '{$params['buy_customer_sysno']}'";
        }
        if(!$params['orders']){
            $params['orders'] = " trans.`updated_at` DESC";
        }

        $where ='trans.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT count(*) FROM ".DB_PREFIX."doc_stock_trans trans
                WHERE {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if($result['totalRow']){
            $sql = "SELECT trans.*,detail.goodsname,sum(qty) as qty,goodsnature,detail.shipname as shipname
                    FROM ".DB_PREFIX."doc_stock_trans trans
            LEFT JOIN (
            SELECT detail.stocktrans_sysno,detail.out_stock_sysno,stock.goodsname,stock.goodsnature,sum(detail.transqty) qty,group_concat(DISTINCT detail.shipname) as shipname FROM ".DB_PREFIX."doc_stock_trans_detail detail
            LEFT JOIN ".DB_PREFIX."storage_stock stock ON(detail.out_stock_sysno=stock.sysno) and stock.iscurrent=1
            GROUP BY detail.stocktrans_sysno
            ) detail ON(trans.sysno=detail.stocktrans_sysno)            
            WHERE {$where}
            group by trans.sysno";
            // if ($params['orders'] != '') {
            //     $sql .= " order by " . $params['orders'];
            // }
            $sql .=  " ORDER BY trans.`created_at` DESC";
            if($pageSize){
                $result['totalPage'] = ceil($result['totalRow'] / $pageSize);
                $this->dbh->set_page_num($pageCurrent);
                $this->dbh->set_page_rows($pageSize);
                $result['list'] = $this->dbh->select_page($sql);
            }else{
                $result['list'] = $this->dbh->select($sql);
            }

        }

        return $result;
    }

    /**
     * 返回列表详细信息
     */
    public function getListDetials($data){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_stock_trans_detail ";
        $where = 'WHERE ';
        if($data){
            $fileds = '';
            foreach ($data as $k=>$val){
                $fileds .= " AND $k='$val'";
            }
            $where .= substr($fileds,4);
            $sql .= $where;
        }

        $list = $this->dbh->select($sql);
        if(!$list)return null;

        //查询库存表信息，然后合并信息
        $ids = [];
        $new_list = [];
        foreach ($list as $item){
            $ids[] = $item['out_stock_sysno'];
        }

        $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        //   $infos = $stock->getElementById($ids);
        $infos = $this->getStockdetailById($ids);
        foreach ($infos as $item){
            foreach ($list as $k=>$val){
                // echo "{$item['sysno']} == {$val['out_stock_sysno']}<br/>";
                // $stockininfo = $stock->getStockinnoByStockId($item['sysno']);
                $stockin_no = $item['stockinno'];
                //     $stockin_no = $stockininfo[0]['stockin_no'];
                if($item['sysno'] == $val['out_stock_sysno']){
                    $list[$k]['stock_sysno'] = $item['sysno'];
                    $list[$k]['stockno'] = $item['stockno'];
                    $list[$k]['stockin_no'] = $stockin_no;
                    $list[$k]['instockdate'] = $item['instockdate'];
                    $list[$k]['goodsname'] = $item['goodsname'];
                    $list[$k]['goods_sysno'] = $item['goods_sysno'];
                    $list[$k]['qualityname'] = $item['qualityname'];
                    $list[$k]['goodsnature'] = $item['goodsnature'];
                    $list[$k]['instockqty'] = $item['instockqty'];
                    $list[$k]['stockqty'] = $item['stockqty']-$item['clockqty'];
                    //$list[$k]['storagetankname'] = $item['storagetankname'];
                    $list[$k]['unitname'] = $item['unitname'];
                    $list[$k]['firstfrom_sysno'] = $item['firstfrom_sysno'];
                    $list[$k]['contract_sysno'] = $item['contract_sysno'];
                    $list[$k]['shipname'] = $item['shipname']?$item['shipname']:$val['shipname'];
                    $list[$k]['release_num'] = $item['release_num'];
                    $list[$k]['unrelease_num'] = $item['unrelease_num'];
                    $list[$k]['goodsnature'] = $item['goodsnature'];
                    $list[$k]['tank_stockqty'] = $item['tank_stockqty'];
                }
            }

        }
        return $list;
    }

    /*
     * @title
     * @author wu xianneng
     */
    public function searchCumstomerContract($parms)
    {
        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '0') {
            $filter[] = "dc.`customer_id` = {$params['customer_sysno']} ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '0') {
            $filter[] = "cg.`contract_sysno` = {$params['contract_sysno']} ";
        }
        if (isset($params['contractstatus']) && $params['contractstatus'] != '') {
            $filter[] = "dc.`contractstatus` = {$params['contractstatus']} ";
        }


        $where = 'WHERE dc.contractstatus=6 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql="SELECT cu.customername,dc.contractno,bg.sysno,bg.goodsno,bg.goodsname,cs.storagetank_sysno,bs.storagetankname
			FROM ".DB_PREFIX."customer cu 
			LEFT JOIN ".DB_PREFIX."doc_contract dc ON dc.customer_id=cu.sysno
			LEFT JOIN ".DB_PREFIX."doc_contract_goods cg ON cg.contract_sysno=dc.sysno
			LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=cg.goods_sysno 
			LEFT JOIN ".DB_PREFIX."doc_contract_storagetank cs ON cs.contract_sysno=dc.sysno
			LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno=cs.storagetank_sysno ".$where;

        return $this->dbh->select($sql);

    }

    /**
     * 根据id获得片区细节
     * id: 权限id
     * @return 数组
     */
    public function getTransById($id = 0)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."doc_stock_trans WHERE sysno = $id ";

        return $this->dbh->select_row($sql);
    }
    /**
     * 添加货权转移
     * @param array $data
     */
    public function add($data,$detail,$stockmarks='',$booktrans_sysno=0,$attachment=null){
        $this->dbh->begin();

        //主表
        $resid =  $this->addTrans($data);
        if(!$resid){
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'添加主表信息失败'];
        }
        //添加保存时待生成预约单变成预约单，不能重复生成
//        $sql = "SELECT * from ".DB_PREFIX."doc_booking_trans where  sysno =".intval($data['bookingtrans_sysno']);
//        $booktrans = $this->dbh->select_row($sql);
//        if (!$booktrans) {
//            $this->dbh->rollback();
//            return ['statusCode'=>300,'msg'=>'查询预约单失败'];
//        }
//        if($booktrans['issaveorder']){
//            $this->dbh->rollback();
//            return ['statusCode'=>300,'msg'=>'该预约单已生成货权转移单，不可重复生成'];
//        }


        //详细信息
        // print_r($detail);die;
        foreach($detail as $val){
            $item = [
                'stocktrans_sysno' => $resid,
                'out_stock_sysno' => $val['out_stock_sysno'],
                'transqty' => $val['transqty'],
                'storagetankname'=>$val['storagetankname'],
                'storagetank_sysno'=>$val['storagetank_sysno'],
                'shipname'=>$val['shipname'],
                'memo' => $val['memo'],
                'updated_at'=>'=NOW()',
            ];
            //待货权转移
//            if($booktrans_sysno){
//                $item['out_stock_sysno'] = $val['stock_sysno'];
//            }
            //print_r($item);exit;

            $res = $this->addTransDetail($item);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加明细表信息失败'];
            }

        }
        /**
         * 判断
         */
        if($booktrans_sysno){
            $bookdata = array('bookingtransstatus'=>'5','issaveorder'=>'1','stock_trans_sysno'=>$resid);
            $book = new BooktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $res = $book->updateTrans($booktrans_sysno,$bookdata);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'修改待货权转移单状态失败'];
            }

        }

        //日志
        $user = Yaf_Registry::get(SSN_VAR);
        $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $input= array(
            'doc_sysno'  =>  $resid,
            'doctype'  =>  9,
            'opertype'  => 0,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime'    => '=NOW()',
            'operdesc'  =>  $stockmarks,
        );
        $res = $S->addDocLog($input);
        if (!$res) {
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'添加操作日志失败-新建'];
        }

        if ($data['stocktransstatus'] == 3) {
            $input['opertype'] = 2;
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加操作日志失败-提交'];
            }
        }else{
            $input['opertype'] = 1;
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加操作日志失败-保存'];
            }
        }

        //回写附件对应转移单的id
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(count($attachment) > 0){
            $res = 	$A->addAttachModelSysno($resid,$attachment);
            if(!$res){
                return COMMON::result(300,'添加附件失败');
            }
        }


        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>'操作成功'];
    }
    /**
     * 修改货权转移
     * @param int $id
     * @param array $data
     */
    public function update($id,$data,$detail,$stockmarks='',$attachment=null,$ststatus){
        $this->dbh->begin();
        //主表
        if($data['stocktransstatus']==2 && $ststatus==6){
            $data['stocktransstatus'] = 6;
        }
        $res =  $this->updateTrans($id,$data);
        if(!$res){
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'修改货权转移信息失败'];
        }

        $res = $this->deleteTransDetail($id);
        if(!$res){
            $this->dbh->rollback();
            return array('statusCode'=>300,'msg'=>'删除入库明细失败');
        }
        $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //详细信息
        foreach($detail as $val){
            //数据
            $item = [
                'stocktrans_sysno' => $id,
                'out_stock_sysno' => $val['out_stock_sysno'],
                'transqty' => $val['transqty'],
                'storagetankname'=>$val['storagetankname'],
                'storagetank_sysno'=>$val['storagetank_sysno'],
                'shipname'=>$val['shipname'],
                'memo' => $val['memo'],
                'updated_at'=>'=NOW()',
            ];


            $res = $this->addTransDetail($item);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加入库明细失败'];
            }

        }

        //日志
        $user = Yaf_Registry::get(SSN_VAR);
        $sys = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $input= array(
            'doc_sysno'  =>  $id,
            'doctype'  =>  9,
            'opertype'  => $data['stocktransstatus']-1,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime'    => '=NOW()',
            'operdesc'  =>  $stockmarks,
        );
        //print_r($input);exit;
        $res = $sys->addDocLog($input);
        if (!$res) {
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'写入日志失败'];
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

        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>'操作成功'];
    }
    /**
     * 审核数据
     * @param int $id
     * @param int $status
     * @param string $stockmarks
     * @return multitype:number string
     */
    public function audit($id,$status,$stockmarks='',$rejectreason='',$costdata=array()){
        $this->dbh->begin();
        //主表
        $data['stocktransstatus'] = $status;
        if($data['stocktransstatus']==6){//
            $data['updated_at'] = '=NOW()';
            $data['auditreason'] = $stockmarks;
            $res =  $this->dbh->update(DB_PREFIX.'doc_stock_trans', $data, 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'审核不通过失败'];
            }
        }

        //驳回
        if($data['stocktransstatus']==8){
            $data['updated_at'] = '=NOW()';
            $data['rejectreason'] = $rejectreason;
            // print_r($data);die;
            $res =  $this->dbh->update(DB_PREFIX.'doc_stock_trans', $data, 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'驳回失败'];
            }
            //调接口回写交易平台驳回状态
            $Trans_info = $this->getTransById($id);
            $transstockno = $Trans_info['stocktransno'];
            $res = COMMON::edittransReject($transstockno,$rejectreason);
            if($res['code']==300){
                return ['statusCode'=>300,'msg'=>'驳回失败-'.$res['message']];
            }
            if($res['code']==200){
                //日志
                $user = Yaf_Registry::get(SSN_VAR);
                $sys = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                $input= array(
                    'doc_sysno'  =>  $id,
                    'doctype'  =>  9,
                    'opertype'  => $data['stocktransstatus']-1,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime'    => '=NOW()',
                    'operdesc'  =>  $rejectreason,
                );
                //print_r($input);exit;
                $res = $sys->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['statusCode'=>300,'msg'=>'写入日志失败'];
                }
                $this->dbh->commit();
                return ['statusCode'=>200,'msg'=>'驳回成功'];
            }

        }

        $info = $this->getTransById($id);
        //获取详细数据
        $where['stocktrans_sysno'] = $id;
        $detail = $this->getListDetials($where);
        $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if($data['stocktransstatus']==4){
            foreach($detail as $val){
                //审核通过

                $params = ['type'=>7,'data'=>['sysno'=>$val['out_stock_sysno']]];
                $ret = $stock->pubstockoperation($params);
                //库存足够
                if($ret >= $val['transqty']){
                    $params = [ 'type' => 3, 'changetype' => 5,];

                    //插入选择合同方的sysno
                    if($info['cost_contract_type']==1){
                        $params['contract_sysno'] = $info['sale_contract_sysno'];//卖家合同id
                    }elseif($info['cost_contract_type']==2){
                        $params['contract_sysno'] = $info['contract_sysno'];//买家合同id
                    }

                    $searchdata = [
                        'sysno' => $val['out_stock_sysno'],
                        'outstockqty' => $val['transqty'],
                        'contract_sysno'=>$info['contract_sysno'],
                        'stocktransdate'=>$info['stocktransdate'],
                    ];
                    $params['data'] = $searchdata;
                    $params['customer_buy'] = $info['buy_customer_sysno'];
                    //print_r($params);exit;
                    $result = $stock->pubstockoperation($params);
                    if ($result['code'] == 200) {
                        $res_stockid = $result['message'];
                    }else{
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>$result['message']];
                    }
                    //写入stockid
                    $item['in_stock_sysno'] = $res_stockid;
                    $res = $this->updateTransDetail($val['sysno'],$item);
                    if(!$res){
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'修改入库id失败'];
                    }
                    // $params['qty']= $params['qty']+ $detail[$key]['transqty'];
                    $transqty['qtys'] =floatval($transqty['qtys'])+floatval($val['transqty']);

                    //添加储罐货品日志记录
                    $L = new LogModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                    //买方+记录
                    $recordlogData = array(
                        'shipname'          => $val['shipname'],
                        'goods_sysno'       => $val['goods_sysno'],
                        'goodsname'         => $val['goodsname'],
                        'storagetank_sysno' => $val['storagetank_sysno'],
                        'storagetankname'   => $val['storagetankname'],
                        'customer_sysno'    => $info['buy_customer_sysno'],
                        'customername'      => $info['buy_customername'],
                        'beqty'             => $val['transqty'],
                        'stockin_sysno'     => $val['firstfrom_sysno'],
                        'stockinno'         => $val['stockin_no'],
                        'doc_sysno'         => $id,
                        'docno'             => $info['stocktransno'],
                        'accountstoragetank_sysno' => $val['storagetank_sysno'],
                        'accountstoragetankname'   => $val['storagetankname'],
                        'tobeqty'                 =>$val['transqty'],
                        'doc_type'          => 5,
                        'stock_sysno'       =>$res_stockid,
                        'ullage'            =>0,
                        'takegoodscompany'  =>'',
                        'goodsnature'       =>$val['goodsnature'],
                        'takegoodsno'       =>'',
                        'father_stock_sysno'=>$val['out_stock_sysno'],
                        'status'            => 1,
                        'isdel'             => 0,
                        'created_at'        =>'=NOW()',
                        'updated_at'        =>'=NOW()',
                    );
                    $log = $L->addGoodsRecordLog($recordlogData);
                    if($log['code']==300){
                        return ['statusCode'=>300,'msg'=>'买方'.$log['message']];
                    }
                    //卖方-记录
                    $recordlogData['beqty'] = '-'.$val['transqty'];
                    $recordlogData['customer_sysno'] = $info['sale_customer_sysno'];
                    $recordlogData['customername'] = $info['sale_customername'];
                    $recordlogData['doc_type'] = 6;
                    $recordlogData['stock_sysno']=$val['out_stock_sysno'];

                    $log = $L->addGoodsRecordLog($recordlogData);
                    if($log['code']==300){
                        return ['statusCode'=>300,'msg'=>'卖方'.$log['message']];
                    }

                    //生成货权转移超期费begin
                    $costdata['stock_sysno'] = $res_stockid;
                    $costdata['datenum'] = 0;

                    if($costdata['freedate']!=0)
                    {
                        $firstdate = $this->getStockfirstdate($costdata['stockin_sysno']);
                        $differencedate  = (strtotime($costdata['buystartdate'])-(max([strtotime($firstdate),strtotime(date("Y-m-d"))])) )/3600/24;
                        if($differencedate>0)
                        {
                            $costdata['datenum'] = $differencedate;
                        }
                    }
                    unset($costdata['freedate']);
                    $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                    $res = $F->costover($costdata);
                    if(!$res){
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'调用生成货权转移超期费失败!'];
                    }

                    //生成货权转移超期费end

                    //国烨云仓过来数据调用
                    if($info['docsource']==2){
                        $edittransStatus = COMMON::edittransStatus($info['stocktransno'], $transqty['qtys']);
                        if(!$edittransStatus){
                            return ['statusCode'=>300,'msg'=>'调用失败'];
                        }
                    }

                }else{
                    return ['statusCode'=>300,'msg'=>'库存不足,当前可用库存：'.$ret];
                }
            }


            //根据转移详细表数量回写货权转移数量
            if($transqty)
            {
                $data['transqty'] = $transqty['qtys'];
                $data['updated_at'] = '=NOW()';
                $data['auditreason'] = $stockmarks;
                $res =  $this->dbh->update(DB_PREFIX.'doc_stock_trans', $data, 'sysno=' . intval($id));
                if(!$res)
                {
                    $this->dbh->rollback();
                    return ['statusCode'=>300,'msg'=>'写入总数失败'];
                }
            }
        }




//        print_r($transqty['qtys']);die;
        if(!trim($stockmarks)){
            if($status==1) $stockmarks = '新建';
            if($status==2) $stockmarks = '暂存';
            if($status==3) $stockmarks = '已提交';
            if($status==4) $stockmarks = '已审核';
            if($status==5) $stockmarks = '已完成';
            if($status==6) $stockmarks = '作废';
            //print_r($data);exit;
        }

        //日志
        $user = Yaf_Registry::get(SSN_VAR);
        $sys = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $input= array(
            'doc_sysno'  =>  $id,
            'doctype'  =>  9,
            'opertype'  => $data['stocktransstatus']-1,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime'    => '=NOW()',
            'operdesc'  =>  $stockmarks,
        );
        //print_r($input);exit;
        $res = $sys->addDocLog($input);
        if (!$res) {
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'写入日志失败'];
        }
        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>'操作成功'];
    }

    public function addTrans($data){
        $data['created_at'] = '=NOW()';
        $data['updated_at'] = '=NOW()';
        return $this->dbh->insert(DB_PREFIX.'doc_stock_trans', $data);
    }

    public function updateTrans($id = 0, $data = array())
    {
        $data['updated_at'] = '=NOW()';
        return $this->dbh->update(DB_PREFIX.'doc_stock_trans', $data, 'sysno=' . intval($id));
    }
    /*
     * 删除
     *
     * */
    public function deleteTrans($id = 0, $data = array())
    {
        $sql = "SELECT stocktransstatus from ".DB_PREFIX."doc_stock_trans where sysno = ".$id;
        $stocktrankstatus = $this->dbh->select_one($sql);
        if ($stocktrankstatus == 1 || $stocktrankstatus == 2 || $stocktrankstatus == 6) {
            $data['updated_at'] = '=NOW()';
            return $this->dbh->update(DB_PREFIX.'doc_stock_trans', $data, 'sysno=' . intval($id));
        }else{
            return false;
        }


    }
    /**
     * 添加货权转移详细
     * @param array $data
     */
    public function addTransDetail($data){
        $data['created_at'] = '=NOW()';
        return $this->dbh->insert(DB_PREFIX.'doc_stock_trans_detail', $data);
    }
    /**
     * 修改货权转移详细
     * @param int $id
     * @param array $data
     */
    public function updateTransDetail($id = 0, $data = array()){
        $data['updated_at'] = '=NOW()';
        return  $this->dbh->update(DB_PREFIX.'doc_stock_trans_detail', $data, 'sysno=' . intval($id));
    }

    public function deleteTransDetail($id = 0, $data = array()){
        return  $this->dbh->delete(DB_PREFIX.'doc_stock_trans_detail',"stocktrans_sysno='$id'");
    }

    public function getGoodaSysno($goodsname){
        if(isset($goodsname)){
            $sql = "select sysno from ".DB_PREFIX."base_goods where goodsname ='".$goodsname."'";
            return $this->dbh->select_one($sql);
        }else{
            return null;
        }
    }
    /*
     * 重新获取库存的方法
     * */
    public function getStockdetailById($ids){
        $ids_str = implode(',', $ids);
        $sql = "SELECT DISTINCT s.*,si.stockinno,st.storagetankname,st.tank_stockqty,q.qualityname,u.unitname,si.release_num,si.unrelease_num
        FROM ".DB_PREFIX."storage_stock s
        LEFT JOIN ".DB_PREFIX."doc_stock_in si ON (s.firstfrom_sysno=si.sysno)
        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail d ON(s.sysno=d.stock_sysno AND d.isdel='0')
        LEFT JOIN ".DB_PREFIX."base_goods goods ON(s.goods_sysno=goods.sysno)
        LEFT JOIN ".DB_PREFIX."base_goods_quality q ON(s.goods_quality_sysno=q.sysno)
        LEFT JOIN ".DB_PREFIX."base_storagetank st ON(s.storagetank_sysno=st.sysno)
        LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON (ga.goods_sysno = goods.sysno)
        LEFT JOIN ".DB_PREFIX."base_unit u ON (u.sysno = ga.unit_sysno)
        WHERE s.isdel='0' AND s.sysno in ($ids_str)";
        //echo $sql;exit;
        return $this->dbh->select($sql);
    }

    /*
     * 获取顶点合同id
     * */
    public  function getContractName($cId){
        $sql = "SELECT contractnodisplay FROM ".DB_PREFIX."doc_contract ss
                WHERE  sysno = ".$cId;
        return $this->dbh->select_one($sql);
    }


    /**
     * 添加货权转移
     * @param $data
     * @param $detail
     * @return array|string
     */
    public function addForApi($data, $detail){
        $this->dbh->begin();
        //主表
        try{
            $id =  $this->addTrans($data);
            if(!$id){
                throw new Exception('添加主表信息失败', 300);
                return false;
            }

            //详细信息

            $item = [
                'stocktrans_sysno' => $id,
                'out_stock_sysno' => $detail['out_stock_sysno'],
                'in_stock_sysno' => $detail['in_stock_sysno'],
                'transqty' => $detail['transqty'],
                'memo' => $detail['memo'],
                'updated_at'=>'=NOW()',
            ];
            $res = $this -> addTransDetail($item);
            if (!$res) {
                throw new Exception('添加明细表信息失败', 300);
                return false;
            }


            //日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  9,
                'opertype'  => 1,
                'operemployee_sysno' => 0,
                'operemployeename' => '云仓',
                'opertime'    => '=NOW()',
                'operdesc'  =>  '新建',
            );
            $logRes = $S->addDocLog($input);
            if (!$logRes) {
                throw new Exception('添加操作日志失败', 300);
                return false;
            }
            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => $id ];
        }catch (Exception $e){
            $this->dbh->rollback();
            return ['statusCode' => $e->getCode(), 'msg' => $e->getMessage()];
        }
    }
    /**
     * 库存数据列表
     * @author HR
     *
     */
    public function getstockList($search)
    {
        $filter = array();

        if (isset($search['sysno']) && $search['sysno'] != '') {
            $filter[] = "ss.sysno >= '{$search['sysno']}'";
        }
        if (isset($search['begin_time']) && $search['begin_time'] != '') {
            $filter[] = "ss.created_at >= '{$search['begin_time']}'";
        }

        if (isset($search['end_time']) && $search['end_time'] != '') {
            $filter[] = "ss.created_at <= '{$search['end_time']}'";
        }

        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[] = " ss.`customer_sysno` = '" . $search['customer_sysno'] . "' ";
        }
        if (isset($search['goodsnature']) && $search['goodsnature'] != '') {
            $filter[] = " ss.`goodsnature` = '" . $search['goodsnature'] . "' ";
        }

        if (isset($search['iscurrent']) && $search['iscurrent'] != '') {
            $filter[] = " ss.`iscurrent` = '" . $search['iscurrent'] . "' ";
        }
        if (isset($search['goodsname']) && $search['goodsname'] != '') {
            $filter[] = " ss.`goods_sysno` = '" . $search['goodsname'] . "' ";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[] = " ss.`goods_sysno` = '" . $search['goods_sysno'] . "' ";
        }
        if (isset($search['contractno']) && $search['contractno'] != '') {
            $filter[] = " dc.`sysno` = '" . $search['contractno'] . "' ";
        }
        if (isset($search['isclearstock']) && $search['isclearstock'] != '') {
            $filter[] = " ss.`isclearstock` = '" . $search['isclearstock'] . "' ";
        }
        $where = ' ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        if (!$search['orders']) {
            $search['orders'] = "updated_at DESC";
        }

        $des = explode(' ', $search['orders']);

        $arr = [
            'updated_at' => 'ss',
            'sysno' => 'ss',
            'firstfrom_no' => 'ss',
            'stockindate' => 'si',
            'stocktransdate' => 'st',
            'firstdate' => 'ss',
            'financedate' => 'ss',
            'doctype' => 'ss',
            'isclearstock' => 'ss',
            'shipname' => 'ss',
            'customer_sysno' => 'ss',
            'customername' => 'ss',
            'goods_sysno' => 'ss',
            'goodsname' => 'ss',
            'goodsqualityname' => 'ss',
            'unitname' => 'unit',
            'storagetankname' => 'bsk',
            'instockqty' => 'ss',
            'stockqty' => 'ss',
            'goodsnature' => 'ss',
            'clockqty' => 'ss',
            'transferflag' => 'ss',
            'transferqty' => 'ss',
            'stockno' => 'ss',
            'created_at' => 'si',
        ];

        $a = $arr[$des[0]];
        $orderby = "  ORDER BY {$a}.{$search['orders']} ";

        $result = array('total' => 0, 'list' => array());


        $sql = "SELECT COUNT( DISTINCT ss.sysno)
                FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON ss.firstfrom_sysno = si.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON ss.sysno=sid.stock_sysno AND si.sysno=sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON ss.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno`
                LEFT JOIN `".DB_PREFIX."doc_contract` dc ON ss.contract_sysno = dc.sysno
                WHERE ss.stockqty!=0 and ss.`iscurrent`=1 AND ss.`status`=1 AND ss.`isdel`=0 AND si.stockinstatus!=5  {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);


        $sql = "SELECT ss.*,(ss.stockqty-ss.clockqty) as stockqty,ss.stockqty as numberqty,si.stockindate,st.stocktransdate,unit.unitname,dc.contractno,si.sysno stockin_sysno,st.sysno stocktrans_sysno,st.buy_customername,dc.contractnodisplay,gq.qualityname,ss.firstfrom_no as stockin_no,si.release_num,si.unrelease_num,
        if(ss.doctype=3,0,ss.instockqty) as inqty,bs.storagetankname as storagetankname,bs.tank_stockqty,
        -- if(si.stockintype=1,sid.shipname,if(si.stockintype=2,'槽车',if(si.stockintype=3,'管输','--'))) as shipname
        if(ss.shipname,ss.shipname,if(si.stockintype=1,ss.shipname,if(si.stockintype=2,'槽车',if(si.stockintype=3,'管输','--')))) as shipname
                FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON ss.firstfrom_sysno = si.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON ss.sysno=sid.stock_sysno AND si.sysno=sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON st.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                left join `".DB_PREFIX."base_storagetank` bs ON bs.sysno = ss.storagetank_sysno and bs.isdel=0 and bs.status<2
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno`
                LEFT JOIN `".DB_PREFIX."doc_contract` dc ON ss.contract_sysno = dc.sysno
                LEFT JOIN  `".DB_PREFIX."base_goods_quality` gq ON ss.goods_quality_sysno = gq.sysno AND gq.isdel=0 AND gq.`status`=1
                WHERE ss.stockqty!=0 and ss.`iscurrent`=1 AND ss.`status`=1 AND ss.`isdel`=0  AND si.stockinstatus!=5 {$where} GROUP BY ss.sysno {$orderby} ";
        // error_log($sql, 3, 'sql_print.txt');
        //  echo $sql;die;
        if (empty($search['pageSize']) && $search['page'] == false) {         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            return $result;
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            //
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }
    }


    //根据买家合同得到商品id
    public  function  getgoodsIdBycontractId($contract_sysno){
        if($contract_sysno){
            $sql = "select goods_sysno from ".DB_PREFIX."doc_contract_goods cg
                     LEFT JOIN  ".DB_PREFIX."doc_contract dc  on dc.sysno = cg.contract_sysno
                     where dc.sysno = $contract_sysno ";
            return $this->dbh->select($sql);
        }
    }
    /*
     * 获取商品id
     * */
    public function getgoodssysno($gname){
        if($gname){
            $sql = "select sysno from ".DB_PREFIX."base_goods g
                         where isdel =0 and status<2 and g.goodsname ='".trim($gname)."'";
            return $this->dbh->select_one($sql);
        }
    }
    /*
     * 获取合同类型3包罐4包罐容
     * */
    public function getcontroltype($control_sysno){
        if($control_sysno){
            $sql = "select contracttype from ".DB_PREFIX."doc_contract
                   where sysno ={$control_sysno}";
            return  $control_type = $this->dbh->select_row($sql);
        }else{
            return array();
        }
    }

    /*
     * 获取
     * */
    public function  getparentshipper($id){
        $sql = "select ss.customername from ".DB_PREFIX."doc_stock_trans st
                    LEFT JOIN  ".DB_PREFIX."doc_stock_trans_detail std ON  st.sysno = std.stocktrans_sysno and std.isdel = 0 and std.status < 2
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.out_stock_sysno AND ss.iscurrent=1 and ss.isdel=0 and ss.status<2
                    where st.isdel = 0 and st.status< 2 and st.sysno = {$id}
                    limit 1";
        return $this->dbh->select_one($sql);
    }

    /**
     * 获取首期到期日
     * @param  [type] $firstfrom_sysno [来源入库单sysno]
     * @return [array]
     */
    public function getStockfirstdate($firstfrom_sysno)
    {
        $sql = "SELECT firstdate FROM `".DB_PREFIX."storage_stock` WHERE doctype!=3 AND firstfrom_sysno={$firstfrom_sysno}";

        return $this->dbh->select_one($sql);
    }
    /*
     * 根据储罐id得到储罐存储的商品
     * */
    public function getGoodsidBystoragetank($storagetank_sysno){
        if($storagetank_sysno){
            $sql = "SELECT * from ".DB_PREFIX."base_storagetank where isdel = 0 and status < 2 AND sysno =".$storagetank_sysno;
            $storagetankData = $this->dbh->select_row($sql);
            return $storagetankData;
        }else{
            return false;
        }
    }
    /*
     * 获取储罐详细信息
     * */
    public  function getstocktankList($search)
    {
        $filter = array();

        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[] = "st.goods_sysno = '{$search['goods_sysno']}'";
        }
        if (isset($search['sysno']) && $search['sysno'] != '') {
            $filter[] = "st.sysno = '{$search['sysno']}'";
        }
        $where = ' ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT( DISTINCT st.sysno) from ".DB_PREFIX."base_storagetank st
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = st.goods_sysno and bg.isdel=0 and bg.status<2
                where st.isdel = 0 and st.status<2  {$where}";
        //  echo $sql;die;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT st.*,bg.goodsname  FROM ".DB_PREFIX."base_storagetank st
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = st.goods_sysno and bg.isdel=0 and bg.status<2
                WHERE st.isdel=0 AND st.status<2 {$where} ";
        // error_log($sql, 3, 'sql_print.txt');
        if (empty($search['pageSize']) && $search['page'] == false) {         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            return $result;
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            //
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }
    }
    //判断单号是否唯一
    public function isstocktransnoOnly($search){
        $filter = array();

        if (isset($search['stocktransno']) && $search['stocktransno'] != '') {
            $filter[] = "stocktransno = '{$search['stocktransno']}'";
        }
        if (isset($search['id']) && $search['id'] != '') {
            $filter[] = "sysno <> '{$search['id']}'";
        }

        if (isset($search['sale_customer_sysno']) && $search['sale_customer_sysno'] != '') {
            $filter[] = "sale_customer_sysno = '{$search['sale_customer_sysno']}'";
        }
        $where = ' ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $sql = "select count(sysno) as count_id from ".DB_PREFIX."doc_stock_trans
               where  isdel=0 {$where} ";
        $count = $this->dbh->select_one($sql);
        return $count;

    }




}
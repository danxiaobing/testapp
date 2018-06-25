<?php
/**
 * Stockout Model
 *jp
 */

class SupplementModel
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
    public function searchSupplement($params)
    {
        $filter = array();

        if (isset($params['supplementdate']) && $params['supplementdate'] != '') {
            $filter[] = " su.`supplementdate`='{$params['supplementdate']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " su.`customer_sysno`='{$params['customer_sysno']}'";
        }

        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " su.`goods_sysno`= '{$params['goods_sysno']}'";
        }

        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " su.`goodsname`like '%".$params['goodsname']."%' ";
        }

        if (isset($params['supplementstatus']) && $params['supplementstatus'] != '') {
            $filter[] = " su.`supplementstatus`='{$params['supplementstatus']}'";
        }

        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " su.`sysno`='{$params['sysno']}'";
        }

        $where = 'su.isdel = 0 and su.status<2 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by su.created_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_supplement` su
                LEFT JOIN `".DB_PREFIX."doc_stock_supplement_detail` sud ON sud.`supplement_sysno`=su.`sysno`
                WHERE {$where} ";
        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT su.*,sud.storagetankname,sud.beqty,sud.supplementtype,sud.bussinesscheckqty
                    FROM `".DB_PREFIX."doc_stock_supplement` su
                    LEFT JOIN `".DB_PREFIX."doc_stock_supplement_detail` sud ON sud.`supplement_sysno`=su.`sysno`
                    WHERE {$where}
                    order by su.updated_at desc";
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
   /*
    * 明细添加-通过id获取选择入库单信息
    *
    * */



    /*
     * 20171016
     * jp
     * 获取入库单详情
     * */

    public function getstockinList($params)
    {
        $filter = array();
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " si.`sysno` = '{$params['sysno']}' ";
        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " si.`customer_sysno` = '{$params['customer_sysno']}' ";
        }

         if (isset($params['stockin_sysno']) && $params['stockin_sysno'] != '') {
             $filter[] = " si.`sysno` = '{$params['stockin_sysno']}' ";
         }

        $where = ' si.isdel=0 and si.status < 2  and si.stockinstatus = 4 and si.stockintype in (1,3) and s.ullage>0 and s.ullage is not null ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)
                FROM `".DB_PREFIX."doc_stock_in` si
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid on (si.`sysno`=sid.`stockin_sysno`)
                LEFT JOIN `".DB_PREFIX."storage_stock` s on (s.`sysno`=sid.`stock_sysno`) and s.iscurrent =1 and s.isdel=0 and s.status<2
                WHERE  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT si.*,si.sysno as stockin_sysno,sid.sysno as stockindatail_sysno,sid.goods_quality_sysno,sid.goodsnature,sid.bussinesscheckqty,sid.beqty,sid.shipname,sid.unitname,sid.goods_sysno,sid.goodsname,s.storagetank_sysno,bs.storagetankname,s.instockqty,s.stockqty,s.ullage,s.beyondqty,s.instockqty,s.stockqty,
                if(sid.stock_sysno,sid.stock_sysno,s.sysno) as stock_sysno,
                if(si.stockintype=1,s.shipname,'管输') as shipname,
                if(sid.qualityname,sid.qualityname,s.goodsqualityname) as qualityname,
                if(sid.goods_quality_sysno,sid.goods_quality_sysno,s.goods_quality_sysno) as goods_quality_sysno
                FROM `".DB_PREFIX."doc_stock_in` si
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid on (si.`sysno`=sid.`stockin_sysno`)
                LEFT JOIN `".DB_PREFIX."storage_stock` s on (s.`sysno`=sid.`stock_sysno`) and s.iscurrent =1 and s.isdel=0 and s.status<2
                LEFT JOIN `".DB_PREFIX."base_storagetank` bs on (bs.`sysno`=s.`storagetank_sysno`)
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

    //获取基础储罐单条明细

    public function getstankinfoByid($params)
    {
        $filter = array();
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " bs.`sysno` = '{$params['sysno']}' ";
        }

        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " bs.`sysno` = '{$params['storagetank_sysno']}' ";
        }

        $where = ' bs.isdel=0 and bs.status < 2 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)
                FROM `".DB_PREFIX."base_storagetank` bs
                WHERE  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        $sql = "SELECT * FROM `".DB_PREFIX."base_storagetank` bs
                WHERE  {$where} ";

        $result['list'] = $this->dbh->select_row($sql);

        //  echo $sql;die;
        return $result;
    }


    /*
    * 添加退货单
     * jp
    */
    public function addSupplement($data,$detaildata,$step)
    {
        $this->dbh->begin();
        try {
            if($step ==2){
                $data['supplementstatus'] = 2;
            }elseif($step ==3){
                $data['supplementstatus'] = 3;
            }
            $res = $this->dbh->insert(DB_PREFIX.'doc_stock_supplement', $data);

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'新增主表信息失败'];
            }

            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_supplement_detail', 'supplement_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除明细表信息失败'];
            }

            foreach ($detaildata as $value) {
                $input_detail = array(
                    'supplement_sysno' => $id,
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'unitname' => '吨',
                    'bussinesscheckqty'=>$value['bussinesscheckqty'],
                    'supplementtype' => $value['supplementtype'],
                    'beqty' => $value['beqty'],
                    'storagetank_sysno'=>$value['storagetank_sysno'],
                    'storagetankname'=>$value['storagetankname'],
                    'qualityname'=>$value['qualityname'],
                    'shipname'=>$value['shipname'],
                    'stock_sysno'=>$value['stock_sysno'],
                    'memo'=>$value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_supplement_detail', $input_detail);

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
                'doctype' => 32,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' =>'新建补充单'
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'新增日志失败'];
            }
            if($step ==2){  //暂存时操作日志
                $input['opertype'] = 2;
                $input['operdesc'] = '暂存补充单';
            }elseif($step ==3){
                $input['opertype'] = 3;
                $input['operdesc'] = '提交补充单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'新增-日志-失败'];
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
    public function updateSupplement($id,$data,$detaildata,$supplementstatus,$step)
    {
        $this->dbh->begin();
        try {
            if($step==2){
                $data['supplementstatus'] =2;
                if($supplementstatus==6){
                    $data['supplementstatus'] =6;
                }
            }else{
                $data['supplementstatus'] =3;
            }


            $res = $this->dbh->update(DB_PREFIX.'doc_stock_supplement', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新主表失败'];
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_supplement_detail', 'supplement_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除明细表信息失败'];
            }

            foreach ($detaildata as $value) {
                $input_detail = array(
                    'supplement_sysno' => $id,
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'unitname' => '吨',
                    'bussinesscheckqty'=>$value['bussinesscheckqty'],
                    'supplementtype' => $value['supplementtype'],
                    'beqty' => $value['beqty'],
                    'storagetank_sysno'=>$value['storagetank_sysno'],
                    'storagetankname'=>$value['storagetankname'],
                    'qualityname'=>$value['qualityname'],
                    'shipname'=>$value['shipname'],
                    'stock_sysno'=>$value['stock_sysno'],
                    'memo'=>$value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_supplement_detail', $input_detail);
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
                'doctype' => 32,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($step ==2) {
                $input['opertype'] = 2;
                $input['operdesc'] = '保存补充单';
            }elseif($step ==3){
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
    public function auditSupplement($id,$data,$detaildata,$step,$auditreason){
        $this->dbh->begin();
        try {
            if($step==4){
                $input_data['supplementstatus'] =4;
            }elseif($step==6){
                $input_data['supplementstatus'] =6;
                $input_data['auditreason'] = $auditreason;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_stock_supplement', $input_data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新主表失败'];
            }

            //审核通过逻辑
           if($step==4){
               $stockin_sysno = $data['stockin_sysno'];
               $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
               //获取入库单详细信息
               $search = array(
                   'stockin_sysno'=>$stockin_sysno,
                   'page'=>false,
               );
               //入库详细信息
               $stockin_data = $this->getstockinList($search)['list'][0];
               $stockin_data_all = $this->getstockinList($search);
               //获取入库总量
               $stockin_count = $this->getstockinqtySUM($stockin_sysno);
               if($stockin_count){
                  $stockin_beqty = $stockin_count['stockin_beqty'];
               }else{
                   $this->dbh->rollback();
                   return ['code'=>300,'msg'=>'获取入库总量失败'];
               }

               foreach ($detaildata as $value) {
                   $supplementtype = $value['supplementtype'];
                   $stock_sysno = $value['stock_sysno'];
                   $stockInfo = $this->getstockInfoById($stock_sysno);
                   if(!$stockInfo){
                       $this->dbh->rollback();
                       return ['code'=>300,'msg'=>'没有库存信息'];
                   }
                   $input_data = array(
                       'supplement_sysno' => $id,
                       'goods_sysno' => $value['goods_sysno'],
                       'goods_quality_sysno' => $value['goods_quality_sysno'],
                       'goodsnature' => $value['goodsnature'],
                       'bussinesscheckqty'=>$value['bussinesscheckqty'],
                       'supplementtype' => $value['supplementtype'],
                       'beqty' => $value['beqty'],
                       'storagetank_sysno'=>$value['storagetank_sysno'],
                       'storagetankname'=>$value['storagetankname'],
                       'qualityname'=>$value['qualityname'],
                       'shipname'=>$value['shipname'],
                       'stock_sysno'=>$value['stock_sysno'],
                   );

                   //添加正库存
                   if($supplementtype==1){
                       //更新入库数量
                       $addbeqty =  floatval($stockin_data['beqty']) + floatval($value['beqty']);
                       $update_data = [
                           'beqty'=>$addbeqty
                       ];
                       $res = $this->dbh->update(DB_PREFIX.'doc_stock_in_detail', $update_data, 'sysno=' . intval($stockin_data['stockindatail_sysno']));
                       if (!$res) {
                           $this->dbh->rollback();
                           return ['code'=>300,'msg'=>'更新入库详细表数量失败'];
                       }

                       #--------------------------更新罐容新方法
                       #################当前存放量
                       $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                       $Storagetanklist = $tank->getStoragetankById($value['storagetank_sysno']);
                       $tank_stockqty = $Storagetanklist['tank_stockqty'] + floatval($value['beqty']);              #增加当前存放量
                       $Storagetank_data = [
                           'tank_stockqty'=>$tank_stockqty,
                       ];

                       $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $Storagetank_data, 'sysno=' . intval($value['storagetank_sysno']));
                       if (!$res) {
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'更新当前存放量失败!'];
                       }
                     //  print_r($tank_stockqty);   print_r($Storagetanklist['actualcapacity']);die;
                       if($tank_stockqty>$Storagetanklist['actualcapacity']){
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'罐容不足'];
                       }

                       #--------------------------END更新罐容-----------------------

                       //更新损耗
                       #============补充入库更新损耗和库存==================
                           $sql_contract_goods = "SELECT isminstockin,minnumber,IF(contractrate,contractrate,firstlossrate) AS contractrate
                                                  FROM `".DB_PREFIX."doc_contract_goods`
                                                  WHERE contract_sysno={$stockin_data['contract_sysno']} AND goods_sysno = {$stockin_data['goods_sysno']} AND isdel<>1";

                           $contract_goods_data = $this->dbh->select_row($sql_contract_goods);

                           //开启了最小入库量的进行库存扣减

                           $instock_ullage_params = array(
                               'beqty'          => $stockin_beqty+floatval($value['beqty']),  //实际入库量+补充入库量
                               'instockqty'     =>$stockin_data['instockqty']+floatval($value['beqty']),//原入库量+补充入库量
                               'stockqty'       =>floatval($stockInfo['stockqty'])+floatval($value['beqty']),//当前可用库量+补充入库量
                               'ullage'         =>floatval($stockInfo['ullage']),//当前库存损耗
                               'stock_sysno'    => $value['stock_sysno'],
                               'contractrate'   => $contract_goods_data['contractrate'], //合约损耗率
                               'minnumber'      => $contract_goods_data['minnumber'],  //最小入库量
                               'isminstockin'   => $contract_goods_data['isminstockin'],
                               'ullagebeqty' => floatval($value['beqty']),
                               'stockin_data_all' => $stockin_data_all,
                           );
                           $res_ullage = $this->instock_ullage($instock_ullage_params);
                           if(!$res_ullage['res'])
                           {
                               $this->dbh->rollback();
                               return ['code'=>300, 'message'=>'扣减损耗和更新库存失败'];
                           }
                       #============end扣减损耗和更新库存==================


                       # 罐容记录表
                       #============插入罐容记录表==================
                       $storage = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                       $arr = [
                           'doc_sysno' => $id,
                           'docno' => $data['stockinno'],
                           'doctype' => 17,   #17补充入库
                           'storagetank_sysno' => $value['storagetank_sysno'],
                           'beqty' => $value['beqty'],
                           'status' => 1,
                           'isdel' => 0
                       ];

                       $res = $storage->addStoragetankLog($arr);
                       if ($res['code'] != 200) {
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'添加罐容记录失败!'];
                       }

                       #============end插入罐容记录表==================

                       # 进出货物记录begin
                       #============插入进出货物记录==================
                       $goodsinoutlog = array(
                           'doc_time'          => '=NOW()',
                           'shipname'          => $data['shipname'],
                           'goods_sysno'       => $data['goods_sysno'],
                           'goodsname'         => $data['goodsname'],
                           'storagetank_sysno' => $value['storagetank_sysno'],
                           'storagetankname'   => $value['storagetankname'],
                           'customer_sysno'    => $data['customer_sysno'],
                           'customername'      => $data['customername'],
                           'beqty'             => $value['beqty'],
                           'tobeqty'           => $value['beqty'],
                           'stockin_sysno'     => $data['stockin_sysno'],
                           'stockinno'         => $data['stockinno'],
                           'doc_sysno'         => $id,
                           'docno'             => $data['supplementno'],
                           'accountstoragetank_sysno' => $value['storagetank_sysno'],
                           'accountstoragetankname'   => $value['storagetankname'],
                           'stock_sysno'       => $value['stock_sysno'],
                           'doc_type'          => 19,
                           'ullage'            =>$res_ullage['beqtyullage'],
                           'takegoodscompany'  =>'',
                           'goodsnature'       =>$value['goodsnature'],
                           'takegoodsno'       =>'',
                           'status'            => 1,
                           'isdel'             => 0,
                           'created_at'        =>'=NOW()',
                           'updated_at'        =>'=NOW()',
                       );
                       # 进出货物记录end
                       $logInstance = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                       $res = $logInstance->addGoodsRecordLog($goodsinoutlog);
                       if ($res['code']!=200) {
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'添加进出货物记录失败!'.$res['message']];
                       }

                       #============end进出货物记录==================
                   }
                   if($supplementtype==2){
                       //更新入库数量
                       $reducebeqty =  floatval($stockin_data['beqty']) - floatval($value['beqty']);
                       if($reducebeqty<0){
                           $this->dbh->rollback();
                           return ['code'=>300,'msg'=>'扣减数量不能大于入库量'];
                       }
                       $update_data = [
                           'beqty'=>$reducebeqty
                       ];

                       $res = $this->dbh->update(DB_PREFIX.'doc_stock_in_detail', $update_data, 'sysno=' . intval($stockin_data['stockindatail_sysno']));
                       if (!$res) {
                           $this->dbh->rollback();
                           return ['code'=>300,'msg'=>'更新入库详细表数量失败'];
                       }

                       #--------------------------更新罐容新方法
                       #################当前存放量
                       $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                       $Storagetanklist = $tank->getStoragetankById($value['storagetank_sysno']);
                       $tank_stockqty = $Storagetanklist['tank_stockqty'] - floatval($value['beqty']);              #增加当前存放量
                      if($tank_stockqty<0){
                          $this->dbh->rollback();
                          return ['code'=>300,'msg'=>'扣减数量不能大于当前罐容量'];
                      }
                       $Storagetank_data = [
                           'tank_stockqty'=>$tank_stockqty,
                       ];

                       $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $Storagetank_data, 'sysno=' . intval($value['storagetank_sysno']));
                       if (!$res) {
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'更新当前存放量失败!'];
                       }

                       #--------------------------END更新罐容-----------------------

                       //更新损耗
                       #============补充入库更新损耗和库存==================
                       $sql_contract_goods = "SELECT isminstockin,minnumber,IF(contractrate,contractrate,firstlossrate) AS contractrate
                                                  FROM `".DB_PREFIX."doc_contract_goods`
                                                  WHERE contract_sysno={$stockin_data['contract_sysno']} AND goods_sysno = {$stockin_data['goods_sysno']} AND isdel<>1";

                       $contract_goods_data = $this->dbh->select_row($sql_contract_goods);

                       //开启了最小入库量的进行库存扣减
                       $instock_ullage_params = array(
                           'beqty'          => $stockin_beqty-floatval($value['beqty']),  //实际入库总量-补充入库量
                           'instockqty'     =>$stockin_data['instockqty']-floatval($value['beqty']),//原入库量+补充入库量
                           'stockqty'       =>floatval($stockInfo['stockqty'])-floatval($value['beqty']),//当前可用库量+补充入库量
                           'ullage'         =>floatval($stockInfo['ullage']),//当前库存损耗
                           'stock_sysno'    => $value['stock_sysno'],
                           'contractrate'   => $contract_goods_data['contractrate'], //合约损耗率
                           'minnumber'      => $contract_goods_data['minnumber'],  //最小入库量
                           'isminstockin'   => $contract_goods_data['isminstockin'],
                           'ullagebeqty' => floatval($value['beqty']),
                           'stockin_data_all' => $stockin_data_all,
                       );

                       if($instock_ullage_params['beqty']<0){
                           $this->dbh->rollback();
                           return ['code'=>300, 'message'=>'扣减数量不能大于入库量'];
                       }

                       $res_ullage = $this->instock_ullage($instock_ullage_params);
                       if(!$res_ullage['res'])
                       {
                           $this->dbh->rollback();
                           return ['code'=>300, 'message'=>'扣减损耗和更新库存失败'];
                       }

                       //更新损耗和库存
                       if($instock_ullage_params['stockqty']<=0){
                           $stock_update = [
                               'stockqty'   =>0,//原可用库量-补充入库量
                               'beyondqty'  => abs($stockin_data['stockqty']-floatval($value['beqty'])),//原可用库量-补充入库量
                           ];
                           $res = $this->dbh->update(DB_PREFIX.'storage_stock', $stock_update, 'sysno=' . intval($value['stock_sysno']));
                           if(!$res)
                           {
                               $this->dbh->rollback();
                               return ['code'=>300, 'message'=>'更新负库存失败'];
                           }
                       }

                       #============end扣减损耗和更新库存==================


                       # 罐容记录表
                       #============插入罐容记录表==================
                       $storage = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                       $arr = [
                           'doc_sysno' => $id,
                           'docno' => $data['stockinno'],
                           'doctype' => 18,   #18扣减入库
                           'storagetank_sysno' => $value['storagetank_sysno'],
                           'beqty' => $value['beqty'],
                           'status' => 1,
                           'isdel' => 0
                       ];

                       $res = $storage->addStoragetankLog($arr);
                       if ($res['code'] != 200) {
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'添加罐容记录失败!'];
                       }

                       #============end插入罐容记录表==================

                       # 进出货物记录begin
                       #============插入进出货物记录==================
                       $goodsinoutlog = array(
                           'doc_time'          => '=NOW()',
                           'shipname'          => $data['shipname'],
                           'goods_sysno'       => $data['goods_sysno'],
                           'goodsname'         => $data['goodsname'],
                           'storagetank_sysno' => $value['storagetank_sysno'],
                           'storagetankname'   => $value['storagetankname'],
                           'customer_sysno'    => $data['customer_sysno'],
                           'customername'      => $data['customername'],
                           'beqty'             => '-'.$value['beqty'],
                           'tobeqty'           => '-'.$value['beqty'],
                           'stockin_sysno'     => $data['stockin_sysno'],
                           'stockinno'         => $data['stockinno'],
                           'doc_sysno'         => $id,
                           'docno'             => $data['supplementno'],
                           'accountstoragetank_sysno' => $value['storagetank_sysno'],
                           'accountstoragetankname'   => $value['storagetankname'],
                           'stock_sysno'       => $value['stock_sysno'],
                           'doc_type'          => 20,
                           'ullage'            =>'-'.$res_ullage['beqtyullage'],
                           'takegoodscompany'  =>'',
                           'goodsnature'       =>$value['goodsnature'],
                           'takegoodsno'       =>'',
                           'status'            => 1,
                           'isdel'             => 0,
                           'created_at'        =>'=NOW()',
                           'updated_at'        =>'=NOW()',
                       );
                       //print_r($goodsinoutlog);die;
                       # 进出货物记录end
                       $logInstance = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                       $res = $logInstance->addGoodsRecordLog($goodsinoutlog);
                       if ($res['code']!=200) {
                           $this->dbh->rollback();
                           return ['code'=>300,'message'=>'添加进出货物记录失败!'.$res['message']];
                       }
                       #============end进出货物记录==================
                   }
               }
           }



            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 32,
                'opertype' =>4,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($step ==4) {
                $input['opertype'] = 4;
                $input['operdesc'] = '审核通过';
            }elseif($step ==6){
                $input['opertype'] = 6;
                $input['operdesc'] = '审核不通过';
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
    public function delSupplement($id,$input){
        $res = $this->dbh->update(DB_PREFIX.'doc_stock_supplement', $input, 'sysno=' . intval($id));
        if(!$res){
            $this->dbh->rollback();
            return ['code'=>300,'msg'=>'删除失败'];
        }
        return ['code'=>200,'msg'=>'删除成功'];

    }

/*
 * 获取库存主信息方法
 * */
    public function getstockInfoById($stock_sysno){
        if($stock_sysno){
            $sql = "SELECT * FROM `".DB_PREFIX."storage_stock` s
                    WHERE  s.iscurrent=1 and s.sysno = {$stock_sysno} ";
            return $this->dbh->select_row($sql);
        }


    }


    //获取主表信息
    public function getsupplementById($id)
    {
        if($id)
        {
            $sql = "SELECT su.*,sud.storagetankname,sud.beqty,sud.supplementtype,sud.bussinesscheckqty
                    FROM `".DB_PREFIX."doc_stock_supplement` su
                    LEFT JOIN `".DB_PREFIX."doc_stock_supplement_detail` sud ON sud.`supplement_sysno`=su.`sysno`
                    WHERE su.sysno = {$id} ";
            return $this->dbh->select_row($sql);
        }

    }

    /*
     * 获取明细数据,编辑页dataurl
     */
    public function getdetailById($params)
    {
        $filter = array();

        if (isset($params['supplementstatus']) && $params['supplementstatus'] != '') {
            $filter[] = " su.`supplementstatus`='{$params['supplementstatus']}'";
        }

        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " su.`sysno`='{$params['sysno']}'";
        }


        $where = 'su.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by su.created_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_supplement` su
                    LEFT JOIN `".DB_PREFIX."doc_stock_supplement_detail` sud ON sud.`supplement_sysno`=su.`sysno`
                    WHERE {$where}  ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT su.shipname,su.goodsname,su.customer_sysno,su.customername,sud.*
                        FROM `".DB_PREFIX."doc_stock_supplement` su
                        LEFT JOIN `".DB_PREFIX."doc_stock_supplement_detail` sud ON (sud.`supplement_sysno`=su.`sysno`)
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

    //获取入库总量
    function  getstockinqtySUM($stockin_sysno){
        if($stockin_sysno){
            $sql = "SELECT sum(sid.beqty) as stockin_beqty
                    FROM `".DB_PREFIX."doc_stock_in` si
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid on (si.`sysno`=sid.`stockin_sysno`)
                    WHERE  si.sysno = {$stockin_sysno}
                    group by si.sysno ";
            return $this->dbh->select_row($sql);
        }else{
            return false;
        }
    }

    /**
     * 合同启用最小入库量时
     * 入库即扣库存损耗
     * @param  [array] $[params]  array('stock_sysno'=>'库存主键', 'beqty'=>'实际入库数量', 'minnumber'=>'最小入库量', 'contractrate'=>'合约损耗率' , 'isminstockin' => '是否开启最小入库','ullagebeqty'=>补充数量);
     * @return [bool] [description]
     * @author HR
     */
    public function instock_ullage($params)
    {
        if($params['stock_sysno']=='' && !isset($params['stock_sysno']))
        {
            return ['code'=>300, 'message'=> '库存ID不能为空!'];
        }
        if ($params['isminstockin']==1){

            $ullage = ( $params['beqty']>$params['minnumber'] ? $params['beqty'] : $params['minnumber'] ) * ($params['contractrate']/1000);
        } else {
            $ullage = $params['beqty'] * ($params['contractrate']/1000);
        }

        $instockqty = $params['instockqty']; //可用库存量
        $stockqty=$params['stockqty'] + $params['ullage']- $ullage; //可用库存量

        $stock_params = array(
            'instockqty' => $instockqty,
            'stockqty'   => $stockqty,
            'ullage'     => $ullage
        );

        $res = $this->dbh->update(DB_PREFIX.'storage_stock', $stock_params, 'sysno='.intval($params['stock_sysno']));

        $stockin_data_all = $params['stockin_data_all'];
        if(!empty($stockin_data_all['list']))
        {
            foreach ($stockin_data_all['list'] as $key => $value) {
                $instock = $instock + $value['instockqty'];
            }
            if($params['ullagebeqty'])
            {
                if ($params['isminstockin']==1){

                    #先判断上一次是否小于最小量
                    if($instock>$params['minnumber'])
                    {
                        $beqtyullage = $params['ullagebeqty'] * ($params['contractrate']/1000);
                    }
                    else
                    {
                        $beqtyullage = ( $params['beqty']>$params['minnumber'] ? ($params['ullagebeqty']-($params['minnumber']-$instock)) : $params['minnumber'] ) * ($params['contractrate']/1000);
                    }
                    
                } else {
                    $beqtyullage = $params['ullagebeqty'] * ($params['contractrate']/1000);
                }
            }
        }
        

        $result = array(
            'res'=>$res,
            'ullage'=>$ullage,
            'beqtyullage'=>$beqtyullage,
        );
        return $result;

    }

}
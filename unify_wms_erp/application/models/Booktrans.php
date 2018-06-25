<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/12/7 0007
 * Time: 9:50
 */
Class BooktransModel
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


    public function getList($params)
    {
        $filter = array();
        if (isset($params['bookingtransno']) && $params['bookingtransno'] != '') {
            $filter[] = " trans.`bookingtransno` LIKE '%" . $params['bookingtransno'] . "%' ";
        }
        
        //日期
        if (isset($params['bookingtransdate_start']) && $params['stocktransdate_start'] != '') {
            $filter[] = " trans.`stocktransdate` >= '{$params['stocktransdate_start']}' ";
        }
        if (isset($params['bookingtransdate_end']) && $params['bookingtransdate_end'] != '') {
            $filter[] = " trans.`bookingtransdate` <= '{$params['bookingtransdate_end']}' ";
        }
        //转让方
        if (isset($params['sale_customer_sysno']) && $params['sale_customer_sysno'] != '') {
            $filter[] = " trans.`sale_customer_sysno` = '{$params['sale_customer_sysno']}' ";
        }
        //受让方
        if (isset($params['buy_customer_sysno']) && $params['buy_customer_sysno'] != '') {
            $filter[] = " trans.`buy_customer_sysno` = '{$params['buy_customer_sysno']}' ";
        }
        //单据状态
        if (isset($params['bookingtransstatus']) && $params['bookingtransstatus'] != '') {
            $filter[] = " trans.`bookingtransstatus` = '{$params['bookingtransstatus']}' ";
        }
        //单据状态
        if (isset($params['issaveorder']) && $params['issaveorder'] != '') {
            $filter[] = " trans.`issaveorder` = '{$params['issaveorder']}' ";
        }
        
        
        $where = 'where trans.isdel = 0';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        
        if(isset($params['orders']) && $params['orders'] != ''){
            $order = " ORDER BY '"."{$params['orders']}"."' DESC";
        }else{
            $order = " ORDER BY trans.`created_at` DESC";
        }

        $sql = " SELECT  COUNT(*)  FROM `".DB_PREFIX."doc_booking_trans`as trans
                LEFT JOIN ".DB_PREFIX."customer as sale on sale.sysno = trans.sale_customer_sysno
                LEFT JOIN ".DB_PREFIX."customer as buy on buy.sysno = trans.buy_customer_sysno
                LEFT JOIN ".DB_PREFIX."doc_contract as coc on coc.sysno = trans.contract_sysno
                {$where} {$order} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = " SELECT trans.*,sale.customername as salename,buy.customername as buyname,
            coc.contractno as cocno  FROM `".DB_PREFIX."doc_booking_trans` as trans
            LEFT JOIN ".DB_PREFIX."customer as sale on sale.sysno = trans.sale_customer_sysno
            LEFT JOIN ".DB_PREFIX."customer as buy on buy.sysno = trans.buy_customer_sysno
            LEFT JOIN ".DB_PREFIX."doc_contract as coc on coc.sysno = trans.contract_sysno
            {$where} {$order} ";
            if (isset($params['page']) && $params['page'] == false) {               
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);                
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

   /**
    * 添加预约
    * @param array $data 主表数据
    * @param array $detail 明细表数据
    * @param string $stockmarks 描述
    * @param array $attachment 附件
    * @return array
    */
    public function add($data,$detail,$stockmarks = '',$attachment = null){
        $this->dbh->begin();
        //主表
        $resid =  $this->addTrans($data);
        if(!$resid){
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'添加主表信息失败'];
        }
        
        //详细信息
        foreach($detail as $val){
            $item = [
                'bookingtrans_sysno' => $resid,
                'stock_sysno' => $val['stock_sysno'],
                'transqty' => $val['transqty'],
                'memo' => $val['memo']
            ];
        
            $res = $this->addTransDetail($item);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode'=>300,'msg'=>'添加明细表失败'];
            }
        
        }
        //日志
        $user = Yaf_Registry::get(SSN_VAR);
        $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $input= array(
            'doc_sysno'  =>  $resid,
            'doctype'  =>  3,
            'opertype'  => 0,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime'    => '=NOW()',
            'operdesc'  =>  $stockmarks,
        );
        $res = $S->addDocLog($input);
        if (!$res) {
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'添加操作日志失败'];
        }
        
        //附件
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(count($attachment) > 0){
            $res = 	$A->addAttachModelSysno($resid,$attachment);
            if(!$res){
               return COMMON::result(300,'添加附件失败');
            }
        }
        
        
        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>'操作成功','sysno'=>$resid];
    }
    /**
     * 修改预约
     * @param unknown $id ID
     * @param unknown $data 主表数据
     * @param unknown $detail 明细表数据
     * @param string $stockmarks 日志描述
     * @param string $attachment 附件
     * @return array
     */
    public function update($id,$data,$detail,$stockmarks = '',$attachment = null){
        $this->dbh->begin();
        //主表
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
                'bookingtrans_sysno' => $id,
                'stock_sysno' => $val['stock_sysno'],
                'transqty' => $val['transqty'],
                'memo' => $val['memo']                
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
            'doctype'  =>  3,
            'opertype'  => $data['bookingtransstatus']-1,
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
        
        //附件
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(count($attachment) > 0){
            $res = 	$A->addAttachModelSysno($id,$attachment);
            if(!$res){
                return COMMON::result(300,'添加附件失败');
            }
        }

        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>'操作成功'];
    }
    
    /**
     * 审核预约单
     * @param int $id
     * @param int $status
     * @param string $stockmarks
     * @return multitype:number string
     */
    public function audit($id,$detaildata,$status,$stockmarks = ''){
        $this->dbh->begin();
        //主表
        $data['bookingtransstatus'] = $status;
        $res =  $this->updateTrans($id,$data);
        if(!$res){
            $this->dbh->rollback();
            return ['statusCode'=>300,'msg'=>'修改货权转移预约信息失败'];
        }
    
        //日志
        $user = Yaf_Registry::get(SSN_VAR);
        $sys = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $input= array(
            'doc_sysno'  =>  $id,
            'doctype'  =>  3,
            'opertype'  => $data['bookingtransstatus']-1,
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

        if($status==4){
            $S = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            foreach ($detaildata as $value) {
                #只有审核才能调用
                $params = array('type'=>9);
                $stockdata = array(
                    'sysno' => $value['stock_sysno'],
                    'clockqty' => $value['transqty']

                );

                $params['data'] = $stockdata;
                $stockres = $S->pubstockoperation($params);



                if ($stockres['code'] !='200') {
                    $msg = $stockres['message'];
                    $this->dbh->rollback();
                    return ['statusCode'=>300,'msg'=>$msg];
                    //return false;
                }
            }
        }

        $this->dbh->commit();
        return ['statusCode'=>200,'msg'=>'操作成功'];
    }
    
    public function addTrans($data){
        $data['created_at'] = '=NOW()';
        return $this->dbh->insert(DB_PREFIX.'doc_booking_trans', $data);
    }
     
    public function updateTrans($id = 0, $data = array()){
        $data['updated_at'] = '=NOW()';
        return  $this->dbh->update(DB_PREFIX.'doc_booking_trans', $data, 'sysno=' . intval($id));
    }
    
    /**
     * 添加货权转移详细
     * @param array $data
     */
    public function addTransDetail($data){
        $data['created_at'] = '=NOW()';
        return $this->dbh->insert(DB_PREFIX.'doc_booking_trans_detail', $data);
    }
    /**
     * 修改货权转移详细
     * @param int $id
     * @param array $data
     */
    public function updateTransDetail($id = 0, $data = array()){
        $data['updated_at'] = '=NOW()';
        return  $this->dbh->update(DB_PREFIX.'doc_booking_trans_detail', $data, 'sysno=' . intval($id));
    }
    
    public function deleteTransDetail($id = 0, $data = array()){
        return  $this->dbh->delete(DB_PREFIX.'doc_booking_trans_detail',"bookingtrans_sysno='$id'");
    }
    /**
     * 根据id详细信息
     * id: 权限id
     * @return 数组
     */
    public function getTransById($id = 0)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."doc_booking_trans WHERE sysno = $id ";    
        return $this->dbh->select_row($sql);
    }
    
    /**
     * 返回列表详细信息
     */
    public function getListDetials($data){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_booking_trans_detail ";
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
            $ids[] = $item['stock_sysno'];
        }
        
        $stock = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $infos = $stock->getElementById($ids);
        
        foreach ($infos as $item){
            foreach ($list as $k=>$val){
                $stockininfo = $stock->getStockinnoByStockId($item['sysno']);
                $stockin_no = $stockininfo[0]['stockin_no'];
                if($item['sysno'] == $val['stock_sysno']){
                    $list[$k]['stock_sysno'] = $item['sysno'];
                    $list[$k]['stockno'] = $item['stockno'];
                    $list[$k]['stockin_no'] = $stockin_no;
                    $list[$k]['instockdate'] = $item['instockdate'];
                    $list[$k]['goodsname'] = $item['goodsname'];
                    $list[$k]['qualityname'] = $item['qualityname'];
                    $list[$k]['goodsnature'] = $item['goodsnature'];
                    $list[$k]['stockqty'] = $item['stockqty'];
                    $list[$k]['storagetankname'] = $item['storagetankname'];
                    $list[$k]['stockinno'] = $item['stockinno'];
    
                }
            }
    
        }

        return $list;
    }


    /**
     * @title 根据编号查询货存专业预约单的信息
     * @param $id
     * @return mixed
     */
    public function getbooktransbyid($id){
        $sql = " SELECT trans.*,sale.customername as salename,buy.customername as buyname,
                coc.contractno as cocno  FROM `".DB_PREFIX."doc_booking_trans` as trans
                LEFT JOIN ".DB_PREFIX."customer as sale on sale.sysno = trans.sale_customer_sysno
                LEFT JOIN ".DB_PREFIX."customer as buy on buy.sysno = trans.buy_customer_sysno
                LEFT JOIN ".DB_PREFIX."doc_contract as coc on coc.sysno = trans.contract_sysno
                where trans.sysno = ".$id." and trans.isdel = 0";
        return $this->dbh->select_row($sql);
    }

    /**
     * @title 更新货存转移信息
     * @param $data
     * @param $id
     */
    public function updatebooktrans($data,$id){
        return $this->dbh->update(DB_PREFIX.'doc_booking_trans', $data, 'sysno=' . intval($id));
    }
    

}
<?php
/**
 * 泊位分配单model
 * User: Jay Xu
 * Date: 2017/7/7
 */
class BerthorderModel
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
     *业务类型：1船入库预约、2船入库订单、3车入库预约、4车入库订单、5管入库预约、6管入库订单、7船出库预约、8船出库订单、9车出库预约、10车出库订单、
     * 11管出库预约、12管出库订单、13靠泊装卸入预约、14靠泊装卸出预约、15靠泊装卸入订单、16靠泊装卸出订单
     *@param  [array] $data array('businesstype'=>业务类型,'booking_sysno'=>所属预约单表主键,'bookingno'=>所属预约单编号,'stock_sysno'=>所属订单表主键,'stockno'=>所属订单编号,'bookingdate'=>预计到货（港）日期,'shipname'=>船名)
     *@return [type]       [description]
     */

    public function newBerthorder($data)
    {
        $data['berthorderno'] = COMMON::getCodeId('P2');

        if(!isset($data['businesstype']) || $data['businesstype']=='')
        {
            return ['code'=>300, 'message'=>'业务类型不能为空!'];
        }

        if(!isset($data['booking_sysno']) || $data['booking_sysno']=='')
        {
            return ['code'=>300, 'message'=>'所属预约单表主键不能为空!'];
        }

        if(!isset($data['bookingno']) || $data['bookingno']=='')
        {
            return ['code'=>300, 'message'=>'所属预约单表单号不能为空!'];
        }

        if(!isset($data['bookingdate']) || $data['bookingdate']=='')
        {
            return ['code'=>300, 'message'=>'所属预约单表预约时间不能为空!'];
        }

        $data['orderstatus'] = 2;
        $data['applydate']   = '=NOW()';
        $data['status']      = 1;
        $data['isdel']       = 0;
        $data['version']     = 1;
        $data['created_at']  = '=NOW()';
        $data['updated_at']  = '=NOW()';
        $res = $this->dbh->insert(DB_PREFIX.'doc_berthorder',$data);

        if(!$res)
        {
            return ['code'=>300, 'message'=>'添加泊位分配单失败'];
        }else{
            return ['code'=>200, 'message'=>$res];
        }
    }

    /**
     * 根据条件检索泊位
     * @return 数组
     * @author Jay Xu
     */
    public function searchBerthorder($params)
    {
        $filter = array();


        if (isset($params['startTime']) && $params['startTime'] != '') {
            $filter[] = "db. `created_at` >='{$params['startTime']} 00:00:00' ";
        }
        if (isset($params['endTime']) && $params['endTime'] != '') {
            $filter[] = "db. `created_at` <= '{$params['endTime']} 23:59:59' ";
        }

        if (isset($params['businesstype']) && $params['businesstype'] != '') {
            $filter[] = " `businesstype`='{$params['businesstype']}'";
        }

        if (isset($params['orderstatus']) && $params['orderstatus'] != '') {
            $filter[] = " `orderstatus` ='{$params['orderstatus']}'";
        }

        $where = ' ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM ".DB_PREFIX."doc_berthorder db where isdel = 0 {$where} {$order}  ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {

            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT db.*,dbd.shipname,dbd.beintime,dbd.beouttime
                        FROM ".DB_PREFIX."doc_berthorder db
                        LEFT JOIN ".DB_PREFIX."doc_berthorder_detail dbd ON dbd.berthorder_sysno = db.sysno
                        where db.isdel = 0 {$where} {$order}";


                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );
                $sql = "SELECT db.*,dbd.shipname,dbd.beintime,dbd.beouttime
                        FROM ".DB_PREFIX."doc_berthorder as db
                        LEFT JOIN ".DB_PREFIX."doc_berthorder_detail as dbd ON dbd.berthorder_sysno = db.sysno
                        where db.isdel = 0 {$where} {$order}";

                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);

                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /**
     * 新增泊位数据
     * @return boolean
     * @author Jay Xu
     */
    public function addBerthorder($detail,$input,$id,$step,$getbookdata)
    {
        //print_r($getbookdata);die;
        $this->dbh->begin();
        try {
            $orderstatus = $input['orderstatus'];
           if($orderstatus==3){
               $input['orderstatus'] = 3;
           }
            $inputMain = array(
                'bookingdate' => $input['bookingdate'],             //预约时间
                'orderstatus' => $step,                             //单据状态
                'applydate' => $input['applydate'],                 //申请時間
                'apply_user_sysno' => $input['apply_user_sysno'],   //申请人
                'apply_employeename' => $input['apply_employeename'],//申请人
                'updated_at' => '=NOW()'
            );
            if ($input['orderstatus'] == 3 && $step == 2) {
                $inputMain['orderstatus'] = 3;
            }


            $res = $this->dbh->update(DB_PREFIX . 'doc_berthorder', $inputMain, 'sysno=' . $id);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新主表失败'];
            }
            // 删除明细表数据
            $res = $this->dbh->delete(DB_PREFIX . 'doc_berthorder_detail', 'berthorder_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '删除明细失败'];
            }
            //循环插入详情表数据
            foreach ($detail as $value) {
                $detaildata['berthorder_sysno'] = $id; //所属泊位单表主键
                $detaildata['berthorderno'] = $input['berthorderno']; //所属泊位单编号
                $detaildata['berth_sysno'] = $value['berth_sysno']; //泊位id
                $detaildata['berthname'] = $value['berthname']; //泊位号
                $detaildata['berthloadcapacity'] = $value['berthloadcapacity']; //允许最大吃水(米)
                $detaildata['berthlength'] = $value['berthlength']; //泊位长度(米)
                $detaildata['berthdeep'] = $value['berthdeep']; //泊位水深(米)
                $detaildata['berthtype'] = $value['berthtype']; //核准停泊船型
                $detaildata['berthloadweight'] = $value['berthloadweight']; //泊位水深(米)
                $detaildata['berthdeep'] = $value['berthdeep']; //核准停泊能力(吨)
                $detaildata['wharf_sysno'] = $value['wharf_sysno']; //所属码头表主键
                $detaildata['wharfname'] = $value['wharfname']; //所属码头名称
                $detaildata['shipname'] = $value['shipname']; //船名
                $detaildata['captain'] = $value['captain']; //船长
                $detaildata['shipcontact'] = $value['shipcontact']; //联系方式
                $detaildata['planintime'] = $value['planintime']; //计划靠泊时间
                $detaildata['planouttime'] = $value['planouttime']; //计划离泊时间
                $detaildata['beintime'] = $value['beintime']; //实际靠泊时间
                $detaildata['beouttime'] = $value['beouttime']; //实际离泊时间
                $detaildata['memo'] = $value['memo']; //备注
                $detaildata['created_at'] = '=NOW()'; //创建时间
                $detaildata['updated_at'] = '=NOW()'; //最后更新时间

                $res = $this->dbh->insert(DB_PREFIX . 'doc_berthorder_detail', $detaildata);

                if (!$res) {
                    $this->dbh->rollback();
                    return ['statusCode' => 300, 'msg' => '更新泊位详细单失败'];
                }
            }



            //插入泊位使用记录
            if($step==3){
                //获取业务单号id
                $orderinfo = $this->getBerthorderById($id);
                if($orderinfo['stock_sysno']){
                    $orderid = $orderinfo['stock_sysno'];
                }else{
                    $orderid = $orderinfo['booking_sysno'];
                }

                $session = Yaf_Registry::get(SSN_VAR);

                foreach($detail as $value){

                    $info = [
                        'berth_sysno'=>$value['berth_sysno'],//泊位id
                        'berthno'=>$value['berthname'],//泊位号
                        'wharf_sysno'=>$value['wharf_sysno'],
                        'wharfname'=>$value['wharfname'],
                        'shipname'=>$value['shipname'],
                        'goods_sysno'=>$getbookdata[0]['goods_sysno'],
                        'goodsname'=>$getbookdata[0]['goodsname'],
                        'businesstype'=>$input['businesstype'],
                        'business_sysno'=>$orderid,
                        'businessno'=>$input['orderno'],
                        'created_user_sysno'=>$session['employee_sysno'],
                        'created_employeename'=>$session['employeename'],
                        'usetime'=>'=NOW()',
                        'memo'=>$value['memo'],
                        'created_at'=>'=NOW()',
                        'updated_at'=>'=NOW()',
                    ];


                    $res = $this->dbh->insert(DB_PREFIX.'base_berth_uselog', $info);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'插入泊位使用记录失败'];
                    }
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  => 21,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );

            if($step==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存泊位单';
            }elseif($step==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交泊位单';
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



    /**
     * 根据id获得泊位细节
     * id: 权限id
     * @return 数组
     */
    public function getBerthorderById($id = 0)
    {
        $sql = "select * from ".DB_PREFIX."doc_berthorder  where sysno = $id ";
        return $this->dbh->select_row($sql);
    }



    /**
     * 泊位更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateBerth($id = 0, $data = array())
    {
        return  $this->dbh->update(DB_PREFIX.'doc_berthorder', $data, 'sysno=' . intval($id));

    }

    /**
     * 获取泊位明细数据
     * @return boolean
     * @author Jay Xu
     */
    public function getListDetails($params)
    {

        $filter =[];
        if(isset($params['sysno']) && $params['sysno'] !=''){
            $filter[] = "berthorder_sysno = {$params['sysno']}";
        }


        $where = 'WHERE isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `sysno` desc";

        $sql = "select * from `".DB_PREFIX."doc_berthorder_detail` {$where} {$order}";

        $result = $this->dbh->select($sql);

        if($result){
            return $result;
        }

    }

    /**
     * 获取泊位明细数据
     * @return boolean
     * @author Jay Xu
     */
    public function getDetails($params)
    {

        $filter =[];

        if(isset($params['book_sysno']) && $params['book_sysno'] !=''){
            $filter[] = "booking_sysno = {$params['book_sysno']}";
        }
        if(isset($params['businesstype']) && $params['businesstype'] !=''){
            $filter[] = "businesstype = {$params['businesstype']}";
        }

        $where = 'WHERE b.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }


        $sql = "select * from `".DB_PREFIX."doc_berthorder_detail` bd
                left join ".DB_PREFIX."doc_berthorder b on b.sysno = bd.berthorder_sysno
                {$where} ";

        $result = $this->dbh->select($sql);

        if($result){
            return $result;
        }

    }






}
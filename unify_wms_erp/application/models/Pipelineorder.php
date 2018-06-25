<?php
/**
 * Created by PhpStorm.
 * User: jp
 * Date: 2017/7/06 0017
 * Time: 11:42
 */

class PipelineorderModel
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
     * @author jp
     */
    public function searchPipeline($params)
    {
        $filter = array();


        if (isset($params['startTime']) && $params['startTime'] != '') {
            $filter[] = " `created_at` >='{$params['startTime']} 00:00:00' ";
        }
        if (isset($params['endTime']) && $params['endTime'] != '') {
            $filter[] = " `created_at` <= '{$params['endTime']} 23:59:59' ";
        }

        if (isset($params['businesstype']) && $params['businesstype'] != '') {
            $filter[] = " `businesstype`='{$params['businesstype']}'";
        }

        if (isset($params['orderstatus']) && $params['orderstatus'] != '') {
            $filter[] = " `orderstatus` ='{$params['orderstatus']}'";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_pipelineorder` {$where} {$order} ";
       //echo $sql;
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
      //print_r($result['totalRow']);die;
        if ($result['totalRow'])
        {

            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."doc_pipelineorder` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "SELECT *,DATE_FORMAT(bookingdate,'%Y-%m-%d') as bookingdate  FROM `".DB_PREFIX."doc_pipelineorder` {$where} {$order} ";

                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);

                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /*
     *
     * 提交保存
     * */
    public function addpipelineorder($detail,$input,$id){
        $this->dbh->begin();
        try {
            $step = $input['step'];
                $inputMain = array(
                    'bookingdate' => $input['bookingdate'],//预约时间
                    'orderstatus' =>$step,                 //单据状态
                    'applydate' => $input['applydate'],                 //申请時間
                    'apply_user_sysno' =>$input['apply_user_sysno'],      //申请人
                    'apply_employeename' =>$input['apply_employeename'],      //申请人
                    'created_user_sysno' =>$input['created_user_sysno'],              //创建人
                    'created_employeename' =>$input['created_employeename'],              //创建人
                    'updated_at' => '=NOW()'
                );
            if($input['orderstatus']==3 && $step==2){
                $inputMain['orderstatus'] = 3;
            }


            $res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$inputMain,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            // 删除明细表数据
            $res = $this->dbh->delete(DB_PREFIX.'doc_pipelineorder_detail', 'pipelineorder_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'删除明细失败'];
            }
            //循环插入详情表数据
            foreach ($detail as $value) {
                $detaildata['pipelineorder_sysno'] =$id; //管线编号
                $detaildata['pipelineorderno'] =$input['pipelineorderno']; //管线编号
                $detaildata['wharf_pipelineno'] = $value['wharf_pipelineno']; //管线编号
                $detaildata['wharf_pipeline_sysno'] =$value['wharf_pipeline_sysno']; //管线编号
                $detaildata['area_pipeline_sysno'] = $value['area_pipeline_sysno']; //码头管线号编号
                $detaildata['area_pipelineno'] = $value['area_pipelineno']; //库区管线号编号
                $detaildata['goods_sysno'] = $value['goods_sysno']; //货品信息表主键
                $detaildata['goodsname'] = $value['goodsname']; //货品名称
                $detaildata['estimateqty'] = $value['estimateqty']; //预计吨数
                $detaildata['estimatedate'] = $value['estimatedate']; //预约时间
                $detaildata['beforeqty'] = $value['beforeqty']; //收发货前流量吨数
                $detaildata['afterqty'] = $value['afterqty']; //收发货后流量吨数
                $detaildata['beqty'] = $value['beqty']; //实际流量吨数
                $detaildata['startpumptime'] = $value['startpumptime']; //启泵时间
                $detaildata['stoppumptime'] = $value['stoppumptime']; //停泵时间
                $detaildata['memo'] = $value['memo']; //memo
                $detaildata['storagetankname'] = $value['storagetankname']; //储罐号
                $detaildata['storagetank_sysno'] = $value['storagetank_sysno']; //储罐编号
                $detaildata['shipname'] = $value['shipname']; //船名
                $detaildata['captain'] = $value['captain']; //船长
                $detaildata['updated_at'] = '=NOW()';
                $detaildata['created_at'] = '=NOW()';

                $res = $this->dbh->insert(DB_PREFIX.'doc_pipelineorder_detail', $detaildata);

                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'更新管线详细单失败'];
                }

            }

            //插入管线使用记录
            if($step==3){
                //获取业务单号id
                $orderinfo = $this->getPipelineMian($id);
               // print_r($orderinfo);die;
                if($orderinfo['stock_sysno']){
                    $orderid = $orderinfo['stock_sysno'];
                }else{
                    $orderid = $orderinfo['booking_sysno'];
                }

                foreach($detail as $value){
                    $info = [
                        'pipeline_sysno'=>$value['wharf_pipeline_sysno'],//管线/库区id
                        'pipelineno'=>$value['wharf_pipelineno'],//管线/库区号
                        'pipelinetype'=>1,
                        'pipelineflow'=>$value['beqty'],
                        'businesstype'=>$input['businesstype'],
                        'goods_sysno'=>$value['goods_sysno'],
                        'goodsname'=>$value['goodsname'],
                        'business_sysno'=>$orderid,
                        'businessno'=>$input['orderno'],
                        'created_user_sysno'=>$input['created_user_sysno'],
                        'created_employeename'=>$input['created_employeename'],
                        'usetime'=>'=NOW()',
                        'memo'=>$value['memo'],
                        'created_at'=>'=NOW()',
                        'updated_at'=>'=NOW()',
                    ];

                    $res = $this->dbh->insert(DB_PREFIX.'base_pipeline_uselog', $info);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'插入码头管线失败'];
                    }
                    $info['pipelinetype'] = 2;
                    $info['pipeline_sysno']=$value['area_pipeline_sysno'];
                    $info['pipelineno']=$value['area_pipelineno'];
                    $res = $this->dbh->insert(DB_PREFIX.'base_pipeline_uselog', $info);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'插入库区管线失败'];
                    }
                }
            }


            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  => 20,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );

            if($step==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存管线单';
            }elseif($step==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交管线单';
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
     * 获取管线明细数据
     * @return boolean
     * @author jp
     */
    public function getListDetials($params)
    {

        $filter =[];
        if(isset($params['sysno']) && $params['sysno'] !=''){
           $filter[] = "pipelineorder_sysno = {$params['sysno']}";
        }

        if(isset($params['book_sysno']) && $params['book_sysno'] !=''){
            $filter[] = "booking_sysno = {$params['book_sysno']}";
        }

         $where = 'WHERE isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `sysno` desc";

        $sql = "select * from `".DB_PREFIX."doc_pipelineorder_detail` {$where} {$order}";

       $result = $this->dbh->select($sql);

        if($result){
            return $result;
        }

    }


    /**
     * 根据id获得管线细节
     * id: 权限id
     * @return 数组
     */
    public function getPipelineMian($id = 0)
    {
        $sql = "select * from ".DB_PREFIX."doc_pipelineorder  where sysno = $id ";

        return $this->dbh->select_row($sql);
    }


    /**
     * 删除更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateBerth($id = 0, $data = array())
    {
        return  $this->dbh->update(DB_PREFIX.'doc_pipelineorder', $data, 'sysno=' . intval($id));

    }

/*
 * 插入管线主编数据
 *业务类型：1船入库预约、2船入库订单、3车入库预约、4车入库订单、5管入库预约、6管入库订单、7船出库预约、
 * 8船出库订单、9车出库预约、10车出库订单、11管出库预约、12管出库订单、13靠泊装卸
  *入预约、14靠泊装卸出预约、15靠泊装卸入订单、16靠泊装卸出订单
 * */
    public function insertPipelineMain($data)
    {
        $data['pipelineorderno'] = COMMON::getCodeId('G');
        if(!isset($data['businesstype']) || $data['businesstype']=='')
        {
            return ['code'=>300, 'message'=>'业务类型不能为空!'];
        }

        if(!isset($data['booking_sysno']) || $data['booking_sysno']=='')
        {
            return ['code'=>300, 'message'=>'所属预约单表主键不能为空!'];
        }

        $data['orderstatus'] = 2;
        $res = $this->dbh->insert(DB_PREFIX.'doc_pipelineorder',$data);

        if(!$res)
        {
            return ['code'=>300, 'message'=>'添加管线分配单失败'];
        }else{
            return ['code'=>200, 'message'=>$res];
        }

    }
    /**
     *  获取 罐容详情信息
     *  @author
     *  @params ID,货品名,储罐编号 规格 计量单位 待入量 待出量 现存量 储罐材质 储罐性质 ,商品规格id ，商品规格。
     */
    public function getStockRetankInfo()
    {

        $sql = "SELECT bs.*,bg.goodsname
                FROM ".DB_PREFIX."base_storagetank bs
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno and bg.isdel=0 and bg.status<2
                WHERE  bs.`status` = 1 AND bs.isdel = 0 ORDER BY bs.sysno ASC";

        return $this->dbh->select($sql);
    }

    /**
     * 获取管线明细数据
     * @return boolean
     * @author jp
     */
    public function getDetials($params)
    {

        $filter =[];
        if(isset($params['sysno']) && $params['sysno'] !=''){
            $filter[] = "pipelineorder_sysno = {$params['sysno']}";
        }

        if(isset($params['book_sysno']) && $params['book_sysno'] !=''){
            $filter[] = "booking_sysno = {$params['book_sysno']}";
        }
        if(isset($params['businesstype']) && $params['businesstype'] !=''){
            $filter[] = "businesstype = {$params['businesstype']}";
        }

        $where = 'WHERE p.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by pd.`sysno` desc";

        $sql = "select * from `".DB_PREFIX."doc_pipelineorder_detail` pd
                RIGHT join ".DB_PREFIX."doc_pipelineorder p on p.sysno = pd.pipelineorder_sysno
                 {$where} {$order}";

        $result = $this->dbh->select($sql);

        if($result){
            return $result;
        }

    }


    /*
     * 获取所有商品明细
     * */
    public function getgoodsInfo()
    {

        $sql = "SELECT bg.*,u.unitname
                FROM ".DB_PREFIX."base_goods bg
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = bg.sysno
                LEFT JOIN ".DB_PREFIX."base_unit u ON u.sysno = ga.unit_sysno
                WHERE ga.`status` < 2 AND ga.isdel = 0 AND  bg.isdel = 0 and bg.status < 2 ORDER BY bg.sysno ASC";

        return $this->dbh->select($sql);
    }

    /*
     * 获取管线基础资料
     * */
    public function getBasePipe($params){

        $filter =[];
        if(isset($params['pipelinetype']) && $params['pipelinetype'] !=''){
            $filter[] = "pipelinetype = {$params['pipelinetype']}";
        }

        $where = 'WHERE isdel = 0 and status < 2';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `sysno` desc";

        $sql = "select * from `".DB_PREFIX."base_pipeline` {$where} {$order}";

        $result = $this->dbh->select($sql);

        if($result){
            return $result;
        }
    }

    //获取入船名
    public  function getshipnameIn($input){
        if($input['stock_sysno']){
            $sql = "select sind.shipname from ".DB_PREFIX."doc_stock_in  sin
                    left join ".DB_PREFIX."doc_stock_in_detail sind on sin.sysno = sind.stockin_sysno
                    where sin.sysno = {$input['stock_sysno']} ";
            $res = $this->dbh->select_row($sql);

        }else{
            $sql = "select bsind.shipname from ".DB_PREFIX."doc_booking_in  bsin
                    left join ".DB_PREFIX."doc_booking_in_detail bsind on bsin.sysno = bsind.bookingin_sysno
                    where bsin.sysno = {$input['booking_sysno']} ";
            $res = $this->dbh->select_row($sql);
        }

        return $res;
    }

    //获取出船名
    public  function getshipnameOut($input){
        if($input['stock_sysno']){
            $sql = "select sod.shipname from ".DB_PREFIX."doc_stock_out  so
                    left join ".DB_PREFIX."doc_stock_out_detail sod on so.sysno = sod.stockout_sysno
                    where so.sysno = {$input['stock_sysno']} ";
            $res = $this->dbh->select_row($sql);

        }else{
            $sql = "select bsod.shipname from ".DB_PREFIX."doc_booking_out  bso
                    left join ".DB_PREFIX."doc_booking_out_detail bsod on bso.sysno = bsod.bookingout_sysno
                    where bso.sysno = {$input['booking_sysno']} ";
            $res = $this->dbh->select_row($sql);
        }

        return $res;
    }



}
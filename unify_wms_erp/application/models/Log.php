<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 10:40
 */
class LogModel
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
     * 查询岗位列表
     * @author hanshutan
     */
    public function searchPrivilegeLog($params)
    {

        $filter = array();

        if (isset($params['bar_controller']) && $params['bar_controller'] != '') {
            $filter[] = " p.controller LIKE '%" . $params['bar_controller'] . "%' ";
        }
        if (isset($params['bar_action']) && $params['bar_action'] != '') {
            $filter[] = " p.action LIKE '%" . $params['bar_action'] . "%' ";
        }
        if (isset($params['bar_realname']) && $params['bar_realname'] != '') {
            $filter[] = " p.user_realname LIKE '%" . $params['bar_realname'] . "%' ";
        }

        $where = '1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."system_log_privilege p where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);


            $sql = "select p.* from ".DB_PREFIX."system_log_privilege p where {$where} ";
            if ($params['orders'] != '')
                $sql .= " order by " . $params['orders'];
            else
                $sql .= " order by  created_at desc ";

            $arr = $this->dbh->select_page($sql);


            $result['list'] = $arr;
        }

        return $result;
    }

    public function searchDocLog($params)
    {

        $filter = array();

        if (isset($params['bar_id']) && $params['bar_id'] != '-100') {
            $filter[] = " l.`doc_sysno`='{$params['bar_id']}'";
        }
        if (isset($params['bar_doctype']) && $params['bar_doctype'] != '-100') {
            $filter[] = " l.`doctype`='{$params['bar_doctype']}'";
        }

        $where = '1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."doc_log l where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {

            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select l.* from ".DB_PREFIX."doc_log l where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];

                $arr = $this->dbh->select($sql);


                $result['list'] = $arr;
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "select l.* from ".DB_PREFIX."doc_log l where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }

                $arr = $this->dbh->select_page($sql);

                $result['list'] = $arr;
            }
        }

        //统一处理显示返回
        //doc_sysno是每张单据表主键
        //doctype含义：1入库预约单2出库预约单3船入库订单4车入库订单5船出库订单6车出库订单7车入库磅码单8车出库磅码单9货权转移单10盘点
        //11清库12倒罐订单13费用单14开票通知单15收款单16合同17结算核销单18 靠泊装卸(装)预约单 19 靠泊装卸(卸)预约单 20 管线分配单
        //21 泊位分配单 22 品质检查单 23 管线入库预约 24 管线出库预约 25 管线入库订单 26 管线出库订单 27 靠泊装货订单 28 靠泊卸货订单 29 提单 30 倒罐申请单
        //31退货32补充入库33库存调整34车辆核对35退货磅码单
        //opertype根据实际doctype自定义 详细代码在case判断里
        //注意：第一次insert单据的时候，必须有2条操作记录。新建和保存。
        foreach ($result['list'] as $key => $value) {
            $result['list'][$key]['opertype'] = $this->operconf($value['doctype'], $value['opertype']);
        }
        return $result;
    }

    private function operconf($doctype = 0, $opertype = 0)
    {
        $cfg = array(
            1 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待确认', 3 => '确认通过', 4 => '审核通过', 5 => '生成入库单', 6 => '退回', 7 => '登记车辆',8 => '登记放行信息',9=>'驳回订单',
            ),
            2 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '确认通过', 4 => '审核通过', 5 => '生成出库单', 6 => '退回', 7 => '登记车辆',8=>'驳回订单',
            ),
            3 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废', 6 => '退回', 7 => '审核驳回'
            ),
            4 => array(
                0 => '新建', 1 => '暂存', 2 => '提交入库', 3 => '终止订单', 4 => '作废', 5 => '增加车辆',
            ),
            5 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',6 => '待执行'
            ),
            6 => array(
                0 => '新建', 1 => '暂存', 2 => '提交出库', 3 => '终止订单', 4 => '作废', 5 => '增加车辆', 6 => '终止'
            ),
            7 => array(
                0 => '新建', 1 => '核单完成', 2 => '重车过磅', 3 => '空车过磅', 4 => '作废',
            ),
            8 => array(
                0 => '新建', 1 => '核单完成', 2 => '空车过磅', 3 => '重车过磅', 4 => '作废',
            ),
            9 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废', 5 => '退回',7=>'驳回订单',
            ),
            10 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '退回', 5 => '作废',
            ),
            11 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过',  5 => '退回' ,6=> '作废',
            ),
            12 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废', 5 => '退回',
            ),
            13 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '删除', 5 => '退回',
            ),
            14 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废', 5 => '退回', 6 => '已关闭'
            ),
            15 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废', 5 => '退回',
            ),
            16 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待评审', 3 => '提交待审核', 4 => '审核通过', 5 => '退回', 6 => '作废', 7 => '评审通过', 8 => '评审不通过',
            ),
            17 => array(
                0 => '新建', 1 => '新建', 2 => '完成', 3 => '删除'
            ),
            18 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            19 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            20 => array(
                0 => '新建', 1 => '暂存', 2 => '提交', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            21 => array(
                0 => '新建', 1 => '暂存', 2 => '提交', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            22 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '退回',5 => '让步待审核',6 => '让步审核通过',7 => '终止',
            ),
            23 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '确认通过', 4 => '审核通过', 5 => '生成入库单', 6 => '退回',
            ),
            24 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '确认通过', 4 => '审核通过', 5 => '生成出库单', 6 => '退回',
            ),
            25 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',7=>'审核驳回',
            ),
            26 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',6 => '待执行'
            ),
            27 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            28 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            29 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '提货中', 4 => '已完成', 5 => '退回', 6 => '已撤销', 7 => '驳回',
            ),
            30 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            31 => array(
                0 => '新建', 2 => '暂存', 3 => '提交待审核', 4 => '已审核', 6 => '退回', 7 => '作废',
            ),
            32 => array(
                0 => '新建', 2 => '暂存', 3 => '提交待审核', 4 => '已审核', 6 => '退回', 7 => '作废',
            ),
            33 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '作废',5 => '退回',
            ),
            34 => array(
                0 => '新建', 1 => '暂存', 2 => '提交待审核', 3 => '审核通过', 4 => '审核不通过',5 => '作废',
            ),
            35 => array(
                0 => '新建', 1 => '核单完成', 2 => '编辑磅码单', 3 => '重车过磅', 4 => '空车过磅',5 => '作废',
            ),

        );
        $logname = $cfg[$doctype][$opertype] ? $cfg[$doctype][$opertype] : '未定义操作';

        return $logname;
    }

    /**
     * 搜索储罐日志表
     * @param $params
     * @return mixed
     */
    public function searchStoragetankLog($params)
    {
        $filter = array();

        if (isset($params['doc_sysno']) && $params['doc_sysno'] != '') {
            $filter[] = "`doc_sysno`='{$params['bar_id']}'";
        }
        if (isset($params['docno']) && $params['docno'] != '') {
            $filter[] = "`docno`='{$params['docno']}'";
        }
        if (isset($params['doctype']) && $params['doctype'] != '') {
            $filter[] = "`doctype`='{$params['doctype']}'";
        }
        if (isset($params['pounds_sysno']) && $params['pounds_sysno'] != '') {
            $filter[] = "`pounds_sysno`='{$params['pounds_sysno']}'";
        }
        if (isset($params['poundsno']) && $params['poundsno'] != '') {
            $filter[] = "`poundsno`='{$params['poundsno']}'";
        }
        if (isset($params['pounds_type']) && $params['pounds_type'] != '') {
            $filter[] = "`pounds_type`='{$params['pounds_type']}'";
        }


        $where = ' status = 1 AND isdel = 0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."doc_storagetank_log where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {

            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select * from ".DB_PREFIX."doc_storagetank_log  where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];

                $arr = $this->dbh->select($sql);


                $result['list'] = $arr;
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "select * from ".DB_PREFIX."doc_storagetank_log  where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }

                $arr = $this->dbh->select_page($sql);

                $result['list'] = $arr;
            }
        }
        return $result;
    }


    /**
     * insert_goods_record_log
     * @param $data
     * @return array
     */
    public function addGoodsRecordLog($data){
        if(!is_array($data)){
            $msgcode = array(
                'code' => 300,
                'message' => '非法调用 参数必需为数组',
            );
            return $msgcode;
        }
        if(!isset($data['goods_sysno']) || empty($data['goods_sysno'])){

            $msgcode = array(
                'code' => 300,
                'message' => '货品SYSNO 必填',
            );
            return $msgcode;
        }
//        if(!isset($data['storagetank_sysno']) || empty($data['storagetank_sysno'])){
//            $msgcode = array(
//                'code' => 300,
//                'message' => '实际罐号SYSNO 必填',
//            );
//            return $msgcode;
//        }
//        if(!isset($data['customer_sysno']) || empty($data['customer_sysno'])){
//            $msgcode = array(
//                'code' => 300,
//                'message' => '客户SYSNO 必填',
//            );
//            return $msgcode;
//        }
        if(!isset($data['beqty'])){
            $data['beqty'] = floatval($data['beqty']);
            $msgcode = array(
                'code' => 300,
                'message' => '实际数量 必填',
            );
            return $msgcode;
        }
        if(!isset($data['tobeqty']) || empty($data['tobeqty'])){
            $data['tobeqty'] = floatval($data['tobeqty']);
            $msgcode = array(
                'code' => 300,
                'message' => '通知/提单数量 必填',
            );
            return $msgcode;
        }
//        if(!isset($data['stockinno']) || empty($data['stockinno'])){
//            $msgcode = array(
//                'code' => 300,
//                'message' => '所属入库订单SYSNO 必填',
//            );
//            return $msgcode;
//        }
        if(!isset($data['doc_sysno']) || empty($data['doc_sysno'])){
            $msgcode = array(
                'code' => 300,
                'message' => '发生操作的单据SYSNO 必填',
            );
            return $msgcode;
        }
        if(!isset($data['doc_type']) || empty($data['doc_type'])){
            $msgcode = array(
                'code' => 300,
                'message' => '类型 必填',
            );
            return $msgcode;
        }
        // if(!in_array($data['doc_type'] , [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17])){
        //     $msgcode = array(
        //         'code' => 300,
        //         'message' => '请填写正确的类型',
        //     );
        //     return $msgcode;
        // }
//        if(!isset($data['accountstoragetank_sysno']) || empty($data['accountstoragetank_sysno'])){
//            $msgcode = array(
//                'code' => 300,
//                'message' => '账面罐号SYSNO 必填',
//            );
//            return $msgcode;
//        }
//        if(!isset($data['stock_sysno']) || empty($data['stock_sysno'])){
//            $msgcode = array(
//                'code' => 300,
//                'message' => '库存SYSNO 必填',
//            );
//            return $msgcode;
//        }
        $data['isdel'] = 0;
        $data['status'] = 1;
        $data['doc_time'] = '=NOW()';
        $data['created_at'] = '=NOW()';
        $data['updated_at'] = '=NOW()';
        $result  =  $this->dbh->insert(DB_PREFIX.'doc_goods_record_log', $data);
        if(!$result){
            $msgcode = array(
                'code' => 300,
                'message' => '新增进出货物记录表失败',
            );
            return $msgcode;
        }
        $msgcode = array(
            'code' => 200,
            'message' => $result,
        );
        return $msgcode;
    }
    /*
 * 储罐性质与货物性质对应关系
 * 储罐性质：1内贸罐，2外贸罐，3保税罐
 *货物性质：1保税,2外贸,3内贸转出口,4内贸内销
 *内贸罐对应的货物是内贸内销；保税罐对应的货物是保税；外贸罐对应的货物是外贸和内贸转出口；
     *
        $arr=[
        '1'=>[4],
        '2'=>[2,3],
        '3'=>[1],
        ]
    *$tank=>储罐性质
    * $goodsnature=>货物性质
     * 调用时传一个二个参数
* */

    /*
     *  * 内贸转出口是入内贸罐、内贸内销改成内贸，也入内贸罐 ,外贸外贸罐、保税入保税罐，
     * *
        $arr=[
        '1'=>[3,4],
        '2'=>[2],
        '3'=>[1],
        ]
     * */
    /**
     * @param array $rule
     */

    public function tankTonature($tankId,$goodsnature){
       if(!$tankId || !$goodsnature){
           $msgcode = array(
               'code' => 300,
               'message' => '参数错误',
           );
           return $msgcode;
       }
       //根据储罐id查询储罐性质
        if($tankId){
            $sql = "select storagetanknature,storagetankname from ".DB_PREFIX."base_storagetank where  sysno = {$tankId} and isdel=0 and status < 2";
            $tankData = $this->dbh->select_row($sql);
            if(!$tankData){
                $msgcode = array(
                    'code' => 300,
                    'message' => '获取储罐性质失败',
                );
                return $msgcode;
            }
        }else{
            $msgcode = array(
                'code' => 300,
                'message' => '储罐id异常',
            );
            return $msgcode;
        }
        if(!in_array($tankData['storagetanknature'],[1,2,3])){
            $msgcode = array(
                'code' => 300,
                'message' => '储罐性质错误',
            );
            return $msgcode;
        }
        if(!in_array($goodsnature,[1,2,3,4])){
            $msgcode = array(
                'code' => 300,
                'message' => '货物性质错误',
            );
            return $msgcode;
        }
       switch($tankData['storagetanknature']){
           case 1 :
             if(!in_array($goodsnature,[3,4])){
                 $msgcode = array(
                     'code' => 300,
                     'message' => '内贸罐-'.$tankData['storagetankname'].'-与货物性质不匹配',
                 );
                 return $msgcode;
             }
              break;
           case 2 :
               if($goodsnature !=2){
                   $msgcode = array(
                       'code' => 300,
                       'message' => '外贸罐-'.$tankData['storagetankname'].'-与货物性质不匹配',
                   );
                   return $msgcode;
               }
               break;
           case 3 :
               if($goodsnature !=1){
                   $msgcode = array(
                       'code' => 300,
                       'message' => '保税罐-'.$tankData['storagetankname'].'-与货物性质不匹配',
                   );
                   return $msgcode;
               }
               break;
          }

        $msgcode = array(
            'code' => 200,
            'message' => '验证成功',
        );
        return $msgcode;
    }

/*
 *
 * 船名不存在时调用
 * 基础资料新增一条
 * */
    public function  Insertshipname($shipname){
        $shipname = trim($shipname);
        if(!$shipname){
            $msgcode = array(
                'code' => 300,
                'message' => '船名不能为空',
            );
            return $msgcode;
        }
        $S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array (
            'shipname' => $shipname,
            'pageCurrent'  => COMMON :: P(),
            'pageSize'     => COMMON :: PR(),
            'page'         =>false,
        );
        $list = $S->searchShipList($search);
        $shipname_count = $list['totalRow'];
        if($shipname_count==0){
            $data = [
                    'shipname'=>$shipname,
                    'status'=>1,
                    'isdel'=>0,
                    'created_at'=>'=NOW()',
                    'updated_at'=>'=NOW()',
                 ];
            $res = $this->dbh->insert(DB_PREFIX."base_ship",$data);
            if(!$res){
                $msgcode = array(
                    'code' => 300,
                    'message' => '新增船名失败',
                );
                return $msgcode;
            }

        }

        $msgcode = array(
            'code' => 200,
            'message' => '成功',
        );
        return $msgcode;


    }



}
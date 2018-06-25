<?php
/**
 * Created by PhpStorm.
 * User: jp
 * Date: 2017/7/06 0017
 * Time: 11:42
 */

class TemreceivableModel
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
    public function searchTem($params)
    {
        $filter = array();

        if (isset($params['id']) && $params['id'] != '') {
            $filter[] = " `sysno` ='{$params['id']}' ";
        }

        if (isset($params['startTime']) && $params['startTime'] != '') {
            $filter[] = " `receivabledate` >='{$params['startTime']}' ";
        }
        if (isset($params['endTime']) && $params['endTime'] != '') {
            $filter[] = " `receivabledate` <= '{$params['endTime']}' ";
        }

        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[] = " `customer_sysno` = '{$params['customername']}' ";
        }

        if (isset($params['receivablestatus']) && $params['receivablestatus'] != '') {
            $filter[] = " `receivablestatus` = '{$params['receivablestatus']}' ";
        }


        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_finance_receivable` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {

            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."doc_finance_receivable` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "SELECT * FROM `".DB_PREFIX."doc_finance_receivable` {$where} {$order} ";

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
    public function addTemreceivable($input,$detail){
        $this->dbh->begin();
        try {

            $receivablestatus = $input['receivablestatus'];

            $res = $this->dbh->insert(DB_PREFIX.'doc_finance_receivable',$input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            $id = $res;

            // 删除明细表数据
            $res = $this->dbh->delete(DB_PREFIX.'doc_finance_receivable_detail', 'receivable_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'删除明细失败'];
            }
            //循环插入详情表数据
            foreach ($detail as $value) {
                $detaildata['receivable_sysno'] =$id; //所属收款单表主键
                $detaildata['costname'] =$value['costname']; //收费名称
                $detaildata['totalprice'] = $value['totalprice']; //收费金额
                $detaildata['memo'] =$value['memo']; //备注
                $detaildata['updated_at'] = '=NOW()';
                $detaildata['created_at'] = '=NOW()';

                $res = $this->dbh->insert(DB_PREFIX.'doc_finance_receivable_detail', $detaildata);

                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'添加临时费用详细单失败'];
                }

            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  15,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );
            if($receivablestatus==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存临时费用单';
            }elseif($receivablestatus==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交临时费用单';
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
     * 更新update
     *
     * */
     public function updateTemreceivable($input,$details,$id,$step){
        $this->dbh->begin();
        try {
            //获取当前状态
            $receivablestatus = $input['receivablestatus'];

            if($receivablestatus == 3 || $step==3){
                $input['receivablestatus'] = 3;
            }elseif($receivablestatus == 2 && $step == 6){
                $input['receivablestatus'] = 6;
            }else{
                $input['receivablestatus'] = 2;
            }

                    $res = $this->dbh->update(DB_PREFIX.'doc_finance_receivable',$input,'sysno='.$id);
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'更新主表失败'];
                    }

                    // 删除明细表数据
                    $res = $this->dbh->delete(DB_PREFIX.'doc_finance_receivable_detail', 'receivable_sysno=' . intval($id));

                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'删除明细失败'];
                    }
                     //循环插入详情表数据
                    foreach ($details as $value) {
                        $detaildata['receivable_sysno'] =$id; //所属收款单表主键
                        $detaildata['costname'] =$value['costname']; //收费名称
                        $detaildata['totalprice'] = $value['totalprice']; //收费金额
                        $detaildata['memo'] =$value['memo']; //备注
                        $detaildata['updated_at'] = '=NOW()';
                        $detaildata['created_at'] = '=NOW()';

                        $res = $this->dbh->insert(DB_PREFIX.'doc_finance_receivable_detail', $detaildata);

                        if (!$res) {
                            $this->dbh->rollback();
                            return  ['statusCode'=>300,'msg'=>'添加临时费用详细单失败'];
                        }

                    }

                    #库存管理业务操作日志
                    $user = Yaf_Registry::get(SSN_VAR);
                    $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                    $input= array(
                        'doc_sysno'  =>  $id,
                        'doctype'  =>  15,
                        'operemployee_sysno' => $user['employee_sysno'],
                        'operemployeename' => $user['employeename'],
                        'opertime'    => '=NOW()',
                    );
                    if($step==2){
                        $input['opertype'] = 1;
                        $input['operdesc'] = '暂存临时费用详细单';
                    }elseif($step==3){
                        $input['opertype'] = 2;
                        $input['operdesc'] = '提交临时费用详细单';
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
    * 审核
    *
    * */
    public function auditTemreceivable($input,$details,$id,$step){
        $this->dbh->begin();
        try {
            //获取当前状态
          //  $receivablestatus = $input['receivablestatus'];
            $info['receivablestatus'] = $step;
            $info['updated_at'] = "=NOW()";
            if($step==6){
              //  $info['auditreason'] = $input['auditreason'];
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_finance_receivable',$info,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  15,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );
            if($step==4){
                $input['opertype'] = 3;
                $input['operdesc'] = '审核通过临时费用详细单';
            }elseif($step==6){
                $input['opertype'] = 5;
                $input['operdesc'] = '审核不通过临时费用详细单';
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
    * 作废
    *
    * */
    public function backTemreceivable($input,$details,$id,$step){
        $this->dbh->begin();
        try {
            //获取当前状态
            //  $receivablestatus = $input['receivablestatus'];
            $info['receivablestatus'] = $step;
            $info['updated_at'] = "=NOW()";
        //  $info['abandonreason'] = $input['abandonreason'];//作废意见

            $res = $this->dbh->update(DB_PREFIX.'doc_finance_receivable',$info,'sysno='.$id);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  15,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
            );
            if($step==5){
                $input['opertype'] = 6;
                $input['operdesc'] = '审核通过临时费用详细单';
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
            $filter[] = "receivable_sysno = {$params['sysno']}";
        }

        $where = 'WHERE isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `sysno` desc";

        $sql = "select * from `".DB_PREFIX."doc_finance_receivable_detail` {$where} {$order}";

        $result = $this->dbh->select($sql);

        if($result){
            return $result;
        }

    }


    /**
     * 删除更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updatedel($id = 0, $data = array())
    {
        return  $this->dbh->update(DB_PREFIX.'doc_finance_receivable', $data, 'sysno=' . intval($id));

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







}
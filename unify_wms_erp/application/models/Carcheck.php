<?php
/**
 * User: HR
 * Date: 2017/07/25
 * Time: 10:20
 */

class CarcheckModel
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
     */
    public function getCarcheckList($params)
    {
        $filter = array();
        if (isset($params['operationtype']) && $params['operationtype'] != '') {
            $filter[] = " `operationtype`='{$params['operationtype']}'";
        }
        if (isset($params['carcheckstatus']) && $params['carcheckstatus'] != '') {
            $filter[] = " `carcheckstatus`='{$params['carcheckstatus']}'";
        }
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " `carid` like '%".$params['carid']."%'";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " AND " . implode(' AND ', $filter);
        }

        $order = "order by `updated_at` desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_carcheck` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {

            $sql = "SELECT * FROM `".DB_PREFIX."doc_carcheck` {$where} {$order} ";
            if( isset($params['page'] ) && $params['page'] == false){

                $data = $this->dbh->select($sql);

                $result['list'] = $data;

            }else{

                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );

                $data = $this->dbh->select_page($sql);

                $result['list'] = $data;
            }

        }

        return $result; //返回查询数据
    }


    public function addCarcheck($data)
    {
        return $this->dbh->insert(DB_PREFIX.'doc_carcheck', $data);
    }



    public function getCarcheckByid($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_carcheck` where sysno={$id}";
        return $this->dbh->select_row($sql);
    }

    public function updateCarcheck($data, $id)
    {
        return $this->dbh->update(DB_PREFIX.'doc_carcheck', $data, 'sysno='.intval($id));
    }

    public function aduitCarcheck($data, $id, $attach, $fanhui = false)
    {
        $this -> dbh -> begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_carcheck', $data, 'sysno='.intval($id));
            if(!$res){
                throw new Exception('审核失败', 300);
                return false;
            }
            if($id && $attach)  {
                $attachmentInstance = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                if (count($attach) > 0) {
                    $res = $attachmentInstance -> addAttachModelSysno($id, $attach);
                    if (!$res) {
                        COMMON::result(300, '添加附件失败');
                        return;
                    }
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            if($data['carcheckstatus'] == 4){
                $marks =  '车辆核对审核通过';
                //回写磅码单的车辆核对状态
                if($data['businesstype'] == 4){
                    #查询车入库磅码单信息
                    $sql = "SELECT p.sysno,p.poundsinno,p.carid,p.customername,p.goodsname, s.customer_sysno,sid.goods_sysno FROM `".DB_PREFIX."doc_pounds_in` p LEFT JOIN ".DB_PREFIX."doc_stock_in s ON p.stockin_sysno = s.sysno LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON  p.stockindetail_sysno = sid.sysno WHERE	p.sysno =".intval($data['business_sysno']);
                    $poundInData = $this -> dbh -> select_row($sql);

                    //审核通过是生成品质检查单 只有车入才需要
                    $qualitycheckData = array(
                        'qualitycheckno' => COMMON::getCodeId('J'),
                        'businesstype' => 3,
                        'booking_sysno' => $poundInData['sysno'],
                        'bookingno' => $poundInData['poundsinno'],
                        'bookingdate' => date('Y-m-d'),
                        'stock_sysno' => $poundInData['sysno'],
                        'stockno' => $poundInData['poundsinno'],
                        'applydate' => '=NOW()',
                        'apply_user_sysno' => $user['employee_sysno'],
                        'apply_employeename' => $user['employeename'],
                        'orderstatus' => 2,
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'carshipname' => $poundInData['carid'],
                        'customer_sysno' => $poundInData['customer_sysno'],
                        'customername' => $poundInData['customername'],
                        'goods_sysno' => $poundInData['goods_sysno'],
                        'goodsname' => $poundInData['goodsname'],
                    );
                    $qualitycheckRes = $this->dbh->insert(DB_PREFIX.'doc_qualitycheck', $qualitycheckData);
                    if(!$qualitycheckRes){
                        throw new Exception('创建品质检查单失败', 300);
                        return false;
                    }
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_in', ['carcheck' => 1], 'sysno='.intval($data['business_sysno']));
                }elseif($data['businesstype'] == 10) {
                    $poundCarcheckRes = $this->dbh->update(DB_PREFIX . 'doc_pounds_out', ['carcheck' => 1], 'sysno=' . intval($data['business_sysno']));
                }elseif ($data['businesstype'] == 16){
                    #获取退货磅码单信息
                    $sql = "SELECT p.sysno, p.poundsinno, p.carid, rd.goodsname, rd.goods_sysno, rd.customer_sysno, rd.customername FROM `".DB_PREFIX."doc_pounds_reback` p LEFT JOIN ".DB_PREFIX."doc_pounds_reback_detail rd ON rd.pounds_reback_sysno = p.sysno WHERE p.sysno = ".intval($data['business_sysno'])." GROUP BY p.sysno";
                    $poundRebackData = $this -> dbh -> select_row($sql);
//                    echo "<pre>";print_r($sql);die;
                    //审核通过是生成品质检查单 只有车入才需要
                    $qualitycheckData = array(
                        'qualitycheckno' => COMMON::getCodeId('J'),
                        'businesstype' => 10,
                        'booking_sysno' => $poundRebackData['sysno'],
                        'bookingno' => $poundRebackData['poundsinno'],
                        'bookingdate' => date('Y-m-d'),
                        'stock_sysno' => $poundRebackData['sysno'],
                        'stockno' => $poundRebackData['poundsinno'],
                        'applydate' => '=NOW()',
                        'apply_user_sysno' => $user['employee_sysno'],
                        'apply_employeename' => $user['employeename'],
                        'orderstatus' => 2,
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'carshipname' => $poundRebackData['carid'],
                        'customer_sysno' => $poundRebackData['customer_sysno'],
                        'customername' => $poundRebackData['customername'],
                        'goods_sysno' => $poundRebackData['goods_sysno'],
                        'goodsname' => $poundRebackData['goodsname'],
                    );
                    $qualitycheckRes = $this->dbh->insert(DB_PREFIX.'doc_qualitycheck', $qualitycheckData);
                    if(!$qualitycheckRes){
                        throw new Exception('创建品质检查单失败', 300);
                        return false;
                    }
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_reback', ['carcheck' => 1], 'sysno='.intval($data['business_sysno']));
                }else{
                    throw new Exception('未知数据来源类型', 300);
                    return false;
                }
            }elseif ($data['carcheckstatus'] == 5){
                //回写磅码单的车辆核对状态
                if($data['businesstype'] == 4){
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_in', ['carcheck' => 2], 'sysno='.intval($data['business_sysno']));
                }elseif($data['businesstype'] == 10){
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_out', ['carcheck' => 2], 'sysno='.intval($data['business_sysno']));
                }elseif ($data['businesstype'] == 16){
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_reback', ['carcheck' => 2], 'sysno='.intval($data['business_sysno']));
                }else{
                    throw new Exception('未知数据来源类型', 300);
                    return false;
                }
                $marks = '车辆核对审核不通过';
            }elseif($data['carcheckstatus'] == 3){
                $marks = '返回上一步操作';
                if($data['businesstype'] == 4){
                    #删除品质检查单
                    $delQualityRes = $this -> dbh -> update(DB_PREFIX.'doc_qualitycheck', ['isdel' => 1,'status' => 0], 'businesstype = 3  AND stock_sysno ='.intval($data['business_sysno']));
                    if(!$delQualityRes){
                        throw new Exception('删除品质检查单失败', 300);
                        return false;
                    }
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_in', ['carcheck' => 0], 'sysno='.intval($data['business_sysno']));
                }elseif($data['businesstype'] == 10){
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_out', ['carcheck' => 0], 'sysno='.intval($data['business_sysno']));
                }elseif ($data['businesstype'] == 16){
                    #删除品质检查单
                    $delQualityRes = $this -> dbh -> update(DB_PREFIX.'doc_qualitycheck', ['isdel' => 1,'status' => 0], 'businesstype = 10 AND stock_sysno ='.intval($data['business_sysno']));
                    if(!$delQualityRes){
                        throw new Exception('删除品质检查单失败', 300);
                        return false;
                    }
                    $poundCarcheckRes = $this -> dbh -> update(DB_PREFIX.'doc_pounds_reback', ['carcheck' => 0], 'sysno='.intval($data['business_sysno']));
                }else{
                    throw new Exception('未知数据来源类型', 300);
                    return false;
                }
            }else{
                throw new Exception('车辆核对审核未知类型', 300);
                return false;
            }

            if(!$poundCarcheckRes){
                throw new Exception('回写磅码单车辆检查信息失败', 300);
                return false;
            }

            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $logData= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  34,
                'opertype'  => $data['carcheckstatus'] - 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $marks,
            );
            if($fanhui == true){
                $logData['opertype'] = 0;
            }
            $logRes = $S->addDocLog($logData);

            #库存管理业务操作日志end
            if(!$logRes){
                throw new Exception('添加日志失败', 300);
                return false;
            }
            $this -> dbh -> commit();
            if($fanhui == true){
                return ['code' => 200, 'message' => '操作成功'];
            }else{
                return ['code' => 200, 'message' => '审核成功'];
            }
        }catch (Exception $e){
            $this -> dbh -> rollback();
            return ['code' => $e -> getCode(), 'message' => $e-> getMessage()];
        }
    }

    public function delCarcheck($data, $pound_sysno, $businesstype)
    {
        return $this->dbh->update(DB_PREFIX.'doc_carcheck', $data,  'businesstype = '.$businesstype.' AND business_sysno ='.intval($pound_sysno));
    }

    public function getBaseinfo($params)
    {
        $filter = array();
        if (isset($params['queuetype']) && $params['queuetype'] != '') {
            $filter[] = " `queuetype`='{$params['queuetype']}'";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " AND " . implode(' AND ', $filter);
        }

        $order = "order by `updated_at` desc";

        $sql = "SELECT * FROM `".DB_PREFIX."doc_carcheck` {$where} {$order} ";

        $Carcheckdata = $this->dbh->select($sql);

        $data = [];
        foreach ($Carcheckdata as $k => $value) {
            $sql = "SELECT COUNT(*) FROM `".DB_PREFIX."doc_car_queue` WHERE tp_sysno={$value['sysno']} AND isdel = 0 AND status=1 AND carstatus = 1 AND queuestatus = 1 ";
            $countcars = $this->dbh->select_one($sql);
            $value['countcars'] = $countcars;
            if($countcars!=0){
                $data[] = $value;
            }
        }

        return $data;
    }
}
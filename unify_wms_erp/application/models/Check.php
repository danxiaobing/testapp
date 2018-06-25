<?php
/**
 * Customer Model
 *
 */

class CheckModel
{
    public $dbh = null;

    public $mch = null;


    /**
     * Constructor
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }


    public function searchCheck($params)
    {
        $filter = array();
        if (isset($params['checkrecorddate']) && $params['checkrecorddate'] != '') {
            $filter[] = " scr.`checkrecorddate` = '{$params['checkrecorddate']}' ";
        }

        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " scr.`storagetank_sysno` = '{$params['storagetank_sysno']}' ";
        }

        if (isset($params['storagetankname']) && $params['storagetankname'] != '') {
            $filter[] = " scr.`storagetankname` = '{$params['storagetankname']}' ";
        }

        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " scr.`goodsname` = '{$params['goodsname']}' ";
        }

        $where = 'scr.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = "order by scr.`updated_at` desc";

        $sql = "SELECT count(scr.sysno),scr.checkrecorddate FROM `" . DB_PREFIX . "doc_stock_check_record` scr
                        LEFT JOIN `" . DB_PREFIX . "base_storagetank` bs ON (scr.`storagetank_sysno` = bs.`sysno`)
                        LEFT JOIN `" . DB_PREFIX . "base_goods` gs on (gs.`sysno` = scr.`goods_sysno`)
                where {$where} {$order}";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT scr.*,bs.storagetankname,gs.goodsname FROM `" . DB_PREFIX . "doc_stock_check_record` scr
                        LEFT JOIN `" . DB_PREFIX . "base_storagetank` bs ON (scr.`storagetank_sysno` = bs.`sysno`)
                        LEFT JOIN `" . DB_PREFIX . "base_goods` gs on (gs.`sysno` = scr.`goods_sysno`)
                        where {$where} {$order} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT scr.*,bs.storagetankname,se.employeename FROM `" . DB_PREFIX . "doc_stock_check_record` scr
                        LEFT JOIN `" . DB_PREFIX . "base_storagetank` bs ON (scr.`storagetank_sysno` = bs.`sysno`)
                        LEFT JOIN `" . DB_PREFIX . "base_goods` gs on (gs.`sysno` = scr.`goods_sysno`)
                        LEFT JOIN `" . DB_PREFIX . "system_employee` se on (se.`sysno` = scr.`created_employee_sysno`)
                        where {$where} {$order} ";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getCheckById($id)
    {
        $sql = "SELECT scr.*,bs.storagetankname,gs.sysno FROM `" . DB_PREFIX . "doc_stock_check_record` scr
                        LEFT JOIN `" . DB_PREFIX . "base_storagetank` bs ON (scr.`storagetank_sysno` = bs.`sysno`)
                        LEFT JOIN `" . DB_PREFIX . "base_goods` gs on (gs.`sysno` = scr.`goods_sysno`)
                        LEFT JOIN `" . DB_PREFIX . "system_employee` se on (se.`sysno` = scr.`created_employee_sysno`)
                        where scr.status = 1 and scr.isdel = 0 and scr.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    /*
     * 新增
     */
    public function addCheck($data,$attachment=null)
    {
        $this->dbh->begin();
        try {

            $res = $this->dbh->insert(DB_PREFIX . 'doc_stock_check_record', $data);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $id = $res;
            //回写附件对应转移单的id
                        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                        if(count($attachment) > 0){
                            $res = 	$A->addAttachModelSysno($id,$attachment);
                            if(!$res){
                                return COMMON::result(300,'添加附件失败');
                            }
                        }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $res,
                'doctype' => 10,
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '新建',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['stockcheckstatus'] == 2) {
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存盘点单';
            } elseif ($data['stockcheckstatus'] == 3) {
                $input['opertype'] = 2;
                $input['operdesc'] = '提交盘点单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $res;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 更新
     */
    public function updateCheck($id, $data, $stockcheckstatus, $attachment)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX . 'doc_stock_check_record', $data, '`sysno`=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新失败'];
            }

            //添加附件
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            if (count($attachment) > 0) {
                $res = $A->addAttachModelSysno($id, $attachment);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['statusCode' => 300, 'msg' => '更新附件失败'];
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 10,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );
            if ($stockcheckstatus == 2) {
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存盘点单';
            } elseif ($stockcheckstatus == 3) {
                $input['opertype'] = 2;
                $input['operdesc'] = '提交盘点单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '日志添加失败'];
            }


            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => '更新成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '更新失败'];
        }
    }

    /*
     * 审核
     */
    public function auditcheck($id, $data, $stockcheckstatus)
    {

        $this->dbh->begin();
        try {

            $res = $this->dbh->update(DB_PREFIX . 'doc_stock_check_record', $data, '`sysno`=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新失败'];
            }

            //更新储罐基本表最后一次盘点时间
            $storagetankdata = array(
                'checkdate' =>'=NOW()'
            );
            $res = $this->dbh->update(DB_PREFIX . 'base_storagetank', $storagetankdata, '`sysno`=' . intval($data['storagetank_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新失败'];
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 10,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['auditreason'],
            );


            if ($stockcheckstatus == 4) {
                $input['opertype'] = 3;
            } elseif ($stockcheckstatus == 6) {
                $input['opertype'] = 4;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '日志添加失败'];
            }

            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => '更新成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '更新失败'];
        }
    }

    /*
     * 作废
     */
    public function abolishcheck($id, $data)
    {
        $this->dbh->begin();
        try {

            $res = $this->dbh->update(DB_PREFIX . 'doc_stock_check_record', $data, '`sysno`=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新盘点表失败'];
            }

            $latesttime = $this ->abolishCheckdate($data['storagetank_sysno']);
            $stordata = array(
                'checkdate' => $latesttime ? $latesttime : "0000-00-00"
            );
            $res = $this->dbh->update(DB_PREFIX . 'base_storagetank', $stordata, '`sysno`=' . intval($data['storagetank_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新储罐表失败'];
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 10,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['abandonreason'],
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '日志添加失败'];
            }

            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => '更新成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '更新失败'];
        }
    }


    /*
     * 搜索作废时盘点时间
     */
    public function abolishCheckdate($id){
        $sql = "SELECT updated_at FROM `" . DB_PREFIX . "doc_stock_check_record` 
                WHERE stockcheckstatus = 4 AND isdel = 0  order by updated_at desc";
                return $this->dbh->select_one($sql);
    }


    /*
     * 删除
     */
    public function delCheck($id, $data)
    {
        $this->dbh->begin();
        try {
            $ret = $this->dbh->update(DB_PREFIX . 'doc_stock_check_record', $data, 'sysno=' . intval($id));

            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 10,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '删除',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }




    public function searchCheckreport($params)
    {
        $filter = array();
        if (isset($params['created_at']) && $params['created_at'] != '') {
            $filter[] = " scr.`created_at` <= '{$params['created_at']} 23:59:59' ";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `" . DB_PREFIX . "doc_stock_check_report` scr {$where} {$order} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT * FROM `" . DB_PREFIX . "doc_stock_check_report` scr {$where} {$order} ";
                $arr = $this->dbh->select($sql);
                $result['list'] = $arr;
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT * FROM `" . DB_PREFIX . "doc_stock_check_report` scr {$where} {$order} ";
                $arr = $this->dbh->select_page($sql);
                $result['list'] = $arr;
            }
        }
        return $result;

    }

    public function reportDetail($id)
    {

        $sql = "SELECT scrd.* FROM `" . DB_PREFIX . "doc_stock_check_report_detail` scrd
                        where scrd.status = 1 and scrd.isdel = 0 and scrd.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    /**
     * 获取储罐信息信息
     * @param int $contract_sysno
     */
    public function getStoragetankInfo($id)
    {
        $sql = "SELECT DISTINCT bs.storagetanknature,bs.storagetankname,bg.goodsname,bg.sysno FROM ".DB_PREFIX."base_storagetank  bs
                LEFT JOIN ".DB_PREFIX."base_goods bg on bg.sysno = bs.goods_sysno
                WHERE bs.sysno = $id";
        return $this->dbh->select_row($sql);
    }

    /*
     * 生成盘点单获取数据
     */
    public function getcheckdata(){
        $sql = "SELECT bs.sysno,bs.storagetankname,bs.storagetanknature,bs.goods_sysno,bs.checkdate,bg.goodsname
                FROM ".DB_PREFIX."base_storagetank bs 
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno ";
        return $this->dbh->select($sql);
    }

    /*
     * 生成盘点单获取数据
     */
    public function getcheckdata2($ids){
        $sql = "SELECT bs.sysno,bs.storagetankname,bs.storagetanknature,bs.goods_sysno,bs.tank_stockqty,bs.checkdate,bg.goodsname,
                (SELECT SUM(beqty) - SUM(ullage) FROM hengyang_doc_goods_record_log WHERE isdel = 0 AND storagetank_sysno = bs.sysno) AS beqty
                FROM ".DB_PREFIX."base_storagetank bs 
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno 
                WHERE bs.sysno IN ($ids )";
        return $this->dbh->select($sql);
    }

    /*
     * 获取盘点表明细
     */
    public function getReportdetail($id)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."doc_stock_check_report_detail  WHERE checkreport_sysno =" . intval($id);
        return $this->dbh->select($sql);
    }

    /*
     * 生成盘点表
     */
    public function generate_check($data,$detaildata){
        $this->dbh->begin();
        try {

            $res = $this->dbh->insert(DB_PREFIX . 'doc_stock_check_report', $data);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $id = $res;

            foreach ($detaildata as $detaildatum){
                $input = array(
                    'checkreport_sysno' => $id,
                    'checkdate' => $detaildatum['checkdate'],
                    'storagetank_sysno' => $detaildatum['storagetank_sysno'],
                    'storagetankname' => $detaildatum['storagetankname'],
                    'storagetanknature' => $detaildatum['storagetanknature'],
                    'goods_sysno' => $detaildatum['goods_sysno'],
                    'goodsname' => $detaildatum['goodsname'],
                    'storagetankaccountqty' => $detaildatum['tank_stockqty'],
                    'storagetankqty' => $detaildatum['beqty'],
                    'storagedcsqty' => 0,
                    'profitqty' => $detaildatum['beqty'] - $detaildatum['tank_stockqty'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $res = $this->dbh->insert(DB_PREFIX . 'doc_stock_check_report_detail', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $res,
                'doctype' => 99,
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '生成盘点表',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $res;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 根据id查询盘点表
     */
    public function getCheckreportById($id){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_stock_check_report where sysno = $id ";
        return $this->dbh->select_row($sql);
    }


}

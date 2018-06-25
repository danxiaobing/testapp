<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24 0024
 * Time: 17:08
 */
Class ContractModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 查询合同列表
     */
    public function searchContract($params)
    {
        $filter = array();
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = "c.`contractdate` >= '{$params['startdate']}' ";
        }
        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = "c.`contractdate` <= '{$params['enddate']}' ";
        }
        if (isset($params['contractnodisplay']) && $params['contractnodisplay'] != '') {
            $filter[] = "c.`contractnodisplay` LIKE '%{$params['contractnodisplay']}%' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '' ) {
            $filter[] = "c.`customer_id` = {$params['customer_sysno']} ";
        }
        if (isset($params['contractstatus']) && $params['contractstatus'] != '') {
            $filter[] = "c.`contractstatus` = '{$params['contractstatus']}' ";
        }
        if (isset($params['contracttype']) && $params['contracttype'] != '' ) {
            $filter[] = "c.`contracttype` = '{$params['contracttype']}' ";
        }

        if (isset($params['contracttypeArr']) && $params['contracttypeArr'] != '' ) {
            $contracttype = implode(',', $params['contracttypeArr']);
            $filter[] = "c.`contracttype` in({$contracttype})";
        }
        if (isset($params['saleemployee_sysno']) && $params['saleemployee_sysno'] != '') {
            $filter[] = "c.`saleemployee_sysno` = '{$params['saleemployee_sysno']}' ";
        }
        if (isset($params['csemployee_sysno']) && $params['csemployee_sysno'] != '') {
            $filter[] = "c.`csemployee_sysno` = '{$params['csemployee_sysno']}' ";
        }

        $where = 'where c.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " ORDER BY c.`updated_at` DESC ";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_contract` c {$where} {$order} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT c.*,cus.customerclass,cus.customerrelation,cus.customercredit,cus.customerterm,
                em1.employeename as saleemployeename,em2.employeename as csemployeename,
                cg.goodsname,cg.goodsqualityname,cg.goodsnature,bu.unitname
				FROM ".DB_PREFIX."doc_contract c
				LEFT JOIN ".DB_PREFIX."customer cus ON c.customer_id = cus.sysno
				left join ".DB_PREFIX."system_employee em1 on c.saleemployee_sysno = em1.sysno
				left join ".DB_PREFIX."system_employee em2 on c.csemployee_sysno = em2.sysno
				left join ".DB_PREFIX."doc_contract_goods cg on cg.contract_sysno = c.sysno
				LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = cg.goods_sysno
				LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = bga.unit_sysno
				{$where} group by c.sysno {$order}";
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT c.*,cus.customerclass,cus.customerrelation,cus.customercredit,cus.customerterm,
                em1.employeename as saleemployeename,em2.employeename as csemployeename,
                cg.goodsname,cg.goodsqualityname,cg.goodsnature,bu.unitname
				FROM ".DB_PREFIX."doc_contract c
				LEFT JOIN ".DB_PREFIX."customer cus ON c.customer_id = cus.sysno
				left join ".DB_PREFIX."system_employee em1 on c.saleemployee_sysno = em1.sysno
				left join ".DB_PREFIX."system_employee em2 on c.csemployee_sysno = em2.sysno
				left join ".DB_PREFIX."doc_contract_goods cg on cg.contract_sysno = c.sysno
				LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = cg.goods_sysno
				LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = bga.unit_sysno
				{$where} group by c.sysno {$order} ";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /**
     * 新增合同和附加信息
     */
    public function addContract($data, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata)
    {
        $this->dbh->begin();
        try {
            if ($contractstatus == 1) {
                $data['contractstatus'] = 2;
            }else if($contractstatus == 2&&count($configreviewdata)!=0){
                $opertype = 2;
                $data['contractstatus'] = 3;
            }else if($contractstatus == 2&&count($configreviewdata)==0){
                $opertype = 3;
                $data['contractstatus'] = 4;
            }
            $res = $this->dbh->insert(DB_PREFIX.'doc_contract', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $id = $res;
            //查询关联表数据，存在的话删除，重新录入
            $goodsresult = $this->dbh->delete(DB_PREFIX.'doc_contract_goods', 'contract_sysno = ' . intval($id));
            $othercostresult = $this->dbh->delete(DB_PREFIX.'doc_contract_othercost', 'contract_sysno = ' . intval($id));

            if (!$goodsresult || !$othercostresult ) {
                $this->dbh->rollback();
                return false;
            }

            $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            foreach ($goodsdetaildata as $item) {
                $res = array(
                    'bar_id' => $item['goods_quality_sysno']
                );
                $result = $quality->getList($res);
                $input = array(
                    'contract_sysno' => $id,
                    'goods_sysno' => $item['goods_sysno'],
                    'goodsname' => $item['goodsname'],
                    'goods_quality_sysno' => $item['goods_quality_sysno'],
                    'goodsqualityname' => $result['list'][0]['qualityname'],
                    'goodsnature' => $item['goodsnature'],
                    'yearqty' => $item['yearqty'],
                    'yearamount' => $item['yearamount'],
                    'exyearrate' => $item['exyearrate'],
                    'goodsqty' => $item['goodsqty'],
                    'isladder' => $item['isladder'],
                    'ladderstart' => $item['ladderstart'],
                    'ladderend' => $item['ladderend'],
                    'goodsdate' => $item['goodsdate'],
                    'firststorageamount' => $item['firststorageamount'],
                    'firsttransportamount' => $item['firsttransportamount'],
                    'lastamount' => $item['lastamount'],
                    'firstlossrate' => $item['firstlossrate'],
                    'lastlossrate' => $item['lastlossrate']/30,
                    'memo' => $item['memo'],
                    'status' => $item['status'],
                    'isdel' => $item['isdel'],
                    'version' => $item['version'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'contractrate' => $item['contractrate'],
                    'invoice_company_sysno' => $item['invoice_company_sysno'],
                    'capacity' => $item['capacity'],
                    'overcapacity' => $item['overcapacity'],
                    'overfirstpayment' => $item['overfirstpayment'],
                    'overlastpayment' => $item['overlastpayment'],
                    'storagetank_sysno' => $item['storagetank_sysno'],
                    'storagetankname' => $item['storagetankname'],
                    'berthcosttype'=>$item['berthcosttype'],
                    'berthcost'=>$item['berthcost'],
                    'berthcostforeign'=>$item['berthcostforeign'],
                    'berthcostdomestic'=>$item['berthcostdomestic'],
                    'isminstockin'=>$item['isminstockin'],
                    'minnumber'=>$item['minnumber'],
                    'isminstockincost'=>$item['isminstockincost'],
                    'isminstockinullage'=>$item['isminstockinullage'],
                    'isminbalance'=>$item['isminbalance'],
                    'minbalancenumber'=>$item['minbalancenumber'],
                    'isminbalancecost'=>$item['isminbalancecost'],
                    'isminbalanceullage'=>$item['isminbalanceullage'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' =>1
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_contract_goods', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if(!empty($othercostdetaildata))
            foreach ($othercostdetaildata as $item) {
                $company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $arr = array(
                    'page' => false,
                    'companyname' => $item['companyname']
                );
                $item['invoice_company_sysno'] = $company->searchCompany($arr);
                $input = array(
                    'contract_sysno' => $id,
                    'othercost_sysno' => $item['sysno'],
                    'costamount' => $item['othercostprice'],
                    'status' => $item['status'],
                    'isdel' => $item['isdel'],
                    'version' => $item['version'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'memo' => $item['othercostmarks'],
                    'invoice_company_sysno' => $item['invoice_company_sysno']['list'][0]['sysno']
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_contract_othercost', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if(count($configreviewdata)!=0){
                foreach ($configreviewdata as $item) {
                    $input = array(
                        'contract_sysno' => $id,
                        'department_sysno' => $item['department_sysno'],
                        'departmnetname' => $item['departmentname'],
                        'position_sysno' => $item['position_sysno'],
                        'positionname' => $item['positionname'],
                        'sortnum' => $item['sortnum'],
                        'reviewstatus' => 1,
                        'reviewmemo' => $item['reviewmemo'],
                        'reviewemployee_id' => $item['reviewemployee_id'],
                        'reviewemployeename' => $item['reviewemployeename'],
                        'reviewdate' => $item['reviewdate'],
                        'status' => $item['status'],
                        'isdel' => $item['isdel'],
                        'version' => $item['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_contract_review', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            #合同业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 16,
                'opertype'=> 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc'=> '新建合同',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($contractstatus ==1){
                $input['opertype'] = 1;
                $input['operdesc'] = "暂存合同";
            }elseif($contractstatus ==2){
                $input['opertype'] = $opertype;
                $input['operdesc'] = "提交合同";
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }

    }

    /**
     *更新合同
     */
    public function updatecontract($id, $list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata)
    {     //$contractstatus  代表操作
        $this->dbh->begin();
        try {
            //暂存状态 and 审核驳回 可以更改信息
            if ($contractstatus == 1) {     //点击暂存后状态
                $list['contractstatus'] = 2;
            }else if(($contractstatus == 2||$list['contractstatus']== 5)&&count($configreviewdata)!=0){  //点击提交或退回提交后会签不为空状态
                $opertype = 2;
                $list['contractstatus'] = 3;
            }else if (($contractstatus == 2||$list['contractstatus']== 5)&&count($configreviewdata)==0) {  ////点击提交或退回提交后会签为空状态
                $opertype = 3;
                $list['contractstatus'] = 4;
            }else if($contractstatus==5){
                $list['contractstatus'] = 7;
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_contract', $list, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            //查询关联表数据，存在的话删除，重新录入
            $goodsresult = $this->dbh->delete(DB_PREFIX.'doc_contract_goods', 'contract_sysno = ' . intval($id));
            $othercostresult = $this->dbh->delete(DB_PREFIX.'doc_contract_othercost', 'contract_sysno = ' . intval($id));
            $reviewresult = $this->dbh->delete(DB_PREFIX.'doc_contract_review', 'contract_sysno = ' . intval($id));

            if (!$goodsresult || !$othercostresult||!$reviewresult) {
                $this->dbh->rollback();
                return false;
            }
            //将新的商品及费用表重新迭代添加
            $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            foreach ($goodsdetaildata as $item) {
                $res = array(
                    'bar_id' => $item['goods_quality_sysno']
                );
                $result = $quality->getList($res);
                $input = array(
                    'contract_sysno' => $id,
                    'goods_sysno' => $item['goods_sysno'],
                    'goodsname' => $item['goodsname'],
                    'goods_quality_sysno' => $item['goods_quality_sysno'],
                    'goodsqualityname' => $result['list'][0]['qualityname'],
                    'goodsnature' => $item['goodsnature'],
                    'yearqty' => $item['yearqty'],
                    'yearamount' => $item['yearamount'],
                    'exyearrate' => $item['exyearrate'],
                    'goodsqty' => $item['goodsqty'],
                    'isladder' => $item['isladder'],
                    'ladderstart' => $item['ladderstart'],
                    'ladderend' => $item['ladderend'],
                    'goodsdate' => $item['goodsdate'],
                    'firststorageamount' => $item['firststorageamount'],
                    'firsttransportamount' => $item['firsttransportamount'],
                    'lastamount' => $item['lastamount'],
                    'firstlossrate' => $item['firstlossrate'],
                    'lastlossrate' => $item['lastlossrate']/30,
                    'storagestartdate' => $item['storagestartdate'],
                    'storageenddate' => $item['storageenddate'],
                    'memo' => $item['memo'],
                    'status' => $item['status'],
                    'isdel' => $item['isdel'],
                    'version' => $item['version'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'contractrate' => $item['contractrate'],
                    'invoice_company_sysno' => $item['invoice_company_sysno'],
                    'capacity' => $item['capacity'],
                    'overcapacity' => $item['overcapacity'],
                    'overfirstpayment' => $item['overfirstpayment'],
                    'overlastpayment' => $item['overlastpayment'],
                    'storagetank_sysno' => $item['storagetank_sysno'],
                    'storagetankname' => $item['storagetankname'],
                    'berthcosttype'=>$item['berthcosttype'],
                    'berthcost'=>$item['berthcost'],
                    'berthcostforeign'=>$item['berthcostforeign'],
                    'berthcostdomestic'=>$item['berthcostdomestic'],
                    'isminstockin'=>$item['isminstockin'],
                    'minnumber'=>$item['minnumber'],
                    'isminstockincost'=>$item['isminstockincost'],
                    'isminstockinullage'=>$item['isminstockinullage'],
                    'isminbalance'=>$item['isminbalance'],
                    'minbalancenumber'=>$item['minbalancenumber'],
                    'isminbalancecost'=>$item['isminbalancecost'],
                    'isminbalanceullage'=>$item['isminbalanceullage'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' =>1
                );

                if($input['ladderend']==''){
                    unset($input['ladderend']);
                }

                $res = $this->dbh->insert(DB_PREFIX.'doc_contract_goods', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            //循环导入新的其他费用表
            if($othercostdetaildata)
                foreach ($othercostdetaildata as $item) {
                    $company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $arr = array(
                        'page' => false,
                        'companyname' => $item['companyname']
                    );
                    $item['invoice_company_sysno'] = $company->searchCompany($arr);
                    $input = array(
                        'contract_sysno' => $id,
                        'othercost_sysno' => $item['sysno'],
                        'costamount' => $item['othercostprice'],
                        'status' => $item['status'],
                        'isdel' => $item['isdel'],
                        'version' => $item['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'memo' => $item['othercostmarks'],
                        'invoice_company_sysno' => $item['invoice_company_sysno']['list'][0]['sysno']
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_contract_othercost', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            //根据新的配置表 来插入到合同评审表
            if(count($configreviewdata)!=0)
                foreach ($configreviewdata as $item) {
                    $input = array(
                        'contract_sysno' => $id,
                        'department_sysno' => $item['department_sysno'],
                        'departmnetname' => $item['departmentname'],
                        'position_sysno' => $item['position_sysno'],
                        'positionname' => $item['positionname'],
                        'sortnum' => $item['sortnum'],
                        'reviewstatus' => 1,
                        'reviewmemo' => $item['reviewmemo'],
                        'reviewemployee_id' => $item['reviewemployee_id'],
                        'reviewemployeename' => $item['reviewemployeename'],
                        'reviewdate' => $item['reviewdate'],
                        'status' => $item['status'],
                        'isdel' => $item['isdel'],
                        'version' => $item['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_contract_review', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            //合同日志捕捉
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 16,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($contractstatus == 1){
                $input['opertype'] = 1;
                $input['operdesc'] = "暂存合同";
            }elseif($contractstatus == 2){
                $input['opertype'] = $opertype;
                $input['operdesc'] = "提交合同";
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }

    }

    /**
     * 审核合同
     */
    public function examecontract($id,$list,$goodsdetaildata, $contractstatus, $auditopinion)
    {
        $this->dbh->begin();
        try {
            if ($contractstatus == 3) {
                $input['contractstatus'] = 5;
                $input['auditopinion'] = $auditopinion;
                $input['updated_at'] = $list['updated_at'];
                $result = $this->dbh->update(DB_PREFIX.'doc_contract', $input, 'sysno=' . intval($id));

                if (!$result) {
                    $this->dbh->rollback();
                    return false;
                }

                //整租合同时回写包罐费用
                if(($list['contracttype']==3||$list['contracttype']==4)&&$list['costtype']==1){
                    $financecost = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    foreach($goodsdetaildata as $item){
                        $data = array(
                            'contractcostdate'=>$list['contractcostdate'],
                            'storagetank_sysno' => isset($item['storagetank_sysno'])?$item['storagetank_sysno']:0,
                            'goods_sysno' => $item['goods_sysno'],
                            'goods_quality_sysno' => isset($item['goods_quality_sysno'])?$item['goods_quality_sysno']:0,
                            'customer_sysno' => $list['customer_id'],
                            'customer_name' => $list['customername'],
                            'totalprice' => $item['yearamount'],
                            'contract_sysno' => $id,
                            'contract_no' => $list['contractnodisplay'],
                        );

                        $costdate = $list['contractcostdate'];
                        $enddate = $list['contractenddate'];

                        $costmonth = $this->getMonthNum($costdate,$enddate,'-');
                        if($costmonth==0){
                            $costmonth=1;
                        }

                        $res = $financecost->addFinancecostByTank($data,$costmonth,1);
                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }
                }

                //回写客户表的成交字段
                $update = array(
                    'customerdeal' => 1,
                );
                $res = $this->dbh->update(DB_PREFIX.'customer', $update, 'sysno=' . intval($list['customer_id']));

                if(!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                //回写储罐表的包罐字段
                if(!empty($goodsdetaildata)){
                    foreach($goodsdetaildata as $item){
                        if($item['storagetank_sysno']!=''){
                            $update = array(
                                'storagetankbg'=>1
                            );
                            $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($item['storagetank_sysno']));
                            if(!$res) {
                                $this->dbh->rollback();
                                return false;
                            }
                        }
                    }
                }

            } elseif ($contractstatus == 4) {
                $list['contractstatus'] = 6;
                $list['auditopinion'] = $auditopinion;
                $res = $this->dbh->update(DB_PREFIX.'doc_contract', $list, 'sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

            }

            //合同日志捕捉
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 16,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$list['auditopinion'],
                'opertime' => '=NOW()',
            );

            if($contractstatus ==3){
                $input['opertype'] = 4;
            }elseif($contractstatus ==4){
                $input['opertype'] = 5;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 计算两个时间段相差月份
     */
    public function getMonthNum( $date1, $date2, $tags='-' ){
//        $date1 = explode($tags,$date1);
//        $date2 = explode($tags,$date2);
//        return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);

        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        $daychaju = ($time2-$time1)/(24*3600);
        return round($daychaju/30);
    }

    /**
     * 作废合同
     */
    public function abolishcontract($id,$list, $contractstatus, $abandonreason)
    {
        $this->dbh->begin();
        try {
            if ($contractstatus == 5) {
                $list['contractstatus'] = 7;
                $list['abandonreason'] = $abandonreason;
                $result = $this->dbh->update(DB_PREFIX.'doc_contract', $list, 'sysno=' . intval($id));
                if(!$result){
                    $this->dbh->rollback();
                    return false;
                }
                //删除费用单
                $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $costdata = array(
                    'isdel'=>1,
                );
                $res = $F->delcost($id,$costdata);
                if(!$res){
                    $this->dbh->rollback();
                    return false;
                }

                //合同日志捕捉
                $user = Yaf_Registry::get(SSN_VAR);
                $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 16,
                    'opertype' =>6,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'operdesc'=>$list['abandonreason'],
                    'opertime' => '=NOW()',
                );

                $res = $S->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /**
     * 删除合同
     **/
    public function deletecontract($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_contract', $data, 'sysno=' . intval($id));
    }

    /*
     * 判断合同是否存在
     * 调用地方：新增合同不能重复
     */
    public function contractisexist($contractnodisplay){
        $sql = "SELECT *  FROM `".DB_PREFIX."doc_contract` WHERE contractnodisplay = '$contractnodisplay'";
        return $this->dbh->select($sql);

    }

    /**
     * 根据id查询合同内容
     */
    public function getContractById($id)
    {
        $sql = "SELECT con.*,cus.customerclass,cus.customercredit,cus.customerterm,em.employeename saleemployee_name,em2.employeename csloyee_name
			FROM ".DB_PREFIX."doc_contract con
			LEFT JOIN ".DB_PREFIX."customer cus ON cus.customername=con.customername
			LEFT JOIN ".DB_PREFIX."system_employee em ON em.sysno=con.saleemployee_sysno
			LEFT JOIN ".DB_PREFIX."system_employee em2 ON em2.sysno=con.csemployee_sysno
			WHERE con.isdel=0 AND con.sysno = {$id}";
        return $this->dbh->select_row($sql);
    }


    /*
     * 插入合同到期提醒消息记录
     */
    public function insertmes(){
        $today = date('Y-m-d');
        $endday = date('Y-m-d',strtotime('1 months',time()));

        $sql = "SELECT * FROM ".DB_PREFIX."doc_contract WHERE contracttype IN (3,4) AND contractenddate >= '$today' AND contractenddate <= '$endday' ";
        $contractdata = $this->dbh->select($sql);

        $sql = "SELECT DISTINCT(urr.user_sysno) FROM `".DB_PREFIX."system_user-r-role` urr
                LEFT JOIN `".DB_PREFIX."system_role-r-privilege` rrp ON rrp.role_sysno = urr.role_sysno
                LEFT JOIN ".DB_PREFIX."system_privilege sp ON sp.sysno = rrp.privilege_sysno
                LEFT JOIN ".DB_PREFIX."system_user su ON su.sysno = urr.user_sysno
                WHERE su.status = 1 and su.isdel = 0 AND sp.parent_sysno = 141
                ORDER BY urr.user_sysno ASC;";
        $users = $this->dbh->select($sql);

        foreach($contractdata as $item){
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            foreach($users as $value){
                $content = $item['customername']."的合同".$item['contractnodisplay']."即将到期";
                $input = array(
                    'send_from_id'=>0,
                    'send_from_name'=>'系统发送',
                    'send_to_id'=>$value['user_sysno'],
                    'viewstatus'=>1,
                    'subject'=>'合同到期提醒',
                    'content'=>$content,
                    'message_type'=>1,
                    'replyid'=>'',
                    'created_at'=>'=NOW()',
                    'updated_at'=>'=NOW()',
                    'action'=>'detail',
                    'control'=>'contract',
                    'doc_sysno'=>$item['sysno'],
                );
                $S ->addmessage($input);
            }

        }
    }

    /**
     * 获取待评审合同列表数据
     * @author Dujiangjiang
     */
    public function getReviewList($search, $rows, $page)
    {
        $filter = array();
        if (isset($search['startDate']) && $search['startDate'] != '') {
            $filter[] = " dc.`contractdate` >='{$search['startDate']}'";
        }
        if (isset($search['endDate']) && $search['endDate'] != '') {
            $filter[] = " dc.`contractdate` <='{$search['endDate']}'";
        }
        if (isset($search['customerId']) && $search['customerId'] != '') {
            $filter[] = " dc.`customer_id` ={$search['customerId']}";
        }
//        if (isset($search['contStatus']) && $search['contStatus'] != '') {
//            $filter[] = " dc.`contractstatus` ={$search['contStatus']}";
//        }

        if (isset($search['contractnodisplay']) && $search['contractnodisplay'] != '') {
            $filter[] = " dc.`contractnodisplay` = '{$search['contractnodisplay']}' ";
        }

        $where = 'WHERE (dc.`isdel` = 0 AND dc.`contractstatus` = 3 AND dcr.`reviewstatus`<2 ';

        if (count($filter) > 0) {
            $where .= " AND " . implode(' AND ', $filter);
            $where .= ')';

        }else{
            $where .= ')';
            if (isset($search['user_sysno']) && $search['user_sysno'] != 0) {
                $where .= " OR (dc.`isdel` = 0 AND dc.`contractstatus` = 3 AND dcr.`reviewstatus`<2 AND dc.`csemployee_sysno` = '{$search['user_sysno']}')";
            }
        }

        $order = "ORDER BY dc.`updated_at` DESC";

        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(DISTINCT(dc.`sysno`)) FROM `".DB_PREFIX."doc_contract` AS dc
                LEFT JOIN ".DB_PREFIX."doc_contract_goods as dcg ON dcg.contract_sysno = dc.sysno 
                LEFT JOIN `".DB_PREFIX."doc_contract_review` AS dcr ON dcr.`contract_sysno`=dc.`sysno` AND dcr.`department_sysno`={$search['department_sysno']} {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $rows);

        $this->dbh->set_page_num($page);
        $this->dbh->set_page_rows($rows);

        $sql = "SELECT dc.*,dcg.goodsname,dcg.goodsnature FROM `".DB_PREFIX."doc_contract` AS dc
                LEFT JOIN ".DB_PREFIX."doc_contract_goods as dcg ON dcg.contract_sysno = dc.sysno 
                LEFT JOIN `".DB_PREFIX."doc_contract_review` AS dcr ON dcr.`contract_sysno`=dc.`sysno` AND dcr.`department_sysno`={$search['department_sysno']} {$where} group by dc.sysno {$order}";
        // error_log($sql, 3, 'sql_print.txt');
        $result['list'] = $this->dbh->select_page($sql);
        return $result;
    }

    /**
     * 获取已评审合同列表数据
     * @author Dujiangjiang
     */
    public function getReviewedList($search, $rows, $page)
    {
        $filter = array();
        if (isset($search['startDate']) && $search['startDate'] != '') {
            $filter[] = " dc.`contractdate` >='{$search['startDate']}'";
        }
        if (isset($search['endDate']) && $search['endDate'] != '') {
            $filter[] = " dc.`contractdate` <='{$search['endDate']}'";
        }
        if (isset($search['customerId']) && $search['customerId'] != '') {
            $filter[] = " dc.`customer_id` ={$search['customerId']}";
        }
        if (isset($search['contStatus']) && $search['contStatus'] != '') {
            $filter[] = " dc.`contractstatus` ={$search['contStatus']}";
        }

        if (isset($search['contractnodisplay']) && $search['contractnodisplay'] != '') {
            $filter[] = " dc.`contractnodisplay` = '{$search['contractnodisplay']}' ";
        }   
        // $where = "WHERE dc.`sysno` IN (SELECT DISTINCT(`contract_sysno`) FROM `".DB_PREFIX."doc_contract_review_log` WHERE `reviewemployee_id`={$search['employee_sysno']}) AND dc.`isdel` = 0 AND dc.`contractstatus`=5 ";
        $where = "WHERE (dc.`sysno` IN (SELECT DISTINCT(`contract_sysno`) FROM `".DB_PREFIX."doc_contract_review_log` WHERE `reviewemployee_id`={$search['employee_sysno']}) AND dc.`isdel` = 0 ";
        if (count($filter) > 0) {
            $where .= " AND " . implode(' AND ', $filter);
            $where .= ')';
        }else{
            $where .= ')';
            if (isset($search['user_sysno']) && $search['user_sysno'] != 0) {
                $where .= " OR (dc.`sysno` IN (SELECT DISTINCT(`contract_sysno`) FROM `".DB_PREFIX."doc_contract_review_log` WHERE `reviewemployee_id`={$search['employee_sysno']}) AND dc.`isdel` = 0 AND dc.`csemployee_sysno` = '{$search['user_sysno']}')";
            }
        }
        $order = "ORDER BY dc.`updated_at` DESC";

        $result = array('total' => 0, 'list' => array());

        $sql = "SELECT count(dc.`sysno`) FROM `".DB_PREFIX."doc_contract` AS dc {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $rows);

        $this->dbh->set_page_num($page);
        $this->dbh->set_page_rows($rows);

        $sql = "SELECT dc.*,dcg.goodsname,dcg.goodsnature FROM `".DB_PREFIX."doc_contract` AS dc 
                LEFT JOIN ".DB_PREFIX."doc_contract_goods as dcg ON dcg.contract_sysno = dc.sysno
                left join `".DB_PREFIX."doc_contract_review_log` dcl on (dcl.`contract_sysno`=dc.`sysno`) {$where} 
                group by dc.`sysno` having count(dcl.sysno)>0 {$order}";
        $result['list'] = $this->dbh->select_page($sql);

        return $result;
    }

    /**
     * 根据员工ID获取该员工评审过的合同ID数据
     * @author Dujiangjiang
     */
    public function getContractIdByEmployeeId($employee_sysno)
    {
        $sql = "SELECT DISTINCT(`contract_sysno`) FROM `".DB_PREFIX."doc_contract_review_log` WHERE `reviewemployee_id`={$employee_sysno}";
        return $this->dbh->select_row($sql);
    }

    /**
     * 获取合同部门汇签数据
     * @author Dujiangjiang
     */
    public function getContractReview($contract_sysno)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_contract_review` WHERE `status`=1 AND `isdel`=0 AND `contract_sysno`={$contract_sysno}";
        return $this->dbh->select($sql);
    }

    /**
     * 更新合同部门汇签流程
     * @author Dujiangjiang
     */
    public function updateContractReview($params, $id, $cid)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_contract_review', $params, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            //取得当前评审批次
            $sql = "SELECT `reviewbatch` FROM `".DB_PREFIX."doc_contract` WHERE `sysno`= {$cid}";
            $rbathc = $this->dbh->select_one($sql);

            $contract = array('reviewbatch' => $rbathc);

            //获取当前评审部门评审数据
            $sql = "SELECT * FROM `".DB_PREFIX."doc_contract_review` WHERE `sysno`=" . intval($id);
            $review = $this->dbh->select_row($sql);

            //记录评审日志
            $reviewlog = array(
                'contract_sysno' => $cid,
                'department_sysno' => $review['department_sysno'],
                'departmnetname' => $review['departmnetname'],
                'position_sysno' => $review['position_sysno'],
                'positionname' => $review['positionname'],
                'reviewstatus' => $params['reviewstatus'],
                'reviewemployee_id' => $params['reviewemployee_id'],
                'reviewemployeename' => $params['reviewemployeename'],
                'reviewmemo' => $params['reviewmemo'],
                'reviewdate' => '=NOW()',
                'reviewbatch' => ($rbathc + 1),
            );
            $res = $this->dbh->insert(DB_PREFIX.'doc_contract_review_log', $reviewlog);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            //评审日志
            $arr = array(
                2=> 7,
                3=> 8,
                );

            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $data= array(
                'doc_sysno'  =>  $cid,
                'doctype'  =>  16,
                'opertype'  => $arr[$params['reviewstatus']],
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $params['reviewmemo'],
            );
            $res = $S->addDocLog($data);
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

//            //判断是否有未评审数据
//            $sql = "SELECT count(`sysno`) FROM `".DB_PREFIX."doc_contract_review` WHERE `contract_sysno`={$cid} AND `reviewstatus`<2 AND `status`=1 AND `isdel`=0";
//            $res = $this->dbh->select_one($sql);
//
//            //大于0有未评审数据
//            if ($res > 0) {
//                $contract['contractstatus'] = 3;
//            } else {
//                $contract['reviewbatch'] = ($rbathc + 1);
//
//                //判断是否有评审未通过数据
//                $sql = "SELECT count(`sysno`) FROM `".DB_PREFIX."doc_contract_review` WHERE `contract_sysno`={$cid} AND `reviewstatus`=3 AND `status`=1 AND `isdel`=0";
//                $res = $this->dbh->select_one($sql);
//                if ($res > 0) {
//                    $contract['contractstatus'] = 6;
//                } else {
//                    $contract['contractstatus'] = 4;
//                }
//            }
            
            //判断是否有评审未通过数据
            $sql = "SELECT count(`sysno`) FROM `".DB_PREFIX."doc_contract_review` WHERE `contract_sysno`= {$cid} AND `reviewstatus`= 3 AND `status`= 1 AND `isdel`= 0";
            $res = $this->dbh->select_one($sql);
            if ($res > 0) {
                $contract['contractstatus'] = 6;
            } else {
                //判断是否有未评审数据
                $sql = "SELECT count(`sysno`) FROM `".DB_PREFIX."doc_contract_review` WHERE `contract_sysno`={$cid} AND `reviewstatus`<2 AND `status`=1 AND `isdel`=0";
                $res = $this->dbh->select_one($sql);
                if ($res > 0) {
                    $contract['contractstatus'] = 3;
                }else{
                    $contract['contractstatus'] = 4;
                }
            }

            $contract['reviewbatch'] = ($rbathc + 1);

            $res = $this->dbh->update(DB_PREFIX.'doc_contract', $contract, 'sysno=' . intval($cid));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return true;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /**
     * @return mixed
     * @title 查询合同评审配置表信息
     */
    public function getconfigreview($versiontype)
    {
        $sql = "SELECT cf.*
                FROM `".DB_PREFIX."system_config_review` cf
                LEFT JOIN ".DB_PREFIX."doc_version v ON cf.version_sysno = v.sysno
                where cf.isdel = 0 and cf.reviewtype = 1 and v.versiontype = $versiontype and cf.department_sysno != 0 AND v.isdel = 0 AND v.`status`=1";
        return $this->dbh->select($sql);
    }

    /*
     *查询客户合同货品
     */
    public function searchCustomerContractgoods($params)
    {
        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '0') {
            $filter[] = "dc.`customer_id` = {$params['customer_sysno']} ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '0') {
            $filter[] = "cg.`contract_sysno` = {$params['contract_sysno']} ";
        }

        $where = 'WHERE dc.contractstatus=5 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql="SELECT DISTINCT cs.storagetank_sysno,cu.customername,dc.contractno,bg.sysno,bg.goodsno,bg.goodsname,bs.storagetankname,bu.unitname
			FROM ".DB_PREFIX."customer cu
			LEFT JOIN ".DB_PREFIX."doc_contract dc ON dc.customer_id=cu.sysno
			LEFT JOIN ".DB_PREFIX."doc_contract_goods cg ON cg.contract_sysno=dc.sysno
			LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=cg.goods_sysno
			LEFT JOIN ".DB_PREFIX."doc_contract_storagetank cs ON cs.contract_sysno=dc.sysno
            LEFT JOIN ".DB_PREFIX."base_goods_attribute AS ga ON ga.goods_sysno = cg.goods_sysno
            LEFT JOIN ".DB_PREFIX."base_unit AS bu ON bu.sysno = ga.unit_sysno
			LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno=cs.storagetank_sysno $where GROUP BY bg.sysno";

        return $this->dbh->select($sql);
    }

    /*
     * 查询客户合同储罐
     */
    public function searchCustomerContractStoragetank($params){
        $filter = array();
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '0') {
            $filter[] = "dc.`sysno` = {$params['contract_sysno']} ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '0') {
            $filter[] = "dc.`customer_id` = {$params['customer_sysno']} ";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' AND ', $filter);
        }

        $sql="SELECT bs.sysno,bs.storagetankname,bs.theoreticalcapacity,bs.actualcapacity,bs.tank_stockqty
                FROM ".DB_PREFIX."doc_contract_goods cg
                LEFT JOIN ".DB_PREFIX."doc_contract dc ON dc.sysno = cg.contract_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = cg.storagetank_sysno ".$where;

        return $this->dbh->select($sql);
    }

    /*
     * 查询储罐是否被合同引用
     */
    public function searchContractStorage($params){
        $filter = array();
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '0') {
            $filter[] = "`storagetank_sysno` = {$params['storagetank_sysno']} ";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' AND ', $filter);
        }
        $sql="SELECT * FROM ".DB_PREFIX."doc_contract_storagetank ".$where;

        return $this->dbh->select($sql);
    }

    /**
     * 取甲方客户信息
     * @param unknown $contract_sysno
     */
    public function getCustomerInfo($company_id)
    {

        $cm = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $info = $cm->getCompanyById($company_id);

        //客户
        //合同编号
        //合同时间
        //仓库地址

        //客户(甲方)
        //客户地址(甲方)
        //电话(甲方)
        //传真(甲方)


        //乙方(保管方)
        //地址(取仓库地址)
        //电话(取仓库电话)
        //传真(取仓库传真号)

        //取客户基础资料的传真机号
        //取开票公司的传真机号
        //取开票公司名,可以有多个,如果有多个分行显示
    }

    /**
     * 获取仓储的货物信息
     * @param int $contract_sysno
     */
    public function getGoodsInfo($contract_sysno)
    {
        $sql = "SELECT DISTINCT goodsname FROM ".DB_PREFIX."doc_contract_goods WHERE contract_sysno = $contract_sysno";
        return $this->dbh->select($sql);
    }

    /**
     * 获取银行账户信息
     * @param int $contract_sysno
     */
    public function getBankAccounts($contract_sysno)
    {

    }

    /**
     * 获取储运条件和仓储费用(包罐)
     * @param int $contract_sysno
     */
    public function getConditionCosts($contract_sysno)
    {
        //settlement_sysno
    }

    /**
     * 杂费费率标准(包罐)
     * @param int $contract_sysno
     */
    public function getRates($contract_sysno)
    {

    }

    /**
     * 仓储费用及结算方式(非包罐)
     * @param unknown $contract_sysno
     */
    public function getCostways($contract_sysno)
    {

    }

    /*
        评审中只能查看自己的评审信息
    */
    public function getContract($contract_sysno,$department_sysno)
    {
        if(empty($department_sysno)){
            $where = '';
        }else{
            $where = 'AND `department_sysno` = '.$department_sysno;
        }
        $sql = "SELECT * FROM `".DB_PREFIX."doc_contract_review` WHERE `status`=1 AND `isdel`=0 AND `contract_sysno`={$contract_sysno} {$where}";
        // error_log($sql,3,'sql_print.txt');

        return $this->dbh->select($sql);
    }

    /**
     * 查询合同列表
     */
    public function searchContractForApi($params)
    {
        $filter = array();
        if (isset($params['customerno_sysno']) && $params['customerno_sysno'] != '0') {
            $filter[] = "c.`customer_id` = '{$params['customerno_sysno']}' ";
        }
        if (isset($params['quality_sysno']) && $params['quality_sysno'] != '') {
            $filter[] = "cg.`goods_quality_sysno` = '{$params['quality_sysno']}' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '' && $params['goods_sysno'] != '0') {
            $filter[] = "cg.`goods_sysno` = '{$params['goods_sysno']}' ";
        }

        $where = 'WHERE c.isdel = 0 AND c.status = 1 AND c.contractstatus = 5 AND c.contractenddate > NOW()';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " ORDER BY c.`updated_at` DESC ";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_contract` c  left join ".DB_PREFIX."doc_contract_goods cg on cg.contract_sysno = c.sysno  {$where} {$order} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT c.*,cus.customerclass,cus.customerrelation,cus.customercredit,cus.customerterm,
                bs.settlementname,em1.employeename as saleemployeename,em2.employeename as csemployeename,
                cg.goodsname,cg.goodsqualityname,sct.storagetank_categoryname
				FROM ".DB_PREFIX."doc_contract c
				LEFT JOIN ".DB_PREFIX."customer cus ON c.customer_id = cus.sysno
				LEFT JOIN ".DB_PREFIX."base_settlement bs ON c.settlement_sysno = bs.sysno
				left join ".DB_PREFIX."system_employee em1 on c.saleemployee_sysno = em1.sysno
				left join ".DB_PREFIX."system_employee em2 on c.csemployee_sysno = em2.sysno
				left join ".DB_PREFIX."doc_contract_goods cg on cg.contract_sysno = c.sysno
				left join ".DB_PREFIX."doc_contract_storagetank tank on tank.contract_sysno = c.sysno
				left join ".DB_PREFIX."base_storagetank bst on bst.sysno = tank.storagetank_sysno
				left join ".DB_PREFIX."base_storagetank_category sct on sct.sysno = bst.storagetank_category_sysno
				{$where} group by c.sysno {$order}";
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT c.*,cus.customerclass,cus.customerrelation,cus.customercredit,cus.customerterm,
                bs.settlementname,em1.employeename as saleemployeename,em2.employeename as csemployeename,
                cg.goodsname,cg.goodsqualityname,sct.storagetank_categoryname
				FROM ".DB_PREFIX."doc_contract c
				LEFT JOIN ".DB_PREFIX."customer cus ON c.customer_id = cus.sysno
				LEFT JOIN ".DB_PREFIX."base_settlement bs ON c.settlement_sysno = bs.sysno
				left join ".DB_PREFIX."system_employee em1 on c.saleemployee_sysno = em1.sysno
				left join ".DB_PREFIX."system_employee em2 on c.csemployee_sysno = em2.sysno
				left join ".DB_PREFIX."doc_contract_goods cg on cg.contract_sysno = c.sysno
				left join ".DB_PREFIX."doc_contract_storagetank tank on tank.contract_sysno = c.sysno
				left join ".DB_PREFIX."base_storagetank bst on bst.sysno = tank.storagetank_sysno
				left join ".DB_PREFIX."base_storagetank_category sct on sct.sysno = bst.storagetank_category_sysno
				{$where} group by c.sysno {$order} ";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /**
     * 根据用户sysno和合同编号查询合同
     * @param $customer_sysno
     * @param $contractno
     * @return array
     */
    public function getContractByCusomerSysnoForApi($customer_sysno, $contractno){
        $sql = "SElECT * FROM `".DB_PREFIX."doc_contract` WHERE status = 1 AND isdel = 0 AND contractstatus = 5 AND contractno = '".$contractno."' AND customer_id = '".$customer_sysno."'";
        $result =  $this->dbh->select_row($sql);
        return $result ? $result : [];
    }

    /**
     * 根据id查询合同和明细内容
     */
    public function getContractInfoById($id)
    {
        $sql = "SELECT con.*,cus.customerclass,cus.customercredit,cus.customerterm,em.employeename saleemployee_name,em2.employeename csloyee_name,cg.goods_sysno,cg.goodsname,cg.goods_quality_sysno,cg.goodsnature,cg.yearamount,cg.storagetank_sysno,cg.storagetankname,cg.berthcostforeign,cg.berthcostdomestic,cg.berthcost
            FROM ".DB_PREFIX."doc_contract con
            LEFT JOIN ".DB_PREFIX."customer cus ON cus.customername=con.customername
            LEFT JOIN ".DB_PREFIX."system_employee em ON em.sysno=con.saleemployee_sysno
            LEFT JOIN ".DB_PREFIX."system_employee em2 ON em2.sysno=con.csemployee_sysno
            LEFT JOIN ".DB_PREFIX."doc_contract_goods cg ON con.sysno = cg.contract_sysno
            WHERE con.isdel=0 AND con.sysno = {$id}";
        return $this->dbh->select($sql);
    }

    /*
     * 根据id、goods_sysno、类型查询靠泊装卸费用
     */
    public function getberthcost($id,$goods_sysno){
        $sql = "SELECT berthcost,berthcostforeign,berthcostdomestic FROM ".DB_PREFIX."doc_contract_goods
                WHERE contract_sysno = $id AND goods_sysno = $goods_sysno";
        return $this->dbh->select_row($sql);
    }

}
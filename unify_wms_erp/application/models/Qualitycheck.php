<?php
/**
 * 品质检查model
 * User: HR
 * Date: 2017/7/6
 */
class QualitycheckModel
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
     * 业务类型：1船入库预约、2船入库订单、3车入库预约、4车入库订单、5管入库预约、6管入库订单、7船出库预约、8船出库订单、9车出库预约、10车出库订单、
     * 11管出库预约、12管出库订单、13靠泊装卸入预约、14靠泊装卸出预约、15靠泊装卸入订单、16靠泊装卸出订单
     * @param  [array] $data array('businesstype'=>业务类型,'booking_sysno'=>预约单主键，'bookingno'=>预约单编号，stock_sysno=>所属订单主键，stockno=>所属订单编号，'bookingdate'=>预计到货（港）日期, shipname=>船名)
     * @return [type]       [description]
     */
    public function  newQualitycheck($data)
    {
        $data['qualitycheckno'] = COMMON::getCodeId('J');
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
        $id = $this->dbh->insert(DB_PREFIX.'doc_qualitycheck',$data);

        if(!$id)
        {
            return ['code'=>300, 'message'=>'添加品质检查单失败'];
        }

        #库存管理业务操作日志
        $user = Yaf_Registry::get(SSN_VAR);
        $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'doc_sysno' => $id,
            'doctype' => 22,
            'opertype' => 0,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime' => '=NOW()',
            'operdesc' => '新建品质检查单',
        );

        $res = $S->addDocLog($input);

        $input = array(
            'doc_sysno' => $id,
            'doctype' => 22,
            'opertype' => 1,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime' => '=NOW()',
            'operdesc' => '暂存品质检查单',
        );

        $res = $S->addDocLog($input);


        return ['code'=>200, 'message'=>$id];


    }


    public function getQualitycheckList($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " created_at >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " created_at <= '{$params['end_time']} 23:59:59' ";
        }
        if ( isset($params['businesstype']) && $params['businesstype'] != '' ) {
            $filter[] = "businesstype = '{$params['businesstype']}'";
        }

        if( isset($params['orderstatus']) && $params['orderstatus'] != '' ){
            $filter[] = "orderstatus = '{$params['orderstatus']}'";
        }
        if( isset($params['carshipname']) && $params['carshipname'] != '' ){
            $filter[] = "carshipname like '%{$params['carshipname']}%'";
        }
        if( isset($params['customername']) && $params['customername'] != '' ){
            $filter[] = "customername like '%{$params['customername']}%'";
        }

        $where =" where isdel=0 "  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by  created_at desc";


        $sql = "SELECT count(sysno) FROM `".DB_PREFIX."doc_qualitycheck` {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT * FROM `".DB_PREFIX."doc_qualitycheck` {$where} {$order}";

        if($params['page']===false){

            $result['list'] = $this->dbh->select($sql);

        }else{

            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);

            $data = $this->dbh->select_page($sql);


            $result['list']=$data;
        }



        return $result;
    }


    public function getQualitycheckByid($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_qualitycheck` where sysno={$id}";
        return $this->dbh->select_row($sql);
    }

    public function getQualitycheckForpendcarin($id)
    {
        $sql = "SELECT q.*,bi.sysno as booking_sysno FROM `".DB_PREFIX."doc_qualitycheck` q
        left join `".DB_PREFIX."doc_pounds_in` pi on (pi.sysno=q.booking_sysno)
        left join `".DB_PREFIX."doc_booking_in` bi on (pi.stockinno=bi.bookinginno)
        where q.sysno={$id}";
        return $this->dbh->select_row($sql);
    }


    public function getQualitycheckdetail($pid='')
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_qualitycheck_detail` where qualitycheck_sysno={$pid} AND isdel = 0 ORDER BY created_at DESC";

        return $this->dbh->select($sql);
    }


    public function update($id,$input)
    {
        return $this->dbh->update(DB_PREFIX.'doc_qualitycheck', $input,'sysno='.intval($id));
    }

    public function updateDetail($id,$input)
    {
        return $this->dbh->update(DB_PREFIX.'doc_qualitycheck_detail', $input,'sysno='.intval($id));
    }
    public function updatePoundin($id,$update)
    {
        return $this->dbh->update(DB_PREFIX.'doc_pounds_in', $update,'sysno='.intval($id));
    }
    public function updatePoundReback($id,$update)
    {
        return $this->dbh->update(DB_PREFIX.'doc_pounds_reback', $update,'sysno='.intval($id));
    }
    public function updateQualitycheck($id,$input,$detail)
    {

        $this->dbh->begin();
        try {
            if($input['apply_user_sysno']==''){
                unset($input['apply_user_sysno']);
                unset($input['apply_employeename']);
            }
            $res = $this->update($id,$input);
            if (!$res) {
                throw new Exception("更新品质检查单失败", 300);
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_qualitycheck_detail','qualitycheck_sysno='.intval($id));

            if(!$res)
            {
                throw new Exception("删除品质检查单明细失败", 300);
                return false;
            }

            foreach ($detail as $value) {
                $u_upload = $value['u_upload'];
                if($value['last_upload']){
                    $u_upload = $u_upload.','.$value['last_upload'];
                }
                unset($value['bjui_local_index']);
                unset($value['u_upload']);
                unset($value['last_upload']);
                unset($value['isskip']);
                if(!empty($value['sysno'])){
                    unset($value['sysno']);
                }
                $value['qualitycheckno'] = $input['qualitycheckno'];
                $value['checktime'] = $value['checktime'];
                $value['qualitycheck_sysno'] = $id;
                $value['created_at'] = '=NOW()';
                $value['updated_at'] = '=NOW()';
                $res = $this->dbh->insert(DB_PREFIX.'doc_qualitycheck_detail',$value);
                if(!$res)
                {
                    throw new Exception("添加品质检查单明细失败", 300);
                    return false;
                }
                $a_img = explode(',',$u_upload);
                foreach ($a_img as $k=>$v){
                    $array1['doc_sysno'] = $res;
                    $this->dbh->update(DB_PREFIX.'system_attach', $array1,'sysno='.intval($v));
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 22,
                'opertype' => $input['orderstatus']==2?1:2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $input['orderstatus']==2?'暂存品质检查单':'提交品质检查单',
            );

            $res = $S->addDocLog($input);

            if(!$res)
            {
                throw new Exception("添加业务操作日志失败", 300);
                return false;
            }

            $this->dbh->commit();
            return ['code'=>200, 'message'=>'更新成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>$e->getCode(), 'message'=>$e->getMessage()];
        }
    }
   public function  getQualityInfo($bookId,$businesstype){
       $sql = "SELECT * from ".DB_PREFIX."doc_qualitycheck dq
                 where   dq.isdel = 0 and dq.booking_sysno = " .intval($bookId) ." and dq.businesstype = " . intval($businesstype);
       return $this->dbh->select_row($sql);
   }



}
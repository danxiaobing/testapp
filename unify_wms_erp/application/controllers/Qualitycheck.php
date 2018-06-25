<?php
/**
 * 品质记录
 * @Author: HR
 * @Date:   2017-7-6
 * @Last Modified by:   HR
 * @Last Modified time: 2017-7-6
 */
class QualitycheckController extends Yaf_Controller_Abstract
{
    public $type = array(
                1=>'船入库预约',
                2=>'船入库订单',
                3=>'车入库磅码单',
                4=>'管入库预约',
                5=>'管入库订单',
                6=>'船出库预约',
                7=>'船出库订单',
                8=>'管出库预约',
                9=>'管出库订单',
                10=>'退货',
        );

    public function init()
    {
        # parent::init();
    }

    public function listAction()
    {
    	$params['type'] = $this->type;

    	return $this->getView()->make('qualitycheck.list', $params);
    }

    public function pendlistAction()
    {
        $params['type'] = $this->type;

        return $this->getView()->make('qualitycheck.pendlist', $params);
    }

    public function compendlistAction()
    {
        $params['type'] = $this->type;

        return $this->getView()->make('qualitycheck.compendlist', $params);
    }

    public function listJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'begin_time'  => $request->getPost('begin_time', ''),
            'end_time'  => $request->getPost('end_time', ''),
            'businesstype'  => $request->getPost('businesstype', ''),
            'orderstatus'    => $request->getPost('orderstatus', ''),
            'carshipname'    => $request->getPost('carshipname', ''),
            'customername'    => $request->getPost('customername', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            );
        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $Q->getQualitycheckList($search);
        $bookno = array(1,4,6,8);    //预约状态
        $stockno = array(2,3,5,7,9,10);  //订单状态
        foreach($list['list'] as $key=>$value){
            //判断业务单号
            if(in_array($value['businesstype'],$bookno)){
                $list['list'][$key]['orderno'] = $value['bookingno'];
            }
            if(in_array($value['businesstype'],$stockno)){
                $list['list'][$key]['orderno'] = $value['stockno'];
            }
        }
        echo json_encode($list);

    }
    //品检审核
    public function auditListAction()
    {
        $params['type'] = $this->type;
        return $this->getView()->make('qualitycheck.auditlist', $params);
    }
    public function auditListJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'begin_time'  => $request->getPost('begin_time', ''),
            'end_time'  => $request->getPost('end_time', ''),
            'businesstype'  => $request->getPost('businesstype', ''),
            'orderstatus'  => $request->getParam('orderstatus', ''),
            'carshipname'    => $request->getPost('carshipname', ''),
            'customername'    => $request->getPost('customername', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            );
        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $Q->getQualitycheckList($search);
        $bookno = array(1,4,6,8);    //预约状态
        $stockno = array(2,3,5,7,9,10);  //订单状态
        foreach($list['list'] as $key=>$value){
            //判断业务单号
            if(in_array($value['businesstype'],$bookno)){
                $list['list'][$key]['orderno'] = $value['bookingno'];
            }
            if(in_array($value['businesstype'],$stockno)){
                $list['list'][$key]['orderno'] = $value['stockno'];
            }
            $input=array(
                'sysno'=>$value['sysno'],
                'businesstype'=>$value['businesstype'],
                'booking_sysno'=>$value['booking_sysno'],
                'stock_sysno'=>$value['stock_sysno'],
            );
        }
        echo json_encode($list);
    }

    //让步审核
    public function skipAuditListAction()
    {
        $params['type'] = $this->type;
        return $this->getView()->make('qualitycheck.skipauditlist', $params);
    }

    public function qualitycheckeditAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $tempType = $request->getParam('tempType', '');
        if(!$id)
        {
            COMMON::result(300,'参数错误!');
            return false;
        }else{
            $action = 'Qualitycheck/editJson';
            $Q = new QualitycheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $params = $Q->getQualitycheckByid($id);
            if($params['orderstatus'] != 2 && $params['orderstatus'] != 5 && $tempType != 'view' && $tempType != 'audit' && $tempType != 'skip'){
                COMMON::result(300,'暂存和退回状态才可以编辑');
                return false;
            }
            $type = $this->type;
            $businesstype = $params['businesstype'];
            if($businesstype==4)
            {
                $params = $Q->getQualitycheckForpendcarin($id);
            }
            $params['businesstype'] = $type[$businesstype];
            $params['businesstypenum'] = $businesstype;
            $params['action'] = $action;
            //客户列表
            $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $search = array(
                'bar_status' => '1',
                'bar_isdel' => '0',
                'page' => false,
            );
            $list = $E->searchEmployee($search);
            $params['employeelist'] = $list['list'];
            $params['id'] = $id;
            $params['tempType'] = $tempType;
            $params['detailUrl'] = $this->getbookdetailInfo($businesstype, $params['booking_sysno']);
            // var_dump($params['detailUrl']);exit;

        }

        return $this->getView()->make('qualitycheck.edit',$params);

    }


    public function qualitycheckdetailAction()
    {
        $request = $this->getRequest();
        $pid = $request->getParam('pid', 0);

        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $Q->getQualitycheckdetail($pid);
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        foreach ($params as $k=>$v){
            $attach1 = $A->getAttachByMAS('qualitycheck','qualitycheck-edit',$v['sysno']);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params[$k]['u_upload']  =  join(',',$files1);
            }
        }
        echo json_encode($params);

    }

    /**
     * 明细编辑function
     * @return [type] [description]
     */
    public function qualitycheckdetaileditAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost('data', []);
        $handlestatus = $request->getParam('handlestatus', '');
        if($handlestatus == 'add'){
            $goodsdata = json_decode($request->getPost('goodsdata', []),true);
            $params['goodsname'] = $goodsdata['goodsname'];
            $params['goods_sysno'] = $goodsdata['goods_sysno'];
        }

        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if ($handlestatus == 'edit' || $handlestatus == 'audit') {
            $attach1 = $A->getAttachByMAS('qualitycheck', 'qualitycheck-edit', $params['sysno']);
            if (is_array($attach1) && count($attach1)) {
                $files1 = array();
                foreach ($attach1 as $file) {
                    $files1[] = $file['sysno'];
                }
                $params['u_upload'] = join(',', $files1);
            }
        }

        $params['handlestatus'] = $handlestatus;

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['timesonly'] = time();

        return $this->getView()->make('qualitycheck.detail',$params);
    }


    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        if(!$id)
        {
            COMMON::result(300,'参数获取错误!');
            exit;
        }
        $detail_data = json_decode($request->getPost('qualitycheck_detail',[]),true);
        $orderstatus = $request->getPost('orderstatus','');

        $input = array(
            'apply_user_sysno'   => $request->getPost('apply_user_sysno',''),
            'apply_employeename' => $request->getPost('apply_employeename',''),
            'orderstatus'        => $orderstatus,
            'qualitycheckno'     => $request->getPost('qualitycheckno',''),
            'updated_at'         => '=NOW()',
            );

        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $Q->updateQualitycheck($id, $input, $detail_data);

        if($res['code']!=200)
        {
            COMMON::result(300, $res['message']);
        }else{
            COMMON::result(200, $res['message']);
        }

    }


    public function delqualitycheckAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        if(!$id)
        {
            COMMON::result(300,'参数获取错误!');
            exit;
        }
        $input = array(
            'isdel'      => 1,
            'updated_at' => '=NOW()',
            );
        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $Q->update($id, $input);

        if(!$res)
        {
            COMMON::result(300, '删除失败!');
        }else{
            COMMON::result(200, '删除成功!');
        }
    }


    public function showQualitychecAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        if(!$id)
        {
            COMMON::result(300,'参数获取错误!');
            exit;
        }

        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $Q->getQualitycheckByid($id);
        $type = $this->type;
        $businesstype = $params['businesstype'];
        if($businesstype==4)
        {
            $params = $Q->getQualitycheckForpendcarin($id);
        }
        $params['businesstype'] = $type[$businesstype];
        $params['businesstypenum'] = $businesstype;
        $params['action'] = $action;
        //客户列表
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['view'] = 'look';
        $params['id'] = $id;
        $params['detailUrl'] = $this->getbookdetailInfo($businesstype, $params['booking_sysno']);
        return $this->getView()->make('qualitycheck.edit',$params);
    }

    public function getbookdetailInfo($businesstype, $booking_sysno)
    {
        // 业务类型：1船入库预约、2船入库订单、3车入库磅码单、4管入库预约、5管入库订单、6船出库预约、7船出库订单、8管出库预约、9管出库订单、10退货
        switch ($businesstype) {
            case 1:
                return "/bookshipin/adddetailJson/id/{$booking_sysno}";
                break;
            case 4:
                return "/bookshipin/adddetailJson/id/{$booking_sysno}";
                break;
            case 6:
                return "/Qualitycheck/getshipoutdetial/id/{$booking_sysno}";
                break;
            case 8:
                return "/Qualitycheck/getshipoutdetial/id/{$booking_sysno}";
                break;
            default:
                return "/bookshipin/adddetailJson/id/{$booking_sysno}";
                break;
        }
    }


    public function getshipoutdetialAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $search = array(
                'bookingout_sysno' =>   $id,
                'page' => false
            );
        $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $detailData =  $B->getBookoutDetailList($search);
        if(!empty($detailData['list'])){
            foreach ($detailData['list'] as $key => $value) {
                $detailData['list'][$key]['bookinginqty'] = $value['bookingoutqty'];
            }
        }
        echo json_encode($detailData['list']) ;
    }

    public function detailListAction(){
        $request = $this->getRequest();
        $module = 'qualitycheck';
        $action = 'qualitycheck-edit';
        $sysno = $request->getParam('id');
//        var_dump($sysno);die();
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach = $A->getAttachByMAS($module,$action,$sysno);

        $params['attach'] = $attach;
        $this->getView()->make('attachment.view',$params);
    }

    public function examJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $examdetail = $request->getPost('examdetail',[]);
        if (!empty($examdetail)) {
            $examdetail = json_decode($examdetail,true);
        }

        if($id == 0){
            COMMON::result(300,'单据信息有误');
            return false;
        }
        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);
        $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $examstep = $request->getPost('examstep',0);
        $auditreason = $request->getPost('auditreason','');
        $qualityInfo = $Q->getQualitycheckByid($id);
        if($examstep == 4){
            //审核通过
            if($examdetail['isskip'] != 1){
                $res = $Q->updateDetail($examdetail['sysno'],array('isskip' => $examdetail['isskip']));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }

                $res = $Q->update($id,array('orderstatus' => 4, 'auditreason' => $auditreason));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
                //回写车入库磅码单
                if($examdetail['ischecked'] == 1 && $qualityInfo['businesstype'] == 3){
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 1));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }else{
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                //回写退货表
                if($examdetail['ischecked'] == 1 && $qualityInfo['businesstype'] == 10){
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 1));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }else{
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 3,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '审核通过',
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }else{
                COMMON::result(300,"已经选择让步，请点击让步提交");
                return false;
            }

        }elseif($examstep == 5){
            //审核不通过
            $res = $Q->update($id,array('orderstatus' => 5 ,'auditreason' => $auditreason));
            if($res){
                if($qualityInfo['businesstype'] == 3){
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }
                if($qualityInfo['businesstype'] == 10){
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $auditreason,
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }else{
                COMMON::result(300,'操作失败');
                return false;
            }
        }elseif($examstep == 6){
            //让步提交
            if($examdetail['isskip'] == 1){
                $res = $Q->updateDetail($examdetail['sysno'],array('isskip' => $examdetail['isskip']));
                if(!$res){
                    COMMON:result(300,'操作失败');
                    return false;
                }
                $res = $Q->update($id,array('orderstatus' => 6, 'auditreason' => $auditreason));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 5,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '让步待审核',
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }
        }elseif($examstep == 7){
            //让步审核通过
            $res = $Q->update($id,array('orderstatus' => 7, 'auditreason' => $auditreason));
            if($res){
                //回写车入库磅码单
                if($qualityInfo['businesstype'] == 3){
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 2));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }elseif($qualityInfo['businesstype'] == 10){
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 2));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 6,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '让步审核通过',
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }else{
                COMMON::result(300,'操作失败');
                return false;
            }

        }elseif($examstep == 8){
            if($qualityInfo['orderstatus'] != 5){
                COMMON::result(300,'退回状态才可以终止');
                return false;
            }
            $res = $Q->update($id,array('orderstatus' => 8));
            if (!$res) {
                COMMON::result(300,'操作失败');
                return false;
            }
            //回写车入库磅码单
            if($qualityInfo['businesstype'] == 3){
                $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
            }

            //回写退货表
            if($qualityInfo['businesstype'] == 10){
                $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
            }
        }
        echo json_encode(array('code' => 200 ,'msg' => '操作成功'));
    }
}
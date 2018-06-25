<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2017/7/5 0010
 * Time: 17:06
 */
class CraneController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        # parent::init();


    }

    /**
     * 鹤位管理
     * @author Jay Xu
     */

    public function listAction()
    {
        $params = array(

        );

        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('crane.list', $params);
    }

    /**
     * 鹤位管理列表JSON
     * @author Jay Xu
     */

    public function craneListJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'cranename' => $request->getPost('cranename',''),
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'bar_status'   => $request->getPost('bar_status','-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $C->searchCrane($search);
        foreach($list['list'] as $key=>$value ){
           $list['list'][$key]['installtime'] = date('Y-m-d',strtotime($value['installtime']));
        }
        echo json_encode($list);
    }


    public function craneEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        if (!$id) {
            $action = "/crane/craneNewJson/";
            $params['action'] = $action;
        } else {
            $action = "/crane/craneEditJson/";
            $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $params = $C->getCraneById($id);
            $params['installtime'] = date('Y-m-d',strtotime($params['installtime']));
            $params['action'] = $action;
            $params['id'] = $id;
        }
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStoragetank($search);
        $params['storagetanklist'] = $list['list'];

        return $this->getView()->make('crane.edit', $params);
    }


    public function CraneNewJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $input = array(
            'cranename'  => $request->getPost('cranename',''),
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'installtime' => $request->getPost('installtime',''),
            'status' => $request->getPost('status', '1'),
            'cranestatus' => $request->getPost('cranestatus',''),
            'csemployee_sysno' => $request->getPost('csemployee_sysno',''),
            'csemployeename' => $request->getPost('csemployeename',''),
            'memo' => $request->getPost('memo',''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno',''),
            'storagetankname' => $request->getPost('storagetankname',''),
            'auditreason' => $request->getPost('auditreason',''),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        //print_r($input);die;

        $input['installtime'] = $input['installtime']?$input['installtime']:date('Y-m-d');
        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $res = $C->addCrane($input,$id);
        if(!$res){
            COMMON::result(300,'添加失败!');
        }else{
            COMMON::result(200,'添加成功!');
        }
    }



    public function craneEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $input = array(
            'cranename'  => $request->getPost('cranename',''),
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'installtime' => $request->getPost('installtime',''),
            'status' => $request->getPost('status', '1'),
            'cranestatus' => $request->getPost('cranestatus',''),
            'csemployee_sysno' => $request->getPost('csemployee_sysno',''),
            'csemployeename' => $request->getPost('csemployeename',''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno',''),
            'storagetankname' => $request->getPost('storagetankname',''),
            'auditreason' => $request->getPost('auditreason',''),
            'memo' => $request->getPost('memo',''),
            'updated_at' => '=NOW()'
        );

        $input['installtime'] = $input['installtime']?$input['installtime']:date('Y-m-d');

        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($ers = $C->updateCrane($input,$id)) {
            $row = $C->getCraneById($ers);
            COMMON::result(200,'更新成功',$row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    public function craneDelAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1,
            'updated_at'    => '=NOW()',
        );

        $array = $C->getCraneById($id);

        if ($array['cranestatus'] != 1 && $array['cranestatus'] != 2 && $array['cranestatus'] != 6) {
            COMMON::result(300, '非暂存或退回状态鹤位不允许删除');
            return false;
        }

        $res = $C->updateCrane($input,$id);

        if(!$res){
            COMMON::result(300,'删除失败!');
        }else{
            COMMON::result(200,'删除成功!');
        }
    }


    /**
     * 启用停用方法
     * @return [type] [description]
     */
    public function setstatusAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $status = $request->getPost('status', '');

        $params = array(
            'status' 		=> $status,
            'updated_at'	=> '=NOW()',
        );
        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $res = $C->updateCrane($params,$id);
        if($status==1){
            $msg = '启用';
        }else{
            $msg = '停用';
        }
        if(!$res){
            COMMON::result(300,$msg.'失败!');
        }else{
            COMMON::result(200,$msg.'成功!');
        }
    }


    /**
     * 审核通过
     */
    public function craneCheckJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $cranestatus = $request->getPost('cranestatus', '');
        $treedata = $request->getPost('treedata', "");
        $treedata = json_decode($treedata, true);

        $input = array(
            'cranename'=>$request->getPost('cranename',''),
            'goods_sysno'=>$request->getPost('goods_sysno',''),
            'goodsname'=>$request->getPost('goodsname',''),
            'installtime' => $request->getPost('installtime',''),
            'csemployee_sysno' => $request->getPost('csemployee_sysno',''),
            'csemployeename' => $request->getPost('csemployeename',''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno',''),
            'storagetankname' => $request->getPost('storagetankname',''),
            'cranestatus' => $cranestatus,
            'auditreason'=> $request->getPost('auditreason', ''),
            'status' => $request->getPost('status', ''),
            'updated_at' => '=NOW()'
        );

        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $C->checkcrane($id, $input,$treedata,$cranestatus);
        if ($res['statusCode'] == 200){
            COMMON::result(200, '审核成功!');
        }else{
            COMMON::result(300, '审核失败!');
        }
    }


}
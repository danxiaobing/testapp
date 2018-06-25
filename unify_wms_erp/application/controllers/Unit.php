<?php
/**
 * 计量单位
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 17:09
 */


class UnitController extends Yaf_Controller_Abstract
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
     * 计量单位列表
     */
    public function listAction()
    {
        $params = array();

        $request = $this->getRequest();
        $this->getView()->make('unit.list',$params);
    }

    /**
     * 计量单位列表
     */
    public function unitlistJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'unitname' => $request->getPost('unitname',''),
            'status' => $request->getPost('bar_status',''),
            'page' => false,
            'orders'  => $request->getPost('orders',''),

        );
        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $U->getBaseUnit($search);
        foreach ($list as $key => $value) {
            if($value['unittype'] == '1')
            {
                $list[$key]['unittype'] = "重量";
            }elseif($value['unittype'] == '2'){
                $list[$key]['unittype'] = "数量";
            }else{
                $list[$key]['unittype'] = "无";
            }
        }
        echo json_encode($list);
    }

    /**
     * 编辑/添加 计量单位
     */
    public function uniteditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);

        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        if(!$id){
            $action = "/unit/unitNewJson/";
            $params =  array ();
        } else {
            $action = "/unit/unitEditJson/";
            $params = $U->getUnitById($id);
        }

        $params['id'] = $id;
        $params['action'] =  $action;
        $this->getView()->make('unit.edit',$params);
    }

    /**
     * 添加 计量单位
     */
    public function unitNewJsonAction()
    {
        $request = $this->getRequest();
         $input = array(
            'unitname'  =>  $request->getPost('unitname',''),
            'unittype'       =>  $request->getPost('unittype',0),
            'decimalpoint'     =>  $request->getPost('decimalpoint',''),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'created_at'    =>'=NOW()',
            'updated_at'    =>'=NOW()'
        );

        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array(
            'unitname'=>$input['unitname']
        );
        if($U->getBaseUnit($search)){
            COMMON::result(300,'添加失败已经存在相同单位名');
            return;
        }
        if($id = $U->addUnit($input))
        {
            $row = $U->getUnitById($id);
            if($row['unittype'] == '1')
            {
                $row['unittype'] = "重量";
            }elseif($row['unittype'] == '2'){
                $row['unittype'] = "数量";
            }else{
                $row['unittype'] = "无";
            }
            COMMON::result(200,'添加成功',$row);
        }else{
            COMMON::result(300,'添加失败');
        }
    }

    public function unitEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $input = array(
            'unitname'  =>  $request->getPost('unitname',''),
            'unittype'       =>  $request->getPost('unittype',0),
            'decimalpoint'     =>  $request->getPost('decimalpoint',''),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'updated_at'    =>'=NOW()'
        );

        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //判断唯一
        $search = array(
            'sysno'=> $id,
            'unitname'=>$input['unitname']
        );
        if($U->getBaseUnitOnly($search)){
            COMMON::result(300,'修改失败已经有相同单位名');
            return;
        }
        if($U->updateUnit($input,$id))
        {
            $row = $U->getUnitById($id);
            if($row['unittype'] == '1')
            {
                $row['unittype'] = "重量";
            }elseif($row['unittype'] == '2'){
                $row['unittype'] = "数量";
            }else{
                $row['unittype'] = "无";
            }
            COMMON::result(200,'修改成功',$row);
        }else{
            COMMON::result(300,'修改失败');
        }
    }

    /**
     * 删除 计量单位
     */

    public function unitdeljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('date',0);
        $id = implode(',',$id);
        //print_r($id);die;
        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $idDel = $U->isDel($id);
        if($idDel>0){
            COMMON::result('300','计量单位被引用不可删除');
            return false;
        }
        if($U->delUnit($id))
        {
            $row = $U->getUnitById($id);
            COMMON::result(200,'删除成功',$row);
        }else{
            COMMON::result(300,'删除失败');
        }
    }
/*
 * 新增状态启用
 * */
    public function statuschangeAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $parms = array(
            'ids'=>$ids,
            'status'=>1,//启用
        );
        $res = $U->updateStatus($parms);
        if($res){
            COMMON::result(200, '启用成功');
        }else{
            COMMON::result('300','启用失败');
        }
    }
    /*
   * 新增状态停用
   * */
    public function statusoverAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $parms = array(
            'ids'=>$ids,
            'status'=>2,//停用
        );
        $U = new UnitModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $res = $U->updateStatus($parms);
        if($res){
            COMMON::result(200, '停用成功');
        }else{
            COMMON::result('300','停用失败');
        }
    }


}
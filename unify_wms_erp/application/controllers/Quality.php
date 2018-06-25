<?php

/**
 * 质量标准
 * User: ty
 * Date: 2016/11/17 0010
 * Time: 19:30
 */
class QualityController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    /**
     * Title:质量列表展示
     */
    public function listAction(){
        $params=array();
        $this->getView()->make('goods.quality.list', $params);
    }

    public function qualitylistJsonAction(){
        $request = $this->getRequest();
        $pages= $request->getPost('pageSize','10');
        $pagec= $request->getPost('pageCurrent','1');

        $search = array (
            'qualityname' => $request->getPost('qualityname',''),
            'bar_status' => $request->getPost('status',''),
        );

        $quality = new QualityModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $params = $quality->getList($search,$pages,$pagec);
        
        echo json_encode($params);
    }

    /**
     * 添加修改展示
     */
    public function QualityeditAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $quality = new QualityModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        if(!$id){
            $action = "/quality/add/";
            $params =  array ();
        } else {
            $action = "/quality/edit/";
            $params = $quality->getQualityById($id);
        }

        $params['id'] = $id;
        $params['action'] =  $action;

        $this->getView()->make('goods.quality.edit',$params);
    }


    /**
     * 新增方法
     */
    public function AddAction(){
        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $request = $this->getRequest();
        $input = array(
            'qualityname'=>  $request->getPost('qualityname',''),
            'status'  =>  $request->getPost('status','1'),
            'isdel'=>  $request->getPost('isdel','0'),
            'created_at'	=> 	'=NOW()',
            'updated_at'	=> 	'=NOW()'
        );

        $isexit = array(
            'qualityname2'=>  $request->getPost('qualityname',''),
        );
        $qualityisexist = $quality->getList($isexit);
        if(count($qualityisexist['list'])!=0){
            COMMON::result(300, '质量标准不能重复！');
            return;
        }

        if($id = $quality->add($input)){
            $row = $quality->getQualityById($id);
            COMMON::result(200,'添加成功',$row);
        }else{
            COMMON::result(300,'添加失败');
        }
    }

    /**
     * Title:编辑方法
     */
    public function EditAction()
    {
        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $input = array(
            'qualityname'=>  $request->getPost('qualityname','0'),
            'status'  =>  $request->getPost('status','1'),
            'isdel'=>  $request->getPost('isdel','0'),
            'updated_at'=>  '=NOW()'
        );

        $oldquality = $quality->getQualityById($id);
        if($oldquality['qualityname']!=$input['qualityname']){
            $isexit = array(
                'qualityname2'=>  $request->getPost('qualityname',''),
            );
            $qualityisexist = $quality->getList($isexit);

            if(count($qualityisexist['list'])!=0){
                COMMON::result(300, '质量标准不能重复！');
                return;
            }
        }

        if($quality->update($id,$input)){
            $row = $quality->getQualityById($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    /*
     * 批量质量标准状态
     * @author wu xianneng
     */
    public function setqualitystatusAction(){
        $request = $this->getRequest();
        $status = $request->getParams('status');
        $status = intval($status['status']);
        $qus = $request->getPost('qus');

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'status'  =>  $status,
            'updated_at'=>  '=NOW()'
        );

        $count=0;
        foreach($qus as $item){
            if($quality->update($item,$input))
                $count++;
        }
        if($count==count($qus)){
            COMMON::result(200,'操作成功');
        }else{
            COMMON::result(300,'操作失败');
        }
    }

    /**
     * Title:删除方法
     */
    public function DeleteAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'isdel' => 1
        );

        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookshiin = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookcarin = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contractisexistquality = $quality->searchcontractgoodsquality($id);
        $bookinisexistquality  = $quality->searchbookinquality($id);

        if($contractisexistquality||$bookinisexistquality){
            COMMON::result(300,'质量标准已被引用，不能删除');
            return;
        }


        if($quality->update($id,$input)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'删除失败');
        }
    }

}
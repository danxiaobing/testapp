<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/17 0017
 * Time: 15:47
 */
class StoragecostController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    public function listAction()
    {
        $params = array();

        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $storagetank_category = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params['goods'] = $goods->getGoodsInfo();
        $params['storagetank_category'] = $storagetank_category->getStoragetankcategory();

        $this->getView()->make('storagecost.list', $params);
    }

    /**
     * Title:查询列表
     */
    public function datailAction()
    {
        $request = $this->getRequest();
        $pages = $request->getPost('pageSize', '10');
        $pagec = $request->getPost('pageCurrent', '1');
        $search = array(
            'storagecostno' => $request->getPost('storagecostno', ''),
            'storagecostname' => $request->getPost('storagecostname', ''),
            'storagecosttype' => $request->getPost('storagecosttype', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'storagetank_category_sysno' => $request->getPost('storagetank_category_sysno', ''),
            'status' => $request->getPost('status', ''),
            'isdel' => $request->getPost('isdel', '0')
        );

        $storagecost = new StoragecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $storagecost->getStoragecost($search, $pages, $pagec);

        echo json_encode($params);
    }

    /**
     * Title：添加编辑视图
     */
    public function storagecostaddeditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $storagecost = new StoragecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $storagetank_category = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $unit = new UnitModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/storagecost/addstoragecost/";
            $params = array();
        } else {
            $action = "/storagecost/editstoragecost/";
            $params = $storagecost->getStoragecostById($id);
            $params['list'] = $storagetank_category->getStoragetankcategoryById($params['storagetank_category_sysno']);
        }

        $params['goods'] = $goods->getGoodsInfos();
        $params['storagetank_category'] = $storagetank_category->getStoragetankcategory();
        $params['units'] = $unit->getUnit();
        $params['id'] = $id;
        $params['action'] = $action;
        $this->getView()->make('storagecost.edit', $params);
    }

    /**
     * title : 新增方法
     */
    public function AddstoragecostAction()
    {
        $request = $this->getRequest();
        $storagecost = new StoragecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'storagecostno' => COMMON::getCodeId('C'),
            'storagecostname' => $request->getPost('storagecostname', ''),
            'storagecosttype' => $request->getPost('storagecosttype', ''),
            'goods_sysno' => $request->getPost('parentId', ''),
            'storagetank_category_sysno' => $request->getPost('storagetank_category_sysno', ''),
            'unit' => $request->getPost('unit', ''),
            'startingprice' => $request->getPost('startingprice', ''),
            'overdueprice' => $request->getPost('overdueprice', ''),
            'minstock' => $request->getPost('minstock', ''),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        if ($id = $storagecost->addStoragecost($input)) {
            $row = $storagecost->getStoragecostById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * title :编辑方法
     */
    public function editstoragecostAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $storagecost = new StoragecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'storagecostno' => $request->getPost('storagecostno', ''),
            'storagecostname' => $request->getPost('storagecostname', ''),
            'storagecosttype' => $request->getPost('storagecosttype', ''),
            'goods_sysno' => $request->getPost('parentId', ''),
            'storagetank_category_sysno' => $request->getPost('storagetank_category_sysno', ''),
            'unit' => $request->getPost('unit', ''),
            'startingprice' => $request->getPost('startingprice', ''),
            'overdueprice' => $request->getPost('overdueprice', ''),
            'minstock' => $request->getPost('minstock', ''),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );
        if ($storagecost->upStoragecost($id, $input)) {
            $row = $storagecost->getStoragecostById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
     * title :仓储管理 软删除
     */
    public function deletestoragecostAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $storagecost = new StoragecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'isdel' => 1
        );
        if ($storagecost->upStoragecost($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
    * 批量启用禁用
    * @author hr
    */
    public function storagecostChangeAction()
    {
        $request = $this->getRequest();

        $data = $request->getPost('data','');

        $state = $request->getPost('state','');

        $storagecost = new StoragecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($storagecost->change($data,$state)){
            COMMON::result(200,'更新成功');
        }else{

            COMMON::result(300,'更新失败');
        }
    }


}
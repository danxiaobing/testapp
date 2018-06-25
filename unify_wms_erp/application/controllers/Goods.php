<?php
/**
 * 货品管理.
 * User: 王文浩
 * Date: 2016/11/17 0017
 * Time: 14:27
 */

class GoodsController extends Yaf_Controller_Abstract
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

    public function basislistAction()
    {
        $params = array();
        $request = $this->getRequest();
        $this->getView()->make('goods.basis.list',$params);
    }

    /**
     * 获取货品基本属性列表
     * @author Alan
     * @time 2016-11-17 15:49:29
     */
    public function basislistJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'goodsno' => $request->getPost('goodsno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'status' => $request->getPost('status',''),
            'isdel' => $request->getPost('isdel','0'),
            'page' => false,
            'orders'  => $request->getPost('orders','')
        );
        $G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $G->getBaseGoodslist($search);
        $list = array();

        foreach($params['list'] as $row){
            if($row['parent_sysno'] ==0 )
                $row['parent_sysno'] = null;
            $list[] = $row;
        }

        echo json_encode($list);
    }

    /**
     * @title 查询商品及相关信息
     */
    public function getGoodsandpriceAction(){
        $G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $data = $G->getGoodsandprice();
        echo json_encode($data);
    }


    /**
     * 添加修改 货品基本属性
     * @author Alan
     * @time 2016-11-17 15:52:10
     */
    public function basiseditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        if(!$id){
            $action = "/goods/basisNewJson/";
            $params =  array ();
        } else {
            $action = "/goods/basisEditJson/";
            $params = $G->getGoodsById($id);
        }

        $params['id'] = $id;
        $params['action'] =  $action;
        $params['goodsinfo'] = $G->getGoodsInfo();

        $this->getView()->make('goods.basis.edit',$params);

    }

    /**
     *  新增货品基本属性
     * @author Alan
     * @time 2016-11-17 18:54:02
     */
    public function basisNewJsonAction()
    {
        $request = $this->getRequest();
         $input = array(
            'parent_sysno'  =>  $request->getPost('parentId',''),
            'goodsno'       =>  $request->getPost('goodsno',''),
            'displayno'     =>  $request->getPost('displayno',''),
            'goodsname'     =>  $request->getPost('goodsname',''),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'created_at'    =>'=NOW()',
            'updated_at'    =>'=NOW()'
        );

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
//        $goodsno = $G->searchGoodsisexist($input);
//        if($goodsno){
//            COMMON::result(300,'货品编号/CA编号不能重复');
//            return ;
//        }

        if($id = $G->addGoods($input))
        {
            $row = $G->getGoodsById($id);
            COMMON::result(200,'添加成功',$row);
        }else{

            COMMON::result(300,'添加失败');
        }
    }

    /**
     * 根据ID修改货品基本属性
     * @author Alan
     * @time 2016-11-17 19:18:55
     */
    public function basisEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $input = array(
            'parent_sysno'  =>  $request->getPost('parentId',''),
            'goodsno'       =>  $request->getPost('goodsno',''),
            'goodsname'     =>  $request->getPost('goodsname',''),
            'displayno'     =>  $request->getPost('displayno',''),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'updated_at'    =>'=NOW()'
        );
        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));

        $oldgoods = $G->getGoodsById($id);
        if($oldgoods['goodsno']!=$input['goodsno']){
            $goodsisexist = $G->searchGoodsisexist($input);
            if(count($goodsisexist)>0){
                COMMON::result(300,'CA编号不能重复');
                return ;
            }
        }

        if($G->updateGoods($input,$id))
        {
            $row = $G->getGoodsById($id);
            COMMON::result(200,'修改成功',$row);
        }else{
            COMMON::result(300,'修改失败');
        }

    }

    /*
     * 批量修改货品基本属性状态
     * @author wu xianneng
     */
    public function setbasisstatusAction(){
        $request = $this->getRequest();
        $status = $request->getParams('status');
        $status = intval($status['status']);
        $qus = $request->getPost('qus');

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        $input = array(
            'status'  =>  $status,
            'updated_at'=>  '=NOW()'
        );

        $count=0;
        foreach($qus as $item){
            if($G->updateGoods($input,$item))
                $count++;
        }
        if($count==count($qus)){
            COMMON::result(200,'操作成功');
        }else{
            COMMON::result(300,'操作失败');
        }
    }

    /**
     * 删除 货品基本属性
     * @author Alan
     * @time 2016-11-17 19:59:49
     */
    public function basisdeljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));

        $search = array(
            'goods_sysno'=>$id,
            'status'=>1,
        );
        $isexistinatt = $G->searchAttibute($search);

        if($isexistinatt){
            COMMON::result(300,'已被其他属性引用，不能删除');
            return ;
        }

        if($G->delGoods($id))
        {
            $row = $G->getGoodsById($id);
            COMMON::result(200,'删除成功',$row);
        }else{
            COMMON::result(300,'修改失败');
        }
    }


    /**
     * 货品 其他信息 列表页 
     * @author Alan
     * @time 2016-11-18 13:30:32
     */

    public function attributeListAction()
    {
        $params = array();

        $this->getView()->make('goods.attribute.list',$params);
    }
    /**
    * 货品其他信息列表
    * @author Alan
    * @time 2016-11-18 14:51:21
    */
    public function attributelistJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'goodsno' => $request->getPost('goodsno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'status' => $request->getPost('status',''),
            'isdel'=>$request->getPost('isdel','0'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders'  => $request->getPost('orders',''),

        );
        $G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $list = $G->getBaseGoodsAttribute($search);

        echo json_encode($list);
    }

    /**
     *  添加/编辑 其他信息
     */
    public function attributeeditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        $S = new StoragetankModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        $U = new UnitModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        if(!$id){
            $action = "/goods/attributeNewJson/";
            $params =  array ();
        } else {
            $action = "/goods/attributeEditJson/";
            $search = array(
                'id'=>$id,
                'page'=>false
            );
            $params['attribute'] = $G->getBaseGoodsAttribute($search);

            $storagetank_categorysysnostr = $params['attribute']['list']['0']['storagetank_sysno'];
            $params['storagetank_sysnoarr'] = explode(',',$storagetank_categorysysnostr);

            //添加附件的显示
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $attach1 = $A->getAttachByMAS('attributelist','attach-1',$id);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $params['attach1']  =  join(',',$files1);
            }
        }

        $params['id'] = $id;
        $params['action'] =  $action;
        //获取货品
        $params['goodsinfo'] = $G->getGoodsInfo();
        //获取储罐材质
        $params['storagetankcategory'] = $S->getStoragetankcategory();

        //获取计量单位
        $params['unit'] = $U->getUnit();

        $this->getView()->make('goods.attribute.edit',$params);
    }

    /**
     * 添加 其他信息
     * @author Alan
     * @time 2016-11-19 11:22:09
     */
    public function attributeNewJsonAction()
    {
        $request = $this->getRequest();
         $input = array(
            'goods_sysno'  =>  $request->getPost('parentId',''),
            'density'  =>  $request->getPost('density',''),
            'islongterm'       =>  $request->getPost('islongterm',''),
            'storagetank_sysno'     =>  $request->getPost('storagetank_sysno',''),
            'controlprice'     =>  $request->getPost('controlprice',''),
            'controlproportion'     =>  $request->getPost('controlproportion',''),
            'rate_waste'     =>  $request->getPost('rate_waste',''),
            'unit_sysno'     =>  $request->getPost('unit_sysno',''),
             'isdrugs'     =>  $request->getPost('isdrugs',0),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'created_at'    =>'=NOW()',
            'updated_at'    =>'=NOW()'
        );

        if($input['storagetank_sysno']!=''){
            $storagetankcategory = explode(',', $input['storagetank_sysno']);
            $S = new StoragetankModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
            $storagetank_sysno = array();
            foreach($storagetankcategory as $item){
                $search = array(
                    'bar_name'=>trim($item),
                    'page'=>false
                );
                $sysno = $S->searchStoragetankcategory($search);
                $storagetank_sysno[] = $sysno['list'][0]['sysno'];

            }

            $storagetank_sysnostr = implode(',', $storagetank_sysno);

            $input['storagetank_sysno'] = $storagetank_sysnostr;
        }

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));

        $isexistattr = $G->getAttributeByGoodsId($input['goods_sysno']);

        if(count($isexistattr)==1){
            COMMON::result(300,'该货品的其他属性已添加');
            return ;
        }

        $chirldnode = $G->chirldnode($input['goods_sysno']);

        if($chirldnode){
            COMMON::result(300,'该货品属于其他货品的父类，不能添加其他属性');
            return ;
        }

        //附件
        $attachment = $request->getPost('attachment',array());
     //   print_r($attachment);
      //  print_r($input);die;
        $res =  $G->addGoodsAttribute($input,$attachment);
     //   print_r($res);die;

        if($res['statusCode']==200)
        {
            $row = $G->getAttributeByGoodsId($res['msg']);
            COMMON::result(200,'添加成功',$row);
        }else{
            COMMON::result(300,'添加失败');
        }
    }

    /**
     * 更新 其他信息
     */
    public function attributeEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);

        $input = array(
            'goods_sysno'  =>  $request->getPost('parentId',''),
            'density'  =>  $request->getPost('density',''),
            'islongterm'       =>  $request->getPost('islongterm',''),
            'storagetank_sysno'     =>  $request->getPost('storagetank_sysno',''),
            'controlprice'     =>  $request->getPost('controlprice',''),
            'controlproportion'     =>  $request->getPost('controlproportion',''),
            'rate_waste'     =>  $request->getPost('rate_waste',''),
            'unit_sysno'     =>  $request->getPost('unit_sysno',''),
            'isdrugs'     =>  $request->getPost('isdrugs',0),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'updated_at'    =>'=NOW()'
        );

        if($input['storagetank_sysno']!=''){
            $storagetankcategory = explode(',', $input['storagetank_sysno']);
            $S = new StoragetankModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
            $storagetank_sysno = array();
            foreach($storagetankcategory as $item){
                $search = array(
                    'bar_name'=>trim($item),
                    'page'=>false
                );
                $sysno = $S->searchStoragetankcategory($search);
                $storagetank_sysno[] = $sysno['list'][0]['sysno'];

            }

            $storagetank_sysnostr = implode(',', $storagetank_sysno);

            $input['storagetank_sysno'] = $storagetank_sysnostr;
        }

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));

        $GoodsAttribute = $G->getGoodsAttributeById($id);

        $isexistattr = $G->getAttributeByGoodsId($input['goods_sysno']);


        if($GoodsAttribute['goods_sysno']!=1&&$isexistattr ==1){
            COMMON::result(300,'该货品的其他属性已添加');
            return ;
        }

        //附件
        $attachment = $request->getPost('attachment',array());
//print_r($attachment);die;
        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        $res = $G->updateGoodsAttributeOnly($input,$id,$attachment);
        if($res['statusCode']==200)
        {
            $row = $G->getAttributeByGoodsId($res['msg']);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    /*
     * 批量修改货品其他属性状态
     * @author wu xianneng
     */
    public function setattributestatusAction(){
        $request = $this->getRequest();
        $status = $request->getParams('status');
        $status = intval($status['status']);
        $qus = $request->getPost('qus');

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        $input = array(
            'status'  =>  $status,
            'updated_at'=>  '=NOW()'
        );

        $count=0;
        foreach($qus as $item){
            if($G->updateGoodsAttribute($input,$item))
                $count++;
        }
        if($count==count($qus)){
            COMMON::result(200,'操作成功');
        }else{
            COMMON::result(300,'操作失败');
        }
    }

    /**
     * 删除 其他属性
     */
    public function attributedeljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $G = new GoodsModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        if($G->delGoodsAttribute($id))
        {
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'删除失败');
        }
    }


}
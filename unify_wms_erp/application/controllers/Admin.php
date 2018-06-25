<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/11/6
 * Time: 14:42
 */
class AdminController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     * @return void
     */
    public function init()
    {
        # parent::init();
    }

    /**
     * 测试网络通讯是否成功
     */
    public function updatePoundoutAction(){
        $request = $this->getRequest();
        $param = $request ->getParams();
        $poundoutid = $param['id'];
        $fullcarqty = $param['fullcarqty'];
        $emptycarqty = $param['emptycarqty'];
        $data = explode(',',$param['data']);

//        echo "<pre>";print_r($data);die;

        $P = new PendcarinModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $res = $P ->adminEditPoundsById($poundoutid,$fullcarqty,$emptycarqty,$data);

        print_r($res);



//        $P = new PoundsapiModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        $poudoutdata = $P ->getOneOutApi($poundoutid);
//        echo "<pre>";print_r($poudoutdata);die;
    }
}
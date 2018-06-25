<?php

/**
 * @Author: Dujiangjiang
 * @Date:   2016-12-07 14:31:11
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-12-10 11:18:50
 */
class BookingController extends Yaf_Controller_Abstract
{

    /**
     * 车入库预约数据列表
     * @author Dujiangjiang
     * @return void
     */
    public function carInListAction()
    {
        $params = array();
        $this->getView()->make('booking.carinlist', $params);
    }

    /**
     * 车入库预约数据JSON
     * @author Dujiangjiang
     * @return json
     */
    public function carInListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'stockintype' => 2, //1船入库，2车入库
        );
        $S = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookingInList($search);
        echo json_encode($list);
    }

    /**
     * 船入库预约数据列表
     * @author Dujiangjiang
     * @return json
     */
    public function shipinlistAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => '',
            'navid' => 'shipin',
            'dataurl' => '/booking/shipInListJson'
        );

        $search = array(
            'page' => false,
        );

        $this->getView()->make('booking.shipinlist', $params);
    }

    /**
     * 车入库预约数据JSON
     * @author Dujiangjiang
     * @return json
     */
    public function shipInListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'begin_time'=>$request->getPost('begin_time'),
            'end_time'=>$request->getPost('end_time'),
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'stockintype' => 1, //1船入库，2车入库
        );
        $S = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookingInList($search);
        echo json_encode($list);
    }

    /**
     * 车出库预约数据列表
     * @author Dujiangjiang
     * @return json
     */
    public function carOutListAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => '',
            'dataurl' => '/booking/carOutListJson',
            'inaction' => '/stockout/edit/type/2/id/',
            'navtitle' => '生成车出库',
            'navid' => 'carout',
        );

        $search = array(
            'page' => false,
        );

        $this->getView()->make('booking.outlist', $params);
    }

    /**
     * 车出库预约数据JSON
     * @author Dujiangjiang
     * @return json
     */
    public function carOutListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'stockintype' => 2, //1船入库，2车入库
        );
        $S = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookingOutList($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);
    }

    /**
     * 船出库预约数据列表
     * @author Dujiangjiang
     * @return json
     */
    public function shipOutListAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => '',
            'dataurl' => '/booking/shipOutListJson',
            'inaction' => 'stockout/shipedit/type/1/id/',
            'navtitle' => '生成船出库订单',
            'navid' => 'shipout',
            'bar_type' => 1,
        );

        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('booking.shipoutlist', $params);
    }

    /**
     * 船出库预约数据JSON
     * @author Dujiangjiang
     * @return json
     */
    public function shipOutListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'stockintype' => 1, //1船出库，2车出库
        );
        $S = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookingOutList($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);
    }

    /**
     * 管出库预约数据列表
     * @author Dujiangjiang
     * @return json
     */
    public function pipelineOutListAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => '',
            'dataurl' => '/booking/pipelineOutListJson',
            'inaction' => '/stockout/pipelineEdit/bookout_sysno/',
            'navtitle' => '生成管出库订单',
            'navid' => 'pipelineout',
            'bar_type' => 3,
        );

        $this->getView()->make('booking.pipelineoutlist', $params);
    }

    /**
     * 管出库预约数据JSON
     * @author Dujiangjiang
     * @return json
     */
    public function  pipelineOutListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'stockintype' => 3, 
        );
        $S = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookingOutList($search);
        echo json_encode($list);
    }
}
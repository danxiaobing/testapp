<?php
/**
 * Created by PhpStorm.
 * User: jp
 * Date: 2017/07/05 0017
 * Time: 10:35
 */
class BerthController extends Yaf_Controller_Abstract
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
     * 泊位管理
     * @author jp
     */

    public function listAction()
    {
        $params = array(

        );
        $this->getView()->make('berth.list', $params);
    }

    /**
     * 泊位管理列表JSON
     * @author jp
     */

    public function ListJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'berthname' => $request->getPost('berthname',''),
             'wharfname' => $request->getPost('wharfname',''),
             'bar_status'   => $request->getPost('bar_status',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $S = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $S->searchBerth($search);
//print_r($list);die;
        echo json_encode($list);
    }


    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $S = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        //获取码头信息
        $params['wharf'] = $S->getWharfMsg();
        if (!$id) {
            $action = "/berth/NewJson/";


        } else {
            $action = "/berth/EditJson/";
            $params['list'] = $S->getBerthById($id);
        }

        $params['id'] = $id;
        $params['action'] = $action;
        //print_r($params);die;
        $this->getView()->make('berth.edit', $params);
    }

    public function NewJsonAction()
    {

        $request = $this->getRequest();

        $B = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'berthname' => $request->getPost('berthname', ''),                  //泊位号
            'berthloadcapacity' => $request->getPost('berthloadcapacity', ''),//允许最大吃水(米)
            'berthlength' => $request->getPost('berthlength', ''),              //泊位长度(米)
            'berthdeep' => $request->getPost('berthdeep', ''),                 //泊位水深(米)
            'berthtype' => $request->getPost('berthtype', ''),                 //核准停泊船型：0不限
            'berthloadweight' => $request->getPost('berthloadweight', ''),      //核准停泊能力(吨)
            'wharf_sysno' => $request->getPost('wharf_sysno', ''),              //所属码头表主键
            'wharfname' => $request->getPost('wharfname', ''),                  //所属码头名称
            'berthmarks' => $request->getPost('berthmarks', ''),                //备注
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        #后台验证泊位编号
        $search = array (
            'berthname'=>$input['berthname'],
            'page' => false,
        );
        $berthid = $B->searchBerth($search);
        if(!empty($berthid['list'])){
            COMMON::result(300,'泊位编号不能重复');
            return;
        }
//print_r($input);die;
        if ($id = $B->addBerth($input)) {
            $row = $B->getBerthById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }



    public function EditJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getPost('id', '');
        if(!$id){
            COMMON::result(300,'数据异常');
            return;
        }

        $B = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'berthname' => $request->getPost('berthname', ''),                  //泊位号
            'berthloadcapacity' => $request->getPost('berthloadcapacity', ''),//允许最大吃水(米)
            'berthlength' => $request->getPost('berthlength', ''),              //泊位长度(米)
            'berthdeep' => $request->getPost('berthdeep', ''),                 //泊位水深(米)
            'berthtype' => $request->getPost('berthtype', ''),                 //核准停泊船型：0不限
            'berthloadweight' => $request->getPost('berthloadweight', ''),      //核准停泊能力(吨)
            'wharf_sysno' => $request->getPost('wharf_sysno', ''),              //所属码头表主键
            'wharfname' => $request->getPost('wharfname', ''),                  //所属码头名称
            'berthmarks' => $request->getPost('berthmarks', ''),                //备注
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );
        //print_r($input);die;
        #后台验证泊位编号
        $search = array (
            'berthname'=>$input['berthname'],
            'sysno'=>$id,
            'page' => false,
        );
        $berthid = $B->searchBerth($search);
        if(!empty($berthid['list'])){
            COMMON::result(300,'泊位编号不能重复');
            return;
        }

        if ($B->updateBerthedit($id, $input)) {
            $row = $B->getBerthById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    public function DelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $berthorderno = $request->getPost('berthname','');

        $S = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1
        );
        $res = $S->updateBerth($id, $input,trim($berthorderno));
        if ($res['statuscode']==200) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败-'.$res['msg']);
        }
    }


    /**
     * 批量启用禁用
     * @author jp
     */
    public function ChangeAction()
    {
        $request = $this->getRequest();

        $data = $request->getPost('data','');

        $state = $request->getPost('state','');

        $B = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($B->change($data,$state)){
            COMMON::result(200,'更新成功');
        }else{

            COMMON::result(300,'更新失败');
        }
    }
    /**
     * 泊位使用历史界面跳转
     * @return [type] [description]
     */
    public function berthhistoryAction()
    {
        $request = $this->getRequest();
        $params['id'] = $request->getPost('id', 0);
        $warf = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['wharf'] = $warf->getwharfdetail();
        //业务类型
        $params['businesstype'] = array(
            '1'=>'船入库预约',
            '2'=>'船入库',
            '7'=>'船出库预约',
            '8'=>'船出库',
            '13'=>'靠泊装货预约',
            '15'=>'靠泊装货',
            '14'=>'靠泊卸货预约',
            '16'=>'靠泊卸货',
        );

        return $this->getView()->make('berth.berthhistory', $params);

    }

    public function historyJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $search = array(
            'begin_time'    => $request->getPost('begin_time', ''),
            'end_time'      => $request->getPost('end_time', ''),
            'wharfname'  => $request->getPost('wharfname', ''),
          //  'wharf_sysno'  => $request->getPost('wharf_sysno', ''),
            'businesstype'   => $request->getPost('businesstype', ''),
            'berth_sysno'=>$id,
            'pageCurrent'   => COMMON:: P(),
            'pageSize'      => COMMON:: PR(),
        );
//print_r($search);die;
        $B = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $B->getberthHistory($search);

        echo json_encode($params);
    }

    /**
     * 导出
     */
    public function historyExcelAction(){

        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $search = array(
            'begin_time'    => $request->getPost('begin_time', ''),
            'end_time'      => $request->getPost('end_time', ''),
            'wharfname'  => $request->getPost('wharfname', ''),
            'wharf_sysno'  => $request->getPost('wharf_sysno', ''),
            'businesstype'   => $request->getPost('businesstype', ''),
            'berth_sysno'=>$id,
            'pageCurrent'   => COMMON:: P(),
            'pageSize'      => COMMON:: PR(),
        );

        $B = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $B->getberthHistory($search);
       // print_r($list);die;

        ob_end_clean();//清除缓冲区,避免乱码
        Header("Content-type:application/octet-stream;charset=utf-8");
        Header("Accept-Ranges:bytes");
        Header("Content-type:application/vnd.ms-excel");
        Header("Content-Disposition:attachment;filename=泊位历史.xls");

        echo
            iconv("UTF-8","GBK//IGNORE", "泊位号")."\t".
            iconv("UTF-8", "GBK//IGNORE", "码头")."\t".
            iconv("UTF-8", "GBK//IGNORE", "船名")."\t".
            iconv("UTF-8", "GBK//IGNORE", "业务单据类型")."\t".
            iconv("UTF-8", "GBK//IGNORE","业务单号")."\t".
            iconv("UTF-8", "GBK//IGNORE", "使用时间")."\t".
            iconv("UTF-8", "GBK//IGNORE", "输送品种")."\t".
            iconv("UTF-8", "GBK//IGNORE", "操作人")."\t";

        foreach ($list['list'] as $key=>$item) {
            switch($item['businesstype'])
            {
                case 1:
                    $list['list'][$key]['businesstype1'] = '船入库预约单';
                    break;
                case 2:
                    $list['list'][$key]['businesstype1'] = '船入库订单';
                    break;
                case 3:
                    $list['list'][$key]['businesstype1']= '车入库预约单';
                    break;
                case 4:
                    $list['list'][$key]['businesstype1'] = '车入库订单';
                    break;
                case 5:
                    $list['list'][$key]['businesstype1'] = '管入库预约单';
                    break;
                case 6:
                    $list['list'][$key]['businesstype1']= '查看管入库订单';
                    break;
                case 7:
                    $list['list'][$key]['businesstype1'] = '船出库预约单';
                     break;
                case 8:
                    $list['list'][$key]['businesstype1']='船出库订单';
                    break;
                case 9:
                    $list['list'][$key]['businesstype1'] ='车出库预约单';
                     break;
                case 10:
                    $list['list'][$key]['businesstype1']='车出库订单';
                    break;
                case 11:
                    $list['list'][$key]['businesstype1']= '管出库预约单';
                    break;
                case 12:
                    $list['list'][$key]['businesstype1'] ='管出库订单';
                    break;
                case 13:
                    $list['list'][$key]['businesstype1'] = '靠泊装货预约单';
                    break;
                case 14:
                    $list['list'][$key]['businesstype1'] = '靠泊卸货预约单';
                    break;
                case 15:
                    $list['list'][$key]['businesstype1'] = '靠泊装货订单';
                    break;
                case 16:
                    $list['list'][$key]['businesstype1'] = '靠泊卸货订单';
                    break;
            }

//print_r($list);die;
            echo "\n";
            echo 'D' .$item['berthno']
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['wharfname'])
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['shipname'])
                ."\t".iconv("UTF-8", "GBK//IGNORE",  $list['list'][$key]['businesstype1'])
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['businessno'])
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['usetime'])
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['goodsname'])
                ."\t".iconv("UTF-8", "GBK//IGNORE", $item['created_employeename']);

        }
    }


}
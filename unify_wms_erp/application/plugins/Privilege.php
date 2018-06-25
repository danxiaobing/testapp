<?php
/**
 * 系统权限插件
 * 1. 动作处理之前判断是否有权限并显示信息
 * 2. 动作处理之后判断是否需要记录日志并处理
 *
 * @author  James
 * @date    2012-01-10 15:00
 * @version $Id$
 */

class PrivilegePlugin extends Yaf_Plugin_Abstract
{
    /**
     * 操作对应编号
     *
     * @var integer
     */

    /**
     * 操作正式处理之前执行，判断输出设定
     * @return void
     */
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

        $P = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);

        $controller =  $request->getControllerName();
        $action = $request->getActionName();

        if($action == 'login' || $action == 'userlogin' || $action == 'logintimeout' || $action == 'ajaxlogin' || $action == 'vcode')
            return;
        $params = ($request->getRequest('params',''));
        if($params != ''){
            Yaf_Registry::set('api', true);
            $secret = new Blowfish();
            $string = $secret->decrypt($params);
            parse_str($string,$data);
            error_log(date("Y-m-d H:i:s") . "\t" . $data['model']."|".$data['action'] . "\t" . json_encode($data) . "\n", 3, './logs/phpinput.log');
//            $data = '{"model":"storageapi","action":"getStockList","customerno":"C20170313432455","goodsno":"45","qualityno":"\u56fd\u6807\u826f\u7b49\u54c1"}';
//            $data = json_decode($data, true);
            if(empty($data)){
                COMMON::ApiJson('1002', '非法参数');
            }
            if(!isset($data['model']) || !isset($data['action'])){
                COMMON::ApiJson('1000', '非法参数');
            }
            if($data['model'] == '' || $data['action'] == ''){
                COMMON::ApiJson('1001', '非法参数');
            }
            foreach ($data as $key => &$value) {
                if(!is_array($value))
                {
                    $value = trim($value);
                }
            }
            unset($value);
            $controller = $data['model'];
            $action = $data['action'];
//            echo "<pre>";print_r($data);die;
            $controller = ucfirst($controller);
            $request->setControllerName($controller);
            $request->setActionName($action);
            $request->setParam($data);
            return true;
        }


        //如果是webapi方式 访问则直接跳过登陆
        if(strtolower($controller) != strtolower('webapi')) {
            if (!$user) {
                if ($controller == 'Index' && $action == 'index') {
                    $response->setRedirect('/login');
                } else {
                    COMMON::result(301, '登录超时，请重新登录。');
                    exit;
                }
            }

            if ($P->check($controller, $action, $user)) {

            } else {
                COMMON::result(300, '您没有权限访问，请联系管理员。');
                exit;
            }
        }
        return true;
    }
}

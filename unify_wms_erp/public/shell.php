<?php
ini_set('display_errors', 1);
error_reporting(E_ALL^E_NOTICE);
date_default_timezone_set('Asia/Shanghai');
if (phpversion() >= '5.3') {
        define('APPLICATION_PATH', dirname(__DIR__));
} else {
        define('APPLICATION_PATH', dirname(dirname(__FILE__)));
}

define('VIEW_PATH', APPLICATION_PATH.'/application/views/');
define('WEB_ROOT',  '/');
define('SSN_PASS',  'online');
define('SSN_INFO',  'msr');
define('SSN_VAR',  'hengyang');
define('SSN_LOG',   'log');
define('SSN_SA',    99999);
define('VERSION', date("YmdH"));
define('DB_PREFIX',  'hengyang_');

define('VAL_YES',     1);
define('VAL_NO',      0);
define('VAL_ALL',   100);
define('VAL_NONE', -100);

define('WEB_HOST',"http://v2.chinayie.com");

require_once (APPLICATION_PATH.'/vendor/autoload.php');


$app = new Yaf_Application(APPLICATION_PATH .'/application/conf/app.ini');

$config = Yaf_Application::app()->getConfig();
Yaf_Registry::set("config", $config);



$db = $config->get("db");
Yaf_Registry::set("db", new MySQL(
        $db->host,
        $db->port,
        $db->username,
        $db->password,
        $db->default,
        $db->charset
));

//车出库单终止
/*
$app->execute("stopStockout");
function stopStockout() {
        echo "start:".date("Y-m-d H:i:s")."\n";
        $SO = new StockoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $SO->stopAllTimeOut(); //终止出库单
        echo "end:".date("Y-m-d H:i:s")."\n";
}
*/

//提单撤销
/*
$app->execute("stopintroduction");
function stopintroduction() {
        echo "start:".date("Y-m-d H:i:s")."\n";
        $I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $I->recycleEndingStocks(); //回收结存量
        echo "end:".date("Y-m-d H:i:s")."\n";
}
*/

//退回库存量
//$app->execute("updatestock");
//function updatestock() {
//    echo "start:".date("Y-m-d H:i:s")."\n";
//    $overdueullage = new Report_OverdueullageModel(Yaf_Registry :: get("db"));
//    $overdueullage -> backstock();
//    echo "end:".date("Y-m-d H:i:s")."\n";
//}

//定时计算超期损耗量
$app->execute("overdueullage");
function overdueullage() {
    echo "start:".date("Y-m-d H:i:s")."\n";
    $overdueullage = new Report_OverdueullageModel(Yaf_Registry :: get("db"));
    $res = $overdueullage -> index();
    if($res) {
        #计算仓储费
        $S = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S->addFinancecostByPlan();
        //计算提单费用
        countIntroduce();
    }
    echo "end:".date("Y-m-d H:i:s")."\n";
}

//计算每天储罐的结存量
$app->execute("balance");
function balance() {
    echo "start:".date("Y-m-d H:i:s")."\n";
    $balanceInstance = new Report_BalanceModel(Yaf_Registry :: get("db"));
    $balanceInstance -> index();
    echo "end:".date("Y-m-d H:i:s")."\n";
}

//合同过期提醒记录插入
$app->execute("contractmes");
function contractmes() {
    echo "start:".date("Y-m-d H:i:s")."\n";
    $C = new ContractModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
    $C ->insertmes();
    echo "end:".date("Y-m-d H:i:s")."\n";
}

//计算提单费用
function countIntroduce(){
    echo "start:".date("Y-m-d H:i:s")."\n";
    $introduceInstance = new  IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
    $introduceInstance -> batchRunIntroduce();
    echo "end:".date("Y-m-d H:i:s")."\n";
}


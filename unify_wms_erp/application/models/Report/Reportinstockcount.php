<?php
/**
 * @Author: wu xianneng
 * @Date:   2016-12-17 14:38:52
 * @Last Modified by:   wu xianneng
 * @Last Modified time: 2016-12-17 17:13:54
 */
class Report_ReportinstockcountModel {
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Constructor
     *
     * @param   object  $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null) {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 获取入库单数据
     */
    public function getList($params){
        $result = $params;
        $arr = array('一月份','二月份','三月份','四月份','五月份','六月份','七月份','八月份','九月份','十月份','十一月份','十二月份');
        for($i=0;$i<12;$i++){
            $starttime = $params['year'].'-'.($i+1).'-01';
            $endtime = $params['year'].'-'.($i+2).'-01';
            if($i==11){
                $endtime = ($params['year']+1).'-01-01';
            }

            $sql = "SELECT SUM(sid.beqty) FROM ".DB_PREFIX."doc_stock_in_detail sid
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sid.stockin_sysno
                WHERE si.stockinstatus in (3,4) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $totalqty = $this->dbh->select_one($sql);

            $sql = "SELECT SUM(sid.beqty) FROM ".DB_PREFIX."doc_stock_in_detail sid
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sid.stockin_sysno
                WHERE si.stockintype = 1 AND si.stockinstatus in (3,4) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $shipqty = $this->dbh->select_one($sql);

            $sql = "SELECT SUM(sid.beqty) FROM ".DB_PREFIX."doc_stock_in_detail sid
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sid.stockin_sysno
                WHERE si.stockintype = 2 AND si.stockinstatus in (3,4) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $carqty = $this->dbh->select_one($sql);

            $sql = "SELECT COUNT(si.sysno) FROM ".DB_PREFIX."doc_stock_in si
                    LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON sid.stockin_sysno = si.sysno
                    WHERE si.stockintype = 1 AND si.stockinstatus in (3,4) AND sid.goodsnature in (1,2,3) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $shipoutnu = $this->dbh->select_one($sql)?$this->dbh->select_one($sql):'';

            $sql = "SELECT COUNT(si.sysno) FROM ".DB_PREFIX."doc_stock_in si
                    LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON sid.stockin_sysno = si.sysno
                    WHERE si.stockintype = 1 AND si.stockinstatus in (3,4) AND sid.goodsnature in (4) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $shipinnu = $this->dbh->select_one($sql)?$this->dbh->select_one($sql):'';

            $sql = "SELECT SUM(sid.beqty) FROM ".DB_PREFIX."doc_stock_in_detail sid
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sid.stockin_sysno
                WHERE si.stockintype = 1 AND si.stockinstatus in (3,4) AND sid.goodsnature in (1,2,3) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $shipoutqty = $this->dbh->select_one($sql);

            $sql = "SELECT SUM(sid.beqty) FROM ".DB_PREFIX."doc_stock_in_detail sid
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sid.stockin_sysno
                WHERE si.stockintype = 1 AND si.stockinstatus in (3,4) AND sid.goodsnature in (4) AND sid.updated_at >= '$starttime' AND sid.updated_at < '$endtime'";
            $shipinqty = $this->dbh->select_one($sql);

            $sql = "SELECT COUNT(sic.sysno) FROM ".DB_PREFIX."doc_stock_in_cars sic
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sic.stockin_sysno
                WHERE si.stockinstatus in (3,4) AND sic.updated_at >= '$starttime' AND sic.updated_at < '$endtime'";
            $carnu = $this->dbh->select_one($sql)?$this->dbh->select_one($sql):'';

            //管出总量
            $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as pipelineinqty from ".DB_PREFIX."doc_stock_in s LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sd ON s.sysno = sd.stockin_sysno  where  s.stockinstatus = 4 and s.isdel=0 and s.stockintype =3 group by extract(year_month from sd.updated_at)";
            $pipeInStockCount = $this->dbh->select($sql);

            //管出外贸
            $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as pipelineinqty from ".DB_PREFIX."doc_stock_in s LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sd ON s.sysno = sd.stockin_sysno  where  s.stockinstatus = 4 and s.isdel=0 and s.stockintype =3 and sd.goodsnature !=4 group by extract(year_month from sd.updated_at)";
            $pipeInStockCount_in = $this->dbh->select($sql);
            //管出内贸
            $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as pipelineinqty from ".DB_PREFIX."doc_stock_in s LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sd ON s.sysno = sd.stockin_sysno  where  s.stockinstatus = 4 and s.isdel=0 and s.stockintype =3 and sd.goodsnature =4 group by extract(year_month from sd.updated_at)";
            $pipeInStockCount_out = $this->dbh->select($sql);

            $result['list'][$i] = array(
                'month'=>$arr[$i],
                'totalqty'=>$totalqty,
                'shipqty'=>$shipqty,
                'carqty'=>$carqty,
                'shipoutnu'=>$shipoutnu,
                'shipinnu'=>$shipinnu,
                'shipoutqty'=>$shipoutqty,
                'shipinqty'=>$shipinqty,
                'carnu'=>$carnu,
//                'pipeInStockCount'=>$pipeInStockCount,
//                'pipeInStockCount_in'=>$pipeInStockCount_in,
//                'pipeInStockCount_out'=>$pipeInStockCount_out
            );
        }

        foreach ($result['list'] as $key => $value){

            foreach($pipeInStockCount as $item){
                $month = COMMON::getMonth($item['month']);
                if($month == $value['month']){
                    $result['list'][$key]['pipeInStockCount']=$item['pipelineinqty'];
                }
            }
            foreach($pipeInStockCount_in as $item){
                $month = COMMON::getMonth($item['month']);
                if($month == $value['month']){
                    $result['list'][$key]['pipeInStockCount_in']=$item['pipelineinqty'];
                }
            }
            foreach($pipeInStockCount_out as $item){
                $month = COMMON::getMonth($item['month']);
                if($month == $value['month']){
                    $result['list'][$key]['pipeInStockCount_out']=$item['pipelineinqty'];
                }
            }
        }
//        echo '<pre>';
//        print_r($result['list']);die;
        $result['list'][12] = array(
            'month'=>'合计',
            'totalqty'=>0,
            'shipqty'=>0,
            'carqty'=>0,
            'shipoutnu'=>0,
            'shipinnu'=>0,
            'shipoutqty'=>0,
            'shipinqty'=>0,
            'carnu'=>0,
            'pipeInStockCount'=>0,
            'pipeInStockCount_in'=>0,
            'pipeInStockCount_out'=>0
        );
        foreach($result['list'] as $item){
            $result['list'][12]['totalqty'] = $result['list'][12]['totalqty']+$item['totalqty'];
            $result['list'][12]['shipqty'] = $result['list'][12]['shipqty']+$item['shipqty'];
            $result['list'][12]['carqty'] = $result['list'][12]['carqty']+$item['carqty'];
            $result['list'][12]['shipoutnu'] = $result['list'][12]['shipoutnu']+$item['shipoutnu'];
            $result['list'][12]['shipinnu'] = $result['list'][12]['shipinnu']+$item['shipinnu'];
            $result['list'][12]['shipoutqty'] = $result['list'][12]['shipoutqty']+$item['shipoutqty'];
            $result['list'][12]['shipinqty'] = $result['list'][12]['shipinqty']+$item['shipinqty'];
            $result['list'][12]['carnu'] = $result['list'][12]['carnu']+$item['carnu'];
            $result['list'][12]['pipeInStockCount'] = $result['list'][12]['pipeInStockCount']+$item['pipeInStockCount'];
            $result['list'][12]['pipeInStockCount_in'] = $result['list'][12]['pipeInStockCount_in']+$item['pipeInStockCount_in'];
            $result['list'][12]['pipeInStockCount_out'] = $result['list'][12]['pipeInStockCount_out']+$item['pipeInStockCount_out'];
        }

        return $result;
    }

}
<?php 


/**
* 
*/
class Report_CountgoodsModel
{
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
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }


    public function getCountgoodsList($params=array())
    {
        $year = $params['year'];

        $data = $this->getGoodsstock($year,$params['type']);


        $result = [];
        // $result['totalRow']=count($data);
        // $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
        // $list=array_chunk($data,$params['pageSize'],false);
        // $result['list']=$list[$params['pageCurrent']-1];


        if($params['page']===false && empty($params['pageSize'])){
            return $data;
        }else{
            $result['totalRow']=count($data);
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $list=array_chunk($data,$params['pageSize'],false);
            $result['list']=$list[$params['pageCurrent']-1];          
            return $result;
        }


    }


    /**
     * 获取统计货品数量
     * @return [type] [description]
     */
    public function getGoodsstock($year,$type)
    {
        $goodsarr = $this->getGoodsname($type);

        foreach ($goodsarr as $key => $value) {

            for ($i=1; $i <=12 ; $i++) { 
                if ($i<10) {
                    $a = $year.'-0'.$i;
                    if ($i == 9){
                        $b = $year.'-'.($i+1);
                    }else{
                        $b = $year.'-0'.($i+1);
                    }
                }else{
                    $a = $year.'-'.$i;
                    $b = $year.'-'.($i+1);
                }

                switch ($type) {
                    //入库统计
                    case 1:
                        $stocknum = $this->getCarshipinstock($a,$b,$value);
                        break;
                    //出库统计
                    case 2:
                        $stocknum = $this->getCarshipoutstock($a,$b,$value);
                        break;
                    //损耗统计
                    case 3:
                        $stocknum = $this->getLoss($a,$b,$value);
                        break;
                    //存货统计
                    case 4:
                        $stocknum = $this->getStock(array('begin_time'=>$a,'goodsname'=>$value),$i,$year);
                        break;
                    default:
                        # code...
                        break;
                }

                $arr[$key]['goodsname'] = $value;
                $arr[$key][$i] = $stocknum;
                $arr[$key]['countnum'] +=$stocknum;
            }
            
        }

        return $arr;
    }

    
    /**
     * 获取入库数量
     * @param  [type] $where [description]
     * @return [array]        [description]
     */
    private function getCarshipinstock($a='',$b='',$goodsname)
    {
        // $carwhere = "Where  isdel = 0 AND poundsinstatus = 4 AND (updated_at>= '{$a}' AND updated_at< '{$b}' ) AND goodsname='{$goodsname}' ";

        $shipwhere = "WHERE si.isdel=0 AND si.stockinstatus=4 AND (si.stockindate>='{$a}' AND si.stockindate<'{$b}') AND sid.goodsname='{$goodsname}' ";
        // $sql = "SELECT SUM(beqty/1000) num FROM `".DB_PREFIX."doc_pounds_in` {$carwhere}";

        // $carinstock = $this->dbh->select_one($sql); 

        $sql =  "SELECT SUM(sid.beqty) num,sid.goodsname FROM `".DB_PREFIX."doc_stock_in` si
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno = sid.stockin_sysno
                {$shipwhere}  
        ";
        $shipinstock = $this->dbh->select_one($sql); 

        // $countnum = $carinstock+$shipinstock;

        return $shipinstock==''?0:$shipinstock;
    }


    //获取入库所有的商品名
    private function getGoodsname($type)
    {
        if($type==1 || $type==3)
        {
            $sql = "SELECT sid.goodsname FROM `".DB_PREFIX."doc_stock_in` si
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno = sid.stockin_sysno
                    WHERE  si.isdel=0  AND si.stockinstatus in(3,4) GROUP BY sid.goodsname
            ";
        }else{
            $sql = "SELECT sod.goodsname FROM `".DB_PREFIX."doc_stock_out` so
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sod ON so.sysno = sod.stockout_sysno
                    WHERE so.isdel=0  AND so.stockoutstatus in(3,4) GROUP BY sod.goodsname";   
        }

        $array = $this->dbh->select($sql);

        return $this->repeat($array);
    }

    //获取出库数量
    private function getCarshipoutstock($a='',$b='',$goodsname)
    {
        // $carwhere = "Where status=1 AND isdel = 0 AND poundsoutstatus = 4 AND (updated_at>= '{$a}' AND updated_at< '{$b}' ) AND goodsname='{$goodsname}' ";
        $shipwhere = "WHERE so.isdel=0 AND so.stockouttype=1 AND so.stockoutstatus=4 AND (sod.updated_at>='{$a}' AND sod.updated_at<'{$b}') AND sod.goodsname='{$goodsname}'";
/*        $sql = "SELECT SUM(beqty/1000) num FROM `".DB_PREFIX."doc_pounds_out` {$carwhere}";

        $caroutstock = $this->dbh->select_one($sql); */

        $sql =  "SELECT SUM(sod.beqty) num FROM `".DB_PREFIX."doc_stock_out` so
                LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sod ON so.sysno = sod.stockout_sysno
                {$shipwhere} 
        ";
        $shipoutstock = $this->dbh->select_one($sql); 

        // $countnum = $caroutstock+$shipoutstock;
        return $shipoutstock==''?0:$shipoutstock;
    }

    //获取损耗量
    public function getLoss($a,$b,$goodsname)
    {   

        $sql = "SELECT IF(SUM(ullage+clearqty) is null,0,SUM(ullage+clearqty)) FROM ".DB_PREFIX."storage_stock WHERE updated_at>='{$a}' AND updated_at< '{$b}' AND goodsname = '{$goodsname}' AND iscurrent=1 ";

        return $this->dbh->select_one($sql);
    }

    //存货统计
    public function getStock($params,$i,$year)
    {
        // $days = cal_days_in_month(CAL_GREGORIAN, $i, $year);
        $days=date('j',mktime(0,0,1,($i==12?1:$i+1),1,($i==12?$year+1:$year))-24*3600);
        $params['end_time'] = $params['begin_time'].'-'.$days;
        $params['begin_time'] = $params['begin_time'].'-01';
        // $params['end_time'] = '2017-04-30';
        // $params['begin_time'] = '2017-04-01';
        $params['page'] = false;
        $R = new ReportgoodsModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc") );
        $params = $R->getlist2($params);
        // var_dump($params);exit();
        return $params[0]['lastqty']==''?0:round($params[0]['lastqty'],3);
    }

     /**
     * 品名去重
     * @param array $array
     * @return array
     */
    private static  function repeat(array $array){
        //定义去重条件
        $goodsarr = [];
        if(is_array($array)){
            foreach ($array as $key => $value) {
                //去重 并把重复的写入相同结果集里
                if(in_array($value['goodsname'], $goodsarr)){
                    continue;
                }else {
                    //不同的的直接写入
                    $goodsarr[] = $value['goodsname'];
                }
            }
        }
        return $goodsarr;
    }


    public function getGoodsoutin($params=array())
    {

       //船入库单
        
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[0] = "si.`updated_at` >= '{$params['updated_at']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[1] = "si.`updated_at` <= '{$params['end_time']}'";
        }
        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[3] = "si.`customername` like '%{$params['customername']}%' ";
        }

        $where = '';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        //船入库
        $sql = "SELECT si.sysno,si.stockinno,sid.storagetank_sysno,bs.storagetankname,SUM(DISTINCT sid.beqty) instock
            ,sid.goodsname,si.customername
            FROM `".DB_PREFIX."doc_stock_in` si 
            LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno = sid.stockin_sysno
            LEFT JOIN  `".DB_PREFIX."base_storagetank` bs ON sid.storagetank_sysno=bs.sysno
            LEFT JOIN ".DB_PREFIX."storage_stock ss ON  si.sysno = ss.firstfrom_sysno
            WHERE si.stockinstatus =4 AND si.isdel=0 AND si.stockintype=1 AND ss.iscurrent=1 AND si.stockintype=1 {$where}
            GROUP BY storagetankname,sysno";

        $stockshipin = $this->dbh->select($sql);


        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[0] = "so.`updated_at` >= '{$params['updated_at']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[1] = "so.`updated_at` <= '{$params['end_time']}'";
        }
        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[3] = "so.`customername` like '%{$params['customername']}%' ";
        }

        $where = '';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }



        $data = [];
        $outstock = [];
        $outdata = [];
        foreach ($stockshipin as $k => $v) {
            //船出库
            $stockshipout = $this->getstockshipout($v['sysno'],$where);

            //车出库
            $stockcarout = $this->getstockcarout($v['sysno'],$where);



            $outdata = $this->getStockcarshipout($stockshipout,$stockcarout);

            $data[] = $v;

            $count = count($data)-1;
            if(!empty($outdata)){

                foreach ($outdata as $key => $value) {

                    if($v['sysno']==$value['sysno'] && $v['storagetank_sysno']==$value['storagetank_sysno'])
                    {

                        $data[$count]['outstock'] += floatval($value['outstock']); //船货品的出和入

                    }else{
                        $value['outstock'] == '' ? $value['outstock']=0 : $value['outstock'];
                        $data[] = $value;
                    }
                }   

            }




        }

        $shiparr = $this->getalltanks($data);


        // return $shiparr 船的所有出入库加货转的出入库 end

        //车入库

        $sql = "SELECT si.sysno,si.stockinno,si.stockindate,pi.goodsname,pi.storagetank_sysno,pi.storagetankname,si.customername,SUM(pi.beqty/1000) as instock
                FROM ".DB_PREFIX."doc_pounds_in pi
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON pi.stockin_sysno = si.sysno
                WHERE si.stockintype = 2 AND si.isdel = 0 AND pi.poundsinstatus=4 
                GROUP BY pi.stockinno,pi.storagetank_sysno";

        $stockcarin = $this->dbh->select($sql);



        $data = [];
        $outstock = [];
        $outdata = [];
        foreach ($stockcarin as $k => $v) {

            $stockshipout = $this->getstockshipout($v['sysno']);

            $stockcarout = $this->getstockcarout($v['sysno']);


            $outdata = $this->getStockcarshipout($stockshipout,$stockcarout);

            $data[] = $v;

            $count = count($data)-1;
            if(!empty($outdata)){

                foreach ($outdata as $key => $value) {

                    if($v['sysno']==$value['sysno'] && $v['storagetank_sysno']==$value['storagetank_sysno'])
                    {

                        $data[$count]['outstock'] += floatval($value['outstock']); //船货品的出和入

                    }else{
                        empty($value['outstock']) ? $value['outstock']=0 : $value['outstock'];
                        $data[] = $value;
                    }
                }   

            }


        }


        $cararr = $this->getalltanks($data);

        $arr = [];

        foreach($shiparr as $k=>$v){

            if(empty($v['outstock'])){
                $v['outstock']= 0;
            }
            $v['lastqty'] = $v['instock']+$v['instock']-$v['outstock'];
            $arr[] = $v;

        }

        foreach ($cararr as $k => $v) {
            if(empty($v['outstock'])){
                $v['outstock']= 0;
            }
            $v['lastqty'] = $v['instock']+$v['instock']-$v['outstock'];            
            $arr[] = $v;
        }



        $result = [];
        if($params['page']===false && empty($params['pageSize'])){
            return $arr;
        }else{
            $result['totalRow']=count($arr);
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $list=array_chunk($arr,$params['pageSize'],false);
            $result['list']=$list[$params['pageCurrent']-1];
             return $result;
        }

    }


    public function getTankout($firstfrom_sysno)
    {
        $sql = "SELECT ss.sysno,ss.firstfrom_no stockinno,std.storagetankname,SUM(std.transqty) outstock,ss.goodsname,ss.customername from ".DB_PREFIX."doc_stock_trans_detail std 
                LEFT JOIN ".DB_PREFIX."storage_stock ss  ON ss.sysno = std.out_stock_sysno
                where ss.firstfrom_sysno = {$firstfrom_sysno} and ss.iscurrent=1 and doctype=3 GROUP BY ss.sysno ";
        return $this->dbh->select($sql);
    }


    public function getTankin($firstfrom_sysno)
    {
        $sql = "SELECT  ss.sysno,ss.firstfrom_no stockinno,std.storagetankname,SUM(std.transqty) instock,ss.goodsname,ss.customername from ".DB_PREFIX."storage_stock ss
                LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std  ON ss.sysno = std.in_stock_sysno
                where ss.firstfrom_sysno = {$firstfrom_sysno} and ss.iscurrent=1 and doctype=3 GROUP BY ss.sysno ";
        return $this->dbh->select($sql);
    }


    public function getstockshipout($stockin_sysno,$where='')
    {
            $sql = "SELECT si.sysno,si.stockinno,sod.storagetank_sysno,bs.storagetankname,SUM(sod.beqty) outstock,sod.goodsname,so.customername
                    FROM ".DB_PREFIX."doc_stock_out so
                    LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON so.sysno=sod.stockout_sysno
                    LEFT JOIN ".DB_PREFIX."base_storagetank bs ON sod.storagetank_sysno=bs.sysno
                    LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=sod.stockin_sysno
                    WHERE so.stockoutstatus=4 AND so.isdel=0 AND so.stockouttype=1 AND sod.stockin_sysno={$stockin_sysno} {$where}
                    GROUP BY storagetankname,stockin_sysno";
            return $this->dbh->select($sql);

    }


    public function getstockcarout($stockin_sysno,$where='')
    {
            $sql = "SELECT si.sysno,si.stockinno,po.storagetank_sysno,po.storagetankname,po.goodsname,po.customername,SUM(po.beqty/1000) outstock,po.goodsname,po.customername
                    FROM ".DB_PREFIX."doc_stock_out so 
                    LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON so.sysno=sod.stockout_sysno
                    LEFT JOIN ".DB_PREFIX."doc_pounds_out po ON po.stockout_sysno=so.sysno AND po.stockoutdetail_sysno=sod.sysno
                    LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=sod.stockin_sysno
                    WHERE po.poundsoutstatus=4 AND si.sysno={$stockin_sysno} {$where}  GROUP BY si.sysno,storagetankname";
            return $this->dbh->select($sql);
    }


    public function getStockcarshipout($stockshipout=array(),$stockcarout=array())
    {
            if(empty($stockshipout))
            {
                $outdata = $stockcarout;

            }else if(empty($stockcarout)){

                $outdata = $stockshipout;

            }else{
                foreach ($stockshipout as  $shipv) {
                    $outdata[] = $shipv;
                    $outcount = count($outdata)-1;
                    foreach ($stockcarout as $carv) {
                        if($shipv['sysno']==$carv['sysno'] && $shipv['storagetank_sysno']==$carv['storagetank_sysno']){
                            $outdata[$outcount]['outstock'] += $carv['outstock'];
                        }else{
                            $outdata[] = $carv;
                        }
                    }
                }
            }

            return $outdata;
    }


    public function getalltanks($data)
    {
        $arr = [];
        $sysnoarr = [];
        foreach ($data as $k => $v) {

            if(!in_array($v['sysno'], $sysnoarr)){
                $out = $this->getTankout($v['sysno']);

                $in = $this->getTankin($v['sysno']);
                $sysnoarr[] = $v['sysno'];
            }else{
                continue;
            }

            $arr[] = $v;
            //in
           foreach ($in as $key => $value) {

                //out
                foreach ($out as  $va) {
                    if($value['sysno']==$va['sysno']){
                        $value['outstock'] = $va['outstock'];
            
                    }else{
                        continue;
                    }
                }
                //out
                $arr[] = $value;
            }
            //in

        }

        return $arr;
    }


    public function getGoodsoutindetail($params)
    {

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = "`customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = "`storagetank_sysno` = '{$params['storagetank_sysno']}' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = "`goods_sysno` = '{$params['goods_sysno']}' ";
        }
        if (isset($params['time']) && $params['time'] != '') {
            $arr = explode('-', $params['time']);
            $days=date('j',mktime(0,0,1,($arr[1]==12?1:$arr[1]+1),1,($arr[1]==12?$arr[0]+1:$arr[0]))-24*3600);

            $end_time = $params['time'].'-'.$days.' 23:59:59';

            $filter[] = "`doc_time` >= '{$params['time']}-01' AND `doc_time` <= '{$end_time}' ";
        }


        $where = 'isdel =0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        
        $sql = "SELECT count(*) FROM ".DB_PREFIX."doc_goods_record_log WHERE doc_type in (1,2,3,4,5,6) AND {$where}";


        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT * FROM ".DB_PREFIX."doc_goods_record_log WHERE doc_type in (1,2,3,4,5,6) AND {$where}";

        if (empty($params['pageSize']) && $params['page'] == false) {         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            return $result;
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            //
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }


    }


    public function getGoodsoutin2($params=array())
    {
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $arr = explode('-', $params['begin_time']);
            // $days = cal_days_in_month(CAL_GREGORIAN, $arr[1], $arr[0]);
            $days=date('j',mktime(0,0,1,($arr[1]==12?1:$arr[1]+1),1,($arr[1]==12?$arr[0]+1:$arr[0]))-24*3600);
            // var_dump($days);exit;
            $end_time = $params['begin_time'].'-'.$days.' 23:59:59';
            $filter[] = "`doc_time` >= '{$params['begin_time']}-01' AND doc_time <= '{$end_time}' ";

            $sonwhere = "`doc_time` >= '{$params['begin_time']}-01' AND doc_time <= '{$end_time}' ";
        }
        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[] = "`customername` like '%{$params['customername']}%' ";
        }

        $where = 'isdel =0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

     
        $sql = "SELECT storagetankname,storagetank_sysno,customername,customer_sysno,goodsname,goods_sysno
                FROM ".DB_PREFIX."doc_goods_record_log 
                WHERE doc_type in(1,2,3,4,5,6) AND {$where}
                GROUP BY storagetank_sysno,customername order by storagetank_sysno,doc_type";

        $data = $this->dbh->select($sql);


        foreach ($data as $key => $value) {
            $sql = "SELECT SUM(beqty) instock FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type in (1,2) AND isdel=0 AND {$sonwhere} AND customer_sysno={$value['customer_sysno']} AND goods_sysno={$value['goods_sysno']}
                    AND storagetank_sysno = {$value['storagetank_sysno']}";
            $instock = $this->dbh->select_one($sql);

            $data[$key]['instock'] = empty($instock) ? 0 : $instock;

            
            $sql = "SELECT SUM(beqty) outstock FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type in (3,4) AND isdel=0 AND {$sonwhere} AND customer_sysno={$value['customer_sysno']} AND goods_sysno={$value['goods_sysno']}
                    AND storagetank_sysno = {$value['storagetank_sysno']}";
            
            $outstock = $this->dbh->select_one($sql);
            
            $data[$key]['outstock'] = empty($outstock) ? 0 : $outstock;


            $sql = "SELECT SUM(beqty) traninstock FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type in (5) AND isdel=0 AND {$sonwhere} AND customer_sysno={$value['customer_sysno']} AND goods_sysno={$value['goods_sysno']}
                    AND storagetank_sysno = {$value['storagetank_sysno']}";
            
            $traninstock = $this->dbh->select_one($sql);

            $data[$key]['traninstock'] = empty($traninstock) ? 0 : $traninstock;


            $sql = "SELECT SUM(beqty) tranoutstock FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type in (6) AND isdel=0 AND {$sonwhere} AND customer_sysno={$value['customer_sysno']} AND goods_sysno={$value['goods_sysno']}
                    AND storagetank_sysno = {$value['storagetank_sysno']}";
            
            $tranoutstock = $this->dbh->select_one($sql);

            $data[$key]['tranoutstock'] = empty($tranoutstock) ? 0 : $tranoutstock;


            $sql = "SELECT SUM(beqty) lastqty FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type in(1,2,3,4,5,6) AND isdel=0  AND goods_sysno={$value['goods_sysno']} AND storagetank_sysno = {$value['storagetank_sysno']} AND customer_sysno={$value['customer_sysno']} AND doc_time < '{$params['begin_time']}'";
            
            $lastqty = $this->dbh->select_one($sql);
            
            $data[$key]['lastqty'] = empty($lastqty) ? 0 : $lastqty;               


            $sql = "SELECT SUM(beqty) customer_beqty FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type in(1,2,3,4,5,6) AND isdel=0  AND customer_sysno={$value['customer_sysno']} AND goods_sysno={$value['goods_sysno']} AND doc_time <= '{$end_time}'
                    ";
            $customer_beqty = $this->dbh->select_one($sql);

            $data[$key]['customer_beqty'] = $customer_beqty;

            $sql = "SELECT SUM(beqty) storagetank_qty FROM ".DB_PREFIX."doc_goods_record_log
                    where doc_type in(1,2,3,4,5,6) AND isdel=0 AND goods_sysno={$value['goods_sysno']} AND storagetank_sysno = {$value['storagetank_sysno']} AND doc_time <= '{$end_time}' ";

            $storagetank_qty = $this->dbh->select_one($sql);
            // var_dump($sql);exit;
            $data[$key]['storagetank_qty'] = $storagetank_qty;

/*            $sql = "SELECT SUM(beqty) storagetank_in FROM ".DB_PREFIX."doc_goods_record_log
                    where doc_type in(7) AND goods_sysno={$value['goods_sysno']} AND storagetank_sysno = {$value['storagetank_sysno']}";

            $storagetank_in = $this->dbh->select_one($sql);

            $data[$key]['instock'] += empty($storagetank_in) ? 0 : $storagetank_in;


            $sql = "SELECT SUM(beqty) storagetank_out FROM ".DB_PREFIX."doc_goods_record_log
                    where doc_type in(8) AND goods_sysno={$value['goods_sysno']} AND storagetank_sysno = {$value['storagetank_sysno']}";

            $storagetank_out = $this->dbh->select_one($sql);

            $data[$key]['outstock'] += empty($storagetank_out) ? 0 : $storagetank_out;     */


        }


        if($params['page']===false && empty($params['pageSize'])){
            return $data;
        }else{
            $result['totalRow']=count($data);
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $list=array_chunk($data,$params['pageSize'],false);
            $result['list']=$list[$params['pageCurrent']-1];          
            return $result;
        }

    }



}
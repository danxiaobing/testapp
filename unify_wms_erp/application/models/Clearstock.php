<?php
/**
 * 清库管理
 * User: ty
 * Date: 2016/11/24 0023
 * Time: 16:43
 */
class ClearstockModel
{
    /**
     * 数据库类实例
     * @var object
     */
    public $dbh = null;
    public $mch = null;

    /**
     * Constructor
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }


    /**
     *  查询单号(已在清库单不显示)
     * @param   object $dbh
     * @return  void
     */
    public function getstockinno($search){
        $filter = array();
        if(isset($search['customer_sysno']) && $search['customer_sysno'] != ''){
            $filter[] = " ss.`customer_sysno` = '".$search['customer_sysno']."'";
        }
       // $where = 'where ss.`isclearstock`=0 AND ss.`iscurrent`=1 AND ss.`status`<2 AND ss.`isdel`=0 AND ( (ss.`doctype`=3 AND dstd.`sysno` IS NOT NULL) OR dsid.`sysno` IS NOT NULL) ';
      //  $where = 'where ss.`isclearstock`=0 AND ss.`iscurrent`=1 AND ss.`status`<2 AND ss.`isdel`=0 AND ( dstd.`sysno` IS NOT NULL OR dsid.`sysno` IS NOT NULL) ';
        if(1<= count($filter)){
            $where .= " and ".implode(' AND',$filter);
        }
        $result=array('total'=>0,'list'=>array());

        $sql="SELECT a.* FROM
            (SELECT ss.sysno,ss.goodsname,ss.goodsqualityname,ss.instockqty,ss.stockqty,ss.doctype,dsi.stockinno,dsi.sysno AS stockin_sysno,dst.stocktransno,dst.sysno AS stocktrans_sysno,unit.unitname
            FROM `".DB_PREFIX."storage_stock` AS ss
            LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` AS dstd ON dstd.`in_stock_sysno`=ss.`sysno`
            LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` AS dsid ON dsid.`stock_sysno`=ss.`sysno` AND dsid.`status`<2 AND dsid.`isdel`=0
            LEFT JOIN `".DB_PREFIX."doc_stock_in` AS dsi ON dsi.`sysno`=dsid.`stockin_sysno` AND dsi.`isdel`=0 AND dsi.`status`<2 AND dsi.`stockinstatus`=4
            LEFT JOIN `".DB_PREFIX."doc_stock_trans` AS dst ON dst.`sysno`=dstd.`stocktrans_sysno` AND dst.`isdel`=0 AND dst.`status`<2 AND dst.`stocktransstatus`=4
            LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2 
            LEFT JOIN `".DB_PREFIX."base_unit` AS unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
            {$where}) AS a WHERE a.`sysno` NOT IN 
            (SELECT dscd.`stock_sysno` FROM ".DB_PREFIX."doc_stock_clear dsc JOIN ".DB_PREFIX."doc_stock_clear_detail dscd ON dsc.`sysno`=dscd.`stockclear_sysno` AND dsc.`isdel`=0 AND dsc.`status`<2 )";

        if(isset($search['page']) && $search['page'] == false){
            $result['list'] = $this->dbh->select($sql);
            return $result;
        }
        else{
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }
    }
    /*
     * 20170606改 清库存
     *
     * */
    public function getstockList($search)
    {
        $filter = array();

        if(isset($search['customer_sysno']) && $search['customer_sysno'] != ''){
            $filter[] = " ss.`customer_sysno` = '".$search['customer_sysno']."'";
        }
        if(isset($search['stockinno']) && $search['stockinno'] !=''){
            $filter[]   = "( ss.`firstfrom_no` LIKE  '%{$search['stockinno']}%' or  st.`stocktransno` LIKE  '%{$search['stockinno']}%' )";
        }
        if(isset($search['stock_sysno']) && $search['stock_sysno'] !=''){
            $filter[] =" ss.`sysno` = '".$search['stock_sysno']."'";
        }
        if(isset($search['goodsname']) && $search['goodsname'] !=''){
            $filter[]   = " ss.`goodsname` LIKE  '%{$search['goodsname']}%'";
        }
        if(isset($search['shipname']) && $search['shipname'] !=''){
            $filter[]   = " ss.`shipname` LIKE  '%{$search['shipname']}%'";
        }
        $where ='';
        if(1<= count($filter)){
            $where .= " and ".implode(' AND',$filter);
        }
        $result=array('total'=>0,'list'=>array());

        $sql = "SELECT COUNT( DISTINCT ss.sysno)
                FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON ss.firstfrom_sysno = si.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON ss.sysno=sid.stock_sysno AND si.sysno=sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON ss.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno`
                LEFT JOIN `".DB_PREFIX."doc_contract` dc ON ss.contract_sysno = dc.sysno
                WHERE ss.`iscurrent`=1 AND ss.clearstockstatus = 1 AND ss.`status`=1 AND ss.`isdel`=0 AND si.stockinstatus!=5  {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT ss.*,si.stockindate,st.stocktransdate,unit.unitname,dc.contractno,st.sysno stocktrans_sysno,st.buy_customername,st.stocktransno,dc.contractnodisplay,bga.controlproportion,
        if(ss.doctype=3,st.transqty,ss.instockqty) as instockqty,if(ss.doctype=3,st.sysno,ss.firstfrom_sysno) as stockin_sysno,(ss.instockqty - ss.outstockqty) as okqty,(ss.stockqty-ss.clockqty) as availableqty,if(ss.doctype=3,st.stocktransno,ss.firstfrom_no) as stockinno
                FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON ss.firstfrom_sysno = si.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON ss.sysno=sid.stock_sysno AND si.sysno=sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON st.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno`
                LEFT JOIN `".DB_PREFIX."doc_contract` dc ON ss.contract_sysno = dc.sysno
                WHERE ss.`iscurrent`=1 AND ss.clearstockstatus = 1  AND ss.`status`=1 AND ss.`isdel`=0  {$where} GROUP BY ss.sysno
                ORDER BY ss.updated_at ";
        // error_log($sql, 3, 'sql_print.txt');
       //  echo $sql;die;
        if (empty($search['pageSize']) && $search['page'] == false) {         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            return $result;
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            $result['list'] = $this->dbh->select_page($sql);

            return $result;
        }
    }

    public function getNewstockinno($search){
        $filter = array();
        if(isset($search['customer_sysno']) && $search['customer_sysno'] != ''){
            $filter[] = " sin.`customer_sysno` = '".$search['customer_sysno']."'";
        }
        if(isset($search['stockinno']) && $search['stockinno'] !=''){
            $filter[]   = " sin.`stockinno` LIKE  '%{$search['stockinno']}%'";
        }
        if(isset($search['stockin_sysno']) && $search['stockin_sysno'] !=''){
            $filter[] =" sin.`sysno` = '".$search['stockin_sysno']."'";
        }
        if(1<= count($filter)){
            $where .= " and ".implode(' AND',$filter);
        }
        $result=array('total'=>0,'list'=>array());
     if(isset($search['page']) && $search['page'] == false){
         $sql = "SELECT sin.*,sin.stockindate AS stockindate,sind.stockin_sysno as stockin_sysno,sind.goodsnature AS goodsnature,SUM(sind.tobeqty) AS tobeqty,SUM(sind.beqty) AS instockqty,sind.shipname AS shipname,sind.goodsname AS goodsname,q.qualityname AS goodsqualityname,sind.unitname AS unitname,sind.goodsnature AS goodsnature
                 FROM ".DB_PREFIX."doc_stock_in sin
                 LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sind.stockin_sysno = sin.sysno
                 LEFT JOIN ".DB_PREFIX."base_goods_quality q ON  q.sysno = sind.goods_quality_sysno AND q.isdel=0 and q.status<2
                 where sin.isdel=0 and sin.stockinstatus = 4 AND sin.status < 2 AND sind.isdel = 0 AND sind.status<2 {$where}
                 GROUP BY sin.sysno
                 ORDER BY sin.sysno ";
            $result['list'] = $this->dbh->select($sql);
            $sqls = "SELECT DISTINCT(sin.sysno),sum(ss.outstockqty) as outstockqty,ss.firstfrom_sysno AS firstfrom_sysno,SUM(ss.stockqty) AS stockqty,SUM(ss.clockqty) AS clockqty,ss.doctype AS doctype
                     FROM ".DB_PREFIX."doc_stock_in sin
                     LEFT JOIN ".DB_PREFIX."storage_stock ss on ss.firstfrom_sysno = sin.sysno and iscurrent = 1
                     WHERE  ss.doctype in (1,2,4,5) AND ss.`clearstockstatus`=1 AND ss.`iscurrent`=1 AND ss.`status`<2 AND ss.`isdel`=0 AND  sin.isdel = 0 AND sin.status < 2 and sin.stockinstatus = 4 {$where}
                     GROUP BY sin.sysno ";
         $stockinfo = $this->dbh->select($sqls);
         if(empty($stockinfo)){
             $result['list'] = array();
            return $result;
         }
         foreach($result['list'] as $key=>$sinId){
             $result['list'][$key]['outstockqty'] = floatval($this->getOutstockqty($sinId['sysno']));//出库量
             foreach($stockinfo as $k=>$stocId){
                 if($stocId['firstfrom_sysno']==$sinId['sysno']){
                     $result['list'][$key]['stockqty'] = $stocId['stockqty'];//余量
                     $result['list'][$key]['clockqty'] = $stocId['clockqty'];//锁定量
                     $result['list'][$key]['clearqty'] =$result['list'][$key]['instockqty']- $result['list'][$key]['outstockqty'];//清库余量
                     $result['list'][$key]['doctype'] = $stocId['doctype'];//入库类型
                 }
             }
         }
        }
//        else{
//            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
//            $this->dbh->set_page_num($search['pageCurrent']);
//            $this->dbh->set_page_rows($search['pageSize']);
//            $result['list'] = $this->dbh->select_page($sql);
//            return $result;
//        }
//print_r($result);die;
        return $result;
    }
    /*
     * 获取出库量
     * */
    public function getOutstockqty($sinId){
        $sql = "select SUM(soutd.beqty) as outstockqty from ".DB_PREFIX."doc_stock_out sout
                 LEFT JOIN ".DB_PREFIX."doc_stock_out_detail soutd on sout.sysno = soutd.stockout_sysno
                 WHERE  sout.stockoutstatus = 4 and sout.isdel=0 and sout.status<2 and soutd.stockin_sysno = {$sinId}
                 GROUP BY soutd.stockin_sysno";
        return $this->dbh->select_one($sql);
    }
    /**
     *  新增清库详情
     * @param   object $dbh
     * @return  void
     */
    public function getDetail($search){
        $filter = array();
        $where='';

        $result=array('total'=>0,'list'=>array());
        if(isset($search['stockin_sysno']) && $search['stockin_sysno'] != ''){
            $filter[] = " dsi.`sysno` = '".$search['stockin_sysno']."'";
            if(1<= count($filter)){
                $where .= " and ".implode(' AND',$filter);
            }
            $sql="SELECT ss.sysno as stock_sysno,dsi.stockinno,ss.doctype,ss.goodsname,dsid.goodsnature,unit.unitname,ss.instockqty,(ss.instockqty-ss.stockqty) as restockqty,ss.stockqty
                    FROM `".DB_PREFIX."storage_stock`  ss
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` dsid ON dsid.stock_sysno=ss.`sysno` AND dsid.`isdel`=0 AND dsid.`status`<2
                    LEFT JOIN `".DB_PREFIX."doc_stock_in` dsi ON dsi.`sysno`=dsid.`stockin_sysno` AND dsi.`isdel`=0 AND dsi.`status`<2 AND dsi.`stockinstatus`=4
                    LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2
                    LEFT JOIN `".DB_PREFIX."base_unit` AS unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                    WHERE  ss.`iscurrent`=1 AND ss.`status`=1 AND ss.`isdel`=0 AND  dsid.`sysno` IS NOT NULL {$where} ";
        }else if((isset($search['stocktrans_sysno']) && $search['stocktrans_sysno'] != '')){
            $filter[] = " dst.`sysno` = '".$search['stocktrans_sysno']."'";
            if(1<= count($filter)){
                $where .= " and ".implode(' AND',$filter);
            }
            $sql="SELECT ss.sysno as stock_sysno,dst.stocktransno,ss.doctype,ss.goodsname,unit.unitname,ss.instockqty,(ss.instockqty-ss.stockqty) AS restockqty,ss.stockqty,dst.stocktransstatus,dsid.goodsnature
                    FROM `".DB_PREFIX."storage_stock`  ss    
                    LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` dstd ON dstd.`in_stock_sysno`=ss.`sysno` AND dstd.`isdel`=0 AND dstd.`status`<2
                    LEFT JOIN `".DB_PREFIX."doc_stock_trans` dst ON dst.`sysno`=dstd.`stocktrans_sysno` AND dst.`isdel`=0 AND dst.`status`<2 AND dst.`stocktransstatus`=4
                    LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2
                    LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` dsid ON dsid.`goods_sysno`=ss.`goods_sysno`
                    WHERE ss.`iscurrent`=1 AND ss.`status`<2 AND ss.`isdel`=0 AND ss.`doctype`=3 AND dstd.`sysno` IS NOT NULL {$where} GROUP BY(dst.`stocktransno`) ";
        }

        if(isset($search['page']) && $search['page'] == false){
            $result['list'] = $this->dbh->select($sql);
            return $result;
        }
        else{
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }
    }
    public function getNewDetail($search){
        $filter = array();
        $where ='';
        $result=array('total'=>0,'list'=>array());

        if(isset($search['stockin_sysno']) && $search['stockin_sysno'] !=''){
            $filter[] =" sin.`sysno` = '".$search['stockin_sysno']."'";
        }
        if(count($filter)>=1){
            $where .= " and ".implode(' AND',$filter);
        }

        if(isset($search['page']) && $search['page'] == false){
            $sql = "SELECT sin.*,sind.stockin_sysno as stockin_sysno,sin.stockindate AS stockindate,sind.goodsnature AS goodsnature,SUM(sind.tobeqty) AS tobeqty,SUM(sind.beqty) AS instockqty,sind.shipname AS shipname,sind.goodsname AS goodsname,sind.qualityname AS goodsqualityname,sind.unitname AS unitname,sind.goodsnature AS goodsnature
                 FROM ".DB_PREFIX."doc_stock_in sin
                 LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sind.stockin_sysno = sin.sysno
                 where sin.isdel=0 and sin.stockinstatus = 4 AND sin.status < 2 AND sind.isdel = 0 AND sind.status<2 {$where}
                 GROUP BY sin.sysno
                 ORDER BY sin.sysno ";
                $result['list'] = $this->dbh->select($sql);
            $sqls = "SELECT sum(ss.outstockqty) as outstockqty,ss.firstfrom_sysno AS firstfrom_sysno,SUM(ss.stockqty) AS stockqty,SUM(ss.clockqty) AS clockqty,ss.doctype AS doctype  from ".DB_PREFIX."doc_stock_in sin
                 LEFT JOIN ".DB_PREFIX."storage_stock ss on ss.firstfrom_sysno = sin.sysno
                 WHERE   ss.`iscurrent`=1 AND ss.`status`<2 AND ss.`isdel`=0 AND  sin.isdel = 0 AND sin.status < 2 and sin.stockinstatus = 4 {$where}
                 GROUP BY sin.sysno ";
            $stockinfo = $this->dbh->select($sqls);
            if(empty($stockinfo)){
                return false;
            }
            foreach($result['list'] as $key=>$sinId){
                $result['list'][$key]['outstockqty'] = floatval($this->getOutstockqty($sinId['sysno']));
                foreach($stockinfo as $k=>$stocId){
                    if($stocId['firstfrom_sysno']==$sinId['sysno']){
                        $result['list'][$key]['stockqty'] = $stocId['stockqty'];//余量
                        $result['list'][$key]['clockqty'] = $stocId['clockqty'];//锁定量
                        $result['list'][$key]['clearqty'] =$result['list'][$key]['instockqty']- $result['list'][$key]['outstockqty'];//清库余量
                        $result['list'][$key]['doctype'] = $stocId['doctype'];//入库类型
                    }
                }
            }

        }
        return $result;
//        else{
//            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
//            $this->dbh->set_page_num($search['pageCurrent']);
//            $this->dbh->set_page_rows($search['pageSize']);
//            $result['list'] = $this->dbh->select_page($sql);
//            return $result;
//        }
    }

    /**
     * 清库管理页面
     * @param   object $dbh
     * @return  void
     */
    public function getList($search){

        $filter = array();
        if (isset($search['created_at']) && $search['created_at'] != '') {
            $filter[] = "unix_timestamp(dsc.`stockcleardate`)> unix_timestamp('".$search['created_at']." 00:00:00') ";
        }
       if (isset($search['updated_at']) && $search['updated_at'] != '') {
            $filter[] = " unix_timestamp(dsc.`stockcleardate`) < unix_timestamp('".$search['updated_at']." 00:00:00') ";
        }
        if (isset($search['customername']) && $search['customername'] != '') {
            $filter[] = " ss.`customer_sysno` = '".$search['customername']."' ";
        }
        if (isset($search['stockclearstatus']) && $search['stockclearstatus'] != '') {
            $filter[] = " dsc.`stockclearstatus` = '".$search['stockclearstatus']."' ";
        }
       //  $where =' where ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND ( (ss.`doctype`=3 AND dstd.`sysno` IS NOT NULL) OR dsid.`sysno` IS NOT NULL)  AND dsc.`sysno` IS NOT NULL ';
       //    $where =' where ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND ( (1=1) OR dsid.`sysno` IS NOT NULL)  AND dsc.`sysno` IS NOT NULL ';

        if (1 <= count($filter)) {
            $where .= " and ". implode(' AND ', $filter);
        }

        $sql="SELECT ss.sysno FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."doc_stock_clear_detail` dscd ON dscd.`stock_sysno`=ss.`sysno` AND dscd.`isdel`=0 AND dscd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_clear` dsc ON dsc.`sysno`=dscd.`stockclear_sysno` AND dsc.`isdel`=0 AND dsc.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` dsid ON dsid.`stock_sysno`=ss.`sysno` AND dsid.`isdel`=0 AND dsid.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_in` dsi ON dsi.`sysno`=dsid.`stockin_sysno` AND dsi.`isdel`=0 AND dsi.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` dstd ON dstd.`in_stock_sysno`=ss.`sysno` AND dstd.`isdel`=0 AND dstd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` dst ON dst.`sysno`=dstd.`stocktrans_sysno` AND dst.`isdel`=0 AND dst.`status`<2
                {$where} GROUP BY dsi.`stockinno`,dst.`stocktransno`";

        $sqldata=$this->dbh->select($sql);
        $result['totalRow'] = count($sqldata);

        $sql="SELECT dsc.`sysno`,dsi.stockinno,dst.stocktransno,ss.`doctype`,ss.goodsname,ss.shipname,ss.instockdate,ss.`customername`,SUM(dscd.`instockqty`) AS instockqty,SUM(dscd.`outstockqty`) AS outstockqty,SUM(dscd.`okqty`) AS okqty,dsc.`sysno`,dsc.`stockcleardate`,dsc.`stockclearstatus`,unit.unitname,dsc.stockclearno,dsc.`updated_at`
                FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."doc_stock_clear_detail` dscd ON dscd.`stock_sysno`=ss.`sysno` AND dscd.`isdel`=0 AND dscd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_clear` dsc ON dsc.`sysno`=dscd.`stockclear_sysno` AND dsc.`isdel`=0 AND dsc.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` dsid ON dsid.`stock_sysno`=ss.`sysno` AND dsid.`isdel`=0 AND dsid.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_in` dsi ON dsi.`sysno`=dsid.`stockin_sysno` AND dsi.`isdel`=0 AND dsi.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` dstd ON dstd.`in_stock_sysno`=ss.`sysno` AND dstd.`isdel`=0 AND dstd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` dst ON dst.`sysno`=dstd.`stocktrans_sysno` AND dst.`isdel`=0 AND dst.`status`<2
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                {$where} GROUP BY dsi.`stockinno`,dst.`stocktransno` ORDER BY(dsc.updated_at) DESC";
        //不带分页
        if(isset($search['page']) && $search['page'] == false){
            $result['list'] = $this->dbh->select($sql);
            return $result;
        }//带分页
        else{
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }

    }
    public function getNewList($search){

        $filter = array();
        if (isset($search['cleardate_start']) && $search['cleardate_start'] != '') {
            $filter[] = "sc.`stockcleardate`>= '".$search['cleardate_start']."'";
        }
        if (isset($search['cleardate_end']) && $search['cleardate_end'] != '') {
            $filter[] = "sc.`stockcleardate` <= '".$search['cleardate_end']."'";
        }
        if (isset($search['customername']) && $search['customername'] != '') {
            $filter[] = " sc.`customer_sysno` = '".$search['customername']."' ";
        }
        if (isset($search['stockclearstatus']) && $search['stockclearstatus'] != '') {
            $filter[] = " sc.`stockclearstatus` = '".$search['stockclearstatus']."' ";
        }
        //  $where =' where ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND ( (ss.`doctype`=3 AND dstd.`sysno` IS NOT NULL) OR dsid.`sysno` IS NOT NULL)  AND dsc.`sysno` IS NOT NULL ';
        //    $where =' where ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND ( (1=1) OR dsid.`sysno` IS NOT NULL)  AND dsc.`sysno` IS NOT NULL ';
       $where = 'and sc.isdel = 0 and sc.status = 1 ';
        if (1 <= count($filter)) {
            $where .= " and ". implode(' AND ', $filter);
        }


        $sql = "SELECT count(*) as num from ".DB_PREFIX."doc_stock_clear sc where 1=1 {$where} ";
        $sqldata=$this->dbh->select_one($sql);

        $result['totalRow'] = $sqldata;

        $sql = "SELECT sc.*,scd.instockqty,scd.outstockqty,scd.okqty,scd.stockin_sysno,scd.stockinno,sin.sysno as stockin_sysno,sin.stockintype,if(ss.doctype=3,DATE_FORMAT(ss.created_at,'%Y-%m-%d'),DATE_FORMAT(sin.stockindate,'%Y-%m-%d')) as stockindate,ss.customername,ss.shipname,g.goodsname,unit.unitname,IF(scd.instockqty = 0 , 0, 1000*(scd.instockqty-scd.outstockqty)/scd.instockqty) loss
                FROM ".DB_PREFIX."doc_stock_clear sc
                LEFT JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = scd.stock_sysno AND ss.isdel =0 AND ss.status<2 AND ss.iscurrent =1
                LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = ss.firstfrom_sysno
                LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno = ss.goods_sysno
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=g.sysno AND bga.`isdel`=0 AND bga.`status`<2
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                WHERE 1=1 {$where}
                GROUP BY sc.sysno
                ORDER BY(sc.updated_at) DESC";
      //  echo $sql;
        //不带分页
        if(isset($search['page']) && $search['page'] == false){
            $result['list'] = $this->dbh->select($sql);
        }//带分页
        else{
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            $result['list'] = $this->dbh->select_page($sql);

        }
        foreach($result['list'] as $key=> $value){
            $result['list'][$key]['loss'] = round($value['loss'],2);
            if(!$value['stockin_sysno']){
                $value['stockin_sysno'] = 0;
            }
            $sql = "SELECT stockoutdate FROM ".DB_PREFIX."doc_stock_out_detail sod
                    LEFT JOIN  ".DB_PREFIX."doc_stock_out so ON so.sysno = sod.stockout_sysno
                    where sod.stockin_sysno = {$value['stockin_sysno']}
                    ORDER BY stockoutdate DESC
                    limit 0,1" ;
            $dateperiod = $this->dbh->select_one($sql);
            if(!$dateperiod){
                $dateperiod = '--';
            }
            $result['list'][$key]['dateperiod'] =$value['stockindate']?date('Y-m-d',strtotime($value['stockindate'])).'/'.$dateperiod : '--/'.$dateperiod;
        }
        return $result;

    }
    /**
     * Title:新增入库
     */
    public function addClearstock($data,$clearstockformdata,$stockmarks,$step,$attachment)
    {
        $this->dbh->begin();
        try {
            //添加主表数据
            $res = $this->dbh->insert(DB_PREFIX.'doc_stock_clear',$data);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'添加主表失败'];
            }
            $id = $res;
            foreach ($clearstockformdata as $value) {
                $input = array(
                    'stockclear_sysno' => $id,
                    'stock_sysno'=>$value['sysno'],
                    'instockqty' => $value['instockqty'],
                    'outstockqty' => $value['outstockqty'],
                    'okqty' => $value['stockqty']-$value['tankclearqty'],
                    'stockin_sysno'=>$value['stockin_sysno'],
                    'stockinno'=>$value['stockin_no'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'tankclearqty' =>$value['tankclearqty']
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_clear_detail', $input);
//                $sql = "SELECT clearqty FROM ".DB_PREFIX."storage_stock where sysno=".$value['obj.sysno'];
//                $clearqty = $this->dbh->select($sql);
//                $up_clearqty['clearqtyqty'] = $value['tankclearqty']+$clearqty[0]['clearqty'];
//                $up_clearqty['stockqty'] = $value['stockqty']-$value['tankclearqty'];
//                $up_clearqty['isclearstock'] = 1;
//                $ress = $this->dbh->update(DB_PREFIX.'storage_stock', $up_clearqty, 'sysno=' . intval($value['obj.sysno']));
//                if (!$ress) {
//                    $this->dbh->rollback();
//                    return  ['statusCode'=>300,'msg'=>'添加详细表失败'];
//                }
//                $record_log = array(
//                    'doc_time' => '=NOW()',
//                    'shipname' => $value['shipname'],
//                    'goods_sysno' => $value['goods_sysno'],
//                    'goodsname' => $value['goodsname'],
//                    'storagetank_sysno' => $value['storagetank_sysno'],
//                    'storagetankname' => $value['storagetankname'],
//                    'customer_sysno' => $value['customer_sysno'],
//                    'customername' => $value['customername'],
//                    'beqty' => '-'.$value['tankclear'],
//                    'stockin_sysno' => $value['stockin_sysno'],
//                    'stockinno' => $value['stockinno'],
//                    'doc_sysno' => $value['stockin_sysno'],
//                    'docno' => $value['stockinno'],
//                    'doc_type' => 18,
//                    'accountstoragetank_sysno' => $value['storagetank_sysno'],
//                    'accountstoragetankname' => $value['storagetankname'],
//                    'status' => 1,
//                    'isdel' => 0,
//                    'version' => 1,
//                    'created_at' => '=NOW()',
//                    'updated_at' => '=NOW()',
//                    'stock_sysno' => $value['obj.sysno'],
//                    'ullage' => $value['tankclear'],
//                    'goodsnature' => $value['goodsnature'],
//                    'stocktype' => 1
//                 );
//                $res_log = $this->dbh->insert(DB_PREFIX.'doc_goods_record_log', $record_log);
//                if (!$res_log) {
//                    $this->dbh->rollback();
//                    return  ['statusCode'=>300,'msg'=>'添加详细表失败'];
//                }
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'添加详细表失败'];
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  11,
                'opertype'  => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $stockmarks,
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新日志失败'];
            }
            $input['opertype']=  $step-1;
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新日志失败'];
            }
            //回写附件对应转移单的id
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            if(count($attachment) > 0){
                $res = 	$A->addAttachModelSysno($id,$attachment);
                if(!$res){
                    return  ['statusCode'=>300,'msg'=>'添加附件失败'];
                }
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return  ['statusCode'=>200,'msg'=>'成功'];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /**
     * Title:编辑入库
     */
    public function updateClearstock($id, $data,$clearstockformdata,$stockmarks='',$auditstep,$clearstatus='',$abandonreason='',$attachment=null)
    {
        $this->dbh->begin();
        try {
            $stockclearno = $data['stockclearno'];
            unset($data['stockclearno']);
            if($clearstatus==6 && $auditstep==2){
                $data['stockclearstatus'] = 6;
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_clear', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新主表失败'];
            }

            if($auditstep != 4 && $auditstep != 6 && $auditstep !=7){
                $res = $this->dbh->delete(DB_PREFIX.'doc_stock_clear_detail', 'stockclear_sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'删除清库单详细失败'];
                }
            }
            // 提交保存
            if($auditstep != 4 && $auditstep != 6 && $auditstep !=7) {
                foreach ($clearstockformdata as $value) {
                    $input = array(
                        'stockclear_sysno' => $id,
                        'stock_sysno' => $value['sysno'],
                        'instockqty' => $value['stockqty'],
                        'outstockqty' => $value['outstockqty'],
                        'okqty' => $value['okqty'],
                        'stockin_sysno'=>$value['stockin_sysno'],
                        'stockinno'=>$value['stockin_no'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'tankclearqty' =>$value['tankclearqty']
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_clear_detail', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'添加主表数失败'];
                    }
                }
                //添加附件
                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                if(count($attachment)>0){
                    $res = $A->addAttachModelSysno($id,$attachment);
                    if(!$res){
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'添加附件失败'];
                    }
                }
            }

            //审核通过
            if($auditstep == 4) {
                foreach ($clearstockformdata as $v){
                    $data['stockclearstatus'] = 4;
                    $res = $this->dbh->update(DB_PREFIX.'doc_stock_clear', $data, 'sysno=' . intval($id));
                    if (!$res){
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'清库失败'];
                    }
                    $sql_qty = "SELECT clearqty from ".DB_PREFIX."storage_stock where sysno=".$v['sysno'];
                    $res_qty = $this->dbh->select($sql_qty);
                    if (($v['stockqty']-$v['tankclearqty']) < 0 ){
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'清库失败,库存不足'];
                    }
                    $input = array(
                        'stockqty' => $v['stockqty']-$v['tankclearqty'],
                        'clearqty' => $v['tankclearqty']+$res_qty[0]['clearqty'],
                        'isclearstock' => 1
                    );
                    $ress = $this->dbh->update(DB_PREFIX.'storage_stock', $input, 'sysno=' . intval($v['sysno']));
                    if (!$ress) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'清库失败'];
                    }
                    $record_log = array(
                        'doc_time' => '=NOW()',
                        'shipname' => $v['shipname'],
                        'goods_sysno' => $v['goods_sysno'],
                        'goodsname' => $v['goodsname'],
                        'storagetank_sysno' => $v['storagetank_sysno'],
                        'storagetankname' => $v['storagetankname'],
                        'customer_sysno' => $v['customer_sysno'],
                        'customername' => $v['customername'],
//                        'beqty' => '-'.$v['tankclearqty'],
                        'stockin_sysno' => $v['stockin_sysno'],
                        'stockinno' => $v['stockinno'],
                        'doc_sysno' => $id,
                        'docno' => $stockclearno,
                        'doc_type' => 18,
                        'accountstoragetank_sysno' => $v['storagetank_sysno'],
                        'accountstoragetankname' => $v['storagetankname'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'stock_sysno' => $v['sysno'],
                        'ullage' => $v['tankclearqty'],
                        'goodsnature' => $v['goodsnature'],
                        'stocktype' => 1
                     );
                    $res_log = $this->dbh->insert(DB_PREFIX.'doc_goods_record_log', $record_log);
                    if (!$res_log) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'清库失败'];
                    }
                }
              // $isout = $this->isStockOut($id);
               // print_r($isout);die;
//                if($isout!=0){
//                    return ['statusCode'=>300,'msg'=>'有未完成的出库订单'];
//                }
//              $istrank = $this->isStockTrank($id);
//                if($istrank!=0){
//                    return ['statusCode'=>300,'msg'=>'有未完成的货权转移订单'];
//                }
             //   $clearstockdata = $this->getClearstockByIds($id);

                //得到该入库单下的所有库存
              //  $clearstockdata = $this->getClearstockByMSN($id);
//                 $clearstockdata = $this->getclearInfo($id);
//                if(!$clearstockdata){
//                    $this->dbh->rollback();
//                    return ['statusCode'=>300,'msg'=>'获取库存信息失败'];
//                }
//             //   print_r($clearstockdata);die;
//                foreach ($clearstockdata as $value) {
//                    if($value['clearstockstatus']==2 && $value['isclearstock']==1){
//                        $this->dbh->rollback();
//                        return ['statusCode'=>300,'msg'=>'不能重复清库'];
//                    }
//                    //审核通过调用
//                    $C = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//                        $stockdata = array(
//                            'sysno' => $value['sysno'],
//                            'doctype' => $value['doctype'],
//                            'clearqty'=>$value['stockqty'],
//                            'stockqty' => 0,
//                            'isclearstock' => 1,
//                            'clearstockstatus'=>2,
//                            'status' => 1,
//                            'isdel' => 0,
//                            'version' => 1,
//                            'updated_at' => '=NOW()'
//                        );
//
//                        $params['type'] = 6;
//                        $params['data'] = $stockdata;
////                        var_dump($params);die();
//                        $stockid = $C->pubstockoperation($params);
//                        if (!$stockid) {
//                            $this->dbh->rollback();
//                            return ['statusCode'=>300,'msg'=>'审核清库单失败'];
//                        }
//                }
                //回写入库单是否被清空状态
//                $stockin_sysno = $clearstockdata[0]['firstfrom_sysno'];
//                $res = $this->dbh->update(DB_PREFIX.'doc_stock_in',['isclearstock'=>'1'],'sysno='.$stockin_sysno);
//                if (!$res) {
//                    $this->dbh->rollback();
//                    return ['statusCode'=>300,'msg'=>'更新入库单清库状态失败'];
//                }

            }
            //审核不通过
            if($auditstep == 6) {
                $refush = array(
                    'stockclearstatus'=>6,
                    'auditreason'  =>  $stockmarks,//审核不通过意见
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_stock_clear', $refush, 'sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                  return  ['statusCode'=>300,'msg'=>'审核不通过失败'];
                }
            }
            //作废
            if($auditstep == 7) {
                $refush = array(
                    'stockclearstatus'=>7,
                    'abandonreason'  => $abandonreason,//作废意见
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_stock_clear', $refush, 'sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'作废意见更新失败'];
                }
                //回退清库状态和余量
            //    $clearstockdata = $this->getClearstockByIds($id);
             //   $clearstockdata = $this-> getClearstockByMSN($id);
                $clearstockdata = $this->getclearInfo($id);
                foreach($clearstockdata as $key=>$item){
                    $clearqty = $item['clearqty']-$item['tankclearqty'];
                    if($clearqty == 0){
                        $isclearstock = 0;
                        $clearstockstatus = 1;
                    }else{
                        $isclearstock = 1;
                        $clearstockstatus = 2;
                    }
                    $updatevalue = array(
                        'sysno' => $item['sysno'],
                        'stockqty'=>$item['stockqty']+$item['tankclearqty'],
                        'clearqty'=>$item['clearqty']-$item['tankclearqty'],
                        'isclearstock' => $isclearstock,
                        'clearstockstatus'=>$clearstockstatus,
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock',$updatevalue,'sysno='.intval($updatevalue['sysno']));
                    $is_del_cg['isdel'] = 1;
                    $res_del = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',$is_del_cg,'doc_sysno='.$item['stockin_sysno'].' and doc_type=18');
                    if (!$res_del) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'作废清库单失败'];
                    }
                    if (!$res) {
                        $this->dbh->rollback();
                        return  ['statusCode'=>300,'msg'=>'作废清库单失败'];
                    }
                }
                //回写清库单是否被清空状态
//                $stockin_sysno = $clearstockdata[0]['firstfrom_sysno'];
//                $res = $this->dbh->update(DB_PREFIX.'doc_stock_in',['isclearstock'=>'0'],'sysno='.$stockin_sysno);
//                if (!$res) {
//                    $this->dbh->rollback();
//                    return ['statusCode'=>300,'msg'=>'更新入库单清库状态失败'];
//                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>11,
                'opertype'  => $auditstep-1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $stockmarks,
            );
            if($auditstep==1)
            {
                $input['opertype'] = 4;
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新日志失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>'成功'];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }

    }
/*
 * 查询存在的出库
 *
 * */
    public  function isStockOut($id){
        $sql = "SELECT count(*) as outNum from ".DB_PREFIX."doc_stock_clear sc
                INNER JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
                INNER JOIN  ".DB_PREFIX."doc_stock_in sin ON sin.sysno = scd.stockin_sysno and sin.stockinstatus = 4 AND sin.isdel=0 and sin.`status`<2
                INNER JOIN  ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno and ss.isclearstock=0 and ss.isdel=0 and ss.`status`<2 and ss.doctype in (1,2,4,5)
                INNER JOIN  ".DB_PREFIX."doc_stock_out_detail soutd ON soutd.stock_sysno = ss.sysno
                INNER JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno and sout.stockoutstatus in (2,3) and sout.isdel=0 and sout.`status`<2
                WHERE  sc.sysno=".$id;
        $outNum = $this->dbh->select_one($sql);
        $booksql = "SELECT count(*) as boutNum from ".DB_PREFIX."doc_stock_clear sc
                INNER JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
                INNER JOIN  ".DB_PREFIX."doc_stock_in sin ON sin.sysno = scd.stockin_sysno and sin.stockinstatus = 4 AND sin.isdel=0 and sin.`status`<2
                INNER JOIN  ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno and ss.isclearstock=0 and ss.isdel=0 and ss.`status`<2 and ss.doctype in (1,2,4,5)
                INNER JOIN  ".DB_PREFIX."doc_booking_out_detail bsoutd ON bsoutd.stock_sysno = ss.sysno and bsoutd.isdel = 0 and bsoutd.`status` <2
                INNER JOIN ".DB_PREFIX."doc_booking_out bsout ON bsout.sysno = bsoutd.bookingout_sysno and bsout.bookingoutstatus in (2,3) and bsout.isdel=0 and bsout.`status`<2
                WHERE  sc.sysno=".$id;
        $boutNum = $this->dbh->select_one($booksql);
//            $sql = "SELECT sum(ss.clockqty) as outNum from ".DB_PREFIX."doc_stock_clear sc
//            INNER JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
//            INNER JOIN  ".DB_PREFIX."doc_stock_in sin ON sin.sysno = scd.stockin_sysno and sin.stockinstatus = 4 AND sin.isdel=0 and sin.`status`<2
//            INNER JOIN  ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno and ss.isclearstock=0 and ss.isdel=0 and ss.`status`<2 and ss.doctype in (1,2,3)
//            WHERE  sc.sysno={$id} GROUP BY ss.sysno";
       return intval($outNum)+intval($boutNum);
    }

    /*
     * 查询存在的货权转移
     * */
    public function isStockTrank($id){
        $sql = "SELECT count(*) as trankNum from ".DB_PREFIX."doc_stock_clear sc
                INNER JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
                INNER JOIN  ".DB_PREFIX."doc_stock_in sin ON sin.sysno = scd.stockin_sysno and sin.stockinstatus = 4 AND sin.isdel=0 and sin.`status`<2
                INNER JOIN  ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno and ss.isclearstock=0 and ss.isdel=0 and ss.`status`<2 and ss.doctype =3
                INNER JOIN ".DB_PREFIX."doc_stock_trans_detail std ON std.in_stock_sysno =ss.sysno
                INNER JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno=std.stocktrans_sysno and st.stocktransstatus in(2,3) and st.isdel=0 and st.status<2
                WHERE  sc.sysno=".$id;
        return $this->dbh->select_one($sql);
    }


    /**
     * Title: 根据查询基本信  */
    public function getclearstockById($id){

        $sql="SELECT sysno,stockclearno,stockcleardate,stockclearstatus,customer_sysno,cs_employee_sysno,abandonreason as abandonreason FROM ".DB_PREFIX."doc_stock_clear where isdel=0 and status<2 and `sysno`=".intval($id)." ";

        return $this->dbh->select_row($sql);
    }
    /**
     * Title: 根据编号查询打印信息
     */
    public function getPrint($id){
        $sql="SELECT dsi.stockinno,ss.goodsname,ss.customername,ss.doctype,SUM(dsid.tobeqty) AS tobeqtynum,SUM(dsid.beqty) AS beqtynum,ss.`instockqty`,(ss.instockqty-ss.stockqty) AS restockqty,ss.stockqty,dst.stocktransno,dsi.stockinno,ss.goodsnature,ss.goodsqualityname,unit.unitname
                FROM `".DB_PREFIX."storage_stock` AS ss
                LEFT JOIN `".DB_PREFIX."doc_stock_clear_detail` dscd ON dscd.`stock_sysno`=ss.`sysno` AND dscd.`isdel`=0 AND dscd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_clear` dsc ON dsc.`sysno`=dscd.`stockclear_sysno` AND dsc.`isdel`=0 AND dsc.`status`<2 
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` dsid ON dsid.`stock_sysno`=ss.`sysno` AND dsid.`isdel`=0 AND dsid.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_in` dsi ON dsi.`sysno`=dsid.`stockin_sysno` AND dsi.`isdel`=0 AND dsi.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` dstd ON dstd.`in_stock_sysno`=ss.`sysno` AND dstd.`isdel`=0 AND dstd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` dst ON dst.`sysno`=dstd.`stocktrans_sysno` AND dst.`isdel`=0 AND dst.`status`<2
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                WHERE ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND dsc.`sysno`=$id
                AND ( (ss.`doctype`=3 AND dstd.`sysno` IS NOT NULL) OR dsid.`sysno` IS NOT NULL)  AND dscd.`sysno` IS NOT NULL";
        return $this->dbh->select_row($sql);
    }

    public function getNewPrint($id){

        $sql = "SELECT sc.*,scd.instockqty,scd.outstockqty,scd.okqty,scd.stockin_sysno,scd.stockinno,sin.stockintype,sin.stockindate,sin.customername,sind.tobeqty,sind.beqty,sind.shipname,g.goodsname,gq.qualityname as goodsqualityname,unit.unitname,sum(sind.takegoodsnum) as takegoodsnum
                FROM ".DB_PREFIX."doc_stock_clear sc
                LEFT  JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = scd.stockin_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sind.stockin_sysno = sin.sysno
                LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno = sind.goods_sysno
                LEFT JOIN  ".DB_PREFIX."base_goods_quality gq ON  gq.sysno = sind.goods_quality_sysno
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=g.sysno AND bga.`isdel`=0 AND bga.`status`<2
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                WHERE  sc.isdel=0 AND sc.status<2 AND sc.sysno = {$id}
                GROUP  BY sc.sysno";
        $result = $this->dbh->select_row($sql);
        $sqls = "SELECT outdate FROM ".DB_PREFIX."doc_stock_out_detail sod
                  LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno=sod.stockin_sysno
                  LEFT  JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockin_sysno = sin.sysno
                  LEFT JOIN ".DB_PREFIX."doc_stock_clear sc ON sc.sysno = scd.stockclear_sysno
                  WHERE sc.sysno = {$id}
                  ORDER BY sod.outdate DESC
                  limit 0,1";
        $outtime = $this->dbh->select_one($sqls);
        $result['outdate'] = $outtime;
        return $result;
    }


    /**
     * Title: 根据编号查询详情信息
     */
    public function getClearstockByIds($id){
       $sql="SELECT ss.sysno as stock_sysno,ss.doctype,ss.goodsname,dsid.goodsnature as dsid_goodsnature,unit.unitname,ss.`instockqty`,(ss.instockqty-ss.stockqty) AS restockqty,ss.stockqty,dsi.stockinno,ss.goodsnature,ss.clearqty,ss.firstfrom_sysno
                FROM `".DB_PREFIX."storage_stock` AS ss
                LEFT JOIN `".DB_PREFIX."doc_stock_in` dsi ON dsi.`sysno`=ss.`firstfrom_sysno` AND dsi.`isdel`=0 AND dsi.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` dsid ON dsid.`stockin_sysno`=dsi.`sysno` AND dsid.`isdel`=0 AND dsid.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_clear_detail` dscd ON dscd.`stockin_sysno`=dsi.`sysno` AND dscd.`isdel`=0 AND dscd.`status`<2
                LEFT JOIN `".DB_PREFIX."doc_stock_clear` dsc ON dsc.`sysno`=dscd.`stockclear_sysno` AND dsc.`isdel`=0 AND dsc.`status`<2
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                WHERE ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND dsc.`sysno`=$id  ";
     //   echo $sql; die;
        return $this->dbh->select($sql);
    }
    /*
     * 查询库存详情
     * */
  public function getClearstockByMSN($id){
      $sql = "select  scd.stockin_sysno from ".DB_PREFIX."doc_stock_clear sc
              LEFT JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
             WHERE sc.sysno = ".$id;
      $stocin_id = $this->dbh->select_one($sql);

      $sqls = "select ss.*,ss.sysno as stock_sysno from ".DB_PREFIX."storage_stock ss
                WHERE ss.`isdel`=0 AND ss.`status`<2 AND ss.`iscurrent`=1 AND ss.clearstockstatus = 1 AND isclearstock = 0 AND firstfrom_sysno=".$stocin_id;
      return $this->dbh->select($sqls);
  }
    /*
     * 审核作废时根据清库单查询库存信息
     *
     * */
    public  function getclearInfo($id){
        if($id){
            $sql = "select scd.tankclearqty as tankclearqty,scd.instockqty as instockqty,scd.outstockqty as outstockqty,scd.stockin_sysno as stockin_sysno,scd.stockinno as stockinno,ss.*
                    from ".DB_PREFIX."doc_stock_clear sc
                    LEFT JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno AND  scd.isdel = 0 and scd.status<2
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = scd.stock_sysno  AND  ss.isdel = 0 AND  ss.status<2
                    WHERE  ss.iscurrent = 1  AND  sc.sysno = {$id}";

            return $this->dbh->select($sql);
        }
    }


    /**
     * Title: 新根据编号查询详情信息
     */
    public function getNewClearstockByIds($id){
        $where = 'where  ss.`iscurrent`=1 AND ss.`status`<2 AND ss.`isdel`=0 ';
//,(scd.okqty+scd.tankclearqty) as stockqty
        $sql = "SELECT  scd.tankclearqty as tankclearqty,bs.storagetankname as storagetankname,scd.stockinno as stockin_no,scd.instockqty as instockqty,scd.outstockqty as outstockqty,scd.okqty AS okqty,scd.stockin_sysno as stockin_sysno,scd.stockinno as stockinno,ss.*,unit.unitname as unitname,scd.memo as memo,bga.controlproportion
                    FROM ".DB_PREFIX."doc_stock_clear sc
                    LEFT JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stockclear_sysno = sc.sysno
                    LEFT JOIN  ".DB_PREFIX."storage_stock ss ON ss.sysno=scd.stock_sysno
                     left join `".DB_PREFIX."base_storagetank` bs ON bs.sysno = ss.storagetank_sysno and bs.isdel=0 and bs.status<2
                    LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno
                    LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON st.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                    LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.goods_sysno AND bga.`isdel`=0 AND bga.`status`<2
                    LEFT JOIN `".DB_PREFIX."base_unit` AS unit ON unit.`sysno`=bga.`unit_sysno` AND bga.`isdel`=0 AND bga.`status`<2
                    {$where} AND sc.sysno= {$id}
                    GROUP  BY ss.sysno";

        return $this->dbh->select($sql);
    }
    /**
     * Title: 删除
     */
    public function delClearstock($id,$data){
        $this->dbh->begin();
        try {
            $ret = $this->dbh->update(DB_PREFIX.'doc_stock_clear', $data, 'sysno=' . intval($id));

            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno' =>  $id,
                'doctype' =>  11,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

}
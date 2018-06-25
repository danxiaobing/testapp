<?php
/**
 * 对帐单
 *
 * @author  Assion
 * @date    2016-12-06 21:07
 *
 */

class BillsModel
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

    /**
     * 查询对帐单报表
     * @author Assion
     * @time 2016-12-06 21:07
     */
    public function getBills($params)
    {
//		$sql='SELECT customername,firstcost,nowcost,discountcost,rececost,firstcost+nowcost-discountcost-rececost AS lastcost FROM (SELECT
//		customer.customername,
//		(SELECT SUM(totalprice) FROM ".DB_PREFIX."doc_finance_cost_detail WHERE coststatus IN (2,3) AND customer_sysno=customer.sysno AND costdate<='.$params['startdate'].') AS firstcost,
//		(SELECT SUM(totalprice) FROM ".DB_PREFIX."doc_finance_cost_detail WHERE coststatus IN (2,3) AND customer_sysno=customer.sysno AND costdate>'.$params['startdate'].' AND costdate<='.$params['enddate'].') AS nowcost,
//		(SELECT SUM(costdiscount) FROM ".DB_PREFIX."doc_finance_invoice WHERE invoicestatus=4 AND customer_sysno=customer.sysno AND invoicedate>'.$params['startdate'].' AND invoicedate<='.$params['enddate'].') AS discountcost,
//		(SELECT SUM(costreceivable) FROM ".DB_PREFIX."doc_finance_receivable WHERE receivablestatus=4 AND customer_sysno=customer.sysno AND receivabledate>'.$params['startdate'].' AND receivabledate<='.$params['enddate'].') AS rececost
//		FROM ".DB_PREFIX."customer customer WHERE customer.customername LIKE "%'.$params['customername'].'%") AS ghosttable';

        #author wu xianneng
        $where = '';
        if(isset($params['customer_sysno'])&&$params['customer_sysno']!=''){
            $where = "AND sysno = {$params['customer_sysno']}";
        }

        $sql = "SELECT sysno,customername,customercredit,customercredit,customerterm,remaincost,0 AS firstcost,0 AS nowcost,
                0 AS discountcost,0 AS rececost,0 AS lastcost,0 AS haveinvocost,0 AS notinvocost
                FROM ".DB_PREFIX."customer
                WHERE status = 1 AND isdel = 0 $where";
		
		$result['list']=$this->dbh->select($sql);

        if(!empty($result['list']))
        foreach($result['list'] as $key=> $value){
            #是否超期
            $daytime = date('Y-m-d',time()-(int)$value['customerterm']*30*24*3600);
            $sql = "SELECT * FROM ".DB_PREFIX."doc_finance_cost_detail
                    WHERE coststatus!= 5 AND costdate<'$daytime' AND isdel = 0 AND customer_sysno = {$value['sysno']}
                    ORDER BY costdate ASC";
            $costdata = $this->dbh->select_row($sql);
            if(!empty($costdata)){
                $result['list'][$key]['overflag'] =1;
            }else{
                $result['list'][$key]['overflag'] = 0 ;
            }

            #期初应收金额=未关闭的费用单总金额-未关闭费用单对应开票通知单已核销的金额-未关闭费用单对应开票通知单折扣总金额

            //未关闭的费用单总金额=所有未开票费用单+所有已开票未关闭费用单
            //分组求出所有客户未开票费用单
            $sql = "SELECT customer_sysno,SUM(totalprice) AS noinvoqty
                    FROM ".DB_PREFIX."doc_finance_cost_detail
                    WHERE invoice_sysno=0 AND costdate<'{$params['startdate']}' AND isdel = 0
                    GROUP BY customer_sysno";
            $notinvoqty = $this->dbh->select($sql);

            if(!empty($notinvoqty)){
                foreach($notinvoqty as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['firstcost'] = $item['noinvoqty'] ;
                        break;
                    }
                }
            }

            //分组求出所有已开票未关闭费用单
//            $sql = "SELECT fcd.customer_sysno,SUM(fcd.totalprice) AS totalprice
//                    FROM ".DB_PREFIX."doc_finance_cost_detail fcd
//                    LEFT JOIN ".DB_PREFIX."doc_finance_invoice fi ON fi.sysno = fcd.invoice_sysno
//                    WHERE fcd.invoice_sysno !=0 AND fcd.costdate<'{$params['startdate']}' AND fi.invoicestatus <7
//                    GROUP BY fcd.customer_sysno";
//            $notoffqty = $this->dbh->select($sql);
//
//            //未关闭费用单对应开票通知单已核销的金额
//            $sql = "SELECT customer_sysno,SUM(receivablecost) FROM ".DB_PREFIX."doc_finance_invoice WHERE invoicestatus<7 ";
//
//            $writeoff = $this->dbh->select($sql);

            //未收款额
            $sql = "SELECT customer_sysno,SUM(unreceivablecost) AS unreceivablecost  FROM ".DB_PREFIX."doc_finance_invoice WHERE invoicestatus<7 AND invoicedate <'{$params['startdate']}' ";

            $unreceicostdata = $this->dbh->select($sql);
            if(!empty($unreceicostdata)){
                foreach($unreceicostdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['firstcost'] = $result['list'][$key]['firstcost'] + $item['unreceivablecost'] ;
                        break;
                    }
                }
            }

            //折扣
            $sql = "SELECT customer_sysno,SUM(costdiscount) AS costdiscount FROM ".DB_PREFIX."doc_finance_invoice WHERE invoicedate < '{$params['startdate']}' ";

            $costdiscountdata = $this->dbh->select($sql);
            if(!empty($costdiscountdata)){
                foreach($costdiscountdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['firstcost'] = $result['list'][$key]['firstcost'] - $item['costdiscount'] ;
                        break;
                    }
                }
            }


            //本期发生额
            $sql = "SELECT customer_sysno,SUM(totalprice) AS nowcost
                    FROM ".DB_PREFIX."doc_finance_cost_detail
                    WHERE costdate >= '{$params['startdate']}' AND costdate <= '{$params['enddate']}' AND isdel = 0
                    GROUP BY customer_sysno ";

            $nowcostdata = $this->dbh->select($sql);
            if(!empty($nowcostdata)){
                foreach($nowcostdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['nowcost'] = $item['nowcost'] ;
                        break;
                    }
                }
            }

            //本期折扣额
            $sql = "SELECT customer_sysno,SUM(costdiscount) AS discountcost
                    FROM ".DB_PREFIX."doc_finance_invoice
                    WHERE invoicestatus = 4 OR invoicestatus = 7 AND invoicedate >= '{$params['startdate']}' AND invoicedate <= '{$params['enddate']}'
                    GROUP BY customer_sysno";
            $discountcostdata = $this->dbh->select($sql);
            if(!empty($discountcostdata)){
                foreach($discountcostdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['discountcost'] = $item['discountcost'] ;
                        break;
                    }
                }
            }

            //本期收款额
            $sql = "SELECT customer_sysno,SUM(costreceivable) AS rececost
                    FROM ".DB_PREFIX."doc_finance_receivable
                    WHERE receivablestatus = 4 AND receivabledate >= '{$params['startdate']}' AND receivabledate <= '{$params['enddate']}'
                    GROUP BY customer_sysno";

            $rececostdata = $this->dbh->select($sql);
            if(!empty($rececostdata)){
                foreach($rececostdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['rececost'] = $item['rececost'] ;
                        break;
                    }
                }
            }

            //已开发票金额
            $sql = "SELECT customer_sysno,SUM(hasinvoicecost) AS haveinvocost
                    FROM ".DB_PREFIX."doc_finance_invoice
                    WHERE invoicestatus = 4 AND isinvoice = 1 AND invoicedate >= '{$params['startdate']}' AND invoicedate <= '{$params['enddate']}'
                    GROUP BY customer_sysno ";

            $haveinvocostdata = $this->dbh->select($sql);
            if(!empty($haveinvocostdata)){
                foreach($haveinvocostdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['haveinvocost'] = $item['haveinvocost'] ;
                        break;
                    }
                }
            }

            //未开发票金额
            $sql = "SELECT customer_sysno,SUM(uninvoicecost) AS notinvocost
                    FROM ".DB_PREFIX."doc_finance_invoice
                    WHERE invoicestatus = 4 AND invoicedate >= '{$params['startdate']}' AND invoicedate <= '{$params['enddate']}'
                    GROUP BY customer_sysno ";
            $notinvocostdata = $this->dbh->select($sql);
            if(!empty($notinvocostdata)){
                foreach($notinvocostdata as $item){
                    if($item['customer_sysno']==$value['sysno']){
                        $result['list'][$key]['notinvocost'] = $item['notinvocost'] ;
                        break;
                    }
                }
            }

        }

        if(isset($params['page'])&&$params['page']==false){
            $data = $result;
        }else{
            $data['totalRow'] = count($result['list']);
            $data['totalPage'] = ceil($data['totalRow'] / $params['pageSize']);
            $list=array_chunk($result['list'],$params['pageSize'],false);
            $data['list']= $list [$params['pageCurrent']-1];
        }

		return $data;
    }

}
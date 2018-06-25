<div class="bjui-pageContent">

    <h4><strong>基本信息</strong></h4>
    <div style="border-bottom: 1px solid #ddd;">
    <table class="table table-bordered" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            filterThead:false,
            local: 'local',
            addLocation: 'last',
            dataUrl: '/introduce/reviewDetailJson/id/{{$id}}',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: false,
            linenumberAll: true,
            showTfoot:true,
            hScrollbar:true
            }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'stockindate',align:'center'}">进货日期</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'shipname',align:'center'}">进货船名</th>
            <th data-options="{name:'storagetankname',align:'center'}">进货罐号</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单量</th>
            <th data-options="{name:'beqty',align:'center'}">商检量</th>
        </tr>
        </thead>
    </table>
    </div>

    <h4><strong>提单信息</strong></h4>
    <div class="bjui-pageHeader">
        <form>
            <fieldset>
                <legend style="font-weight:normal;">高级搜索</legend>
                <div class="bjui-row col-3">
                    <input type="hidden" name="id" id="reviewDetail_id" value="{{$id}}">
                    <label class="row-label">提单类型</label>
                    <div class="row-input">
                        <select name="introductiontype" id="reviewDetail_introductiontype" data-toggle="selectpicker"  data-width="100%">
                            <option value="">全部</option>
                            <option value="1" @if($introductiontype == 1) selected @endif>可撤销</option>
                            <option value="2" @if($introductiontype == 2) selected @endif>不可撤销</option>
                        </select>
                    </div>

                    <label class="row-label">提货单号</label>
                    <div class="row-input">
                        <input type="text" name="takegoodsno" id="reviewDetail_takegoodsno" value="" placeholder="提货单号">
                    </div>

                    <label class="row-label">转出方</label>
                    <div class="row-input">
                        <select name="sale_customer_sysno" data-toggle="selectpicker" id="sale_customer_sysno" data-width="100%" data-live-search="true" data-size="10">
                            <option value="">全部</option>
                        </select>
                    </div>

                    <label class="row-label">转入方</label>
                    <div class="row-input">
                        <select name="buy_customer_sysno" data-toggle="selectpicker" id="buy_customer_sysno1" data-width="100%" data-live-search="true" data-size="10">
                            <option value="">全部</option>
                        </select>
                    </div>

                    <div class="row-input">
                        <div class="btn-group">
                            <button type="button" id="searchinstockbtn" class="btn-green" data-icon="search">开始搜索</button>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <table class="table table-bordered" id="reviewIntroduceDetail-table" data-toggle="datagrid" data-options="{
            tableWidth:'99%',
            height: '100%',
            showToolbar: false,
            dataUrl: '/introduce/introduceDetailJson/id/{{$id}}',
            paging: false,
            filterThead:false,
            addLocation:'first'
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'introductiondate',align:'center'}">创建时间</th>
            <th data-options="{name:'introductiontype',align:'center',render:function(value){if(value=='1') {return '可撤销'} else if(value=='2') {return '不可撤销'}}}">提单类型</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
            <th data-options="{name:'sale_customername',align:'center'}">转出方</th>
            <th data-options="{name:'buy_customername',align:'center'}">转入方</th>
            <th data-options="{name:'receivestart',align:'center'}">提货开始日</th>
            <th data-options="{name:'receiveend',align:'center'}">提货结束日</th>
            <th data-options="{name:'freecostdate',align:'center',render:function(value){if(value=='0') {return '--'}}}">免仓期(天)</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单数量(吨)</th>
            <th data-options="{name:'takegoodsqty',align:'center'}">实提数量(吨)</th>
            <th data-options="{name:'outqty',align:'center'}">转出数量(吨)</th>
            <th data-options="{name:'ullage',align:'center'}">损耗量(吨)</th>
            <th data-options="{name:'untakegoodsnum',align:'center'}">结存量(吨)</th>
        </tr>
        </thead>
    </table>
</div>

<script>
$(function(){
    //异步获取公司信息列表
   var gets = getCompanyMessage('customer/listAllJson','#sale_customer_sysno');
   var gets1 = getCompanyMessage('customer/listAllJson','#buy_customer_sysno1');
})
var i = 0;
$("#searchinstockbtn").click(function (){
    var takegoodsno = $("#reviewDetail_takegoodsno").val();
    var introductiontype = $("#reviewDetail_introductiontype option:selected").val();
    var id = $("#reviewDetail_id").val();
    var sale_customer_sysno = $("#sale_customer_sysno option:selected").val();
    var buy_customer_sysno = $("#buy_customer_sysno1 option:selected").val();

    BJUI.ajax('doajax', {
        url:'/introduce/introduceDetailJson/id/'+id,
        data:{takegoodsno:takegoodsno,introductiontype:introductiontype,sale_customer_sysno:sale_customer_sysno,buy_customer_sysno:buy_customer_sysno},
        loadingmask: true,
        okCallback: function (json, options) {
            $('#reviewIntroduceDetail-table').datagrid('reload',  {data:json});
        }
    });
})
function getCompanyMessage(Url,obj){

  var htm='<option value="">全部</option>';

   $(obj).empty();

    $.ajax({
        url: Url,
        dataType: 'json'
    })
    .done(function(data) {

        for (var i = data.length - 1; i >= 0; i--)
        {
            htm+="<option value="+data[i].sysno+">"+data[i].customername+"</option>";
        }

        $(obj).append(htm);
        $(obj).selectpicker('refresh');
        $(obj).selectpicker('render');

    })
    .fail(function() {
        BJUI.alertmsg('warn',"公司列表信息未成功获取，请刷新当前页面",{displayPosition:'middlecenter',displayMode:'fade'});
    });
}
</script>

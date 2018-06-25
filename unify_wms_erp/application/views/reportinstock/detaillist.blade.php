<div class="bjui-pageContent">
    <h4><strong>基本信息</strong></h4>
    <div style="border-bottom: 1px solid #ddd;">
        <table class="table table-bordered" data-toggle="datagrid" data-options="{
         tableWidth:'100%',
         filterThead:false,
         local: 'local',
         addLocation: 'last',
         dataUrl: '/report_reportinstock/baseJson/id/{{$id}}',
         dataType: 'json',
         jsonPrefix: 'obj',
         paging: false,
         linenumberAll: true,
         }">
            <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'customername',align:'center',width:280}">货主</th>
                <th data-options="{name:'shipname',align:'center',render:function(value,data){if(data.stockintype=='2'){return '槽车进货'}else if(data.stockintype=='3'){return '管输'}}}">进货船名</th>
                <th data-options="{name:'goodsname',align:'center'}">货物名称</th>
                <th data-options="{name:'stockindate',align:'center'}">进货日期</th>
                <th data-options="{name:'storagetankname',align:'center'}">进货罐号</th>
                <th data-options="{name:'takegoodsnum',align:'center',render:function(value){return parseFloat(value).toFixed(3)}}">提单量</th>
                <th data-options="{name:'instockqty',align:'center',render:function(value){return parseFloat(value).toFixed(3)}}">商检岸罐</th>
                <th data-options="{name:'ullage',align:'center',render:function(value){return parseFloat(value).toFixed(3)}}">损耗量</th>
                <th data-options="{name:'stockqty',align:'center',render:function(value){return parseFloat(value).toFixed(3)}}">结存量</th>
            </tr>
            </thead>
        </table>
    </div>

    <h4><strong>货转信息</strong></h4>
    <div style="border-bottom: 1px solid #ddd;">
        <table class="table table-bordered" data-toggle="datagrid" data-options="{
             tableWidth:'100%',
             filterThead:false,
             local: 'local',
             addLocation: 'last',
             dataUrl: '/report_reportinstock/transJson/id/{{$id}}',
             dataType: 'json',
             jsonPrefix: 'obj',
             paging: false,
             linenumberAll: true,
             showTfoot:true,
             hScrollbar:true
             }">
            <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'stocktransdate',align:'center',width:280}">日期</th>
                <th data-options="{name:'sale_customername',align:'center'}">转让方</th>
                <th data-options="{name:'buy_customername',align:'center'}">受让方</th>
                <th data-options="{name:'stocktransno',align:'center'}">转货单号</th>
                <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                <th data-options="{name:'transqty',align:'center'}">货转量</th>
                <th data-options="{name:'ullage',align:'center'}">损耗量</th>
                <th data-options="{name:'stockqty',align:'center'}">结存量</th>
            </tr>
            </thead>
        </table>
    </div>

    <h4><strong>出库信息</strong></h4>
    <div class="bjui-pageHeader">
        <form id="instockdetailform" action="/reportinstock/searchinstock" method="post">
            <fieldset>
                <legend style="font-weight:normal;">高级搜索</legend>
                <div class="bjui-row col-4">
                    <input type="hidden" id="instockdetaildata" value="">
                    <label class="row-label">车牌号</label>
                    <div class="row-input">
                        <input type="text" name="shipname" id="instockshipname" value="" placeholder="车牌号">
                    </div>

                    <label class="row-label">提单号</label>
                    <div class="row-input">
                        <input type="text" name="takegoodsno" id="instocktakegoodsno" value="" placeholder="提单号">
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <input type="text" name="customername" id="instockcustomername" value="" placeholder="客户">
                    </div>

                    <div class="row-input">
                        <div class="btn-group">
                            <button type="button" id="searchinstockbtn" class="btn-green" data-icon="search">开始搜索</button>
                        </div>
                    </div>

                    <div class="row-input">
                        <div class="btn-group">
                            <button type="button" class="btn btn-green" data-icon="sign-out" onclick="signoutdetail()"></i>EXCEL导出</button>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <table class="table table-bordered" id="reportinstockdetaillist-table" data-toggle="datagrid" data-options="{
             tableWidth:'99%',
             height: '100%',
             showToolbar: false,
             dataUrl: '/report_reportinstock/detailJson/id/{{$id}}/stockintype/{{$stockintype}}',
             paging: false,
             filterThead:false,
             addLocation:'first'
         }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'date',align:'center'}">日期</th>
            <th data-options="{name:'doctype',align:'center',render:function(value){if(value=='1') {return '船出库'}else if(value=='2') {return '车出库'} else if(value=='3') {return '管出库'}}}">类型</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'poundsoutno',align:'center'}">磅单号</th>
            <th data-options="{name:'shipname',align:'center',render:function(value){if(value=='') {return '管输'}}}">车/船号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提单号</th>
            <th data-options="{name:'beqty',align:'center',render:function(value){return parseFloat(value).toFixed(3)}}">实际提货量</th>
        </tr>
        </thead>
    </table>
</div>

<script>
    var i = 0;
    $("#searchinstockbtn").click(function (){
        var alldata;
        if(i==0){
            alldata = $("#reportinstockdetaillist-table").data('allData');
            $("#instockdetaildata").val(JSON.stringify(alldata));
            alldata = $("#instockdetaildata").val();
        }else{
            alldata = $("#instockdetaildata").val();
        }
        i=i+1;

        var shipname = $("#instockshipname").val();
        var takegoodsno = $("#instocktakegoodsno").val();
        var customername = $("#instockcustomername").val();


        BJUI.ajax('doajax', {
            url:'/report_reportinstock/searchinstock',
            data:{shipname:shipname,takegoodsno:takegoodsno,detail:alldata,customername:customername},
            loadingmask: true,
            okCallback: function (json, options) {
                $('#reportinstockdetaillist-table').datagrid('reload',  {data:json});
            }
        });
    })

    function signoutdetail(){
        BJUI.ajax('ajaxdownload', {
            url:'/report_reportinstock/exceldetail/',
            type:'POST',
            data:{id:{{$id}}},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>

<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" id="treedata" name="treedata">
        <input type="hidden" name="parentId" id="parentId">

        <div class="bjui-row col-1">

            <label class="row-label">费用项:</label>
            <div class="row-input required">
                <input type="hidden" name="sysno">
                <input type="text" name="othercostname" readonly value=""
                       data-rule="required" data-toggle="findgrid" data-options="{
                        dialogOptions: {width:'800',height:'500',title:'其他费用',maxable:true,resizable:true,mask:true},
                        empty:false,
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:7},
                            dataUrl: '/othercost/listjsonforcon',
                            columns: [
                                {name:'sysno', label:'id',hide:true},
                                {name:'othercostname', label:'其他费用名称'},
                                {name:'unitname',label:'计量单位'},
                                {name:'othercostprice',label:'费用价格'}
                            ],
                            showLinenumber:false
                        },
                    }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">计量单位</label>
            <div class="row-input">
                <input type="text" id="unitname" name="unitname" value="">
            </div>

            <label class="row-label">费用</label>
            <div class="row-input required">
                <input type="text" id="othercostprice" name="othercostprice" value="" style="width:70%;">         <span>元</span>
            </div>

            <label class="row-label">开票公司</label>
            <div class="row-input">
                <select id="company_sysno" name="invoice_company_sysno"  data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
                    <option value="">请选择</option>
                    @foreach($companylist['list'] as $item)
                        <option value="{{$item['sysno']}}" @if($item['sysno']==$invoice_company_sysno) selected @elseif($item['isdefault'] == 1) selected @endif>{{$item['companyname']}}</option>
                    @endforeach
                </select>
                <input type="hidden" id="companyname" name="companyname" value="">
            </div>

            <label class="row-label">备注</label>

            <div class="row-input">
                <textarea name="othercostmarks"></textarea>
            </div>

        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
     <li>
            <button type="button" class="btn-green" data-icon="save" onclick="saveothercost()">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>

<script>
    function saveothercost(){
        $('#treeform').isValid(function(v){
            if(v){
                var data  = $("#treeform").serializeJson();
                var allData  = $.CurrentNavtab.find("#othercost-detail-table").data('allData');


                var companyname = $("#company_sysno option:selected").text();

                data.companyname = companyname;

                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }
                $.CurrentNavtab.find('#othercost-detail-table').datagrid('reload',  {data:allData});

                BJUI.dialog('closeCurrent');
            }
        })
    }
</script>

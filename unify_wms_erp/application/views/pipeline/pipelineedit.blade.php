<div class="bjui-pageContent">
    <div class="bs-example">
        <form  id="edit_pipeline" action="{{$action}}"  class="datagrid-edit-form"  data-data-type="json" >
        <input type="hidden" name="pipeline_id" value="{{$id}}">
            <div class="bjui-row col-2">

                <label class="row-label">管线号</label>
                <div class="row-input required">
				
				<input type="text" name="pipelinename" value="{{$pipelinename}}" placeholder="管线号" data-rule="required">

                </div>

                <label class="row-label">管线类型</label>
                <div class="row-input required">
                
                    <select name="pipelinetype" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required">
                        <option value="" >请选择</option>
                        <option value="1" @if($pipelinetype==1) selected @endif>码头管线</option>
                        <option value="2" @if($pipelinetype==2) selected @endif>库区管线</option>
                    </select>
               
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input required">
                    <input type="hidden" id="pipeline_base_goods_sysno" name="goods_sysno" value="{{$goods_sysno}}">
                    <input type="text" id="goodsname" name="goodsname" value="{{$goodsname}}" data-rule="required" data-toggle="findgrid" readonly data-options="{
                            dialogOptions: {width:'1200',height:'600',title:'货品详情',resizable:true,mask:true},
                            gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'96%',
                            local: 'local',
                            paging: {pageSize:20},
                            data: {{$goodslist}} ,
                            columns: [
                                {name:'goods_sysno',hide:true, label:'货品ID'},
                                {name:'goodsname', label:'货品名称'},
                                {name:'density', label:'货品密度'},
                                {name:'controlprice', label:'控货单价'},
                                {name:'controlproportion', label:'控货比重‰'},
                                {name:'rate_waste', label:'内控损耗‰'},
                                {name:'unitname', label:'计量单位'},
                            ],
                            fullGrid:true,
                            showLinenumber:true
                        },
                    }" placeholder="点放大镜按钮查找">
                </div>

                <label class="row-label">管线材质</label>
                <div class="row-input ">
                    <input type="text" name="pipelinecategory" value="{{$pipelinecategory}}" placeholder="管线材质">
                </div>
        

                <label class="row-label">是否保温</label>
                <div class="row-input required">
                    <select name="iswarm" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required">
                        <option value="" selected>请选择</option>
                        <option value="1" @if($iswarm==1) selected @endif>是</option>
                        <option value="0" @if($iswarm==0) selected @endif>否</option>
                    </select>
                </div>


                <label class="row-label">管内径(MM)</label>
                <div class="row-input ">
                    <input type="text" name="caliber" value="{{$caliber}}" placeholder="管内径(MM)">
                </div>


                <label class="row-label">流量/分钟</label>
                <div class="row-input required">
               		<input type="text" name="pipelineflow" value="{{$pipelineflow}}" placeholder="流量/分钟" data-rule="required">
                </div>


                <label class="row-label">安装时间</label>
                <div class="row-input required">
               		<input type="text" name="installtime" value="{{$installtime or date('Y-m-d')}}" data-toggle="datepicker" data-rule="date;required">
                </div>

                <label class="row-label">管线状态</label>
                <div class="row-input required">
               		<input type="radio" value="1" name="status" data-toggle="icheck"  data-label="启用" @if($status==1) checked @else checked @endif>
               		<input type="radio" value="2" name="status" data-toggle="icheck"  data-label="停用" @if($status==2) checked @endif>
                </div>

            </div>
           

            
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
     <li><button type="button" class="btn-green" data-icon="save" onclick="submit()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
       
    </ul>
</div>


<script type="text/javascript">
function submit()
{
	BJUI.ajax('ajaxform',{
	    url: "{{$action}}",
	    form: $.CurrentDialog.find('#edit_pipeline'),
	    type: 'POST',
	    validate: true,
	    loadingmask: true,
	    okCallback: function(json, options) {
	    	BJUI.navtab('refresh', 'menu486');
	        BJUI.dialog('closeCurrent','');
	    }
	});
}
</script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="goodsattributeform" action="{{$action}}" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" name="id" value="{{$attribute['list'][0]['sysno']}}">
            <input type="hidden" name="parentId" id="parentId" value="{{$attribute['list'][0]['goods_sysno']}}">

            <div class="bjui-row col-2">
                <label class="row-label">货品编号</label>
                <div class="row-input required">
                    <input type="text" id="goodsno" name="goodsno" value="{{$attribute['list'][0]['goodsno']}}" readonly>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input required">
                    <input type="text" name="parent_sysno" id="j_ztree_menus2" data-toggle="selectztree" data-tree="#j_select_tree2" readonly placeholder="点击此处选择货品"
                           value="{{$attribute['list'][0]['goodsname']}}" data-rule="required;" data-width="16%" @if($attribute['list'][0]['unit_sysno']) disabled @endif>
                    <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true" data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck" data-on-click="S_NodeClicks" >
                        @foreach($goodsinfo as $info)
                        <li data-id="{{$info['sysno']}}" data-pid="{{$info['parent_sysno']}}" data-no="{{$info['goodsno']}}" @if($info['sysno'] == $attribute['list'][0]['goods_sysno'] ) data-checked='true' disabled @endif >{{$info['goodsname']}}</li>
                        @endforeach
                    </ul>
                </div>

                <label class="row-label">货品密度</label>
                <div class="row-input required">
                    <input type="text" name="density" value="{{$attribute['list'][0]['density']}}" data-rule="required number range[0~]">
                </div>

                <label class="row-label">控货单价</label>
                <div class="row-input required">
                    <input type="text" name="controlprice" value="{{$attribute['list'][0]['controlprice']}}" data-rule="required number range[0~]">
                </div>

                <label class="row-label">控货比重</label>
                <div class="row-input required">
                    <input type="text" name="controlproportion" value="{{$attribute['list'][0]['controlproportion']}}"  data-rule="required number range[0~]">
                </div>

                <label class="row-label">内控损耗率‰</label>
                <div class="row-input required">
                    <input type="text" name="rate_waste" value="{{$attribute['list'][0]['rate_waste']}}" data-rule="required number range[0~]">
                </div>

                <label class="row-label">计量单位</label>
                <div class="row-input required">
                    <select id="unit_sysno" name="unit_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" disabled >
                        @foreach($unit as $units)
                            <option value="{{$units['sysno']}}" @if($units['unitname'] == '吨') selected @endif >{{$units['unitname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">不能存放的材质</label>
                <div class="row-input">
                    <input type="hidden" id="storagetank_sysno" name="storagetank_sysno" value="">
                    <select name="" data-toggle="selectpicker" data-width="100%" multiple="">
                        <option value="0">请选择</option>
                        @if($id)
                        @foreach($storagetankcategory as $item)
                                <option value="{{$item['sysno']}}"
                                        @foreach($storagetank_sysnoarr as $item2)
                                        @if($item2==$item['sysno']) selected
                                        @endif
                                        @endforeach
                                >{{$item['storagetank_categoryname']}}</option>
                            @endforeach
                        @else
                            @foreach($storagetankcategory as $item)
                                <option value="{{$item['sysno']}}">{{$item['storagetank_categoryname']}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <label class="row-label">是否长期品种</label>
                <div class="row-input required">
                    <input type="radio" name="islongterm"  data-toggle="icheck" value="1" data-rule="checked" data-label="是&nbsp;&nbsp;" @if( !$attribute['list'][0]['islongterm'] || $attribute['list'][0]['islongterm'] ==1) checked @endif>
                    <input type="radio" name="islongterm"  data-toggle="icheck" value="0" data-label="否" @if($attribute['list'][0]['islongterm'] == 0) checked @endif>
                </div>

                <label class="row-label">状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-label="启用&nbsp;&nbsp;" @if( !$attribute['list'][0]['status'] || $attribute['list'][0]['status'] ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($attribute['list'][0]['status'] ==2) checked @endif>
                </div>


                <label class="row-label">是否易制毒</label>
                <div class="row-input required">
                    <input type="radio" name="isdrugs"  data-toggle="icheck" value="1" data-rule="checked" data-label="是&nbsp;&nbsp;" @if( !$attribute['list'][0]['isdrugs'] || $attribute['list'][0]['isdrugs'] ==1) checked @endif>
                    <input type="radio" name="isdrugs"  data-toggle="icheck" value="0" data-label="否" @if($attribute['list'][0]['isdrugs'] == 0) checked @endif>
                </div>
            </div>

                <!-- 临时解决end -->
                    <fieldset class="goodsfieldset" id="goodsfieldset">
                        {{--  <legend>上传货权转移单<span class='red'>*</span></legend>--}}
                        <legend>上传货品单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'attributelist',action:'attach-1'},
                            required: false,
                            required: false,
                            uploaded: '{{ $attach1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                            }
                        }"
                        >
                    </fieldset>
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button id="goodsattributesubmit" type="button" class="btn-green" data-icon="save">保存</button></li>
    </ul>
</div>
<script src="/static/common/js/custom.js"></script>
<script type="text/javascript">
    //单击事件
    function S_NodeClicks(event, treeId, treeNode) {
        var zTree = $.fn.zTree.getZTreeObj(treeId)

        zTree.checkNode(treeNode, !treeNode.checked, true, true)

        event.preventDefault();
        $("#goodsno").val(treeNode.no);
    }
    
    $.CurrentDialog.find("#goodsattributesubmit").click(function() {
        $.CurrentDialog.find('#unit_sysno').removeAttr("disabled");
        var storagetank_sysno = $.CurrentDialog.find(".filter-option").eq(1).text();
        $("#storagetank_sysno").val(storagetank_sysno);

        $.CurrentDialog.find("#j_ztree_menus2").attr("data-rule","required");

        var isdrugs = $('input:radio[name=isdrugs]:checked').val();

        if(isdrugs==1) {
            //提交验证附件必须上传
            var attachnum = 0;
            $.CurrentDialog.find('#goodsfieldset').each(function(){
                if($(this).find(".filelist > li").length > 0)
                {
                    attachnum = attachnum +1;
                }
            });

            if(attachnum<1  )
            {
                BJUI.alertmsg('warn', '请先上传附件再提交表单',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
        }

        }


        BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentDialog.find('#goodsattributeform'),
                validate: true,
                loadingmask: true,
                okCallback:function (json, options) {
                    BJUI.navtab('refresh','navab350');
                    BJUI.dialog('closeCurrent', '');
            }
        });
    });
</script>

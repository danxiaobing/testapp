<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="pendcarin_edit"  method="POST" class="pendcarin-edit-form"   data-data-type="json" >
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="poundid" value="{{$poundid}}">
            <!--base message start-->
            <fieldset>
                <legend>基本信息</legend>
                <br>

                <div class="bjui-row col-3">
                    <label class="row-label">磅码单号</label>

                    <div class="row-input">
                        <input type="text" name="poundsinno"
                               value="@if($detail['poundsinno']){{$detail['poundsinno']}}@else{{系统自动生成}}@endif" readonly>
                    </div>
                    <label class="row-label">选择地磅</label>

                    <div class="row-input required">
                        <input type="radio" name="loadometer" value="50T" data-toggle="icheck"  data-label="50T" @if($detail['loadometer']=='50T') checked @endif>
                        <input type="radio" name="loadometer" value="80T" checked data-toggle="icheck"  data-label="80T" @if($detail['loadometer']=='80T') checked @endif>
                        <input type="radio" name="loadometer" value="100T" data-toggle="icheck"  data-label="100T" @if($detail['loadometer']=='100T') checked @endif>
                    </div>

                    <label class="row-label">单据状态</label>

                    <div class="row-input ">
                        <select name="stockinstatus" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled="disabled">
                         	<option value="1" selected >新增</option>
                            <option value="2" @if($poundsinstatus == 2) selected @endif>核单完成</option>
                            <option value="3" @if($poundsinstatus == 3) selected @endif>重车过磅</option>
                            <option value="4" @if($poundsinstatus == 4) selected @endif>空车过磅</option>
                            <option value="5" @if($poundsinstatus == 5) selected @endif >作废</option>
                        </select>
                    </div>
                    <label class="row-label">进货罐号</label>
                    	<input type="hidden" id="storagetankname_pendcar" name="storagetankname_pendcar" value="哈哈">
                    <div class="row-input required">
                    	<select name="storagetank_sysno_pendcar" id="storagetank_sysno_pendcar" data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                        @if($detail)
                            @foreach($storagetankinfo as $item)
                                <option value="{{ $item['storagetank_sysno'] }}" @if($item['storagetank_sysno']==$detail['storagetank_sysno']) selected="" @endif >{{ $item['storagetankname'] }}</option>
                            @endforeach
                        @else if
                        @foreach($storagetankinfo as $item)
                    		<option value="{{ $item['storagetank_sysno'] }}" @if($item['storagetank_sysno']==$list['storagetank_sysno']) selected="" @endif >{{ $item['storagetankname'] }}</option>
                        @endforeach
                        @endif
                    	</select>
                    </div>

                    <label class="row-label">鹤位号</label>

                    <div class="row-input ">
                        <input type="text" name="cranename" value="{{ $detail['cranename'] }}"  >
                    </div>


                    <label class="row-label">车牌号</label>

                    <div class="row-input ">
                    	<input type="text" name="carid" value="{{ $list['carid'] }}" readonly="readonly" >
                    </div>

                    <label class="row-label">司机名称</label>

                    <div class="row-input">
                    	<input type="text" name="carname" value="{{ $list['carname'] }}" readonly="readonly">
                    </div>

                    <label class="row-label">手机号码</label>

                    <div class="row-input ">
                        <input type="text" name="mobilephone" value="{{$list['mobilephone']}}" readonly="readonly">
                    </div>

                    <label class="row-label">身份证号</label>

                    <div class="row-input ">
                        <input type="text" name="idcard"  value="{{ $list['idcard'] }}" readonly="readonly">
                    </div>

                <hr>
            	<label class="row-label">客户</label>

            	<div class="row-input ">
                    <input type="text"  name="customername"    value="{{ $list['customername'] }}" readonly="readonly">
            		<input type="hidden" name="customer_sysno" value="{{ $list['customer_sysno'] }}">
            	</div>

            	<label class="row-label ">送货公司</label>

            	<div class="row-input ">
            		<input type="text" name="deliverycompany" value="{{ $detail['deliverycompany'] }}" >
            	</div>

            	<label class="row-label ">预卸数量(kg)</label>

            	<div class="row-input required">
            		<input type="text" name="unloadnumber" id="unloadnumber" value="{{ intval($detail['unloadnumber']) }}" data-rule="required;range[0~]">
            	</div>


            	<label class="row-label">品名</label>

            	<div class="row-input ">
            		<input type="text" name="goodsname" value="{{ $list['goodsname'] }}" readonly="readonly">
                    <input type="hidden" name="goods_sysno" value="{{ $list['goods_sysno'] }}">
            	</div>          
            	  	
            	<label class="row-label">入库订单号</label>
                    <input type="hidden" name="stockin_sysno" value="{{ $list['stockin_sysno'] }}">
            		<input type="hidden" name="in_detail_sysno" value="{{ $list['in_detail_sysno'] }}">
            	<div class="row-input ">
            		<input type="text" name="stockinno" value="{{ $list['stockinno'] }}" readonly="readonly">
            	</div>

                <label class="row-label">卸货单号</label>
                <div class="row-input ">
                    <input type="text" name="takegoodsno" value="{{ $list['takegoodsno'] }}" readonly="readonly">
                </div>
                
                <label class="row-label">是否排队</label>
                <div class="row-input ">
                    <input type="radio" name="isqueue" value="1" data-toggle="icheck"  data-label="是" checked>
                    <input type="radio" name="isqueue" value="2" data-toggle="icheck"  data-label="否">
                </div>

                <label class="row-label">司磅员</label>
                <div class="row-input required">
                    <select name="create_username" data-size="5"
                            data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                        @foreach($customerlist as $v)
                        <option value="{{$v['realname']}}" @if($v['realname'] == $detail['create_username']) selected @endif>{{$v['realname']}}</option>
                        @endforeach
                    </select>
                </div>
                <br/>
                <label class="row-label">备 注</label>
                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$list['memo'] or ''}}</textarea>
                </div>

            	</div>
            </fieldset>
            {{--<label class="row-label">卸货单号</label>--}}
            {{--<div class="row-input ">--}}
                {{--<input type="text" name="takegoodsno" value="{{ $list['takegoodsno'] }}" readonly="readonly">--}}
            {{--</div>--}}

            @if($isqualitycheck==1)
            <!-- 品质检查单start -->

            <div class="remarks">
                <fieldset>
                    <legend>品质检查明细</legend>
                    <table class="table table-bordered" id="stockshipin-qualitycheck-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar:false,
                        local: 'local',
                        data: '{{$qualitycheck}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">品种</th>
                            <th data-options="{name:'checktime',align:'center'}">品质检查时间</th>
                            <th data-options="{name:'ischecked',align:'center',render:function(value){switch(value) { case '1': return '是'; case '2':return '否'; default: return '';  }}}">是否合格</th>
                            <th data-options="{name:'isskip',align:'center',render:function(value){switch(value) { case '0': return '不用让步'; case '1': return '是'; case '2':return '否'; default: return '--';  }}}">是否让步</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>

            <!-- 品质检查单end -->
            @endif

            <div class="comuser-add-center" id='pendcarin_release'>
                 <fieldset class="customerfieldset" >
                   <legend>上传上家出库磅码单</legend>
                     <input type="file"  data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'pendcarin',action:'pendcarin_release',doc_sysno:'{{$id}}'},
                            required: false,
                            uploaded: '{{ $uploaded1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf,txt',
                                mimeTypes: '.jpg,.png,.pdf,.txt'
                            }
                        }"
                >
                </fieldset>   
            </div>

            <br><br>
            <div class="text-center btns-user">
            @if($type)
                <button id="pendcarin_edit1" type="button" class="btn btn-green btn-lg">保存</button>
            @else
                <button id="pendcarin_success" type="button" class="btn btn-green btn-lg">核单完成</button>
            @endif
                <button id="pendcarin_look" type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
            </div>
            <br><br>

            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">

                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
   //----------------------操作记录 
    addLog($.CurrentNavtab.find('.addTable'), {{$id or 0}}, 7);
    //----------------------操作记录 end
    var sourcestoragetank_sysno = '{{$sourcestoragetank_sysno}}';

    var sure_storagetank_sysno = "{{$storagetank_arr}}";
    var storagetank_sysno_array = new Array;
    $('#pendcarin_success').click(function () {

        if($(":input[name='loadometer']:checked").size()<1){

            BJUI.alertmsg('warn','<h4>请选择地磅!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        var storagetankname_pendcar = $('#storagetank_sysno_pendcar option:selected').text();
        var beqty = $('#unloadnumber').val();
        if(beqty==''){
            BJUI.alertmsg('warn','<h4>请填写预提数量!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        $('#storagetankname_pendcar').attr('value',storagetankname_pendcar);

        // console.log($('#storagetankname').val());return;
        var canstore = '';
        var storagetank_sysno_pendcar = $('#storagetank_sysno_pendcar').val();
        $.ajax({
            url:'/pendcarin/AjaxgetCanstore',
            type:'POST',
            async:false,
            data:{storagetank_sysno:storagetank_sysno_pendcar},
            success:function(data){
                canstore = data;
            }
        });
        // console.log(canstore<(beqty/1000));
        // return false;
        //


    storagetank_sysno_array = sure_storagetank_sysno.split(",");
    if($.inArray(storagetank_sysno_pendcar, storagetank_sysno_array) == -1)
    {
        BJUI.alertmsg('confirm', '进货罐号与预约单的不一致，确定要继续吗?', {
            okCall: function() {
                //
            if(canstore<(beqty/1000)){
                    BJUI.alertmsg('confirm', '当前储罐容量不足，是否继续？', {
                        okCall: function() {
                            poundsinSubmit();      
                        }
                    });
                }else{
                    poundsinSubmit();              
                }
            }
        })
    }else{
        if(canstore<(beqty/1000)){
            BJUI.alertmsg('confirm', '当前储罐容量不足，是否继续？', {
                okCall: function() {
                    poundsinSubmit();      
                }
            });
        }else{
            poundsinSubmit();             
        }
        
    }
        

    });
    

    function poundsinSubmit()
    {
        BJUI.ajax('ajaxform', {
            url: '/pendcarin/poundsin',
            form:$.CurrentNavtab.find('#pendcarin_edit'),
            validate:true,
            okCallback:function(json, options){
                BJUI.navtab('reloadFlag', 'navab442,navab447');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }


    $('#pendcarin_edit1').click(function(){
        if($(':radio:checked').size()<1){

            BJUI.alertmsg('warn','<h4>请选择地磅!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var storagetankname_pendcar = $('#storagetank_sysno_pendcar option:selected').text();
        $('#storagetankname_pendcar').attr('value',storagetankname_pendcar);
        var beqty = $('#unloadnumber').val();
        if(beqty==''){
            BJUI.alertmsg('warn','<h4>请填写预提数量!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxform', {
            url: '/pendcarin/poundsdetailedit',
            form:$.CurrentNavtab.find('#pendcarin_edit'),
            validate:true,
            okCallback:function(json, options){
                BJUI.navtab('reloadFlag', 'navab442,navab447');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    });

</script>
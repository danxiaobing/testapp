<div class="bjui-pageContent">

    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
      <!--base message start-->

          <form id="customerform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">

            <input type="hidden" name="id" value="{{$id}}">

            <fieldset>
                <legend>基本信息</legend>
                <div class="bjui-row col-3">
                    <label class="row-label">客户编码</label>
                    <div class="row-input">
                        <input type="text" name="customerno" value="@if($customerno){{$customerno}}@else{{系统自动生成}}@endif" readonly>
                    </div>
                    <label class="row-label">客户名称</label>
                    <div class="row-input required">
                        <input type="text" name="customername" value="{{$customername}}" data-rule="required" @if($views=='look') readonly @endif>
                    </div>
                     <label class="row-label">客户简称</label>
                    <div class="row-input required">
                        <input type="text" name="customerabbreviation" value="{{$customerabbreviation}}" data-rule="required" @if($views=='look') readonly @endif>
                    </div>
                     <label class="row-label">来源渠道</label>
                    <div class="row-input required">
                        <select name="customerchannel" data-toggle="selectpicker" data-rule="required" data-width="100%" @if($views=='look') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerchannellist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $customerchannel) selected @endif>{{$item['channelname']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <label class="row-label">客户分类</label>
                    <div class="row-input required">
                        <select name="customercategory_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                            <option value="">请选择</option>
                            @foreach($customercategorylist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $customercategory_sysno) selected @endif>{{$item['categoryname']}}</option>
                            @endforeach
                        </select>
                    </div> -->
                     <label class="row-label">客户性质</label>
                    <div class="row-input required">
                        <select name="customerclass" data-toggle="selectpicker" data-rule="required" data-width="100%" @if($views=='look') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerclasslist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $customerclass) selected @endif>{{$item['classname']}}</option>
                            @endforeach
                        </select>
                    </div>
                     <label class="row-label">关联客户</label>
                    <div class="row-input">
                        <input type="checkbox" name="customerrelation" id="customerrelation" value="1" data-toggle="icheck" @if($views=='look') disabled @endif data-label="" @if($customerrelation == 1) checked @endif>
                    </div>

                      <label class="row-label">停用</label>
                    <div class="row-input">
                        <input type="checkbox" name="status" id="status" value="2" data-toggle="icheck" data-label="" @if($views=='look') disabled @endif @if($status == 2) checked @endif>
                    </div>

                    <label class="row-label">成交</label>
                    <div class="row-input">
                        <input type="checkbox" name="customerdeal" id="customerdeal" value="1" data-toggle="icheck" data-label="" @if($views=='look') disabled @endif @if($customerdeal == 1) checked @endif>
                    </div>
                    <label class="row-label">传真</label>
                    <div class="row-input ">
                        <input type="text" name="customerfax" value="{{$customerfax}}" @if($views=='look') readonly @endif>
                    </div>
                     <label class="row-label">信用额度</label>
                    <div class="row-input required">
                        <input type="text" name="customercredit" value="{{$customercredit}}" data-rule="required;number;range[0~]" @if($views=='look') readonly @endif>
                    </div>

                    <label class="row-label">信用期限(月)</label>
                    <div class="row-input required">
                        <input type="text" name="customerterm" value="{{$customerterm}}" data-rule="required;digits" @if($views=='look') readonly @endif>
                    </div>
                     <label class="row-label">分管业务员</label>
                    <div class="row-input required">
                        <select name="business_user_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" @if($views=='look') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $business_user_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                    </div>
                     <label class="row-label">创建人</label>
                    <div class="row-input required">
                        <input type="hidden" name="created_user_sysno" value="@if(!$created_user_sysno){{$user['employee_sysno']}}@else{{$created_user_sysno}}@endif">
                        <input type="text" name="employeename" value="@if(!$created_user_sysno){{$user['employeename']}}@else{{$employeename}}@endif" readonly>
                    </div>
                    <label class="row-label">创建日期</label>
                    <div class="row-input">
                        <input type="text" name="created_at" value="@if($created_at){{date('Y-m-d',strtotime($created_at))}}@else{{date('Y-m-d')}}@endif" readonly>
                    </div>
                </div>
            </fieldset>

            <!--regist start-->

            <fieldset>
                <legend>注册信息</legend>

                 <div class="bjui-row col-3">

                    <label class="row-label">法人代表</label>
                    <div class="row-input required">
                        <input type="text" name="customerrepresentative" value="{{$customerrepresentative}}" data-rule="required" @if($views=='look') readonly @endif>
                    </div>

                    <label class="row-label">开户银行网点</label>
                    <div class="row-input ">
                        <input type="text" name="customerbank" value="{{$customerbank}}" @if($views=='look') readonly @endif>
                    </div>

                    <label class="row-label">开户账号</label>
                    <div class="row-input ">
                        <input type="text" name="customeraccount" value="{{$customeraccount}}" @if($views=='look') readonly @endif>
                    </div>
                     <label class="row-label">三证合一</label>
                    <div class="row-input">
                        <input type="radio" name="customercreditcodechecked" @if($views=='look') disabled @endif  data-toggle="icheck" value="1" data-label="是" @if(!$customercreditcodechecked || $customercreditcodechecked ==1) checked @endif>
                        <input type="radio" name="customercreditcodechecked" @if($views=='look') disabled @endif  data-toggle="icheck" value="2" data-label="否"
                        @if($customercreditcodechecked ==2) checked @endif>
                    </div>

                     <label class="row-label">客户地址</label>
                     <div class="row-input required">
                        <input type="text" name="customeraddress" value="{{$customeraddress}}" data-rule="required" @if($views=='look') readonly @endif>
                    </div>

                    <label class="row-label">电话</label>
                    <div class="row-input ">
                        <input type="text" name="customertelephone" value="{{$customertelephone}}" @if($views=='look') readonly @endif>
                    </div>

                    <span class="ischeckview1">
                    <label class="row-label">社会信用代码</label>
                    <div class="row-input required">
                        <input type="text" id="customercreditcode" name="customercreditcode" value="{{$customercreditcode}}" 
                        data-rule="required" @if($views=='look') readonly @endif></div>
                    </span>


                    <span class="ischeckview2">
                        <label class="row-label">营业执照</label>
                        <div class="row-input required">
                           <input type="text" id="customerlicense" name="customerlicense" value="{{$customerlicense}}" 
                            data-rule="required" @if($views=='look') readonly @endif>
                        </div>
                        <label class="row-label">组织代码</label>
                        <div class="row-input required">
                           <input type="text" id="customerorganizationcode" name="customerorganizationcode" 
                            value="{{$customerorganizationcode}}" data-rule="required" @if($views=='look') readonly @endif>
                        </div>
                        <label class="row-label">纳税识别号</label>
                        <div class="row-input required">
                            <input type="text" id="customertaxid" name="customertaxid" value="{{$customertaxid}}" data-rule="required" @if($views=='look') readonly @endif>
                        </div>
                     </span>
                </div>
            </fieldset>
            <!--regist end-->


              <!--people start-->
                 <div class="remarks">
                    <fieldset>
                      <legend>业务联系人</legend>
                        <div class="table-edit">
                            <input type="hidden" id="contactsdata" name="contactsdata">
                                <table style="height:100px" class="table table-bordered" id="contacttable" data-toggle="datagrid" 
                                data-options="{
                                                    gridTitle : '',
                                                    filterThead:false,
                                                    showToolbar: true,
                                                    @if($views!='look')
                                                    toolbarItem: 'add,|,edit,|,save,|,cancel,|,del',
                                                    @endif
                                                    local: 'local',
                                                    addLocation: 'last',
                                                    dataUrl: '/customer/contactslistJson/id/{{$id}}',
                                                    editMode:'inline',
                                                    editUrl: '/index/ajaxDone',
                                                    paging: false,
                                                    linenumberAll: true,
                                                    fullGrid:true
                                                }">
                                <thead>
                                    <tr data-options="{name:'sysno'}">
                                        <th data-options="{name:'contactsname',rule:'required',align:'center'}">姓名(必填)</th>
                                        <th data-options="{name:'contactsposition',align:'center'}">职位</th>
                                        <th data-options="{name:'contactsmobilephone',align:'center'}">手机</th>
                                        <th data-options="{name:'contactsemail',align:'center'}">Email</th>
                                        <th data-options="{name:'contactstelephone',align:'center'}">座机</th>
                                        <th data-options="{name:'contactsmarks',align:'center'}">备注</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </fieldset>
                </div>
              <!--people end-->
              
              <div class="clear-curstom"></div>

              <!--project start-->
              <div class="remarks">
                            <fieldset>
                                <legend>经营品种</legend>
                                <div class="table-edit">
                                    <input type="hidden" id="goodsdata" name="goodsdata">
                                    <table class="table table-bordered" id="customergoods-selected-table" data-toggle="datagrid" data-options="{
                                    gridTitle : '',
                                    filterThead:false,
                                    showToolbar: true,
                                    toolbarCustom:$.CurrentNavtab.find('#custom_customer_tb'),
                                    local: 'local',
                                    addLocation: 'last',
                                    data: {{$customerGoods}},
                                    paging: false,
                                    linenumberAll: true,
                                    fullGrid:true
                                }">
                                <thead>
                                    <tr data-options="{name:'sysno'}">
                                        <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                                        <th data-options="{name:'desc',align:'center'}">备注</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </fieldset>
                </div>
              <!--project end-->

              <div class="clear-curstom"></div>

              <!--people start-->
                 <div class="remarks">
                    <fieldset>
                      <legend>客户开票抬头</legend>
                        <div class="table-edit">
                            <input type="hidden" id="companydata" name="companydata">
                                <table style="height:100px" class="table table-bordered" id="customercompanytable" data-toggle="datagrid" 
                                data-options="{
                                                    gridTitle : '',
                                                    filterThead:false,
                                                    showToolbar: true,
                                                    @if($views!='look')
                                                    toolbarItem: 'add,|,edit,|,save,|,cancel,|,del',
                                                    @endif
                                                    local: 'local',
                                                    addLocation: 'last',
                                                    dataUrl: '/customer/companylistJson/id/{{$id}}',
                                                    editMode:'inline',
                                                    editUrl: '/index/ajaxDone',
                                                    paging: false,
                                                    linenumberAll: true,
                                                    fullGrid:true
                                                }">
                                <thead>
                                    <tr data-options="{name:'sysno'}">
                                        <th data-options="{name:'companyname',rule:'required',align:'center'}">开票抬头(必填)</th>
                                        <th data-options="{name:'memo',align:'center'}">备注</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </fieldset>
                </div>
              <!--people end-->

                <div class="clear-curstom"></div>
                                <!--upload start-->
                     <div class="comuser-add">
                            <!-- 自带bug -->
                            <div style="display: none">
                                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                                {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'customer',action:'customercertificates'},
                                required: false,
                                uploaded: '',
                                basePath: '',
                                accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                                    }
                                }"
                                >
                            </div>
                            <!-- 临时解决end -->
                            <div class="comuser-add-left">
                                <fieldset class="customerfieldset">
                                 <legend>上传三证</legend>
                                 <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                                 {
                                 pick: {label: '点击选择图片'},
                                 server: '/attachment/uploadjson',
                                 fileNumLimit: 10,
                                 formData: {module:'customer',action:'customercertificates'},
                                 required: false,
                                 uploaded: '{{ $uploaded1 }}',
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
                         </div>
                         <div class="comuser-add-right">
                           <fieldset class="customerfieldset">
                             <legend>上传提货单样单</legend>
                             <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                             {
                             pick: {label: '点击选择图片'},
                             server: '/attachment/uploadjson',
                             fileNumLimit: 10,
                             formData: {module:'customer',action:'customerlading'},
                             required: false,
                             uploaded: '{{ $uploaded2 }}',
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
                     </div>
            </div>
             <!--upload end-->
              <div class="remarks">
                <fieldset>
                 <legend>备注</legend>
                 <textarea name="customermarks" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写备注" @if($views=='look') readonly @endif>{{$customermarks}}</textarea>
             </fieldset>
             </div>

             <div class="text-center">
                 @if($views!='look')
                 <button id="customersubmit" type="button" class="btn btn-green btn-lg" data-icon="save">保存</button>
                 @endif
             </div>

         </form>
    </div>
</div>

<div id="custom_customer_tb">
    @if($views!='look')
    <button type="button" class="btn btn-blue" data-icon="add" onclick="addGoods()"><i class="fa fa-plus"></i> 添加货品</button>
    @endif
</div>

<script type="text/javascript">
    $('input').on('ifChecked', function(event){
      var v=$("input[type='radio']:checked").val();
      if(v==1)
      {
        $.CurrentNavtab.find(".ischeckview1").show();
        $.CurrentNavtab.find(".ischeckview2").hide();
    }
    else
    {
        $.CurrentNavtab.find(".ischeckview2").show();
        $.CurrentNavtab.find(".ischeckview1").hide();
    }
});
    //三证合一初始化
    var v=$("input[type='radio']:checked").val();
    if(v==1)
    {
        $.CurrentNavtab.find(".ischeckview1").show();
        $.CurrentNavtab.find(".ischeckview2").hide();
    }
    else
    {
        $.CurrentNavtab.find(".ischeckview2").show();
        $.CurrentNavtab.find(".ischeckview1").hide();
    }

    function addGoods() {
        BJUI.dialog({
            id:'customer-goods-{{$id}}',
            url:'/customer/goodslist/id/{{$id}}',
            title:'添加货品',
            mask:true,
            maxable:false,
            minable:false,
            width:1200,
            height:500

        });
    }

    function viewObjs() {
        var data  = $("#customergoods-selected-table").data('allData');
        // console.log(data);
    }

//提交客户资料
$.CurrentNavtab.find("#customersubmit").click(function() {

    var v=$("input[type='radio']:checked").val();
    if(v==1)
    {
        $.CurrentNavtab.find("#customerlicense").attr("data-rule","a");
        $.CurrentNavtab.find("#customerorganizationcode").attr("data-rule","a");
        $.CurrentNavtab.find("#customertaxid").attr("data-rule","a");
        $.CurrentNavtab.find("#customercreditcode").attr("data-rule","required");
    }
    else
    {
        $.CurrentNavtab.find("#customercreditcode").attr("data-rule","a");
        $.CurrentNavtab.find("#customerlicense").attr("data-rule","required");
        $.CurrentNavtab.find("#customerorganizationcode").attr("data-rule","required");
        $.CurrentNavtab.find("#customertaxid").attr("data-rule","required");
    }

    var error4Uploader = false
    $.CurrentNavtab.find('.customerfieldset').each(function(){
        if(!$(this).find('.uploadBtn').hasClass('disabled')  && $(this).find(".filelist > li").length > 0  ){
            error4Uploader = true;
        }
    });
    if(error4Uploader){
        BJUI.alertmsg('info', '请先提交图片再提交表单！')
        return;
    }
    
    var customergoodsstring = "";
    
    var customerObj = $.CurrentNavtab.find("#contacttable").data('allData');
    if(customerObj.length<=0 || customerObj==null)
    {
        BJUI.alertmsg('warn', '请先保存业务联系人！');
         return;
    }
    for (var i = customerObj.length - 1; i >= 0; i--) {
        if (!$.trim(customerObj[i].contactsmobilephone) && !$.trim(customerObj[i].contactstelephone)) {
            BJUI.alertmsg('warn', '手机和座机必须填写一个');
            return;
        }else {
            var myreg = /^1[3-9]\d{9}$/; 
            if(!myreg.test(customerObj[i].contactsmobilephone) && customerObj[i].contactsmobilephone) 
            { 
                BJUI.alertmsg('warn', '手机号格式不正确！');
                return;
            }
            var myreg = /^(?:(?:0\d{2,3}[\- ]?[1-9]\d{6,7})|(?:[48]00[\- ]?[1-9]\d{6}))$/; 
            if(!myreg.test(customerObj[i].contactstelephone) && customerObj[i].contactstelephone) 
            { 
                BJUI.alertmsg('warn', '请填写有效的电话号码！');
                return;
            }
        }
    }

    var customergoodsObj = $.CurrentNavtab.find("#customergoods-selected-table").data('allData');

    $.CurrentNavtab.find("#contactsdata").val(JSON.stringify(customerObj));

    for (var i = customergoodsObj.length - 1; i >= 0; i--) {
        customergoodsstring+=customergoodsObj[i].sysno+",";
    }

    $.CurrentNavtab.find("#goodsdata").val(customergoodsstring.substr(0,customergoodsstring.length-1));

    var customercompanyObj = $.CurrentNavtab.find("#customercompanytable").data('allData');
     $.CurrentNavtab.find("#companydata").val(JSON.stringify(customercompanyObj));

    BJUI.ajax('ajaxform', {
        url: '{{$action}}',
        form: $.CurrentNavtab.find('#customerform'),
        validate: true,
        loadingmask: true,
        okCallback:function (json, options) {
            // BJUI.navtab('refresh', 'navab327');
            BJUI.navtab('closeCurrentTab', '');
        }

    });
});
</script>
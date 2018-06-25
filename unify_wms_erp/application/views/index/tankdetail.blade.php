<div class="bjui-pageContent clearfix">
    <div class="bglog-tab">
        <ul>
            <li class="active" data-url="/Index/storagetankcurrent">显示当前</li>
            <li data-url="/Index/storagetankin" >考虑待入库</li>
            <li data-url="/Index/storagetankinout">考虑待入库和待出库</li>
        </ul>
    </div>
    <br>
    <div id="tab-log" style="height: inherit; overflow: auto;"></div>
</div>
<script type="text/javascript">
    $(function(){
        $(this).bjuiajax('doLoad', {url:'/Index/storagetankcurrent/type/1', target:"#tab-log"});
    })
    $('.bglog-tab ul li').each(function(){
        $(this).click(function(){
            $('.bglog-tab ul').find('li').eq($(this).index()).addClass('active').siblings('li').removeClass('active');
            $(event.target).bjuiajax('doLoad', {url:$(this).attr('data-url'), target:"#tab-log"});
        });
    });
</script>

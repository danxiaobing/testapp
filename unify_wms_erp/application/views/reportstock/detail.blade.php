<div class="bjui-pageContent clearfix">
    <div class="bglog-tab">
        <ul>
            <li class="active" data-url="/Reportstock/outStock/sysno/{{$sysno}}/firstfrom_sysno/{{$firstfrom_sysno}}">出库</li>
            <li data-url="/Reportstock/changeStock/sysno/{{$sysno}}/firstfrom_sysno/{{$firstfrom_sysno}}" >货权转移</li>
            <li data-url="/Reportstock/clearStock/sysno/{{$sysno}}/firstfrom_sysno/{{$firstfrom_sysno}}">清库</li>
        </ul>
    </div>
    <br>
    <div id="tab_log_stock" style="height: inherit; overflow: auto;"></div>
</div>
<script type="text/javascript">
    $(function(){
        $(this).bjuiajax('doLoad', {url:'/Reportstock/outStock/sysno/{{$sysno}}/firstfrom_sysno/{{$firstfrom_sysno}}', target:"#tab_log_stock"});
    })
    $('.bglog-tab ul li').each(function(){
        $(this).click(function(){
            $('.bglog-tab ul').find('li').eq($(this).index()).addClass('active').siblings('li').removeClass('active');
            $(event.target).bjuiajax('doLoad', {url:$(this).attr('data-url'), target:"#tab_log_stock"});
        });
    });
</script>

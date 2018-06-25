<div class="stock">
    @foreach($storageData as $sdata)
        <div class="barbox" style="background: rgba(0,0,0,.05);">
            <h3>{{$sdata['areaname']}}</h3>
            <ul class="bar clearfix">
                @if(isset($sdata['storageData']))
                    @foreach($sdata['storageData'] as $value)
                        <li>
                            <div class="bottom" >
                                <div class="blockDt3 active" id="example3_{{$value['sysno']}}">
                                    <p>{{$value['actualcapacity']}}T</p>
                                    <p>{{$value['tankinoutqty']}}T</p>
                                    <p class="theoreticalcapacity">{{round(($value['tankinoutqty'])/$value['actualcapacity']*100).'%'}}</p>
                                </div>
                            </div>
                            <div class="top"></div>
                            <div class="idcode">{{$value['storagetankname']}}<br>{{$value['goodsname']}}</div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endforeach
</div>
<script type="text/javascript">
$(function(){
    $(".blockDt3").each(function(index, el) {
        var idName = $(this).attr("id");
        var hBac = $(this).find(".theoreticalcapacity").html();
        hBac = parseFloat(hBac);
        jQuery("#"+idName).raindrops({color:'rgba(76,175,80,.5)',canvasHeight:(hBac*400)/100});
        
    });
});
</script>
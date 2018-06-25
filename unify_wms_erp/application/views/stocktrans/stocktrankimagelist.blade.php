<div class="bjui-pageContent">
    <div class="comuser-add-right" id='ship_release'>
        <fieldset class="customerfieldset" id='release'>
            <legend>附件</legend>
            @foreach($imageData as $key=>$value)
                <img src="{{$value['path'] . '/' . $value['name']}}"><br/>
            @endforeach
        </fieldset>
    </div>
</div>


<div class="bjui-pageContent">
    <div class="comuser-add-right" id='retank_release'>
        <fieldset class="customerfieldset" id='retank_release'>
            <legend>附件</legend>
            @foreach($imageData as $key=>$value)
                <img src="{{$value['path'] . '/' . $value['name']}}"><br/>
            @endforeach
        </fieldset>
    </div>
</div>


<div class="bjui-pageContent">
    <div class="bs-example">
        <span style="font-size:14px;"><div id="uploader-demo">
            <!--用来存放item-->
            <div id="thelist" class="uploader-list"></div>
            <div>
                 <div id="filePicker">选择文件</div>
                 <button id="ctlBtn" class="btn btn-default">开始上传</button>
                </div>
            </div>
        </span>
    </div>
</div>
<script type="text/javascript">
  $(function(){
      /*init webuploader*/
      var $list=$("#thelist");   //这几个初始化全局的百度文档上没说明，好蛋疼。
      var $btn =$("#ctlBtn");   //开始上传
      var thumbnailWidth = 100;   //缩略图高度和宽度 （单位是像素），当宽高度是0~1的时候，是按照百分比计算，具体可以看api文档
      var thumbnailHeight = 100;

      var uploader = WebUploader.create({
          // 选完文件后，是否自动上传。
          auto: false,
          // swf文件路径
          swf: '/static/BJUI/B_JUI/plugins/webuploader/Uploader.swf',

          // 文件接收服务端。
          server: '/import/import',

          // 选择文件的按钮。可选。
          // 内部根据当前运行是创建，可能是input元素，也可能是flash.
          pick: '#filePicker',

          // 只允许选择图片文件。
          accept: {
              title: 'excel',
              extensions: 'xlsx,xlsm,xltx,xltm,xls,xlt',
              mimeTypes: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' //excel类型2010 .xlxs 
          },
          method:'POST',
      });
      // 当有文件添加进来的时候
      uploader.on( 'fileQueued', function( file ) {  // webuploader事件.当选择文件后，文件被加载到文件队列中，触发该事件。等效于 uploader.onFileueued = function(file){...} ，类似js的事件定义。
          var $li = $(
                          '<div id="' + file.id + '" class="file-item thumbnail">' +
                          '<img>' +
                          '<div class="info">' + file.name + '</div>' +
                          '</div>'
                  ),
                  $img = $li.find('img');


          // $list为容器jQuery实例
          $list.append( $li );

          // 创建缩略图
          // 如果为非图片文件，可以不用调用此方法。
          // thumbnailWidth x thumbnailHeight 为 100 x 100
          uploader.makeThumb( file, function( error, src ) {   //webuploader方法
              if ( error ) {
                  $img.replaceWith('<span>不能预览</span>');
                  return;
              }

              $img.attr( 'src', src );
          }, thumbnailWidth, thumbnailHeight );
      });
      // 文件上传过程中创建进度条实时显示。
//      uploader.on( 'uploadProgress', function( file, percentage ) {
//          var $li = $( '#'+file.id ),
//                  $percent = $li.find('.progress span');
//
//          // 避免重复创建
//          if ( !$percent.length ) {
//              $percent = $('<p class="progress"><span></span></p>')
//                      .appendTo( $li )
//                      .find('span');
//          }
//
//          $percent.css( 'width', percentage * 100 + '%' );
//      });

      // 文件上传成功，给item添加成功class, 用样式标记上传成功。
      uploader.on( 'uploadSuccess', function( file,json ) {
          if(json.code == 200){
              BJUI.alertmsg('warn','<h4>'+json.message+'</h4>');
              BJUI.navtab('reloadFlag', 'menu571');
          }else {
              BJUI.alertmsg('warn','<h4>'+json.message+'</h4>');
              BJUI.navtab('reloadFlag', 'menu571');
          }
      });

      // 文件上传失败，显示上传出错。
//      uploader.on( 'uploadError', function( file ) {
//          var $li = $( '#'+file.id ),
//                  $error = $li.find('div.error');
//
//          // 避免重复创建
//          if ( !$error.length ) {
//              $error = $('<div class="error"></div>').appendTo( $li );
//          }
//
//          $error.text('上传失败');
//      });

      $btn.on( 'click', function() {
          uploader.upload();
      });
  });
 </script>

function showRecords() {
  $.CurrentNavtab.find('.hideshow').toggle(500);
 }

function addLog(obj,objid,doctypes) {

            BJUI.ajax('doajax', {
            url:'/log/doclogJson',
            data: {id:objid,doctype:doctypes},
            loadingmask: true,
            okCallback: function(json, options) {

               var htm=' <table class="jl-table">'
                      +'<thead>'
                      +'<tr>'
                      +'<th>NO</th>'  
                      +'<th>操作</th>'
                      +'<th>备注</th>'
                      +'<th>操作人</th>'
                      +'<th>操作时间</th>'
                      +'</tr>'
                      +'</thead>'
                      +'<tbody>';

                    for(var i=0;i<json.list.length;i++)
                    {
                     htm +=' <tr><td>'+ (i+1) +' </td><td>'+ json.list[i].opertype +' </td><td>'+ json.list[i].operdesc +' </td><td>'
                     + json.list[i].operemployeename +' </td><td>'+ json.list[i].opertime +' </td></tr>';
                    }

                     htm+=' </tbody></table>';
                     obj.html(htm);

                }

            });
            
        }
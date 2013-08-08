function array_values (input,getkeys) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +      improved by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: array_values( {firstname: 'Kevin', surname: 'van Zonneveld'} );
  // *     returns 1: {0: 'Kevin', 1: 'van Zonneveld'}
  var tmp_arr = [],
    key = '';

  if (input && typeof input === 'object' && input.change_key_case) { // Duck-type check for our own array()-created PHPJS_Array
    return input.values();
  }

  for (key in input) {
      if(typeof(geykeys)!= undefined){
          tmp_arr[tmp_arr.length] = key;
      }else{
          tmp_arr[tmp_arr.length] = input[key];
      }
  }

  return tmp_arr;
}

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
$.fn.extend({
    resaltar:function (busqueda, claseCSSbusqueda) {
        var regex = new RegExp("(<[^>]*>)|(" + busqueda.replace(/([-.*+?^${}()|[\]\/\\])/g, "\\$1") + ')', 'ig');
        var nuevoHtml = this.html(this.html().replace(regex, function (a, b, c) {
            return (a.charAt(0) == "<") ? a : "<span class=\"" + claseCSSbusqueda + "\">" + c + "</span>";
        }));
        return nuevoHtml;
    }
});
$.wait = function (callback, seconds) {
    return window.setTimeout(callback, seconds * 1000);
}
$.clearwait = function (callback, seconds) {
    return window.clearTimeout(callback);
}
$.clock = function (callback, seconds) {
    return window.setInterval(callback, seconds * 1000);
}
$.clearclock = function (callback) {
    return window.clearInterval(callback);
}

function resetForm($form) {
    $form.find('input:text, input:password, input:file, select, textarea').val('');
    $form.find('input:radio, input:checkbox')
         .removeAttr('checked').removeAttr('selected');
}
$(".cancelar").click(function () {
    history.back();
});
var ua = navigator.userAgent,
eventtrigger = (ua.match(/iPad/i)) ? "touchstart" : "click";

function confirmDel(layout,oktext,canceltext,textlabel,data,urlgo,callback) {
    
    noty({
      text: textlabel,
      type: 'confirm',
      dismissQueue: true,
      layout: layout,
      theme: 'noty_theme_default',
      buttons: [
        {type: 'btn btn-primary', text: oktext, click: function($noty) {
                $.ajax({
                    type:"POST",
                    url:urlgo,
                    dataType: 'json',
                    data: data,
                    error:function (jqXHR, textStatus, errorThrown){
                        console.log(JSON.stringify(jqXHR) + ' ' + textStatus +'  '+errorThrown );
                     }
                }).done(function(data) {
                    callback(data);
                });
                $noty.close();
            }
        },
        {type: 'btn btn-danger', text: canceltext, click: function($noty) {
            $noty.close();
          }
        }
      ]
    });
  }
function showMsj(layout,textLabel) {
    noty({
      text: textLabel,
      type: 'confirm',
      dismissQueue: true,
      layout: layout,
      theme: 'noty_theme_default'
    });
  }

function showNotification(mensaje) {
    noty({
        text:mensaje,
        type:'information',
        dismissQueue:true,
        layout:'center',
        timeout:'500',
        theme:'noty_theme_default'
    });
}
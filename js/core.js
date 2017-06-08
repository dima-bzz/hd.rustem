var my_errors = {fio: false, login: false, pass: false, noty: false};
$(document).ready(function() {



        moment.locale(MyLANG);



        function makemytime(s){

        var now = moment();

        String.prototype.toHHMMSS = function() {
            var sec_num = parseInt(this, 10); // don't forget the second param
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);
            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            var time = hours + ':' + minutes + ':' + seconds;
            return time;
        }


          $('time#a').each(function(i, e) {
          var time = moment($(e).attr('datetime'));
          $(e).html('<span>' + time.from(now,true) + '</span>');
        })
		    $('time#b').each(function(i, e) {
		    var time = moment($(e).attr('datetime'));
		    $(e).html('<span>' + time.from(now) + '</span>');
		    });

		    $('time#c').each(function(i, e) {
		    var time = moment($(e).attr('datetime'));

		    $(e).html('<span>' + time.format("ddd, Do MMM Y, HH:mm:ss") + '</span>');
		    });

        $('span#ded').each(function(i, e) {
        var time = moment($(e).attr('datetime'));

        $(e).attr({"data-toggle":"tooltip", "data-placement":"bottom",'data-original-title':time.format("ddd, Do MMM Y, HH:mm:ss")});
        });

        $('time#f').each(function(i, e) {
            var time = $(e).attr('datetime');

            var ms = time * 1000;
            $(e).html('<span>' + humanizeDuration(ms,{language: MyLANG, units:['d','h','m','s'], round: true, delimiter: ' '}) + '</span>');
        });
    };

    var intr = null;
    //setInterval(plus_sec(), 1000);
    function gotimer_worker() {
        //setInterval(function() {
        if ($('#work_timer').attr('value') == 'true') {
            var t = $('#work_timer > #f').attr('datetime');
            t++;
            $('#work_timer > #f').attr('datetime', t);
            var el = $('#work_timer > time#f').attr('datetime');
            var ms = el * 1000;
            $('#work_timer > time#f').html('<span>' + humanizeDuration(ms,{language: MyLANG, units:['d','h','m','s'], round: true, delimiter: ' '}) + '</span>');
        }
        //  }, 1000);
        //$('#work_timer').attr('value', 'false');
    }

    function gotimer_deadline() {
        //setInterval(function() {
        if ($('#deadline_timer').attr('value') == 'true') {
            var t = $('#deadline_timer > #f').attr('datetime');
            t--;
            $('#deadline_timer > #f').attr('datetime', t);
            var el = $('#deadline_timer > time#f').attr('datetime');
            var ms = el * 1000;
            $('#deadline_timer > time#f').html('<span>' + humanizeDuration(ms,{language: MyLANG, units:['d','h','m','s'], round: true, delimiter: ' '}) + '</span>');
        }
        //  }, 1000);
        //$('#work_timer').attr('value', 'false');
    }
    function stop_gotimer_deadline(){
      if ($('#deadline_timer > #f').attr('datetime') == "0"){
      $('#deadline_timer').attr('value', 'false');
      //intr= setInterval(gotimer_worker,1000);
      clearInterval(intr);
      $('#deadline_timer').empty().append( $.i18n('TICKET_overdue') );
    }
    }
    if ($('#deadline_timer').attr('value') == 'true') {
        //gotimer_worker();
        intr = setInterval(function(){
          gotimer_deadline();
          stop_gotimer_deadline();
        }, 1000);
        //clearInterval(intr);
    }
    if ($('#work_timer').attr('value') == 'true') {
        //gotimer_worker();
        intr = setInterval(gotimer_worker, 1000);
        //clearInterval(intr);
    }

var ACTIONPATH=MyHOSTNAME+"actions.php";

function format(state) {
    var originalOption = state.element;


    return "<img class='flag' src='" + MyHOSTNAME + "img/" + $(originalOption).data('foo')  + ".png' alt='" + $(originalOption).data('foo') + "' /> " + state.text;
}



function sendFile(file, editor, welEditable) {
 data = new FormData();
 data.append("file", file);
 $.ajax({
 data: data,
 type: "POST",
 url: MyHOSTNAME+"sys/up_summernote.php",
 cache: false,
 contentType: false,
 processData: false,
 success: function(url) {
 editor.insertImage(welEditable, url);
 }
 });
 }


 $(".chosen-select_no_search").chosen({
   disable_search: true
 });

 $.i18n.debug = false;
 $.i18n().locale = MyLANG;
 $.i18n().load( {
   'en' : MyHOSTNAME + "lang/lang.en.json",
   'ru' : MyHOSTNAME + "lang/lang.ru.json",
   'ua' : MyHOSTNAME + "lang/lang.ua.json"
 }).done (function(){


    $('textarea').autosize({append: "\n"});

$("#users_do").select2({
    formatResult: format,
    formatSelection: format,
    allowClear: true,
    width:'element',
    formatNoMatches:$.i18n('JS_not_found'),
    escapeMarkup: function(m) { return m; }
});
$("#show_noty_edit").select2({
    allowClear: true,
    width:'element',
    formatNoMatches:$.i18n('JS_not_found'),
    escapeMarkup: function(m) { return m; }
});
$("#jabber_show_edit").select2({
    allowClear: true,
    width:'element',
    formatNoMatches:$.i18n('JS_not_found'),
    escapeMarkup: function(m) { return m; }
});
$("#show_noty_profile").select2({
    allowClear: true,
    width:'element',
    formatNoMatches:$.i18n('JS_not_found'),
    escapeMarkup: function(m) { return m; }
});
$("#jabber_show_profile").select2({
    allowClear: true,
    width:'element',
    formatNoMatches:$.i18n('JS_not_found'),
    escapeMarkup: function(m) { return m; }
});
$("#t_users_do").select2({
    formatResult: format,
    formatSelection: format,
    allowClear: true,
    width:'element',
    formatNoMatches:$.i18n('JS_not_found'),
    escapeMarkup: function(m) { return m; }
});

    $('#my-select').multiSelect({
    });

    $("#spinner").hide();
    $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});

    var settingsShow = function() {
        var showPanel = $(".chosen-select").find('option:selected').attr('id');

    }
    $(".chosen-select").chosen({
        no_results_text: $.i18n('JS_not_found'),
        allow_single_deselect: true
    });
    $(".chosen-select").chosen().change(settingsShow);
    $.fn.editable.defaults.mode = 'inline';
    $("#user_info").hide();
    $("#alert_add").hide();

    $("#refer_to").hide();

function ispath(p1) {
var url = window.location.href;
var zzz=false;
if (url.search(p1) >= 0) {
    zzz=true;
}
return zzz;
};



function whatpath() {
var url = window.location.href;
var zzz="";
if (url.search("inc") >= 0) {
    zzz="../";
}
return zzz;
};

function check_approve(up){
$.ajax({
type: "POST",
url: ACTIONPATH,
data: "mode=approve",
dataType: "json",
success: function(html){

  $.each(html, function(i, item) {
    if (up != 'update'){
if (item.approve !== '0'){
  if ((item.approve == $( "#ap" ).html()) && ($( "#ap" ).html() != '')){
$( "#ap" ).html( item.approve ).fadeIn(500);
$( "#ap" ).html( item.approve )

}
else{
$( "#ap" ).hide().empty().html( item.approve ).fadeIn(500);
$( "#ap2" ).empty().html( item.approve );
}
}
else {
$( "#ap" ).fadeOut(500);
$( "#ap2" ).empty().removeAttr( 'style' );
}

if (item.approve_ticket !== '0'){
  if ((item.approve_ticket == $( "#ap_ticket" ).html()) && ($( "#ap_ticket" ).html() != '')){
$( "#ap_ticket" ).html( item.approve_ticket ).fadeIn(500);
$( "#ap_ticket" ).html( item.approve_ticket )

}
else{
$( "#ap_ticket" ).hide().empty().html( item.approve_ticket ).fadeIn(500);
$( "#ap_ticket2" ).empty().html( item.approve_ticket );
}
}
else {
$( "#ap_ticket" ).fadeOut(500);
$( "#ap_ticket2" ).empty().removeAttr( 'style' );
}

if (item.online_users !== '0'){
  if ((item.online_users == $( "#online" ).html()) && ($( "#online" ).html() != '')){
$( "#online" ).html( item.online_users ).fadeIn(500);
$( "#online" ).html( item.online_users )

}
else{
$( "#online" ).hide().empty().html( item.online_users ).fadeIn(500);
// $( "#online2" ).empty().html( item.online_users );
}
}
else {
$( "#online" ).fadeOut(500);
// $( "#online2" ).empty().removeAttr( 'style' );
}

if (item.make_logout_user == "true"){
      window.location.href = MyHOSTNAME + 'index.php?logout';
      $.ajax({
        url:  ACTIONPATH,
        type: "POST",
        data:"mode=update_logout" +
        "&userid=" + userid
      })
}
}
else{
  if (item.approve !== '0'){
    if ((item.approve == $( "#ap" ).html()) && ($( "#ap" ).html() != '')){
  $( "#ap" ).html( item.approve ).fadeIn(500);
  $( "#ap" ).html( item.approve )

  }
  else{
  $( "#ap" ).hide().empty().html( item.approve ).fadeIn(500);
  $( "#ap2" ).empty().html( item.approve );
  }
  }
  else {
  $( "#ap" ).fadeOut(500);
  $( "#ap2" ).empty().removeAttr( 'style' );
  }
}
})
}
})
}
// ***** Отключение пользователя*****
$('body').on('click', 'button#users_logout', function(event) {
          event.preventDefault();
          var id_users_logout = $(this).attr('value');
          $.ajax({
            url:  ACTIONPATH,
            type: "POST",
            data:"mode=make_logout_user" +
            "&userid=" + id_users_logout,
            success: function(){
              window.location = MyHOSTNAME + "users";
            }
          })
});
// ***** Таблица с пользователями в сети*****
$('body').on('click', 'a#show_online_users', function(event) {
          event.preventDefault();
          $.ajax({
            url:  ACTIONPATH,
            type: "POST",
            data:"mode=approve_online_users_table",
            success: function(html){
              $('#online3').html(html);
              $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
            }
          })
          $.ajax({
          type: "POST",
          url: ACTIONPATH,
          data: "mode=approve",
          dataType: "json",
          success: function(html){

            $.each(html, function(i, item) {
              $( "#online2" ).empty().html( item.online_users );
            })
          }
        })
});
function send_jabber(){
  // var ee = $("#main_last_new_ticket_jabber").val();
  // if (ee){
  //   $.ajax({
  //       type: "POST",
  //       url: ACTIONPATH,
  //       data: "mode=check_update_jabber"+
  //           "&last_update="+encodeURIComponent(ee),
  //       success: function(html){
  //           if (html == "no") {
  //           }
  //           else {
  //               var new_lu=html;
  $.ajax({
      type: "POST",
      url: ACTIONPATH,
      data: "mode=send_jabber_noty",
      // "&last_update="+encodeURIComponent(ee),
      success: function(html){
        makemytime(true);
        // console.log(ee);
        // console.log(new_lu);
        // $("#main_last_new_ticket_jabber").attr('value', new_lu);
      }
    });
//   }
// }
// });
//   }
}
function update_labels(){
  // var wt = var t = $('#work_timer > #f').attr('datetime');
  // var dt = var t = $('#deadline_timer > #f').attr('datetime');

  $.ajax({
      type: "POST",
      url: ACTIONPATH,
      data: "mode=update_labels",
      // "&last_update="+encodeURIComponent(ee),
      success: function(html){
        makemytime(true);
      }
    });
}
    $("#fio").autocomplete({
        max: 10,
        minLength: 2,
        source: MyHOSTNAME+"inc/json.php?fio",
        focus: function(event, ui) {
            $("#fio").val(ui.item.label);
            return false;
        },
        select: function(event, ui) {
            $("#fio").val(ui.item.label);
            $("#client_id_param").val(ui.item.value);
            $('#fio').popover('hide');
            $('#for_fio').removeClass('has-error').addClass('has-success');
            $("#user_info").hide().fadeIn(500);
            $("#alert_add").hide();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_client_from_new_t&get_client_info=" + ui.item.value,
                success: function(html) {
                    $("#user_info").hide().html(html).fadeIn(500);
                    $('#edit_login').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });
                    $('#edit_posada').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто',
                        mode: 'popup',
                        showbuttons: false
                    });				$('#edit_unit').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто',
                        mode: 'popup',
                        showbuttons: false
                    });				$('#edit_tel').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });				$('#edit_adr').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });				$('#edit_mail').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });
                    $('#for_fio').addClass('has-success');
                    $("#status_action").val('edit');
makemytime(true);
                }
            });
            return false;
        },
        change: function(event, ui) {
            //console.log(this.value);

            if ($('input#fio').val().length != 0){


                if (ui.item == null) {

/*
ajax запрос с фио или логин или номер тел человека

php:
если найдена 1 запись, то выдать найденого
если не найдено то
		    1. разрешено добавление клиентов - выдать новый пользователь
		    2. не разрешено добавление клиентов - выдать - не найден пользователь и поле фио очистить

*/
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: ACTIONPATH,
                        data: "mode=find_client&name="+encodeURIComponent($("#fio").val()),
                        success: function(html) {
                        $.each(html, function(i, item) {
                        if (item.res == true) {


                        ///////////
           $("#client_id_param").val(item.p);
            $('#fio').popover('hide');
            $('#for_fio').removeClass('has-error').addClass('has-success');
            $("#user_info").hide().fadeIn(500);
            $("#alert_add").hide();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_client_from_new_t&get_client_info=" + encodeURIComponent(item.p),
                success: function(html) {
                    $("#user_info").hide().html(html).fadeIn(500);
                    $('#edit_login').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });
                    $('#edit_posada').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто',
                        mode: 'popup',
                        showbuttons: false
                    });				$('#edit_unit').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто',
                        mode: 'popup',
                        showbuttons: false
                    });				$('#edit_tel').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });				$('#edit_adr').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });				$('#edit_mail').editable({
                        inputclass: 'input-sm',
                        emptytext: 'пусто'
                    });
                    $('#for_fio').addClass('has-success');
                    $("#status_action").val('edit');
makemytime(true);
                }
            });
                        ///////////



                        //console.log(item.p);
                        }
                        if (item.res == false) {


                            if (item.priv == true) {//console.log('add');
                            $("#user_info").hide();

                    $('#fio').popover('hide');
                    $('#for_fio').removeClass('has-error');
                    $('#for_fio').addClass('has-success');



	                        $("#status_action").val('add');
	                                            $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_client_from_new_t&new_client_info="+encodeURIComponent($("#fio").val()),
                        success: function(html) {
                            $("#alert_add").hide().html(html).fadeIn(500);

                            $('#username').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_login').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                            $('#new_posada').editable({inputclass: 'input-sm posada_class',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_unit').editable({inputclass: 'input-sm',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_tel').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_adr').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_mail').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                            makemytime(true);
                        }
                    });

                            }
                            if (item.priv == false) {//console.log('not add');
                            $("#user_info").hide();
                            $("#alert_add").hide().html(item.msg_error).fadeIn(500);
                            $("#status_action").val('');
                            $("#fio").val('');
                            makemytime(true);
                            }
                        }
                        });
                        }
                        });
                    /*
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_client_from_new_t&new_client_info="+$("#fio").val(),
                        success: function(html) {
                            $("#alert_add").hide().html(html).fadeIn(500);

                            $('#username').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_login').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                            $('#new_posada').editable({inputclass: 'input-sm posada_class',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_unit').editable({inputclass: 'input-sm',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_tel').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_adr').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_mail').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                        }
                    });
                    */
                } else {
                }

            }



        }









    });
    $("select#to").on('change', function() {

        if ( $('select#to').val() != 0 ){

            $('#for_to').popover('hide');
            $('#for_to').removeClass('has-error');
            $('#for_to').addClass('has-success');
            $('#dsd').popover('hide');
        }
        else {
	    $('#dsd').popover('show');
            $('#for_to').popover('show');
            $('#for_to').addClass('has-error');

        }

    });

    function createuserslist(unit_id, target_id) {


        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_users_list"+
                "&unit="+encodeURIComponent(unit_id),
            dataType: "json",
            success: function(html){
                $('select#'+target_id).removeAttr("disabled");
                $('select#'+target_id).empty();
                if (html) {
		    $('select#'+target_id)
                            .append($("<option></option>"));
                    $.each(html, function(i, item) {
                        $('select#'+target_id)
                            .append($("<option></option>")
                                .attr("value",item.co)
				.attr("data-foo",item.stat)
                                .text(item.name));

                    });
                }
                else{
                  $('select#'+target_id).attr("disabled","disabled");
                  $('select#'+target_id).empty();
                }
		$('select#'+target_id).trigger('change');
                $('select#'+target_id).trigger('chosen:updated');

            }

        });




    }


    $("select#to").change(function() {
        var i=$('select#to').val();
        if ( $('select#to').val() != 0 ){

            $('#for_to').popover('hide');
            $('#for_to').removeClass('has-error');
            $('#for_to').addClass('has-success');

        createuserslist(i, 'users_do');

        }
        else {

            $('#for_to').popover('show');
            $('#for_to').addClass('has-error');

            createuserslist(i, 'users_do');

        }

    });
if (ispath('config') ) {
if ( $('select#mail_type').val() == "sendmail" ){
 $('#smtp_div').hide();
 }
 else if ( $('select#mail_type').val() == "SMTP" ){
 $('#smtp_div').show();
 }
 setInterval(function(){
     check_update();
 },5000);
}

//mail_type
 $("select#mail_type").change(function() {

 if ( $('select#mail_type').val() == "sendmail" ){
 $('#smtp_div').hide();
 }
 else if ( $('select#mail_type').val() == "SMTP" ){
 $('#smtp_div').show();
 }
 });




    $("select#subj").change(function() {

        if ( $('select#subj').val() != 0 ){

            $('#for_subj').popover('hide');
            $('#for_subj').removeClass('has-error');
            $('#for_subj').addClass('has-success');
        }
        else {

            $('#for_subj').popover('show');
            $('#for_subj').addClass('has-error');

        }

    });

    $("select#subj").blur(function() {

        if ( $('select#subj').val() != 0 ){

            $('#for_subj').popover('hide');
            $('#for_subj').removeClass('has-error');
            $('#for_subj').addClass('has-success');
        }
        else {

            $('#for_subj').popover('show');
            $('#for_subj').addClass('has-error');

        }

    });





    $('textarea#msg').keydown(function (e) {

        if (e.ctrlKey && e.keyCode == 13) {


            $("button#do_comment").click();


        }
    });


/*
    $("textarea#msg").blur(function() {
        if($(this).val().length > 1) {
            $("textarea#msg").popover('hide');
            $("#for_msg").removeClass('has-error').addClass('has-success');

        } else {
            $("textarea#msg").popover('show');
            $("#for_msg").addClass('has-error');
        }
    });
*/
    $("textarea#msg").keyup(function() {
        if($(this).val().length > 1) {
            $("textarea#msg").popover('hide');
            $("#for_msg").removeClass('has-error').addClass('has-success');

        } else {
            $("textarea#msg").popover('show');
            $("#for_msg").addClass('has-error');
        }
    });

	$("input#subj").keyup(function() {
        if($(this).val().length > 1) {
            $("#subj").popover('hide');
            $("#for_subj").removeClass('has-error').addClass('has-success');

        } else {
            $("#subj").popover('show');
            $("#for_subj").addClass('has-error');
        }
    });



    var options_workers = {
        currentPage: $("#cur_page").val(),
        totalPages: $("#total_pages").val(),
        bootstrapMajorVersion: 3,
        size: "small",
        itemContainerClass: function (type, page, current) {
            return (page === current) ? "active" : "pointer-cursor";
        },
        onPageClicked: function(e,originalEvent,type,page){
            var current=$("#curent_page").attr('value');

            if (page != current) {

                $("#curent_page").attr('value', page);

                $.ajax({
                    type: "POST",
                    url: MyHOSTNAME+"inc/workers.inc.php",
                    data: "page="+encodeURIComponent(page)+
                        "&menu=list",
                    success: function(html){
                        $("#content_worker").hide().html(html).fadeIn(500);
                        $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                    }
                });
            }


        }

    }


    var options_in = {
        currentPage: $("#cur_page").val(),
        totalPages: $("#total_pages").val(),
        bootstrapMajorVersion: 3,
        size: "small",
        itemContainerClass: function (type, page, current) {
            return (page === current) ? "active" : "pointer-cursor";
        },
        onPageClicked: function(e,originalEvent,type,page){
            var current=$("#curent_page").attr('value');

            if (page != current) {

                $("#curent_page").attr('value', page);


                $("#spinner").fadeIn(300);

                $.ajax({
                    type: "POST",
                    url: MyHOSTNAME+"inc/list_content.inc.php",
                    data: "menu=in"+
                        "&page="+encodeURIComponent(page),
                    success: function(html){
                        $("#content").hide().html(html).fadeIn(500);
                        $("#spinner").hide();
                        $('[data-toggle="popover"]').popover();
                        $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});

makemytime(true);
                    }
                });
            }


        }

    }
    var options_out = {
        currentPage: $("#cur_page").val(),
        totalPages: $("#total_pages").val(),
        bootstrapMajorVersion: 3,
        size: "small",
        itemContainerClass: function (type, page, current) {
            return (page === current) ? "active" : "pointer-cursor";
        },
        onPageClicked: function(e,originalEvent,type,page){
            var current=$("#curent_page").attr('value');

            if (page != current) {
                $("#spinner").fadeIn(300);
                $("#curent_page").attr('value', page);
                $.ajax({
                    type: "POST",
                    url: MyHOSTNAME+"inc/list_content.inc.php",
                    data: "menu=out"+
                        "&page="+encodeURIComponent(page),
                    success: function(html){
                        $("#content").hide().html(html).fadeIn(500);
                        $("#spinner").hide();
                        $('[data-toggle="popover"]').popover();
                        $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
makemytime(true);
                    }
                });
            }

        }

    }
    var options_arch = {
        currentPage: $("#cur_page").val(),
        totalPages: $("#total_pages").val(),
        bootstrapMajorVersion: 3,
        size: "small",
        itemContainerClass: function (type, page, current) {
            return (page === current) ? "active" : "pointer-cursor";
        },
        onPageClicked: function(e,originalEvent,type,page){
            var current=$("#curent_page").attr('value');

            if (page != current) {
                $("#curent_page").attr('value', page);
                $("#spinner").fadeIn(300);

                $.ajax({
                    type: "POST",
                    url: MyHOSTNAME+"inc/list_content.inc.php",
                    data: "menu=arch"+
                        "&page="+encodeURIComponent(page),
                    success: function(html){
                        $("#content").hide().html(html).fadeIn(500);
                        $("#spinner").hide();
                        $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});

makemytime(true);
                    }
                });
            }


        }

    }


    $('#example_in').bootstrapPaginator(options_in);
    $('#example_out').bootstrapPaginator(options_out);
    $('#example_arch').bootstrapPaginator(options_arch);
    $('#example_workers').bootstrapPaginator(options_workers);



    $.ionSound({
        sounds: [
            "button_tiny"
        ]
    });


    var def_p = window.location.pathname.split("/");
    var def_filename = def_p[def_p.length-1];




//alert(window.location);

window.addEventListener('load', function() {

    function theNotification() {
        var n = new Notification("Hi!",  {
    	icon: 'icon.jpg',
    	tag: 'note',
    	body: 'Notification content...'
        });

    }
});

    $.noty.defaults = {
        layout: 'top',
        theme: 'defaultTheme',
        type: 'information',
        text: '',
        dismissQueue: true,
        template: '<div class="noty_message"><button type="button" class="close" style="padding-left: 2px;margin-top: -5px;" aria-hidden="true">&times;</button><span class="noty_text"></span></div>',
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500
        },
        timeout: false,
        force: false,
        modal: false,
        maxVisible: 5,
        killer: false,
        closeWith: ['click'],
        callback: {
            onShow: function() {},
            afterShow: function() {},
            onClose: function() {},
            afterClose: function() {}
        },
        buttons: false
    };


    function sendNotification(title, options, ticket) {
    // Проверим, поддерживает ли браузер HTML5 Notifications
    if (!("Notification" in window)) {
    alert($.i18n('Update_browser'));
    }

    // Проверим, есть ли права на отправку уведомлений
    else if (Notification.permission === "granted") {
    // Если права есть, отправим уведомление
    var notification = new Notification(title, options);
    notification.onclick = function(){
      window.open(MyHOSTNAME + 'ticket?' + ticket, '_top').focus();
      notification.close();
    }
    }

    // Если прав нет, пытаемся их получить
    else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
    // Если права успешно получены, отправляем уведомление
    if (permission === "granted") {
    var notification = new Notification(title, options);
    notification.onclick = function(){
      window.open(MyHOSTNAME + 'ticket?' + ticket, '_top').focus();
      notification.close();
    }
    } else {
    alert($.i18n('Disable_browser_noty')); // Юзер отклонил наш запрос на показ уведомлений
    }
    });
    } else {
    // Пользователь ранее отклонил наш запрос на показ уведомлений
    // В этом месте мы можем, но не будем его беспокоить. Уважайте решения своих пользователей.
    }
    };

    function Noty_Show(){
    $.ajax({
    url:  ACTIONPATH,
    type: "POST",
    data:"mode=update_noty",
    dataType: "json",
    success: function(html){
    if(html){

      $.each(html, function(i, item) {

          if (item.show == "true"){
          var nt = $('#'+item.name).attr('id');
          var noty_id = item.name.split('_')[4];
          console.log(item.name);
          if ((nt == item.name) && (nt != undefined)){
              my_errors.noty = true;
            }
            else{
              my_errors.noty = false;
            }
          if (my_errors.noty == false){

            var t= item.message + '<br><div style=\'float: right;\'><small><i class=\"fa fa-clock-o fa-fw\" aria-hidden=\"true\"></i>&nbsp;'+item.time+'</small></div>';

          var n = noty({
            //theme: 'metroui',
            layout: item.layout,
            maxVisible: 5,
            type: item.type,
            text: t,
            dismissQueue: true,
            force: false,
            modal: item.modal,
            killer: false,
            id: item.name,
            animation: {
              open: item.animated_open,
              close: item.animated_close,
              easing: 'swing',
              speed: 500
            },
            closeWith: ['click'],
            callback:{
                        onClose: function(){
                          $.ajax({
                          url:  ACTIONPATH,
                          type: "POST",
                          data:"mode=update_noty_id_read"+
                          "&id=" + noty_id,
                          success: function(){
                            if (item.noty_w == 'system_update'){
                              window.location.href = MyHOSTNAME + 'index.php?logout';
                            }
                          }
                        })
                      },
            }
          });
          if (item.noty_w != 'system_update'){
          sendNotification(item.name_firm, {
          body: item.message,
          icon: MyHOSTNAME+"images/logo32.png",
          dir: 'auto',
          tag: item.name
          }, noty_id);
          }
          $.ionSound.play("button_tiny");
          }
        }
        makemytime(true);
        })
    }
    }
    })

    }

    Noty_Show();


    function check_update() {
    makemytime(true);
        var ee=$("#main_last_new_ticket").val();
        var url = window.location.href;
	var zcode="";
if (url.search("inc") >= 0) {
    var zcode="../";
}



        if (ee) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=check_update"+
                    "&type=all"+
                    "&last_update="+encodeURIComponent(ee),
                success: function(html){
                    if (html == "no") {
                    }
                    else {
                        var new_lu=html;
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=list_ticket_update"+
                                "&last_update="+encodeURIComponent(ee),
                            dataType: "json",
                            success: function(html){
                                if (html) {
                                    $.each(html, function(i, item) {
                                        if (item.show == "true"){
                                        var t='<div style=\'float: left;\'><a style=\'color: rgb(243, 235, 235); cursor: inherit;\' target=\'_blank\' href=\''+item.url+'/ticket?'+item.hash+'\'><strong>'+item.ticket+' #'+item.name+'</strong> </a></div><div style=\'float: right; padding-right: 10px;\'><small>'+item.time+'</small></div><br><hr style=\'margin-top: 5px; margin-bottom: 8px; border:0; border-top:0px solid #E4E4E4\'><a style=\'color: rgb(252, 252, 252); cursor: inherit;\' target=\'_blank\' href=\''+item.url+'/ticket?'+item.hash+'\'>'+item.at+'</a>';
                                        noty({
                                            text: t,
                                            layout: item.show_noty,
                                            timeout: false
                                        });
                                        $.ionSound.play("button_tiny");
                                        $.titleAlert(item.up);
                                      }
                                        makemytime(true);
                                    });
                                }
                                $("#main_last_new_ticket").attr('value', new_lu);
                            }

                        });
                            $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=list_ticket_update2"+
                                "&last_update="+encodeURIComponent(ee),
                            dataType: "json",
                            success: function(html){
                                if (html) {
                                    $.each(html, function(i, item) {
                                    if (item.show == "true"){
                                    var h = ''+item.ticket+' #'+item.name+' - '+item.at+'';
                                    sendNotification('Helpdesk', {
                                    body: h,
                                    icon: MyHOSTNAME+"img/help-desk-icon.png",
                                    dir: 'auto',
                                    tag: item.hash
                                    }, item.hash);
                                  }
                                    });
                                }
                                $("#main_last_new_ticket").attr('value', new_lu);
                            }

                        });
                    }
                }});
                Noty_Show();
                check_approve();
        }
    };
$("#show_noty").change(function(){
  var n = $(this).val();
  noty({
        text: 'test',
        layout: n,
        timeout: 5000
      });
})


    function check_update_index() {
    makemytime(true);
        var ee=$("#main_last_new_ticket").val();
        if (ee) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=check_update"+
                    "&type=all"+
                    "&last_update="+encodeURIComponent(ee),
                success: function(html){
                    if (html == "no") {
                    }
                    else {
                        var new_lu=html;
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=list_ticket_update"+
                                "&last_update="+encodeURIComponent(ee),
                            dataType: "json",
                            success: function(html){

                                if (html) {
                                    $.each(html, function(i,item) {
                                        if (item.show == "true"){
                                        var t='<div style=\'float: left;\'><a style=\'color: rgb(243, 235, 235); cursor: inherit;\' target=\'_blank\' href=\''+item.url+'/ticket?'+item.hash+'\'><strong>'+item.ticket+' #'+item.name+'</strong> </a></div><div style=\'float: right; padding-right: 10px;\'><small>'+item.time+'</small></div><br><hr style=\'margin-top: 5px; margin-bottom: 8px; border:0; border-top:0px solid #E4E4E4\'><a style=\'color: rgb(252, 252, 252); cursor: inherit;\' target=\'_blank\' href=\''+item.url+'/ticket?'+item.hash+'\'>'+item.at+'</a>';
					noty({
                                            text: t,
                                            layout: item.show_noty,
                                            timeout: false
                                        });
                                        $.ionSound.play("button_tiny");
                                      }
makemytime(true);
                                        $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            data: "mode=last_news",
                                            success: function(html){
                                                $('#last_news').html(html);
                                                $('[data-toggle="tooltip"]').tooltip('hide');
                                                $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                                                makemytime(true);
makemytime(false);
                                            }
                                        });
                                        $('#spinner').show();
                                        $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            data: "mode=dashboard_t",
                                            success: function(html){

                                                $('#dashboard_t').html(html);
                                                $('#spinner').hide();
                                                $('[data-toggle="popover"]').popover();
                                                // $('[data-toggle="tooltip"]').tooltip('hide');
                                                $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                                                makemytime(true);                                            }
                                        });

                                        $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            dataType: "json",
                                            data: "mode=update_dashboard_labels",
                                            success: function(html){
                                                $.each(html, function(i, item) {
                                                    $('#d_label_1').html(item.a);
                                                    $('#d_label_2').html(item.b);
                                                    $('#d_label_3').html(item.c);
                                                });
                                                makemytime(true);
                                            }
                                        });


                                    });
                                }
                                $("#main_last_new_ticket").attr('value', new_lu);
                            }
                        });
			$.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=list_ticket_update2"+
                                "&last_update="+encodeURIComponent(ee),
                            dataType: "json",
                            success: function(html){
                                if (html) {
                                    $.each(html, function(i,item) {
                                    if (item.show == "true"){
					                          var h = ''+item.ticket+' #'+item.name+' - '+item.at+'';
                                    sendNotification('Helpdesk', {
                                    body: h,
                                    icon: MyHOSTNAME+"img/help-desk-icon.png",
                                    dir: 'auto',
                                    tag: item.hash
                                    }, item.hash);
                                    }
                                  });
            	                   }
                  $("#main_last_new_ticket").attr('value', new_lu);

                }});
        }
	}});
  Noty_Show();
  check_approve();
	}
    };








if (ispath('notes') ) {
    //if (def_filename == "notes.php") {

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_list_notes",
            success: function(html){
                $('#table_list').html(html);

                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_first_note",
                    success: function(html){
                    }
                });


            }
        });






    }

if (ispath('ticket')) {
    //if (def_filename == "ticket.php") {

    makemytime(true);

        setInterval(function(){
            var lu=$("#last_update").attr('value');
            var tid=$("#ticket_id").attr('value');
            check_update();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                dataType: "json",
                data: "mode=check_update_one"+
                    "&id="+tid+
                    "&last_update="+lu,
                success: function(html){


                    if (html) {
                        $.each(html, function(i, item) {


                            if (item.type == "update") {window.location = MyHOSTNAME+"ticket?"+item.hash+"&refresh"; }
                            else if (item.type == "comment") {
                                $.ajax({
                                    type: "POST",
                                    url: ACTIONPATH,
                                    data: "mode=view_comment"+
                                        "&tid="+encodeURIComponent(tid),
                                    success: function(r) {

                                        $("#comment_content").html(r);

                                        $("#last_update").attr('value',item.time);
                                        makemytime(true);
                                          var scroll    = $('#comment_body');
  var height = scroll[0].scrollHeight;
  scroll.scrollTop(height);
console.log(height);
                                    }
                                });

                            }
                            else if (item.type == "no") {

                            }



                        });

                    }

                }
            });

        var p = new RegExp ('(\.|\/)'+$('input#file_types').val());
            // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: MyHOSTNAME+'sys/index_comment.php',
            autoUpload: true,
            disableValidation: false,
    	//acceptFileTypes: /(\.|\/)(gif|jpe?g|png|doc|xls|rtf|pdf|zip|rar|bmp|docx|xlsx|txt)$/i,
            acceptFileTypes: p,
    	 maxFileSize: $('input#file_size').val(),
    	// messages: {
      //
      //           },

            formData: {hashname:$('input#hashname').val()}
    }).on('fileuploadprocessalways', function (e, data) {

    $(this).removeClass('fileupload-processing');


        }).on('fileuploaddone', function (e, data) {

            $.each(data.result.files, function (index, file) {
                if (file.url) {
                    var link = $('<a>')
                        .attr('target', '_blank')
                        .prop('href', file.url);
                    $(data.context.children()[index])
                        .wrap(link);
                } else if (file.error) {
                    var error = $('<span class="text-danger"/>').text(file.error);

                    $(data.context.children()[index])
                        .append('<br>')
                        .append(error);
                }
            });



        }).on('fileuploadstop', function(e, data) {


             /*if (check_form_ticket() == 0 ) {
    enter_ticket();
    }
    */

    //enter_ticket();
        }).on('fileuploadadd', function (e, data) {/* ...
    $("#uploadBtn").on('click',function () {
    data.submit();
    console.log('hello');
    });
    */

    }).on('fileuploadsubmit', function (e, data) {
    // console.log(data);
    });
  },5000);

    }



 if (ispath('userinfo') ) {
 makemytime(true);
 }

    if (ispath('clients') ) {
    //if (def_filename == "clients.php") {
        setInterval(function(){
            check_update();
        },5000);
    }
    if (ispath('approve') ) {
    //if (def_filename == "clients.php") {
        setInterval(function(){
            check_update();
        },5000);
    }
if (ispath('profile') ) {
    //if (def_filename == "profile.php") {
        setInterval(function(){
            check_update();
        },5000);
    }
    if (ispath('create') ) {
    //if (def_filename == "new.php") {
        setInterval(function(){
            check_update();
        },5000);
    }
if (ispath('notes') ) {
    //if (def_filename == "notes.php") {
        $('#buttons').hide();
        setInterval(function(){
            check_update();
        },5000);

    }
    if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
    //alert(ACTIONPATH);


    $('body').on('click', 'a#more_news', function(event) {
        event.preventDefault();
        var tid=$(this).attr('value');

                                            $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            data: "mode=last_news"+
                                            "&v="+encodeURIComponent($(this).attr('value')),
                                            success: function(html){
                                                $('#last_news').html(html);
                                                $('[data-toggle="tooltip"]').tooltip('hide');
                                                $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                                                makemytime(false);                                            }
                                        });
                                        });







        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=last_news",
            success: function(html){
                $('#last_news').html(html);


                //console.log($('#then').html());
                makemytime(false);



            }
        });
        $('#spinner').show();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=dashboard_t",
            success: function(html){
                $('#dashboard_t').html(html);
                $('#spinner').hide();
                $('[data-toggle="popover"]').popover();
                // $('[data-toggle="tooltip"]').tooltip('hide');
                $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
makemytime(true);
            }
        });



        setInterval(function(){
            check_update_index();


        },5000);

    }


    if (def_filename == "reports.php") {

        $('#reportrange').daterangepicker(
            {
                ranges: {
                    'Сьогодні': [moment(), moment()],
                    'Вчора': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'За тиждень': [moment().subtract('days', 6), moment()],
                    'За 30 днів': [moment().subtract('days', 29), moment()],
                    'За місяць': [moment().startOf('month'), moment().endOf('month')],
                    'Прошлий місяць': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                startDate: moment().subtract('days', 29),
                endDate: moment()
            },
            function(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#d_start').attr('value', start.format('YYYY-MM-DD'));
                $('#d_stop').attr('value', end.format('YYYY-MM-DD'));
            }
        );

    }

if (ispath('helper') ) {
    //if (def_filename == "helper.php") {
        setInterval(function(){
            check_update();
        },5000);

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=list_help",
            success: function(html) {
                $("#help_content").html(html);
            }
        });
    };




    if (ispath('statistics')) {
    //if (def_filename == "stats.php") {
        setInterval(function(){
            check_update();
        },5000);
    }

    if (ispath('users')) {
        setInterval(function(){
            check_update();
        },5000);
    }

    if (ispath('files')) {
        setInterval(function(){
            check_update();
        },5000);
    }


    if (ispath('main_stats')) {
        setInterval(function(){
            check_update();
        },5000);
    }


    if (ispath('list')) {
    //if (def_filename == "list.php") {
        //alert('ok');
        $('[data-toggle="popover"]').popover();

        setInterval(function(){
            var oo=$("#curent_page").attr('value');
            var pt=$("#page_type").attr('value');
            var lt=$("#last_ticket").attr('value');


            check_update();

            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=check_update"+
                    "&type="+encodeURIComponent(pt)+
                    "&last_update="+encodeURIComponent(lt),
                success: function(html){
                    if (html == "no") {
                    }
                    else {
                        if (oo == "null") {
                            window.location = MyHOSTNAME+"list?"+pt;
                        }

                        var new_lu=html;


                        $.ajax({
                            type: "POST",
                            url: MyHOSTNAME+"inc/list_content.inc.php",
                            data: "menu="+encodeURIComponent(pt)+
                                "&page="+encodeURIComponent(oo),
                            success: function(html){
                                $('[data-toggle="tooltip"]').tooltip('hide');
                                $("#content").html(html);
                                $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                            }
                        });



                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=update_list_labels",
                            dataType: "json",
                            success: function(html){
                                if (html) {
                                    $.each(html, function(i, item) {
                                        $('span#label_list_in').html(item.in);
                                        $('span#label_list_out').html(item.out);

                                    });
                                }
                            }
                        });


                        $("#last_ticket").attr('value', new_lu);



                    }

                }
            });









        },5000);
    }

makemytime(true);

 $('body').on('click', 'button#sort_list', function(event) {
 event.preventDefault();
 var pt=$("#page_type").attr('value');
 var st=$(this).attr('value');
 $('#spinner').show();
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=sort_list"+
 "&pt="+encodeURIComponent(pt)+
 "&st="+encodeURIComponent(st),
 success: function() {

 window.location = MyHOSTNAME+"list?"+pt;


 }
 });
 });

 $('body').on('click', 'a#d_label_1', function(event) {
 event.preventDefault();
 var pt=$(this).attr('value2');
  if ($(this).text() != 0){
 var st=$(this).attr('value');
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=sort_list"+
 "&pt="+encodeURIComponent(pt)+
 "&st="+encodeURIComponent(st),
 success: function() {

 window.location = MyHOSTNAME+"list?"+pt;

 }
 });
}
else{
  var st='main';
  $.ajax({
  type: "POST",
  url: ACTIONPATH,
  data: "mode=sort_list"+
  "&pt="+encodeURIComponent(pt)+
  "&st="+encodeURIComponent(st),
  success: function() {

  window.location = MyHOSTNAME+"list?"+pt;

  }
  });
}
 });
 $('body').on('click', 'a#d_label_2', function(event) {
 event.preventDefault();
 var pt=$(this).attr('value2');
 if ($(this).text() != 0){
 var st=$(this).attr('value');
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=sort_list"+
 "&pt="+encodeURIComponent(pt)+
 "&st="+encodeURIComponent(st),
 success: function() {

 window.location = MyHOSTNAME+"list?"+pt;

 }
 });
 }
 else{
   var st='main';
   $.ajax({
   type: "POST",
   url: ACTIONPATH,
   data: "mode=sort_list"+
   "&pt="+encodeURIComponent(pt)+
   "&st="+encodeURIComponent(st),
   success: function() {

   window.location = MyHOSTNAME+"list?"+pt;

   }
   });
 }
 });

 $('body').on('click', 'button#ticket_delete', function(event) {
 event.preventDefault();
 var td=$("#ticket_delete").attr('value');
 bootbox.dialog({
   message: $.i18n('JS_ticket_delete'),
   title: $.i18n('JS_ticket_delete_title'),
   buttons:{
     cancel:{
       label:$.i18n('JS_ticket_delete_cancel'),
       className:'btn-default cancel',
       callback: function(){
       }
     },
     danger:{
       label:$.i18n('TICKET_delete'),
       className:'btn-danger danger',
       callback: function(){
         $.ajax({
         type: "POST",
         url: ACTIONPATH,
         data: "mode=ticket_delete"+
         "&td="+encodeURIComponent(td),
         success: function() {

         window.location = MyHOSTNAME;

         }
         });
  }
}
}
 });
 });


        $('body').on('click', 'button#list_set_ticket', function(event) {
        event.preventDefault();
        var pt=$("#page_type").attr('value');
        var z=$(this).text();
        $('#spinner').show();
                    $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=set_list_count"+
                    "&pt="+encodeURIComponent(pt)+
                    "&v="+encodeURIComponent(z),
                success: function() {

                    window.location = MyHOSTNAME+"list?"+pt;


                }
            });
        });


    $("#fio_find").autocomplete({
        max: 10,
        minLength: 2,
        source: MyHOSTNAME+"inc/json.php?fio",
        focus: function(event, ui) {
            $("#fio_find").val(ui.item.label);
            return false;
        },
        select: function(event, ui) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=find_worker"+
                    "&fio="+encodeURIComponent($("input#fio_find").val()),
                success: function(html) {

                    $("#content_worker").hide().html(html).fadeIn(500);


                }
            });
            return false;
        }
    });
$('body').on('click', 'button#deps_show', function(event) {
 event.preventDefault();
 var u=$(this).attr('value');
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=deps_show"+
 "&id="+u,
 success: function(html) {
 window.location = MyHOSTNAME+"deps";
 }
 });
 });

 $('body').on('click', 'button#deps_hide', function(event) {
 event.preventDefault();
 var u=$(this).attr('value');
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=deps_hide"+
 "&id="+u,
 success: function(html) {
 window.location = MyHOSTNAME+"deps";
 }
 });
 });
    $('body').on('click', 'button#create_new_help', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=create_helper",
            success: function(html) {

                $("#help_content").hide().html(html).fadeIn(500);
                var settingsShow = function() {
                    var showPanel = $(".chosen-select").find('option:selected').attr('id');
                }
                $(".chosen-select").chosen({
                    no_results_text: $.i18n('JS_not_found'),
                    allow_single_deselect: true,
                });
                $(".chosen-select").chosen().change(settingsShow);
                $('#summernote_help').summernote({
                    height: 300,
                    focus: true,
            	    lang: $.i18n('summernote_lang'),
                    onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0], editor, welEditable);
 }
                });

            }
        });



    });
    $('body').on('click', 'a#go_back', function(event) {
        event.preventDefault();
    history.back(1);
});


    $('body').on('click', 'button#action_refer_to', function(event) {
        event.preventDefault();
        var st=$("#action_refer_to").attr('value');


        if (st == '0') {
            $("#refer_to").fadeIn(500);
            $(this).addClass('active').attr('value', '1');
        }
        if (st == '1') {
            $("#refer_to").fadeOut(500);
            $(this).removeClass('active').attr('value', '0');
        }


    });


    $('body').on('click', 'button#dashboard_set_ticket', function(event) {
        event.preventDefault();
	var p=$(this).text();
  $('#spinner').show();
                                        $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            data: "mode=dashboard_t"+
                                            "&p="+encodeURIComponent(p),
                                            success: function(html){

                                                $('#dashboard_t').html(html);
                                                $('#spinner').hide();
                                                $('[data-toggle="popover"]').popover();
                                                // $('[data-toggle="tooltip"]').tooltip('hide');
                                                $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                                                makemytime(true);                                            }
                                        });
                                        });






    $('body').on('click', 'button#save_notes', function(event) {
        event.preventDefault();
        var u=$(this).attr('value');
        var sHTML = $('#summernote').code();
        var data = { 'mode' : 'save_notes', 'hn' : u, 'msg' : sHTML };

        $.ajax({
            type: "POST",
            url: ACTIONPATH,

            data: data,
            success: function(html){
                //console.log(html);
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_list_notes",
                    success: function(html){
                        $('#table_list').html(html);


                        noty({
                            text: $.i18n('note_save'),
                            layout: 'center',
                            type: 'information',
                            timeout: 2000

                        });


                    }
                });
            }
        });

    });
    $("input#input_find").keyup(function() {
    if($(this).val().length > 1) {
    $("#input_find_grp").removeClass('has-error').addClass('has-success');
    }
    else {
    $("#input_find_grp").removeClass('has-success').addClass('has-error');
    }
    });
    $('input#input_find').keydown(function (e) {
        if (e.keyCode == 13) {
            $("button#b_find").click();
        }
    });
$('body').on('click', 'button#b_find', function(event) {
    var z = $("input#input_find").val();
    var valid_find = function(){
    var valid_result = false;
    if ($('input#input_find').val().length == '0'){
      $('#input_er').popover('show');
      $('#input_find_grp').addClass('has-error');
      setTimeout(function(){$("#input_er").popover('hide');},2000);
      valid_result = true;
    }
    return valid_result;
    };
    if (valid_find() == false){
    // $.ajax({
        // type: "GET",
        // url: ACTIONPATH,
        // data:"&t="+z,
        // success: function(html){
          window.location = MyHOSTNAME+"list?t="+z+"&find";
        // }
    // });
  }
    // console.log(z);
})
    $('body').on('click', 'a#to_notes', function(event) {
        event.preventDefault();
        var u=$(this).attr('value');
        var hostadr=get_host_conf();
        var langp=$.i18n('JS_save');
        var langup=$.i18n('JS_pub');

        $('#exampleInputEmail1').attr('value', MyHOSTNAME+"inc/note.php?h="+u);

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_notes"+
                "&hn="+encodeURIComponent(u),
            success: function(html){
                $('#summernote').destroy();

                $('#summernote').html(html);
                $('#buttons').show();

                $('#summernote').summernote({
                    height: 300,
                    focus: true,
                    lang: $.i18n('summernote_lang'),
                    onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0], editor, welEditable);
                     },
                    oninit: function() {

                        var openBtn = '<button id="save_notes" value="'+u+'" type="button" class="btn btn-success btn-sm btn-small" title="'+langp+'" data-event="something" tabindex="-1"><i class="fa fa-check-circle"></i></button>';
                        var saveBtn = '<button id="saveFileBtn" type="button" class="btn btn-warning btn-sm btn-small" title="'+langup+'" data-event="something" tabindex="-1" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-bullhorn"></i> </button>';
                        var fileGroup = '<div class="note-file btn-group">' + openBtn + saveBtn + '</div>';
                        $(fileGroup).prependTo($('.note-toolbar'));

                        $('#save_notes').tooltip({container: 'body', placement: 'bottom'});
                        $('#saveFileBtn').tooltip({container: 'body', placement: 'bottom'});
                    }
                });


            }
        });

    });

    function get_host_conf() {
        var result="";
        var zcode="";
        var url = window.location.href;
        if (url.search("inc") >= 0) {
    zcode="../";
}

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_host_conf",
            async: false,
            success: function(html){


                result=html;
            }
        });

        return (result);

    };

    $('body').on('click', 'button#del_notes', function(event) {
        event.preventDefault();
        var n_id=$(this).attr('value');
        var langp=$.i18n('JS_create');
        var lang_del=$.i18n('JS_del');
        bootbox.confirm(lang_del, function(result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=del_notes"+
                        "&nid="+encodeURIComponent(n_id),
                    success: function(html){
                        //alert(html);
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=get_list_notes",
                            success: function(html){
                                $('#table_list').html(html);
                                $('#summernote').destroy();

                                $('#summernote').html("<div class=\"jumbotron\"><p><center>"+langp+"</center></p></div>");
                                $('#buttons').hide();
                            }
                        });
                    }
                });


            }
            else if (result == false) {}
        });
    });
    $('body').on('click', 'button#create_new_note', function(event) {
        event.preventDefault();
        var langp=$.i18n('JS_save');

        var langup=$.i18n('JS_pub');
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=create_notes",
            success: function(html){

                var u=html;
                var hostadr=get_host_conf();
                $('#exampleInputEmail1').attr('value', MyHOSTNAME+"inc/note.php?h="+u);
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_notes"+
                        "&hn="+encodeURIComponent(u),
                    success: function(html){
                        $('#summernote').destroy();

                        $('#summernote').html(html);
                        $('#buttons').show();

                        $('#summernote').summernote({
                            height: 300,
                            focus: true,
                            lang: $.i18n('summernote_lang'),
                            onImageUpload: function(files, editor, welEditable) {
                            sendFile(files[0], editor, welEditable);
                             },
                            oninit: function() {

                                var openBtn = '<button id="save_notes" value="'+u+'" type="button" class="btn btn-success btn-sm btn-small" title="'+langp+'" data-event="something" tabindex="-1"><i class="fa fa-check-circle"></i></button>';
                                var saveBtn = '<button id="saveFileBtn" type="button" class="btn btn-warning btn-sm btn-small" title="'+langup+'" data-event="something" tabindex="-1" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-bullhorn"></i> </button>';
                                var fileGroup = '<div class="note-file btn-group">' + openBtn + saveBtn + '</div>';
                                $(fileGroup).prependTo($('.note-toolbar'));
                                $('#save_notes').tooltip({container: 'body', placement: 'bottom'});
                                $('#saveFileBtn').tooltip({container: 'body', placement: 'bottom'});

                            }
                        });


                    }
                });










                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_list_notes",
                    success: function(html){
                        $('#table_list').html(html);
                    }
                });
            }
        });







    });





    $('body').on('click', 'button#units_del', function(event) {
        event.preventDefault();
	var ids=$(this).attr('value');
	bootbox.confirm($.i18n('JS_del'), function(result) {
	 if (result == true) {


        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=units_del"+
                "&id="+ids,
            success: function(html) {
                $("#content_units").html(html);

            }
        });
        }
	 });
    });




    $('body').on('click', 'button#do_report', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_report"+
                "&id="+$("#user_report").val()+
                "&s="+$("#d_start").val()+
                "&e="+$("#d_stop").val(),
            success: function(html) {
                $("#content_report").html(html);

            }
        });

    });


    $('body').on('click', 'button#units_add', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=units_add"+
                "&text="+encodeURIComponent($("#units_text").val()),
            success: function(html) {
                $("#content_units").html(html);
                $("#units_text").val('');
            }
        });

    });


    $('body').on('click', 'button#subj_del', function(event) {
        event.preventDefault();
	var ids=$(this).attr('value');
	bootbox.confirm($.i18n('JS_del'), function(result) {
	 if (result == true) {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=subj_del"+
                "&id="+ids,
            success: function(html) {
                $("#content_subj").html(html);

            }
        });
        }
        });
    });


    $('body').on('click', 'button#subj_add', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=subj_add"+
                "&text="+encodeURIComponent($("#subj_text").val()),
            success: function(html) {
                $("#content_subj").html(html);
                $("#subj_text").val('');
            }
        });

    });

$('body').on('click', 'button#files_del', function(event) {
 event.preventDefault();
 var ids=$(this).attr('value');
bootbox.confirm($.i18n('JS_del'), function(result) {
if (result == true) {
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=files_del"+
 "&id="+ids,
 success: function(html) {

 window.location = MyHOSTNAME+"files";

 }
 });
 }


});

});
$('body').on('click', 'button#files_del_comment', function(event) {
    event.preventDefault();
    var ids=$(this).attr('value');
bootbox.confirm($.i18n('JS_del'), function(result) {
if (result == true) {
         $.ajax({
        type: "POST",
        url: ACTIONPATH,
        data: "mode=files_del_comment"+
            "&id="+ids,
        success: function(html) {

        window.location = MyHOSTNAME+"files";

        }
        });
        }


});

});

$('body').on('click', 'button#files_del_upload_comment', function(event) {
    event.preventDefault();
    var ids=$(this).attr('value');
    // console.log(ids);
         $.ajax({
        type: "POST",
        url: ACTIONPATH,
        data: "mode=files_del_comment"+
            "&id="+ids,
        success: function(html) {

        $('tr#'+ids).empty();

        }
        });


});
$('body').on('click', 'button#files_del_upload', function(event) {
    event.preventDefault();
    var ids=$(this).attr('value');
    // console.log(ids);
         $.ajax({
        type: "POST",
        url: ACTIONPATH,
        data: "mode=files_del"+
            "&id="+ids,
        success: function(html) {

        $('tr#'+ids).empty();

        }
        });


});


    $('body').on('click', 'button#deps_del', function(event) {
        event.preventDefault();
	var ids=$(this).attr('value');
	bootbox.confirm($.i18n('JS_del'), function(result) {
	 if (result == true) {
	     $.ajax({
		 type: "POST",
		 url: ACTIONPATH,
		 data: "mode=deps_del"+
		 "&id="+ids,
		 success: function(html) {
		 $("#content_deps").html(html);
                $('a#edit_deps').each(function(i, e) {

                                       $(e).editable({
           inputclass: 'input-sm',
           emptytext: 'пусто',
           params: {
           mode: 'edit_deps'
 }
 });

 });
          }
          });
          }
          if (result == false) {
          console.log('false');
          }
          });
 /*
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=deps_del"+
                "&id="+$(this).attr('value'),
            success: function(html) {
                $("#content_deps").html(html);

            }
        });
       */
    });
    $('body').on('click', 'button#deps_add', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=deps_add"+
                "&text="+encodeURIComponent($("#deps_text").val()),
            success: function(html) {
                $("#content_deps").html(html);
                $("#deps_text").val('');
                $('a#edit_deps').each(function(i, e) {
                                       $(e).editable({
         inputclass: 'input-sm',
         emptytext: 'пусто',
         params: {
         mode: 'edit_deps'
           }
        });
           });
                 }
        });
  });

    $('body').on('click', 'button#posada_add', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=posada_add"+
                "&text="+encodeURIComponent($("#posada_text").val()),
            success: function(html) {
                $("#content_posada").html(html);
                $("#posada_text").val('');
            }
        });

    });
    $('body').on('click', 'button#posada_del', function(event) {
        event.preventDefault();
	var ids=$(this).attr('value');
	bootbox.confirm($.i18n('JS_del'), function(result) {
	 if (result == true) {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=posada_del"+
                "&id="+ids,
            success: function(html) {
                $("#content_posada").html(html);

            }
        });
        }
        });
    });

    $('body').on('click', 'button#editable_enable', function(event) {
        event.preventDefault();
        $('#edit_subj_ticket').editable('toggleDisabled');
        $('#edit_msg_ticket').editable('toggleDisabled');
    });


    $('body').on('click', 'button#action_aprove_yes', function(event) {
        event.preventDefault();
        var table_id = $(this).attr('value');
        var elem = "#table_" + table_id;

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=aprove_yes"+
                "&id="+encodeURIComponent(table_id),
            success: function() {
                $(elem).fadeOut(500);
                check_approve('update');
            }
        });

    });

    $('body').on('click', 'button#action_aprove_no', function(event) {
        event.preventDefault();
        var table_id = $(this).attr('value');
        var elem = "#table_" + table_id;

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=aprove_no"+
                "&id="+encodeURIComponent(table_id),
            success: function() {
                $(elem).fadeOut(500);
                check_approve('update');
            }
        });

    });




    $('body').on('click', 'button#edit_profile_main', function(event) {
        event.preventDefault();


        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=edit_profile_main"+
                "&login="+encodeURIComponent($("#login").val())+
                "&mail="+encodeURIComponent($("#mail").val())+
                "&lang="+encodeURIComponent($("select#lang").val())+
                "&id="+encodeURIComponent($("#edit_profile_main").attr('value')),
            success: function(html) {

                $("#m_info").hide().html(html).fadeIn(500);


            }
        });
    });
    $('body').on('click', 'button#profile_edit_noty', function(event) {
        event.preventDefault();


        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=edit_profile_noty"+
                "&jabber_active_profile="+encodeURIComponent($("select#jabber_active_profile").val())+
                "&jabber_show_profile="+encodeURIComponent($("select#jabber_show_profile").val())+
                "&show_noty_profile="+encodeURIComponent($("select#show_noty_profile").val())+
                "&show_noty="+encodeURIComponent($("select#show_noty").val())+
                "&id="+encodeURIComponent($("#edit_profile_main").attr('value')),
            success: function(html) {

                $("#noty_res").hide().html(html).fadeIn(500);
                setTimeout(function() {$("#noty_res").fadeOut(500);}, 3000);


            }
        });
    });


    $('body').on('click', 'button#edit_profile_pass', function(event) {
        event.preventDefault();


        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=edit_profile_pass"+
                "&old_pass="+encodeURIComponent($("#old_pass").val())+
                "&new_pass="+encodeURIComponent($("#new_pass").val())+
                "&new_pass2="+encodeURIComponent($("#new_pass2").val())+
                "&id="+encodeURIComponent($("#edit_profile_main").attr('value')),
            success: function(html) {
                $("#p_info").hide().html(html).fadeIn(500);


            }
        });
    });



    $('body').on('click', 'button#send_zapit_add', function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=send_zapit_add"+"&"+
                $('#form_approve').serialize()
            ,
            success: function(html) {

                $("#sze_info").hide().html(html).fadeIn(500);





                $("#send_zapit_add").fadeOut(500);

            }
        });
    });


    $('body').on('click', 'button#send_zapit_edit_ok', function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=send_zapit_edit_ok"+"&"+
                $('#form_approve').serialize(),
            success: function(html) {

                $("#sze_info").hide().html(html).fadeIn(500);





                $("#send_zapit_edit_ok").fadeOut(500);

            }
        });
    });



    $('body').on('click', 'button#send_zapit_edit', function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=send_zapit_edit"+"&"+
                $('#form_approve').serialize()
            ,
            success: function(html) {

                $("#sze_info").hide().html(html).fadeIn(500);





                $("#send_zapit_edit").fadeOut(500);

            }
        });
    });

    // ******Проверка наличия обновлений******
    $('body').on('click', 'button#conf_check_update', function(event) {
            event.preventDefault();
            $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=conf_check_update",
            success: function(html){
              $("#conf_check_update").blur();
              $("#check_update").hide().html(html).fadeIn(500);
            }
            });
          });
    // ******Послание обновления******
    $('body').on('click', 'button#conf_system_update', function(event) {
            event.preventDefault();
            $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=conf_system_update",
            success: function(html){
              $("#conf_system_update").blur();
              $("#up_success").hide().html(html).fadeIn(500);
              setTimeout(function() {$('#up_success').children('.alert_conf').fadeOut(500);}, 3000);
            }
            });
          });
          $('body').on('click', 'a#conf_menu', function(event) {
            event.preventDefault();
            $that = $(this);
            $that.parent().find('a').removeClass('active');
            $that.addClass('active');
          });
//conf_edit_main
$('body').on('click', 'button#conf_edit_main', function(event) {
event.preventDefault();
$.ajax({
type: "POST",
url: ACTIONPATH,
data: "mode=conf_edit_main"+
"&name_of_firm="+encodeURIComponent($("input#name_of_firm").val())+
"&title_header="+encodeURIComponent($("input#title_header").val())+
"&hostname="+encodeURIComponent($("input#hostname").val())+
"&mail="+encodeURIComponent($("input#mail").val())+
"&days2arch="+encodeURIComponent($("input#days2arch").val())+
"&first_login="+encodeURIComponent($("#first_login").val())+
"&fix_subj="+encodeURIComponent($("#fix_subj").val())+
"&file_uploads="+encodeURIComponent($("#file_uploads").val())+
"&file_types="+encodeURIComponent($("#file_types").val())+
"&time_zone="+encodeURIComponent($("#time_zone").val())+
"&file_size="+encodeURIComponent($("#file_size").val()*1024*1024),
success: function(html) {
$("#conf_edit_main_res").hide().html(html).fadeIn(500);
setTimeout(function() {$('#conf_edit_main_res').children('.alert').fadeOut(500);}, 3000);
}
});
});
$('body').on('click', 'button#conf_edit_jabber', function(event) {
event.preventDefault();
$.ajax({
type: "POST",
url: ACTIONPATH,
data: "mode=conf_edit_jabber"+
 "&jabber_active="+encodeURIComponent($("#jabber_active").val())+
 "&jabber_server="+encodeURIComponent($("#jabber_server").val())+
 "&jabber_port="+encodeURIComponent($("#jabber_port").val())+
 "&jabber_login="+encodeURIComponent($("#jabber_login").val())+
 "&jabber_pass="+encodeURIComponent($("#jabber_pass").val()),

success: function(html) {
$("#conf_edit_jabber_res").hide().html(html).fadeIn(500);
setTimeout(function() {$('#conf_edit_jabber_res').children('.alert').fadeOut(500);}, 3000);
}
});
});


$('body').on('click', 'button#conf_test_jabber', function(event) {
 event.preventDefault();
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=conf_test_jabber"+
 "&jabber_active="+encodeURIComponent($("#jabber_active").val())+
 "&jabber_server="+encodeURIComponent($("#jabber_server").val())+
 "&jabber_port="+encodeURIComponent($("#jabber_port").val())+
 "&jabber_login="+encodeURIComponent($("#jabber_login").val())+
 "&jabber_pass="+encodeURIComponent($("#jabber_pass").val()),
 success: function(html) {
   $("#conf_test_jabber_res").hide().html(html).fadeIn(500);
   setTimeout(function() {$('#conf_test_jabber_res').children('.alert').fadeOut(500);}, 3000);
 }
 });
 });

 $('body').on('click', 'button#conf_test_jabber_profile', function(event) {
  event.preventDefault();

  $.ajax({
  type: "POST",
  url: ACTIONPATH,
  data: "mode=conf_test_jabber_profile"+
  "&jabber_active="+encodeURIComponent($("select#jabber_active_profile").val()),

  success: function(html) {
    $("#conf_test_jabber_res_profile").hide().html(html).fadeIn(500);
    setTimeout(function() {$('#conf_test_jabber_res_profile').children('.alert').fadeOut(500);}, 3000);
  }
  });
  });



$('body').on('click', 'button#conf_test_mail', function(event) {
 event.preventDefault();
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=conf_edit_mail"+
 "&mail_active="+encodeURIComponent($("#mail_active").val())+
 "&host="+encodeURIComponent($("#host").val())+
 "&port="+encodeURIComponent($("#port").val())+
 "&auth="+encodeURIComponent($("#auth").val())+
 "&auth_type="+encodeURIComponent($("#auth_type").val())+
 "&username="+encodeURIComponent($("#username").val())+
 "&password="+encodeURIComponent($("#password").val())+
 "&from="+encodeURIComponent($("#from").val())+
 "&type="+encodeURIComponent($("#mail_type").val()),
 success: function(html) {

 $("#conf_edit_mail_res").hide().html(html).fadeIn(500);
 setTimeout(function() {$('#conf_edit_mail_res').children('.alert').fadeOut(500);}, 3000);
 $.ajax({
 type: "POST",
 url: ACTIONPATH,
 data: "mode=conf_test_mail",
 success: function(html) {
  $('#conf_test_mail_res').html(html);
 }
 });


 }
 });



 });


//conf_edit_mail
$('body').on('click', 'button#conf_edit_mail', function(event) {
event.preventDefault();
$.ajax({
type: "POST",
url: ACTIONPATH,
data: "mode=conf_edit_mail"+
"&mail_active="+encodeURIComponent($("#mail_active").val())+
"&host="+encodeURIComponent($("#host").val())+
"&port="+encodeURIComponent($("#port").val())+
"&auth="+encodeURIComponent($("#auth").val())+
"&auth_type="+encodeURIComponent($("#auth_type").val())+
"&username="+encodeURIComponent($("#username").val())+
"&password="+encodeURIComponent($("#password").val())+
"&from="+encodeURIComponent($("#from").val())+
"&type="+encodeURIComponent($("#mail_type").val()),
success: function(html) {
$("#conf_edit_mail_res").hide().html(html).fadeIn(500);
setTimeout(function() {$('#conf_edit_mail_res').children('.alert').fadeOut(500);}, 3000);
}
});
});

    $('body').on('click', 'button#do_find', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=find_worker"+
                "&fio="+encodeURIComponent($("input#fio_find").val()),
            success: function(html) {

                $("#content_worker").hide().html(html).fadeIn(500);


            }
        });
    });




    $('body').on('click', 'button#do_comment', function(event) {
        event.preventDefault();
        var tid=$(this).attr('value');
        var usr=$(this).attr('user');
        var m=$("textarea#msg").val().length;




        var error_code=0;
        if (m == 0) {
            error_code=1;
            $("textarea#msg").popover('show');
            $("#for_msg").addClass('has-error');

        }

        if (error_code == 0) {




            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=add_comment"+
                    "&user="+encodeURIComponent(usr)+
                    "&textmsg="+encodeURIComponent(($("textarea#msg").val()))+
                    "&hashname="+encodeURIComponent($("#hashname").val())+
                    "&tid="+tid,
                success: function(html) {

                    $("#comment_content").html(html);
                    $("textarea#msg").val('');
                    $("table tbody.files").empty();
makemytime(true);


  var scroll    = $('#comment_body');
  var height = scroll[0].scrollHeight;
  scroll.scrollTop(height);
console.log(height);

                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=log_update"+
                    "&tid="+tid,
                    success: function(html){
                      $("#log_content").html(html);
                      makemytime(true);
                    }
                  });
                  send_jabber();
                }
            });


        }

    });
$("body").on("click", "a#select_init_user", function(event) {
            event.preventDefault();
            //console.log($(this).attr("param-hash"));
            //var n = $("#user_login_name").val();
	    var n = user_login_name;
            //var r = $("#user_init_id2").val();
	    var r = user_init_id2;
            $("#fio").popover("hide");
            $("#for_fio").removeClass("has-error").addClass("has-success");
            $("#user_info").hide().fadeIn(500);
            $("#alert_add").hide();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_client_from_new_t&get_client_info=" + r,
                success: function(html) {
                      $("#client_id_param").val(r);
                      $("#fio").val(n);
                      $("#user_info").hide().html(html).fadeIn(500);
                      $('#edit_login').editable({
                          inputclass: 'input-sm',
                          emptytext: 'пусто'
                      });
                      $('#edit_posada').editable({
                          inputclass: 'input-sm',
                          emptytext: 'пусто',
                          mode: 'popup',
                          showbuttons: false
                      });				$('#edit_unit').editable({
                          inputclass: 'input-sm',
                          emptytext: 'пусто',
                          mode: 'popup',
                          showbuttons: false
                      });				$('#edit_tel').editable({
                          inputclass: 'input-sm',
                          emptytext: 'пусто'
                      });				$('#edit_adr').editable({
                          inputclass: 'input-sm',
                          emptytext: 'пусто'
                      });				$('#edit_mail').editable({
                          inputclass: 'input-sm',
                          emptytext: 'пусто'
                      });
                      $('#for_fio').addClass('has-success');
                      $("#status_action").val('edit');
                      makemytime(true);
                }
            })
        });

    $('body').on('click', 'button#prio_low', function(event) {
        event.preventDefault();
        $('button#prio_low').addClass('active');
        $('button#prio_normal').removeClass('active');
        $('button#prio_high').removeClass('active');
        $('i#lprio_low').addClass('fa fa-check');
        $("i#lprio_norm").removeClass("fa fa-check");
        $("i#lprio_high").removeClass("fa fa-check");
        $("#prio").val('0');
    });

    $('body').on('click', 'button#prio_normal', function(event) {
        event.preventDefault();
        $('button#prio_low').removeClass('active');
        $('button#prio_normal').addClass('active');
        $('button#prio_high').removeClass('active');
        $('i#lprio_low').removeClass('fa fa-check');
        $("i#lprio_norm").addClass("fa fa-check");
        $("i#lprio_high").removeClass("fa fa-check");
        $("#prio").val('1');
    });

    $('body').on('click', 'button#prio_high', function(event) {
        event.preventDefault();
        $('button#prio_low').removeClass('active');
        $('button#prio_normal').removeClass('active');
        $('button#prio_high').addClass('active');
        $('i#lprio_low').removeClass('fa fa-check');
        $("i#lprio_norm").removeClass("fa fa-check");
        $("i#lprio_high").addClass("fa fa-check");
        $("#prio").val('2');
    });

        $("input#login_user").keyup(function() {
        if($(this).val().length > 3) {

            $("#login_user_grp").removeClass('has-error').addClass('has-success');
	    //$("#errors").val('false');
	    my_errors.login = false;

 $.ajax({
 type: "POST",
 dataType: "json",
 url: ACTIONPATH,
 data: "mode=check_login"+
 "&login="+$(this).val(),
 success: function(html) {
 $.each(html, function(i, item) {
 if (item.check_login_status == true) {
 $("#login_user_grp").removeClass('has-error').addClass('has-success');
 //$("#errors").val('false');
 my_errors.login = false;
 }
 else if (item.check_login_status == false) {
 $("#login_user_grp").addClass('has-error');
 //$("#errors").val('true');
 my_errors.login = true;
 }
 }
 );
 //console.log(html);
}

 });
        } else {

            $("#login_user_grp").addClass('has-error');
            //$("#errors").val('true');
              my_errors.login = true;
        }
    });

 $("input#fio_user").keyup(function() {
 if($(this).val().length > 3) {
 //$("#errors").val('false');
 my_errors.fio = false;
 $("#fio_user_grp").removeClass('has-error').addClass('has-success');
 }
 else {
 //$("#errors").val('true');
   my_errors.fio = true;
 $("#fio_user_grp").removeClass('has-success').addClass('has-error');
 }
 });
 $("input#exampleInputPassword1").keyup(function() {
 if($(this).val().length > 3) {
 //$("#errors").val('false');
   my_errors.pass = false;
 $("#pass_user_grp").removeClass('has-error').addClass('has-success');
 }
 else {
 //$("#errors").val('true');
   my_errors.pass = true;
 $("#pass_user_grp").removeClass('has-success').addClass('has-error');
 }
 });


    $('body').on('click', 'button#create_user', function(event) {
        event.preventDefault();
        if ($("#fio_user").val().length < 3) {
	    my_errors.fio = true;
	    $("#fio_user_grp").addClass('has-error');
	}

	if ($("#exampleInputPassword1").val().length < 3) {
	    my_errors.login = true;
	    $("#pass_user_grp").addClass('has-error');
	}

	if ($("#login_user").val().length < 3) {
	    my_errors.pass = true;
	    $("#login_user_grp").addClass('has-error');

	}
        //var er=$("#errors").val();
	var er = my_errors.login || my_errors.fio || my_errors.pass;
	//console.debug(my_errors);
	if (er == false) {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=add_user"+
                "&fio="+encodeURIComponent($("#fio_user").val())+
                "&login="+encodeURIComponent($("#login_user").val())+
                "&pass="+encodeURIComponent($("#exampleInputPassword1").val())+
                "&unit="+encodeURIComponent($("#my-select").val())+
                "&priv="+encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val())+
                "&mess="+encodeURIComponent($("textarea#mess").val())+
                "&lang="+encodeURIComponent($('select#lang').val())+
                "&jabber_active_client="+encodeURIComponent($('select#jabber_active_client').val())+
                "&priv_add_client="+encodeURIComponent($("#priv_add_client").prop('checked'))+
                "&priv_edit_client="+encodeURIComponent($("#priv_edit_client").prop('checked'))+
                "&user_add_client="+encodeURIComponent($("#user_add_client").prop('checked'))+
                "&admin_client="+encodeURIComponent($("#admin_client").prop('checked'))+
                "&jabber="+encodeURIComponent($("#jabber").val()) +
                "&mail="+encodeURIComponent($("#mail").val()),
            success: function(html) {

                window.location = MyHOSTNAME+"users?adduser&ok";

            }
        });
        }
        else {
            console.log('error');
            $("html, body").animate({ scrollTop: 0 }, "slow");
        }
    });

    $('body').on('click', 'button#edit_helper', function(event) {
        event.preventDefault();
        var hn=$(this).val();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=edit_helper"+
                "&hn="+encodeURIComponent(hn),
            success: function(html) {
                $("#help_content").html(html);
                var settingsShow = function() {
                    var showPanel = $(".chosen-select").find('option:selected').attr('id');
                }
                $(".chosen-select").chosen({
                    no_results_text: $.i18n('JS_not_found'),
                    allow_single_deselect: true,
                });
                $(".chosen-select").chosen().change(settingsShow);
                $('#summernote_help').summernote({
                    height: 300,
                    focus: true,
                    lang: $.i18n('summernote_lang'),
                    onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0], editor, welEditable);
            }
                });
            }
        });
    });
    $('body').on('click', 'button#del_helper', function(event) {
        event.preventDefault();
        var hn=$(this).val();

        var langdel= $.i18n('JS_del');

        bootbox.confirm(langdel, function(result) {
            if (result == true) {        $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=del_help"+
                    "&hn="+hn,
                success: function(html) {

                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=list_help",
                        success: function(html) {
                            $("#help_content").html(html);
                        }
                    });

                }
            });
            }
        });






    });
//do_save_help
    $('body').on('click', 'button#do_save_help', function(event) {
        event.preventDefault();
        var sHTML = $('#summernote_help').code();
        var hn = $(this).val();
        var u=$("#u").chosen().val();

        var lang_unit= $.i18n('JS_unit');
        var lang_probl= $.i18n('JS_probl');
        var t=$("#t").val();
        var data = { 'mode' : 'do_save_help', 'u' : u, 't' : t, 'msg' : sHTML, 'hn': hn };

        var error_code=0;
        if (u == null) {
            error_code=1;
            noty({
                text: lang_unit,
                layout: 'center',
                type: 'information',
                timeout: 2000

            });
        }

        if ($("#t").val().length == 0 ) { error_code=1;
            noty({
                text: lang_probl,
                layout: 'center',
                type: 'information',
                timeout: 2000

            });
        }

        if (error_code == 0) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: data,
                success: function(html) {


                    window.location = MyHOSTNAME+"helper";

                }
            });
        }
    });


    $('body').on('click', 'button#do_create_help', function(event) {
        event.preventDefault();
        var sHTML = $('#summernote_help').code();

        var u=$("#u").chosen().val();
        var lang_unit= $.i18n('JS_unit');
        var lang_probl= $.i18n('JS_probl');

        var t=$("#t").val();
        var data = { 'mode' : 'do_create_help', 'u' : u, 't' : t, 'msg' : sHTML };

        var error_code=0;

        //alert (u);
        if (u == null) {
            error_code=1;
            noty({
                text: lang_unit,
                layout: 'center',
                type: 'information',
                timeout: 2000

            });
        }

        if ($("#t").val().length == 0 ) { error_code=1;
            noty({
                text: lang_probl,
                layout: 'center',
                type: 'information',
                timeout: 2000

            });
        }

        if (error_code == 0) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: data,
                success: function(html) {


                    window.location = MyHOSTNAME+"helper";

                }
            });
        }
    });
//find_helper
    $("input#find_helper").keyup(function() {
        var t=$(this).val();



        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=find_help"+
                "&t="+t,
            success: function(html) {
                $("#help_content").html(html);
            }
        });


    });


    $("input#fio_find_admin").keyup(function() {
        var t=$(this).val(),
            t_l=$(this).val().length;


        console.log(t_l);

        if (t_l > 0 ) {
            $('#example_workers').hide();
        }
        if (t_l == 0 ) {
            $('#example_workers').show();
        }

        $.ajax({
            type: "POST",
            url: MyHOSTNAME+"inc/workers.inc.php",
            data: "menu=list"+
                "&page=1"+
                "&t="+t,
            success: function(html) {
                $("#content_worker").html(html);


            }
        });


    });


    $('body').on('click', 'button#edit_user', function(event) {
        event.preventDefault();
        var usid = $(this).attr('value');
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=edit_user"+
                "&fio="+encodeURIComponent($("#fio_edit").val())+
                "&login="+encodeURIComponent($("#login").val())+
                "&pass="+encodeURIComponent($("#exampleInputPassword1").val())+
                "&unit="+encodeURIComponent($("#my-select").val())+
                "&priv="+encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val())+
                "&status="+encodeURIComponent($("#lock").val())+
                "&mess="+encodeURIComponent($("textarea#mess").val())+
                "&lang="+encodeURIComponent($('select#lang').val())+
                "&jabber_active_client="+encodeURIComponent($('select#jabber_active_client').val())+
                "&jabber_show_edit="+encodeURIComponent($('select#jabber_show_edit').val())+
                "&show_noty_edit="+encodeURIComponent($('select#show_noty_edit').val())+
                "&show_noty="+encodeURIComponent($('select#show_noty').val())+
                "&mail="+encodeURIComponent($("#mail").val())+
                "&jabber="+encodeURIComponent($("#jabber").val())+
                "&priv_add_client="+encodeURIComponent($("#priv_add_client").prop('checked'))+
                "&priv_edit_client="+encodeURIComponent($("#priv_edit_client").prop('checked'))+
                "&admin_client="+encodeURIComponent($("#admin_client").prop('checked'))+
                "&idu="+encodeURIComponent(usid),
            success: function(html) {
                //alert(html);
                window.location = MyHOSTNAME+"users?edit="+usid+"&ok";

            }
        });
    });



    $('body').on('click', 'button#save_edit_ticket', function(event) {
        event.preventDefault();
         var s=$('#subj').val(),
         m=$('#msg_up').val(),
	 p=$('#prio').val(),
         t_hash=$('#ticket_hash').val();
                $.ajax({
                       type: "POST",
                       url: ACTIONPATH,
                       data: "mode=save_edit_ticket"+
                             "&t_hash="+t_hash+
                             "&subj="+encodeURIComponent(s)+
                             "&prio="+encodeURIComponent(p)+
                             "&msg="+encodeURIComponent(m),
                             success: function(html){
                             //console.log(html);
                             send_jabber();
                             $('#myModal').modal('hide');
                             //$(elem).removeClass().addClass('success', 1000);


 }
 });


 });
              $('a#edit_deps').each(function(i, e) {
              $(e).editable({
                  inputclass: 'input-sm',
                  emptytext: 'пусто',
                  params: {
                  mode: 'edit_deps'
}
});
});
    $('body').on('click', 'button#action_list_ok', function(event) {
        event.preventDefault();
        var status_ll = $(this).attr('status');
        var tr_id = $(this).attr('value');
        var elem = '#tr_' + tr_id;
        var us=$(this).attr('user');
        var pt=$("#page_type").attr('value');
        var ls=$("#list_sort").attr('value');
        var oo=$("#curent_page").attr('value');

        if (status_ll == "ok") {
            $(this).attr("status", "unok");
            $(this).html('<i class=\"fa fa-check-circle-o\"></i>');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=status_no_ok"+
                    "&tid="+tr_id+
                    "&user="+encodeURIComponent(us),
                success: function(){

                    $(elem).removeClass().addClass('success', 1000);
                    send_jabber();
                    if (ls != ''){
                    // window.location = MyHOSTNAME+"list?"+pt;
                    $("#spinner").fadeIn(300);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME+"inc/list_content.inc.php",
                        data: "menu="+encodeURIComponent(pt)+
                            "&page="+encodeURIComponent(oo),
                        success: function(html){
                            $('[data-toggle="tooltip"]').tooltip('hide');
                            $("#content").html(html);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                        }
                    });
                    }


                }
            });
        }
        if (status_ll == "unok") {
            $(this).attr("status", "ok");
            $(this).html('<i class=\"fa fa-circle-o\"></i>');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=status_ok"+
                    "&tid="+tr_id+
                    "&user="+encodeURIComponent(us),
                success: function(){

                    $(elem).removeClass('success', 1000);
                    send_jabber();
                    if (ls != ''){
                    // window.location = MyHOSTNAME+"list?"+pt;
                    $("#spinner").fadeIn(300);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME+"inc/list_content.inc.php",
                        data: "menu="+encodeURIComponent(pt)+
                            "&page="+encodeURIComponent(oo),
                        success: function(html){
                            $('[data-toggle="tooltip"]').tooltip('hide');
                            $("#content").html(html);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                        }
                    });
                    }


                }
            });
        }


    });


    $('body').on('click', 'button#action_arch_now', function(event) {
        event.preventDefault();

        var tr_id = $(this).attr('value');
        var elem = '#tr_' + tr_id;
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=arch_now"+
                "&tid="+tr_id,
            success: function(){

                $(elem).fadeOut(500);


            }
        });

    });
    $('body').on('click', 'button#action_list_lock', function(event) {
        event.preventDefault();

        var status_ll = $(this).attr('status');
        var tr_id = $(this).attr('value');
        var elem = '#tr_' + tr_id;
        var us=$(this).attr('user');
        var pt=$("#page_type").attr('value');
        var ls=$("#list_sort").attr('value');
        var oo=$("#curent_page").attr('value');


        if (status_ll == "lock") {

            $(this).attr("status", "unlock");
            $(this).html('<i class=\"fa fa-lock\"></i>');
            // $(elem + " > td > center > div > button#action_list_ok").removeAttr('disabled');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=lock"+
                    "&tid="+tr_id+
                    "&user="+encodeURIComponent(us),
                success: function(){
                    $(elem).removeClass().addClass('warning', 1000);
                    send_jabber();
                    if (ls != ''){
                    // window.location = MyHOSTNAME+"list?"+pt;
                    $("#spinner").fadeIn(300);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME+"inc/list_content.inc.php",
                        data: "menu="+encodeURIComponent(pt)+
                            "&page="+encodeURIComponent(oo),
                        success: function(html){
                            $('[data-toggle="tooltip"]').tooltip('hide');
                            $("#content").html(html);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                        }
                    });
                    }
                }
            });

        }

        if (status_ll == "unlock") {


            $(this).attr("status", "lock");
            $(this).html('<i class=\"fa fa-unlock\"></i>');
            $(elem + " > td > center > div > button#action_list_ok").attr('disabled', "disabled");




            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=unlock"+
                    "&tid="+tr_id,
                success: function(){

                    $(elem).removeClass('warning', 1000);
                    send_jabber();
                    if (ls != ''){
                    // window.location = MyHOSTNAME+"list?"+pt;
                    $("#spinner").fadeIn(300);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME+"inc/list_content.inc.php",
                        data: "menu="+encodeURIComponent(pt)+
                            "&page="+encodeURIComponent(oo),
                        success: function(html){
                            $('[data-toggle="tooltip"]').tooltip('hide');
                            $("#content").html(html);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                        }
                    });
                    }

                }
            });




        }


    });





    $('body').on('click', 'button#action_ok', function(event) {
        event.preventDefault();
        var status_lock=$("button#action_ok").attr('status');
        var ok_val=$("button#action_ok").attr("value");
        var ok_val_tid=$("button#action_ok").attr("tid");
        var lang_ok= $.i18n('JS_ok');
        if (status_lock == 'ok') {
            $("button#action_ok").attr('status', "no_ok").html("<i class=\"fa fa-check\"></i> "+lang_ok);
            // $("button#action_lock").removeAttr('disabled');
            //$("button#action_refer_to").removeAttr('disabled');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=status_ok"+
                    "&tid="+ok_val_tid+
                    "&user="+encodeURIComponent(ok_val),
                success: function(html){
                    $('#work_timer').attr('value', 'true');
                    intr = setInterval(gotimer_worker, 1000);

                    $("#msg").hide().html(html).fadeIn(500);
                    setTimeout(function() {$('#msg').children('.alert').fadeOut(500);}, 3000);
                    send_jabber();


                }
            });
        }
        if (status_lock == 'no_ok') {
            var lang_nook= $.i18n('JS_no_ok');
            // var t = $('#work_timer > #f').attr('datetime');
            $("button#action_lock").attr('disabled', "disabled");
            $("button#action_refer_to").attr('disabled', "disabled");
            $("button#action_ok").attr('status', "ok").html("<i class=''></i> "+lang_nook);
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=status_no_ok"+
                    "&tid="+ok_val_tid+
                    // "&t="+encodeURIComponent(t)+
                    "&user="+encodeURIComponent(ok_val),
                success: function(html){

                    clearInterval(intr);

                    $("#msg").hide().html(html).fadeIn(500);
                    setTimeout(function() {$('#msg').children('.alert').fadeOut(500);}, 3000);
                    send_jabber();


                }
            });

        }



    });


    $('body').on('click', 'button#action_lock', function(event) {
        event.preventDefault();

        var lock_val=$("button#action_lock").attr("value");
        var lock_val_tid=$("button#action_lock").attr("tid");
        var status_lock=$("button#action_lock").attr('status');
        var permitok=$("button#action_lock").attr("permitok");
        var ok_lock=$("button#action_familiar").attr("unlok");
        var lang_unlock= $.i18n('JS_unlock');
        if (status_lock == 'lock') {
            $("button#action_refer_to").attr('disabled', "disabled");
            // if (permitok != '1'){
            // $("button#action_ok").removeAttr('disabled');
            // }
            // if ((permitok == '1') && (ok_lock == 'yes')){
            //   $("button#action_ok").removeAttr('disabled');
            // }
            $("button#action_lock").attr('status', "unlock").html("<i class='fa fa-unlock'></i> "+lang_unlock);
            $("#msg_e").hide();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=lock"+
                    "&tid="+lock_val_tid+
                    "&user="+encodeURIComponent(lock_val),
                success: function(html){
                    $('#work_timer').attr('value', 'true');
                    intr = setInterval(gotimer_worker, 1000);

                    $("#msg").hide().html(html).fadeIn(500);
                    setTimeout(function() {$('#msg').children('.alert').fadeOut(500);}, 3000);
                    send_jabber();


                }
            });

        }
        if (status_lock == 'unlock') {
            $("#msg_e").hide();
            var lang_lock= $.i18n('JS_lock');
            var permitok=$("button#action_lock").attr("permitok");
            var ok_lock=$("button#action_familiar").attr("unlok");
            // var t = $('#work_timer > #f').attr('datetime');
            // if (permitok != '1'){
            // $("button#action_refer_to").removeAttr('disabled');
            // }
            // if ((permitok == '1') && (ok_lock == 'yes')){
            //   $("button#action_refer_to").removeAttr('disabled');
            // }
            $("button#action_ok").attr('disabled', "disabled");
            $("button#action_lock").attr('status', "lock").html("<i class='fa fa-lock'></i> "+lang_lock);
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=unlock"+
                    "&tid="+lock_val_tid+
                    // "&t="+encodeURIComponent(t)+
                    "&user="+encodeURIComponent(lock_val),
                success: function(html){
                    $('#work_timer').attr('value', 'false');
                    //intr= setInterval(gotimer_worker,1000);
                    clearInterval(intr);

                    $("#msg").hide().html(html).fadeIn(500);
                    setTimeout(function() {$('#msg').children('.alert').fadeOut(500);}, 3000);
                    send_jabber();


                }
            });
        }




    });

    $('body').on('click', 'button#action_familiar', function(event) {
        event.preventDefault();
        var fam_val=$("button#action_familiar").attr("value");
        var fam_val_tid=$("button#action_familiar").attr("tid");
        var permitok=$("button#action_familiar").attr("permitok");
        var ok_lock=$("button#action_familiar").attr("unlok");
        $("button#action_familiar").attr('disabled', "disabled");

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=familiar"+
            "&tid="+fam_val_tid+
            "&user="+encodeURIComponent(fam_val),
            success: function(html){

              $.ajax({
                  type: "POST",
                  url: ACTIONPATH,
                  data: "mode=unlock_ok"+
                  "&tid="+fam_val_tid+
                  "&p="+encodeURIComponent(permitok),
                  success: function(data){
                    if ((data === '0') && (ok_lock === 'yes')){
                      $("button#action_ok").removeAttr('disabled');
                    }
                  }
              });

              $("#msg").hide().html(html).fadeIn(500);
              setTimeout(function() {$('#msg').children('.alert').fadeOut(500);}, 3000);
              send_jabber();

            }
        });
      });


    $('body').on('click', 'button#ref_ticket', function(event) {
        event.preventDefault();

        var to=$("select#t_to").val();
        var tou;
        if( $("select#t_users_do").val() === "" ) {
            tou = '0';
        }
        if( $("select#t_users_do").val() != "" ) {
        	  tou = $("select#t_users_do").val();
        }
        // var tou=$("select#t_users_do").val();
        var tom=$("#msg1").val();

        var error_code=0;
        if (to == '0') {
            error_code=1;

            $('#t_for_to').popover('show');
            $('#t_for_to').addClass('has-error');

        }

        if (error_code == 0) {
            var pp=$("button#ref_ticket").attr("value");

            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                async:false,
                data: "mode=update_to"+
                    "&ticket_id="+pp+
                    "&to="+encodeURIComponent(to)+
                    "&tou="+encodeURIComponent(tou)+
                    "&tom="+encodeURIComponent(tom),
                success: function(html){
                  console.log(tou);
                        send_jabber();

                        $("#ccc").hide().html(html).fadeIn(500);
                        window.location = MyHOSTNAME+"list?in";

                    }
            });
          }

    });




    $("select#t_users_do").change(function() {

        var p=$('select#t_users_do').val();
        var t=$('select#t_to').val();



        if (t == 0 ) {
            if (p != 0 ) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_unit_id"+
                        "&uid="+p,
                    success: function(html){




                        $("select#t_to [value='"+html+"']").attr("selected", "selected");
                        $('select#t_to').trigger('chosen:updated');
                        $('#t_for_to').popover('hide');
                        $('#t_for_to').removeClass('has-error');
                        $('#t_for_to').addClass('has-success');



                    }
                });
            }
            if (p == 0 ) {
                $("select#t_to").find('option:selected').removeAttr("selected");
                $('select#t_to').trigger('chosen:updated');
            }
        }


    });



    // $("select#users_do").change(function() {
    //
    //     var p=$('select#users_do').val();
    //     var t=$('select#to').val();
    //
    //
    //
    //     if (t == 0 ) {
    //         if (p != 0 ) {
    //             $.ajax({
    //                 type: "POST",
    //                 url: ACTIONPATH,
    //                 data: "mode=get_unit_id"+
    //                     "&uid="+p,
    //                 success: function(html){
    //
    //
    //
    //
    //                     $("select#to [value='"+html+"']").attr("selected", "selected");
    //                     $('select#to').trigger('chosen:updated');
    //                     $('#for_to').popover('hide');
    //                     $('#for_to').removeClass('has-error');
    //                     $('#for_to').addClass('has-success');
    //
    //
    //
    //                 }
    //             });
    //         }
    //         if (p == 0 ) {
    //             $("select#to").find('option:selected').removeAttr("selected");
    //             $('select#to').trigger('chosen:updated');
    //         }
    //     }
    //
    //
    // });




    $("select#t_to").change(function() {
        var i=$('select#t_to').val();
        if ( $('select#t_to').val() != 0 ){

            $('#t_for_to').popover('hide');
            $('#t_for_to').removeClass('has-error');
            $('#t_for_to').addClass('has-success');
            createuserslist(i, 't_users_do');
        }
        else {

            $('#t_for_to').popover('show');
            $('#t_for_to').addClass('has-error');

        }

    });

$('body').on('click', 'button#print_now', function(event) {
        event.preventDefault();

       window.print();

        });
if (ispath('create') ) {
//if (def_filename == "new.php") {
$('body').on('click', 'button#reset_ticket', function(event) {
        event.preventDefault();
        window.location = MyHOSTNAME+"create";
        });
        $('#deadline').datetimepicker({
            locale: 'ru',
            format: 'YYYY-MM-DD HH:mm',
            sideBySide:true,
            allowInputToggle:false,
            focusOnShow:true,
            useCurrent:false,
            toolbarPlacement: 'bottom',
            showTodayButton: true,
            showClear: true,
            showClose:true,
            tooltips:(function(){})
        });
        $('#deadline').on('dp.change', function(e) {
        if (e.oldDate === null) {
            $(this).data('DateTimePicker').date(new Date(e.date._d.setHours(23, 59)));
        }
    });
function check_form_ticket(){

     var z=$("#username").text();
        var s=$("#subj").val();
        var to=$("select#to").val();
        var m=$("#msg").val().length;


        var error_code=0;


if ($('#s_start').length) {


 //if ($(this).)
 $('button#start_upload').popover('show');


 error_code=1;

}
if (!$('#s_start').length) {


 //if ($(this).)
 $('button#start_upload').popover('hide');


 error_code=0;

}

        if ($('#fio').val().length == 0){
            error_code=1;

            $('#fio').popover('show');
            $('#for_fio').addClass('has-error');
	    setTimeout(function(){$("#fio").popover('hide');},2000);
        }

        if (to == '0') {
            error_code=1;

            $('#dsd').popover('show');
            $('#for_to').addClass('has-error');
	    setTimeout(function(){$("#dsd").popover('hide');},2000);

        }

        if (s == 0) {
            error_code=1;
            $("#subj").popover('show');
            $("#for_subj").addClass('has-error');
	    setTimeout(function(){$("#subj").popover('hide');},2000);

        }
        if (m == 0) {
            error_code=1;
            $("#msg").popover('show');
            $("#for_msg").addClass('has-error');
	    setTimeout(function(){$("#msg").popover('hide');},2000);

        }
    return error_code;
}


function enter_ticket() {
	    var status_action=$("#status_action").val();
		var u_do;
            if (status_action =='add') {

                //uploadObj.startUpload();
$('#enter_ticket').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
$('#reset_ticket').prop('disabled', true);

if( $("#users_do").val() === '' ) {
	u_do='0';
}
if( $("#users_do").val() === null ) {
    u_do='0';
}
if( $("#users_do").val() != null ) {
	u_do=$("#users_do").val();
}


                $.ajax({
                    type: "POST",
                    //async: false,
                    url: ACTIONPATH,
                    data: "mode=add_ticket"+
                        "&type_add=add"+
                        "&fio="+encodeURIComponent($("#username").text())+
                        "&tel="+encodeURIComponent($("#new_tel").text())+
                        "&login="+encodeURIComponent($("#new_login").text())+
                        "&pod="+encodeURIComponent($("#new_unit").text())+
                        "&adr="+encodeURIComponent($("#new_adr").text())+
                        "&tel="+encodeURIComponent($("#new_tel").text())+
                        "&mail="+encodeURIComponent($("#new_mail").text())+
                        "&posada="+encodeURIComponent($("#new_posada").text())+
                        "&user_init_id="+encodeURIComponent($("#user_init_id").val())+
                        "&user_do="+encodeURIComponent(u_do)+
                        "&subj="+encodeURIComponent($("#subj").val())+
                        "&msg="+encodeURIComponent($("#msg").val())+
                        "&unit_id="+encodeURIComponent($("#to").val())+
                        "&prio="+encodeURIComponent($("#prio").val())+
                        "&confirm="+encodeURIComponent($("#confirm").prop('checked'))+
                        "&deadline_t="+encodeURIComponent($("#deadline_t").val())+
                        "&hashname="+encodeURIComponent($("#hashname").val()),
                    success: function(html) {


                        //window.location = "new.php?ok&h="+html;
			window.location = MyHOSTNAME+"create/?ok&h="+html+"&povtor="+$("[name=povtor]").prop("checked");
                    }
                });




            }


            if (status_action =='edit') {
                //uploadObj.startUpload();
                $('#enter_ticket').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
		$('#reset_ticket').prop('disabled', true);

if( $("#users_do").val() === '' ) {
    u_do='0';
}
if( $("#users_do").val() === null ) {
    u_do='0';
}
if( $("#users_do").val() != null ) {
	u_do=$("#users_do").val();
}

                $.ajax({
                    type: "POST",
                    //async: false,
                    url: ACTIONPATH,
                    data: "mode=add_ticket"+
                        "&type_add=edit"+
                        "&client_id_param="+encodeURIComponent($("#client_id_param").val())+
                        "&tel="+encodeURIComponent($("#edit_tel").text())+
                        "&login="+encodeURIComponent($("#edit_login").text())+
                        "&pod="+encodeURIComponent($("#edit_unit").text())+
                        "&adr="+encodeURIComponent($("#edit_adr").text())+
                        "&tel="+encodeURIComponent($("#edit_tel").text())+
                        "&mail="+encodeURIComponent($("#edit_mail").text())+
                        "&posada="+encodeURIComponent($("#edit_posada").text())+
                        "&user_init_id="+encodeURIComponent($("#user_init_id").val())+
                        "&user_do="+encodeURIComponent(u_do)+
                        "&subj="+encodeURIComponent($("#subj").val())+
                        "&msg="+encodeURIComponent($("#msg").val())+
                        "&unit_id="+encodeURIComponent($("#to").val())+
                        "&prio="+encodeURIComponent($("#prio").val())+
                        "&confirm="+encodeURIComponent($("#confirm").prop('checked'))+
                        "&deadline_t="+encodeURIComponent($("#deadline_t").val())+
                        "&hashname="+encodeURIComponent($("#hashname").val()),
                    success: function(html) {


                        //console.log(html);
			window.location = MyHOSTNAME+"create/?ok&h="+html+"&povtor="+$("[name=povtor]").prop("checked");
                    }
                });




            }





}

    /*
    var lang_dd= $.i18n('TICKET_file_upload_msg');

    var uploadObj = $("#fileuploader").uploadFile({
	allowedTypes: "jpeg,jpg,png,gif,doc,docx,xls,xlsx,rtf,pdf,zip,bmp",
        url: MyHOSTNAME+"/sys/upload.php",
        multiple:true,
        autoSubmit:false,
        fileName:"myfile",
        formData: {"hashname":$("#hashname").val()},
        maxFileSize:30000000,
        showStatusAfterSuccess:false,
        dragDropStr: "<span><b>"+lang_dd+"</b></span>",
        abortStr:"abort",
        cancelStr:$.i18n('upload_cancel'),
        doneStr:"done",
        sizeErrorStr:$.i18n('upload_errorsize')

    }
    );
    */






    var p = new RegExp ('(\.|\/)'+$('input#file_types').val());
        // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: MyHOSTNAME+'sys/index.php',
        autoUpload: true,
        disableValidation: false,
	//acceptFileTypes: /(\.|\/)(gif|jpe?g|png|doc|xls|rtf|pdf|zip|rar|bmp|docx|xlsx|txt)$/i,
        acceptFileTypes: p,
	 maxFileSize: $('input#file_size').val(),
	messages: {
                maxNumberOfFiles: 'Maximum number of files exceeded',
                acceptFileTypes: $.i18n('upload_errortypes'),
                maxFileSize: $.i18n('upload_errorsize'),
                minFileSize: 'File is too small'
            },

        formData: {hashname: $('input#hashname').val()}
}).on('fileuploadprocessalways', function (e, data) {

$(this).removeClass('fileupload-processing');



    }).on('fileuploaddone', function (e, data) {

        $.each(data.result.files, function (index, file) {
            if (file.url) {
                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
                $(data.context.children()[index])
                    .wrap(link);
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);

                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }
        });



    }).on('fileuploadstop', function(e, data) {


         /*if (check_form_ticket() == 0 ) {
enter_ticket();
}
*/

//enter_ticket();
    }).on('fileuploadadd', function (e, data) {/* ...
$("#uploadBtn").on('click',function () {
data.submit();
console.log('hello');
});
*/


}).on('fileuploadsubmit', function (e, data) {
// console.log(data);
});
    }

//$('.start').hide();

    $('body').on('click', 'button#enter_ticket_client', function(event) {
        event.preventDefault();
        	if (check_form_ticket_client() == 0 ) {
     enter_ticket_client();
     //console.log('ok');
     }
        }
        );


    $('body').on('click', 'button#enter_ticket', function(event) {
        event.preventDefault();




if (check_form_ticket() == 0 ) {
     enter_ticket();
     }

//console.log($("#users_do").val());



});


});



});



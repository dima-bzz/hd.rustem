<?php
session_start();
include_once("../functions.inc.php");
$CONF['title_header']=lang('NEW_title')." - ".$CONF['name_of_firm'];
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if ($_SESSION['helpdesk_user_id']) {
   include("head.inc.php");
   include("navbar.inc.php");
$hn=$_POST['hashname'];



//check_unlinked_file();
?>



<div class="container" id="form_add">
<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">


<div class="row" style="padding-bottom:20px;">

    <div class="col-md-8"> <center><h3><i class="fa fa-tag"></i> <?=lang('NEW_title');?></h3></center></div>


</div>

<div class="row" style="padding-bottom:20px;">




<div class="col-md-8" id="div_new">
<?php
if (isset($_GET['ok'])) {
    if (isset($_GET['h'])) {$h=$_GET['h'];}

    ?>
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><i class="fa fa-check"></i> <?=lang('NEW_ok');?></strong> <?=lang('NEW_ok_1');?> <a class="alert-link" href="<?=$CONF['hostname']?>ticket?<?=$h;?>"><?=lang('NEW_ok_2');?></a> <?=lang('NEW_ok_3');?>
        <a class="alert-link" href="<?=$CONF['hostname']?>print_ticket?<?=$h;?>"target="_blank"> <?=lang('NEW_ok_4');?></a>.
    </div>
<?php

}
?>
<div class="panel panel-success" style="padding:20px;">
<div class="panel-body">

<div class="form-horizontal" id="main_form" novalidate="" action="" method="post">


<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_fio">

            <label for="fio" class="col-sm-2 control-label"><small><?=lang('NEW_from');?>: </small></label>
<div class="col-sm-10" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?=lang('NEW_from_desc');?>">
            <div class="input-group">



<?php
$usid=$_SESSION['helpdesk_user_id'];

$stmt = $dbConnection->prepare('SELECT cl.fio, cl.id from clients as cl inner join users as us on us.login =cl.login where us.id=:usid');
	$stmt->execute(array(':usid'=>$usid));
	$res1 = $stmt->fetchAll();

	foreach($res1 as $row) {

$fio=$row['fio'];
$id_cl=$row['id'];


}
?>
<input  type="text" name="fio" class="form-control input-sm ui-autocomplete-input" id="fio" placeholder="<?=lang('NEW_fio');?>" autofocus data-toggle="popover" data-trigger="manual" data-html="true" data-placement="top" data-content="<small><?=lang('NEW_fio_desc');?></small>" autocomplete="off" title="" data-original-title="">

   <a id="select_init_user" href="#" class="input-group-addon">Я</a>
            </div>



        </div></div></div>

    <hr>
      <div data-toggle="tooltip" data-placement="right" title="" data-original-title="<?=lang('NEW_to_desc');?>">
    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">

        <label for="to" class="col-md-2 control-label" ><small><?=lang('NEW_to');?>: </small></label>

        <div class="col-md-10">
<div style="" id="dsd" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_to_unit_desc');?></small>">
            <select data-placeholder="<?=lang('NEW_to_unit');?>" class="chosen-select form-control" id="to" name="unit_id">
                <option value="0"></option>
                <?php
                        /*$qstring = "SELECT name as label, id as value FROM deps where id !='0' ;";
                        $result = mysql_query($qstring);//query the database for entries containing the
                        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
                        */

        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
        $stmt->execute(array(':n'=>'0',':s'=>'1'));
	$res1 = $stmt->fetchAll();
        foreach($res1 as $row) {


//echo($row['label']);
                            $row['label']=$row['label'];
                            $row['value']=(int)$row['value'];


                            ?>

                            <option value="<?=$row['value']?>"><?=$row['label']?></option>

                        <?php


                        }

                        ?>

            </select>
        </div>





            <select data-placeholder="<?=lang('NEW_to_user');?>" multiple id="users_do" name="unit_id" style="padding-top:15px;width:100%;" disabled="disabled">
	     	<option></option>


                <?php


               /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		*/

        $stmt = $dbConnection->prepare('SELECT fio as label, id as value  FROM users where status=:n and login !=:system order by fio ASC');
	$stmt->execute(array(':n'=>'1',':system'=>'system'));
	$res1 = $stmt->fetchAll();
        foreach($res1 as $row) {







//echo($row['label']);
		    $row['label']=$row['label'];
                    $row['value']=(int)$row['value'];


if (get_user_status_text($row['value']) == "online") {$s="status-online-icon";}
else if (get_user_status_text($row['value']) == "offline") {$s="status-offline-icon";}

                    ?>

                    <option data-foo="<?=$s;?>" value="<?=$row['value']?>"><?=nameshort($row['label'])?> </option>

                <?php


                }

                ?>

            </select>


        </div>

    </div>




</div>
</div>



<div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"><small><?=lang('NEW_prio');?>: </small></label>
            <div class="col-sm-10" style=" padding-top: 5px; ">

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs" id="prio_low"><i id="lprio_low" class=""></i><?=lang('NEW_prio_low');?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs active" id="prio_normal"><i id="lprio_norm" class="fa fa-check"></i> <?=lang('NEW_prio_norm');?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?=lang('NEW_prio_high_desc');?>" id="prio_high"><i id="lprio_high" class=""></i><?=lang('NEW_prio_high');?></button>
                    </div>
                </div>
            </div></div></div></div>

            <div class="control-group" id="deadline_in">
            	<div class="controls">
                      <div class="form-group">
                <label for="deadline_t" class="col-sm-2 control-label"><small><?=lang('NEW_deadline');?>: </small></label>
                <div class='col-sm-10'>
        <div class='input-group date' id='deadline' data-toggle="tooltip" data-placement="right" title="" data-original-title="<?=lang('NEW_deadline_info');?>">
            <input type='text' class="form-control input-sm" id="deadline_t" placeholder="<?=lang('NEW_deadline');?>">
            <span class="input-group-addon" >
                <span class="fa fa-calendar"></span>
            </span>
        </div>
      </div></div></div></div>

      <div class="control-group">
      <div class="controls">
                <div class="form-group">


              <label for="povtor" class="col-sm-2 control-label"><small><?=lang('NEW_ok_familiar');?>: </small></label>
      <div class="checkbox">
      	       <div class="col-sm-10">
      		  <label>
             <input type="checkbox" id="confirm" /> <small><?=lang('NEW_ok_familiar_text');?></small>
          </label>
        </div></div></div></div></div>


<div class="control-group">
<div class="controls">
          <div class="form-group">


        <label for="povtor" class="col-sm-2 control-label"><small><?=lang('NEW_povtor');?>: </small></label>
<div class="checkbox">
	       <div class="col-sm-10">
		  <label>
		     <?php
		     if (!isset($_GET['ok'])) {
			?>
       <input type="checkbox" name="povtor" /> <small><?=lang('NEW_povtor_text');?></small>
       <?php } ?>
		     <?php
		     if (isset($_GET['ok'])) {
			?>
       <input type="checkbox" name="povtor" <?php if ($_GET['povtor'] == 'true') echo 'checked';?> /> <small><?=lang('NEW_povtor_text');?></small>
       <?php } ?>
    </label>
  </div></div></div></div></div>

<?php

/*





*/
if ($_GET['povtor'] == "true"){
if (isset($_GET['ok'])) {
   if (isset($_GET['h'])) {$h=$_GET['h'];}
//echo var_dump($_GET);
if ($CONF['fix_subj'] == "false") {

    $stmt = $dbConnection->prepare('SELECT subj, msg from tickets where hash_name=:hn');
    $stmt->execute(array(':hn'=>$h));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach($res1 as $row) {
?>

<div class="control-group" id="for_subj">
	<div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?=lang('NEW_subj');?>" data-toggle="popover" data-html="true" value="<?= $row['subj'] ?>" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_subj_msg');?></small>">
    </div>
  </div></div></div>
<?php  }}
    else if ($CONF['fix_subj'] == "true") {
?>



<div class="control-group" id="for_subj" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_subj_msg');?></small>">
    <div class="controls">
        <div class="form-group">
            <label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
            <div class="col-sm-10" style="">
                <select data-placeholder="<?=lang('NEW_subj_det');?>" class="chosen-select form-control input-sm" id="subj" name="subj">
                    <option value="0"></option>
                    <?php
                    /*$qstring = "SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC";
                    $result = mysql_query($qstring);//query the database for entries containing the term
		    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		    */


	$stmt = $dbConnection->prepare('SELECT name,id FROM subj order by name COLLATE utf8_unicode_ci ASC');
	$stmt->execute();
	$res1 = $stmt->fetchAll();
        foreach($res1 as $row) {


                        ?>

                        <option value="<?=$row['name']?>" valueid="<?=$row['id']?>"><?=$row['name']?></option>

                    <?php


                    }

                    ?>

                </select>
            </div>
        </div>

    </div>
</div>
<div id="form_subj">
</div>

<?php }
?>
<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?=lang('NEW_MSG');?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_MSG_msg');?></small>" placeholder="<?=lang('NEW_MSG_ph');?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="Укажите сообщение" aria-invalid="false"><?php echo $row['msg']?></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>
<?php
}
}
}
?>


<?php

/*





*/
if ($_GET['povtor'] == "false"){
if (isset($_GET['ok'])) {
if ($CONF['fix_subj'] == "false") {
?>

<div class="control-group" id="for_subj">
	<div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?=lang('NEW_subj');?>" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_subj_msg');?></small>">
    </div>
  </div></div></div>
<?php }
    else if ($CONF['fix_subj'] == "true") {
?>



<div class="control-group" id="for_subj" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_subj_msg');?></small>">
    <div class="controls">
        <div class="form-group">
            <label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
            <div class="col-sm-10" style="">
                <select data-placeholder="<?=lang('NEW_subj_det');?>" class="chosen-select form-control input-sm" id="subj" name="subj">
                    <option value="0"></option>
                    <?php
                    /*$qstring = "SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC";
                    $result = mysql_query($qstring);//query the database for entries containing the term
		    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		    */


	$stmt = $dbConnection->prepare('SELECT name,id FROM subj order by name COLLATE utf8_unicode_ci ASC');
	$stmt->execute();
	$res1 = $stmt->fetchAll();
        foreach($res1 as $row) {


                        ?>

                        <option value="<?=$row['name']?>" valueid="<?=$row['id']?>"><?=$row['name']?></option>

                    <?php


                    }

                    ?>

                </select>
            </div>
        </div>

    </div>
</div>
<div id="form_subj">
</div>

<?php }
?>
<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?=lang('NEW_MSG');?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="bottom" data-content="<small><?=lang('NEW_MSG_msg');?></small>" placeholder="<?=lang('NEW_MSG_ph');?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="Укажите сообщение" aria-invalid="false"></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>
<?php
}
}




if ($_GET['povtor'] == 0){
if (!isset($_GET['ok'])) {


if ($CONF['fix_subj'] == "false") {
?>

<div class="control-group" id="for_subj">
	<div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?=lang('NEW_subj');?>" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_subj_msg');?></small>">
    </div>
  </div></div></div>
<?php }
    else if ($CONF['fix_subj'] == "true") {
?>



<div class="control-group" id="for_subj" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_subj_msg');?></small>">
    <div class="controls">
        <div class="form-group">
            <label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
            <div class="col-sm-10" style="">
                <select data-placeholder="<?=lang('NEW_subj_det');?>" class="chosen-select form-control input-sm" id="subj" name="subj">
                    <option value="0"></option>
                    <?php
                    /*$qstring = "SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC";
                    $result = mysql_query($qstring);//query the database for entries containing the term
		    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		    */


	$stmt = $dbConnection->prepare('SELECT name,id FROM subj order by name COLLATE utf8_unicode_ci ASC');
	$stmt->execute();
	$res1 = $stmt->fetchAll();
        foreach($res1 as $row) {


                        ?>

                        <option value="<?=$row['name']?>" valueid="<?=$row['id']?>"><?=$row['name']?></option>

                    <?php


                    }

                    ?>

                </select>
            </div>
        </div>

    </div>
</div>
<div id="form_subj">
</div>

<?php }
?>
<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?=lang('NEW_MSG');?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="bottom" data-content="<small><?=lang('NEW_MSG_msg');?></small>" placeholder="<?=lang('NEW_MSG_ph');?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="Укажите сообщение" aria-invalid="false"></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>
<?php
}
}
?>


<?php if ($CONF['file_uploads'] == "true") { ?>

<div class="control-group">
    <div class="controls">
    <div class="form-group">

    <label for="" class="col-sm-2 control-label"><small><?=lang('TICKET_file_add');?>:</small></label>

    <div class="col-sm-10">

 <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
        <div class="fileupload-buttonbar">
            <div class="">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button btn-xs">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span><?=lang('TICKET_file_upload')?></span>
                    <input id="filer" type="file" name="files[]" multiple>
                </span>
                <!--button data-toggle="popover" data-html="true" data-trigger="manual" data-placement="top" data-content="<small><?=lang('upload_not_u')?></small>" type="submit" class="btn btn-primary start btn-xs" id="start_upload">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span><?=lang('TICKET_file_startupload');?></span>
                </button>
                <button type="reset" class="btn btn-warning cancel btn-xs">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span><?=lang('TICKET_file_notupload')?></span>
                </button--><br>
               <small class="text-muted"><?=lang('TICKET_file_upload_msg');?></small>
                <!-- The global file processing state -->



                <span class="fileupload-process"></span>
            </div>

        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form>

</div>
    </div>
    </div>
</div>


<!--div class="control-group">
    <div class="controls">
    <div class="form-group">

    <label for="" class="col-sm-2 control-label"><small><?=lang('TICKET_file_add');?>:</small></label>

    <div class="col-sm-10">
        <div id="fileuploader"><?=lang('TICKET_file_upload');?></div>
    </div>
    </div>
</div>
</div-->
<?php } ?>
<form id="add_field_form">
  <div>
    <?php
    $stmt = $dbConnection->prepare('SELECT field_hash, field_name, field_placeholder, field_value, field_type, field_status FROM dop_fields WHERE field_status = :n and field_subj = :n2 and field_name <> "" order by id asc');
    $stmt->execute(array(':n' => '1', ':n2' => '0'));
    $res1 = $stmt->fetchAll();
    foreach ($res1 as $row) {
     ?>
    <div class="control-group">
      <div class="controls">
        <div class="form-group">
          <label for="<?=$row['field_hash'];?>" class="col-sm-2 control-label"><small><?=$row['field_name']?>:</small></label>
          <div class="col-sm-10" style="padding-top: 5px;">
            <?php
            if ($row['field_type'] == "text"){
              ?>
              <input type="text" class="form-control input-sm" name="<?=$row['field_hash'];?>" id="<?=$row['field_hash'];?>" placeholder="<?=$row['field_placeholder']?>" value="<?=$row['field_value']?>">
              <?php
            }
             ?>
             <?php
             if ($row['field_type'] == "textarea"){
               ?>
               <textarea rows="3" class="form-control input-sm" name="<?=$row['field_hash'];?>" id="<?=$row['field_hash'];?>" placeholder="<?=$row['field_placeholder']?>" style="overflow:hidden; word-wrap:break-word; resize: horizontal; height: 66px;"><?=$row['field_value']?></textarea>
               <?php
             }
              ?>
              <?php
              if ($row['field_type'] == "select"){
                ?>
                <select data-placeholder="<?=$row['field_placeholder']?>" class="chosen-select form-control" id="<?=$row['field_hash'];?>" name="<?=$row['field_hash'];?>">
                  <?php
                  $val = explode(',',$row['field_value']);
                  foreach ($val as $key) {
                    ?>
                    <option value="<?=$key;?>"><?=$key;?></option>
                    <?php
                  }
                   ?>
                </select>
                <?php
              }
               if ($row['field_type'] == "multiselect"){
                 ?>
                 <select data-placeholder="<?=$row['field_placeholder']?>" class="multi_field select2-offscreen" id="<?=$row['field_hash'];?>" name="<?=$row['field_hash'];?>[]" multiple="multiple">
                   <?php
                   $val = explode(',',$row['field_value']);
                   foreach ($val as $key) {
                     ?>
                     <option value="<?=$key;?>"><?=$key;?></option>
                     <?php
                   }
                    ?>
                 </select>
                 <?php
               }
                ?>
        </div>
      </div>
    </div>
  </div>
    <?php
  }
     ?>
  </div>
</form>
<div class="col-md-2"></div>
<div class="col-md-10" id="processing">
    <div class="btn-group btn-group-justified">
        <div class="btn-group">
            <button id="enter_ticket" class="btn btn-success" type="button"><i class="fa fa-check-circle-o"></i> <?=lang('NEW_button_create');?></button>
        </div>
        <div class="btn-group">
            <button id="reset_ticket" class="btn btn-default" type="submit"><i class="fa fa-eraser"></i> <?=lang('NEW_button_reset');?></button>
        </div>
    </div>
    <input type="hidden" id="file_array" value="">
    <input type="hidden" id="client_id_param" value="">
    <input type="hidden" id="hashname" value="<?=md5(time());?>">
    <input type="hidden" id="status_action" value="">
    <input type="hidden" id="prio" value="1">
    <input type="hidden" value="<?php echo $_SESSION['helpdesk_user_id']; ?>" id="user_init_id">


<input type="hidden" id="file_types" value="<?=$CONF['file_types']?>">
<input type="hidden" id="file_size" value="<?=$CONF['file_size']?>">

</div>


</div>
</div>
</div>

    <br>

</div>
<div class="col-md-4">

    <div class="panel panel-success" id="user_info" style="display: block;">






    </div>
    <div id="alert_add">
    </div>



</div>



</div>





</div>

</div>

<?php
 include("footer.inc.php");
?>
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade" id="up_entry">
        <td>
            <p class="name">
{% if (file.name.length>20) { %}
{%=file.name.substr(0,10) %}...{%=file.name.substr(-5) %}
{% } %}
{% if (file.name.length<20) { %}
{%=file.name%}
{% } %}



            </p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button id="s_start" class="btn btn-primary start btn-xs" disabled><i class="glyphicon glyphicon-upload"></i> <?=lang('TICKET_file_startupload');?>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel btn-xs">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span><?=lang('TICKET_file_notupload_one');?></span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade" id= "{%=file.name.replace(/\..*/, '')%}">
<td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name2%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>

        <td>
            <p class="name">
<td>
{% if (file.name2.length>30) { %}
<?=lang('file_info');?>: {%=file.name2.substr(0,30) %}...{%=file.name2.substr(-5) %} - <?=lang('file_info2');?>
{% } %}
{% if (file.name2.length<30) { %}
<?=lang('file_info');?>: {%=file.name2%} - <?=lang('file_info2');?>
{% } %}
</td>

            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>

        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
<p class="name">
<span class="label label-success" style="font-size:98%;"><i class="fa fa-check"></i> ok</span>
<button id="files_del_upload" type="button" class="btn btn-danger btn-xs" value={%=file.name.replace(/\..*/, '')%} title="<?=lang('FILES_del');?>"><i class="fa fa-trash-o"></i> </button>
</p>
        </td>
        </tr>
{% } %}
</script>
<script>
   var user_login_name = '<?php echo $fio; ?>';
   var user_init_id2 = '<?php echo $id_cl; ?>';
   </script>

<?php
    }
    }
else {
    include 'auth.php';
}
?>
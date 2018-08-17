<?php
session_start();

include("../functions.inc.php");
$rkeys=array_keys($_GET);

$CONF['title_header']=lang('TICKET_name')." #".get_ticket_id_by_hash($rkeys[1])." - ".$CONF['name_of_firm'];


if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    include("head.inc.php");
    include("navbar.inc.php");

    //echo $rkeys[1];
    //$hn=($_GET['hash']);
    $hn=$rkeys[1];
    $user_id_1=id_of_user($_SESSION['helpdesk_user_login']);
    $stmt = $dbConnection->prepare('SELECT
			    id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, comment, last_edit, is_read, lock_t, lock_by, work_t, ok_by, arch, ok_date, prio, familiar, deadline_t, last_update, permit_ok, approve_tickets
			    from tickets
			    where hash_name=:hn');
    $stmt->execute(array(':hn'=>$hn));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach($res1 as $row) {






            $lock_by=$row['lock_by'];
            $ok_by=$row['ok_by'];
            $ok_date=$row['ok_date'];
            $cid=$row['client_id'];
            $tid=$row['id'];
            $arch=$row['arch'];
            $familiar=$row['familiar'];
            $user_to_idd = $row['user_to_id'];
            $user_init_idd = $row['user_init_id'];
            $status_ok=$row['status'];
            $lock_t=$row['lock_t'];
            $work_t=$row['work_t'];
            $dt = $row['date_create'];
            $deadline_t = $row['deadline_t'];
            $permit_ok = $row['permit_ok'];
            $approve_tickets = $row['approve_tickets'];



            if ($arch == 1) {$st= "<span class=\"label label-default\"><i class=\"fa fa-archive\"></i> ".lang('TICKET_status_arch')."</span>";}
            if ($arch == 0) {
              if (get_conf_param('approve_tickets') == 'false'){
                if ($status_ok == 1) {$st=  "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> ".lang('TICKET_status_ok')." ".nameshort(name_of_user_ret($ok_by))."</span>";}
                if ($status_ok == 0) {
                    if ($lock_by <> 0) {$st=  "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> ".lang('TICKET_status_lock')." ".name_of_user_ret($lock_by)."</span>";}
                    if ($lock_by == 0) {$st=  "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> ".lang('TICKET_status_new')."</span>";}
                }
              }
              else{
                if (($status_ok == 1) && ($approve_tickets == 1)) {$st=  "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> ".lang('TICKET_status_ok')." ".nameshort(name_of_user_ret($ok_by))."</span>";}
                if (($status_ok == 1) && ($approve_tickets == 0)) {$st=  "<span class=\"label label-info\"><i class=\"fa fa-exclamation-circle \"></i> ".lang('TICKET_status_ok')." ".nameshort(name_of_user_ret($ok_by))." ".lang('TICKET_status_approve')."</span>";}
                if ($status_ok == 0) {
                    if ($lock_by <> 0) {$st=  "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> ".lang('TICKET_status_lock')." ".name_of_user_ret($lock_by)."</span>";}
                    if ($lock_by == 0) {$st=  "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> ".lang('TICKET_status_new')."</span>";}
                }
              }
            }













            if ($row['user_to_id'] <> 0 ) {
              $t = get_fio_name_return($row['user_to_id']);
              $t2 = get_fio_name_return($row['user_to_id']);
              $g = count($t);
              if ($t[1] != ''){
                if ($g == 2){
                  $to_text="<div class=''>".view_array(get_fio_name_return($row['user_to_id']))."</div>";
                }
                if ($g > 2 ){
                  if (in_array($user_id_1,explode(',',$row['user_to_id']))){
                  }
                  if (($l = array_search($t2[0],$t)) !==FALSE){
                    unset($t[$l]);
                  }
                  if (($l2 = array_search($t2[1],$t)) !==FALSE){
                    unset($t[$l2]);
                  }
                  $to_text="<div class='' data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".view_array($t)."\">".$t2[0]."<br>".$t2[1]." и другие </div>";
                }
          }
            else {
              $to_text="<div class=''>".name_of_user_ret($row['user_to_id'])."</div>";
            }
            }
            if ($row['user_to_id'] == 0 ) {
                $to_text="<strong>".lang('t_list_a_all')."</strong> ".lang('T_from')." ".view_array(get_unit_name_return($row['unit_id']));
            }




            if ($row['is_read'] == "0") {

                $res = $dbConnection->prepare("update tickets set is_read=:n where id=:tid");
                $res->execute(array(':n' => '1', ':tid'=>$tid));

            }

            if ($lock_by <> "0") {
                if ($lock_by == $_SESSION['helpdesk_user_id']) {
                    $status_lock="me";
                    //$lock_disabled="";
                    $lock_text="<i class=\"fa fa-unlock\"></i> ".lang('TICKET_action_unlock')."";
                    $lock_status="unlock";
                }
                else {

                    $status_lock="you";

                    $lock_status="unlock";
                    $lock_text="<i class=\"fa fa-unlock\"></i> ".lang('TICKET_action_unlock')."";

                }



            }
            if ($lock_by == "0") {

                $lock_text="<i class=\"fa fa-lock\"></i> ".lang('TICKET_action_lock')."";
                $lock_status="lock";

            }

            if ($status_ok == "1") {
                $status_ok_text=lang('TICKET_action_nook');
                $status_ok_status="ok";

            }

            if ($status_ok == "0") {
                $status_ok_text="<i class=\"fa fa-check\"></i> ".lang('TICKET_action_ok')."";
                $status_ok_status="no_ok";
            }




            $inituserid_flag=0;
            if ($row['user_init_id'] == $_SESSION['helpdesk_user_id']) {
                $inituserid_flag=1;
            }



            $prio="<span class=\"label label-info\"><i class=\"fa fa-minus\"></i> ".lang('t_list_a_p_norm')."</span>";
	    if ($row['prio'] == "1") {
	    $prio_style['normal']="active";}

            else if ($row['prio'] == "0") {$prio= "<span class=\"label label-primary\"><i class=\"fa fa-arrow-down\"></i> ".lang('t_list_a_p_low')."</span>"; }

            else if ($row['prio'] == "2") {$prio= "<span class=\"label label-danger\"><i class=\"fa fa-arrow-up\"></i> ".lang('t_list_a_p_high')."</span>"; }



            ?>
	          <input type="hidden" id="prio" value="<?=$row['prio'];?>">
            <input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
            <input type="hidden" id="last_update" value="<?=$row['last_update'];?>">
            <input type="hidden" id="ticket_id" value="<?=$row['id'];?>">
            <input type="hidden" id="ticket_hash" value="<?=$row['hash_name'];?>">
            <div class="container">

            <div class="row">

            <div class="col-md-8">
              <div class="row">

              <div class="col-md-12">
            <?php if (isset($_GET['refresh'])) { ?>
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-refresh"></i> <?=lang('TICKET_msg_updated');?></div>
            <?php
            }
            ?>

            <div class="panel panel-default">
	    <div class="panel-heading">
	    <table>
	    <tr>
	    <td style="width:1000px;">
            <h3 class="panel-title"style="display:inline;"><i class="fa fa-ticket"></i> <?=lang('TICKET_name');?> <strong>#<?=$row['id']?></strong></h3>
	    </td>
	    <td style="width:600px;text-align:right;">
      <a href="print?<?php echo $row['hash_name']; ?>"target="_blank" class="btn btn-default btn-xs pull-right"><i class="fa fa-print"></i> <?=lang('HELPER_print');?></a>
	    <?php if (($inituserid_flag == 1) && ($arch == 0)) { ?>
	    <button class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#myModal">
	    <i class="fa fa-pencil-square-o"></i> <?=lang('TICKET_edit');?>
	    </button>
	    <?php } ?>
      <?php if (validate_admin($_SESSION['helpdesk_user_id'])) { ?>
      <button class="btn btn-default btn-xs pull-right" id="ticket_delete" value="<?php echo $row['hash_name']; ?>"> <?=lang('TICKET_delete');?></button>
      <?php } ?>
	    </td>
	    </tr>
	    </table>
	    </div>
            <div class="panel-body">


            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td style="width:50px;"><small><strong><?=lang('TICKET_t_from');?> </strong></small></td>
                    <td><small><?=name_of_user($row['user_init_id'])?> </small></td>
                    <td style="width:150px;"><small><strong> <?=lang('TICKET_t_was_create');?></strong></small></td>
                    <td style="width:150px;"><small><time id="c" datetime="<?=$row['date_create']; ?>"></time> </small></td>
                </tr>
                <tr>
                    <td style="width:50px;"><small><strong><?=lang('TICKET_t_to');?> </strong></small></td>
                    <td><small><?=$to_text;?> </small></td>
                    <td style="width:50px;"><small><strong><?=lang('TICKET_t_last_edit');?> </strong></small></td>
                    <td><small><?php if ($row['last_edit']) { ?> <time id="c" datetime="<?=$row['last_edit'];?>"></time> <?php } ?> </small></td>
                </tr>
                <tr>
                    <td><small><strong><?=lang('TICKET_t_worker');?></strong></small>
                    </td>
                    <td class=""><small><?=name_of_client($cid)?></small></td>

                    <td style="width:50px;"><small><strong><?=lang('TICKET_t_last_up');?> </strong></small></td>
                    <td><small><?php if ($row['last_update']) { ?> <time id="c" datetime="<?=$row['last_update'];?>"></time> <?php } ?> </small></td>


                </tr>
                <tr>
                    <td><small><strong><?=lang('TICKET_t_status');?></strong></small>
                    </td>
                    <td><small><?=$st;?></small></td>
                    <td><small><strong><?=lang('TICKET_t_prio');?></strong></small>
                    </td>
                    <td><small><?=$prio;?></small>
                    </td>
                </tr>
                <?php
                if (($user_to_idd == 0) || (strpos($user_to_idd,",") <> false)){
                 ?>
                <tr>
                    <td><small><strong><?=lang('TICKET_t_familiars');?></strong></small>
                    </td>
                    <td colspan="3"><small>
                      <?php
                      echo view_array2(nameshort(get_fio_name_return($familiar)));
                       ?>
                    </small></td>
                </tr>
                <?php
              }
                 ?>
                </tbody>
            </table>



<link rel="stylesheet" href="<?=$CONF['hostname']?>css/ticket_style.css">

<?php if (($inituserid_flag == 1) && ($arch == 0)) { ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<h4 class="modal-title"><?=lang('P_title');?></h4>
</div>
<div class="modal-body">
<form class="form-horizontal" role="form">



<?php
if ($CONF['fix_subj'] == "false") {
?>
<div class="control-group" id="for_s">
<div class="controls">
<div class="form-group">
<label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
<div class="col-sm-10">
<input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?=lang('NEW_subj');?>" value="<?=$row['subj'];?>">
</div>
</div></div></div>
<?php }
else if ($CONF['fix_subj'] == "true") {
?>
<div class="control-group" id="for_subj" >
<div class="controls">
<div class="form-group">
<label for="subj" class="col-sm-2 control-label"><small><?=lang('NEW_subj');?>: </small></label>
<div class="col-sm-10" style="">
<select data-placeholder="<?=lang('NEW_subj_det');?>" class="form-control input-sm" id="subj" name="subj">
<option value="0"></option>
<?php
$stmts = $dbConnection->prepare('SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC');
$stmts->execute();
$res11 = $stmts->fetchAll();
foreach($res11 as $rows) {
$sel_flag="";
if ($rows['name'] == $row['subj']) {$sel_flag="selected";}
?>
<option <?=$sel_flag;?> value="<?=$rows['name']?>"><?=$rows['name']?></option>
<?php
}
?>
</select>
</div>
</div>
</div>
</div>
<?php } ?>

<div class="control-group" id="for_prio">
<div class="controls">
<div class="form-group">
<label for="" class="col-sm-2 control-label"><small><?=lang('NEW_prio');?>: </small></label>
<div class="col-sm-10" style=" padding-top: 5px; ">
<div class="btn-group btn-group-justified">
<div class="btn-group">
<button type="button" class="btn btn-primary btn-xs <?=$prio_style['low'];?>" id="prio_low"><i id="lprio_low" class=""></i><?=lang('NEW_prio_low');?></button>
</div>
<div class="btn-group">
<button type="button" class="btn btn-info btn-xs <?=$prio_style['normal'];?>" id="prio_normal"><i id="lprio_norm" class=""></i> <?=lang('NEW_prio_norm');?></button>
</div>
<div class="btn-group">
<button type="button" class="btn btn-danger btn-xs <?=$prio_style['high'];?>" data-toggle="tooltip" data-placement="top" title="<?=lang('NEW_prio_high_desc');?>" id="prio_high"><i id="lprio_high" class=""></i><?=lang('NEW_prio_high');?></button>
</div>
</div>
</div></div></div></div>

<div class="control-group">
<div class="controls">
<div class="form-group" id="for_msg">
<label for="msg" class="col-sm-2 control-label"><small><?=lang('NEW_MSG');?>:</small></label>
<div class="col-sm-10">
<textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_MSG_msg');?></small>" placeholder="<?=lang('NEW_MSG_ph');?>" class="form-control input-sm animated" name="msg" id="msg_up" rows="3" required="" data-validation-required-message="Укажите сообщение" aria-invalid="false"><?=$row['msg'];?></textarea>
</div>
</div>
<div class="help-block"></div></div></div>
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('TICKET_file_notupload_one');?></button>
<button type="button" id="save_edit_ticket" class="btn btn-primary"><?=lang('JS_save');?></button>
</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
}
$stmt = $dbConnection->prepare('SELECT field_name, field_value FROM tickets_fields WHERE ticket_hash = :hashname order by id asc');
$stmt->execute(array(':hashname' => $hn));
$res1 = $stmt->fetchAll();
if (!empty($res1)){
?>
<label class="control-label"><small><?=lang('TICKET_dop_info');?>:</small></label>
<table class="table table-bordered">
<tbody>
<?php
foreach ($res1 as $row2) {
  ?>
  <tr>
    <td style="width:150px;"><small><?=$row2['field_name']?>:</small></td>
    <td><small><?=$row2['field_value']?></small></td>
  </tr>
  <?php
}
 ?>
 </tbody>
 </table>
 <?php
}
  ?>
<div class="panel panel-default">
<table class="table table-bordered">
<tbody>
<tr>
<td style="width:50px;padding: 8px;border-right: 0px;"><small><strong><?=lang('TICKET_t_subj');?>: </strong></small></td>
<td style="padding: 8px; border-left: 0px; border-right: 0px;"><?php if (($inituserid_flag == 1) && ($arch == 0)) {?>
<?php
if ($CONF['fix_subj'] == "false") {?>
<!--a href="#" data-pk="<?=$tid?>" data-url="actions.php" id="edit_subj_ticket" data-type="text"-->
<?php }
if ($CONF['fix_subj'] == "true") {
?>
<?php } } ?>
<?=make_html($row['subj'])?>
<?php if (($inituserid_flag == 1) && ($arch == 0)) {?><!--/a--><?php }?>
</td>
</tr>
<tr>
<td style=" padding: 20px;border-top: 1px solid #DDD;background-color:#FDFDFD; " colspan="2">
<!--p href="#" data-pk="<?=$tid?>" data-url="actions.php" id="edit_msg_ticket" data-type="textarea"-->
<?=make_html($row['msg'])?>
<!--/p-->
</td>
</tr>
</tbody>
</table>

                <?php



                $stmt = $dbConnection->prepare('SELECT file_hash, original_name, file_size FROM files where ticket_hash=:tid');
                $stmt->execute(array(':tid'=>$hn));
                $res1 = $stmt->fetchAll();
                if (!empty($res1)) {


                    ?>
                    <hr style="margin:0px;">
                    <div class="row" style="padding:10px;">
                        <div class="col-md-3">
                            <center><small><strong><?=lang('TICKET_file_list')?>:</strong></small></center>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-hover">
                                    <tbody>
                                <?php

                                foreach($res1 as $r) {
                                    ?>



                    <tr>
                        <td style="width:20px;"><small><?=get_file_icon($r['file_hash']);?></small></td>
                        <td><small><a href='<?=$CONF['hostname'];?>sys/download.php?step=files&hn=<?=$r['file_hash'];?>'><?=$r['original_name'];?></a></small></td>
                        <td><small><?php echo round(($r['file_size']/(1024*1024)),2);?> Mb</small></td>
                    </tr>






                                <?php }?>
                            </table>
                        </div>
                    </div>


                <?php
                } ?>











</div>
</div>
</div>
</div>

            </div>
            <div class="row">
              <div class="col-md-12">
            <?php
            $user_id=id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user=unit_of_user($user_id);
            $ps=priv_status($user_id);
            $oku=permit_ok_ticket($hn);

            $lo="no";
            $lo2="no";
            $lo3="no";
            $lo4="no";
            $lo5="no";

/////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
if ($ps == 1) {
//ЗАявка не выполнена ИЛИ выполнена мной
//ЗАявка не заблокирована ИЛИ заблокирована мной
if ($row['user_init_id'] == $user_id) {
if ($status_ok == 0)
                    {
                                if ($lock_by == 0){
                                $lo="yes";
                                $lo2="yes";
				// $lo3="yes";
                                	    }
				else if (($lock_by == $user_id) || ($lock_by <> $user_id)) {
                                $lo2 = "yes";
                                $lo3 = "yes";
                              $lo5="yes";
					}
			    }
if ($status_ok == 1){
$lo3="yes";
}

                            }


if ($row['user_init_id'] <> $user_id) {

//if (($status_ok == 0) || (($status_ok == 1) && ($ok_by == $user_id)))
if ($status_ok == 0)
                    {
			if ($lock_by == 0){
        if ($permit_ok <> 1){
			$lo = "yes";
    }
      $lo2 = "yes";
			// $lo3="yes";
			}
                        if ($lock_by == $user_id) {
                        $lo2 = "yes";
                        if ($oku <> 1){
                        $lo3 = "yes";
                        }
                      $lo5="yes";
                        }
                        if ($familiar == 0){
                          $lo4 = "yes";
                        }
                        else if (!in_array($user_id,explode(",",$familiar))){
                          $lo4 = "yes";
                        }

	}

else if (($status_ok == 1) && ($ok_by == $user_id)){
                        $lo3="yes";
                        }


		    }

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





/////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
if ($ps == 0) {
if ($status_ok == 0){
if ($lock_by == 0){
$lo="yes";
$lo2="yes";
$lo5="yes";
// $lo3="yes";
}

else if (($lock_by == $user_id) || ($lock_by <> $user_id)) {
$lo2 = "yes";
// if ($oku <> '1'){
$lo3 = "yes";
// }
$lo5="yes";
}
if ($familiar == 0){
  $lo4 = "yes";
}
else if (!in_array($user_id,explode(",",$familiar))){
  $lo4 = "yes";
}
}
if ($status_ok == 1){
$lo3="yes";
}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





//////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
if ($ps == 2) {
if ($status_ok == 0){
if ($lock_by == 0){
$lo="yes";
$lo2="yes";
$lo5="yes";
// $lo3="yes";
}
else if (($lock_by == $user_id) || ($lock_by <> $user_id)) {
$lo2 = "yes";
$lo3 = "yes";
$lo5="yes";
}
if ($familiar == 0){
  $lo4 = "yes";
}
else if (!in_array($user_id,explode(",",$familiar))){
  $lo4 = "yes";
}
}
if ($status_ok == 1){
$lo3="yes";
}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////






            if ($lo == "no") {$lock_disabled="disabled=\"disabled\"";}
            else if ($lo == "yes") {$lock_disabled="";}
            if ($lo2 == "no") {$lock_disabled_2="disabled=\"disabled\"";}
            else if ($lo2 == "yes") {$lock_disabled_2="";}
            if ($lo3 == "no") {$lock_disabled_3="disabled=\"disabled\"";}
            else if ($lo3 == "yes") {$lock_disabled_3="";}
            if ($lo4 == "no") {$lock_disabled_4="disabled=\"disabled\"";}
            else if ($lo4 == "yes") {$lock_disabled_4="";}





            ?>


              <div class="button_action">
                <div id="ccc"></div>

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button <?=$lock_disabled?>  id="action_refer_to" value="0" type="button" class="btn btn btn-danger"><i class="fa fa-share"></i> <?=lang('TICKET_t_refer');?></button>
                    </div>
                    <div class="btn-group">
                        <button <?=$lock_disabled_2?>  id="action_lock" status="<?=$lock_status?>" value="<?=$_SESSION['helpdesk_user_id']?>" tid="<?=$tid?>" unlok="<?=$lo5?>" permitok="<?=$permit_ok?>" type="button" class="btn btn btn-danger"> <?=$lock_text?></button>
                    </div>
                    <div class="btn-group">
                        <button <?=$lock_disabled_3?> id="action_ok" status="<?=$status_ok_status?>" value="<?=$_SESSION['helpdesk_user_id']?>" tid="<?=$tid?>" type="button" class="btn btn btn-danger"><?=$status_ok_text?> </button>
                    </div>
                </div>
                <?php
                    if (($user_to_idd == 0) || (strpos($user_to_idd,",") <> false)){
                      if($user_init_idd <> $user_id){
                      if (($familiar <> 0) && ($familiar <> "")){
                        if ($lock_by <> 0){
                 ?>
                    <button <?=$lock_disabled_4?>  id="action_familiar" value="<?=$_SESSION['helpdesk_user_id']?>" tid="<?=$tid?>" permitok="<?=$permit_ok?>" unlok="<?=$lo5?>" type="button" class="btn btn btn-primary" style="width:100%;"><i class="fa fa-hand-o-right"></i> <?=lang('TICKET_t_familiar');?></button>
                    <?php
                  }
                  }
                }
                }
                     ?>
                </center>

              </div>
            <!-- </div>

              <div class="col-md-12"> -->
                <div class="refer">
            <div id="refer_to" class="col-md-12 panel panel-default" style="padding:10px; margin-top: 20px; margin-bottom: 0px;">




                <div class="form-group" id="t_for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?=lang('NEW_to_unit_desc');?></small>">
                    <label for="to" class="col-sm-3 control-label"><small><?=lang('TICKET_t_refer_to');?>: </small></label>
                    <div class="col-sm-4" style="">
                        <select <?=$lock_disabled?> data-placeholder="<?=lang('NEW_to_unit');?>" class="chosen-select form-control input-sm" id="t_to" name="unit_id">
                            <option value="0"></option>
                            <?php


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



                    <div class="col-sm-5" style="">

                        <select <?=$lock_disabled?> data-placeholder="<?=lang('NEW_to_user');?>"  id="t_users_do" name="unit_id" style="width:100%;">
	     	<option></option>
                            <?php



                            $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and login !=:system order by fio ASC');
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
                        <p class="help-block" style="text-align:center;"><small ><?=lang('TICKET_t_opt');?></small></p>

                    </div>
                    <div class="col-md-12" style="">
                        <textarea placeholder="<?=lang('NEW_MSG_ph_1');?>" class="form-control input-sm animated" name="msg1" id="msg1" rows="3"></textarea>
                    </div>
                    <div class="col-sm-12" style="padding-top:15px;">
                        <button id="ref_ticket" value="<?=$tid?>" type="button" style="float:right;" class="btn btn-default btn-sm" <?=$lock_disabled?>><i class="fa fa-check"></i> <?=lang('TICKET_t_refer_ok');?></button>
                    </div>
                </div>




            </div>
</div>
</div>
</div>













<div class="row">

            <div class="col-md-12" style="margin-top: 20px;" id="msg">
            </div>

</div>





<div class="row">
  <div class="col-md-12">
            <div  class="tabbable">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-comments-o"></i> <?=lang('TICKET_t_comment');?></a></li>
                    <li><a href="#profile" data-toggle="tab"><i class="fa fa-list"></i> <?=lang('TICKET_t_history');?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="home">
                        <div class="col-md-12 comment">

                                  <div class="box-header">
                                  </div>
                                    <div id="comment_content" class="box-body chat">
                                        <?=view_comment($tid);?>
                                    </div>


                                  <div class="box-footer">
                                    <div class="control-group">
                                        <div class="controls">
                                            <div class="form-group" id="for_msg">
                                                <label for="msg" class="col-sm-3 control-label"><small><?=lang('TICKET_t_your_comment');?>:</small></label>
                                                <div class="col-sm-12" style="">






                                                    <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="&lt;small&gt;<?=lang('TICKET_t_det_ticket');?>&lt;/small&gt;" placeholder="<?=lang('TICKET_t_comm_ph');?>" class="form-control input-sm animated" name="msg" id="msg" rows="1" required="" data-validation-required-message="Укажите сообщение" aria-invalid="false"></textarea>


</div>
</div>
</div>
</div>

<?php if ($CONF['file_uploads'] == "true") { ?>

<div class="control-group" style="padding-bottom:15px;">
<div class="controls">
<div class="form-group">

<label for="" class="col-sm-3 control-label"><small><?=lang('TICKET_file_add');?>:</small></label>

<div class="col-sm-12">

<form id="fileupload" action="" method="POST" enctype="multipart/form-data">
<div class="fileupload-buttonbar">
<div class="">
<!-- The fileinput-button span is used to style the file input field as button -->
<span class="btn btn-success fileinput-button btn-xs">
<i class="glyphicon glyphicon-plus"></i>
<span><?=lang('TICKET_file_upload')?></span>
<input id="filer" type="file" name="files[]" multiple>
</span>
<br>
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
<?php } ?>


                                                <div class="col-sm-12" style="padding-bottom:10px;"><button id="do_comment"  user="<?=$_SESSION['helpdesk_user_id']?>" value="<?=$tid?>" type="button" class="btn btn-default btn-sm pull-right"><?=lang('TICKET_t_send');?> (ctrl+enter)</button></div>
                                              </div>
                                            <div class="help-block" style="margin:0px;"></div>
                                            <input type="hidden" id="file_array" value="">
                                            <input type="hidden" id="hashname" value="<?=md5(time());?>">
                                            <input type="hidden" id="file_types" value="<?=$CONF['file_types']?>">
                                            <input type="hidden" id="file_size" value="<?=$CONF['file_size']?>">


                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile"><div id="log_content"><?php view_log($tid); ?></div></div>
                </div>
            </div>
            </div>
          </div>
        </div>
                      <div class="col-md-4">
                          <div class="panel panel-info">
                              <?=get_client_info_ticket($cid)?>
                          </div>


                          <div class="row">

                                      <?php



                                      $wt = 'false';
                                      $ded = "false";
                                      $dtt = date("Y-m-d H:i:s");

                                      if ($arch == 1) {
                                          ?>
                                          <div class="col-md-12" style="padding-top:20px;">
                                              <div class="alert alert-warning"><?=lang('TICKET_t_in_arch');?></div>
                                          </div>
                                      <?php
                                      }
                                      if ($arch == 0) {
                                        if(get_conf_param('approve_tickets') == 'false'){
                                          if ($status_ok == 1) {
                                              ?>


                                              <div class="col-md-12" style="padding-top:20px;" id="msg_e">
                                                  <div class="alert alert-success"> <?=lang('TICKET_t_ok');?> <strong> <?=name_of_user($ok_by)?></strong> <?=$ok_date;?>.<br> <?=lang('TICKET_t_ok_1');?></div>
                                              </div>


                                          <?php
                                            }
                                          }
                                          else{
                                            if (($status_ok == 1) && ($approve_tickets == 1)) {
                                                ?>


                                                <div class="col-md-12" style="padding-top:20px;" id="msg_e">
                                                    <div class="alert alert-success"> <?=lang('TICKET_t_ok');?> <strong> <?=name_of_user($ok_by)?></strong> <?=$ok_date;?>.<br> <?=lang('TICKET_t_ok_1');?></div>
                                                </div>


                                            <?php
                                              }
                                            if (($status_ok == 1) && ($approve_tickets == 0)) {
                                                ?>


                                                <div class="col-md-12" style="padding-top:20px;" id="msg_e">
                                                    <div class="alert alert-info"> <?=lang('TICKET_t_approve');?> <strong> <?=name_of_user($ok_by)?></strong> <?=$ok_date;?>.<br> <strong><?=lang('TICKET_t_approve1');?></strong></div>
                                                </div>


                                            <?php
                                              }
                                          }
                                          if ($status_ok == 0) {
                                            if (strtotime($dtt) < strtotime($deadline_t) ){
                                              $ded = "true";
                                            }
                                              if ($lock_by <> 0) {
                                                $wt = 'true';
                                                  if ($status_lock == "you") {

                                                      ?>
                                                      <div class="col-md-12" style="padding-top:20px;">
                                                          <div class="alert alert-warning"><?=lang('TICKET_t_lock');?> <strong><?=name_of_user($lock_by)?></strong>. <br><?=lang('TICKET_t_lock_1');?> <?php
                                                          if (($user_to_idd == 0) || (strpos($user_to_idd,",") <> false)){
                                                          if ($user_init_idd <> $user_id){
                                                          if(!in_array($user_id,explode(",",$familiar))){
                                                            if ($oku == '1'){
                                                              echo lang('TICKET_t_lock_3')."<br>";
                                                            }
                                                            echo lang('TICKET_t_lock_2');
                                                          }
                                                          if(in_array($user_id,explode(",",$familiar))){
                                                          echo lang('TICKET_t_lock_4');
                                                        }
                                                          }
                                                        }
                                                          ?>
                                                        </div>
                                                      </div>
                                                  <?php
                                                  }
                                                  if ($status_lock == "me") {

                                                      ?>
                                                      <div class="col-md-12" style="padding-top:20px;" id="msg_e">
                                                          <div class="alert alert-warning"><?=lang('TICKET_t_lock_i');?>
                                                          <?php
                                                          if (($user_to_idd == 0) || (strpos($user_to_idd,",") <> false)){
                                                          if ($user_init_idd <> $user_id){
                                                            if ($oku == '1'){
                                                              echo lang('TICKET_t_lock_3');
                                                            }
                                                          }
                                                        }
                                                          ?>
                                                          </div>
                                                      </div>
                                                  <?php
                                                  }

                                              }

                                          }
                                      }
                                      if (strtotime($dt) > strtotime("2016-04-26 16:00:00")){
                                      ?>
                                      <div class="col-md-12">
                                        <div class="info-box bg-blue">
                                          <span class="info-box-icon"><i class="fa fa-bolt"></i></span>
                                          <div class="info-box-content">
                                            <span class="info-box-text"><?=lang('TICKET_reaction');?></span>
                                            <span class="info-box-number" style="white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;">
                                              <?php
                                                if ($lock_t == 0){
                                                  echo lang('TICKET_not_lock');
                                                }
                                                else{
                                                $dr = strtotime($lock_t) - strtotime($dt);
                                               ?>
                                          <time id="f" datetime="<?=$dr?>"></time>
                                          <?php
                                        }
                                          ?>
                                        </span>
                                        </div>
                                      </div>
                                          <div class="info-box bg-yellow">
                                            <span class="info-box-icon"><i class="fa fa-lock"></i></span>
                                            <div class="info-box-content">
                                              <span class="info-box-text"><?=lang('TICKET_ok_dt');?></span>
                                              <span class="info-box-number" style="white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;" id="work_timer" value="<?=$wt?>">
                                                <?php
                                                // if ($status_ok == 1){
                                                //   $dw = strtotime($ok_date) - strtotime($lock_t);
                                                // }
                                                // else{
                                                //   $dw = $work_t;
                                                // }
                                                 ?>
                                              <time id="f" datetime="<?=$work_t?>"></time>
                                          </span>
                                          </div>
                                        </div>
                                        <div class="info-box bg-red">
                                          <span class="info-box-icon"><i class="fa fa-check-square"></i></span>
                                          <div class="info-box-content">
                                            <span class="info-box-text"><?=lang('TICKET_deadline');?></span>
                                            <span class="info-box-number" style="white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;" id="deadline_timer" value="<?=$ded?>">
                                              <?php
                                              if ($deadline_t == "0000-00-00 00:00:00"){
                                              echo lang('TICKET_not_deadline');
                                              }
                                          else if (($deadline_t != "0000-00-00 00:00:00") && (strtotime($dtt) > strtotime($deadline_t)) && ($status_ok == 0)){
                                                echo lang('TICKET_overdue');
                                              }
                                              else if (($deadline_t != "0000-00-00 00:00:00") && (strtotime($ok_date) < strtotime($deadline_t)) && ($status_ok == 1)){
                                                    echo lang('TICKET_deadline_ok');
                                                  }
                                                  else if (($deadline_t != "0000-00-00 00:00:00") && (strtotime($ok_date) > strtotime($deadline_t)) && ($status_ok == 1)){
                                                        echo lang('TICKET_overdue');
                                                      }
                                        else{
                                          $dd = strtotime($deadline_t) - strtotime($dtt);
                                           ?>
                                           <time id="f" datetime="<?=$dd?>"></time>

                                           <?php
                                        }
                                        if ($deadline_t != "0000-00-00 00:00:00"){
                                           ?>
                                        </span>
                                        <div class="progress">
                                          <div class="progress-bar" style="width: 0%"></div>
                                          </div>
                                          <span class="progress-description">
                      <?=lang('TICKET_deadline_dt');?>: <time id="c" datetime="<?=$deadline_t?>"></time>
                                        </span>
                                        <?php
                                      }
                                         ?>
                                        </div>
                                      </div>
                                      </div>
                                      <?php
                                    }
                                        ?>
                          </div>


                      </div>
</div>
</div>
        <?php






        }
    }
    else {
        ?>
        <div class="well well-large well-transparent lead">
            <center><?=lang('TICKET_t_no');?></center>
        </div>
    <?php
    }



    ?>


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
    <span class="label label-success" style="font-size:88%;"><i class="fa fa-check"></i> ok</span>
    <button id="files_del_upload_comment" type="button" class="btn btn-danger btn-xs" value={%=file.name.replace(/\..*/, '')%} title="<?=lang('FILES_del');?>"><i class="fa fa-trash-o"></i> </button>
    </p>
            </td>
            </tr>
    {% } %}
    </script>
<?php

}
else {
    include 'auth.php';
}
?>
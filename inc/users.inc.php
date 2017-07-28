<?php

session_start();
//include("../functions.inc.php");



if (isset($_POST['menu'])) {

if ($_POST['menu'] == 'new' ) {


if (isset($_GET['ok'])) {

    ?>
    <div class="alert alert-success"><?=lang('USERS_msg_add');?></div>
    <?php
}

?>
<style>
.select2-search-choice-close {
  top: 3px;
}
</style>
<div class="panel panel-default">
  <div class="panel-heading"><?=lang('USERS_new');?></div>
  <div class="panel-body">

<div id="form_message"></div>
<form class="form-horizontal" role="form">


  <div class="form-group" id="fio_user_grp">
    <label for="fio" class="col-sm-2 control-label"><?=lang('USERS_fio');?></label>
    <div class="col-sm-10">
    <input autocomplete="off" id="fio_user" name="fio_user" type="" class="form-control input-sm" placeholder="<?=lang('USERS_fio_full');?>">
    </div>
  </div>
  <div class="form-group" id="login_user_grp">
    <label for="login" class="col-sm-2 control-label"><?=lang('USERS_login');?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="login_user" type="" class="form-control input-sm" id="login_user" placeholder="<?=lang('USERS_login');?>">
        </div>
  </div>
  <div class="form-group" id="pass_user_grp">
    <label for="exampleInputPassword1" class="col-sm-2 control-label"><?=lang('USERS_pass');?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="password" type="password" class="form-control input-sm" id="exampleInputPassword1" placeholder="<?=lang('USERS_pass');?>">
        </div>
  </div>
    <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><?=lang('USERS_mail');?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?=lang('USERS_mail');?>">
        </div>
  </div>
  <div class="form-group">
  <label for="jabber" class="col-sm-2 control-label"><?=lang('USERS_jabber');?></label>
      <div class="col-sm-10">
  <input autocomplete="off" name="jabber" type="text" class="form-control input-sm" id="jabber" placeholder="<?=lang('USERS_jabber');?>">
      </div>
</div>
<div class="form-group">
  <label for="jabber_active_client" class="col-sm-2 control-label"><?=lang('CONF_jabber_status');?></label>
  <div class="col-sm-10">
<select class="chosen-select_no_search form-control input-sm" id="jabber_active_client">
<option value="0"><?=lang('CONF_false');?></option>
<option value="1"><?=lang('CONF_true');?></option>
</select>    </div>
</div>


      <div class="form-group">
    <label for="lang" class="col-sm-2 control-label"><?=lang('SYSTEM_lang');?></label>
        <div class="col-sm-10">
    <select data-placeholder="<?=lang('SYSTEM_lang');?>" class="chosen-select_no_search form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>

                        <option value="en">English</option>
                        <option value="ru">Русский</option>
</select>
        </div>
  </div>



  <div class="form-group">
  <label for="my-select" class="col-sm-2 control-label"><?=lang('USERS_units');?></label>
  <div class="col-sm-10">
  <select multiple="multiple" id="my-select" name="unit[]">
<?php
                        /*$qstring = "SELECT name as label, id as value FROM deps where id !='0' ;";
                        $result = mysql_query($qstring);
                        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
                        */


        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n');
	$stmt->execute(array(':n'=>'0'));
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
  </div>




      <div class="form-group">
    <label for="mess" class="col-sm-2 control-label"><?=lang('MAIL_msg');?></label>
        <div class="col-sm-10">
        <textarea placeholder="<?=lang('DASHBOARD_msg');?>" class="form-control input-sm animated" name="mess" id="mess" rows="3"></textarea>


        </div>
  </div>

    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('USERS_profile_priv');?></label>
  <div class="col-sm-10">
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios3" value="2" >
    <strong class="text-warning"><?=lang('USERS_nach1');?></strong>
    <p class="help-block"><small><?=lang('USERS_nach1_desc');?></small></p>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios1" value="0" >
    <strong class="text-success"><?=lang('USERS_nach');?></strong>
    <p class="help-block"><small><?=lang('USERS_nach_desc');?></small></p>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios2" value="1" checked="checked">
    <strong class="text-info"><?=lang('USERS_wo');?></strong>
    <p class="help-block"><small><?=lang('USERS_wo_desc');?></small></p>
  </label>

</div>
  </div>
  </div>

    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('USERS_privs');?></label>
  <div class="col-sm-10">



      <div class="col-sm-6">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_add_client" checked="checked"> <?=lang('TICKET_p_add_client');?>
    </label>
  </div>
      </div>

          <div class="col-sm-6">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_edit_client" checked="checked"> <?=lang('TICKET_p_edit_client');?>
    </label>
  </div>
      </div>

  </div>
    </div>

    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('USERS_admin');?></label>
  <div class="col-sm-10">



      <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="admin_client"> <?=lang('USERS_admin_info');?>
    </label>
  </div>
      </div>
  </div>
    </div>


    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('Add_users');?></label>
  <div class="col-sm-10">

          <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="user_add_client"> <?=lang('Add_users_info');?>
    </label>
  </div>
      </div>

  </div>
    </div>

<div class="col-sm-12"><hr></div>
<div class="col-md-offset-3 col-md-6">
<center>
    <button type="submit" id="create_user" class="btn btn-success"><?=lang('USERS_make_create');?></button>
</center>
</div>
</form>




  </div>
</div>



<?php
}

if ($_POST['menu'] == 'list' ) {
?>
<div class="panel panel-default">
  <div class="panel-heading"><?=lang('USERS_list');?></div>
  <div class="panel-body">
  <table class="table table-bordered">
        <thead>
          <tr>
            <th><center><small><?=lang('USERS_uid');?>			</small></center></th>
            <th><center><small><?=lang('USERS_fio');?>			</small></center></th>
            <th><center><small><?=lang('USERS_login');?>		</small></center></th>
            <th><center><small><?=lang('USERS_privs');?>		</small></center></th>
            <th><center><small><?=lang('USERS_unit');?>			</small></center></th>
	          <th><center><small><?=lang('t_LIST_status');?>		</small></center></th>
            <th><center><small><?=lang('Users_logout');?>		</small></center></th>

          </tr>
        </thead>
        <tbody>
        <?php
    //include("../dbconnect.inc.php");
    //$results = mysql_query("SELECT id, fio, login, priv, unit, status from users;");
    //while ($row = mysql_fetch_assoc($results)) {
    //$getunit=get_unit_name($row['priv']);



        $stmt = $dbConnection->prepare('SELECT id, fio, login, priv, unit, status, us_kill from users order by status desc');
	$stmt->execute();
	$res1 = $stmt->fetchAll();
        foreach($res1 as $row) {



    $unit=view_array(get_unit_name_return($row['unit']));
    $statuss=$row['status'];

    if ($row['priv'] == "0") {$priv=lang('USERS_p_1');}
    else if ($row['priv'] == "1") {$priv=lang('USERS_p_2');}
    else if ($row['priv'] == "2") {$priv=lang('USERS_p_3');}

    if ($statuss == "1") {$r="";}
    if ($statuss != "1") {$r="warning";}

    ?>
          <tr class="<?=$r;?>">
            <td><small><center><?php echo $row['id']; ?></center></small></td>
            <td><small><a value="<?php echo $row['id']; ?>" href="<?php echo $CONF['hostname']; ?>users?edit=<?=$row['id'];?>"><?php echo $row['fio']; ?></a></small></td>
            <td><small><?php echo $row['login']; ?></small></td>
            <td><small><?php echo $priv; ?></small></td>
            <td><small><span data-toggle="tooltip" data-placement="right" title="<?=$unit;?>"><?=lang('LIST_pin')?> <?=count(get_unit_name_return($row['unit'])); ?> </span></small></td>
	          <td><small class="text-danger"><center><?=get_user_status($row['id']);?></center></small></td>
            <td><center><button class="btn btn-xs btn-warning" id="users_logout" value="<?php echo $row['id']; ?>">logout</button><center></td>
          </tr>
        <?php } ?>
       </tbody>
      </table>
  </div>
</div>
<?php
}
if ($_POST['menu'] == 'edit' ) {
//echo $_POST['id'];
$usid=($_POST['id']);




  /* $query = "SELECT fio, pass, login, status, priv, unit,email,messages,lang from users where id='$usid'; ";
    $sql = mysql_query($query) or die(mysql_error());
if (mysql_num_rows($sql) == 1) {
$row = mysql_fetch_assoc($sql);
*/



        $stmt = $dbConnection->prepare('SELECT fio, pass, login, status, priv, is_admin, unit,email,jabber,messages,lang,priv_add_client,priv_edit_client, jabber_noty, noty, mail_noty_show, show_noty, jabber_noty_show from users where id=:usid');
	$stmt->execute(array(':usid'=>$usid));
	$res1 = $stmt->fetchAll();

        foreach($res1 as $row) {




$priv_add_client=$row['priv_add_client'];
$priv_edit_client=$row['priv_edit_client'];
$fio=$row['fio'];
$login=$row['login'];
$pass=$row['pass'];
$status=$row['status'];
$priv=$row['priv'];
$unit=$row['unit'];
$email=$row['email'];
$jabber=$row['jabber'];
$noty=$row['noty'];
$jnoty = $row['jabber_noty_show'];
$mnoty = $row['mail_noty_show'];
$show_noty=$row['show_noty'];
$messages=$row['messages'];
$langu=$row['lang'];
$priv_edit_admin_client=$row['is_admin'];
$jabber_noty = $row['jabber_noty'];

            if ($priv_add_client == "1") {$priv_add_client="checked";} else {$priv_add_client="";}
            if ($priv_edit_client == "1") {$priv_edit_client="checked";} else {$priv_edit_client="";}
            if ($priv_edit_admin_client == "8") {$priv_edit_admin_client="checked";} else {$priv_edit_admin_client="";}



if ($langu == "en") 	 {$status_lang_en="selected";}
else if ($langu == "ru") {$status_lang_ru="selected";}

if ($status == "0") {$status_lock="selected";}
if ($status == "1") {$status_unlock="selected";}


if ($priv == "0") {$status_admin="checked";}
if ($priv == "1") {$status_user="checked";}
if ($priv == "2") {$status_superadmin="checked";}


}
if (isset($_GET['ok'])) {

    ?>
    <div class="alert alert-success"><?=lang('USERS_msg_edit_ok');?></div>
    <?php
}
?>
<div class="panel panel-default">
  <div class="panel-heading"><?=lang('USERS_make_edit');?></div>
  <div class="panel-body">






<form class="form-horizontal" role="form">


  <div class="form-group">
    <label for="fio" class="col-sm-2 control-label"><?=lang('USERS_fio');?></label>
    <div class="col-sm-10">
    <input autocomplete="off" id="fio_edit" name="fio_edit" type="" class="form-control input-sm" placeholder="<?=lang('USERS_fio_full');?>" value="<?=$fio?>">
    </div>
  </div>
  <div class="form-group">
    <label for="login" class="col-sm-2 control-label"><?=lang('USERS_login');?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="login" type="" class="form-control input-sm" id="login" placeholder="<?=lang('USERS_login');?>" value="<?=$login?>">
        </div>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1" class="col-sm-2 control-label"><?=lang('USERS_pass');?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="password" type="password" class="form-control input-sm" id="exampleInputPassword1" placeholder="<?=lang('USERS_pass');?>" value="">
        </div>
  </div>
      <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><?=lang('USERS_mail');?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?=lang('USERS_mail');?>" value="<?=$email;?>">
        </div>
  </div>
  <div class="form-group">
<label for="jabber" class="col-sm-2 control-label"><?=lang('USERS_jabber');?></label>
    <div class="col-sm-10">
<input autocomplete="off" name="jabber" type="text" class="form-control input-sm" id="jabber" placeholder="<?=lang('USERS_jabber');?>" value="<?=$jabber;?>">
    </div>
</div>
<div class="form-group">
  <label for="jabber_active_client" class="col-sm-2 control-label"><?=lang('CONF_jabber_status');?></label>
  <div class="col-sm-10">
<select class="chosen-select_no_search form-control input-sm" id="jabber_active_client">
<option value="1" <?php if ($jabber_noty == "1") {echo "selected";} ?>><?=lang('CONF_true');?></option>
<option value="0" <?php if ($jabber_noty == "0") {echo "selected";} ?>><?=lang('CONF_false');?></option>
</select>    </div>
</div>
<div class="form-group">
  <label for="jabber_show_profile" class="col-sm-2 control-label"><?=lang('P_jabber_show');?></label>
  <div class="col-sm-10">
<select class="form-control input-sm" data-placeholder="<?=lang('P_noty_show_p');?>" multiple id="jabber_show_edit">
  <?php
    $jn = explode(",",$jnoty);
   ?>
<option value="1" <?php if (in_array("1",$jn)) {echo "selected";} ?>><?=lang('P_create');?></option>
<option value="2" <?php if (in_array("2",$jn)) {echo "selected";} ?>><?=lang('P_refer');?></option>
<option value="3" <?php if (in_array("3",$jn)) {echo "selected";} ?>><?=lang('P_comment');?></option>
<option value="4" <?php if (in_array("4",$jn)) {echo "selected";} ?>><?=lang('P_lock');?></option>
<option value="5" <?php if (in_array("5",$jn)) {echo "selected";} ?>><?=lang('P_unlock');?></option>
<option value="6" <?php if (in_array("6",$jn)) {echo "selected";} ?>><?=lang('P_ok');?></option>
<option value="7" <?php if (in_array("7",$jn)) {echo "selected";} ?>><?=lang('P_no_ok');?></option>
<option value="8" <?php if (in_array("8",$jn)) {echo "selected";} ?>><?=lang('P_msg');?></option>
<option value="9" <?php if (in_array("9",$jn)) {echo "selected";} ?>><?=lang('P_subj');?></option>
<option value="10" <?php if (in_array("10",$jn)) {echo "selected";} ?>><?=lang('P_familiar');?></option>
</select>    </div>
</div>
        <div class="form-group">
    <label for="lang" class="col-sm-2 control-label"><?=lang('SYSTEM_lang');?></label>
        <div class="col-sm-10">
    <select data-placeholder="<?=lang('SYSTEM_lang');?>" class="chosen-select_no_search form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>

                        <option <?=$status_lang_en;?> value="en">English</option>
                        <option <?=$status_lang_ru;?> value="ru">Русский</option>
</select>
        </div>
  </div>

    <div class="form-group">
  <label for="my-select" class="col-sm-2 control-label"><?=lang('USERS_units');?></label>
  <div class="col-sm-10">
  <select multiple="multiple" id="my-select" name="unit[]">
<?php
			$u=explode(",", $unit);


                       /* $qstring = "SELECT name as label, id as value FROM deps where id !='0' ;";
                        $result = mysql_query($qstring);
                        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){*/

        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n');
	$stmt->execute(array(':n'=>'0'));
	$res1 = $stmt->fetchAll();

        foreach($res1 as $row) {



//echo($row['label']);
                            $row['label']=$row['label'];
                            $row['value']=(int)$row['value'];

$opt_sel='';
foreach ($u as $val) {
if ($val== $row['value']) {$opt_sel="selected";}

}

                            ?>

                            <option <?=$opt_sel;?> value="<?=$row['value']?>"><?=$row['label']?></option>

                        <?php

//
                        }

                        ?>
    </select>
  </div>
  </div>


        <div class="form-group">
    <label for="mess" class="col-sm-2 control-label"><?=lang('MAIL_msg');?></label>
        <div class="col-sm-10">
        <textarea placeholder="<?=lang('');?>" class="form-control input-sm animated" name="mess" id="mess" rows="3"><?=$messages;?></textarea>


        </div>
  </div>



  <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('USERS_profile_priv');?></label>
  <div class="col-sm-10">
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios3" value="2" <?=$status_superadmin?>>
    <strong class="text-warning"><?=lang('USERS_nach1');?></strong>
    <p class="help-block"><small><?=lang('USERS_nach1_desc');?></small></p>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios1" value="0" <?=$status_admin?>>
    <strong class="text-success"><?=lang('USERS_nach');?></strong>
    <p class="help-block"><small><?=lang('USERS_nach_desc');?></small></p>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios2" value="1" <?=$status_user?>>
    <strong class="text-info"><?=lang('USERS_wo');?></strong>
    <p class="help-block"><small><?=lang('USERS_wo_desc');?></small></p>
  </label>

</div>
  </div>
  </div>


    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('USERS_privs');?></label>
  <div class="col-sm-10">



      <div class="col-sm-6">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_add_client" <?=$priv_add_client?>> <?=lang('TICKET_p_add_client');?>
    </label>
  </div>
      </div>

          <div class="col-sm-6">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_edit_client" <?=$priv_edit_client?>> <?=lang('TICKET_p_edit_client');?>
    </label>
  </div>
      </div>

  </div>
    </div>
    <div class="form-group">
      <label for="show_active_profile" class="col-sm-2 control-label"><?=lang('P_noty_show');?></label>
      <div class="col-sm-10">
    <select class="form-control input-sm" data-placeholder="<?=lang('P_noty_show_p');?>" multiple id="show_noty_edit">
      <?php
        $n = explode(",",$noty);
       ?>
    <option value="1" <?php if (in_array("1",$n)) {echo "selected";} ?>><?=lang('P_create');?></option>
    <option value="2" <?php if (in_array("2",$n)) {echo "selected";} ?>><?=lang('P_refer');?></option>
    <option value="3" <?php if (in_array("3",$n)) {echo "selected";} ?>><?=lang('P_comment');?></option>
    <option value="4" <?php if (in_array("4",$n)) {echo "selected";} ?>><?=lang('P_lock');?></option>
    <option value="5" <?php if (in_array("5",$n)) {echo "selected";} ?>><?=lang('P_unlock');?></option>
    <option value="6" <?php if (in_array("6",$n)) {echo "selected";} ?>><?=lang('P_ok');?></option>
    <option value="7" <?php if (in_array("7",$n)) {echo "selected";} ?>><?=lang('P_no_ok');?></option>
    <option value="8" <?php if (in_array("8",$n)) {echo "selected";} ?>><?=lang('P_msg');?></option>
    <option value="9" <?php if (in_array("9",$n)) {echo "selected";} ?>><?=lang('P_subj');?></option>
    <option value="10" <?php if (in_array("10",$n)) {echo "selected";} ?>><?=lang('P_familiar');?></option>
  </select>    </div>
    </div>
    <div class="form-group">
      <label for="mail_active_profile" class="col-sm-2 control-label"><?=lang('P_mail_show');?></label>
      <div class="col-sm-10">
    <select class="form-control input-sm" data-placeholder="<?=lang('P_noty_show_p');?>" multiple id="mail_noty_edit">
      <?php
        $m = explode(",",$mnoty);
       ?>
    <option value="1" <?php if (in_array("1",$m)) {echo "selected";} ?>><?=lang('P_create');?></option>
    <option value="2" <?php if (in_array("2",$m)) {echo "selected";} ?>><?=lang('P_refer');?></option>
    <option value="3" <?php if (in_array("3",$m)) {echo "selected";} ?>><?=lang('P_comment');?></option>
    <option value="4" <?php if (in_array("4",$m)) {echo "selected";} ?>><?=lang('P_lock');?></option>
    <option value="5" <?php if (in_array("5",$m)) {echo "selected";} ?>><?=lang('P_unlock');?></option>
    <option value="6" <?php if (in_array("6",$m)) {echo "selected";} ?>><?=lang('P_ok');?></option>
    <option value="7" <?php if (in_array("7",$m)) {echo "selected";} ?>><?=lang('P_no_ok');?></option>
    <option value="8" <?php if (in_array("8",$m)) {echo "selected";} ?>><?=lang('P_msg');?></option>
    <option value="9" <?php if (in_array("9",$m)) {echo "selected";} ?>><?=lang('P_subj');?></option>
    <option value="10" <?php if (in_array("10",$m)) {echo "selected";} ?>><?=lang('P_familiar');?></option>
  </select>    </div>
    </div>
    <div class="form-group">
      <label for="show_noty" class="col-sm-2 control-label"><?=lang('P_noty_show_site');?></label>
      <div class="col-sm-10">
    <select class="chosen-select_no_search form-control input-sm" id="show_noty">
    <option value="top" <?php if ($show_noty == "top") {echo "selected";} ?>><?=lang('P_top');?></option>
    <option value="topLeft" <?php if ($show_noty == "topLeft") {echo "selected";} ?>><?=lang('P_topLeft');?></option>
    <option value="topRight" <?php if ($show_noty == "opRight") {echo "selected";} ?>><?=lang('P_topRight');?></option>
    <option value="topCenter" <?php if ($show_noty == "topCenter") {echo "selected";} ?>><?=lang('P_topCenter');?></option>
    <option value="center" <?php if ($show_noty == "center") {echo "selected";} ?>><?=lang('P_center');?></option>
    <option value="centerLeft" <?php if ($show_noty == "centerLeft") {echo "selected";} ?>><?=lang('P_centerLeft');?></option>
    <option value="centerRight" <?php if ($show_noty == "centerRight") {echo "selected";} ?>><?=lang('P_centerRight');?></option>
    <option value="bottom" <?php if ($show_noty == "bottom") {echo "selected";} ?>><?=lang('P_bottom');?></option>
    <option value="bottomLeft" <?php if ($show_noty == "bottomLeft") {echo "selected";} ?>><?=lang('P_bottomLeft');?></option>
    <option value="bottomRight" <?php if ($show_noty == "bottomRight") {echo "selected";} ?>><?=lang('P_bottomRight');?></option>
    <option value="bottomCenter" <?php if ($show_noty == "bottomCenter") {echo "selected";} ?>><?=lang('P_bottomCenter');?></option>

  </select>    </div>
    </div>
    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('USERS_admin');?></label>
  <div class="col-sm-10">



      <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="admin_client" <?=$priv_edit_admin_client?>> <?=lang('USERS_admin_info');?>
    </label>
  </div>
      </div>
  </div>
    </div>

  <div class="col-sm-12"><hr></div>
    <div class="form-group">
    <label for="lock" class="col-sm-2 control-label"><?=lang('USERS_acc');?></label>
        <div class="col-sm-10">

    <select class="form-control input-sm" name="lock" id="lock">
  <option <?=$status_lock?> value="0"><?=lang('USERS_not_active');?></option>
  <option <?=$status_unlock?> value="1"><?=lang('USERS_active');?></option>
    </select>

        </div>
  </div>

<hr>
<div class="col-md-offset-1 col-md-10">



<center>
<div class="btn-group">
    <button type="button" class="btn btn-success" id="edit_user" value="<?=$_POST['id']?>" ><?=lang('USERS_editable');?></button>
    <!--button type="button" class="btn btn-danger" id="delete_user" value="<?=$_POST['id']?>" ><?=lang('USERS_delete');?></button-->
</div>
</center>

</div>
</form>








  </div>
</div>

<?php
}

}

?>
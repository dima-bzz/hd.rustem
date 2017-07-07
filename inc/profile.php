<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
//if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");



?>
<style>
.select2-search-choice-close {
  top: 3px;
}
</style>
<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">

<?php
$usid=$_SESSION['helpdesk_user_id'];

//$query = "SELECT fio, pass, login, status, priv, unit,email, lang from users where id='$usid'; ";
//    $sql = mysql_query($query) or die(mysql_error());


	$stmt = $dbConnection->prepare('SELECT fio, pass, login, status, priv, unit,email, lang, jabber_noty, noty, show_noty, jabber_noty_show, mail_noty_show from users where id=:usid');
	$stmt->execute(array(':usid'=>$usid));
	$res1 = $stmt->fetchAll();





	//if (mysql_num_rows($sql) == 1) {
        //$row = mysql_fetch_assoc($sql);
foreach($res1 as $row) {
$jabber_noty=$row['jabber_noty'];
$fio=$row['fio'];
$login=$row['login'];
$pass=$row['pass'];
$email=$row['email'];
$noty = $row['noty'];
$jnoty = $row['jabber_noty_show'];
$mnoty = $row['mail_noty_show'];
$show_noty = $row['show_noty'];
$langu=$row['lang'];

if ($langu == "en") 	 {$status_lang_en="selected";}
else if ($langu == "ru") {$status_lang_ru="selected";}


}

?>

<div class="container">
<div class="page-header" style="margin-top: -15px;">
          <h3 ><center><?=lang('P_title');?></center></h3>
 </div>


<div class="row">



      <div class="col-md-offset-2 col-md-8">
      <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-user"></i> <?=lang('P_main');?></div>
      <div class="panel-body">
      <form class="form-horizontal" role="form">
      <div class="form-group">
      <div class="col-sm-4 text-right" ><strong ><small><?=lang('WORKER_fio');?>:</small></strong></div>
      <div class="col-sm-8"><small><?=$fio;?></small></div>
      </div>
      <div class="form-group">
      <div class="col-sm-4 text-right" ><strong ><small><?=lang('PROFILE_priv');?>:</small></strong></div>
      <div class="col-sm-8"><small><?=priv_status_name($usid);?></small></div>
      </div>
      <div class="form-group">
      <div class="col-sm-4 text-right"><strong><small><?=lang('PROFILE_priv_unit');?>:</small></strong></div>
      <div class="col-sm-8"><p><small><?=view_array(get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id'])));?></small></p></div>
      <div class="col-sm-12">
      <hr>
      </div>
      </div>

      <div class="form-group">
    <label for="login" class="col-sm-4 control-label"><?=lang('P_login');?></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="login" type="" class="form-control input-sm" id="login" placeholder="<?=lang('P_login');?>" value="<?=$login;?>">
        </div>
  </div>
    <div class="form-group">
    <label for="mail" class="col-sm-4 control-label"><?=lang('P_mail');?></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?=lang('P_mail');?>" value="<?=$email;?>">
    <p class="help-block"><small><?=lang('P_mail_desc');?></small></p>
        </div>
  </div>


          <div class="form-group">
    <label for="lang" class="col-sm-4 control-label"><?=lang('SYSTEM_lang');?></label>
        <div class="col-sm-8">
    <select data-placeholder="<?=lang('SYSTEM_lang');?>" class="chosen-select_no_search form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>

                        <option <?=$status_lang_en;?> value="en">English</option>
                        <option <?=$status_lang_ru;?> value="ru">Русский</option>
</select>
        </div>
  </div>


    <div class="col-md-offset-3 col-md-6">
<center>
    <button type="submit" id="edit_profile_main" value="<?=$usid?>" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('P_edit');?></button>
</center>
</div>
      </form>





      </div>

      </div>
      <div id="m_info"></div>

      <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-bell"></i> <?=lang('P_noty');?></div>
      <div class="panel-body">
        <form class="form-horizontal" role="form">
          <?php
          if ($CONF_JABBER['active'] == "true"){
            if (($CONF_JABBER['server'] != "") && ($CONF_JABBER['port'] != "") && ($CONF_JABBER['login'] != "") && ($CONF_JABBER['pass'] != "")){
           ?>
          <div class="form-group">
            <label for="jabber_active_profile" class="col-sm-4 control-label"><?=lang('CONF_jabber_status');?></label>
            <div class="col-sm-8">
          <select class="chosen-select_no_search form-control input-sm" id="jabber_active_profile">
          <option value="1" <?php if ($jabber_noty == "1") {echo "selected";} ?>><?=lang('CONF_true');?></option>
          <option value="0" <?php if ($jabber_noty == "0") {echo "selected";} ?>><?=lang('CONF_false');?></option>
        </select>    </div>
          </div>
          <div class="form-group">
            <label for="jabber_show_profile" class="col-sm-4 control-label"><?=lang('P_jabber_show');?></label>
            <div class="col-sm-8">
          <select class="form-control input-sm" data-placeholder="<?=lang('P_noty_show_p');?>" multiple id="jabber_show_profile">
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
          <?php
        }
      }
           ?>
          <div class="form-group">
            <label for="show_noty_profile" class="col-sm-4 control-label"><?=lang('P_noty_show');?></label>
            <div class="col-sm-8">
          <select class="form-control input-sm" data-placeholder="<?=lang('P_noty_show_p');?>" multiple id="show_noty_profile">
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
            <label for="mail_noty_profile" class="col-sm-4 control-label"><?=lang('P_mail_show');?></label>
            <div class="col-sm-8">
          <select class="form-control input-sm" data-placeholder="<?=lang('P_noty_show_p');?>" multiple id="mail_noty_profile">
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
            <label for="show_noty" class="col-sm-4 control-label"><?=lang('P_noty_show_site');?></label>
            <div class="col-sm-8">
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
          <div class="col-md-offset-3 col-md-6">
      <center>
          <button type="submit" id="profile_edit_noty" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('P_edit');?></button>


      </center>

      </div>
        </form>
        <?php
        if ($CONF_JABBER['active'] == "true"){
          if (($CONF_JABBER['server'] != "") && ($CONF_JABBER['port'] != "") && ($CONF_JABBER['login'] != "") && ($CONF_JABBER['pass'] != "")){
         ?>
        <button type="submit" id="conf_test_jabber_profile" class="btn btn-default btn-sm pull-right"> test jabber</button>
        <?php
      }
    }
         ?>
      </div>
      </div>
      <div id="conf_test_jabber_res_profile"></div>
      <div id="noty_res"></div>

      <div class="panel panel-danger">
      <div class="panel-heading"><i class="fa fa-key"></i> <?=lang('P_passedit');?></div>
      <div class="panel-body">
      <form class="form-horizontal" role="form">

              <div class="form-group">
    <label for="old_pass" class="col-sm-4 control-label"><?=lang('P_pass_old');?></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="old_pass" type="password" class="form-control input-sm" id="old_pass" placeholder="<?=lang('P_pass_old2');?>">
        </div>
  </div>


        <div class="form-group">
    <label for="new_pass" class="col-sm-4 control-label"><?=lang('P_pass_new');?></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="new_pass" type="password" class="form-control input-sm" id="new_pass" placeholder="<?=lang('P_pass_new2');?>">
        </div>
  </div>

          <div class="form-group">
    <label for="new_pass2" class="col-sm-4 control-label"><?=lang('P_pass_new_re');?></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="new_pass2" type="password" class="form-control input-sm" id="new_pass2" placeholder="<?=lang('P_pass_new_re2');?>">
        </div>
  </div>
  <div class="col-md-offset-3 col-md-6">
<center>
    <button type="submit" id="edit_profile_pass" value="<?=$usid?>" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('P_do_edit_pass');?></button>
</center>
</div>


      </form>

      </div>
      </div>
<div id="p_info"></div>


      </div>




</div>




<br>
</div>
<?php
 include("footer.inc.php");
?>

<?php
    //}
    }
else {
    include 'auth.php';
}
?>
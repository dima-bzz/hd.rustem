<?php


include_once('conf.php');
define("DIR_ROOT", __DIR__);
define("DS", DIRECTORY_SEPARATOR);
include_once('sys/class.phpmailer.php');
include_once('sys/Parsedown.php');
require 'library/HTMLPurifier.auto.php';
$dbConnection = new PDO(
    'mysql:host='.$CONF_DB['host'].';dbname='.$CONF_DB['db_name'],
    $CONF_DB['username'],
    $CONF_DB['password'],
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$CONF = array (
'title_header'	=> get_conf_param('title_header'),
'hostname'	=> get_conf_param('hostname'),
'mail'	=> get_conf_param('mail'),
'days2arch'	=> get_conf_param('days2arch'),
'name_of_firm'	=> get_conf_param('name_of_firm'),
'fix_subj'	=> get_conf_param('fix_subj'),
'first_login'	=> get_conf_param('first_login'),
'file_uploads'	=> get_conf_param('file_uploads'),
'file_types' => '('.get_conf_param('file_types').')',
'file_size' => get_conf_param('file_size'),
'time_zone' => get_conf_param('time_zone')
);
$CONF_MAIL = array (
'active'	=> get_conf_param('mail_active'),
'host'	=> get_conf_param('mail_host'),
'port'	=> get_conf_param('mail_port'),
'auth'	=> get_conf_param('mail_auth'),
'auth_type' => get_conf_param('mail_auth_type'),
'username'	=> get_conf_param('mail_username'),
'password'	=> get_conf_param('mail_password'),
'from'	=> get_conf_param('mail_from'),
'debug' => 'false'
);
$CONF_JABBER = array (
'active'	=> get_conf_param('jabber_active'),
'server'	=> get_conf_param('jabber_server'),
'port'	=> get_conf_param('jabber_port'),
'login'	=> get_conf_param('jabber_login'),
'pass'	=> get_conf_param('jabber_pass'),
);


if ($CONF_HD['debug_mode'] == false) {
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(0);
}
date_default_timezone_set(get_conf_param('time_zone'));


include_once('inc/mail.inc.php');
include_once('inc/jabber.inc.php');

$forhostname=substr($CONF['hostname'], -1);
if ($forhostname == "/") {$CONF['hostname']=$CONF['hostname'];}
else if ($forhostname <> "/") {$CONF['hostname']=$CONF['hostname']."/";}

function get_version(){
  $v = '2.20.4';
  return $v;
}

function get_user_lang(){
    global $dbConnection;


    $mid=$_SESSION['helpdesk_user_id'];
    $stmt = $dbConnection->prepare('SELECT lang from users where id=:mid');
    $stmt->execute(array(':mid' => $mid));
    $max = $stmt->fetch(PDO::FETCH_NUM);

    $max_id=$max[0];
    $length = strlen(utf8_decode($max_id));
    if (($length < 1) || $max_id == "0") {$ress='ru';} else {$ress=$max_id;}
    return $ress;
}


function lang($in){

  $lang2 = get_user_lang();
  switch ($lang2) {
      case 'ru':
          $lang_file2 = (DIR_ROOT . DS . "lang" . DS ."lang.ru.json");
          break;

      case 'en':
          $lang_file2 = (DIR_ROOT . DS . "lang" . DS ."lang.en.json");
          break;

      case 'ua':
          $lang_file2 = (DIR_ROOT . DS . "lang" . DS ."lang.ua.json");
          break;

      default:
          $lang_file2 = (DIR_ROOT . DS . "lang" . DS ."lang.ru.json");

  }

  $file = file_get_contents($lang_file2);
  $json = json_decode($file);
  if (isset($json->$in)){
  return $json->$in;
  }else {
  return 'undefined';
}
}

function get_conf_param($in) {
 global $dbConnection;
 $stmt = $dbConnection->prepare('SELECT value FROM perf where param=:in');
 $stmt->execute(array(':in' => $in));
 $fio = $stmt->fetch(PDO::FETCH_ASSOC);

return $fio['value'];

}

$fio_user=$fio['fio'];

function generateRandomString($length = 5) {
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}
 function validate_exist_login($str) {
 global $dbConnection;
 $uid=$_SESSION['helpdesk_user_id'];

 $stmt = $dbConnection->prepare('SELECT count(login) as n from users where login=:str');
 $stmt->execute(array(':str' => $str));
 $row = $stmt->fetch(PDO::FETCH_ASSOC);
 if ($row['n'] > 0) {$r=false;}
 else if ($row['n'] == 0) {$r=true;}

 return $r;
}

function validate_exist_mail($str) {
    global $dbConnection;
    $uid=$_SESSION['helpdesk_user_id'];

    $stmt = $dbConnection->prepare('SELECT count(email) as n from users where email=:str and id != :uid');
    $stmt->execute(array(':str' => $str,':uid' => $uid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {$r=false;}
    else if ($row['n'] == 0) {$r=true;}

    return $r;
}

function validate_email($str)
{
    return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$str);
}

function validate_alphanumeric_underscore($str)
{
    return preg_match('/^[a-zA-Z0-9_\.-]+$/',$str);
}

function update_val_by_key($key,$val) {
 global $dbConnection;
$stmt = $dbConnection->prepare('update perf set value=:value where param=:param');
$stmt->execute(array(':value' => $val,':param' => $key));
return true;

}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 5; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

function randomhash() {
    $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 24; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}




function nameshort($name) {
    $nameshort = preg_replace('/(\w+) (\w)\w+ (\w)\w+/iu', '$1 $2. $3.', $name);
    return $nameshort;
}


function xss_clean($data)
{

    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');


    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);


    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);


    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);


    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {

        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);


    return $data;
}

function get_file_icon($in) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT file_type FROM files where file_hash=:file_hash');
    $stmt->execute(array(':file_hash' => $in));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ftype=$row['file_type'];


    switch($ftype) {




    case 'application/pdf': $icon="<i class=\"fa fa-file-pdf-o\"></i>";	break;
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': $icon="<i class=\"fa fa-file-word-o\"></i>";	break;
    case 'application/msword': $icon="<i class=\"fa fa-file-word-o\"></i> ";	break;
    case 'application/excel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/vnd.ms-excel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/x-excel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/x-msexcel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': $icon="<i class=\"fa fa-file-word-o\"></i>";	break;
    case 'image/jpeg': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;
    case 'image/jpg': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;
    case 'image/gif': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;
    case 'image/png': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;

	default: $icon="<i class=\"fa fa-file\"></i>";
    }

    return $icon;
}
function view_files_ticket(){
  global $CONF;
  global $dbConnection;
  $stmt = $dbConnection->prepare('select id, ticket_hash, original_name,file_hash,file_type,file_size,file_ext from files');
  $stmt->execute();
  $res1 = $stmt->fetchAll();
  if (!empty($res1)){
    ?>

    <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
            <thead>
              <tr>
                <th><center>ID</center></th>
                <th><center><?=lang('FILES_name');?></center></th>
                <th><center><?=lang('FILES_ticket');?></center></th>
                <th><center><?=lang('t_LIST_status');?></center></th>
                <th><center><?=lang('FILES_size');?></center></th>
                <th><center><?=lang('t_LIST_action');?></center></th>
              </tr>
            </thead>
    	<tbody>
    	<?php
    	    foreach($res1 as $row) {
    	?>
    	<tr id="tr_<?=$row['id'];?>">

    	<td><small><center><?=$row['id'];?></center></small></td>

    	<td><small><?=get_file_icon($row['file_hash']);?> <?=$row['original_name'];?></small></td>
    	<td><small><a href="./ticket?<?=$row['ticket_hash']?>">#<?=get_ticket_id_by_hash($row['ticket_hash']);?></a></small></td>
      <td><small><center> <?=get_ticket_id_by_hash_status($row['ticket_hash']);?><center></small></td>
    	<td><small><?=round(($row['file_size']/(1024*1024)),2);?> Mb</small></td>
    <td><small><center>
    <button id="files_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['file_hash'];?>" title="<?=lang('FILES_del');?>"><i class="fa fa-trash-o"></i> </button>
    <a href="<?=$CONF['hostname'];?>sys/download.php?step=files&hn=<?=$row['file_hash'];?>" class="btn btn-success btn-xs" title="<?=lang('FILES_down');?>"><i class="fa fa-download"></i> </a>
    </center></small></td>


    	</tr>
    		<?php
}
?>
</tbody>
</table>
<?php
}
else{
  ?>
  <div class="well well-large well-transparent lead">
      <center>
          <?=lang('MSG_no_files');?>
      </center>
  </div>
  <?php
}
}
function view_files_comment(){
  global $dbConnection;
  $stmt = $dbConnection->prepare('select id, comment_hash, original_name,file_hash,file_type,file_size,file_ext from files_comment');
  $stmt->execute();
  $res1 = $stmt->fetchAll();
  if (!empty($res1)){

  ?>

  <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
          <thead>
            <tr>
              <th><center>ID</center></th>
              <th><center><?=lang('FILES_name');?></center></th>
              <th><center><?=lang('FILES_ticket');?></center></th>
              <th><center><?=lang('t_LIST_status');?></center></th>
              <th><center><?=lang('FILES_size');?></center></th>
              <th><center><?=lang('t_LIST_action');?></center></th>
            </tr>
          </thead>
    <tbody>
  <?php
  foreach($res1 as $row) {
?>
<tr id="tr_<?=$row['id'];?>">


<td><small><center><?=$row['id'];?></center></small></td>

<td><small><?=get_file_comment_icon($row['file_hash']);?> <?=$row['original_name'];?></small></td>
<td><small><a href="./ticket?<?=get_ticket_hash_comment_hash($row['comment_hash']);?>">#<?=get_ticket_id_by_comment_hash($row['comment_hash']);?></a></small></td>
<td><small><center><?=get_ticket_id_by_comment_hash_status($row['comment_hash']);?><center></small></td>
<td><small><?=round(($row['file_size']/(1024*1024)),2);?> Mb</small></td>
<td><small><center>
<button id="files_del_comment" type="button" class="btn btn-danger btn-xs" value="<?=$row['file_hash'];?>" title="<?=lang('FILES_del');?>"><i class="fa fa-trash-o"></i> </button>
<a href="<?=$CONF['hostname'];?>sys/download.php?step=files_comment&hn=<?=$row['file_hash'];?>" class="btn btn-success btn-xs" title="<?=lang('FILES_down');?>"><i class="fa fa-download"></i> </a>
</center></small></td>

</tr>
<?php
}
?>
</tbody>
</table>
<?php
}
else{
  ?>
  <div class="well well-large well-transparent lead">
      <center>
          <?=lang('MSG_no_files');?>
      </center>
  </div>
  <?php
}
}
function get_file_comment_icon($in) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT file_type FROM files_comment where file_hash=:file_hash');
    $stmt->execute(array(':file_hash' => $in));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ftype=$row['file_type'];


    switch($ftype) {




    case 'application/pdf': $icon="<i class=\"fa fa-file-pdf-o\"></i>";	break;
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': $icon="<i class=\"fa fa-file-word-o\"></i>";	break;
    case 'application/msword': $icon="<i class=\"fa fa-file-word-o\"></i> ";	break;
    case 'application/excel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/vnd.ms-excel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/x-excel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/x-msexcel': $icon="<i class=\"fa fa-file-excel-o\"></i>";	break;
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': $icon="<i class=\"fa fa-file-word-o\"></i>";	break;
    case 'image/jpeg': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;
    case 'image/jpg': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;
    case 'image/gif': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;
    case 'image/png': $icon="<i class=\"fa fa-file-image-o\"></i>";	break;

	default: $icon="<i class=\"fa fa-file\"></i>";
    }

    return $icon;
}

function validate_admin($user_id) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT is_admin from users where id=:user_id LIMIT 1');
    $stmt->execute(array(':user_id' => $user_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin=$row['is_admin'];

    if ($admin == "8") {return true;}
    else {return false;}

}

function view_log($tid) {
global $dbConnection;

                        $stmt = $dbConnection->prepare('SELECT msg,
			    date_op, init_user_id, to_user_id, to_unit_id from ticket_log where
			    ticket_id=:tid order by date_op DESC');
                        $stmt->execute(array(':tid'=>$tid));
                        $re = $stmt->fetchAll();






                        if(!empty($re)) {






                            ?>

                            <div class="col-md-12 log">
                                    <div class="box-header">
                                    </div>
                                    <div class="log-body" style="">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th><center><small><?=lang('TICKET_t_date');?></small></center>	</th>
                                                <th><center><small><?=lang('TICKET_t_init');?>	</small></center></th>
                                                <th><center><small><?=lang('TICKET_t_action');?> 	</small></center></th>
                                                <th><center><small><?=lang('TICKET_t_desc');?>	</small></center></th>


                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                                            foreach($re as $row) {




                                                $t_action=$row['msg'];

                                                if ($t_action == 'refer') {
                                                    $icon_action="fa fa-long-arrow-right";
                                                    $text_action="".lang('TICKET_t_a_refer')." <br>".view_array(get_unit_name_return($row['to_unit_id']))."<br>".name_of_user_ret($row['to_user_id']);

                                                }
                                                if ($t_action == 'arch') {$icon_action="fa fa-archive"; $text_action=lang('TICKET_t_a_arch');}

                                                if ($t_action == 'ok') {$icon_action="fa fa-check-circle-o"; $text_action=lang('TICKET_t_a_ok');}
                                                if ($t_action == 'no_ok') {$icon_action="fa fa-circle-o"; $text_action=lang('TICKET_t_a_nook');}
                                                if ($t_action == 'lock') {$icon_action="fa fa-lock"; $text_action=lang('TICKET_t_a_lock');}
                                                if ($t_action == 'unlock') {$icon_action="fa fa-unlock"; $text_action=lang('TICKET_t_a_unlock');}
                                                if ($t_action == 'create') {$icon_action="fa fa-star-o";
                                                // $user_id_1=id_of_user($_SESSION['helpdesk_user_login']);
                                                  if ($row['to_user_id'] <> 0 ) {
                                                    $t = nameshort(get_fio_name_return($row['to_user_id']));
                                                    $t2 = nameshort(get_fio_name_return($row['to_user_id']));
                                                    $g = count($t);
                                                    if ($t[1] != ''){
                                                      if ($g == 2){
                                                        $to_text="<br><strong>".lang('TICKET_t_a_to_user')."</strong>".view_array(nameshort(get_fio_name_return($row['to_user_id'])));
                                                      }
                                                      if ($g > 2 ){
                                                        // if (in_array($user_id_1,explode(',',$row['to_user_id']))){
                                                        // }
                                                        if (($l = array_search($t2[0],$t)) !==FALSE){
                                                          unset($t[$l]);
                                                        }
                                                        if (($l2 = array_search($t2[1],$t)) !==FALSE){
                                                          unset($t[$l2]);
                                                        }
                                                        $to_text="<div class='' data-toggle=\"tooltip\" data-placement=\"right\" title=\"".view_array($t)."\"><strong>".lang('TICKET_t_a_to_user')."</strong>".$t2[0]."<br>".$t2[1]."".lang('TICKET_t_a_other')."</div>";
                                                      }
                                                }
                                                  else {
                                                    $to_text="<br><strong>".lang('TICKET_t_a_to_user')."</strong>".nameshort(name_of_user_ret($row['to_user_id']));
                                                  }
                                                  }
                                                  if ($row['to_user_id'] == 0 ) {
                                                      $to_text="<br><strong>".lang('TICKET_t_a_to_user')."</strong>".view_array(get_unit_name_return($row['to_unit_id']));
                                                  }
                                                  $text_action=lang('TICKET_t_a_create')."".$to_text;
                                                }
                                                if ($t_action == 'familiar') {$icon_action="fa fa-hand-o-right"; $text_action=lang('TICKET_t_a_familiar');}

                                                if ($t_action == 'edit_msg') {$icon_action="fa fa-pencil-square"; $text_action=lang('TICKET_t_a_e_text');}
                                                if ($t_action == 'edit_subj') {$icon_action="fa fa-pencil-square"; $text_action=lang('TICKET_t_a_e_subj');}
                                                if ($t_action == 'comment') {$icon_action="fa fa-comment"; $text_action=lang('TICKET_t_a_com');}

                                                ?>
                                                <tr>
                                                    <td style="width: 100px; vertical-align: inherit;"><small><center>

                                                    <time id="c" datetime="<?=$row['date_op']?>"></time>

                                                    </center></small></td>
                                                    <td style=" width: 200px; vertical-align: inherit;"><small><center><?=name_of_user($row['init_user_id'])?></center></small></td>
                                                    <td style=" width: 50px; vertical-align: inherit;"><small><center><i class="<?=$icon_action;?>"></i>  </center></small></td>
                                                    <td style=" width: 200px; vertical-align: inherit;"><small><?=$text_action?></small></td>


                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                </div>





                            </div>



                        <?php }
}
function make_html($in, $type) {



 $Parsedown = new Parsedown();
 $text=$Parsedown->text($in);

$text=str_replace("\n", "<br />", $text);
$config = HTMLPurifier_Config::createDefault();



$config->set('Core.Encoding', 'UTF-8');
$config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
$config->set('Cache.DefinitionImpl', null);
$config->set('AutoFormat.RemoveEmpty',false);
$config->set('AutoFormat.AutoParagraph',true);
//$config->set('URI.DisableExternal', true);
if ($type == "no") {
$config->set('HTML.ForbiddenElements', array( 'p' ) );
}

$purifier = new HTMLPurifier($config);
$def = $config->getHTMLDefinition(true);
$def->addElement('ul', 'List', 'Optional: List | li', 'Common', array());
$def->addElement('ol', 'List', 'Optional: List | li', 'Common', array());
// here, the javascript command is stripped off
$content = $purifier->purify($text);

return $content;

}
function permit_ok_ticket($in){
  global $dbConnection;

  $stmt = $dbConnection->prepare('SELECT user_to_id, familiar, unit_id, user_init_id from tickets where hash_name=:hn and permit_ok=:n');
  $stmt->execute(array(':hn'=>$in,':n'=>'1'));
  $per = $stmt->fetch(PDO::FETCH_ASSOC);
  $unit_id = $per['unit_id'];
  $familiar= $per['familiar'];
  $user_to_id = $per['user_to_id'];
  $user_init_id = $per['user_init_id'];

  if ($user_to_id == '0'){
  $stmt = $dbConnection->prepare('SELECT id from users where unit rlike :n and status=:n2 and id !=:n3');
  $stmt->execute(array(':n'=>"[[:<:]]".$unit_id."[[:>:]]",':n2'=>'1',':n3'=>$user_init_id));
  $res1 = $stmt->fetchAll();
  if (!empty($res1)) {
    foreach($res1 as $r) {

      $users[]=$r['id'];

    }
  }

  $fam = explode(",",$familiar);
  $t = array_diff($users,$fam);
  if (empty($t)){
    $permit = '0';
  }
  else{
    $permit = '1';
  }
}
if ($user_to_id <> '0'){
  $fam = explode(",",$familiar);
  $usid = explode(",",$user_to_id);
  $t = array_diff($usid,$fam);
  if (empty($t)){
    $permit = '0';
  }
  else{
    $permit = '1';
  }
}
  return (int)$permit;
}
function view_comment($tid) {
    global $dbConnection;


    ?>






	<div class="row" id="comment_body" style="max-height: 400px; scroll-behavior: initial; overflow-y: scroll; overflow-x: hidden; padding-right:5px;">
    <input type="hidden" id="hashname" value="<?=md5(time());?>">
        <div class="timeline-centered">
        <?php
        $stmt = $dbConnection->prepare('SELECT user_id, comment_text, dt, hashname_comment from comments where t_id=:tid order by dt ASC');
        $stmt->execute(array(':tid' => $tid));
        while ($rews = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>

        <article class="timeline-entry">

            <div class="timeline-entry-inner">

                <div class="timeline-icon bg-info">
                    <i class="entypo-feather"></i>
                </div>

                <div class="timeline-label">
                                                    <div class="header">
                                    <strong class="primary-font"><?=nameshort(name_of_user_ret($rews['user_id']));?></strong> <small class="pull-right text-muted">
                                        <span class="glyphicon glyphicon-time"></span>
                                        <time id="b" datetime="<?=$rews['dt'];?>"></time> <time id="c" datetime="<?=$rews['dt'];?>"></time></small>

                                </div><br>
                    <p><?=make_html($rews['comment_text'], true); ?></p>
                    <?php show_files($rews['hashname_comment']); ?>
                </div>
            </div>

        </article>











        <?php } ?>

        </div>
    </div>





<?php

}
function dop_fields(){
  global $CONF;
  global $dbConnection;
  $stmt = $dbConnection->prepare('SELECT field_hash, field_name, field_subj, field_placeholder, field_value, field_type, field_status FROM dop_fields order by id asc');
  $stmt->execute();
  $res1 = $stmt->fetchAll();
  if (!empty($res1)) {
    ?>
    <table class="table table-hover">
      <tbody>
        <tr>
          <th class="center_header"><?=lang('CONF_status');?></th>
          <?php
          if ($CONF['fix_subj'] == "true") {
            ?>
            <th class="center_header"><?=lang('CONF_subj_h');?></th>
            <?php
          }
            ?>
          <th class="center_header"><?=lang('CONF_name');?></th>
          <th class="center_header"><?=lang('CONF_placeholder');?></th>
          <th class="center_header"><?=lang('CONF_value');?></th>
          <th class="center_header"><?=lang('CONF_type');?></th>
          <th class="center_header"><?=lang('CONF_del');?></th>
        </tr>
      <?php
      foreach ($res1 as $row) {
        if (($CONF['fix_subj'] == "false") && ($row['field_subj'] != '0')){
          $dis = "disabled";
          $dis2 = "active";
        }
        else{
          $dis = "";
          $dis2 = "";
        }
        switch ($row['field_type']) {
        case 'text':
          $val = 'value';
          break;
          case 'textarea':
            $val = 'value';
            break;
            case 'select':
              $val = 'value,value,value';
              break;
              case 'multiselect':
                $val = 'value,value,value';
                break;

                  }
        ?>
        <tr id="<?=$row['field_hash'];?>" class="<?=$dis2;?>">
          <td style="text-align:center;">
                <input type="checkbox" class="checkbox_fields" <?=$dis;?> id="field_checkbox" value="<?=$row['field_status']?>" <?php if ($row['field_status']=="1") {echo "checked";};?>>
          </td>
          <?php
          if ($CONF['fix_subj'] == "true") {
            ?>
            <td>
              <select id="field_subj_select" class="form-control input-sm">
                <option value=""><?=lang('CONF_subj_select');?></option>
                <?php
                $stmt = $dbConnection->prepare('SELECT name, id FROM subj order by position asc');
                $stmt->execute();
                $res2 = $stmt->fetchAll();
                foreach ($res2 as $row2) {
                  ?>
                  <option value="<?=$row2['id'];?>" <?php if($row['field_subj'] == $row2['id']) echo 'selected="selected"';?>><?=$row2['name']?></option>
                  <?php
                }
                 ?>
              </select>
            </td>
            <?php
          }
           ?>
          <td>
            <input autocomplete="off" type="text" class="form-control input-sm" id="field_name" <?=$dis;?> placeholder="name" value="<?=$row['field_name'];?>">
          </td>
          <td>
            <input autocomplete="off" type="text" class="form-control input-sm" id="field_placeholder" <?=$dis;?> placeholder="placeholder" value="<?=$row['field_placeholder'];?>">
          </td>
          <td>
            <input autocomplete="off" type="text" class="form-control input-sm" id="field_value" <?=$dis;?> placeholder="<?=$val;?>" value="<?=$row['field_value'];?>">
          </td>
          <td>
            <select id="field_select" <?=$dis;?> class="form-control input-sm">
              <option value="text" <?php if($row['field_type'] == "text") echo 'selected="selected"';?>><?=lang('CONF_text');?></option>
              <option value="textarea" <?php if($row['field_type'] == "textarea") echo 'selected="selected"';?>><?=lang('CONF_textarea');?></option>
              <option value="select" <?php if($row['field_type'] == "select") echo 'selected="selected"';?>><?=lang('CONF_select');?></option>
              <option value="multiselect" <?php if($row['field_type'] == "multiselect") echo 'selected="selected"';?>><?=lang('CONF_multiselect');?></option>
            </select>
          </td>
          <td style="text-align:center;">
            <button id="del_field" <?=$dis;?> class="btn btn-danger btn-sm"  type="submit"><i class="fa fa-trash"></i>
          </td>
        </tr>
        <?php
      }
       ?>
      </tbody>
    </table>
    <br>
    <?php
  }
  else{
    ?>
<div class="well well-large well-transparent lead">
  <center>
    <?=lang('MSG_no_records')?>
  </center>
</div>
    <?php
  }
}
function form_subj($in){
  global $dbConnection;
  ?>
  <form id="add_field_form_subj">
    <div>
      <?php
      $stmt = $dbConnection->prepare('SELECT field_hash, field_name, field_placeholder, field_value, field_type, field_status FROM dop_fields WHERE field_status = :n and field_subj = :n2 and field_name <> "" order by id asc');
      $stmt->execute(array(':n' => '1', ':n2' => $in));
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
                    <option value=""></option>
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
  <?php
}
function show_files($hn){
  global $CONF;
  global $dbConnection;

  $stmt = $dbConnection->prepare('SELECT file_hash, original_name, file_size FROM files_comment where comment_hash=:tid');
  $stmt->execute(array(':tid'=>$hn));
  $res1 = $stmt->fetchAll();
  if (!empty($res1)) {


      ?>
      <hr style="margin:0px;background-color:red;">
      <div class="row">
          <div class="col-md-4 text-left">
              <small><strong><?=lang('TICKET_file_list')?>:</strong></small>
          </div>
          <div class="col-md-12">
              <table class="table table-hover">
                      <tbody>
                  <?php

                  foreach($res1 as $r) {
                      ?>



      <tr>
          <td style="width:20px;"><small><?=get_file_comment_icon($r['file_hash']);?></small></td>
          <td><small><a href='<?=$CONF['hostname'];?>sys/download.php?step=files_comment&hn=<?=$r['file_hash'];?>'><?=$r['original_name'];?></a></small></td>
          <td><small><?php echo round(($r['file_size']/(1024*1024)),2);?> Mb</small></td>
      </tr>






                  <?php }?>
              </table>
          </div>
      </div>


  <?php
  }
}
function check_unlinked_file() {
global $dbConnection;

$stmt = $dbConnection->prepare('SELECT original_name, ticket_hash, file_hash, file_ext FROM files
LEFT JOIN tickets ON tickets.hash_name = files.ticket_hash
WHERE tickets.hash_name IS NULL');
    $stmt->execute();
$result = $stmt->fetchAll();
        if (!empty($result)) {




foreach ($result as $row) {

                $stmt = $dbConnection->prepare("delete FROM files where ticket_hash=:id");
                $stmt->execute(array(':id'=> $row['ticket_hash']));
unlink(realpath(dirname(__FILE__))."/upload_files/".$row['file_hash'].".".$row['file_ext']);
unlink(realpath(dirname(__FILE__))."/upload_files/thumbnail/".$row['file_hash'].".".$row['file_ext']);


}}


}
function check_unlinked_file_comment() {
global $dbConnection;

$stmt = $dbConnection->prepare('SELECT original_name, comment_hash, file_hash, file_ext FROM files_comment
LEFT JOIN comments ON comments.hashname_comment = files_comment.comment_hash
WHERE comments.hashname_comment IS NULL');
    $stmt->execute();
$result = $stmt->fetchAll();
        if (!empty($result)) {




foreach ($result as $row) {

                $stmt = $dbConnection->prepare("delete FROM files_comment where comment_hash=:id");
                $stmt->execute(array(':id'=> $row['comment_hash']));
unlink(realpath(dirname(__FILE__))."/upload_files/".$row['file_hash'].".".$row['file_ext']);
unlink(realpath(dirname(__FILE__))."/upload_files/thumbnail/".$row['file_hash'].".".$row['file_ext']);


}}


}

function validate_user($user_id, $input) {

    global $dbConnection;

    if (!isset($_SESSION['code'])) {

        if (isset($_COOKIE['authhash_code'])) {

            $user_id=$_COOKIE['authhash_uid'];
            $input=$_COOKIE['authhash_code'];
            $_SESSION['code']=$input;
            $_SESSION['helpdesk_user_id']=$user_id;

        }


    }


    $stmt = $dbConnection->prepare('SELECT pass,login,fio from users where id=:user_id LIMIT 1');
    $stmt->execute(array(':user_id' => $user_id));


    if ($stmt -> rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);







        //$row = mysql_fetch_assoc($sql);
        $dbpass=md5($row['pass']);
        $_SESSION['helpdesk_user_login'] = $row['login'];
        $_SESSION['helpdesk_user_fio'] = $row['fio'];
        //$_SESSION['helpdesk_sort_prio'] == "none";
        if ($dbpass == $input) {return true;}
        else { return false;}
    }
}

function get_user_status($in) {
	    global $dbConnection;

    $stmt = $dbConnection->prepare('select last_time from users where id=:in and us_kill=1');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
	$lt=$total_ticket['last_time'];
        $d = time()-strtotime($lt);
	if ($d > 20) {
	$lt_tooltip="";
  if ($lt != '0000-00-00 00:00:00') {$lt_tooltip=lang('stats_last_time')."<br>".MySQLDateTimeToDateTime($lt);}
  else{$lt_tooltip=lang('stats_last_time')."<br>".lang('stats_last_time_never');}
	$res="<span  val=\"status_offline\" data-toggle=\"tooltip\" data-placement=\"bottom\" class=\"label label-default\" data-original-title=\"".$lt_tooltip."\" data-html=\"true\"><i class=\"fa fa-thumbs-down\"></i> offline</span>";}
	else {$res="<span class=\"label label-success\"><i class=\"fa fa-thumbs-up\"></i> online</span>";}

	return $res;
}

function get_user_status_text($in) {
	    global $dbConnection;

    $stmt = $dbConnection->prepare('select last_time from users where id=:in and us_kill=1');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
	$lt=$total_ticket['last_time'];
	$d = time()-strtotime($lt);
	if ($d > 20) {


	$res="offline";}
	else {$res="online";}

	return $res;
}

function MySQLDateTimeToDateTime($dt)
{

   $str1 = explode("-", $dt);
   $str2 = explode(" ", $str1[2]);
   $str3 = explode(":", $str2[1]);
   $dtt=$str2[0].".".$str1[1].".".$str1[0]." ".$str3[0].":".$str3[1];
   return $dtt;
};

function get_ticket_id_by_hash($in) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select id from tickets where hash_name=:in');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


    $tt=$total_ticket['id'];
    return $tt;
}
function get_ticket_id_by_hash_status($in) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select arch from tickets where hash_name=:in');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    $tt=$total_ticket['arch'];
    if ($tt == '1'){
      $r="<span class=\"label label-default\">".lang('t_list_files_a_arch')."</span>";
    }
    else{
      $r="<span class=\"label label-primary\">".lang('t_list_files_a_work')."</span>";
    }
    return $r;
}
function get_ticket_id_by_comment_hash($in) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select t_id from comments where hashname_comment=:in LIMIT 1');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


    $tt=$total_ticket['t_id'];
    return $tt;
}
function get_ticket_id_by_comment_hash_status($in) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select arch from tickets INNER JOIN comments ON comments.t_id = tickets.id where hashname_comment=:in LIMIT 1');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


    $tt=$total_ticket['arch'];
    if ($tt == '1'){
      $r="<span class=\"label label-default\">".lang('t_list_files_a_arch')."</span>";
    }
    else{
      $r="<span class=\"label label-primary\">".lang('t_list_files_a_work')."</span>";
    }
    return $r;
}

function get_ticket_hash_comment_hash($in) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select hash_name from tickets INNER JOIN comments ON comments.t_id = tickets.id where hashname_comment=:in LIMIT 1');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


    $tt=$total_ticket['hash_name'];
    return $tt;
}
function get_helper() {
    global $dbConnection;


    $user_id   = id_of_user($_SESSION['helpdesk_user_login']);
    $unit_user = unit_of_user($user_id);
    $priv_val  = priv_status($user_id);

    $units = explode(",", $unit_user);
    array_push($units, "0");

    $stmt = $dbConnection->prepare('SELECT
			    id, user_init_id, unit_to_id, dt, title, message, hashname
			    from helper
			    order by dt desc
			    limit 3');
    $stmt->execute();
    $result = $stmt->fetchAll();
    ?>
    <table class="table table-hover" style="margin-bottom: 0px;" id="">
        <?php

        if (empty($result)) {
            ?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?= lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
        } else if (!empty($result)) {
            foreach ($result as $row) {
                $unit2id = explode(",", $row['unit_to_id']);
                $diff    = array_intersect($units, $unit2id);
                if ($priv_val == 1) {
                    if ($diff) {
                        $ac = "ok";
                    }


                    if ($user_id == $row['user_init_id']) {
                        $priv_h = "yes";
                    }
                } else if ($priv_val == 0) {
                    $ac = "ok";
                    if ($user_id == $row['user_init_id']) {
                        $priv_h = "yes";
                    }
                } else if ($priv_val == 2) {
                    $ac     = "ok";
                    $priv_h = "yes";
                }

                if ($ac == "ok") {

                    ?>
                    <tr><td><small><i class="fa fa-file-text-o"></i> </small><a href="helper?h=<?= $row['hashname']; ?>"><small><?= cutstr_help2_ret($row['title']); ?></small></a></td><td><small style="float:right;" class="text-muted">(<?= lang('DASHBOARD_author'); ?>: <?= nameshort(name_of_user_ret($row['user_init_id'])); ?>)</small></td></tr>

                <?php
                }

            }
        }
        ?>
    </table>
<?
}
function get_notes() {
    global $dbConnection;


    $user_id   = id_of_user($_SESSION['helpdesk_user_login']);


    $stmt = $dbConnection->prepare('SELECT
			    id, user_id, dt, message, hashname
			    from notes where  user_id=:user_id
			    order by dt desc
			    limit 2');
    $stmt->execute(array(':user_id'=>$user_id));
    $result = $stmt->fetchAll();
    ?>
    <table class="table table-hover" style="margin-bottom: 0px;" id="">
        <?php

        if (empty($result)) {
            ?>
            <div id="" class="well2 well-transparent lead">
                <center>
                    <?= lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
        } else if (!empty($result)) {
		    foreach ($result as $row) {
		    ?>
                    <tr><td><small><i class="fa fa-file-text-o"></i> </small><small><?= cutstr_notes_ret($row['message']); ?></small></a></td><td><small style="float:right;" class="text-muted">(<?= lang('DASHBOARD_notes_dt'); ?>: <time id="c" datetime="<?=$row['dt']?>"></time>)</small></td></tr>

                <?php
	    }

        }
        ?>
    </table>
<?
}
function get_online_users_total(){
  global $dbConnection;

  $stmt = $dbConnection->prepare('select count(*) as count from users where UNIX_TIMESTAMP(last_time) > UNIX_TIMESTAMP(NOW())-20 and us_kill=1');
  $stmt->execute();
  $cn = $stmt->fetch(PDO::FETCH_ASSOC);
  $count=$cn['count'];

  return $count;
}
function return_users_array_unit($in){
  global $dbConnection;

  $stmt = $dbConnection->prepare('SELECT id from users where unit IN ('.$in.')');
        $stmt->execute();
        $res = $stmt->fetchAll();
        $us_id = array();
        foreach ($res as $r) {
          $id = $r['id'];
          array_push($us_id,$id);
        }
        $u = implode(",",$us_id);
        return $u;
}
function get_users_online(){
  global $dbConnection;

  $stmt = $dbConnection->prepare('select count(*) as count from users where UNIX_TIMESTAMP(last_time) > UNIX_TIMESTAMP(NOW())-20 and us_kill=1');
  $stmt->execute();
  $cn = $stmt->fetch(PDO::FETCH_ASSOC);
  $count=$cn['count'];

    $stmt = $dbConnection->prepare('select id from users where status=:n order by last_time DESC, fio ASC');
    $stmt->execute(array(':n'=>'1'));
    $result = $stmt->fetchAll();
        if (!empty($result)) {
          ?>
          <label><small style="margin-left:6px;"><?=lang('NAVBAR_users_online2');?>: <span id="online2"><?=$count;?></span></small></label>
          <div id="online3">
          <table class="table table-hover">

<?php
  foreach ($result as $row) {
  ?>
                        <tr><td><small style="margin-left:5px;"><?=nameshort(name_of_user_ret($row['id']));?></small></td><td><small style="float:right;"><span><?=get_user_status($row['id']);?><span></small></td></tr>
  		<?php

  		}}
      ?>
    </table>
  </div>
      <?php
}



function get_client_info_ticket($id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT fio,tel,unit_desc,adr,tel_ext,email,login,posada FROM clients where id=:id');
    $stmt->execute(array(':id' => $id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);



    $fio_user=$fio['fio'];
    $loginf=$fio['login'];
    $tel_user=$fio['tel'];
    $pod=$fio['unit_desc'];
    $adr=$fio['adr'];
    $tel_ext=$fio['tel_ext'];

    $posada=$fio['posada'];
    $email=$fio['email'];


    $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(':id' => $id));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


    $tt=$total_ticket['t1'];


    $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
    $stmt->execute(array(':id' => $id));
    $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


    $lt=$last_ticket['dc'];

    $uid=$_SESSION['helpdesk_user_id'];
    $priv_val=priv_status($uid);
    ?>



    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-user"></i> <?=lang('WORKER_TITLE');?></h4>
    </div>
    <div class="panel-body">
        <h4><center><strong><?php echo $fio_user; ?></strong></center></h4>

        <table class="table  ">
            <tbody>
            <?php if ($loginf) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?=lang('WORKER_login');?>:</small></td>
                    <td><small><?=$loginf?></small></td>
                </tr>
            <?php } if ($posada) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?=lang('WORKER_posada');?>:</small></td>
                    <td><small><?php echo $posada; ?></small></td>
                </tr>
            <?php } if ($pod) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?=lang('WORKER_unit');?>:</small></td>
                    <td><small><?php echo $pod; ?></small></td>
                </tr>
            <?php } if ($tel_user) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?=lang('WORKER_tel');?>:</small></td>
                    <td><small><?php echo $tel_user." ".$tel_ext; ?></small></td>
                </tr>
            <?php } if ($adr) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?=lang('WORKER_room');?>:</small></td>
                    <td><small><?php echo $adr; ?></small></td>
                </tr>
            <?php } if ($email) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?=lang('WORKER_mail');?>:</small></td>
                    <td><small><?php echo $email; ?></small></td>
                </tr>
            <?php } ?>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_total');?>:</small></td>
                <td><small>
                        <?php if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?=$id?>">
                            <?php }?>
                            <?php echo $tt; ?>
                            <?php if ($priv_val <> "1") { ?>
                        </a>
                    <?php }?>
                    </small></td>
            </tr>
<?php if ( $tt <> 0) { ?>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_last');?>:</small></td>
                <td><small><?php if ($priv_val <> "1") { ?><a target="_blank" href="userinfo?user=<?=$id?>"><?php } ?>

                <time id="b" datetime="<?=$lt;?>"></time>
                <time id="c" datetime="<?=$lt;?>"></time>

                <?php if ($priv_val <> "1") { ?></a><?php } ?></small></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>

<?php
}

function get_unit_name_return4news($input) {
    global $dbConnection;

    $u=explode(",", $input);
    foreach ($u as $val) {

        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(':val' => $val));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);


        $res=$dep['name'];

    }
    return $res;
}


function get_unit_name_return($input) {
    global $dbConnection;

    $u=explode(",", $input);
    $res=array();
    foreach ($u as $val) {

        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(':val' => $val));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);


	array_push($res, $dep['name']);
        //$res.=$dep['name'];
        //$res.="<br>";
    }

    return $res;
}
function get_fio_name_return($input) {
    global $dbConnection;

    $u=explode(",", $input);
    $res=array();
    foreach ($u as $val) {

        $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:val');
        $stmt->execute(array(':val' => $val));
        $fio = $stmt->fetch(PDO::FETCH_ASSOC);


		array_push($res, $fio['fio']);
        //$res.=$dep['name'];
        //$res.="<br>";
    }

    return $res;
}

function view_array($in) {
$end_element = array_pop($in);
foreach ($in as $value) {
   // делаем что-либо с каждым элементом
        $res.=$value;
        $res.="<br>";
}
$res.=$end_element;
   // делаем что-либо с последним элементом $end_element


    return $res;
}
function view_array2($in) {
$end_element = array_pop($in);
foreach ($in as $value) {
   // делаем что-либо с каждым элементом
        $res.=$value;
        $res.=", ";
}
$res.=$end_element;
   // делаем что-либо с последним элементом $end_element


    return $res;
}

function get_user_val($in) {
    global $CONF;
    global $dbConnection;
    $i=$_SESSION['helpdesk_user_id'];
    $stmt = $dbConnection->prepare('SELECT '.$in.' FROM users where id=:id');
    $stmt->execute(array(':id'=>$i));

    $fior = $stmt->fetch(PDO::FETCH_NUM);



return $fior[0];
}




function get_client_info($id) {
    global $CONF;
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT fio,tel,unit_desc,adr,tel_ext,email,login, posada, email FROM clients where id=:id');
    $stmt->execute(array(':id' => $id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);


    $priv_edit_client=get_user_val('priv_edit_client');
    $fio_user=$fio['fio'];
    $loginf=$fio['login'];
    $tel_user=$fio['tel'];
    $pod=$fio['unit_desc'];
    $adr=$fio['adr'];
    $tel_ext=$fio['tel_ext'];
    $mails=$fio['email'];
    $posada=$fio['posada'];


    $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(':id' => $id));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    $tt=$total_ticket['t1'];

    $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
    $stmt->execute(array(':id' => $id));
    $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    $lt=$last_ticket['dc'];
    $uid=$_SESSION['helpdesk_user_id'];
    $priv_val=priv_status($uid);
    //echo $priv_edit_client;
    if ($priv_edit_client == 1) {$can_edit=true;}
    else if ($priv_edit_client == 0) {$can_edit=false;}
    //$can_edit=false;
    if ($can_edit == true) {
    ?>



    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-user"></i> <?=lang('WORKER_TITLE');?></h4>
    </div>
    <div class="panel-body">
        <h4><center><strong><?php echo $fio_user; ?></strong></center></h4>

        <table class="table  ">
            <tbody>

            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_login');?>:</small></td>
                <td><small><a href="#" id="edit_login" data-type="text"><?=$loginf?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_posada');?>:</small></td>
                <td><small><a href="#" id="edit_posada" data-type="select" data-source="<?=$CONF['hostname'];?>/inc/json.php?posada" data-pk="1" data-title="<?=lang('WORKER_posada');?>"><?=$posada?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_unit');?>:</small></td>
                <td><small><a href="#" id="edit_unit" data-type="select" data-source="<?=$CONF['hostname'];?>/inc/json.php?units" data-pk="1" data-title="<?=lang('NEW_to_unit');?>"><?php echo $pod; ?></a></small></td>
            </tr>

            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_tel');?>:</small></td>
                <td><small><a href="#" id="edit_tel" data-type="text"><?php echo $tel_user." ".$tel_ext; ?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_room');?>:</small></td>
                <td><small><a href="#" id="edit_adr" data-type="text"><?php echo $adr; ?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_mail');?>:</small></td>
                <td><small><a href="#" id="edit_mail" data-type="text"><?=$mails?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_total');?>:</small></td>
                <td><small class="text-muted">
                        <?php if ($priv_val <> "1") { ?>
                         <a target="_blank" href="userinfo?user=<?=$id?>"><?php }?><?php echo $tt; ?><?php if ($priv_val <> "1") { ?></a><?php } ?></small></td>
            </tr>
<?php if ( $tt <> 0) { ?>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_last');?>:</small></td>
                <td><small class="text-muted">
                        <?php if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?=$id?>">
                            <?php }?>
                <time id="b" datetime="<?=$lt;?>"></time>
                <time id="c" datetime="<?=$lt;?>"></time>

                            <?php if ($priv_val <> "1") { ?></a><?php } ?></small></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>

<?php
}



 if ($can_edit == false) {
    ?>



    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-user"></i> <?=lang('WORKER_TITLE');?></h4>
    </div>
    <div class="panel-body">
        <h4><center><strong><?php echo $fio_user; ?></strong></center></h4>

        <table class="table  ">
            <tbody>

            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_login');?>:</small></td>
                <td><small><?=$loginf?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_posada');?>:</small></td>
                <td><small><?=$posada?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_unit');?>:</small></td>
                <td><small><?php echo $pod; ?></small></td>
            </tr>

            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_tel');?>:</small></td>
                <td><small><?php echo $tel_user." ".$tel_ext; ?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_room');?>:</small></td>
                <td><small><?php echo $adr; ?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?=lang('WORKER_mail');?>:</small></td>
                <td><small><?=$mails?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_total');?>:</small></td>
                <td><small class="text-muted">
                        <?php if ($priv_val <> "1") { ?>
                         <a target="_blank" href="userinfo?user=<?=$id?>"><?php }?><?php echo $tt; ?><?php if ($priv_val <> "1") { ?></a><?php } ?></small></td>
            </tr>

            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_last');?>:</small></td>
                <td><small class="text-muted">
                        <?php if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?=$id?>">
                            <?php }?><?php echo $lt; ?><?php if ($priv_val <> "1") { ?></a><?php } ?></small></td>
            </tr>
            </tbody>
        </table>

    </div>

<?php
}



}
function client_unit($input) {
    global $dbConnection;


    $stmt = $dbConnection->prepare('SELECT unit_desc FROM clients where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);


    echo($fio['unit_desc']);

}
function id_of_user($input) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT id FROM users where login=:input');
    $stmt->execute(array(':input' => $input));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($id['id']);
}


function priv_status_name($input) {
    global $dbConnection;


    $stmt = $dbConnection->prepare('SELECT priv FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);

switch($id['priv']) {
    case '2': 	$r="<strong class=\"text-warning\">".lang('USERS_nach1')."</strong>";	break;
    case '0': 	$r="<strong class=\"text-success\">".lang('USERS_nach')."</strong>";	break;
    case '1': 	$r="<strong class=\"text-info\">".lang('USERS_wo')."</strong>";	break;
    default: $r="";
}


    return ($r);
}



function priv_status($input) {
    global $dbConnection;


    $stmt = $dbConnection->prepare('SELECT priv FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);


    return ($id['priv']);
}
function show_noty($input) {
    global $dbConnection;


    $stmt = $dbConnection->prepare('SELECT show_noty FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);


    return ($id['show_noty']);
}
// function get_last_ticket_new_jabber() {
//   global $dbConnection;
//   $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets;");
//   $stmt->execute();
//   $max = $stmt->fetch(PDO::FETCH_NUM);
//
//   $max_id=$max[0];
//
// return $max_id;
// }
function get_last_ticket_new($id) {
    global $dbConnection;
    $unit_user=unit_of_user($id);
    $priv_val=priv_status($id);
    $units = explode(",", $unit_user);
    $u = return_users_array_unit($unit_user);


    $units =implode("', '", $units);

$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}

$ee2=explode(",", $u);
foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}
    if ($priv_val == "0") {

        $stmt = $dbConnection->prepare('SELECT max(last_update) from tickets where unit_id IN ('.$in_query.') or user_init_id IN ('.$in_query2.')');

            // $paramss=array(':id' => $id);
            $stmt->execute(array_merge($vv,$vv2));




        $max = $stmt->fetch(PDO::FETCH_NUM);



        $max_id=$max[0];
        //echo $max_id;
    }


    else if ($priv_val == "1") {


        $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((user_to_id rlike :id) or (user_to_id=:tid and unit_id IN (".$in_query."))) or user_init_id=:id2");


        $paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':tid' => '0', ':id2' => $id);
        $stmt->execute(array_merge($vv,$paramss));


        $max = $stmt->fetch(PDO::FETCH_NUM);



        $max_id=$max[0];



    }

    else if ($priv_val == "2") {




        $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets;");
        $stmt->execute();
        $max = $stmt->fetch(PDO::FETCH_NUM);



        $max_id=$max[0];



    }
    return $max_id;
}

function get_who_last_action_ticket($ticket_id) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select init_user_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    $r=$fio['init_user_id'];
    return $r;
}

function get_last_action_type($ticket_id) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);



    $r=$fio['msg'];
    return $r;
}
function get_last_action_ticket($ticket_id) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);



    $r=$fio['msg'];
    $uss=nameshort(name_of_user_ret($fio['init_user_id']));
    $uss_to=nameshort(name_of_user_ret($fio['to_user_id']));
    $unit_to=get_unit_name_return4news($fio['to_unit_id']);
    if ($r=='refer') {$red='<i class=\'fa fa-long-arrow-right\'></i> '.lang('TICKET_ACTION_refer').' <em>'.$uss.'</em> '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to;}
    if ($r=='ok') {$red='<i class=\'fa fa-check-circle-o\'></i> '.lang('TICKET_ACTION_ok').' <em>'.$uss.'</em>';}
    if ($r=='no_ok') {$red='<i class=\'fa fa-circle-o\'></i> '.lang('TICKET_ACTION_nook').' <em>'.$uss.'</em>';}
    if ($r=='lock') {$red='<i class=\'fa fa-lock\'></i> '.lang('TICKET_ACTION_lock').' <em>'.$uss.'</em>';}
    if ($r=='unlock') {$red='<i class=\'fa fa-unlock\'></i> '.lang('TICKET_ACTION_unlock').' <em>'.$uss.'</em>';}
    if ($r=='create') {$red='<i class=\'fa fa-check-square\'></i> '.lang('TICKET_ACTION_create').' <em>'.$uss.'</em>';}
    if ($r=='familiar') {$red='<i class=\'fa fa-hand-o-right\'></i> '.lang('TICKET_ACTION_familiar').' <em>'.$uss.'</em>';}
    if ($r=='edit_msg') {$red='<i class=\'fa fa-pencil-square\'></i> '.lang('TICKET_ACTION_edit').' <em>'.$uss.'</em>';}
    if ($r=='edit_subj') {$red='<i class=\'fa fa-pencil-square\'></i> '.lang('TICKET_ACTION_edit').' <em>'.$uss.'</em>';}
    if ($r=='comment') {$red='<i class=\'fa fa-comment\'></i> '.lang('TICKET_ACTION_comment').' <em>'.$uss.'</em>';}
    if ($r == 'arch') {$red='<i class=\'fa fa-archive\'></i> '.lang('TICKET_ACTION_arch').'';}
    return $red;
}
function get_last_action_ticket_noty($ticket_id,$uid) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbConnection->prepare('select noty from users where id=:id');
    $stmt->execute(array(':id' => $uid));
    $notys = $stmt->fetch(PDO::FETCH_ASSOC);
    $noty = explode(",",$notys['noty']);

    $r=$fio['msg'];
    $uss=nameshort(name_of_user_ret($fio['init_user_id']));
    $uss_to=nameshort(name_of_user_ret($fio['to_user_id']));
    $unit_to=get_unit_name_return4news($fio['to_unit_id']);
    switch ($r) {
      case 'create':
        if (in_array('1',$noty)){
          $red='<i class=\'fa fa-star-o\'></i> '.lang('TICKET_ACTION_create').' <em>'.$uss.'</em>';
        }
        break;
        case 'refer':
          if (in_array('2',$noty)){
          $red='<i class=\'fa fa-long-arrow-right\'></i> '.lang('TICKET_ACTION_refer').' <em>'.$uss.'</em> '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to;
        }
          break;
          case 'comment':
            if (in_array('3',$noty)){
              $red='<i class=\'fa fa-comment\'></i> '.lang('TICKET_ACTION_comment').' <em>'.$uss.'</em>';
            }
            break;
            case 'lock':
              if (in_array('4',$noty)){
              $red='<i class=\'fa fa-lock\'></i> '.lang('TICKET_ACTION_lock').' <em>'.$uss.'</em>';
            }
              break;
              case 'unlock':
                if (in_array('5',$noty)){
                  $red='<i class=\'fa fa-unlock\'></i> '.lang('TICKET_ACTION_unlock').' <em>'.$uss.'</em>';
                }
                break;
                case 'ok':
                  if (in_array('6',$noty)){
                    $red='<i class=\'fa fa-check-circle-o\'></i> '.lang('TICKET_ACTION_ok').' <em>'.$uss.'</em>';
                  }
                  break;
                  case 'no_ok':
                    if (in_array('7',$noty)){
                    $red='<i class=\'fa fa-circle-o\'></i> '.lang('TICKET_ACTION_nook').' <em>'.$uss.'</em>';
                    }
                    break;
                    case 'edit_msg':
                      if (in_array('8',$noty)){
                      $red='<i class=\'fa fa-pencil-square\'></i> '.lang('TICKET_ACTION_edit').' <em>'.$uss.'</em>';
                      }
                      break;
                      case 'edit_subj':
                        if (in_array('9',$noty)){
                          $red='<i class=\'fa fa-pencil-square\'></i> '.lang('TICKET_ACTION_edit').' <em>'.$uss.'</em>';
                        }
                        break;
                        case 'familiar':
                          if (in_array('10',$noty)){
                            $red='<i class=\'fa fa-hand-o-right\'></i> '.lang('TICKET_ACTION_familiar').' <em>'.$uss.'</em>';
                          }
                          break;
    }
    // var_dump($rr);
    // if ($rr == $key) {$red='<i class=\'fa fa-long-arrow-right\'></i> '.lang('TICKET_ACTION_refer').' <em>'.$uss.'</em> '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to;}
    // if ($rr == $key) {$red='<i class=\'fa fa-check-circle-o\'></i> '.lang('TICKET_ACTION_ok').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-circle-o\'></i> '.lang('TICKET_ACTION_nook').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-lock\'></i> '.lang('TICKET_ACTION_lock').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-unlock\'></i> '.lang('TICKET_ACTION_unlock').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-star-o\'></i> '.lang('TICKET_ACTION_create').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-pencil-square\'></i> '.lang('TICKET_ACTION_edit').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-pencil-square\'></i> '.lang('TICKET_ACTION_edit').' <em>'.$uss.'</em>';}
    // if ($rr == $key) {$red='<i class=\'fa fa-comment\'></i> '.lang('TICKET_ACTION_comment').' <em>'.$uss.'</em>';}
    return $red;
}
function get_last_action_ticket2($ticket_id,$uid) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbConnection->prepare('select noty from users where id=:id');
    $stmt->execute(array(':id' => $uid));
    $notys = $stmt->fetch(PDO::FETCH_ASSOC);
    $noty = explode(",",$notys['noty']);

    $r=$fio['msg'];
    $uss=nameshort(name_of_user_ret($fio['init_user_id']));
    $uss_to=nameshort(name_of_user_ret($fio['to_user_id']));
    $unit_to=get_unit_name_return4news($fio['to_unit_id']);
    switch ($r) {
      case 'create':
        if (in_array('1',$noty)){
          $red=''.lang('TICKET_ACTION_create').' '.$uss;
        }
        break;
        case 'refer':
          if (in_array('2',$noty)){
          $red=''.lang('TICKET_ACTION_refer').' '.$uss.' '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to;
        }
          break;
          case 'comment':
            if (in_array('3',$noty)){
              $red=''.lang('TICKET_ACTION_comment').' '.$uss;
            }
            break;
            case 'lock':
              if (in_array('4',$noty)){
              $red=''.lang('TICKET_ACTION_lock').' '.$uss;
            }
              break;
              case 'unlock':
                if (in_array('5',$noty)){
                  $red=''.lang('TICKET_ACTION_unlock').' '.$uss;
                }
                break;
                case 'ok':
                  if (in_array('6',$noty)){
                    $red=''.lang('TICKET_ACTION_ok').' '.$uss;
                  }
                  break;
                  case 'no_ok':
                    if (in_array('7',$noty)){
                    $red=''.lang('TICKET_ACTION_nook').' '.$uss;
                    }
                    break;
                    case 'edit_msg':
                      if (in_array('8',$noty)){
                      $red=''.lang('TICKET_ACTION_edit').' '.$uss;
                      }
                      break;
                      case 'edit_subj':
                        if (in_array('9',$noty)){
                          $red=''.lang('TICKET_ACTION_edit').' '.$uss;
                        }
                        break;
                        case 'familiar':
                          if (in_array('10',$noty)){
                            $red=''.lang('TICKET_ACTION_familiar').' '.$uss;
                          }
                          break;
    }
    // $r=$fio['msg'];
    // $uss=nameshort(name_of_user_ret($fio['init_user_id']));
    // $uss_to=nameshort(name_of_user_ret($fio['to_user_id']));
    // $unit_to=get_unit_name_return4news($fio['to_unit_id']);
    // if ($r=='refer') {$red=''.lang('TICKET_ACTION_refer').' '.$uss.' '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to;}
    // if ($r=='ok') {$red=''.lang('TICKET_ACTION_ok').' '.$uss;}
    // if ($r=='no_ok') {$red=''.lang('TICKET_ACTION_nook').' '.$uss;}
    // if ($r=='lock') {$red=''.lang('TICKET_ACTION_lock').' '.$uss;}
    // if ($r=='unlock') {$red=''.lang('TICKET_ACTION_unlock').' '.$uss;}
    // if ($r=='create') {$red=''.lang('TICKET_ACTION_create').' '.$uss;}
    // if ($r=='edit_msg') {$red=''.lang('TICKET_ACTION_edit').' '.$uss;}
    // if ($r=='edit_subj') {$red=''.lang('TICKET_ACTION_edit').' '.$uss;}
    // if ($r=='comment') {$red=''.lang('TICKET_ACTION_comment').' '.$uss;}
    // if ($r == 'arch') {$red=''.lang('TICKET_ACTION_arch').'';}
    return $red;
}

function get_last_action_ticket_jabber($ticket_id,$uid) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbConnection->prepare('select jabber_noty_show from users where id=:id');
    $stmt->execute(array(':id' => $uid));
    $notys = $stmt->fetch(PDO::FETCH_ASSOC);
    $noty = explode(",",$notys['jabber_noty_show']);

    $r=$fio['msg'];
    $uss=nameshort(name_of_user_ret($fio['init_user_id']));
    $uss_to=nameshort(name_of_user_ret($fio['to_user_id']));
    $unit_to=get_unit_name_return4news($fio['to_unit_id']);
    switch ($r) {
        case 'refer':
          if (in_array('2',$noty)){
          $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_refer').' '.$uss.' '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to;
        }
          break;
          case 'comment':
            if (in_array('3',$noty)){
              $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_comment').' '.$uss;
            }
            break;
            case 'lock':
              if (in_array('4',$noty)){
              $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_lock').' '.$uss;
            }
              break;
              case 'unlock':
                if (in_array('5',$noty)){
                  $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_unlock').' '.$uss;
                }
                break;
                case 'ok':
                  if (in_array('6',$noty)){
                    $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_ok').' '.$uss;
                  }
                  break;
                  case 'no_ok':
                    if (in_array('7',$noty)){
                    $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_nook').' '.$uss;
                    }
                    break;
                    case 'edit_msg':
                      if (in_array('8',$noty)){
                      $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_edit').' '.$uss;
                      }
                      break;
                      case 'edit_subj':
                        if (in_array('9',$noty)){
                          $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_edit').' '.$uss;
                        }
                        break;
                        case 'familiar':
                          if (in_array('10',$noty)){
                            $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_familiar').' '.$uss;
                          }
                          break;
    }
    return $red;
}
function get_last_action_ticket_mail($ticket_id,$uid) {
    global $dbConnection;
    global $CONF;

    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbConnection->prepare('select hash_name from tickets where id=:ticket_id limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $hash = $stmt->fetch(PDO::FETCH_ASSOC);
    $h = $hash['hash_name'];

    $stmt = $dbConnection->prepare('select mail_noty_show from users where id=:id');
    $stmt->execute(array(':id' => $uid));
    $notys = $stmt->fetch(PDO::FETCH_ASSOC);
    $noty = explode(",",$notys['mail_noty_show']);

    $r=$fio['msg'];
    $uss=nameshort(name_of_user_ret($fio['init_user_id']));
    $uss_to=nameshort(name_of_user_ret($fio['to_user_id']));
    $unit_to=get_unit_name_return4news($fio['to_unit_id']);
    switch ($r) {
        case 'refer':
          if (in_array('2',$noty)){
          // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_refer').' '.$uss.' '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to.' <a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
          $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
          <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_refer').' '.$uss.' '.lang('TICKET_ACTION_refer_to').' '.$unit_to.' '.$uss_to.'</p>
          <table width="100%" cellpadding="3" cellspacing="0">
            <tbody>
              <tr id="tr_">
                <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
              font-size: 12px;">'.lang('MAIL_code').':</td>
                <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
              font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
              font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
              </tr>
            </tbody>
          </table>
          </center>
          </div>';
        }
          break;
          case 'comment':
            if (in_array('3',$noty)){
              // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_comment').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a>';
              $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
              <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_comment').' '.$uss.'</p>
              <table width="100%" cellpadding="3" cellspacing="0">
                <tbody>
                  <tr id="tr_">
                    <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                  font-size: 12px;">'.lang('MAIL_code').':</td>
                    <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                  font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                    <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                  font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                  </tr>
                </tbody>
              </table>
              </center>
              </div>';
            }
            break;
            case 'lock':
              if (in_array('4',$noty)){
              // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_lock').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
              $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
              <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_lock').' '.$uss.'</p>
              <table width="100%" cellpadding="3" cellspacing="0">
                <tbody>
                  <tr id="tr_">
                    <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                  font-size: 12px;">'.lang('MAIL_code').':</td>
                    <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                  font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                    <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                  font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                  </tr>
                </tbody>
              </table>
              </center>
              </div>';
            }
              break;
              case 'unlock':
                if (in_array('5',$noty)){
                  // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_unlock').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
                  $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
                  <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_unlock').' '.$uss.'</p>
                  <table width="100%" cellpadding="3" cellspacing="0">
                    <tbody>
                      <tr id="tr_">
                        <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                      font-size: 12px;">'.lang('MAIL_code').':</td>
                        <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                      font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                        <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                      font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                      </tr>
                    </tbody>
                  </table>
                  </center>
                  </div>';
                }
                break;
                case 'ok':
                  if (in_array('6',$noty)){
                    // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_ok').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
                    $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_ok').' '.$uss.'</p>
                    <table width="100%" cellpadding="3" cellspacing="0">
                      <tbody>
                        <tr id="tr_">
                          <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                        font-size: 12px;">'.lang('MAIL_code').':</td>
                          <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                        font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                          <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                        font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                        </tr>
                      </tbody>
                    </table>
                    </center>
                    </div>';
                  }
                  break;
                  case 'no_ok':
                    if (in_array('7',$noty)){
                    // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_nook').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
                    $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_nook').' '.$uss.'</p>
                    <table width="100%" cellpadding="3" cellspacing="0">
                      <tbody>
                        <tr id="tr_">
                          <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                        font-size: 12px;">'.lang('MAIL_code').':</td>
                          <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                        font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                          <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                        font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                        </tr>
                      </tbody>
                    </table>
                    </center>
                    </div>';
                    }
                    break;
                    case 'edit_msg':
                      if (in_array('8',$noty)){
                      // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_edit').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
                      $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
                      <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_edit').' '.$uss.'</p>
                      <table width="100%" cellpadding="3" cellspacing="0">
                        <tbody>
                          <tr id="tr_">
                            <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                          font-size: 12px;">'.lang('MAIL_code').':</td>
                            <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                          font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                            <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                          font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                          </tr>
                        </tbody>
                      </table>
                      </center>
                      </div>';
                      }
                      break;
                      case 'edit_subj':
                        if (in_array('9',$noty)){
                          // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_edit').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
                          $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
                          <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_edit').' '.$uss.'</p>
                          <table width="100%" cellpadding="3" cellspacing="0">
                            <tbody>
                              <tr id="tr_">
                                <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                              font-size: 12px;">'.lang('MAIL_code').':</td>
                                <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                              font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                                <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                              font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                              </tr>
                            </tbody>
                          </table>
                          </center>
                          </div>';
                        }
                        break;
                        case 'familiar':
                          if (in_array('10',$noty)){
                            // $red=''.lang('TICKET_name').' #'.$ticket_id.' - '.lang('TICKET_ACTION_familiar').' '.$uss.' '.$unit_to.' '.$uss_to.'&nbsp;&nbsp;<a href='.$CONF['hostname'].'ticket?'.$h.'>'.lang('MAIL_2link').'</a>';
                            $red = '<div style="background: #FAFCFF; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
                            <p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">'.lang('TICKET_name').' '.lang('TICKET_ACTION_familiar').' '.$uss.'</p>
                            <table width="100%" cellpadding="3" cellspacing="0">
                              <tbody>
                                <tr id="tr_">
                                  <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                                font-size: 12px;">'.lang('MAIL_code').':</td>
                                  <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                                font-size: 19px;"><b>#'.$ticket_id.'</b></td>
                                  <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
                                font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href="'.$CONF['hostname'].'ticket?'.$h.'">'.lang('MAIL_2link').'</a></p></td>
                                </tr>
                              </tbody>
                            </table>
                            </center>
                            </div>';
                          }
                          break;
    }
    return $red;
}
function get_last_action_ticket_mail_subj($ticket_id,$uid) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('select msg from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(':ticket_id' => $ticket_id));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbConnection->prepare('select mail_noty_show from users where id=:id');
    $stmt->execute(array(':id' => $uid));
    $notys = $stmt->fetch(PDO::FETCH_ASSOC);
    $noty = explode(",",$notys['mail_noty_show']);

    $r=$fio['msg'];
    switch ($r) {
        case 'refer':
          if (in_array('2',$noty)){
          $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_refer').')';
        }
          break;
          case 'comment':
            if (in_array('3',$noty)){
              $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_comment').')';
            }
            break;
            case 'lock':
              if (in_array('4',$noty)){
              $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_lock').')';
            }
              break;
              case 'unlock':
                if (in_array('5',$noty)){
                  $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_unlock').')';
                }
                break;
                case 'ok':
                  if (in_array('6',$noty)){
                    $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_ok').')';
                  }
                  break;
                  case 'no_ok':
                    if (in_array('7',$noty)){
                    $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_nook').')';
                    }
                    break;
                    case 'edit_msg':
                      if (in_array('8',$noty)){
                      $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_edit').')';
                      }
                      break;
                      case 'edit_subj':
                        if (in_array('9',$noty)){
                          $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_edit').')';
                        }
                        break;
                        case 'familiar':
                          if (in_array('10',$noty)){
                            $red=''.lang('TICKET_name').' #'.$ticket_id.' ('.lang('TICKET_ACTION_MAIL_familiar').')';
                          }
                          break;
    }
    return $red;
}

function get_last_ticket($menu, $id) {
    global $dbConnection;




    if ($menu == "all") {
        $unit_user=unit_of_user($id);
        $priv_val=priv_status($id);
        $u = return_users_array_unit($unit_user);

	$ee=explode(",", $unit_user);
	foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
	$in_query = substr($in_query, 0, -2);
	foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}

  $ee2=explode(",", $u);
  foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
  $in_query2 = substr($in_query2, 0, -2);
  foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}
        if ($priv_val == "0") {
          // var_dump($ee2);


            $stmt = $dbConnection->prepare('SELECT max(last_update) from tickets where unit_id IN ('.$in_query.') or user_init_id IN ('.$in_query2.')');
            // $paramss=array(':id' => $id);
            $stmt->execute(array_merge($vv,$vv2));
            // var_dump($stmt);

            $max = $stmt->fetch(PDO::FETCH_NUM);
            // var_dump($max);

	    $max_id=$max[0];
        }


        else if ($priv_val == "1") {
	    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where (
	    (user_to_id rlike :id) or (user_to_id=:tid and unit_id IN (".$in_query."))
	    ) or user_init_id=:id2");
            $paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':tid' => '0', ':id2' => $id);
            $stmt->execute(array_merge($vv,$paramss));

            $max = $stmt->fetch(PDO::FETCH_NUM);
	    $max_id=$max[0];



        }
        else if ($priv_val == "2") {


            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets");
            $stmt->execute();
            $max = $stmt->fetch(PDO::FETCH_NUM);

            $max_id=$max[0];

        }
    }
    else if ($menu == "in") {



        $unit_user=unit_of_user($id);
        $priv_val=priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);
$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}



        if ($priv_val == "0") {



        if (isset($_SESSION['hd.rustem_sort_in'])) {
if ($_SESSION['hd.rustem_sort_in'] == "ok"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (".$in_query.") and arch='0' and status=:s");
$paramss=array(':s' => '1');
$stmt->execute(array_merge($vv,$paramss));
}
else if ($_SESSION['hd.rustem_sort_in'] == "free"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (".$in_query.") and arch='0' and status=:s and lock_by=:lb");
$paramss=array(':s'=>'0',':lb' => '0');
$stmt->execute(array_merge($vv,$paramss));}
else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (".$in_query.") and arch='0' and lock_by=:lb and (status=0)");
$paramss=array(':lb' => $id);
$stmt->execute(array_merge($vv,$paramss));}
else if ($_SESSION['hd.rustem_sort_in'] == "lock"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (".$in_query.") and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$paramss=array(':lb' => $id);
$stmt->execute(array_merge($vv,$paramss));
}
}
if (!isset($_SESSION['hd.rustem_sort_in'])) {
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (".$in_query.") and arch='0'");
//$stmt->execute(array(':units' => $units));
$stmt->execute($vv);
}
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}


        else if ($priv_val == "1") {


            if (isset($_SESSION['hd.rustem_sort_in'])) {
if ($_SESSION['hd.rustem_sort_in'] == "ok"){$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and status=:s");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':s'=>'1');
$stmt->execute(array_merge($vv,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "free"){$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and lock_by=:lb and status=:s");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':lb'=>'0', ':s'=>'0');
$stmt->execute(array_merge($vv,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and lock_by=:lb and status=0");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':lb'=>$id);
$stmt->execute(array_merge($vv,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
else if ($_SESSION['hd.rustem_sort_in'] == "lock"){$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and (lock_by<>:lb and lock_by<>0) and (status=0)");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':lb'=>$id);
$stmt->execute(array_merge($vv,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
}
if (!isset($_SESSION['hd.rustem_sort_in'])) { $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0'))");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]');
$stmt->execute(array_merge($vv,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
        else if ($priv_val == "2") {

if (isset($_SESSION['hd.rustem_sort_in'])) {
if ($_SESSION['hd.rustem_sort_in'] == "ok"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and status=:s");
$stmt->execute(array(':s'=>'1'));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
else if ($_SESSION['hd.rustem_sort_in'] == "free"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and lock_by=:lb and status=:s");
$stmt->execute(array(':lb'=>'0',':s'=>'0'));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and lock_by=:lb and (status=0)");
$stmt->execute(array(':lb'=>$id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
else if ($_SESSION['hd.rustem_sort_in'] == "lock"){	$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$stmt->execute(array(':lb'=>$id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
}
if (!isset($_SESSION['hd.rustem_sort_in'])) {
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0'");
$stmt->execute();
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];}
}
}


    else if ($menu == "out") {
      $priv_val=priv_status($id);
      $unit_user=unit_of_user($id);
      $u = return_users_array_unit($unit_user);
      $ee2=explode(",", $u);
      foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
      $in_query2 = substr($in_query2, 0, -2);
      foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}

if($priv_val == "0"){
if (isset($_SESSION['hd.rustem_sort_out'])) {
if ($_SESSION['hd.rustem_sort_out'] == "ok"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id IN (".$in_query2.") and arch='0' and status=:s");
// $stmt->execute(array(':s'=>'1'));
$paramss=array(':s'=>'1');
$stmt->execute(array_merge($vv2,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "free"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id IN (".$in_query2.") and arch='0' and lock_by=:lb and status=:s");
// $stmt->execute(array(':lb'=>'0', ':s'=>'0'));
$paramss=array(':lb'=>'0', ':s'=>'0');
$stmt->execute(array_merge($vv2,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "ilock"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id IN (".$in_query2.") and arch='0' and lock_by=:lb and (status=0)");
// $stmt->execute(array(':lb'=>$id));
$paramss=array(':lb'=>$id);
$stmt->execute(array_merge($vv2,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "lock"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id IN (".$in_query2.") and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
// $stmt->execute(array(':lb'=>$id));
$paramss=array(':lb'=>$id);
$stmt->execute(array_merge($vv2,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_out'])) {
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id IN (".$in_query2.") and arch=:n");
// $stmt->execute();
$paramss=array(':n'=>'0');
$stmt->execute(array_merge($vv2,$paramss));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
if($priv_val == "1"){
if (isset($_SESSION['hd.rustem_sort_out'])) {
if ($_SESSION['hd.rustem_sort_out'] == "ok"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and status=:s");
$stmt->execute(array(':id' => $id, ':s'=>'1'));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "free"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and status=:s");
$stmt->execute(array(':id' => $id, ':lb'=>'0', ':s'=>'0'));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "ilock"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and (status=0)");
$stmt->execute(array(':id' => $id, ':lb'=>$id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "lock"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$stmt->execute(array(':id' => $id, ':lb'=>$id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_out'])) {
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0'");
$stmt->execute(array(':id' => $id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
if($priv_val == "2"){
if (isset($_SESSION['hd.rustem_sort_out'])) {
if ($_SESSION['hd.rustem_sort_out'] == "ok"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and status=:s");
$stmt->execute(array(':id' => $id, ':s'=>'1'));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "free"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and status=:s");
$stmt->execute(array(':id' => $id, ':lb'=>'0', ':s'=>'0'));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "ilock"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and (status=0)");
$stmt->execute(array(':id' => $id, ':lb'=>$id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "lock"){
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$stmt->execute(array(':id' => $id, ':lb'=>$id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_out'])) {
$stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0'");
$stmt->execute(array(':id' => $id));
$max = $stmt->fetch(PDO::FETCH_NUM);
$max_id=$max[0];
}
}
}


    else if ($menu == "arch") {




        $unit_user=unit_of_user($id);
        $priv_val=priv_status($id);


$ee=explode(",", $unit_user);
$s=1;
foreach($ee as $key=>$value) { $in_query = $in_query . ' :val_' . $key . ', '; $s++; }
$c=($s-1);
foreach($ee as $key=>$value) {$in_query2 = $in_query2 . ' :val_' . ($c+$key) . ', '; }
$in_query = substr($in_query, 0, -2);
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}
 foreach ($ee as $key=>$value) { $vv2[":val_" . ($c+$key)]=$value;}

        if ($priv_val == "0") {

            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (".$in_query.") and arch='1'");

            //$stmt->execute(array(':units' => $units));
            $stmt->execute($vv);
            $max = $stmt->fetch(PDO::FETCH_NUM);

            $max_id=$max[0];




        }


        else if ($priv_val == "1") {





            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where
            (user_to_id rlike :id and unit_id IN (".$in_query.") and arch='1')
             or
	    (user_to_id='0' and unit_id IN (".$in_query2.") and arch='1')");





            $paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]');
	    $stmt->execute(array_merge($vv,$vv2,$paramss));


            $max = $stmt->fetch(PDO::FETCH_NUM);





            $max_id=$max[0];



        }
        else if ($priv_val == "2") {

            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='1'");
            $stmt->execute();
            $max = $stmt->fetch(PDO::FETCH_NUM);

            $max_id=$max[0];




        }









    }





    return $max_id;

}

function get_unit_stat_free($in){
global $dbConnection;
$res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=0 and lock_by=0');
$res->execute(array(':uid' => $in));
$count = $res->fetch(PDO::FETCH_NUM);
return $count[0];
}
function get_unit_stat_lock($in){
global $dbConnection;
$res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=0 and lock_by!=0');
$res->execute(array(':uid' => $in));
$count = $res->fetch(PDO::FETCH_NUM);
return $count[0];
}
function get_unit_stat_ok($in){
global $dbConnection;
$res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=1');
$res->execute(array(':uid' => $in));
$count = $res->fetch(PDO::FETCH_NUM);
return $count[0];
}

function get_total_tickets_out($in) {

    global $dbConnection;
    if (empty($in)) {$uid=$_SESSION['helpdesk_user_id'];}
     else if (!empty($in)) {$uid=$in;}
    $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id=:uid');
    $res->execute(array(':uid' => $uid));
    $count = $res->fetch(PDO::FETCH_NUM);


    return $count[0];
}
function get_total_tickets_lock($in) {
    global $dbConnection;
    if (empty($in)) {$uid=$_SESSION['helpdesk_user_id'];}
     else if (!empty($in)) {$uid=$in;}


    $res = $dbConnection->prepare("SELECT count(*) from tickets where lock_by=:uid and status='0'");
    $res->execute(array(':uid' => $uid));
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}
function get_total_tickets_ok($in) {
    global $dbConnection;

    if (empty($in)) {$uid=$_SESSION['helpdesk_user_id'];}
     else if (!empty($in)) {$uid=$in;}
    $res = $dbConnection->prepare("SELECT count(*) from tickets where ok_by=:uid");
    $res->execute(array(':uid' => $uid));
    $count = $res->fetch(PDO::FETCH_NUM);



    return $count[0];
}
function get_total_tickets_out_and_success($in) {


    global $dbConnection;

    if (empty($in)) {$uid=$_SESSION['helpdesk_user_id'];}
     else if (!empty($in)) {$uid=$in;}

$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (ok_by='0') and arch='0'");
$res->execute(array(':uid' => $uid));
$count = $res->fetch(PDO::FETCH_NUM);

    return $count[0];
}
function return_lang_out_label(){
  global $dbConnection;

  $uid=$_SESSION['helpdesk_user_id'];
  $priv_val=priv_status($uid);
  if ($priv_val == "0"){
    $text = lang('DASHBOARD_ticket_out_desc_unit');
  }
  if ($priv_val == "1"){
    $text = lang('DASHBOARD_ticket_out_desc');
  }
  if ($priv_val == "2"){
    $text = lang('DASHBOARD_ticket_out_desc');
  }
  return $text;
}
function get_total_tickets_out_and_success_count($in) {

    global $dbConnection;

    if (empty($in)) {$uid=$_SESSION['helpdesk_user_id'];}
     else if (!empty($in)) {$uid=$in;}
      $priv_val=priv_status($uid);
      $unit_user=unit_of_user($uid);
      $u = return_users_array_unit($unit_user);
      $ee2=explode(",", $u);
      foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
      $in_query2 = substr($in_query2, 0, -2);
      foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}

      if ($priv_val == "0"){
    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id IN (".$in_query2.") and (ok_by=:n) and arch=:n1");
    // $res->execute();
    $paramss=array(':n'=>'0', ':n1'=>'0');
    $res->execute(array_merge($vv2,$paramss));
    $count = $res->fetch(PDO::FETCH_NUM);
  }
  if ($priv_val == "1"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (ok_by='0') and arch='0'");
$res->execute(array(':uid' => $uid));
$count = $res->fetch(PDO::FETCH_NUM);
}
if ($priv_val == "2"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (ok_by='0') and arch='0'");
$res->execute(array(':uid' => $uid));
$count = $res->fetch(PDO::FETCH_NUM);
}

    return $count[0];
}
function get_total_tickets_out_and_lock() {
    global $dbConnection;
    $uid=$_SESSION['helpdesk_user_id'];

    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (lock_by!='0' and ok_by='0') and arch='0'");
    $res->execute(array(':uid' => $uid));
    $count = $res->fetch(PDO::FETCH_NUM);

    return $count[0];
}






function get_total_tickets_out_and_ok() {
    global $dbConnection;
    $uid=$_SESSION['helpdesk_user_id'];


    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (ok_by!='0') and arch='0'");
    $res->execute(array(':uid' => $uid));
    $count = $res->fetch(PDO::FETCH_NUM);

    return $count[0];
}

function get_total_tickets_free($in) {
    global $dbConnection;
    if (empty($in)) {$uid=$_SESSION['helpdesk_user_id'];}
     else if (!empty($in)) {$uid=$in;}
    $unit_user=unit_of_user($uid);
    $priv_val=priv_status($uid);

    $units = $unit_user;



$in_query="";
$unit_user=unit_of_user($uid);
$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}




    if ($priv_val == "0") {


        $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and status='0' and lock_by='0'");


        //$res->execute(array(':units' => $units));
        $res->execute($vv);
        $count = $res->fetch(PDO::FETCH_NUM);
        $count=$count[0];


    }


    else if ($priv_val == "1") {

        $res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :uid and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and status='0' and lock_by='0'");


        //$res->execute(array(':uid' => $uid));

        $paramss=array(':uid' => '[[:<:]]'.$uid.'[[:>:]]');
        $res->execute(array_merge($vv,$paramss));
        $count = $res->fetch(PDO::FETCH_NUM);
        $count=$count[0];





    }
    else if ($priv_val == "2") {



        $res = $dbConnection->prepare("SELECT count(*) from tickets where status='0' and lock_by='0'");
        $res->execute();
        $count = $res->fetch(PDO::FETCH_NUM);
        $count=$count[0];

    }

    return $count;
}


function get_dashboard_msg(){
    global $dbConnection;
    $mid=$_SESSION['helpdesk_user_id'];

    $stmt = $dbConnection->prepare('SELECT messages from users where id=:mid');
    $stmt->execute(array(':mid' => $mid));

    $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
    // $res1 = $stmt->fetchAll();



    if (isset($res1['messages'])) {$max_id=$res1['messages'];}
    else {$max_id="";}


    $length = strlen(utf8_decode($max_id));
    if ($length < 1) {$ress=lang('DASHBOARD_def_msg');} else {$ress=' '.$max_id;}
    return $ress;
}
function get_myname(){
    $uid=$_SESSION['helpdesk_user_id'];
    $nu=name_of_user_ret($uid);
    $length = strlen(utf8_decode($nu));

    if ($length > 2) {$n=explode(" ", name_of_user_ret($uid)); $t=$n[1]." ".$n[2];}
    else if ($length <= 2) {$t="";}
    //$n=explode(" ", name_of_user_ret($uid));
    return $t;
}
function get_total_pages_workers() {
    global $dbConnection;
    $perpage='10';

    $res = $dbConnection->prepare("SELECT count(*) from clients");
    $res->execute();
    $count = $res->fetch(PDO::FETCH_NUM);
    $count=$count[0];



    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    }
    else {
        $pages_count = 0;
        return $pages_count;
    }

    return $count;
}

function get_approve() {
global $dbConnection;
            $stmt = $dbConnection->prepare('select count(id) as t1 from approved_info ');
            $stmt->execute();
            $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


            return $total_ticket['t1'];
}

function get_total_pages($menu, $id) {

    global $dbConnection;
    $perpage='10';

    if ($menu == "dashboard") {
$perpage='10';
if (isset($_SESSION['hd.rustem_list_in'])) {
$perpage= $_SESSION['hd.rustem_list_in'];
}
$unit_user=unit_of_user($id);
$priv_val=priv_status($id);
$units = explode(",", $unit_user);
$units = implode("', '", $units);
$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}
if ($priv_val == "0") {
$res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and arch='0'");
$res->execute($vv);
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($priv_val == "1") {
$res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0'))");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]');
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($priv_val == "2") {
$res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0'");
$res->execute();
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}


    if ($menu == "in") {
$perpage='10';


if (isset($_SESSION['hd.rustem_list_in'])) {
          $perpage=  $_SESSION['hd.rustem_list_in'];
        }



        $unit_user=unit_of_user($id);
        $priv_val=priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);


$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}
         if ($priv_val == "0") {
        if (isset($_SESSION['hd.rustem_sort_in'])) {
if ($_SESSION['hd.rustem_sort_in'] == "ok"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and arch='0' and status=:s");
$paramss=array(':s'=>'1');
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];}
else if ($_SESSION['hd.rustem_sort_in'] == "free"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and arch='0' and lock_by=:lb and status=:s");
$paramss=array(':lb'=>'0', ':s'=>'0');
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and arch='0' and lock_by=:lb and (status=0)");
$paramss=array(':lb'=>$id);
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "lock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$paramss=array(':lb'=>$id);
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_in'])) {
$res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (".$in_query.") and arch='0'");
$res->execute($vv);
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}


        else if ($priv_val == "1") {

            if (isset($_SESSION['hd.rustem_sort_in'])) {
if ($_SESSION['hd.rustem_sort_in'] == "ok"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and status=:s");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':s'=>'1');
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "free"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and lock_by=:lb and status=:s");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':lb'=>'0',':s'=>'0');
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and lock_by=:lb and (status=0)");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':lb'=>$id);
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "lock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0')) and (lock_by<>:lb and lock_by<>0) and (status=0)");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]', ':lb'=>$id);
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_in'])) {
$res = $dbConnection->prepare("SELECT count(*) from tickets where ((user_to_id rlike :id and arch='0') or (user_to_id='0' and unit_id IN (".$in_query.") and arch='0'))");
$paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]');
$res->execute(array_merge($vv,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
        else if ($priv_val == "2") {

if (isset($_SESSION['hd.rustem_sort_in'])) {
if ($_SESSION['hd.rustem_sort_in'] == "ok"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and status=:s");
$res->execute(array(':s'=>'1'));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "free"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and lock_by=:lb and status=:s");
$res->execute(array(':lb'=>'0',':s'=>'0'));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and lock_by=:lb and (status=0)");
$res->execute(array(':lb'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_in'] == "lock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$res->execute(array(':lb'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_in'])) {
$res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0'");
$res->execute();
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}

	}
    }
    if ($menu == "out") {
$perpage='10';
$priv_val=priv_status($id);
$unit_user=unit_of_user($id);
$u = return_users_array_unit($unit_user);

$ee2=explode(",", $u);
foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}

if (isset($_SESSION['hd.rustem_list_out'])) {
          $perpage=  $_SESSION['hd.rustem_list_out'];
        }
        if ($priv_val == "0"){
        if (isset($_SESSION['hd.rustem_sort_out'])) {
if ($_SESSION['hd.rustem_sort_out'] == "ok"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id IN (".$in_query2.") and arch='0' and status=:s");
// $res->execute(array(':s'=>'1'));
$paramss=array(':s'=>'1');
$res->execute(array_merge($vv2,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "free"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id IN (".$in_query2.") and arch='0' and lock_by=:lb and status=:s");
// $res->execute(array(':lb'=>'0', ':s'=>'0'));
$paramss=array(':lb'=>'0', ':s'=>'0');
$res->execute(array_merge($vv2,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "ilock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id IN (".$in_query2.") and arch='0' and lock_by=:lb");
// $res->execute(array(':lb'=>$id));
$paramss=array(':lb'=>$id);
$res->execute(array_merge($vv2,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "lock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id IN (".$in_query2.") and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
// $res->execute(array(':lb'=>$id));
$paramss=array(':lb'=>$id);
$res->execute(array_merge($vv2,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_out'])) {
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id IN (".$in_query2.") and arch=:n");
// $res->execute();
$paramss=array(':n'=>'0');
$res->execute(array_merge($vv2,$paramss));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];}
}
if ($priv_val == "1"){
if (isset($_SESSION['hd.rustem_sort_out'])) {
if ($_SESSION['hd.rustem_sort_out'] == "ok"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and status=:s");
$res->execute(array(':id'=>$id,':s'=>'1'));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "free"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and status=:s");
$res->execute(array(':id'=>$id,':lb'=>'0', ':s'=>'0'));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "ilock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and lock_by=:lb");
$res->execute(array(':id'=>$id,':lb'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "lock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$res->execute(array(':id'=>$id,':lb'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_out'])) {
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0'");
$res->execute(array(':id'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];}
}
if ($priv_val == "2"){
if (isset($_SESSION['hd.rustem_sort_out'])) {
if ($_SESSION['hd.rustem_sort_out'] == "ok"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and status=:s");
$res->execute(array(':id'=>$id,':s'=>'1'));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "free"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and status=:s");
$res->execute(array(':id'=>$id,':lb'=>'0', ':s'=>'0'));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "ilock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and lock_by=:lb");
$res->execute(array(':id'=>$id,':lb'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
else if ($_SESSION['hd.rustem_sort_out'] == "lock"){
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
$res->execute(array(':id'=>$id,':lb'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];
}
}
if (!isset($_SESSION['hd.rustem_sort_out'])) {
$res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0'");
$res->execute(array(':id'=>$id));
$count = $res->fetch(PDO::FETCH_NUM);
$count=$count[0];}
}
    }
    if ($menu == "arch") {
$perpage='10';
if (isset($_SESSION['hd.rustem_list_arch'])) {
          $perpage=  $_SESSION['hd.rustem_list_arch'];
        }



        $unit_user=unit_of_user($id);
        $priv_val=priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);

$ee=explode(",", $unit_user);
$s=1;
foreach($ee as $key=>$value) { $in_query = $in_query . ' :val_' . $key . ', '; $s++; }
$c=($s-1);
foreach($ee as $key=>$value) {$in_query2 = $in_query2 . ' :val_' . ($c+$key) . ', '; }
$in_query = substr($in_query, 0, -2);
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}
 foreach ($ee as $key=>$value) { $vv2[":val_" . ($c+$key)]=$value;}


        if ($priv_val == "0") {


            $res = $dbConnection->prepare("SELECT count(*) from tickets where (unit_id IN (".$in_query.") or user_init_id=:id) and arch='1'");

            $paramss=array(':id' => $id);
            $res->execute(array_merge($vv,$paramss));
            $count = $res->fetch(PDO::FETCH_NUM);
            $count=$count[0];
        }


        else if ($priv_val == "1") {


            $res = $dbConnection->prepare("SELECT count(*) from tickets
			    where (user_to_id rlike :id and unit_id IN (".$in_query.") and arch='1') or
			    (user_to_id='0' and unit_id IN (".$in_query2.") and arch='1') or
			    (user_init_id=:id2 and arch='1')");

            $paramss=array(':id' => '[[:<:]]'.$id.'[[:>:]]',':id2' => $id);
            $res->execute(array_merge($vv,$vv2,$paramss));
            $count = $res->fetch(PDO::FETCH_NUM);
            $count=$count[0];



        }
        else if ($priv_val == "2") {


            $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='1'");

            $res->execute();
            $count = $res->fetch(PDO::FETCH_NUM);
            $count=$count[0];



        }








    }

    if ($menu == "client") {

        $res = $dbConnection->prepare("SELECT count(*) from clients");

        $res->execute();
        $count = $res->fetch(PDO::FETCH_NUM);
        $count=$count[0];


    }


    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    }
    else {
        $pages_count = 0;
        return $pages_count;
    }

    return $count;

}
function name_of_client($input) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT fio FROM clients where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);


    echo($fio['fio']);

}
function name_of_client_ret($input) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT fio FROM clients where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    return $fio['fio'];

}






function time_ago($in) {
    $time = $in;
    $datetime1 = date_create($time);
    $datetime2 = date_create('now',new DateTimeZone('Europe/Moscow'));
    $interval = date_diff($datetime1, $datetime2);
    echo $interval->format('%d д %h:%I');

}







function humanTiming_period ($time1, $time_ago)
{


    $time = (strtotime($time_ago) - strtotime($time1)); // to get the time since that moment



    return $time;
}




function humanTiming_old ($time)
{

    $time = time() - $time;

    return floor($time/86400);
}




function get_unit_name($input) {
    global $dbConnection;

    $u=explode(",", $input);

    foreach ($u as $val) {
        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(':val' => $val));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);



        $res.=$dep['name'];
        $res.="<br>";
    }

    echo $res;
}

function name_of_user($input) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    echo($fio['fio']);
}

function name_of_user_ret($input) {
    global $dbConnection;


    $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);


    return($fio['fio']);
}

function unit_of_user($input) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT unit FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($fio['unit']);
}

function cutstr_help_ret($input) {

    $result = implode(array_slice(explode('<br>',wordwrap($input,500,'<br>',false)),0,1));
    $r=$result;
    if($result!=$input)$r.='...';
    return $r;
}

function cutstr_help2_ret($input) {

    $result = implode(array_slice(explode('<br>',wordwrap($input,100,'<br>',false)),0,1));
    $r=$result;
    if($result!=$input)$r.='...';
    return $r;
}
function cutstr_notes_ret($input) {

    $result = implode(array_slice(explode('<br>',wordwrap($input,50,'<br>',false)),0,1));
    $r=$result;
    if($result!=$input)$r.='...';
    return $r;

}
function cutstr_team_ret($input) {

    $result = implode(array_slice(explode('<br>',wordwrap($input,100,'<br>',false)),0,1));
    $r=$result;
    if($result!=$input)$r.='...';
    return $r;

}

function cutstr_ret($input) {

    $result = implode(array_slice(explode('<br>',wordwrap($input,30,'<br>',true)),0,1));
    return $result;
    if($result!=$input)return'...';
}


function cutstr($input) {

    $result = implode(array_slice(explode('<br>',wordwrap($input,51,'<br>',false)),0,1));
    echo $result;
    if($result!=$input)echo'...';
}
function get_date_ok($d_create, $id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('select date_op from ticket_log where ticket_id=:id and msg=:ok order by date_op DESC');
    $stmt->execute(array(':id' => $id, ':ok' => 'ok'));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);



    $tt=$total_ticket['date_op'];

    return $tt;
}
function GetArrayUsersOnline(){ // Возврат - массив пользователей online
		global $dbConnection;
    $id_user = $_SESSION['helpdesk_user_id'];
		$mOrgs = array();
  		$stmt = $dbConnection->prepare('SELECT * FROM users WHERE status=1 and us_kill=1');
      $stmt->execute();
      $res1 = $stmt->fetchAll();
  		if ($res1!='') {
        foreach($res1 as $myrow) {
          $lt=$myrow['last_time'];
                $d = time()-strtotime($lt);
          if ($d < 20) {
				   $mOrgs[]=$myrow["id"];
          }
				  };
          $us_me = array_search($id_user,$mOrgs);
          if ($us_me !== FALSE){
            unset($mOrgs[$us_me]);
            $us_dd = $mOrgs;
          }
          else{
            $us_dd = $mOrgs;
          }

				return $us_dd;
                    }
};
class Helper_TimeZone
{
public static function getTimeZoneSelect($selectedZone = NULL)
{
$regions = array(
'Africa' => DateTimeZone::AFRICA,
'America' => DateTimeZone::AMERICA,
'Antarctica' => DateTimeZone::ANTARCTICA,
'Aisa' => DateTimeZone::ASIA,
'Atlantic' => DateTimeZone::ATLANTIC,
'Europe' => DateTimeZone::EUROPE,
'Indian' => DateTimeZone::INDIAN,
'Pacific' => DateTimeZone::PACIFIC
);

$structure = '<select data-placeholder="'.lang('Select_time_zone').'" class="chosen-select form-control"  name="time_zone" id="time_zone">';
$structure .= '<option value=""></option>';

foreach ($regions as $mask) {
$zones = DateTimeZone::listIdentifiers($mask);
$zones = self::prepareZones($zones);

foreach ($zones as $zone) {
    $continent = $zone['continent'];
    $city = $zone['city'];
    $subcity = $zone['subcity'];
    $p = $zone['p'];
    $timeZone = $zone['time_zone'];

    if (!isset($selectContinent)) {
        $structure .= '<optgroup label="'.$continent.'">';
    }
    elseif ($selectContinent != $continent) {
        $structure .= '</optgroup><optgroup label="'.$continent.'">';
    }

    if ($city) {
        if ($subcity) {
            $city = $city . '/'. $subcity;
        }

        $structure .= "<option ".(($timeZone == $selectedZone) ? 'selected="selected "':'') . " value=\"".($timeZone)."\">(UTC ".$p.") " .str_replace('_',' ',$city)."</option>";
    }

    $selectContinent = $continent;
}
}

$structure .= '</optgroup></select>';

return $structure;
}

private static function prepareZones(array $timeZones)
{
$list = array();
foreach ($timeZones as $zone) {
$time = new DateTime(NULL, new DateTimeZone($zone));
$p = $time->format('P');
if ($p > 13) {
    continue;
}
$parts = explode('/', $zone);

$list[$time->format('P')][] = array(
    'time_zone' => $zone,
    'continent' => isset($parts[0]) ? $parts[0] : '',
    'city' => isset($parts[1]) ? $parts[1] : '',
    'subcity' => isset($parts[2]) ? $parts[2] : '',
    'p' => $p,
);
}

ksort($list, SORT_NUMERIC);

$zones = array();
foreach ($list as $grouped) {
$zones = array_merge($zones, $grouped);
}

return $zones;
}
}
?>
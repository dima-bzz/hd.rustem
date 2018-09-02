<?php
$base = dirname(dirname(__FILE__));
$backup = $base.'/backup';
include($base ."/functions.inc.php");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>Update System</title>
    <link href="favicon.ico" type="image/ico" rel="icon" />
    <link href="favicon.ico" type="image/ico" rel="shortcut icon" />

    <link rel="stylesheet" href="<?=$CONF['hostname']?>js/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=$CONF['hostname']?>js/bootstrap/css/bootstrap-theme.min.css">

    <style>
      .update_version_info{
        padding-left: 30px;
      }
      .update_version{
        padding-left: 20px;
      }
      .ok, .error{
        padding-left: 20px
      }
      .info{
        padding-left: 10px
      }
    </style>

</head>
<body>
<?php

if (is_writable($backup)){
  shell_exec("mysqldump -u".$CONF_DB['username']." -h".$CONF_DB['host']." -p".$CONF_DB['password']." ".$CONF_DB['db_name']." > ".$backup."/mysql_backup`date +%d%m%Y`.sql");

  $version = $CONF['system_version'];
  $error = false;

  function work_base($log, $sql, $param, $vr=null){
    global $error;
    global $version;
    global $dbConnection;
    echo '<div class="info">' . $log . '</div>';
    try {
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute($param);
      echo '<div class="text-success ok">ok</div>';
      update_version($vr);
    } catch (PDOException $e) {
      $message = $e->getMessage();
      $info = $e->getTrace();
      $error = true;
      echo '<div class="error">';
      echo '<div class="text-danger">' . lang('Update_error_bd') . ' </div>' . $message . '<br>';
      print_r($info);
      echo '</div>';
    }
  }

  function update_version($vr){
    global $error;
    global $version;
    global $dbConnection;
    echo '<div class="update_version">' . lang('Update_version') . $vr . '</div>';
    try {
      $stmt = $dbConnection->prepare('UPDATE perf SET value=:value WHERE param=:param');
      $stmt->execute(array(':value' => $vr, ':param' => 'system_version'));
      $version = $vr;
      echo '<div class="text-success update_version_info">ok</div>';
    } catch (PDOException $e) {
      $message = $e->getMessage();
      $info = $e->getTrace();
      $error = true;
      echo '<div class="update_version_info">';
      echo '<div class="text-danger">' . lang('Update_error_bd') . ' </div>' . $message . '<br>';
      print_r($info);
      echo '</div>';
    }
  }

  echo '<center><h4>' . lang('Update_start') . '</h4></center><br>';
  // обновляем до 2.12.3
  if (!isset($version)){
  	$vr = '2.12.3';
  	$log = 'добавляю в таблицу perf информацию о версии системы';
    $sql = 'INSERT INTO perf (id,param) VALUES (:id,:param)';
    $param = array(':id'=>'31', ':param'=>'system_version');
    work_base($log, $sql, $param, $vr);
  }

  if (empty($version)){
    $error = true;
    echo '<div class="text-danger error">' . lang('Update_uppps') . '</div>';
  }

  if ((isset($version)) && ($version == '2.12.2')){
  	$vr = '2.12.3';
    update_version($vr);
  }

  if (!$error){
    echo '<div class="text-info"><center><h4>' . lang('Update_end') . '</h4></center></div>';
    echo '<div class="text-warning"><center><strong><h3>' . lang('Update_end_info') . '</h3></strong></center></div>';
  }
  else{
    echo '<div class="text-danger"><center><h3>' . lang('Update_end_error') . '</h3></center></div>';
  }
}
else{
  echo '<div class="text-danger"><center><h3>Permission-error: <em>' . $backup . '</em> is not writable. Add access to write.</h3></center></div>';
}
?>
</body>
</html>
<?php

include_once('sys/XMPPHP/XMPP.php');

function send_jabber($to,$msg) {
  global $CONF_JABBER;
  $conn = new XMPPHP_XMPP($CONF_JABBER['server'], $CONF_JABBER['port'], $CONF_JABBER['login'].'@'.$CONF_JABBER['server'], $CONF_JABBER['login'], 'xmpphp', $CONF_JABBER['server'], $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);

  try {
  	//$conn->useEncryption(true);
      $conn->connect();

      $conn->processUntil('session_start');
      $conn->presence();
      $conn->message($to.'@'.$CONF_JABBER['server'], $msg);
      $conn->disconnect();
  } catch(XMPPHP_Exception $e) {
      die($e->getMessage());
}
}
function send_jabber_to($type,$tid) {
global $CONF, $dbConnection;

if ($type == "new_all") {
  $stmt = $dbConnection->prepare('SELECT user_to_id, unit_id, hash_name FROM tickets where id=:tid');
$stmt->execute(array(':tid'=>$tid));
$max_id_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

$unit_id=$max_id_ticket['unit_id'];
$client_id=$max_id_ticket['user_to_id'];
$h=$max_id_ticket['hash_name'];

$stmt = $dbConnection->prepare('SELECT jabber, unit, jabber_noty_show  FROM users where status=:n and jabber_noty = :n2');
$stmt->execute(array(':n'=>'1',':n2'=>'1'));
$res1 = $stmt->fetchAll();
foreach($res1 as $qrow) {




                $u=explode(",", $qrow['unit']);

                foreach ($u as $val) {

  if ($val== $unit_id) {


    if (in_array('1',explode(",",$qrow['jabber_noty_show']))){


    if (!is_null($qrow['jabber'])) {
      $to      = $qrow['jabber'];
      $message = lang("JABBER_all")."".$tid."".lang("JABBER_href")." ".$CONF['hostname']."ticket?".$h;

send_jabber($to,$message);
}
}
}
}
}
}
if ($type == "new_coord") {
  $stmt = $dbConnection->prepare('SELECT user_to_id, unit_id, hash_name FROM tickets where id=:tid');
$stmt->execute(array(':tid'=>$tid));
$max_id_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

$unit_id=$max_id_ticket['unit_id'];
$client_id=$max_id_ticket['user_to_id'];
$h=$max_id_ticket['hash_name'];

$cl_id = explode(',',$client_id);
foreach ($cl_id as $clients_id) {
$stmt = $dbConnection->prepare('SELECT jabber, unit, jabber_noty_show FROM users where status=:n and (priv=:n1 || priv=:n2) and id!=:id and jabber_noty = :n3');

$stmt->execute(array(':n'=>'1',':n1'=>'0',':n2'=>'2', ':n3'=>'1', ':id' => $clients_id));
$res1 = $stmt->fetchAll();
foreach($res1 as $qrow) {



                $u=explode(",", $qrow['unit']);

                foreach ($u as $val) {
  if($val != '100'){
  if ($val== $unit_id) {

    if (in_array('1',explode(",",$qrow['jabber_noty_show']))){

    if (!is_null($qrow['jabber'])) {
      $to      = $qrow['jabber'];
      $message = lang("JABBER_coord")." ".name_of_user_ret($clients_id)." ".lang("JABBER_coord1")."".$tid."".lang("JABBER_href")." ".$CONF['hostname']."ticket?".$h;

send_jabber($to,$message);
}
}
}
}
}
}
}
}
if ($type == "new_user") {
  $stmt = $dbConnection->prepare('SELECT user_to_id, hash_name FROM tickets where id=:tid');
$stmt->execute(array(':tid'=>$tid));
$max_id_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

$client_id=$max_id_ticket['user_to_id'];
$h=$max_id_ticket['hash_name'];

$cl_id = explode(',',$client_id);
foreach ($cl_id as $clients_id) {
$stmt = $dbConnection->prepare('SELECT jabber, jabber_noty_show from users where id=:client_id and status=:n and jabber_noty = :n2');
$stmt->execute(array(':n'=>'1',':n2'=>'1',':client_id'=>$clients_id));
$res1 = $stmt->fetchAll();
foreach($res1 as $row) {



  if (in_array('1',explode(",",$row['jabber_noty_show']))){

    if (!is_null($row['jabber'])) {
      $to      = $row['jabber'];
      $message = lang("JABBER_users")."".$tid."".lang("JABBER_href")." ".$CONF['hostname']."ticket?".$h;

send_jabber($to,$message);
}
}
}
}
}
}
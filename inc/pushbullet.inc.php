<?php
include_once('sys/PushBullet.class.php');

function send_push($to,$title,$url,$msg){
  global $CONF_PUSH;

  try {
    #### AUTHENTICATION ####
    // Get your API key here: https://www.pushbullet.com/account
    $send = new PushBullet($CONF_PUSH['api']);
    if ($url != ""){
    $send->pushLink($to, $title, $url, $msg);
    }
    else{
    $send->pushNote($to, $title, $msg);
    }


    #### Get methods

    // Print the definitions for your own devices. Useful for getting the 'iden' for using with the push methods.
    // print_r($p->getDevices());

    // // Print the definitions for contacts/devices shared with you. Useful for getting 'iden', too.
    // print_r($p->getContacts());
    //
    // // Print information about your Pushbullet account
    // print_r($p->getUserInformation());
    //
    // // Print a list of sent push notifications, modified after 1400441645 unix time
    // print_r($p->getPushHistory(1400441645));



    // #### Push methods
    //
    // // Push to email me@example.com a note with a title 'Hey!' and a body 'It works!'
    // $p->pushNote('me@example.com', 'Hey!', 'It works!';
    //
    // // Push to device s2GBpJqaq9IY5nx a note with a title 'Hey!' and a body 'It works!'
    // $p->pushNote('s2GBpJqaq9IY5nx', 'Hey!', 'It works!');
    //
    // // Push to device gXVZDd2hLY6TOB1 a link with a title 'ivkos at GitHub', a URL 'https://github.com/ivkos' and body 'Pretty useful.'
    // $p->pushLink('gXVZDd2hLY6TOB1', 'ivkos at GitHub', 'https://github.com/ivkos', 'Pretty useful.');
    //
    // // Push to device a91kkT2jIICD4JH a Google Maps address with a name 'Google HQ' and an address '1600 Amphitheatre Parkway'
    // $p->pushAddress('a91kkT2jIICD4JH', 'Google HQ', '1600 Amphitheatre Parkway');
    //
    // // Push to device qVNRhnXxZzJ95zz a to-do list with a title 'Shopping List' and items 'Milk' and 'Butter'
    // $p->pushList('qVNRhnXxZzJ95zz', 'Shopping List', array('Milk', 'Butter'));
    //
    // // Push to device 0PpyWzARDK0w6et the file '../pic.jpg' of MIME type image/jpeg
    // // Method accepts absolute and relative paths.
    // $p->pushFile('0PpyWzARDK0w6et', '../pic.jpg', 'image/jpeg');
    // // If the MIME type argument is omitted, an attempt to guess it will be made.
    // $p->pushFile('0PpyWzARDK0w6et', '../pic.jpg');


    // #### Pushing to multiple devices
    //
    // // Push to all of your own devices, if you set the first argument to NULL or an empty string
    // $p->pushNote(NULL, 'Some title', 'Some text');
    // $p->pushNote('', 'Some title', 'Some text');



    // #### Delete methods
    //
    // // Delete the push notification with the 'iden' specified
    // $p->deletePush('a91kkT2jIICD4JH');
    //
    // // Delete the device with the 'iden' specified
    // $p->deleteDevice('s2GBpJqaq9IY5nx');
    //
    // // Delete the contact with the 'iden' specified
    // $p->deleteContact('0PpyWzARDK0w6et');
  } catch (PushBulletException $e) {
    // Exception handling
    die($e->getMessage());
  }
}
function send_push_to($type,$tid) {
  global $CONF, $dbConnection;

  if ($type == "new_all") {
    $stmt = $dbConnection->prepare('SELECT user_to_id, unit_id, hash_name FROM tickets where id=:tid');
  $stmt->execute(array(':tid'=>$tid));
  $max_id_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

  $unit_id=$max_id_ticket['unit_id'];
  $client_id=$max_id_ticket['user_to_id'];
  $h=$max_id_ticket['hash_name'];

  $stmt = $dbConnection->prepare('SELECT push, unit, push_noty_show  FROM users where status=:n and push_noty = :n2');
  $stmt->execute(array(':n'=>'1',':n2'=>'1'));
  $res1 = $stmt->fetchAll();
  foreach($res1 as $qrow) {




                  $u=explode(",", $qrow['unit']);

                  foreach ($u as $val) {

    if ($val== $unit_id) {


      if (in_array('1',explode(",",$qrow['push_noty_show']))){


      if (!is_null($qrow['push'])) {
        $to      = $qrow['push'];
        $title = lang('PUSH_name');
        $url = $CONF['hostname']."ticket?".$h;
        $message = lang("PUSH_all")."".$tid."!";

  send_push($to,$title,$url,$message);
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
  $stmt = $dbConnection->prepare('SELECT push, unit, push_noty_show FROM users where status=:n and (priv=:n1 || priv=:n2) and id!=:id and push_noty = :n3');

  $stmt->execute(array(':n'=>'1',':n1'=>'0',':n2'=>'2', ':n3'=>'1', ':id' => $clients_id));
  $res1 = $stmt->fetchAll();
  foreach($res1 as $qrow) {



                  $u=explode(",", $qrow['unit']);

                  foreach ($u as $val) {
    if($val != '100'){
    if ($val== $unit_id) {

      if (in_array('1',explode(",",$qrow['push_noty_show']))){

      if (!is_null($qrow['push'])) {
        $to      = $qrow['push'];
        $title = lang('PUSH_name');
        $url = $CONF['hostname']."ticket?".$h;
        $message = lang("PUSH_coord")." ".name_of_user_ret($clients_id)." ".lang("PUSH_coord1")."".$tid."!";

  send_push($to,$title,$url,$message);
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
  $stmt = $dbConnection->prepare('SELECT push, push_noty_show from users where id=:client_id and status=:n and push_noty = :n2');
  $stmt->execute(array(':n'=>'1',':n2'=>'1',':client_id'=>$clients_id));
  $res1 = $stmt->fetchAll();
  foreach($res1 as $row) {



    if (in_array('1',explode(",",$row['push_noty_show']))){

      if (!is_null($row['push'])) {
        $to      = $row['push'];
        $title = lang('PUSH_name');
        $url = $CONF['hostname']."ticket?".$h;
        $message = lang("PUSH_users")."".$tid."!";

  send_push($to,$title,$url,$message);
  }
  }
  }
  }
  }
}
 ?>
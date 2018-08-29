<?php
session_start();
include("functions.inc.php");
if ( isset($_POST['mode']) ) {

    $mode=($_POST['mode']);

    if ($mode == "get_host_conf") {

        print($CONF['hostname']);
    }

    if ($mode == "get_lang_param") {
        $p=($_POST['param']);
        $r=lang($p);
        print($r);
    }


    if ($mode == "activate_login") {
        $mailadr=($_POST['mailadress']);


	$stmt = $dbConnection->prepare('SELECT id, fio,login,status FROM users where email=:mailadr');
        $stmt->execute(array(':mailadr' => $mailadr));
        $r = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($r)) {

                //foreach($res as $row) {
        //if(mysql_num_rows($res)>0) {

            //$r= mysql_fetch_assoc( $res );

            if ($r['status'] == "0") {

                $l=$r['login'];
                $fio=$r['fio'];
                $id=$r['id'];

                $pass=randomPassword();

                mailtoactivate($l, $mailadr, $pass);
                mailtoactivate_admin($l, $mailadr, $pass);

                $npass=md5($pass);
                $stmt = $dbConnection->prepare("UPDATE users SET pass=:pass, status=1 where id=:id");
                $stmt->execute(array(':pass' => $npass, ':id' => $id));

                ?>
                <div class="alert alert-success">
                    <center><?=lang('CREATE_ACC_success');?>
                    </center>
                </div>
            <?php
            }
            else if ($r['status'] == "1") {

                ?>
                <div class="alert alert-danger">
                    <center><?=lang('CREATE_ACC_already');?>
                    </center>
                </div>
            <?php

            }


        }
        else {
            ?>
            <div class="alert alert-danger">
                <center><?=lang('CREATE_ACC_error');?>
                </center>
            </div>
        <?php
        }
        ?>
        <center><img src="img/help-desk-icon.png"><h2 class="text-muted"><?=lang('MAIN_TITLE');?></h2><small class="text-muted"><?=lang('AUTH_USER');?></small></center><br>
        <input type="text" name="login" autocomplete="off" class="form-control" placeholder="<?=lang('login');?>">
        <input type="password" name="password" class="form-control" placeholder="<?=lang('pass');?>">
        <div style="padding-left:75px;">
            <div class="checkbox">
                <label>
                    <input id="mc" name="remember_me" value="1" type="checkbox"> <?=lang('remember_me');?>
                </label>
            </div>
        </div>
        <?php if ($va == 'error') { ?>
            <div class="alert alert-danger">
                <center><?=lang('error_auth');?></center>
            </div> <?php } ?>
        <input type="hidden" name="req_url" value="/index.php">
        <button class="btn btn-lg btn-primary btn-block"> <i class="fa fa-sign-in"></i>  <?=lang('log_in');?></button>

        <!hr style=" margin: 10px; ">
        <small>
            <center style=" margin-bottom: -20px; "><br><a href="#" id="show_activate_form"><?=lang('first_in_auth');?>.</a>
            </center>
        </small>

    <?php

    }
    if ($mode == "activate_login_form") {
        ?>
        <center><img src="img/help-desk-icon.png"><h2 class="text-muted"><?=lang('MAIN_TITLE');?></h2><small class="text-danger"><?=lang('user_auth');?></small></center><br>
        <input type="text" id="mailadress" name="login" autocomplete="off" class="form-control" placeholder="<?=lang('work_mail');?>">
        <p class="help-block"><small><?=lang('work_mail_ph');?></small></p>
        <div style="padding-left:75px;">
        </div>
        <br>
        <button id="do_activate" type="submit" class="btn btn-lg btn-success btn-block"> <i class="fa fa-check-circle-o"></i>  <?=lang('action_auth');?></button>






    <?php
    }









    if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {


        if ($mode == "get_list_notes") {
            $userid=$_SESSION['helpdesk_user_id'];


            $stmt = $dbConnection->prepare('SELECT id, hashname, message from notes where user_id=:userid order by dt DESC');
            $stmt->execute(array(':userid' => $userid));
            $res = $stmt->fetchAll();

            ?>
            <table class="table table-hover" style="margin-bottom: 0px; margin-bottom: 0px;" id="table_list">


            <?php
            if (empty($res)) {
                echo lang('empty');
            }
            else if (!empty($res)) {

                foreach($res as $row) {


                    $t_msg=cutstr_ret(strip_tags($row['message']));

                    if (strlen($t_msg) < 2){$t_msg="<em>".lang('NOTES_single')."</em>";}

                    ?>
                    <tr class="tr_<?=$row['id'];?>"><td style="width:90%"><a style=" cursor: pointer; " id="to_notes" value="<?=$row['hashname'];?>"><?=$t_msg;?></a></td><td><button id="del_notes" value="<?=$row['hashname'];?>" type="button" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-trash"></i></button></td></tr>
                <?php
                }
                ?></table><?php
            }
        }

 if ($mode == "check_login") {

 $l=$_POST['login'];

 if (validate_exist_login($l) == true) {$r['check_login_status']=true;}
 else if (validate_exist_login($l) == false) {$r['check_login_status']=false;}
 $row_set[] = $r;
 echo json_encode($row_set);
 }


        if ($mode == "save_notes") {
            $noteid=($_POST['hn']);
            $message=($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);



            $stmt = $dbConnection->prepare('update notes set message=:message, dt=now() where hashname=:noteid');
            $stmt->execute(array(':message' => $message, ':noteid' => $noteid));


            print_r ($_POST['msg']);
        }



        if ($mode == "get_first_note") {
            $noteid=($_POST['hn']);
            $uid=$_SESSION['helpdesk_user_id'];



            $stmt = $dbConnection->prepare('select hashname, message from notes where user_id=:uid order by dt DESC limit 1');
            $stmt->execute(array(':uid' => $uid));

            $res = $stmt->fetchAll();

            if (empty($res)) {
                echo "no";

            }
            else if (!empty($res)) {

                foreach($res as $row) {
                    echo $row['message'];
                }
            }


        }


        if ($mode == "get_notes") {
            $noteid=($_POST['hn']);

            $stmt = $dbConnection->prepare('select hashname, message from notes where hashname=:noteid');
            $stmt->execute(array(':noteid' => $noteid));
            $res = $stmt->fetchAll();

            foreach($res as $row) {
                echo $row['message'];

            }
        }


        if ($mode == "del_notes") {
            $noteid=($_POST['nid']);
            $stmt = $dbConnection->prepare('delete from notes where hashname=:noteid');
            $stmt->execute(array(':noteid' => $noteid));


        }

        if ($mode == "create_notes") {
            $uid=$_SESSION['helpdesk_user_id'];
            $hn=md5(time());
            $stmt = $dbConnection->prepare('insert into notes (message, hashname, user_id, dt) values (:nr, :hn, :uid, now())');
            $stmt->execute(array(':nr' => 'new record', ':hn'=> $hn, ':uid'=>$uid));


            echo $hn;
        }


if ($mode == "find_client") {


        $term = trim(strip_tags(($_POST['name'])));


            $stmt = $dbConnection->prepare('SELECT id FROM clients WHERE (fio LIKE :term) or (login LIKE :term2) or (tel LIKE :term3) limit 1');
	    $stmt->execute(array(':term' => '%'.$term.'%',':term2' => '%'.$term.'%',':term3' => '%'.$term.'%'));

    $res1 = $stmt->fetchAll();

    if (!empty($res1)) {
    foreach ($res1 as $row) {
$r['res']=true;
$r['p']=$row['id'];
}
}


    if (empty($res1)) {
    $r['res']=false;

    //user priv to add client in new ticket
    $pa=get_user_val('priv_add_client');
    if ($pa == 1) {$r['priv']=true;}
    if ($pa == 0) {$r['priv']=false;}

    $r['msg_error']="<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
  <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>
  ".lang('TICKET_error_msg')."
</div>";


    }





    $row_set[] = $r;
    echo json_encode($row_set);
}


        if ($mode == "get_client_from_new_t") {
            if (isset($_POST['get_client_info'])) {

                $client_id=($_POST['get_client_info']);



                get_client_info($client_id);



            }
            if (isset($_POST['new_client_info'])) {
                $fio=($_POST['new_client_info']);
                $u_l=($_POST['new_client_login']);

                ?>


                <div id="" class="alert alert-warning alert-dismissable" style="padding: 5px; margin-bottom: 10px;">
                    <button style="right: 0px;" type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <small>
                        <?=lang('msg_created_new_user');?> <br></small>
                </div>
                <div class="panel panel-success" id="user_info" style="display: block;">
                    <div class="panel-body">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-user"></i> <?=lang('WORKER_TITLE');?></h4>
                        </div>
                        <div class="panel-body">




                            <table class="table  ">
                                <tbody>
                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_fio');?>:</small></td>
                                    <td><small>
                                            <a href="#" id="username" data-type="text" data-pk="1" data-title="Enter username"><?=$fio?></a>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_login');?>:</small></td>
                                    <td><small><a href="#" id="new_login" data-type="text"  data-pk="1" data-title="Enter username"><?=$u_l?></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_posada');?>:</small></td>
                                    <td><small><a href="#" id="new_posada"  data-type="select" data-source="<?=$CONF['hostname'];?>/inc/json.php?posada" data-pk="1" data-title="<?=lang('WORKER_posada');?>"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_unit');?>:</small></td>
                                    <td><small><a href="#" id="new_unit" data-type="select" data-source="<?=$CONF['hostname'];?>/inc/json.php?units" data-pk="1" data-title="<?=lang('NEW_to_unit');?>"></a></small></td>
                                </tr>

                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_tel');?>:</small></td>
                                    <td><small><a href="#" id="new_tel" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_room');?>:</small></td>
                                    <td><small><a href="#" id="new_adr" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?=lang('WORKER_mail');?>:</small></td>
                                    <td><small><a href="#" id="new_mail" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            <?php
            }

        }





        if ($mode == "get_unit_id") {
            $uid=($_POST['uid']);


            $u=unit_of_user($uid);
            $units = explode(",", $u);
            echo $units[0];

        }


        if ($mode == "get_ticket_body") {
        }

        if ($mode == "update_dashboard_labels") {
            $results[] = array(
                'a' => get_total_tickets_free(),
                'b' => get_total_tickets_lock(),
                'c' => get_total_tickets_out_and_success_count()
            );
            print json_encode($results);
        }

        if ($mode == "update_list_labels") {
            $newt=get_total_tickets_free();

            if ($newt != 0) {
                $newtickets="(".$newt.")";
            }
            if ($newt == 0) {
                $newtickets="";
            }
            $outt=get_total_tickets_out_and_success_count();
            if ($outt != 0) {
                $out_tickets="(".$outt.")";
            }
            if ($outt == 0) {
                $out_tickets="";
            }

            $results[] = array(
                'in' => $newtickets,
                'out' => $out_tickets
            );
            print json_encode($results);
        }
        if ($mode == "check_update_one") {
            $lu=($_POST['last_update']);
            $ticket_id=($_POST['id']);




            $stmt = $dbConnection->prepare('SELECT last_update,hash_name, lock_by FROM tickets where id=:ticket_id');
            $stmt->execute(array(':ticket_id' => $ticket_id));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);

            $db_lu=$fio['last_update'];
            $db_hn=$fio['hash_name'];
            $at=get_last_action_type($ticket_id);


            if (strtotime($db_lu) > strtotime($lu)) {
                if ($at == 'comment') {$todo="comment";

                } else { $todo="update";}}
            if (strtotime($db_lu) <= strtotime($lu)) {$todo= "no";}


            $results[] = array(
                'type' => $todo,
                'time' => $db_lu,
                'hash' => $db_hn
            );


            print json_encode($results);
        }



        if ($mode == "get_users_list") {
            $idzz=($_POST['unit']);


            $stmt = $dbConnection->prepare('SELECT fio, id, unit FROM users where status = 1');
            $stmt->execute();
            $result = $stmt->fetchAll();




            foreach($result as $row) {


                $un=$row['fio'];
                $ud=(int)$row['id'];
                $u=explode(",",$row['unit']);

                if (in_array($idzz, $u)) {

		    if (get_user_status_text($ud) == "online") {$s="status-online-icon";}
				else if (get_user_status_text($ud) == "offline") {$s="status-offline-icon";}


                    $results[] = array(
                        'name' => nameshort($un),
                        'stat' =>$s,
                        'co' => $ud
                    );



                }



            }

            print json_encode($results);
        }

        if ($mode == "edit_helper") {
            $hn=($_POST['hn']);



            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname from helper where hashname=:hn');
            $stmt->execute(array(':hn' => $hn));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);


            $u=$fio['unit_to_id'];


            ?>
            <form class="form-horizontal" role="form">




                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?=lang('NEW_to');?>: </small></label>
                    <div class="col-md-6">
                        <select data-placeholder="<?=lang('NEW_to_unit');?>" class="chosen-select form-control" id="u" name="unit_id" multiple>
                        <option value="0"><?=lang('HELP_all');?></option>
                            <?php
                            $u=explode(",", $u);
                            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps');
                            $stmt->execute();
                            $result = $stmt->fetchAll();




                            foreach($result as $row) {

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
                <div class="">
                    <div class="">
                        <div class="form-group">

                            <label for="t" class="col-sm-2 control-label"><small><?=lang('HELP_desc');?>: </small></label>

                            <div class="col-sm-10">


                                <input  type="text" name="fio" class="form-control input-sm" id="t" placeholder="<?=lang('HELP_desc');?>" value="<?=$fio['title'];?>">



                            </div>



                        </div></div>
                    <div class="form-group">

                        <label for="t2" class="col-sm-2 control-label"><small><?=lang('HELP_do');?>: </small></label>

                        <div class="col-sm-10">


                            <div id="summernote_help"><?=$fio['message'];?></div>



                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button id="do_save_help" value="<?=$hn?>" class="btn btn-success" type="submit"><i class="fa fa-check-circle-o"></i> <?=lang('HELP_save');?></button>
                                </div>
                                <div class="btn-group">
                                    <a href="helper" class="btn btn-default" type="submit"><i class="fa fa-reply"></i> <?=lang('HELP_back');?></a>
                                </div>
                            </div>


                        </div>
            </form>
        <?php

        }



        if ($mode == "create_helper") {

            ?>
            <form class="form-horizontal" role="form">




                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?=lang('NEW_to');?>: </small></label>
                    <div class="col-md-6">
                        <select style="height: 34px;" data-placeholder="<?=lang('NEW_to_unit');?>" class="chosen-select form-control" id="u" name="unit_id" multiple>
                            <option value="0"><?=lang('HELP_all');?></option>
                            <?php

                            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
                            $stmt->execute(array(':n' => '0',':s' => '1'));
                            $result = $stmt->fetchAll();
                            foreach($result as $row) {



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
                <div class="">
                    <div class="">
                        <div class="form-group">

                            <label for="t" class="col-sm-2 control-label"><small><?=lang('HELP_desc');?>: </small></label>

                            <div class="col-sm-10">


                                <input  type="text" name="fio" class="form-control input-sm" id="t" placeholder="<?=lang('HELP_desc');?>">



                            </div>



                        </div></div>
                    <div class="form-group">

                        <label for="t2" class="col-sm-2 control-label"><small><?=lang('HELP_do');?>: </small></label>

                        <div class="col-sm-10">


                            <div id="summernote_help"></div>



                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button id="do_create_help" class="btn btn-success" type="submit"><i class="fa fa-check-circle-o"></i> <?=lang('HELP_create');?></button>
                                </div>
                                <div class="btn-group">
                                    <a href="helper" class="btn btn-default" type="submit"><i class="fa fa-reply"></i> <?=lang('HELP_back');?></a>
                                </div>
                            </div>


                        </div>
            </form>
        <?php

        }

        if ($mode == "find_help") {
            $t=($_POST['t']);
            $user_id=id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user=unit_of_user($user_id);
            $priv_val=priv_status($user_id);

            $units = explode(",", $unit_user);
            array_push($units,"0");


            $stmt = $dbConnection->prepare("SELECT
			    id, user_init_id, unit_to_id, dt, title, message, hashname
			    from helper where title like :t or message like :t2
			    order by dt desc");
            $stmt->execute(array(':t' => '%'.$t.'%',':t2' => '%'.$t.'%'));
            $result = $stmt->fetchAll();
            foreach($result as $row) {





                $unit2id = explode(",", $row['unit_to_id']);


                $diff = array_intersect($units, $unit2id);

                $priv_h="no";
                if ($priv_val == 1) {
                    if (($diff) || ($user_id==$row['user_init_id'])) {$ac= "ok";}

                    if ($user_id==$row['user_init_id']) {$priv_h="yes";}
                }


                else if ($priv_val == 0) {
                    $ac= "ok";
                    if ($user_id==$row['user_init_id']) {$priv_h="yes";}
                }


                else if ($priv_val == 2) {
                    $ac= "ok";
                    $priv_h="yes";
                }



                if ($ac == "ok") {
                    ?>

                    <h5 style=" margin-bottom: 5px; "><i class="fa fa-file-text-o"></i> <a href="helper?h=<?=$row['hashname'];?>"><?=$row['title'];?></a> <small>(<?=lang('DASHBOARD_author');?>: <?=nameshort(name_of_user_ret($row['user_init_id']));?>)<?php if ($priv_h== "yes") { echo "
            <div class=\"btn-group\">
            <button id=\"edit_helper\" value=\"".$row['hashname']."\" type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></button>
            <button id=\"del_helper\" value=\"".$row['hashname']."\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            "; } ?></small></h5>
                    <p style=" margin-bottom: 30px; "><small><?=cutstr_help_ret(strip_tags($row['message']));?>
                        </small>

                    </p><hr>
                <?php
                }
            }


        }

        if ($mode == "del_help") {
            $hn=($_POST['hn']);

            $stmt = $dbConnection->prepare('delete from helper where hashname=:hn');
            $stmt->execute(array(':hn' => $hn));

        }


        if ($mode == "list_help") {
            $user_id=id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user=unit_of_user($user_id);
            $priv_val=priv_status($user_id);

            $units = explode(",", $unit_user);
            array_push($units,"0");



            $stmt = $dbConnection->prepare('SELECT
			    id, user_init_id, unit_to_id, dt, title, message, hashname
			    from helper
			    order by dt desc');
            $stmt->execute();
            $result = $stmt->fetchAll();


            if(empty($result)) {





                ?>
                <div class="jumbotron">
                    <p>                </p><center><?=lang('MSG_no_records');?></center><p></p>

                </div>



            <?php
            }
            else if(!empty($result)){




		foreach($result as $row)



		{
                    $unit2id = explode(",", $row['unit_to_id']);

                    $diff = array_intersect($units, $unit2id);
                    $priv_h="no";
                    if ($priv_val == 1) {
                        if (($diff) || ($user_id==$row['user_init_id'])) {$ac= "ok";}


                        if ($user_id==$row['user_init_id']) {$priv_h="yes";}
                    }
                    else if ($priv_val == 0) {
                        $ac= "ok";
                        if ($user_id==$row['user_init_id']) {$priv_h="yes";}
                    }
                    else if ($priv_val == 2) {
                        $ac= "ok";
                        $priv_h="yes";
                    }

                    if ($ac == "ok") {
                        ?>

                        <h5 style=" margin-bottom: 5px; "><i class="fa fa-file-text-o"></i> <a href="helper?h=<?=$row['hashname'];?>"><?=$row['title'];?></a> <small>(<?=lang('DASHBOARD_author');?>: <?=nameshort(name_of_user_ret($row['user_init_id']));?>)<?php if ($priv_h== "yes") { echo "
            <div class=\"btn-group\">
            <button id=\"edit_helper\" value=\"".$row['hashname']."\" type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></button>
            <button id=\"del_helper\" value=\"".$row['hashname']."\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            "; } ?></small></h5>
                        <p style=" margin-bottom: 30px; "><small><?=cutstr_help_ret(strip_tags($row['message']));?>
                            </small>

                        </p>
                        <hr>
                    <?php
                    }
                }
            }




        }
        ///////
        if ($mode == "do_save_help") {
            $u=$_POST['u'];
            $beats = implode(',', $u);
            $hn=($_POST['hn']);

            $t=($_POST['t']);
            $user_id_z=$_SESSION['helpdesk_user_id'];

            $message=($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);

            $stmt = $dbConnection->prepare('update helper set user_init_id=:user_id_z, unit_to_id=:beats, dt=now(), title=:t, message=:message where hashname=:hn');
            $stmt->execute(array(':hn' => $hn, ':user_id_z'=>$user_id_z, ':beats'=>$beats, ':t'=>$t, ':message'=>$message));


        }

        if ($mode == "do_create_help") {
            $u=$_POST['u'];
            $beats = implode(',', $u);


            $t=($_POST['t']);
            $user_id_z=$_SESSION['helpdesk_user_id'];

            $hn=md5(time());
            $message=($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            $stmt = $dbConnection->prepare('insert into helper (hashname, user_init_id,unit_to_id, dt, title,message) values
	(:hn,:user_id_z,:beats, now(), :t,:message)');
            $stmt->execute(array(':hn' => $hn, ':user_id_z'=>$user_id_z, ':beats'=>$beats, ':t'=>$t, ':message'=>$message));





        }

        if ($mode == "dashboard_t") {

            $page=1;
            $perpage='5';

            if (isset($_POST['p'])) {
                $perpage=$_POST['p'];
            }

            $start_pos = ($page - 1) * $perpage;

            $user_id=id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user=unit_of_user($user_id);
            $priv_val=priv_status($user_id);

            $units = explode(",", $unit_user);
            $units = implode("', '", $units);

$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}



            if ($priv_val == 0) {

                $stmt = $dbConnection->prepare('SELECT
			    id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update, deadline_t, ok_date
			    from tickets
			    where unit_id IN ('.$in_query.')  and arch=:n
			    order by ok_by asc, prio desc, id desc
			    limit :start_pos, :perpage');


               $paramss=array(':n' => '0', ':start_pos'=>$start_pos, ':perpage'=>$perpage);
               $stmt->execute(array_merge($vv,$paramss));
                $results = $stmt->fetchAll();






            }
            else if ($priv_val == 1) {


                $stmt = $dbConnection->prepare('SELECT
			    id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update, deadline_t, ok_date
			    from tickets
			    where ((user_to_id rlike :user_id and arch=:n) or
			    (user_to_id=:n1 and unit_id IN ('.$in_query.') and arch=:n2))
			    order by ok_by asc, prio desc, id desc
			    limit :start_pos, :perpage');

                $paramss=array(':n' => '0',':start_pos'=>$start_pos, ':perpage'=>$perpage, ':user_id'=>'[[:<:]]'.$user_id.'[[:>:]]',':n1' => '0',':n2' => '0');

                $stmt->execute(array_merge($vv,$paramss));
                $results = $stmt->fetchAll();



            }
            else if ($priv_val == 2) {

                $stmt = $dbConnection->prepare('SELECT
			    id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update, deadline_t, ok_date
			    from tickets
			    where arch=:n
			    order by ok_by asc, prio desc, id desc
			    limit :start_pos, :perpage');
                $stmt->execute(array(':n' => '0',':start_pos'=>$start_pos, ':perpage'=>$perpage));
                $results = $stmt->fetchAll();



            }




            $aha=get_total_pages('dashboard', $user_id);
            if ($aha == "0") {

                ?>
                <div id="spinner" class="well well-large well-transparent lead">
                    <center>
                        <?=lang('MSG_no_records');?>
                    </center>
                </div>
            <?php } if ($aha <> "0") {

                ?>

                <input type="hidden" value="<?php echo get_total_pages('in', $user_id); ?>" id="val_menu">
                <input type="hidden" value="<?php echo $user_id; ?>" id="user_id">
                <input type="hidden" value="" id="total_tickets">
                <input type="hidden" value="" id="last_total_tickets">








                <div class="table-responsive">
                <table class="table table-bordered table-hover " style=" font-size: 14px; ">
                <thead>
                <tr>
                    <th><center><div id="sort_id" >#<?=$id_icon;?></div></center></th>
		    <th><center><div id="sort_prio"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="<?=lang('t_LIST_prio');?>"></i><?=$prio_icon;?></div></center></th>
		    <th><center><div id="sort_subj"><?=lang('t_LIST_subj');?><?=$subj_icon;?></div></center></th>
		    <th><center><div id="sort_cli"><?=lang('t_LIST_worker');?><?=$cli_icon;?></div></center></th>
                    <th><center><?=lang('t_LIST_create');?></center></th>
                    <th><center><?=lang('t_LIST_ago');?></center></th>
                    <th><center><div id="sort_init"><?=lang('t_LIST_init');?><?=$init_icon;?></div></center></th>
                    <th><center><?=lang('t_LIST_to');?></center></th>
                    <th><center><?=lang('t_LIST_status');?></center></th>

                </tr>
                </thead>
                <tbody>

                <?php

                foreach($results as $row) {


                    $lb=$row['lock_by'];
                    $ob=$row['ok_by'];


                    $user_id_z=$_SESSION['helpdesk_user_id'];
                    $unit_user_z=unit_of_user($user_id_z);
                    $status_ok_z=$row['status'];
                    $ok_date_z=$row['ok_date'];
                    $ok_by_z=$row['ok_by'];
                    $lock_by_z=$row['lock_by'];



                    ////////////////////////////Раскрашивает и подписывает кнопки/////////////////////////////////////////////////////////////////
if ($row['is_read'] == "0") { $style="bold_for_new"; }
if ($row['is_read'] <> "0") { $style=""; }
                    if ($row['status'] == "1") {
                        $ob_text="<i class=\"fa fa-check-circle-o\"></i>";
                        $ob_status="unok";
                        $ob_tooltip=lang('t_list_a_nook');
                        $style="success";

                        if ($lb <> "0") {
                            $lb_text="<i class=\"fa fa-lock\"></i>";
                            $lb_status="unlock";
                            $lb_tooltip=lang('t_list_a_unlock');
                        }
                        if ($lb == "0") {
                            $lb_text="<i class=\"fa fa-unlock\"></i>";
                            $lb_status="lock";
                            $lb_tooltip=lang('t_list_a_lock');
                        }


                    }

                    if ($row['status'] == "0") {
                        $ob_text="<i class=\"fa fa-circle-o\"></i>";
                        $ob_status="ok";
                        $ob_tooltip=lang('t_list_a_ok');
                        if ($lb <> "0") {
                            $lb_text="<i class=\"fa fa-lock\"></i>";
                            $lb_status="unlock";
                            $lb_tooltip=lang('t_list_a_unlock');
                            if ($lb == $user_id) {$style="warning";}
                            if ($lb <> $user_id) {$style="active";}
                        }

                        if ($lb == "0") {
                            $lb_text="<i class=\"fa fa-unlock\"></i>";
                            $lb_status="lock";
                            $lb_tooltip=lang('t_list_a_lock');
                        }

                    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////




////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                if ($row['user_to_id'] <> 0 ) {
                  $t = nameshort(get_fio_name_return($row['user_to_id']));
                  $t2 = nameshort(get_fio_name_return($row['user_to_id']));
                  $g = (count($t)) - 1;
                  if ($t[1] != ''){
                    if (in_array($user_id,explode(',',$row['user_to_id']))){
                      $t_end = nameshort(name_of_user_ret($user_id));
                      if (($l = array_search(nameshort(name_of_user_ret($user_id)),$t)) !==FALSE){
                        unset($t[$l]);
                      }
                    }
                      else{
                        if (($l = array_search($t2[0],$t)) !==FALSE){
                          unset($t[$l]);
                        }
                        $t_end = $t2[0];
                      }
                  $to_text="<strong>".$t_end." + ".$g."</strong>";
                  $to_text2= view_array($t);

                }
                else {
                  $to_text="<div class=''>".nameshort(name_of_user_ret($row['user_to_id']))."</div>";
                  $to_text2= '';
                }
                }
                if ($row['user_to_id'] == 0 ) {
                    $to_text="<strong>".lang('t_list_a_all')."</strong>";
                    $to_text2= view_array(get_unit_name_return($row['unit_id']));

                }
                  if ($row['deadline_t'] != "0000-00-00 00:00:00"){
                    $deadline_t = $row['deadline_t'];
                    $dtt = date("Y-m-d H:i:s");
                    if ((strtotime($dtt) > strtotime($deadline_t)) && ($row['status'] == 0)){
                      $deadline = "<center><span class='label label-danger' style='white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;' id='ded' datetime='".$deadline_t."'>".lang('DASHBOARD_deadline')."".lang('TICKET_overdue')."</span></center>";
                    }
                    else if ((strtotime($ok_date_z) < strtotime($deadline_t)) && ($row['status'] == 1)){
                    $deadline = "<center><span class='label label-danger' style='white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;' id='ded' datetime='".$deadline_t."'>".lang('DASHBOARD_deadline')."".lang('TICKET_deadline_ok')."</span></center>";
                        }
                        else if ((strtotime($ok_date_z) > strtotime($deadline_t)) && ($row['status'] == 1)){
                        $deadline = "<center><span class='label label-danger' style='white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;' id='ded' datetime='".$deadline_t."'>".lang('DASHBOARD_deadline')."".lang('TICKET_overdue')."</span></center>";
                            }
                  else{
                $deadline = "<center><span class='label label-danger' style='white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;'>".lang('DASHBOARD_deadline')."<time id='c' datetime='".$deadline_t."'></time></span></center>";
              }
                }
                else{
                  $deadline = '';
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////



////////////////////////////Показывает приоритет//////////////////////////////////////////////////////////////
                $prio="<span class=\"label label-info\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".lang('t_list_a_p_norm')."\"><i class=\"fa fa-minus\"></i></span>";

                if ($row['prio'] == "0") {$prio= "<span class=\"label label-primary\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".lang('t_list_a_p_low')."\"><i class=\"fa fa-arrow-down\"></i></span>"; }

                if ($row['prio'] == "2") {$prio= "<span class=\"label label-danger\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".lang('t_list_a_p_high')."\"><i class=\"fa fa-arrow-up\"></i></span>"; }
////////////////////////////////////////////////////////////////////////////////////////////////////////////






////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                if ($row['status'] == 1) {$st=  "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> ".lang('t_list_a_oko')." ".nameshort(name_of_user_ret($ob))."</span>";
                    $t_ago=get_date_ok($row['date_create'], $row['id']);
                }
                if ($row['status'] == 0) {
                    $t_ago=$row['date_create'];
                    if ($lb <> 0) {

                        if ($lb == $user_id) {$st=  "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> ".lang('t_list_a_lock_i')."</span>";}

                        if ($lb <> $user_id) {$st=  "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> ".lang('t_list_a_lock_u')." ".nameshort(name_of_user_ret($lb))."</span>";}

                    }
                    if ($lb == 0) {$st=  "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> ".lang('t_list_a_hold')."</span>";}
                }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



/////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
if ($priv_val == 1) {
//ЗАявка не выполнена ИЛИ выполнена мной
//ЗАявка не заблокирована ИЛИ заблокирована мной
$lo == "no";
if (($status_ok_z == 0) || (($status_ok_z == 1) && ($ok_by_z == $user_id_z)))
                    {
                        if (($lock_by_z == 0) || ($lock_by_z == $user_id_z)) {
                        $lo == "yes";
			}
		    }
                if ($lo == "yes") {$lock_st=""; $muclass="";}
                else if ($lo == "no") {$lock_st="disabled=\"disabled\""; $muclass="text-muted";}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





/////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
else if ($priv_val == 0) {
$lock_st=""; $muclass="";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





//////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
else if ($priv_val == 2) {
$lock_st=""; $muclass="";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    ?>




                    <tr id="tr_<?php echo $row['id']; ?>" class="<?=$style?>">
                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>"><center><?php echo $row['id']; ?></center></small></td>
                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>"><center><?=$prio?></center></small></td>
                        <td style=" vertical-align: middle; "><a class="<?=$muclass;?>" data-toggle="popover" data-placement="right" data-trigger="hover" data-html="true" data-title='<?=make_html($row['subj'], 'no')?>' data-content='<?=cutstr_team_ret(make_html($row['msg'], 'no'))?>' href="ticket?<?php echo $row['hash_name']; ?>"><?php cutstr(make_html($row['subj'], 'no')); ?></a></td>
                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>"><?php name_of_client($row['client_id']); ?></small></td>
                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>"><center><time id="c" datetime="<?=$row['date_create']; ?>"></time></center><?=$deadline;?></small></td>
                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>"><center><time id="a" datetime="<?=$t_ago;?>"></time></center></small></td>

                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>"><?php echo nameshort(name_of_user_ret($row['user_init_id'])); ?></small></td>

                        <td style=" vertical-align: middle; "><small class="<?=$muclass;?>" data-toggle="tooltip" data-placement="bottom" title="<?=make_html($to_text2,'no')?>">
                                <?=make_html($to_text, 'no')?>
                            </small></td>
                        <td style=" vertical-align: middle; "><small><center>
                                    <?=$st;?> </center>
                            </small></td>

                    </tr>
                <?php
                }

                ?>
                </tbody>
                </table>

                </div>



            <?php
            }




        }
if ($mode == "set_list_count") {
$pt=$_POST['pt'];
$v=$_POST['v'];
if ($pt == "in") {$_SESSION['hd.rustem_list_in'] =$v;}
else if ($pt == "out") {$_SESSION['hd.rustem_list_out'] =$v;}
else if ($pt == "arch") {$_SESSION['hd.rustem_list_arch'] =$v;}

}

if ($mode == "sort_list") {
$pt=$_POST['pt'];
$sort_type=$_POST['st'];

if ($pt == "in") {


switch($sort_type) {
 case 'main': unset($_SESSION['hd.rustem_sort_in']); break;
 case 'free': $_SESSION['hd.rustem_sort_in']="free"; break;
 case 'ok': $_SESSION['hd.rustem_sort_in']="ok"; break;
 case 'ilock': $_SESSION['hd.rustem_sort_in']="ilock"; break;
 case 'lock': $_SESSION['hd.rustem_sort_in']="lock"; break;
 case 'approved': $_SESSION['hd.rustem_sort_in']="approved"; break;
 default: unset($_SESSION['hd.rustem_sort_in']);
}




 }

else if ($pt == "out") {
 switch($sort_type) {
 case 'main': unset($_SESSION['hd.rustem_sort_out']); break;
 case 'free': $_SESSION['hd.rustem_sort_out']="free"; break;
 case 'ok': $_SESSION['hd.rustem_sort_out']="ok"; break;
 case 'ilock': $_SESSION['hd.rustem_sort_out']="ilock"; break;
 case 'lock': $_SESSION['hd.rustem_sort_out']="lock"; break;
 case 'approved': $_SESSION['hd.rustem_sort_out']="approved"; break;
 default: unset($_SESSION['hd.rustem_sort_out']);
}
}


}

if ($mode == "tb_sort"){
  $pt=$_POST['pt'];
  $sort_type=$_POST['st'];

  if ($pt == "in") {

if (!isset($_SESSION['hd.rustem_sort_in_o'])){
  $_SESSION['hd.rustem_sort_in_o'] = 'asc';
}
else{
  if ($sort_type == $_SESSION['hd.rustem_sort_tb_in']){
  if ($_SESSION['hd.rustem_sort_in_o'] == 'desc'){
    $_SESSION['hd.rustem_sort_in_o'] = 'asc';
  }
  else{
    $_SESSION['hd.rustem_sort_in_o'] = 'desc';
  }
  }
  else{
    $_SESSION['hd.rustem_sort_in_o'] = 'asc';
  }
}
  switch($sort_type) {
   case 'subj': $_SESSION['hd.rustem_sort_tb_in']="subj"; break;
   case 'id': $_SESSION['hd.rustem_sort_tb_in']="id"; break;
   case 'prio': $_SESSION['hd.rustem_sort_tb_in']="prio"; break;
   case 'client_id': $_SESSION['hd.rustem_sort_tb_in']="client_id"; break;
   case 'date_create': $_SESSION['hd.rustem_sort_tb_in']="date_create"; break;
   case 'user_init_id': $_SESSION['hd.rustem_sort_tb_in']="user_init_id"; break;

  }



   }
   else if ($pt == "out") {

 if (!isset($_SESSION['hd.rustem_sort_out_o'])){
   $_SESSION['hd.rustem_sort_out_o'] = 'asc';
 }
 else{
   if ($sort_type == $_SESSION['hd.rustem_sort_tb_out']){
   if ($_SESSION['hd.rustem_sort_out_o'] == 'desc'){
     $_SESSION['hd.rustem_sort_out_o'] = 'asc';
   }
   else{
     $_SESSION['hd.rustem_sort_out_o'] = 'desc';
   }
   }
   else{
     $_SESSION['hd.rustem_sort_out_o'] = 'asc';
   }
 }
   switch($sort_type) {
    case 'subj': $_SESSION['hd.rustem_sort_tb_out']="subj"; break;
    case 'id': $_SESSION['hd.rustem_sort_tb_out']="id"; break;
    case 'prio': $_SESSION['hd.rustem_sort_tb_out']="prio"; break;
    case 'client_id': $_SESSION['hd.rustem_sort_tb_out']="client_id"; break;
    case 'date_create': $_SESSION['hd.rustem_sort_tb_out']="date_create"; break;
    case 'user_init_id': $_SESSION['hd.rustem_sort_tb_out']="user_init_id"; break;

   }



    }
}
if ($mode == "reset_sort"){
  $pt=$_POST['pt'];

  if ($pt == "in") {
  unset($_SESSION['hd.rustem_sort_tb_in']);
  unset($_SESSION['hd.rustem_sort_in_o']);
  }
  else if ($pt == "out"){
  unset($_SESSION['hd.rustem_sort_tb_out']);
  unset($_SESSION['hd.rustem_sort_out_o']);
  }

}

        if ($mode == "last_news") {

            $uid=$_SESSION['helpdesk_user_id'];
            $unit_user=unit_of_user($uid);
            $priv_val=priv_status($uid);
            $c=7;
            $start=10;
            $u = return_users_array_unit($unit_user);


            if (isset($_POST['v'])) { $c=$_POST['v']; $start=($_POST['v']+5);}


	    //$_POST['v']



            $units = explode(",", $unit_user);
            $units = implode("', '", $units);
$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}

$ee2=explode(",", $u);
foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}

            if ($priv_val == "0") {

                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$in_query.') or user_init_id IN ('.$in_query2.')) order by last_update DESC limit :c');
                $paramss=array(':c'=>$c);
                $stmt->execute(array_merge($vv,$vv2,$paramss));
                $res1 = $stmt->fetchAll();



                foreach($res1 as $rews) {
                    $at=get_last_action_ticket($rews['id']);

                    $who_action=get_who_last_action_ticket($rews['id']);
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );


                }
            }


            else if ($priv_val == "1") {

                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
    ((user_to_id rlike :uid) or (user_to_id=:n and unit_id IN ('.$in_query.')))
    or user_init_id=:uid2) order by last_update DESC limit :c');
                $paramss=array(':uid'=>'[[:<:]]'.$uid.'[[:>:]]', ':n'=>'0', ':uid2'=>$uid, ':c'=>$c);
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();




                foreach($res1 as $rews) {


                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);


                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );

                }



            }
            else if ($priv_val == "2") {


                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets order by last_update DESC limit :c');
                $stmt->execute(array(':c'=>$c));
                $res1 = $stmt->fetchAll();





                foreach($res1 as $rews) {
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);


                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );

                }



            }



            if (empty($results)) {
                ?>
                <div id="" class="well well-large well-transparent lead">
                    <center>
                        <?=lang('MSG_no_records');?>
                    </center>
                </div>
            <?php
            }
            else {

                ?><table class="table table-hover" style="margin-bottom: 0px;" id=""> <?php

                foreach ($results as $arr) {
                    ?>

                    <tr><td style=" width: 100px; vertical-align: inherit;"><small><i class="fa fa-tag"></i> </small><a href="ticket?<?=$arr['hash'];?>"><small><?=lang('TICKET_name');?> #<?=$arr['name'];?></small></a></td><td><small><?=$arr['at'];?></small></td>
                    <td style=" width: 110px; vertical-align: inherit;"><small style="float:right;" class="text-muted "> <time id="b" datetime="<?=$arr['time'];?>"></time></small></td></tr>

                <?php

                }
                ?></table><small><center><a id="more_news" value="<?=$start?>" class="btn btn-default btn-xs"><?=lang('last_more');?></a></center></small><?php
            }

        }

        if ($mode == "check_update") {
            $pm=($_POST['type']);
            $uid=$_SESSION['helpdesk_user_id'];
            $lu=($_POST['last_update']);
            // var_dump($lu);

            $current_ticket_update=get_last_ticket($pm,$uid);
            if (strtotime($current_ticket_update) > strtotime($lu)) {echo $current_ticket_update;}
            if (strtotime($current_ticket_update) <= strtotime($lu)) {echo "no";}


//update
 $stmt = $dbConnection->prepare('update users set last_time=now() where id=:cid');
 $stmt->execute(array(':cid' => $uid ));
 //$stmt = $dbConnection->prepare('Update users set live=:live WHERE UNIX_TIMESTAMP(last_time)<UNIX_TIMESTAMP(NOW())-20');
 //$stmt->execute(array(':live' => 0));

         }
        //  if ($mode == "check_update_jabber") {
        //      $lu=($_POST['last_update']);
         //
        //      $current_ticket_update=get_last_ticket_new_jabber();
         //
        //      if (strtotime($current_ticket_update) > strtotime($lu)) {echo $current_ticket_update;}
        //      if (strtotime($current_ticket_update) <= strtotime($lu)) {echo "no";}
         //
        //   }


        if ($mode == "list_ticket_update") {
            $pm=($_POST['type']);
            $uid=$_SESSION['helpdesk_user_id'];
            $lu=($_POST['last_update']);
            $nlu=($_POST['new_last_update']);
            $unit_user=unit_of_user($uid);
            $priv_val=priv_status($uid);
            $show_noty=show_noty($uid);
            $u = return_users_array_unit($unit_user);


            $units = explode(",", $unit_user);
            $units = implode("', '", $units);

$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}

$ee2=explode(",", $u);
foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}

            if ($priv_val == "0") {
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$in_query.') or user_init_id IN ('.$in_query2.')) and last_update > :lu');

                $paramss=array(':lu'=>$lu);
                $stmt->execute(array_merge($vv,$vv2,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {

                    $at=get_last_action_ticket_noty($rews['id'],$uid);

                    $who_action=get_who_last_action_ticket($rews['id']);
                    // var_dump($who_action);
                    if ($who_action <> $uid) {
                      if ($at == NULL){
                        $results[] = array(
                          'show'=> 'false',
                          'up' => lang('JS_up'),
                        );
                      }
                      else {
                        $results[] = array(
                            'show'=> 'true',
                            'show_noty'=> $show_noty,
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                      }

                    }



		}

            }


            else if ($priv_val == "1") {

                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
    ((user_to_id rlike :uid) or (user_to_id=:n and unit_id IN ('.$in_query.')))
    or user_init_id=:uid2) and last_update > :lu');
                $paramss=array(':uid'=>'[[:<:]]'.$uid.'[[:>:]]', ':lu'=>$lu, ':uid2'=>$uid, ':n'=>'0');
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {


                    $at=get_last_action_ticket_noty($rews['id'],$uid);
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                      if ($at == NULL){
                        $results[] = array(
                          'show'=> 'false',
                          'up' => lang('JS_up'),
                        );
                      }
                      else {
                        $results[] = array(
                            'show'=> 'true',
                            'show_noty'=> $show_noty,
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                      }

                    }
		}









            }
            else if ($priv_val == "2") {

                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where last_update > :lu');
                $stmt->execute(array(':lu'=>$lu));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {


                    $at=get_last_action_ticket_noty($rews['id'],$uid);
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                      if ($at == NULL){
                        $results[] = array(
                          'show'=> 'false',
                          'up' => lang('JS_up'),
                        );
                      }
                      else {
                        $results[] = array(
                            'show'=> 'true',
                            'show_noty'=> $show_noty,
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                      }

                    }




	    }



            }
            print json_encode($results);

        }



  if ($mode == "send_jabber_noty"){
  if ($CONF_JABBER['active'] == "true") {
  // $lu=($_POST['last_update']);
  $stmt = $dbConnection->prepare('SELECT id, priv, unit, jabber, fio from users where status=:n and jabber_noty=:n2');
  $stmt->execute(array(':n'=>'1',':n2'=>'1'));
  $res1 = $stmt->fetchAll();
  foreach($res1 as $row) {
  $priv_val = $row['priv'];
  $uid = $row['id'];
  $unit = $row['unit'];
  $jabber = $row['jabber'];
  $fio = $row['fio'];
  $u = return_users_array_unit($unit);

  if ($priv_val == "2") {

      $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
      $stmt->execute();
      $res1 = $stmt->fetchAll();
      foreach($res1 as $rews) {

          $at=get_last_action_ticket_jabber($rews['id'],$uid);
          $who_action=get_who_last_action_ticket($rews['id']);
          if ($who_action <> $uid) {
            if ($at <> NULL){
              if (!is_null($jabber)) {
              $to = $jabber;
              $g = $at;
              send_jabber($to,$g);
            }
}
          }

}
  }
  if ($priv_val == "0") {
      $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$unit.') or user_init_id IN ('.$u.')) and UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
      $stmt->execute();
      $res1 = $stmt->fetchAll();
      foreach($res1 as $rews) {

          $at=get_last_action_ticket_jabber($rews['id'],$uid);
          $who_action=get_who_last_action_ticket($rews['id']);
          if ($who_action <> $uid) {
            if ($at <> NULL){
              if (!is_null($jabber)) {
              $to = $jabber;
              $g = $at;
              send_jabber($to,$g);
            }
}
          }

}
  }
  if ($priv_val == "1") {
    $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
((user_to_id rlike :uid) or (user_to_id=:n and unit_id IN ('.$unit.')))
or user_init_id=:uid2) and UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
    $stmt->execute(array(':uid'=>'[[:<:]]'.$uid.'[[:>:]]', ':uid2'=>$uid, ':n'=>'0'));
      $res1 = $stmt->fetchAll();
      foreach($res1 as $rews) {

          $at=get_last_action_ticket_jabber($rews['id'],$uid);
          $who_action=get_who_last_action_ticket($rews['id']);
          if ($who_action <> $uid) {
            if ($at <> NULL){
              if (!is_null($jabber)) {
              $to = $jabber;
              $g = $at;
              send_jabber($to,$g);
            }
}
          }

}
  }
}
}

}
if ($mode == "send_push_noty"){
if ($CONF_PUSH['active'] == "true") {
// $lu=($_POST['last_update']);
$stmt = $dbConnection->prepare('SELECT id, priv, unit, push, fio from users where status=:n and push_noty=:n2');
$stmt->execute(array(':n'=>'1',':n2'=>'1'));
$res1 = $stmt->fetchAll();
foreach($res1 as $row) {
$priv_val = $row['priv'];
$uid = $row['id'];
$unit = $row['unit'];
$push = $row['push'];
$fio = $row['fio'];
$u = return_users_array_unit($unit);

if ($priv_val == "2") {

    $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach($res1 as $rews) {

        $at=get_last_action_ticket_push($rews['id'],$uid);
        $who_action=get_who_last_action_ticket($rews['id']);
        if ($who_action <> $uid) {
          if ($at <> NULL){
            if (!is_null($push)) {
            $to = $push;
            $title = lang('PUSH_name');
            $msg = $at;
            send_push($to,$title,$msg);
          }
}
        }

}
}
if ($priv_val == "0") {
    $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$unit.') or user_init_id IN ('.$u.')) and UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach($res1 as $rews) {

        $at=get_last_action_ticket_push($rews['id'],$uid);
        $who_action=get_who_last_action_ticket($rews['id']);
        if ($who_action <> $uid) {
          if ($at <> NULL){
            if (!is_null($push)) {
            $to = $push;
            $title = lang('PUSH_name');
            $msg = $at;
            send_push($to,$title,$msg);
          }
}
        }

}
}
if ($priv_val == "1") {
  $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
((user_to_id rlike :uid) or (user_to_id=:n and unit_id IN ('.$unit.')))
or user_init_id=:uid2) and UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
  $stmt->execute(array(':uid'=>'[[:<:]]'.$uid.'[[:>:]]', ':uid2'=>$uid, ':n'=>'0'));
    $res1 = $stmt->fetchAll();
    foreach($res1 as $rews) {

        $at=get_last_action_ticket_push($rews['id'],$uid);
        $who_action=get_who_last_action_ticket($rews['id']);
        if ($who_action <> $uid) {
          if ($at <> NULL){
            if (!is_null($push)) {
            $to = $push;
            $title = lang('PUSH_name');
            $msg = $at;
            send_push($to,$title,$msg);
          }
}
        }

}
}
}
}

}
if ($mode == "send_mail_noty"){
if ($CONF_MAIL['active'] == "true") {
// $lu=($_POST['last_update']);
$stmt = $dbConnection->prepare('SELECT id, priv, unit, email, fio from users where status=:n');
$stmt->execute(array(':n'=>'1'));
$res1 = $stmt->fetchAll();
foreach($res1 as $row) {
$priv_val = $row['priv'];
$uid = $row['id'];
$unit = $row['unit'];
$email = $row['email'];
$fio = $row['fio'];
$u = return_users_array_unit($unit);

if ($priv_val == "2") {

    $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach($res1 as $rews) {

        $at=get_last_action_ticket_mail($rews['id'],$uid);
        $s=get_last_action_ticket_mail_subj($rews['id'],$uid);
        $who_action=get_who_last_action_ticket($rews['id']);
        if ($who_action <> $uid) {
          if ($at <> NULL){
            if (!is_null($email)) {
            $to = $email;
            $g = $at;
            $subj = $s;
            send_mail($to,$subj,$g);
          }
}
        }

}
}
if ($priv_val == "0") {
    $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$unit.') or user_init_id IN ('.$u.')) and UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach($res1 as $rews) {

        $at=get_last_action_ticket_mail($rews['id'],$uid);
        $s=get_last_action_ticket_mail_subj($rews['id'],$uid);
        $who_action=get_who_last_action_ticket($rews['id']);
        if ($who_action <> $uid) {
          if ($at <> NULL){
            if (!is_null($email)) {
              $to = $email;
              $g = $at;
              $subj = $s;
              send_mail($to,$subj,$g);
          }
}
        }

}
}
if ($priv_val == "1") {
  $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
((user_to_id rlike :uid) or (user_to_id=:n and unit_id IN ('.$unit.')))
or user_init_id=:uid2) and UNIX_TIMESTAMP(last_update) > UNIX_TIMESTAMP(NOW())-5');
  $stmt->execute(array(':uid'=>'[[:<:]]'.$uid.'[[:>:]]', ':uid2'=>$uid, ':n'=>'0'));
    $res1 = $stmt->fetchAll();
    foreach($res1 as $rews) {

        $at=get_last_action_ticket_mail($rews['id'],$uid);
        $s=get_last_action_ticket_mail_subj($rews['id'],$uid);
        $who_action=get_who_last_action_ticket($rews['id']);
        if ($who_action <> $uid) {
          if ($at <> NULL){
            if (!is_null($email)) {
              $to = $email;
              $g = $at;
              $subj = $s;
              send_mail($to,$subj,$g);
          }
}
        }

}
}
}
}

}

        if ($mode == "list_ticket_update2") {
            $pm=($_POST['type']);
            $uid=$_SESSION['helpdesk_user_id'];
            $lu=($_POST['last_update']);
            $nlu=($_POST['new_last_update']);
            $unit_user=unit_of_user($uid);
            $priv_val=priv_status($uid);
            $u = return_users_array_unit($unit_user);


            $units = explode(",", $unit_user);
            $units = implode("', '", $units);

$ee=explode(",", $unit_user);
foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);
foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}

$ee2=explode(",", $u);
foreach($ee2 as $key2=>$value2) {$in_query2 = $in_query2 . ' :vall_' . $key2 . ', '; }
$in_query2 = substr($in_query2, 0, -2);
foreach ($ee2 as $key2=>$value2) { $vv2[":vall_" . $key2]=$value2;}

            if ($priv_val == "0") {
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$in_query.') or user_init_id IN ('.$in_query2.')) and last_update > :lu');

                $paramss=array(':lu'=>$lu);
                $stmt->execute(array_merge($vv,$vv2,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {


		    $at=get_last_action_ticket2($rews['id'],$uid);

                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                      if ($at == NULL){
                        $results[] = array(
                          'show'=> 'false'
                        );
                      }
                      else {
                        $results[] = array(
                            'show'=> 'true',
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'hash' => $rews['hash_name'],
                            'at' => $at,

                        );
                    }
                  }

		}


            }


            else if ($priv_val == "1") {

                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
    ((user_to_id rlike :uid) or (user_to_id=:n and unit_id IN ('.$in_query.')))
    or user_init_id=:uid2) and last_update > :lu');
                $paramss=array(':uid'=>'[[:<:]]'.$uid.'[[:>:]]', ':lu'=>$lu, ':uid2'=>$uid, ':n'=>'0');
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {



                    $at=get_last_action_ticket2($rews['id'],$uid);

                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                      if ($at == NULL){
                        $results[] = array(
                          'show'=> 'false'
                        );
                      }
                      else {
                        $results[] = array(
                            'show'=> 'true',
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'hash' => $rews['hash_name'],
                            'at' => $at,

                        );
                    }
                  }


	    }




            }
            else if ($priv_val == "2") {

                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where last_update > :lu');
                $stmt->execute(array(':lu'=>$lu));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {



		    $at=get_last_action_ticket2($rews['id'],$uid);

                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                      if ($at == NULL){
                        $results[] = array(
                          'show'=> 'false'
                        );
                      }
                      else {
                        $results[] = array(
                            'show'=> 'true',
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'hash' => $rews['hash_name'],
                            'at' => $at,

                        );
                    }
                  }


	    }



            }
            print json_encode($results);

        }


        if ($mode == "find_worker") {

            $fio=($_POST['fio']);

            $stmt = $dbConnection->prepare('SELECT id,fio,tel,unit_desc,adr,tel_ext,email,login, posada, email FROM clients where status=:status and fio like :fio');
            $stmt->execute(array(':fio' => '%'.$fio.'%',':status'=>'1'));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);





            $fio_user=$fio['fio'];
            $loginf=$fio['login'];
            $tel_user=$fio['tel'];
            $pod=$fio['unit_desc'];
            $adr=$fio['adr'];
            $tel_ext=$fio['tel_ext'];
            $mails=$fio['email'];
            $posada=$fio['posada'];
            $id=$fio['id'];


            $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
            $stmt->execute(array(':id' => $id));
            $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


            $tt=$total_ticket['t1'];



            $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
            $stmt->execute(array(':id' => $id));
            $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);



            $lt=$last_ticket['dc'];
            ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-default">
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

                                <?php  ?>

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
                                    <td><small class="text-muted"><?php echo $tt; ?></small></td>
                                </tr>
 <?php if ($tt <> 0) { ?>
                                <tr>
                                    <td style=" width: 30px; "><small class="text-muted"><?=lang('WORKER_last');?>:</small></td>
                                    <td><small class="text-muted"><?php echo $lt; ?></small></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">



                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-pencil-square-o"></i> <?=lang('WORKER_edit_info');?></h4>
                        </div>
                        <div class="panel-body">



                            <form class="form-horizontal" role="form" id="form_approve">
                                <div class="form-group">
                                    <label for="pib" class="col-sm-2 control-label"><small><?=lang('WORKER_fio');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="pib" class="form-control input-sm" id="pib" placeholder="<?=lang('WORKER_fio');?>" value="<?=$fio_user;?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="login" class="col-sm-2 control-label"><small><?=lang('WORKER_login');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="login" class="form-control input-sm" id="login" placeholder="<?=lang('WORKER_login');?>" value="<?=$loginf?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="posada" class="col-sm-2 control-label"><small><?=lang('WORKER_posada');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="posada" class="form-control input-sm" id="posada" placeholder="<?=lang('WORKER_posada');?>" value="<?=$posada?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pidrozdil" class="col-sm-2 control-label"><small><?=lang('WORKER_unit');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="pid" class="form-control input-sm" id="pidrozdil" placeholder="<?=lang('WORKER_unit');?>" value="<?=$pod;?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tel" class="col-sm-2 control-label"><small><?=lang('WORKER_tel');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="tel" class="form-control input-sm" id="tel" placeholder="<?=lang('WORKER_tel_full');?>" value="<?php if ($tel_ext != "") {echo $tel_user." ".$tel_ext;} else {echo $tel_user;}?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="adr" class="col-sm-2 control-label"><small><?=lang('WORKER_room');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="adr" class="form-control input-sm" id="adr" placeholder="<?=lang('WORKER_room');?>" value="<?php echo $adr; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label"><small><?=lang('WORKER_mail');?></small></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="mail" class="form-control input-sm" id="email" placeholder="<?=lang('WORKER_mail');?>" value="<?=$mails?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                </div>
                                <div class="col-sm-6">
                                    <input type="hidden" name="id_client" value="<?=$id;?>">
                                    <button type="submit" id="send_zapit_edit" class="btn btn-success btn-xs btn-block"><?=lang('WORKER_send_approve');?></button>
                                </div>


                            </form>

                        </div>

                    </div>
                    <div id="sze_info">

                    </div>





                </div>
            </div>
        <?php
        }

        if ($mode == "approve_t_yes") {
            $id=($_POST['id']);
            $uid=$_SESSION['helpdesk_user_id'];



            $stmt = $dbConnection->prepare('SELECT id,t_id,user_from FROM approved_tickets where id=:id');
            $stmt->execute(array(':id' => $id));
            $tick = $stmt->fetch(PDO::FETCH_ASSOC);


            $qt_id=($tick['t_id']);
	          $q_from=($tick['user_from']);


            $stmt = $dbConnection->prepare('update tickets set status=:n, ok_by=:from,last_update=now(),approve_tickets=:n2 where id=:qt_id');
            $stmt->execute(array(':n' => 1, ':qt_id' => $qt_id, ':from' => $q_from, ':n2' => 1));

            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id) values (:ok_approved, now(), :unow, :tid)');
            $stmt->execute(array(':ok_approved'=>'ok_approved',':tid' => $qt_id,':unow'=>$uid));

	          $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id) values (:ok, DATE_ADD(NOW(), INTERVAL 1 SECOND), :unow, :tid)');
            $stmt->execute(array(':ok'=>'ok',':tid' => $qt_id,':unow'=>$q_from));


            $stmt = $dbConnection->prepare('delete from approved_tickets where id=:id');
            $stmt->execute(array(':id' => $id));

            view_approved_tickets();

        }

        if ($mode == "approve_yes") {
            $id=($_POST['id']);



            $stmt = $dbConnection->prepare('SELECT id,fio,tel,unit_desc,adr ,email,login, posada, email,client_id FROM approved_info where id=:id');
            $stmt->execute(array(':id' => $id));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);


            $qfio=trim($fio['fio']);
            $qlogin=trim($fio['login']);
            $tel=trim($fio['tel']);
            $qpod=($fio['unit_desc']);
            $adr=trim($fio['adr']);

            $email=trim($fio['email']);
            $posada=trim($fio['posada']);
            $cid=($fio['client_id']);






            $stmt = $dbConnection->prepare('update clients set fio=:qfio, tel=:tel, login=:qlogin, unit_desc=:qpod,
			adr=:adr, email=:email, posada=:posada where id=:cid');
            $stmt->execute(array(':qfio' => $qfio, ':tel' => $tel,':qlogin' => $qlogin,':qpod' => $qpod,':adr' => $adr,':email' => $email, ':posada' => $posada, ':cid' => $cid));






            $stmt = $dbConnection->prepare('delete from approved_info where id=:id');
            $stmt->execute(array(':id' => $id));

        }
        if ($mode == "approve_no") {
            $id=($_POST['id']);


            $stmt = $dbConnection->prepare('delete from approved_info where id=:id');
            $stmt->execute(array(':id' => $id));

        }

        if ($mode == "approve_t_no") {
             $id=($_POST['id']);

             $stmt = $dbConnection->prepare('SELECT id,t_id,user_init_id FROM approved_tickets where id=:id');
             $stmt->execute(array(':id' => $id));
             $tick = $stmt->fetch(PDO::FETCH_ASSOC);


             $qt_id=($tick['t_id']);
 	           $q_user=($tick['user_init_id']);


             $stmt = $dbConnection->prepare('update tickets set status=:n,last_update=now() where id=:qt_id');
             $stmt->execute(array(':n' => 0, ':qt_id' => $qt_id));

             $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id) values (:no_ok, now(), :unow, :tid)');
             $stmt->execute(array(':tid' => $qt_id, ':unow'=>$q_user,':no_ok'=>'no_ok'));


             $stmt = $dbConnection->prepare('delete from approved_tickets where id=:id');
             $stmt->execute(array(':id' => $id));

             view_approved_tickets();

         }

if ($mode == "conf_edit_mail") {
update_val_by_key("mail_type", $_POST['type']);
update_val_by_key("mail_active", $_POST['mail_active']);
update_val_by_key("mail_host", $_POST['host']);
update_val_by_key("mail_port", $_POST['port']);
update_val_by_key("mail_auth", $_POST['auth']);
update_val_by_key("mail_auth_type", $_POST['auth_type']);
update_val_by_key("mail_username", $_POST['username']);
update_val_by_key("mail_password", $_POST['password']);
update_val_by_key("mail_from", $_POST['from']);
//update_val_by_key("mail_debug", $_POST['debug']);
?>
<div class="alert alert-success">
<?=lang('PROFILE_msg_ok');?>
</div>
<?php
}
if ($mode == "conf_edit_jabber") {
update_val_by_key("jabber_active", $_POST['jabber_active']);
update_val_by_key("jabber_server", $_POST['jabber_server']);
update_val_by_key("jabber_port", $_POST['jabber_port']);
update_val_by_key("jabber_login", $_POST['jabber_login']);
update_val_by_key("jabber_pass", $_POST['jabber_pass']);

?>
<div class="alert alert-success">
<?=lang('PROFILE_msg_ok');?>
</div>
<?php
}
if ($mode == "conf_edit_push") {
update_val_by_key("push_active", $_POST['push_active']);
update_val_by_key("push_api", $_POST['push_api']);

?>
<div class="alert alert-success">
<?=lang('PROFILE_msg_ok');?>
</div>
<?php
}
if ($mode == "conf_edit_main") {
update_val_by_key("name_of_firm", $_POST['name_of_firm']);
update_val_by_key("title_header", $_POST['title_header']);
update_val_by_key("hostname", $_POST['hostname']);
update_val_by_key("first_login", $_POST['first_login']);
update_val_by_key("shutdown", $_POST['shutdown']);
update_val_by_key("pass_server", $_POST['pass_server']);
update_val_by_key("time_zone", $_POST['time_zone']);
update_val_by_key("mail", $_POST['mail']);
?>
<div class="alert alert-success">
<?=lang('PROFILE_msg_ok');?>
</div>
<?php
}
if ($mode == "conf_edit_ticket") {
update_val_by_key("days2arch", $_POST['days2arch']);
update_val_by_key("fix_subj", $_POST['fix_subj']);
update_val_by_key("file_uploads", $_POST['file_uploads']);

$bodytag = str_replace(",", "|", $_POST['file_types']);

update_val_by_key("file_types", $bodytag);
update_val_by_key("file_size", $_POST['file_size']);
update_val_by_key("approve_tickets", $_POST['approve_tickets']);
?>
<div class="alert alert-success">
<?=lang('PROFILE_msg_ok');?>
</div>
<?php
}
        if ($mode == "edit_profile_main") {
            $l=($_POST['login']);
            $m=($_POST['mail']);
            $p=($_POST['push']);
            $id=($_POST['id']);
            $langu=($_POST['lang']);

            $ec=0;
            $em="";
            $em2="";
            $em3="";
            if (!validate_alphanumeric_underscore($l)) { $ec=1;$em=lang('PROFILE_msg_login_validate');}
            if ($m != ""){
            if (!validate_email($m)) {$ec=1;$em2=lang('PROFILE_msg_email_validate');}
            if (!validate_exist_mail($m)) {$ec=1;$em3=lang('PROFILE_msg_email_duplicate');}
            }
            if ($p != ""){
            if (!validate_push($p)) {$ec=1;$em2=lang('PROFILE_msg_push_validate');}
            if (!validate_exist_push($p)) {$ec=1;$em3=lang('PROFILE_msg_push_duplicate');}
            }

            if ($ec == 0) {
              $stmt = $dbConnection->prepare('update users set login=:l, email=:m, push=:p, lang=:langu where id=:id');
              $stmt->execute(array(':id' => $id,':l' => $l,':m' => $m,':p' => $p,':langu' => $langu));


                ?>
                <div class="alert alert-success">
                    <?=lang('PROFILE_msg_ok');?>
                </div>
            <?php
            }
            if ($ec == 1) {
                ?>
                <div class="alert alert-danger">
                    <!-- <?=lang('PROFILE_msg_error');?> -->
                    <?php
                    echo $em." ".$em2." ".$em3;
                     ?>
                </div>
            <?php
            }
        }
        if ($mode == "edit_profile_noty") {
            $id=($_POST['id']);
            if ($_POST['jabber_active_profile'] != "undefined"){
              $jabber_noty = $_POST['jabber_active_profile'];
            }
            else{
              $jabber_noty = "0";
            }
            if ($_POST['jabber_show_profile'] != "undefined"){
              $jnoty = $_POST['jabber_show_profile'];
            }
            else{
              $jnoty = "1";
            }
            if ($_POST['push_active_profile'] != "undefined"){
              $push_noty = $_POST['push_active_profile'];
            }
            else{
              $push_noty = "0";
            }
            if ($_POST['push_show_profile'] != "undefined"){
              $pushnoty = $_POST['push_show_profile'];
            }
            else{
              $pushnoty = "1";
            }
            if ($_POST['mail_noty_profile'] != "undefined"){
              $mnoty = $_POST['mail_noty_profile'];
            }
            else{
              $mnoty = "1";
            }
            if ($_POST['show_noty_profile'] != "undefined"){
              $noty=($_POST['show_noty_profile']);
            }
            else{
              $noty="1";
            }
            $show_noty=($_POST['show_noty']);



            $stmt = $dbConnection->prepare('update users set jabber_noty=:jabber_noty, push_noty=:push_noty, noty=:noty, show_noty=:show_noty, jabber_noty_show=:jnoty, push_noty_show=:pushnoty, mail_noty_show=:mnoty where id=:id');
            $stmt->execute(array(':id' => $id,':jabber_noty' => $jabber_noty,':push_noty' => $push_noty,':noty'=>$noty, ':show_noty' => $show_noty, ':jnoty'=>$jnoty, ':pushnoty'=>$pushnoty, ':mnoty'=>$mnoty));


                ?>
                <div class="alert alert-success">
                    <?=lang('PROFILE_msg_ok');?>
                </div>
            <?php
        }

        if ($mode == "edit_profile_pass") {
            $p_old=md5(($_POST['old_pass']));
            $p_new=md5(($_POST['new_pass']));
            $p_new2=md5(($_POST['new_pass2']));
            $id=($_POST['id']);




            $stmt = $dbConnection->prepare('select pass from users where id=:id');
            $stmt->execute(array(':id' => $id));
            $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);


            $pass_orig=$total_ticket['pass'];

            $ec=0;

            if ($pass_orig <> $p_old) {
                $ec=1;
                $text=lang('PROFILE_msg_pass_err');
            }

            if ($p_new <> $p_new2) {
                $ec=1;
                $text=lang('PROFILE_msg_pass_err2');
            }

            if (strlen($p_new) < 3) {
                $ec=1;
                $text=lang('PROFILE_msg_pass_err3');
            }




            if ($ec == 0) {


                $stmt = $dbConnection->prepare('update users set pass=:p_new where id=:id');
                $stmt->execute(array(':id' => $id,':p_new' => $p_new));

                session_destroy();
                unset($_SESSION);
                session_unset();
                setcookie('authhash_uid', "");
                setcookie('authhash_code', "");
                unset($_COOKIE['authhash_uid']);
                unset($_COOKIE['authhash_code']);


                ?>
                <div class="alert alert-success">
                    <?=lang('PROFILE_msg_pass_ok');?>
                </div>
            <?php
            }
            if ($ec == 1) {
                ?>
                <div class="alert alert-danger">
                    <?=lang('PROFILE_msg_te');?> <?=$text;?>
                </div>
            <?php
            }




        }


        if ($mode == "subj_del") {
            $id=($_POST['id']);

            $stmt = $dbConnection->prepare('SELECT max(position) as position FROM subj');
            $stmt->execute();
            $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
            $position = (int)$row1["position"];

            $stmt = $dbConnection->prepare('SELECT position FROM subj WHERE id = :id');
            $stmt->execute(array(':id' => $id));
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            $ps = (int)$row2["position"];

              $stmt = $dbConnection->prepare('UPDATE subj SET position = position-1 WHERE position <= :position and position > :ps');
              $stmt->execute(array(':position' => $position, ':ps' => $ps));

            $stmt = $dbConnection->prepare('delete from subj where id=:id');
            $stmt->execute(array(':id' => $id));


            $stmt = $dbConnection->prepare('select id, name from subj order by position asc');
            $stmt->execute();
            $res1 = $stmt->fetchAll();

            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($res1 as $row) {

                    ?>
                    <tr id="tr_<?=$row['id'];?>" style="cursor:move;">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_subj" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }

        if ($mode == "edit_subj") {
         $v=($_POST['value']);
         $pk=($_POST['pk']);



         $stmt = $dbConnection->prepare('update subj set name=:v where id=:pk');
         $stmt->execute(array(':v'=>$v, ':pk'=>$pk));

         }

        if ($mode == "deps_add") {
            $t=($_POST['text']);


            $stmt = $dbConnection->prepare('insert into deps (name) values (:t)');
            $stmt->execute(array(':t' => $t));




            $stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
            $stmt->execute(array(':n' => '0'));
            $res1 = $stmt->fetchAll();
            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                //while ($row = mysql_fetch_assoc($results)) {
                foreach($res1 as $row) {
$cl="";
		    if ($row['status'] == "0") {$id_action="deps_show"; $icon="<i class=\"fa fa-eye-slash\"></i>"; $cl="active";}
		    if ($row['status'] == "1") {$id_action="deps_hide"; $icon="<i class=\"fa fa-eye\"></i>"; $cl="";}
                    ?>
                    <tr id="tr_<?=$row['id'];?>" class="<?=$cl;?>">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_deps" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center>
			<button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button>
			<button id="<?=$id_action;?>" type="button" class="btn btn-default btn-xs" value="<?=$row['id'];?>"><?=$icon;?></button>

			</center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }
 if ($mode == "approve_online_users_table"){

     $stmt = $dbConnection->prepare('select id from users where status=:n order by last_time DESC, fio ASC');
     $stmt->execute(array(':n'=>'1'));
     $result = $stmt->fetchAll();
         if (!empty($result)) {
           ?>
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
 if ($mode == "approve"){
    $stmt = $dbConnection->prepare('select count(id) as t1 from approved_info ');
    $stmt->execute();
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbConnection->prepare('select count(*) as count from users where UNIX_TIMESTAMP(last_time) > UNIX_TIMESTAMP(NOW())-20 and us_kill=1');
    $stmt->execute();
    $cn = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_online=$cn['count'];

    $uid=$_SESSION['helpdesk_user_id'];
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

    $stmt = $dbConnection->prepare ('SELECT us_kill FROM users WHERE id=:id');
    $stmt->execute(array(':id' => $uid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row['us_kill'] == '1'){
      $mlu = 'false';
    }
    else{
      $mlu = 'true';
    }
   //  echo $total_ticket['t1'];
    $results[] = array(
      'approve'=> $total_ticket['t1'],
      'online_users'=> $count_online,
      'new_tickets'=> $count,
      'make_logout_user'=> $mlu,
      'approve_tickets' => get_approve_tickets()
      );

    print json_encode($results);
  }
 if ($mode == "files_del") {
 $id=($_POST['id']);

 $stmt2 = $dbConnection->prepare('SELECT file_ext from files where file_hash=:id');
 $stmt2->execute(array(':id' => $id));
 $max = $stmt2->fetch(PDO::FETCH_NUM);
 $ext=$max[0];

 unlink(realpath(dirname(__FILE__))."/upload_files/".$id.".".$ext);
 unlink(realpath(dirname(__FILE__))."/upload_files/thumbnail/".$id.".".$ext);
 $stmt = $dbConnection->prepare('delete from files where file_hash=:id');
 $stmt->execute(array(':id' => $id));
 }
 if ($mode == "files_del_comment") {
 $id=($_POST['id']);

 $stmt2 = $dbConnection->prepare('SELECT file_ext from files_comment where file_hash=:id');
 $stmt2->execute(array(':id' => $id));
 $max = $stmt2->fetch(PDO::FETCH_NUM);
 $ext=$max[0];

 unlink(realpath(dirname(__FILE__))."/upload_files/".$id.".".$ext);
 unlink(realpath(dirname(__FILE__))."/upload_files/thumbnail/".$id.".".$ext);

 $stmt = $dbConnection->prepare('delete from files_comment where file_hash=:id');
 $stmt->execute(array(':id' => $id));
 }

        if ($mode == "deps_del") {
            $id=($_POST['id']);

            $stmt = $dbConnection->prepare('delete from deps where id=:id');
            $stmt->execute(array(':id' => $id));


            $stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
            $stmt->execute(array(':n' => '0'));
            $res1 = $stmt->fetchAll();
            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach($res1 as $row) {
		$cl="";
		if ($row['status'] == "0") {$id_action="deps_show"; $icon="<i class=\"fa fa-eye-slash\"></i>"; $cl="active";}
		if ($row['status'] == "1") {$id_action="deps_hide"; $icon="<i class=\"fa fa-eye\"></i>"; $cl="";}
                    ?>
                    <tr id="tr_<?=$row['id'];?>" class="<?=$cl;?>">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_deps" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button> <button id="<?=$id_action;?>" type="button" class="btn btn-default btn-xs" value="<?=$row['id'];?>"><?=$icon;?></button></center></small></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }



        if ($mode == "subj_add") {
            $t=($_POST['text']);

            $stmt = $dbConnection->prepare('SELECT max(position) as position FROM subj');
            $stmt->execute();
            $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
            $pos= $row1['position']+1;

            $stmt = $dbConnection->prepare('insert into subj (name,position) values (:t,:pos)');
            $stmt->execute(array(':t' => $t, ':pos' => $pos));




            $stmt = $dbConnection->prepare('select id, name from subj order by position asc');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($res1 as $row) {


                    ?>
                    <tr id="tr_<?=$row['id'];?>" style="cursor:move;">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_subj" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }

        if ($mode == "posada_add") {
            $t=($_POST['text']);



            $stmt = $dbConnection->prepare('insert into posada (name) values (:t)');
            $stmt->execute(array(':t' => $t));

            $stmt = $dbConnection->prepare('select id, name from posada');
            $stmt->execute();
            $res1 = $stmt->fetchAll();


            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($res1 as $row) {

                    ?>
                    <tr id="tr_<?=$row['id'];?>">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_posada" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="posada_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }

        if ($mode == "posada_del") {
            $id=($_POST['id']);



            $stmt = $dbConnection->prepare('delete from posada where id=:id');
            $stmt->execute(array(':id' => $id));

            $stmt = $dbConnection->prepare('select id, name from posada');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($res1 as $row) {

                    ?>
                    <tr id="tr_<?=$row['id'];?>">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_posada" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="posada_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }

        if ($mode == "edit_posada") {
         $v=($_POST['value']);
         $pk=($_POST['pk']);



         $stmt = $dbConnection->prepare('update posada set name=:v where id=:pk');
         $stmt->execute(array(':v'=>$v, ':pk'=>$pk));

         }
        if ($mode == "units_add") {
            $t=($_POST['text']);


            $stmt = $dbConnection->prepare('insert into units (name) values (:t)');
            $stmt->execute(array(':t' => $t));

            $stmt = $dbConnection->prepare('select id, name from units');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($res1 as $row) {

                    ?>
                    <tr id="tr_<?=$row['id'];?>">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_units" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }
        if ($mode == "units_del") {
            $id=($_POST['id']);




            $stmt = $dbConnection->prepare('delete from units where id=:id');
            $stmt->execute(array(':id' => $id));

            $stmt = $dbConnection->prepare('select id, name from units');
            $stmt->execute();
            $res1 = $stmt->fetchAll();

            ?>



            <table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?=lang('TABLE_name');?></center></th>
                    <th><center><?=lang('TABLE_action');?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($res1 as $row) {

                    ?>
                    <tr id="tr_<?=$row['id'];?>">


                        <td><small><center><?=$row['id'];?></center></small></td>
                        <td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_units" data-type="text"><?=$row['name'];?></a></small></td>
                        <td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button></center></small></td>
                    </tr>
                <?php } ?>



                </tbody>
            </table>
            <br>
        <?


        }
        if ($mode == "edit_units") {
         $v=($_POST['value']);
         $pk=($_POST['pk']);



         $stmt = $dbConnection->prepare('update units set name=:v where id=:pk');
         $stmt->execute(array(':v'=>$v, ':pk'=>$pk));

         }
        if ($mode == "send_zapit_add") {
            $pib=trim($_POST['pib']);
            $login=trim($_POST['login']);
            $posada=($_POST['posada']);
            $pid=($_POST['pid']);
            $tel=trim($_POST['tel']);
            $adr=trim($_POST['adr']);
            $mail=trim($_POST['mail']);

            $stmt = $dbConnection->prepare('insert into clients
	    (fio, tel, login, unit_desc, adr, email, posada,status)
	    VALUES (:pib, :tel, :login, :pid, :adr, :mail,  :posada, :status)');
            $stmt->execute(array(':pib' => $pib,':tel' => $tel,':login' => $login,':pid' => $pid,':adr' => $adr,':mail' => $mail,':posada' => $posada,':status' => '1'));


            ?>
            <div class="alert alert-success">
                <?=lang('PROFILE_msg_add');?>
            </div>
        <?php
        }

        if ($mode == "send_zapit_edit_ok") {
            $pib=trim($_POST['pib']);
            $login=trim($_POST['login']);
            $posada=($_POST['posada']);
            $pid=($_POST['pid']);
            $tel=trim($_POST['tel']);
            $adr=trim($_POST['adr']);
            $mail=trim($_POST['mail']);
            $id=($_POST['id_client']);
//echo "ff";
            $stmt = $dbConnection->prepare('update clients set
fio=:pib,
tel=:tel,
login=:login,
unit_desc=:pid,
adr=:adr,
email=:mail,
posada=:posada where id = :id');
            $stmt->execute(array(
            ':pib' => $pib,
            ':tel' => $tel,
            ':login' => $login,
            ':pid' => $pid,
            ':adr' => $adr,
            ':mail' => $mail,
            ':posada' => $posada,
            ':id'=>$id));
            ?>
            <div class="alert alert-success">
                <?=lang('PROFILE_msg_ok');?>
            </div>
        <?php
        }


        if ($mode == "send_zapit_edit") {
            $pib=trim($_POST['pib']);
            $login=trim($_POST['login']);
            $posada=($_POST['posada']);
            $pid=($_POST['pid']);
            $tel=trim($_POST['tel']);
            $adr=trim($_POST['adr']);
            $mail=trim($_POST['mail']);
            $uf=$_SESSION['helpdesk_user_id'];
            $id=($_POST['id_client']);



            $stmt = $dbConnection->prepare('insert into approved_info
(client_id, fio, tel, login, unit_desc, adr, email, posada, user_from, date_app)
VALUES (:id, :pib, :tel, :login, :pid, :adr, :mail,  :posada, :uf, now())');

            $stmt->execute(array(
            ':id' => $id,
            ':pib' => $pib,
            ':tel' => $tel,
            ':login' => $login,
            ':pid' => $pid,
            ':adr' => $adr,
            ':mail' => $mail,
            ':posada' => $posada,
            ':uf'=>$uf));


            ?>
            <div class="alert alert-success">
                <?=lang('PROFILE_msg_send');?>
            </div>
        <?php
        }


        if ($mode == "arch_now") {
            $user=($_POST['user']);
            $tid=($_POST['tid']);


            $stmt = $dbConnection->prepare('SELECT arch FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);

            $s=$fio['arch'];

            if ($s == "0") {

                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=now() where id=:tid');
                $stmt->execute(array(':tid' => $tid,':n1' => '1'));
            }
            if ($s == "1") {
                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=now() where id=:tid');
                $stmt->execute(array(':tid' => $tid,':n1' => '0'));
            }




            $unow=$_SESSION['helpdesk_user_id'];

            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:ar, now(), :unow, :tid)');
            $stmt->execute(array(':tid' => $tid,':unow' => $unow, ':ar'=>'arch'));


        }

        if ($mode == "status_no_ok") {
            $user=($_POST['user']);
            $tid=($_POST['tid']);
            // $t=($_POST['t']);




            $stmt = $dbConnection->prepare('SELECT status, ok_by, user_init_id, subj, unit_id, approved FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);

            $st=$fio['status'];
            $ob=$fio['ok_by'];
            $userinit=$fio['user_init_id'];
            $subj=$fio['subj'];
      	    $unit=$fio['unit_id'];
            $approved=$fio['approved'];

            $ps=priv_status($ob);



            if ($st == "0") {

                if (($user != $userinit) && ($approved == '1')){

                  $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=now(), last_update=now() where id=:tid');
                  $stmt->execute(array(':s'=>'1',':tid' => $tid,':user'=>$user));

                  $unow=$_SESSION['helpdesk_user_id'];


                  $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id) values (:ok, now(), :unow, :tid)');
                  $stmt->execute(array(':ok'=>'ok_wait',':tid' => $tid,':unow'=>$unow));

                  $stmt = $dbConnection->prepare('INSERT INTO approved_tickets (t_id, user_init_id, unit_id, subj,user_from,date_app) values (:tid,:user_init_id,:unit,:subj,:unow, now())');
                  $stmt->execute(array(':tid' => $tid,':unow'=>$unow,':user_init_id'=>$userinit,':unit'=>$unit,':subj'=>$subj));

                ?>

                <div class="alert alert-info"><i class="fa fa-check"></i> <?=lang('TICKET_msg_OK_wait');?></div>

            <?php
            }
            else{

              $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=now(), last_update=now(), approve_tickets=:a where id=:tid');
              $stmt->execute(array(':s'=>'1',':tid' => $tid,':user'=>$user, ':a'=>'1'));

              $unow=$_SESSION['helpdesk_user_id'];

              $stmt = $dbConnection->prepare('INSERT INTO ticket_log
          (msg, date_op, init_user_id, ticket_id)
    values (:ok, now(), :unow, :tid)');
              $stmt->execute(array(':ok'=>'ok',':tid' => $tid,':unow'=>$unow));
              ?>

              <div class="alert alert-success"><i class="fa fa-check"></i> <?=lang('TICKET_msg_OK');?></div>

          <?php
            }
            }
            if ($st == "1") {



                ?>

                <div class="alert alert-danger"><?=lang('TICKET_msg_OK_error');?> <?=name_of_user($ob);?></div>

            <?php
            }
        }
        if ($mode == "status_ok") {

            $user=($_POST['user']);
            $tid=($_POST['tid']);



            $stmt = $dbConnection->prepare('SELECT status, ok_by, arch, user_init_id, approved FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);



            $st=$fio['status'];
            $ob=$fio['ok_by'];
            $uinitd=$fio['user_init_id'];
            $approved=$fio['approved'];

            $ps=priv_status($user);



            if ($st == "1") {


                if ( ($ob == $user) || ($ps == "0") || ($ps == "2") || ($uinitd == $user)) {


                    if ($approved == '1'){
                      $stmt = $dbConnection->prepare('update tickets set ok_by=:n, status=:n1, arch=:n2, ok_date=:n3, last_update=now(), approve_tickets=:n4 where id=:tid');
                      $stmt->execute(array(':tid' => $tid, ':n'=>'0',':n1'=>'0',':n2'=>'0',':n3'=>'0', ':n4'=>'0'));
                    }
                    else{
                      $stmt = $dbConnection->prepare('update tickets set ok_by=:n, status=:n1, arch=:n2, ok_date=:n3, last_update=now() where id=:tid');
                      $stmt->execute(array(':tid' => $tid, ':n'=>'0',':n1'=>'0',':n2'=>'0',':n3'=>'0'));
                    }



                    $unow=$_SESSION['helpdesk_user_id'];



                    $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id) values (:no_ok, now(), :unow, :tid)');
                    $stmt->execute(array(':tid' => $tid, ':unow'=>$unow,':no_ok'=>'no_ok'));
                    if ($approved == '1'){
                    $stmt = $dbConnection->prepare('delete from approved_tickets where t_id=:tid');
                    $stmt->execute(array(':tid' => $tid));
                    }
                    ?>

                    <div class="alert alert-success"><i class="fa fa-check"></i> <?=lang('TICKET_msg_unOK');?></div>

                <?php
                }
            }
            if ($st == "0") {
                ?>
                <div class="alert alert-danger"><?=lang('TICKET_msg_unOK_error');?></div>
            <?php
            }


        }

        if ($mode == "lock") {
            $user=($_POST['user']);
            $tid=($_POST['tid']);


            $stmt = $dbConnection->prepare('SELECT lock_by, familiar, user_to_id, lock_t FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);


            $lb=$fio['lock_by'];
            $fm=$fio['familiar'];
            $lock_t=$fio['lock_t'];

            $ps=priv_status($lb);

            if ($lock_t == 0){
              $stmt = $dbConnection->prepare('update tickets set lock_t=now() where id=:tid');
              $stmt->execute(array(':tid' => $tid));
            }

            if ($lb == "0") {
              if (($fio['user_to_id'] == 0) || (strpos($fio['user_to_id'],",") <> false)){
                if ($fm == 0){
                $stmt = $dbConnection->prepare('update tickets set lock_by=:user, familiar=:user2, last_update=now() where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':user'=>$user, ':user2'=>$user));
                }
                else if (in_array($user,explode(',',$fm))){
                  $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=now() where id=:tid');
                  $stmt->execute(array(':tid' => $tid, ':user'=>$user));
                }
                else if (!in_array($user,explode(',',$fm))){
                  $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=now(), familiar= concat(familiar,:user2) where id=:tid');
                  $stmt->execute(array(':tid' => $tid, ':user'=>$user, ':user2'=>",".$user));
                }
              }
              else {
                $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=now() where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':user'=>$user));
              }
                $unow=$_SESSION['helpdesk_user_id'];


                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:lock, now(), :unow, :tid)');
                $stmt->execute(array(':tid' => $tid, ':unow'=>$unow, ':lock'=>'lock'));
                ?>

                <div class="alert alert-success"><i class="fa fa-check"></i> <?=lang('TICKET_msg_lock');?></div>

            <?php
            }
            if ($lb <> "0") {



                ?>
                <div class="alert alert-danger"><?=lang('TICKET_msg_lock_error');?> <?=name_of_user($lb);?></div>
            <?php
            }





        }
        if ($mode == "familiar") {
            $user=($_POST['user']);
            $tid=($_POST['tid']);


            $stmt = $dbConnection->prepare('SELECT familiar FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);


            $fm=$fio['familiar'];

            $ps=priv_status($lb);



            if ($fm == "0") {


                $stmt = $dbConnection->prepare('update tickets set familiar=:user, last_update=now() where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':user'=>$user));

                $unow=$_SESSION['helpdesk_user_id'];


                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:familiar, now(), :unow, :tid)');
                $stmt->execute(array(':tid' => $tid, ':unow'=>$unow, ':familiar'=>'familiar'));

            }
            if ($fm <> "0") {


                $stmt = $dbConnection->prepare('update tickets set familiar= concat(familiar,:user), last_update=now() where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':user'=>",".$user));

                $unow=$_SESSION['helpdesk_user_id'];


                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:familiar, now(), :unow, :tid)');
                $stmt->execute(array(':tid' => $tid, ':unow'=>$unow, ':familiar'=>'familiar'));

            }
?>
<div class="alert alert-success"><i class="fa fa-check"></i> <?=lang('TICKET_msg_familiar');?></div>
<?php
        }
        if ($mode == "unlock_ok"){
          $p=($_POST['p']);
          $tid=($_POST['tid']);

          $stmt = $dbConnection->prepare('SELECT user_to_id, familiar, unit_id from tickets where id=:id and permit_ok=:n');
          $stmt->execute(array(':id'=>$tid,':n'=>$p));
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
          echo $permit;
        }
        if ($mode == "unlock") {
            $tid=($_POST['tid']);
            $user=($_POST['user']);
            // $t=($_POST['t']);

            $stmt = $dbConnection->prepare('SELECT user_to_id FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($fio['user_to_id'] == 0) || (strpos($fio['user_to_id'],",") <> false)){

            $stmt = $dbConnection->prepare("update tickets set lock_by=:n, last_update=now(), familiar=trim(BOTH ',' FROM REPLACE(concat(',',familiar,','),',$user,',','))  where id=:tid");
            $stmt->execute(array(':tid' => $tid, ':n'=>'0'));
            }
            else {
              $stmt = $dbConnection->prepare("update tickets set lock_by=:n, last_update=now()  where id=:tid");
              $stmt->execute(array(':tid' => $tid, ':n'=>'0'));
            }

            $unow=$_SESSION['helpdesk_user_id'];




            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:unlock, now(), :unow, :tid)');
            $stmt->execute(array(':tid' => $tid, ':unow'=>$unow, ':unlock'=>'unlock'));

            ?>

            <div class="alert alert-success"><i class="fa fa-check"></i> <?=lang('TICKET_msg_unlock');?></div>

        <?php
        }

        if ($mode == "update_to") {


            $tid=($_POST['ticket_id']);
            $to=($_POST['to']);
            $tou=($_POST['tou']);
            $tom=($_POST['tom']);

            if (strlen($tom) > 2) {

                $x_refer_comment='<small class=\"text-muted\">'.nameshort(name_of_user_ret($_SESSION['helpdesk_user_id'])).' '.lang('REFER_comment_add').' ('.date(' d.m.Y H:i:s').'):</small> '.strip_tags(xss_clean(($_POST['tom'])));


                $stmt = $dbConnection->prepare('update tickets set
            unit_id=:to,
            user_to_id=:tou,
            msg=concat(msg,:br,:x_refer_comment),
            lock_by=:n,
            lock_t=:n2,
            last_update=now() where id=:tid');
                $stmt->execute(array(
                    ':to'=>$to,
                    ':tou'=>$tou,
                    ':br'=>'<br>',
                    ':x_refer_comment'=>$x_refer_comment,
                    ':tid' => $tid,
                    ':n'=>'0',
                    ':n2'=>'0000-00-00 00:00:00'));
            }
            else if (strlen($tom) <= 2) {

                $stmt = $dbConnection->prepare('update tickets set
            unit_id=:to,
            user_to_id=:tou,
            lock_by=:n,
            lock_t=:n2,
            last_update=now() where id=:tid');
                $stmt->execute(array(
                    ':to'=>$to,
                    ':tou'=>$tou,
                    ':tid' => $tid,
                    ':n'=>'0',
                    ':n2'=>'0000-00-00 00:00:00'));
            }



            $unow=$_SESSION['helpdesk_user_id'];


            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, to_user_id, ticket_id, to_unit_id) values (:refer, now(), :unow, :tou, :tid, :to)');
            $stmt->execute(array(':to'=>$to,':tou'=>$tou,':refer'=>'refer',':tid' => $tid, ':unow'=>$unow));




if ($CONF_MAIL['active'] == "true") {
            if ($tou == "0") {
                send_mail_to('new_all',$tid);
            }

            if ($tou <> "0") {
                send_mail_to('new_user',$tid);
                send_mail_to('new_coord',$tid);
            }
}
// if ($CONF_JABBER['active'] == "true") {
//             if ($tou == "0") {
//                 send_jabber_to('new_all',$tid);
//             }
//
//             else if ($tou <> "0") {
//                 send_jabber_to('new_user',$tid);
//                 send_jabber_to('new_coord',$tid);
//             }
//             }




            ?>
            <div class="alert alert-success"><?=lang('TICKET_msg_refer');?></div>
        <?php
        }
if ($mode == "edit_user") {
            $fio=($_POST['fio']);
            $login=($_POST['login']);

            $unit=($_POST['unit']);
            $priv=($_POST['priv']);
            $status=($_POST['status']);
            $usid=($_POST['idu']);
            $mail=($_POST['mail']);
            $jabber=($_POST['jabber']);
            $push=($_POST['push']);
            $mess=($_POST['mess']);
            $lang=($_POST['lang']);
            $jabber_noty=($_POST['jabber_active_client']);
            $push_noty=($_POST['push_active_client']);
            $noty=($_POST['show_noty_edit']);
            $jnoty=($_POST['jabber_show_edit']);
            $pushnoty=($_POST['push_show_edit']);
            $mnoty=($_POST['mail_noty_edit']);
            $show_noty=($_POST['show_noty']);
            $priv_add_client=$_POST['priv_add_client'];
            $priv_edit_client=$_POST['priv_edit_client'];
            $admin = $_POST['admin_client'];

            if ($priv_add_client == "true") {$priv_add_client=1;} else {$priv_add_client=0;}
            if ($priv_edit_client == "true") {$priv_edit_client=1;} else {$priv_edit_client=0;}
            if ($admin == "true") {$admin=8;} else {$admin=0;}

            if (strlen($_POST['pass'])>1) {
                $p=md5($_POST['pass']);

		 $stmt = $dbConnection->prepare('update users set fio=:fio, login=:login, pass=:pass, status=:status, priv=:priv, unit=:unit, email=:mail, jabber=:jabber, push=:push, messages=:mess, lang=:lang,
		 priv_add_client=:priv_add_client, priv_edit_client=:priv_edit_client, is_admin=:is_admin, jabber_noty=:jabber_noty, push_noty=:push_noty, noty=:noty, show_noty=:show_noty, jabber_noty_show=:jnoty, push_noty_show=:pushnoty, mail_noty_show=:mnoty where id=:usid');
		 $stmt->execute(array( ':fio'=>$fio, ':login'=>$login, ':status'=>$status, ':priv'=>$priv, ':unit'=>$unit, ':mail'=>$mail, ':jabber'=>$jabber, ':push'=>$push, ':mess'=>$mess, ':lang'=>$lang, ':usid'=>$usid,
		 ':pass'=>$p, ':priv_add_client'=>$priv_add_client, ':priv_edit_client'=>$priv_edit_client,':is_admin'=>$admin, ':jabber_noty'=>$jabber_noty, ':push_noty'=>$push_noty, ':noty'=>$noty, ':show_noty'=>$show_noty, ':jnoty'=>$jnoty, ':pushnoty'=>$pushnoty, ':mnoty'=>$mnoty));


		 $stmt = $dbConnection->prepare('update clients set status=:status where login=:login');
		 $stmt->execute(array( ':login'=>$login, ':status'=>$status));
            }
            else { $p="";
                $stmt = $dbConnection->prepare('update users set fio=:fio, login=:login, status=:status, priv=:priv, unit=:unit, email=:mail, jabber=:jabber, push=:push, messages=:mess, lang=:lang, priv_add_client=:priv_add_client,priv_edit_client=:priv_edit_client, is_admin=:is_admin, jabber_noty=:jabber_noty, push_noty=:push_noty, noty=:noty, show_noty=:show_noty, jabber_noty_show=:jnoty, push_noty_show=:pushnoty, mail_noty_show=:mnoty where id=:usid');
                $stmt->execute(array(':fio'=>$fio, ':login'=>$login, ':status'=>$status, ':priv'=>$priv, ':unit'=>$unit, ':mail'=>$mail, ':jabber'=>$jabber, ':push'=>$push, ':mess'=>$mess, ':lang'=>$lang, ':usid'=>$usid,':priv_add_client'=>$priv_add_client,':priv_edit_client'=>$priv_edit_client,':is_admin'=>$admin, ':jabber_noty'=>$jabber_noty, ':push_noty'=>$push_noty, ':noty'=>$noty, ':show_noty'=>$show_noty, ':jnoty'=>$jnoty, ':pushnoty'=>$pushnoty, ':mnoty'=>$mnoty));

		$stmt = $dbConnection->prepare('update clients set status=:status where login=:login');
		$stmt->execute(array( ':login'=>$login, ':status'=>$status));

            }



        }

        if ($mode == "add_user") {
            $fio=($_POST['fio']);
            $login=($_POST['login']);
            $pass=md5(($_POST['pass']));
//$unit[]=$_POST['unit'];
            $priv=($_POST['priv']);
            $mail=($_POST['mail']);
            $jabber=($_POST['jabber']);
            $push=($_POST['push']);
            $mess=($_POST['mess']);
            $lang=($_POST['lang']);
            $jabber_noty=($_POST['jabber_active_client']);
            $push_noty=($_POST['push_active_client']);
            $hidden=array();
            $hidden = ($_POST['unit']);
            //print_r($hidden);
            $unit=($_POST['unit']);

	    $priv_add_client=$_POST['priv_add_client'];
            $priv_edit_client=$_POST['priv_edit_client'];
            $user_add_client=$_POST['user_add_client'];
            $admin = $_POST['admin_client'];
	          if ($priv_add_client == "true") {$priv_add_client=1;} else {$priv_add_client=0;}
            if ($priv_edit_client == "true") {$priv_edit_client=1;} else {$priv_edit_client=0;}
            if ($admin == "true") {$admin=8;} else {$admin=0;}


            $stmt = $dbConnection->prepare('INSERT INTO users (fio, login, pass, status, priv, unit, email, jabber, push, messages, lang, priv_add_client, priv_edit_client, is_admin, jabber_noty, push_noty)
values (:fio, :login, :pass, :one, :priv, :unit, :mail, :jabber, :push, :mess, :lang, :priv_add_client, :priv_edit_client, :is_admin, :jabber_noty, :push_noty)');
            $stmt->execute(array(':fio'=>$fio, ':login'=>$login, ':pass'=>$pass, ':one'=>'1', ':priv'=>$priv, ':unit'=>$unit, ':mail'=>$mail, ':jabber'=>$jabber, ':push'=>$push, ':mess'=>$mess, ':lang'=>$lang,':priv_add_client'=>$priv_add_client,':priv_edit_client'=>$priv_edit_client,':is_admin'=>$admin, ':jabber_noty'=>$jabber_noty, ':push_noty'=>$push_noty));
            if ($user_add_client == 'true'){
              $stmt = $dbConnection->prepare('insert into clients
  	    (fio, login, email,status)
  	    VALUES (:fio, :login, :mail, :status)');
              $stmt->execute(array(':fio' => $fio,':login' => $login,':mail' => $mail,':status' => '1'));
            }



        }


        if ($mode == "save_edit_ticket") {
$t_hash=$_POST['t_hash'];
$subj=$_POST['subj'];
$msg=$_POST['msg'];
$prio=$_POST['prio'];

$stmt = $dbConnection->prepare('SELECT id, subj, msg, prio FROM tickets where hash_name=:hn');
       $stmt->execute(array(':hn' => $t_hash));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
           $pk = $fio['id'];

if ($prio != $fio['prio']) {
		     $stmt = $dbConnection->prepare('update tickets set prio=:v, last_edit=now(), last_update=now() where hash_name=:pk');
		 $stmt->execute(array(':v'=>$prio, ':pk'=>$t_hash));
		 $unow=$_SESSION['helpdesk_user_id'];
		 $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_subj, now(), :unow, :pk)');
		 $stmt->execute(array(':edit_subj'=>'edit_subj', ':pk'=>$pk,':unow'=>$unow));



}


if ($subj != $fio['subj']) {
                              $stmt = $dbConnection->prepare('update tickets set subj=:v, last_edit=now(), last_update=now() where hash_name=:pk');
                              $stmt->execute(array(':v'=>$subj, ':pk'=>$t_hash));


           $unow=$_SESSION['helpdesk_user_id'];



                              $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_subj, now(), :unow, :pk)');
                             $stmt->execute(array(':edit_subj'=>'edit_subj', ':pk'=>$pk,':unow'=>$unow));
}



if ($msg != $fio['msg']) {



                             $stmt = $dbConnection->prepare('update tickets set msg=:v, last_edit=now(), last_update=now() where hash_name=:pk');
                             $stmt->execute(array(':v'=>$msg, ':pk'=>$t_hash));


        $unow=$_SESSION['helpdesk_user_id'];


                              $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_msg, now(), :unow, :pk)');
                             $stmt->execute(array(':edit_msg'=>'edit_msg', ':pk'=>$pk,':unow'=>$unow));
}
}
if ($mode == "ticket_delete"){
  $td=($_POST['td']);

  $stmt = $dbConnection->prepare('SELECT id FROM tickets where hash_name=:hn');
         $stmt->execute(array(':hn' => $td));
      $fio = $stmt->fetch(PDO::FETCH_ASSOC);
        $t_d_id = $fio['id'];

        $stmt = $dbConnection->prepare('delete from ticket_log where ticket_id=:tid');
        $stmt->execute(array(':tid' => $t_d_id));

// Файлы заявок
        $stmt2 = $dbConnection->prepare('SELECT file_hash from files where ticket_hash=:td');
        $stmt2->execute(array(':td' => $td));
        // $max = $stmt2->fetch(PDO::FETCH_NUM);
        // $ext=$max[0];
        $res1 = $stmt2->fetchAll();
        if (!empty($res1)){
        foreach($res1 as $row) {
          $fh = $row['file_hash'];
          $stmt3 = $dbConnection->prepare('SELECT file_ext from files where file_hash=:fh');
          $stmt3->execute(array(':fh' => $fh));
          $max = $stmt3->fetch(PDO::FETCH_NUM);
          $ext=$max[0];
        unlink(realpath(dirname(__FILE__))."/upload_files/".$fh.".".$ext);
        unlink(realpath(dirname(__FILE__))."/upload_files/thumbnail/".$fh.".".$ext);
      }
      $stmt4 = $dbConnection->prepare('delete from files where ticket_hash=:td');
      $stmt4->execute(array(':td' => $td));
    }
// Комментарии и файл к ним

      $stmt5 = $dbConnection->prepare('SELECT hashname_comment from comments where t_id=:td');
      $stmt5->execute(array(':td' => $t_d_id));
      $res2 = $stmt5->fetchAll();
      if (!empty($res2)){
      foreach($res2 as $row2) {
        $ch = $row2['hashname_comment'];
        $stmt6 = $dbConnection->prepare('SELECT file_hash from files_comment where comment_hash=:ch');
        $stmt6->execute(array(':ch' => $ch));
        $res3 = $stmt6->fetchAll();
        if (!empty($res3)){
        foreach($res3 as $row3) {
          $fh2 = $row3['file_hash'];
        $stmt7 = $dbConnection->prepare('SELECT file_ext from files_comment where file_hash=:fh');
        $stmt7->execute(array(':fh' => $fh2));
        $max2 = $stmt7->fetch(PDO::FETCH_NUM);
        $ext=$max2[0];
      unlink(realpath(dirname(__FILE__))."/upload_files/".$fh2.".".$ext);
      unlink(realpath(dirname(__FILE__))."/upload_files/thumbnail/".$fh2.".".$ext);
    }
    $stmt = $dbConnection->prepare('delete from files_comment where comment_hash=:ch');
    $stmt->execute(array(':ch' => $ch));
  }
    }
}
    $stmt = $dbConnection->prepare('delete from comments where t_id=:id');
    $stmt->execute(array(':id' => $t_d_id));

    $stmt = $dbConnection->prepare('delete from tickets_fields where ticket_hash=:td');
    $stmt->execute(array(':td' => $td));


    $stmt = $dbConnection->prepare('delete from tickets where hash_name=:td');
    $stmt->execute(array(':td' => $td));
}

if ($mode == "deps_hide") {
$id=($_POST['id']);
$stmt = $dbConnection->prepare('update deps set status=:v where id=:id');
$stmt->execute(array(':v'=>'0', ':id'=>$id));
}
if ($mode == "deps_show") {
$id=($_POST['id']);
$stmt = $dbConnection->prepare('update deps set status=:v where id=:id');
$stmt->execute(array(':v'=>'1', ':id'=>$id));
}

if ($mode == "edit_deps") {
 $v=($_POST['value']);
 $pk=($_POST['pk']);



 $stmt = $dbConnection->prepare('update deps set name=:v where id=:pk');
 $stmt->execute(array(':v'=>$v, ':pk'=>$pk));



 }


        if ($mode == "view_comment") {


            $tid_comment=($_POST['tid']);
            view_comment($tid_comment);


        }


        if ($mode == "add_comment") {

            $user_comment=($_POST['user']);
            $tid_comment=($_POST['tid']);
            $hashname=($_POST['hashname']);
            //$text_comment=strip_tags(xss_clean(($_POST['textmsg'])),"<b><a><br>");
            $text_comment=$_POST['textmsg'];




            $stmt = $dbConnection->prepare('INSERT INTO comments (t_id, user_id, comment_text, dt, hashname_comment)
values (:tid_comment, :user_comment, :text_comment, now(), :hashname)');
            $stmt->execute(array(':tid_comment'=>$tid_comment, ':user_comment'=>$user_comment,':text_comment'=>$text_comment, ':hashname'=>$hashname));




            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:comment, now(), :user_comment, :tid_comment)');
            $stmt->execute(array(':tid_comment'=>$tid_comment, ':user_comment'=>$user_comment,':comment'=>'comment'));



            $stmt = $dbConnection->prepare('update tickets set last_update=now() where id=:tid_comment');
            $stmt->execute(array(':tid_comment'=>$tid_comment));


            view_comment($tid_comment);
            check_unlinked_file_comment();

        }
        if ($mode == "log_update") {
          $tid_comment=($_POST['tid']);
          view_log($tid_comment);
        }
        if ($mode == "upload_file") {
            $name=$_POST['name'];
            $hn=$_POST['hn'];



            $stmt = $dbConnection->prepare('insert into files (name, h_name) VALUES (:name, :hn)');
            $stmt->execute(array(':name'=>$name, ':hn'=>$hn));



        }
        if ($mode == "conf_test_jabber") {
          $id=$_SESSION['helpdesk_user_id'];
          if ($_POST['jabber_active'] == "true"){
            if (($_POST['jabber_server'] != "") && ($_POST['jabber_port'] != "") && ($_POST['jabber_login'] != "") && ($_POST['jabber_pass'] != "")){
          $stmt = $dbConnection->prepare("SELECT jabber FROM users WHERE id = :id");
          $stmt->execute(array(':id' => $id));
          $jabber = $stmt->fetch(PDO::FETCH_ASSOC);

          $to = $jabber['jabber'];
          $g = lang('Perf_jabber_msg');
          send_jabber($to,$g);
          ?>
          <div class="alert alert-success">
          <?=lang('Perf_jabber_sent');?>
          </div>
          <?php
        }
        else {
          ?>
          <div class="alert alert-danger">
          <?=lang('Perf_jabber_sent_error');?>
          </div>
          <?php
        }
        }
        else {
          ?>
          <div class="alert alert-danger">
          <?=lang('Perf_jabber_sent_shut');?>
          </div>
          <?php
        }
        }
        if ($mode == "conf_test_push") {
          $id=$_SESSION['helpdesk_user_id'];
          if ($_POST['push_active'] == "true"){
            if ($_POST['push_api'] != ""){
          $stmt = $dbConnection->prepare("SELECT push FROM users WHERE id = :id");
          $stmt->execute(array(':id' => $id));
          $push = $stmt->fetch(PDO::FETCH_ASSOC);

          $to = $push['push'];
          $title = lang('Perf_push_title');
          $msg = lang('Perf_push_msg');
          send_push($to,$title,$msg);
          ?>
          <div class="alert alert-success">
          <?=lang('Perf_push_sent');?>
          </div>
          <?php
        }
        else {
          ?>
          <div class="alert alert-danger">
          <?=lang('Perf_push_sent_error');?>
          </div>
          <?php
        }
        }
        else {
          ?>
          <div class="alert alert-danger">
          <?=lang('Perf_push_sent_shut');?>
          </div>
          <?php
        }
        }

        if ($mode == "conf_test_jabber_profile") {
          $id=$_SESSION['helpdesk_user_id'];
          if ($_POST['jabber_active'] == "1"){
          $stmt = $dbConnection->prepare("SELECT jabber FROM users WHERE id = :id");
          $stmt->execute(array(':id' => $id));
          $jabber = $stmt->fetch(PDO::FETCH_ASSOC);

          $to = $jabber['jabber'];
          $g = lang('Perf_jabber_msg');
          send_jabber($to,$g);
          ?>
          <div class="alert alert-success">
          <?=lang('Perf_jabber_sent');?>
          </div>
          <?php
        }
        else{
          ?>
          <div class="alert alert-danger">
          <?=lang('Perf_jabber_sent_shut');?>
          </div>
          <?php
        }
      }
      if ($mode == "conf_test_push_profile") {
        $id=$_SESSION['helpdesk_user_id'];
        if ($_POST['push_active'] == "1"){
        $stmt = $dbConnection->prepare("SELECT push FROM users WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        $push = $stmt->fetch(PDO::FETCH_ASSOC);

        $to = $push['push'];
        $title = lang('Perf_push_title');
        $msg = lang('Perf_push_msg');
        send_push($to,$title,$msg);
        ?>
        <div class="alert alert-success">
        <?=lang('Perf_push_sent');?>
        </div>
        <?php
      }
      else{
        ?>
        <div class="alert alert-danger">
        <?=lang('Perf_push_sent_shut');?>
        </div>
        <?php
      }
      }
      if ($mode == "make_logout_user"){
        $user_id = $_POST['userid'];
        $stmt = $dbConnection->prepare("UPDATE users SET us_kill = :kill WHERE id=:user_id");
        $stmt->execute(array(':user_id' => $user_id, ':kill' => 0));
      }
      if ($mode == "update_logout"){
        $user_id = $_POST['userid'];

        $stmt = $dbConnection->prepare("UPDATE users SET us_kill = :kill WHERE id=:user_id");
        $stmt->execute(array(':user_id' => $user_id, ':kill' => 1));
      }
      if ($mode == "conf_system_update"){

  $us = GetArrayUsersOnline();
  $us = implode(',',$us);

  $stmt = $dbConnection->prepare('SELECT max(id) as id FROM noty');
  $stmt->execute();
  $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
  $idd = $row3['id']+1;
  if ($us != ''){
  $stmt = $dbConnection->prepare('INSERT INTO noty (id,noty_w,userid,dt) VALUES (:id,:noty_w,:userid,now())');
  $stmt->execute(array(':id' => $idd, ':noty_w' => 'system_update', ':userid' => $us));
  ?>
  <br>
  <div class="alert_conf">
    <center>
  <?=lang('CONF_system_update_success');?>
</center>
  </div>
  <?php
}
}
if ($mode == "conf_check_update"){
$v = $CONF['system_version'];
$c = curl_init();
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_USERAGENT,'Awesome-Octocat-App');
curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
curl_setopt($c, CURLOPT_URL, 'https://api.github.com/repos/dima-bzz/hd.rustem/releases/latest');
$content = curl_exec($c);
curl_close($c);
$api = json_decode($content, true);
$v_g = $api['tag_name'];
$url = $api['zipball_url'];
if ($v >= $v_g){
  ?>
  <div class="alert_conf" style="margin-bottom: -20px;">
        <center>
  <?=lang('CONF_check_update_latest');?>
</center>
  </div>
  <?php
}
else{
  ?>
  <div class="alert_conf" style="margin-bottom: -20px;">
        <center>
          <?=lang('CONF_check_update_actual').''.$v_g;?>
          <p></p>
          <a href="<?=$url;?>">
  <?=lang('CONF_check_update_download');?>
</a>
</center>
  </div>
  <?php
}
}
if ($mode == "update_noty"){
  $id_user = $_SESSION['helpdesk_user_id'];

  $stmt = $dbConnection->prepare('SELECT dt, id, userid, user_read, noty_w FROM noty WHERE userid rlike :id2 order by dt desc');
  $stmt->execute(array(':id2' => '[[:<:]]'.$id_user.'[[:>:]]'));
  $res1 = $stmt->fetchAll();
  foreach($res1 as $rews) {
  if ($rews != ''){
    $userid = explode(',',$rews['userid']);
    $user_read = explode(',',$rews['user_read']);
    $date = strtotime($rews['dt']);

    $noty_w = $rews['noty_w'];
    $p = array_diff($userid,$user_read);
    if (empty($p)){
      $permit = 'false';
    }
    else{
      $permit = 'true';
    }
      if(($permit == 'true') && (!in_array($id_user,$user_read))){
        if ($noty_w == 'system_update'){
  $results[] = array(
        'show'=> 'true',
        'name'=> 'noty_'.$date.'_'.$noty_w.'_'.$rews['id'],
        'type'=> 'warning',
        'animated_open' => 'animated bounceInRight',
        'animated_close' => 'animated bounceOutLeft',
        'noty_w'=> $noty_w,
        'modal'=> true,
        'layout'=> 'center',
        'message'=> lang('System_update'),
        'time' => "<time id=\"b\" datetime=\"".$rews['dt']."\"></time>"
      );
      }
      else{
        $results[] = array(
            'show'=> 'false',
        );
    }
      }
      else{
        $results[] = array(
            'show'=> 'false',
        );
    }
  }
  else{
    $results[] = array(
        'show'=> 'false',
    );
  }
}

  print json_encode($results);
}
if ($mode == "update_noty_id_read"){
  $id = $_POST['id'];
  $id_user = $_SESSION['helpdesk_user_id'];

  $stmt = $dbConnection->prepare('SELECT user_read FROM noty where id=:id');
  $stmt->execute(array(':id' => $id));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $us=$row['user_read'];

  if ($us == '0'){
  $stmt = $dbConnection->prepare('UPDATE noty SET user_read=:user where id=:id');
  $stmt->execute(array(':id' => $id, ':user'=>$id_user));
  }
  else if ($us <> '0'){
    $stmt = $dbConnection->prepare('UPDATE noty SET user_read= concat(user_read,:user) where id=:id');
    $stmt->execute(array(':id' => $id, ':user'=>",".$id_user));
  }

  $stmt = $dbConnection->prepare('SELECT user_read, userid FROM noty where id=:id');
  $stmt->execute(array(':id' => $id));
  $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

  $userid = explode(',',$row2['userid']);
  $user_read = explode(',',$row2['user_read']);
  $p = array_diff($userid,$user_read);
  if (empty($p)){
    $stmt = $dbConnection->prepare ('DELETE FROM noty  WHERE id = :id');
    $stmt->execute(array(':id' => $id));
  }


}
if ($mode == "add_field_item"){
  $hash = md5(time());
  $stmt = $dbConnection->prepare('INSERT INTO dop_fields (field_hash) VALUES (:hash)');
  $stmt->execute(array(':hash'=>$hash));

  dop_fields();
}
if ($mode == "change_field_name"){
  $hash = ($_POST['hash']);
  $name = trim($_POST['name']);
  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_name= :name where field_hash=:hash');
  $stmt->execute(array(':name' => $name, ':hash'=>$hash));
}
if ($mode == "change_field_placeholder"){
  $hash = ($_POST['hash']);
  $name = trim($_POST['name']);
  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_placeholder= :name where field_hash=:hash');
  $stmt->execute(array(':name' => $name, ':hash'=>$hash));
}
if ($mode == "change_field_value"){
  $hash = ($_POST['hash']);
  $name = trim($_POST['name']);
  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_value= :name where field_hash=:hash');
  $stmt->execute(array(':name' => $name, ':hash'=>$hash));
}
if ($mode == "change_field_select"){
  $hash = ($_POST['hash']);
  $name = $_POST['name'];
  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_type= :name where field_hash=:hash');
  $stmt->execute(array(':name' => $name, ':hash'=>$hash));
}
if ($mode == "change_field_subj_select"){
  $hash = ($_POST['hash']);
  $name = $_POST['name'];
  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_subj= :name where field_hash=:hash');
  $stmt->execute(array(':name' => $name, ':hash'=>$hash));
}
if ($mode == "change_field_checkbox"){
  $hash = ($_POST['hash']);
  $name = $_POST['name'];
  if ($name == 'true'){$name = '1';}else{$name = '0';}
  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_status= :name where field_hash=:hash');
  $stmt->execute(array(':name' => $name, ':hash'=>$hash));
}
if ($mode == "del_field"){
  $hash = ($_POST['hash']);

  $stmt = $dbConnection->prepare('DELETE FROM dop_fields WHERE field_hash=:hash');
  $stmt->execute(array(':hash'=>$hash));

  dop_fields();

}

if ($mode == "change_subj_plus_fields"){
  $id = $_POST['subj_id'];
  form_subj($id);
}
if ($mode == "update_dop_fields"){
  dop_fields();
}
if ($mode == "change_field_check_subj"){

  $stmt = $dbConnection->prepare('UPDATE dop_fields SET field_status= :name where field_subj<>:subj');
  $stmt->execute(array(':name' => '0', ':subj'=>'0'));

}
if ($mode == "conf_test_mail") {
/*
if (get_conf_param('mail_auth_type') != "none")
{
$mail->SMTPSecure = $CONF_MAIL['auth_type'];
}
sendmail?
SMTP?
*/
if (get_conf_param('mail_type') == "sendmail") {
$mail = new PHPMailer(true);
$mail->IsSendmail(); // telling the class to use SendMail transport
try {
$mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
$mail->AddAddress($CONF['mail'], 'admin helpdesk');
$mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
$mail->Subject = 'test message';
$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
$mail->MsgHTML('Test message via sendmail');
$mail->Send();
echo "Message Sent OK<p></p>\n";
} catch (phpmailerException $e) {
echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
echo $e->getMessage(); //Boring error messages from anything else!
}
}
else if (get_conf_param('mail_type') == "SMTP") {
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
$mail->IsSMTP(); // telling the class to use SMTP
try {
$mail->SMTPDebug = 2; // enables SMTP debug information (for testing)
$mail->SMTPAuth = $CONF_MAIL['auth']; // enable SMTP authentication
if (get_conf_param('mail_auth_type') != "none")
{
$mail->SMTPSecure = $CONF_MAIL['auth_type'];
}
$mail->Host = $CONF_MAIL['host'];
$mail->Port = $CONF_MAIL['port'];
$mail->Username = $CONF_MAIL['username'];
$mail->Password = $CONF_MAIL['password'];


$mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
$mail->AddAddress($CONF['mail'], 'admin helpdesk');
$mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
$mail->Subject = 'test message via smtp';
$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
$mail->MsgHTML("test message");
$mail->Send();
echo "Message Sent OK<p></p>\n";
} catch (phpmailerException $e) {
echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
echo $e->getMessage(); //Boring error messages from anything else!
}
}
}
        if ($mode == "add_ticket") {
            $type=($_POST['type_add']);

            $user_init_id=($_POST['user_init_id']);
            $user_to_id=($_POST['user_do']);
            $subj=strip_tags(xss_clean(($_POST['subj'])));
            $msg=strip_tags(xss_clean(($_POST['msg'])));
            $status='0';
            $unit_id=($_POST['unit_id']);
            $prio=($_POST['prio']);
            $deadline_t=($_POST['deadline_t']);
            $approve_tickets = $_POST['approved'];
            if ($confirm == "true") {$confirm=1;} else {$confirm=0;}
            if ($approve_tickets == "true") {$approve_tickets=0;$approved=1;} else {$approve_tickets=1;$approved=0;}

            $client_fio=trim(strip_tags(xss_clean(($_POST['fio']))));
            $client_tel=trim(strip_tags(xss_clean(($_POST['tel']))));
            $client_login=trim(strip_tags(xss_clean(($_POST['login']))));
            $unit_desc=strip_tags(xss_clean(($_POST['pod'])));

            $client_adr=trim(strip_tags(xss_clean(($_POST['adr']))));
            $client_mail=trim(strip_tags(xss_clean(($_POST['mail']))));
            $client_posada=trim(strip_tags(xss_clean(($_POST['posada']))));

            $client_id_param=($_POST['client_id_param']);

            if ($client_fio == "пусто") {$client_fio="";}
            if ($client_tel == "пусто") {$client_tel="";}
            if ($client_login == "пусто") {$client_login="";}
            if ($unit_desc == "пусто") {$unit_desc="";}
            if ($client_adr == "пусто") {$client_adr="";}
            if ($client_mail == "пусто") {$client_mail="";}
            if ($client_posada == "пусто") {$client_posada="";}
/*
На этом месте можно дописывать код, для обработки создания заявки.
Например SMS-информирование, подключать API и тд и тп
Доступны переменные:
$user_init_id	ID-пользователя, который создал заявку
$user_to_id		ID-пользователя, которому назначена заявку
$subj			Тема заявки
$msg			Сообщение
$unit_id		ID-подразделения, на которое назначена заявка
$prio			Приоритет заявки
$client_fio		ФИО клиента
$client_tel		Тел клиента
$client_login	Логин клиента
$unit_desc		Подразделение клиента
$client_adr		Адрес клиента
$client_mail	Почта клиента
$client_posada	Должность клиента
*/

            if ($type == "add") {



                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM clients");
                $stmt->execute();
                $max = $stmt->fetch(PDO::FETCH_NUM);

                $max_id=$max[0]+1;
                $hashname=($_POST['hashname']);




                $stmt = $dbConnection->prepare('insert into clients
	     (id, fio, tel, login, unit_desc, adr, email, posada) VALUES
	     (:max_id, :client_fio, :client_tel, :client_login, :unit_desc, :client_adr,  :client_mail, :client_posada)');

                $stmt->execute(array(
                    ':max_id'=>$max_id,
                    ':client_fio'=>$client_fio,
                    ':client_tel'=>$client_tel,
                    ':client_login'=>$client_login,
                    ':unit_desc'=>$unit_desc,
                    ':client_adr'=>$client_adr,
                    ':client_mail'=>$client_mail,
                    ':client_posada'=>$client_posada));




                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);


                $max_id_res_ticket=$max_id_ticket[0]+1;



                $stmt = $dbConnection->prepare('INSERT INTO tickets
				(id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update, deadline_t, permit_ok, approve_tickets, approved) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, now(),:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, now(), :deadline_t, :permit_ok, :approve, :approved)');
                $stmt->execute(array(':max_id_res_ticket'=>$max_id_res_ticket,':user_init_id'=>$user_init_id,':user_to_id'=>$user_to_id,':subj'=>$subj,':msg'=>$msg,':max_id'=>$max_id,':unit_id'=>$unit_id,':status'=>$status,':hashname'=>$hashname,':prio'=>$prio, ':deadline_t'=>$deadline_t, ':permit_ok'=>$confirm, ':approve'=>$approve_tickets, ':approved' => $approved));



                $stmt = $dbConnection->prepare('SELECT field_hash, field_name FROM dop_fields');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                  $field_hash = $row['field_hash'];
                  $field_value = $_POST[$field_hash];
                  $field_name = $row['field_name'];
                  if (is_array($field_value)){
                    if (!empty($field_value)){
                      $field_value_end = trim(implode(',',$field_value));
                  }

                  }
                  else{
                    $field_value_end = trim($field_value);
                  }
                  if ($field_value_end != ""){
                  $stmt = $dbConnection->prepare('INSERT INTO tickets_fields (ticket_hash, field_name, field_value) values (:hashname, :field_name, :field_value)');
                  $stmt->execute(array(':hashname'=>$hashname, ':field_name' => $field_name, ':field_value' => $field_value_end));
                  }
                }




                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, now(), :unow, :max_id_res_ticket, :user_to_id, :unit_id)');

                $stmt->execute(array(':create'=>'create', ':unow'=>$unow,':max_id_res_ticket'=>$max_id_res_ticket,':user_to_id'=>$user_to_id,':unit_id'=>$unit_id));


if ($CONF_MAIL['active'] == "true") {
                if ($user_to_id == "0") {
                    send_mail_to('new_all',$max_id_res_ticket);
                }

                else if ($user_to_id <> "0") {
                    send_mail_to('new_user',$max_id_res_ticket);
                    send_mail_to('new_coord',$max_id_res_ticket);

                }
                }
if ($CONF_JABBER['active'] == "true") {
                if ($user_to_id == "0") {
                    send_jabber_to('new_all',$max_id_res_ticket);
                }

                else if ($user_to_id <> "0") {
                    send_jabber_to('new_user',$max_id_res_ticket);
                    send_jabber_to('new_coord',$max_id_res_ticket);

                }
            }
if ($CONF_PUSH['active'] == "true") {
                if ($user_to_id == "0") {
                      send_push_to('new_all',$max_id_res_ticket);
                  }

                  else if ($user_to_id <> "0") {
                      send_push_to('new_user',$max_id_res_ticket);
                      send_push_to('new_coord',$max_id_res_ticket);

                  }
            }
                echo($hashname);
            }
            if ($type == "edit") {

                $hashname=($_POST['hashname']);
                if (get_user_val('priv_edit_client') == 1){
                  $can_edit = true;
                }
                else if (get_user_val('priv_edit_client') == 0){
                  $can_edit = false;
                }
                if ($can_edit == true){
                $stmt = $dbConnection->prepare('update clients set tel=:client_tel, login=:client_login, unit_desc=:unit_desc, adr=:client_adr, email=:client_mail, posada=:client_posada where id=:client_id_param');

                $stmt->execute(array(':client_tel'=>$client_tel, ':client_login'=>$client_login, ':unit_desc'=>$unit_desc, ':client_adr'=>$client_adr, ':client_mail'=>$client_mail, ':client_posada'=>$client_posada, ':client_id_param'=>$client_id_param));
                }





                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);


                $max_id_res_ticket=$max_id_ticket[0]+1;




                $stmt = $dbConnection->prepare('INSERT INTO tickets
				(id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update, deadline_t, permit_ok, approve_tickets, approved) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, now(),:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, now(), :deadline_t, :permit_ok, :approve, :approved)');
                $stmt->execute(array(':max_id_res_ticket'=>$max_id_res_ticket,':user_init_id'=>$user_init_id,':user_to_id'=>$user_to_id,':subj'=>$subj,':msg'=>$msg,':max_id'=>$client_id_param,':unit_id'=>$unit_id,':status'=>$status,':hashname'=>$hashname,':prio'=>$prio, ':deadline_t'=>$deadline_t, ':permit_ok'=>$confirm, ':approve'=>$approve_tickets, ':approved' => $approved));


                $stmt = $dbConnection->prepare('SELECT field_hash, field_name FROM dop_fields');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                  $field_hash = $row['field_hash'];
                  $field_value = $_POST[$field_hash];
                  $field_name = $row['field_name'];
                  if (is_array($field_value)){
                    if (!empty($field_value)){
                      $field_value_end = trim(implode(',',$field_value));
                  }

                  }
                  else{
                    $field_value_end = trim($field_value);
                  }
                  if ($field_value_end != ""){
                  $stmt = $dbConnection->prepare('INSERT INTO tickets_fields (ticket_hash, field_name, field_value) values (:hashname, :field_name, :field_value)');
                  $stmt->execute(array(':hashname'=>$hashname, ':field_name' => $field_name, ':field_value' => $field_value_end));
                  }
                }


                $unow=$_SESSION['helpdesk_user_id'];




                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, now(), :unow, :max_id_res_ticket, :user_to_id, :unit_id)');

                $stmt->execute(array(':create'=>'create', ':unow'=>$unow,':max_id_res_ticket'=>$max_id_res_ticket,':user_to_id'=>$user_to_id,':unit_id'=>$unit_id));


//echo("dd");
if ($CONF_MAIL['active'] == "true") {
                if ($user_to_id == "0") {
                    send_mail_to('new_all',$max_id_res_ticket);
                }
                else if ($user_to_id <> "0") {
                    send_mail_to('new_user',$max_id_res_ticket);
                    send_mail_to('new_coord',$max_id_res_ticket);

                }
                }
if ($CONF_JABBER['active'] == "true") {
                if ($user_to_id == "0") {
                    send_jabber_to('new_all',$max_id_res_ticket);
                }

                else if ($user_to_id <> "0") {
                    send_jabber_to('new_user',$max_id_res_ticket);
                    send_jabber_to('new_coord',$max_id_res_ticket);

                }
              }
if ($CONF_PUSH['active'] == "true") {
              if ($user_to_id == "0") {
                  send_push_to('new_all',$max_id_res_ticket);
              }

              else if ($user_to_id <> "0") {
                  send_push_to('new_user',$max_id_res_ticket);
                  send_push_to('new_coord',$max_id_res_ticket);

              }
          }
                echo($hashname);
            }



check_unlinked_file();

        }

    }
}
?>

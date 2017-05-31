<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");



?>
<style>
.alert-info hr{
  border-top-color: #fff !important;
}
.chosen-container{
  width: 100% !important;
}
</style>

<div class="container">
<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
<div class="page-header" style="margin-top: -15px;">
<div class="row">
         <div class="col-md-6"> <h3><i class="fa fa-cog"></i>  <?=lang('CONF_title');?></h3></div><div class="col-md-6">

</div>

</div>
 </div>


<div class="row" >
<div class="col-md-3">
  <div class="alert alert-info" role="alert">
  <small>
  <i class="fa fa-info-circle" aria-hidden="true"></i>

<?=lang('CONF_info');?>
<hr>
<?=lang('CONF_version')." ".get_version()?>
<br>
<?=lang('CONF_version_1');?>
</small>
<button class="btn btn-default btn-block" style="margin-top: 20px;" id="conf_check_update"><?=lang('CONF_check_update');?></button>
<div id="check_update"></div>
<hr>
<button class="btn btn-primary btn-block" id="conf_system_update" data-toggle="tooltip" data-placement="left" title="<?=lang('CONF_system_update_title');?>"><?=lang('CONF_system_update');?></button>
  <div id="up_success"></div>
  </div>

      <div class="alert alert-warning" role="alert">
      <small>
      Coding by
      Y.Snisar (c) 2014<br>
      <a class="alert-link" href="https://github.com/rustem-art/hd.rustem"><i class="fa fa-github"></i> Rustem-Art on github</a><br>
      <i class="fa fa-envelope"></i> info&#64rustem&#46com&#46ua<br>
      <i class="fa fa-skype"></i> rustem_ck (only for $)
      </ul>
      </small>
      </div>


      </div>

      <div class="col-md-9" id="content_info">

        <div  class="tab_conf">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#conf_main" data-toggle="tab"><i class="fa fa-cog"></i> <?=lang('CONF_mains');?></a></li>
                <li><a href="#conf_ticket" data-toggle="tab"><i class="fa fa-tag"></i> <?=lang('CONF_ticket_name');?></a></li>
                <li><a href="#conf_jabber" data-toggle="tab"><i class="fa fa-bell"></i> <?=lang('CONF_jabber_name');?></a></li>
                <li><a href="#conf_mail" data-toggle="tab"><i class="fa fa-send"></i> <?=lang('CONF_mail_name');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="conf_main">
                  <div class="col-md-12 box-body_conf">
                  <form class="form-horizontal" role="form">
                  <div class="form-group">
                  <label for="name_of_firm" class="col-sm-4 control-label"><small><?=lang('CONF_name');?></small></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control input-sm" id="name_of_firm" placeholder="<?=lang('CONF_name');?>" value="<?=get_conf_param('name_of_firm');?>">
                  </div>
                </div>

                  <div class="form-group">
                  <label for="mail" class="col-sm-4 control-label"><small><?=lang('CONF_mail');?></small></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control input-sm" id="mail" placeholder="<?=lang('CONF_mail');?>" value="<?=get_conf_param('mail');?>">
                  </div>
                </div>



                <div class="form-group">
                  <label for="title_header" class="col-sm-4 control-label"><small><?=lang('CONF_title_org');?></small></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control input-sm" id="title_header" placeholder="<?=lang('CONF_title_org');?>" value="<?=get_conf_param('title_header');?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="hostname" class="col-sm-4 control-label"><small><?=lang('CONF_url');?></small></label>
                  <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">http://</span>
                  <input type="text" class="form-control input-sm" id="hostname" placeholder="<?php
                  $pos = strrpos($_SERVER['REQUEST_URI'], '/');
                  echo $_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], 0, $pos + 1);?>" value="<?=preg_replace("/http:\/\//","",get_conf_param('hostname')); ?>">
                </div>
              </div>
                </div>
                <div class="form-group">
                <label for="time_zone" class="col-sm-4 control-label"><small><?=lang('CONF_time_zone');?></small></label>
                <div class="col-sm-8">
                  <?=Helper_TimeZone::getTimeZoneSelect(get_conf_param('time_zone'));?>
                </div>
              </div>
                    <div class="form-group">
                  <label for="first_login" class="col-sm-4 control-label"><small><?=lang('CONF_f_login');?></small></label>
                  <div class="col-sm-8">
                <select class="chosen-select_no_search form-control input-sm" id="first_login">
                <option value="true" <?php if (get_conf_param('first_login') == "true") {echo "selected";} ?>><?=lang('CONF_f_login_opt_true');?></option>
                <option value="false" <?php if (get_conf_param('first_login') == "false") {echo "selected";} ?>><?=lang('CONF_false');?></option>
              </select>
              <p class="help-block"><small>
              <?=lang('CONF_f_login_info');?>
              </small></p>
               </div>
                </div>


                <div class="col-md-offset-3 col-md-6">
              <center>
                  <button type="submit" id="conf_edit_main" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('CONF_act_edit');?></button>

              </center>

              </div>

                  </form>

                <div class="col-md-12" style="margin-top:10px;" id="conf_edit_main_res"></div>
              </div>
                </div>
                <div class="tab-pane fade" id="conf_ticket">
                  <div class="col-md-12 box-body_conf">
                  <form class="form-horizontal" role="form">
                    <div class="form-group">
                    <label for="days2arch" class="col-sm-4 control-label"><small><?=lang('CONF_2arch');?></small></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control input-sm" id="days2arch" placeholder="<?=lang('CONF_2arch');?>" value="<?=get_conf_param('days2arch');?>">
                      <p class="help-block"><small><?=lang('CONF_2arch_info');?></small><br>
                      <pre><small class="pull-left">5 0 * * * /usr/bin/php5 -f <?=realpath(dirname(dirname(__FILE__)))."/sys/4cron.php"?> > <?=realpath(dirname(dirname(__FILE__)))."/4cron.log"?> 2>&1</small></pre>
                    </p>
                      <p class="help-block"><small><?=lang('CONF_ticket_update');?></small><br>
                      <pre><small>5 0 * * * /usr/bin/php5 -f <?=realpath(dirname(dirname(__FILE__)))."/sys/4cron_live_ticket.php"?> > <?=realpath(dirname(dirname(__FILE__)))."/4cron.log"?> 2>&1</small></pre>
                    </p>
                    </div>
                  </div>
                    <div class="form-group">
                <label for="fix_subj" class="col-sm-4 control-label"><small><?=lang('CONF_subj');?></small></label>
                <div class="col-sm-8">
              <select class="chosen-select_no_search form-control input-sm" id="fix_subj">
              <option value="true" <?php if (get_conf_param('fix_subj') == "true") {echo "selected";} ?>><?=lang('CONF_fix_list');?></option>
              <option value="false" <?php if (get_conf_param('fix_subj') == "false") {echo "selected";} ?>><?=lang('CONF_subj_text');?></option>
            </select>
            <p class="help-block"><small>
            <?=lang('CONF_subj_info');?>
            </small></p>
            </div>
              </div>

                      <div class="form-group">
                <label for="file_uploads" class="col-sm-4 control-label"><small><?=lang('CONF_fup');?></small></label>
                <div class="col-sm-8">
              <select class="chosen-select_no_search form-control input-sm" id="file_uploads">
              <option value="true" <?php if (get_conf_param('file_uploads') == "true") {echo "selected";} ?>><?=lang('CONF_true');?></option>
              <option value="false" <?php if (get_conf_param('file_uploads') == "false") {echo "selected";} ?>><?=lang('CONF_false');?></option>
            </select>
            <p class="help-block"><small>
            <?=lang('CONF_fup_info');?>
            </small></p>
            </div>
              </div>



              <div class="form-group">
                <label for="file_types" class="col-sm-4 control-label"><small><?=lang('CONF_file_types');?></small></label>
                <div class="col-sm-8">
                  <input type="text" class="form-control input-sm" id="file_types" placeholder="gif,jpe?g,png,doc,xls,rtf,pdf,zip,rar,bmp,docx,xlsx" value="<?php
                  $bodytag = str_replace("|", ",", get_conf_param('file_types'));
                  echo $bodytag;

                  ?>">

                </div>
              </div>

                <div class="form-group">
                <label for="file_size" class="col-sm-4 control-label"><small><?=lang('CONF_file_size');?></small></label>
                <div class="col-sm-8">
                <div class="input-group">
                  <input type="text" class="form-control input-sm" id="file_size" placeholder="5" value="<?=round(get_conf_param('file_size')/1024/1024);?>">
            <span class="input-group-addon">Mb</span>
                </div>
                </div>
              </div>
              <div class="col-md-offset-3 col-md-6">
            <center>
                <button type="submit" id="conf_edit_ticket" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('CONF_act_edit');?></button>

            </center>

            </div>
                  </form>
                  <div class="col-md-12" style="margin-top:10px;" id="conf_edit_ticket_res"></div>
                </div>
              </div>
    <div class="tab-pane fade" id="conf_jabber">
      <div class="col-md-12 box-body_conf">
        <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="jabber_active" class="col-sm-4 control-label"><small><?=lang('CONF_jabber_status');?></small></label>
            <div class="col-sm-8">
          <select class="chosen-select_no_search form-control input-sm" id="jabber_active">
          <option value="true" <?php if (get_conf_param('jabber_active') == "true") {echo "selected";} ?>><?=lang('CONF_true');?></option>
          <option value="false" <?php if (get_conf_param('jabber_active') == "false") {echo "selected";} ?>><?=lang('CONF_false');?></option>
        </select>    </div>
          </div>
          <div class="form-group">
            <label for="server" class="col-sm-4 control-label"><small><?=lang('CONF_jabber_server');?></small></label>
            <div class="col-sm-8">
              <input type="text" class="form-control input-sm" id="jabber_server" placeholder="<?=lang('CONF_jabber_server');?>" value="<?=get_conf_param('jabber_server')?>">
            </div>
          </div>
          <div class="form-group">
            <label for="port" class="col-sm-4 control-label"><small><?=lang('CONF_jabber_port');?></small></label>
            <div class="col-sm-8">
              <input type="text" class="form-control input-sm" id="jabber_port" placeholder="<?=lang('CONF_jabber_port');?>" value="<?=get_conf_param('jabber_port')?>">
            </div>
          </div>
          <div class="form-group">
            <label for="login" class="col-sm-4 control-label"><small><?=lang('CONF_jabber_login');?></small></label>
            <div class="col-sm-8">
              <input type="text" class="form-control input-sm" id="jabber_login" placeholder="<?=lang('CONF_jabber_login');?>" value="<?=get_conf_param('jabber_login')?>">
            </div>
          </div>
          <div class="form-group">
            <label for="password" class="col-sm-4 control-label"><small><?=lang('CONF_jabber_pass');?></small></label>
            <div class="col-sm-8">
              <input type="password" class="form-control input-sm" id="jabber_pass" placeholder="<?=lang('CONF_jabber_pass');?>" value="<?=get_conf_param('jabber_pass')?>">
            </div>
          </div>
          <div class="col-md-offset-3 col-md-6">
      <center>
          <button type="submit" id="conf_edit_jabber" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('CONF_act_edit');?></button>

      </center>
      </div>
        </form>
        <button type="submit" id="conf_test_jabber" class="btn btn-default btn-sm pull-right"> test</button>
        <div class="col-md-12" style="margin-top:10px;" id="conf_edit_jabber_res"></div>
        <div class="col-md-12" style="margin-top:10px;" id="conf_test_jabber_res"></div>
      </div>
    </div>

      <div class="tab-pane fade" id="conf_mail">
        <div class="col-md-12 box-body_conf">
          <form class="form-horizontal" role="form">

          <div class="form-group">
          <label for="mail_active" class="col-sm-4 control-label"><small><?=lang('CONF_mail_status');?></small></label>
          <div class="col-sm-8">
        <select class="chosen-select_no_search form-control input-sm" id="mail_active">
        <option value="true" <?php if (get_conf_param('mail_active') == "true") {echo "selected";} ?>><?=lang('CONF_true');?></option>
        <option value="false" <?php if (get_conf_param('mail_active') == "false") {echo "selected";} ?>><?=lang('CONF_false');?></option>
      </select>    </div>
        </div>

        <div class="form-group">
          <label for="from" class="col-sm-4 control-label"><small><?=lang('CONF_mail_from');?></small></label>
          <div class="col-sm-8">
            <input type="text" class="form-control input-sm" id="from" placeholder="<?=lang('CONF_mail_from');?>" value="<?=get_conf_param('mail_from')?>">
          </div>
        </div>
            <div class="form-group">
          <label for="mail_type" class="col-sm-4 control-label"><small><?=lang('CONF_mail_type');?></small></label>
          <div class="col-sm-8">
        <select class="chosen-select_no_search form-control input-sm" id="mail_type">
        <option value="sendmail" <?php if (get_conf_param('mail_type') == "sendmail") {echo "selected";} ?>>sendmail</option>
        <option value="SMTP" <?php if (get_conf_param('mail_type') == "SMTP") {echo "selected";} ?>>SMTP</option>
      </select>    </div>
        </div>

        <div id="smtp_div">

          <div class="form-group">
          <label for="host" class="col-sm-4 control-label"><small><?=lang('CONF_mail_host');?></small></label>
          <div class="col-sm-8">
            <input type="text" class="form-control input-sm" id="host" placeholder="<?=lang('CONF_mail_host');?>" value="<?=get_conf_param('mail_host')?>">
          </div>
        </div>

          <div class="form-group">
          <label for="port" class="col-sm-4 control-label"><small><?=lang('CONF_mail_port');?></small></label>
          <div class="col-sm-8">
            <input type="text" class="form-control input-sm" id="port" placeholder="<?=lang('CONF_mail_port');?>" value="<?=get_conf_param('mail_port')?>">
          </div>
        </div>

        <div class="form-group">
          <label for="auth" class="col-sm-4 control-label"><small><?=lang('CONF_mail_auth');?></small></label>
          <div class="col-sm-8">
        <select class="chosen-select_no_search form-control input-sm" id="auth">
        <option value="true" <?php if (get_conf_param('mail_auth') == "true") {echo "selected";} ?>><?=lang('CONF_true');?></option>
        <option value="false" <?php if (get_conf_param('mail_auth') == "false") {echo "selected";} ?>><?=lang('CONF_false');?></option>
      </select>    </div>
        </div>

        <div class="form-group">
          <label for="auth_type" class="col-sm-4 control-label"><small><?=lang('CONF_mail_type');?></small></label>
          <div class="col-sm-8">
        <select class="chosen-select_no_search form-control input-sm" id="auth_type">
        <option value="none" <?php if (get_conf_param('mail_auth_type') == "none") {echo "selected";} ?>>no</option>
        <option value="ssl" <?php if (get_conf_param('mail_auth_type') == "ssl") {echo "selected";} ?>>SSL</option>
        <option value="tls" <?php if (get_conf_param('mail_auth_type') == "tls") {echo "selected";} ?>>TLS</option>
      </select>    </div>
        </div>

            <div class="form-group">
          <label for="username" class="col-sm-4 control-label"><small><?=lang('CONF_mail_login');?></small></label>
          <div class="col-sm-8">
            <input type="text" class="form-control input-sm" id="username" placeholder="<?=lang('CONF_mail_login');?>" value="<?=get_conf_param('mail_username')?>">
          </div>
        </div>

            <div class="form-group">
          <label for="password" class="col-sm-4 control-label"><small><?=lang('CONF_mail_pass');?></small></label>
          <div class="col-sm-8">
            <input type="password" class="form-control input-sm" id="password" placeholder="<?=lang('CONF_mail_pass');?>" value="<?=get_conf_param('mail_password')?>">
          </div>
        </div>

        </div>


          <div class="col-md-offset-3 col-md-6">
      <center>
          <button type="submit" id="conf_edit_mail" class="btn btn-success"><i class="fa fa-pencil"></i> <?=lang('CONF_act_edit');?></button>
      </center>
      </div>
          </form>
          <button type="submit" id="conf_test_mail" class="btn btn-default btn-sm pull-right"> test</button>
            <div class="col-md-12" style="margin-top:10px;" id="conf_edit_mail_res"></div>
            <div class="col-md-12" style="margin-top:10px;" id="conf_test_mail_res"></div>
        </div>
      </div>
    </div>
  </div>




      </div>
            <br>
      <?php

       ?>



</div>




<br>
</div>
<?php
 include("footer.inc.php");
?>

<?php
    }
    }
else {
    include '../auth.php';
}
?>
<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$priv_val = priv_status($_SESSION['helpdesk_user_id']);
if ((validate_admin($_SESSION['helpdesk_user_id'])) || ($priv_val == "2")) {
   include("head.inc.php");
   include("navbar.inc.php");



?>


<div class="container">
<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
<div class="page-header" style="margin-top: -15px;">
<div class="row">
         <div class="col-md-6"> <h3><i class="fa fa-files-o"></i> <?=lang('FILES_title');?></h3></div><div class="col-md-6">

</div>

</div>
 </div>


<div class="row" >
<div class="col-md-3">

<?php if ($CONF['file_uploads'] == "false") { ?>
<div class="alert alert-danger" role="alert">
      <small>
<?=lang('FILES_off');?>
      </small>
      </div>
<?php } ?>

      <div class="alert alert-info" role="alert">
      <small>
      <i class="fa fa-info-circle"></i>

<?=lang('FILES_info');?>
      </small>
      </div>
      </div>
      <div class="col-md-9">
        <div  class="tabbable_files">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#files_ticket" data-toggle="tab"><i class="fa fa-files-o"></i> <?=lang('FILES__ticket');?></a></li>
                <li><a href="#files_comment" data-toggle="tab"><i class="fa fa-files-o"></i> <?=lang('FILES__comment');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="files_ticket">
                  <div class="col-md-12 box-body_files">
                    <div class="box-header_files">
                    </div>
                    <div id="files_ticket_content">
                      <?php view_files_ticket();?>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="files_comment">
                  <div class="col-md-12 box-body_files">
                    <div class="box-header_files">
                    </div>
                    <div id="files_comment_content">
                      <?php view_files_comment();?>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>


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
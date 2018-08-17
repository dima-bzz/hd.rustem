<?php
session_start();
include_once("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
  if(get_conf_param('approve_tickets') == 'true'){

      include("head.inc.php");
      include("navbar.inc.php");

        ?>


        <div class="container">
	<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
            <div class="page-header" style="margin-top: -15px;">
                <div class="row">
                    <div class="col-md-6"> <h3><i class="fa fa-exclamation-circle"></i> <?=lang('APPROVE_tickets_title');?></h3></div>

                </div>
            </div>


            <div class="row">


      <div class="col-md-3">
      <div class="alert alert-info" role="alert">
      <small>
      <i class="fa fa-info-circle"></i>

<?=lang('APPROVED_tickets_info');?>
      </small>
      </div>
      </div>

      <div class="col-md-9" id="content_approved_tickets">


        <?php view_approved_tickets(); ?>

      </div>

      </div>

        </div>
        <?php
        include("footer.inc.php");

}
}
else {
    include 'auth.php';
}
?>
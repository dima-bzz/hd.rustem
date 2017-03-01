<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$priv_val = priv_status($_SESSION['helpdesk_user_id']);
$shutdown = get_conf_param('shutdown');
$sh = explode(",",$shutdown);
if ((validate_admin($_SESSION['helpdesk_user_id'])) || (in_array($_SESSION['helpdesk_user_id'],$sh))) {
   include("head.inc.php");
   include("navbar.inc.php");



?>
<style>
.modal-footer{
  text-align: center;
}
.cancel,.danger{
  width: 49%;
}
</style>

<div class="container">
<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
<div class="page-header" style="margin-top: -15px;">
<div class="row">
         <div class="col-md-6"> <h3><i class="fa fa-power-off"></i> <?=lang('Shut_title');?></h3></div><div class="col-md-6">

</div>

</div>
 </div>


<div class="row" >
<div class="col-md-12">

      <div class="alert alert-danger" id="shut_info" role="alert">
        <center>
      <h4>
      <i class="fa fa-warning"></i>
<?=lang('Shut_info');?>
      </h4>
    </center>
      <br>
      <center>
        <button id="shutdown" type="button" class="btn btn-danger"><?=lang('Shutdown');?></button>
      </center>
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
<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");


if (isset($_GET['create'])) {
	$status_create="active";
}
else if (isset($_GET['list'])) {
	$status_list="active";
}
else {
	$status_list="active";
}

?>


<div class="container">
   <input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
<div class="page-header" style="margin-top: -15px;">
          <h3 ><i class="fa fa-users"></i> <?=lang('USERS_title');?></h3>
 </div>


<div class="row">
  <div class="col-md-3">
	  <ul class="nav nav-pills nav-stacked">
  <li class="<?=$status_create?>"><a href="?adduser" id="create_user"><i class="fa fa-male"></i> <?=lang('USERS_create');?></a></li>
  <li class="<?=$status_list?>"><a href="?alluser" id="list_user"><i class="fa fa-list-alt"></i> <?=lang('USERS_list');?></a></li>
 </ul>
  </div>
  <div class="col-md-8">
	  <div id="content_users">
	  <?php

	  if (isset($_GET['adduser'])) {
		//echo "in";
		$_POST['menu']="new";
		include_once("users.inc.php");
		}

		else if (isset($_GET['alluser'])) {
		//echo "in";
		$_POST['menu']="list";
		include_once("users.inc.php");
		}

		else if (isset($_GET['edit'])) {
		//echo "in";
		$_POST['menu']="edit";
		$_POST['id']=$_GET['edit'];
		include_once("users.inc.php");
		}
		else {
		$_GET['alluser']="s";
			$_POST['menu']="list";
		include_once("users.inc.php");
		}

	  ?>
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
    include 'auth.php';
}
?>
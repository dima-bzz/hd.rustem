<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");



?>
<style>
.sort-highlight{
  background-color: #fcf8e3;
  border: 2px dashed #428bca;
}
</style>







<div class="container">
<div class="page-header" style="margin-top: -15px;">
<div class="row">
         <div class="col-md-6"> <h3><i class="fa fa-tags"></i> <?=lang('SUBJ_title');?></h3></div><div class="col-md-6">

         <h4> <div class="input-group">
      <input type="text" class="form-control input-sm ui-autocomplete-input" id="subj_text" placeholder="<?=lang('SUBJ_name');?>" autocomplete="off">
      <span class="input-group-btn">
        <button id="subj_add" class="btn btn-default btn-sm" type="submit"><?=lang('SUBJ_add');?></button>
      </span>
    </div></h4></div>

</div>
 </div>


<div class="row">
      <div class="col-md-3">
        <?php if ($CONF['fix_subj'] == "false") { ?>
        <div class="alert alert-danger" role="alert">
              <small>
        <?=lang('DEPS_off');?>
              </small>
              </div>
        <?php } ?>

      <div class="alert alert-info" role="alert">
      <small>
      <i class="fa fa-info-circle"></i>

<?=lang('SUBJ_info');?>
      </small>
      </div>
      </div>

      <div class="col-md-9" id="content_subj">

<?php

	//$results = mysql_query("select id, name from subj;");

	$stmt = $dbConnection->prepare('select id, name, position from subj order by position asc');
	$stmt->execute();
	$res1 = $stmt->fetchAll();



?>



<table class="table table-bordered table-hover" style=" font-size: 14px;background-color:#fff" id="">
        <thead>
          <tr>
            <th><center>ID</center></th>
            <th><center><?=lang('SUBJ_n');?></center></th>
            <th><center><?=lang('SUBJ_action');?></center></th>
          </tr>
        </thead>
	<tbody>
	<?php
	//while ($row = mysql_fetch_assoc($results)) {
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
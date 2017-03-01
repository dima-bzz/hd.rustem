<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");

   
  

?>


<div class="container">
<input type="hidden" id="main_last_new_ticket" value="<?=get_last_ticket_new($_SESSION['helpdesk_user_id']);?>">
<div class="page-header" style="margin-top: -15px;">
<div class="row">
         <div class="col-md-6"> <h3><i class="fa fa-history"></i> <?=lang('Live_title');?></h3></div><div class="col-md-6"> 
         
</div>
         
</div>
 </div>
        

<div class="row" >
<div class="col-md-3">



      <div class="alert alert-info" role="alert">
      <small>
      <i class="fa fa-info-circle"></i> 
          
<?=lang('Live_info');?>
      </small>
      </div>
      </div>

      <div class="col-md-9" id="content_users">
      
      
<table class="table table-bordered table-hover" style=" font-size: 14px; ">
        <thead>
          <tr>
            <th><center>ID</center></th>
            <th><center><?=lang('ALLSTATS_user_fio');?></center></th>
	    <th><center><?=lang('Live_units');?></center></th>
            <th><center><?=lang('t_LIST_status');?></center></th>
          </tr>
        </thead>
	<tbody>		
	<?php 
	
	    $stmt = $dbConnection->prepare('SELECT id, unit, live from users order by live DESC, id ASC');
    $stmt->execute();
    $result = $stmt->fetchAll();
        if (!empty($result)) {
        
        
        
     
foreach ($result as $row) {
    $unit=view_array(get_unit_name_return($row['unit']));
if($result) { 

?>
	<tr id="tr_<?=$row['id'];?>" class="<?=$cl;?>">
	
	
	<td><small><center><?=$row['id'];?></center></small></td>
	
	<td style=""><small><?=name_of_user_ret($row['id']);?></small></td>
	<td><small><span data-toggle="tooltip" data-placement="right" title="<?=$unit;?>"><?=lang('LIST_pin')?> <?=count(get_unit_name_return($row['unit'])); ?> </span></small></td>
        <td style=""><small class="text-danger"><center><?=get_user_status($row['id']);?></center></small></td>
	

	</tr>
		<?php
}

		}} ?>
	
	
	    
	</tbody>
</table>
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
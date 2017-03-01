<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
//if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include("head.inc.php");
    include("navbar.inc.php");



    ?>
    <style>
    .nav-tabs>li.active>a,.nav-tabs>li.active>a:hover,.nav-tabs > li.active>a:focus{
      border-top: 3px solid transparent;
      border-top-color: #3c8dbc !important;
      border-left-color: : #eee !important;
      border-right-color: : #eee !important;
      background-color: #fff !important;
    }
    .nav-tabs > li:not(.active) > a:hover,
    .nav-tabs > li:not(.active) > a:focus,
    .nav-tabs > li:not(.active) > a:active {
      border-color: transparent;
      background-color: #fff !important;
    }
    </style>
    <div class="container">
        <div class="page-header" style="margin-top: -15px;">
            <h3 ><center><?=lang('HELP_title');?></center></h3>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="panel ">
                    <div class="panel-body">
                        <center>
                            <img src="img/helpdesk.001.png" class="img-responsive img-thumbnail">
                        </center>
                    </div>
                </div>
            </div>
            <div class="col-md-offset-1 col-md-10">











              <div  class="helptable">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#home" data-toggle="tab">1. <?=lang('HELP_new');?></a></li>
                    <li><a href="#profile" data-toggle="tab">2. <?=lang('HELP_review');?></a></li>
                    <li><a href="#messages" data-toggle="tab">3. <?=lang('HELP_edit_user');?></a></li>
		    <li><a href="#prof" data-toggle="tab">4. <?=lang('HELP_edit_prof');?></a></li>

                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="home">
                      <div class="help_box">
                                <img src="img/help1.png" class="img-responsive  img-thumbnail-my">
                                <br>
                                <div class="help_text">
                                <?=lang('HELP_new_text');?>
                              </div>
                                </div>
                              </div>

                    <div class="tab-pane fade" id="profile">
                      <div class="help_box">
                                <img src="img/help2.png" class="img-responsive img-thumbnail-my">
                                <br>
                                <div class="help_text">
                                <?=lang('HELP_review_text');?>
				                          </div>
                                </div></div>

                    <div class="tab-pane fade" id="messages">
                      <div class="help_box">
			                     <img src="img/help4.png" class="img-responsive img-thumbnail-my">
                                <br>
                                <div class="help_text">
                                <?=lang('HELP_edit_user_text');?>
                              </div>
                            </div></div>

		    <div class="tab-pane fade" id="prof">
          <div class="help_box">
                                <img src="img/help5.png" class="img-responsive img-thumbnail-my">
                                <br>
                                <div class="help_text">
                                <?=lang('HELP_edit_prof_text');?>
                                </div>
                              </div></div>

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
    //}
}
else {
    include 'auth.php';
}
?>

<script>
    $(document).ready(function () {
        var msg = '<?php echo $this->session->userdata("msg"); ?>';
        var type = '<?php echo $this->session->userdata("type"); ?>';
        if (msg != "" && type != "") {
            if (type == "success") {
                var icon = "fa fa-check-circle vd_green";
            } else {
                var icon = "fa fa-exclamation-circle vd_red";
            }
            notification("topright", type, icon, type, msg);
<?php echo $this->session->unset_userdata("msg"); ?>
<?php echo $this->session->unset_userdata("type"); ?>
        }
    });
</script>
<div class="vd_content-wrapper">
    <div class="vd_container">
        <div class="vd_content clearfix">
            <div class="vd_head-section clearfix">
                <div class="vd_panel-header">
                    <div class="vd_panel-menu hidden-sm hidden-xs" data-intro="<strong>Expand Control</strong><br/>To expand content page horizontally, vertically, or Both. If you just need one button just simply remove the other button code." data-step=5  data-position="left">
                        <div data-action="remove-navbar" data-original-title="Remove Navigation Bar Toggle" data-toggle="tooltip" data-placement="bottom" class="remove-navbar-button menu"> <i class="fa fa-arrows-h"></i> </div>
                        <div data-action="remove-header" data-original-title="Remove Top Menu Toggle" data-toggle="tooltip" data-placement="bottom" class="remove-header-button menu"> <i class="fa fa-arrows-v"></i> </div>
                        <div data-action="fullscreen" data-original-title="Remove Navigation Bar and Top Menu Toggle" data-toggle="tooltip" data-placement="bottom" class="fullscreen-button menu"> <i class="glyphicon glyphicon-fullscreen"></i> </div>
                    </div>
                </div>
            </div>
            <div class="vd_content-section clearfix">
                <div class="panel widget light-widget">
                    <div class="panel-body">
                        <div class="panel widget">
                            <div class="panel-heading vd_bg-grey">
                                <h3 class="panel-title" style="color:white"> <span class="menu-icon"> <i class="fa fa-dot-circle-o"></i> </span>Change Doctor Setting</h3>
                            </div>
                            <div  class="panel-body table-responsive left">
                                <form class="form-horizontal"  action="<?= $this->config->base_url() ?>admin/Doc_select" method="post" role="form" id="register-form">
                                  
                             
                                    <div class="form-group">
                                        <label class="control-label  col-md-2">Subtype<span class="vd_red">*</span></label>
                                        <div id="first-name-input-wrapper"  class="controls col-md-8">
                                            <select name="subtype" id="paypal_account">
                                              
                                              <?php foreach($ress as $value){ print_r($value); ?>

                                                    <option value="<?php echo $value->id ?>"><?php echo $value->name ?></option>

                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label  col-md-2">Doctor Rate in %<span class="vd_red">*</span></label>
                                        <div id="first-name-input-wrapper"  class="controls col-md-8">
                                            <input type="text" placeholder="Doctor rate" value="<?php echo $res->Doc_rate ?>" class="width-120 required"  name="Doc_rate" id="driver_rate" required >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label  col-md-2">Ride fare(Per visit)<span class="vd_red">*</span></label>
                                        <div id="first-name-input-wrapper"  class="controls col-md-8">
                                            <input type="text" placeholder="Ride fare(Per kilometer)" value="<?php echo $set[0]->value ?>" class="width-120 required"  name="FARE" id="fare" required >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label  col-md-2">UNIT<span class="vd_red">*</span></label>
                                        <div id="first-name-input-wrapper"  class="controls col-md-8">
                                            <input type="text" placeholder="UNIT" value="<?php echo $set[1]->value ?>" class="width-120 required"  name="UNIT" id="unit" required >
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-left:160px">
                                        <button class="btn vd_bg-green vd_white" type="submit" id="submit-register">Submit</button>
                                    </div>
                                </form>
                            </div>

                  

                       


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

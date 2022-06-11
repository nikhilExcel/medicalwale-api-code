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
                                <h3 class="panel-title" style="color:#F9C30B"> <span class="menu-icon"> <i class="fa fa-dot-circle-o"></i> </span>Add New Type</h3>
                            </div>
                            <div  class="panel-body table-responsive left">
                                <form class="form-horizontal"  action="<?= $this->config->base_url() ?>admin/addType" method="post" role="form" id="register-form">
                                    <div class="form-group">
                                        <label class="control-label  col-md-2">Enter Type<span class="vd_red">*</span></label>
                                        <div id="first-name-input-wrapper"  class="controls col-md-8">
                                            <input type="text" placeholder="Type" class="width-120 required"  name="type" id="api_key" required >
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-left:160px">
                                        <button class="btn vd_bg-green vd_white" type="submit" id="submit-register" name="add">Submit</button>
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

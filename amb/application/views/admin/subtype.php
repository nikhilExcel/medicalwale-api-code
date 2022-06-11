

<style>

    .fileUpload {

        position: relative;

        overflow: hidden;

        margin: 10px;

    }

    .fileUpload input.upload {

        position: absolute;

        top: 0;

        right: 0;

        margin: 0;

        padding: 0;

        font-size: 20px;

        cursor: pointer;

        opacity: 0;

        filter: alpha(opacity=0);

    }

</style>
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


        $(document).on('click', '.btnaction', function () {
            var action = $(this).attr('data-original-title');
            var id = $(this).attr('id');
            if (action == 'view') {
                $.ajax({
                    type: 'post',
                    url: '<?php echo $this->config->base_url() ?>admin/getsubtype',
                    data: "user_id=" + id,
                    success: function (data) {
                        $("#confirm").modal("show");
                        $("#response").html(data);
                    }
                });
            }

            if (action == 'edit') {

                $.ajax({
                    type: 'post',
                    url: '<?php echo $this->config->base_url() ?>admin/getsubtype',
                    data: "id=" + id,
                    success: function (data) {
                        $("#confirm").modal("show");
                        $("#response").html(data);
                    }
                });
            }
            if (action == 'delete') {
                $('#confirmdel')
                        .modal('show', {backdrop: 'static', keyboard: false})
                        .one('click', '#delete', function (e) {
                            $.ajax({
                                type: 'post',
                                url: '<?php echo $this->config->base_url() ?>admin/subtyp_delete',
                                data: "id=" + id,
                                success: function () {
                                    $('.hiderow' + id).closest('tr').hide();
                                }
                            });
                        });
            }
        });


//        $('#example').DataTable( {
//            stateSave: true,
//            "sDom": "Tfrtip",
//            "processing": true,
//            "serverSide": true,
//            ajax: "../admin/user_search",
//            columns: [
//                { data: 0},
//                { data: 1},
//                { data: 2},
//                {
//                    data:   null,
//                    render: function ( data, type, row ) {
//                        if ( type === 'display' ) {
//                            if(row[5] == 1){
//                                return 'Driver';
//                            }else{
//                                return 'User';
//                            }
//                            
//                        }
//                        return data;
//                    },
//                    className: "dt-body-center"
//                },
//                {
//                    data:   null,
//                    render: function ( data, type, row ) {
//                        if ( type === 'display' ) {
//                            if(row[3] == 1){
//                                return '<span id="span" class="label label-success" style="background-color:green;color:white;">Active</span>';
//                            }else{
//                                return '<span id="span" class="label label-success" style="background-color:red;color:white;">Deactive</span>';
//                            }
//                            
//                        }
//                        return data;
//                    },
//                    className: "dt-body-center"
//                },
//                {
//                    data:   null,
//                    render: function ( data, type, row ) {
//                        if ( type === 'display' ) {
//                            return '<span class="menu-action hiderow'+row[4]+'"><a id="'+row[4]+'" data-original-title="view" data-toggle="tooltip" data-placement="top" class="btnaction btn menu-icon vd_bd-green vd_green"> <i class="fa fa-eye"></i> </a><a id="'+row[4]+'" data-original-title="edit" data-toggle="tooltip" data-placement="top" class="btnaction btn menu-icon vd_bd-yellow vd_yellow"> <i class="fa fa-pencil"></i> </a> <a id="'+row[4]+'" data-original-title="delete" data-toggle="tooltip" data-placement="top" class="btnaction btn menu-icon vd_bd-red vd_red"> <i class="fa fa-times"></i> </a></span>';
//                        }
//                        return data;
//                    },
//                    className: "dt-body-center"
//                }
//            ]
//        } );
    });
</script>
<style type="text/css">
    .vd_container {
    min-height: auto!important;
}
</style>
<div aria-hidden="true" role="dialog" tabindex="-1" class="modal fade" id="confirmdel" style="display: none;z-index: 2147483648">
    <div class="modal-dialog">
        <div class="modal-body">
            Are you sure want to delete!
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Delete</button>
            <button type="button" data-dismiss="modal" class="btn">Cancel</button>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" class="modal fade" id="confirm" style="display: none;z-index: 2147483648">

    <div class="modal-dialog" id="response">



    </div>

</div>
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

                                <h3 class="panel-title" style="color:#F9C30B"> <span class="menu-icon"> <i class="fa fa-dot-circle-o"></i> </span>Add New Sub Type</h3>

                            </div>

                            <div  class="panel-body table-responsive left">

                                <form class="form-horizontal"  action="<?= $this->config->base_url() ?>admin/addSubType" method="post" role="form" id="register-form" enctype="multipart/form-data">

                                    <div class="form-group">

                                        <label class="control-label  col-md-2">Select Type<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <select name="type" id="type">

                                                <?php foreach($res as $value){?>

                                                    <option value="<?php echo $value->id ?>"><?php echo $value->name ?></option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <label class="control-label  col-md-2">Enter Type<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="text" placeholder="SubType" class="width-120 required"  name="stype" id="api_key" required >

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <label class="control-label  col-md-2">Basic Details<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="text" placeholder="Basic Details" class="width-120 required"  name="detail" id="api_key" required >

                                        </div>

                                    </div>
                         <div class="form-group">

                                        <label class="control-label  col-md-2">Features<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="text" placeholder="Features" class="width-120 required"  name="features" id="api_key" required >

                                        </div>

                                    </div>

                                      <div class="form-group">

                                        <label class="control-label  col-md-2">When to Use<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="text" placeholder="When to use" class="width-120 required"  name="use" id="api_key" required >

                                        </div>

                                    </div>

             <div class="form-group">

                                        <label class="control-label  col-md-2">Cost<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="text" placeholder="Cost" class="width-120 required"  name="cost" id="api_key" required >

                                        </div>

                                    </div>

                                     <div class="form-group">

                                        <label class="control-label  col-md-2">Select Icon<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="file" placeholder="Select icon" class="width-120 required"  name="icon" id="" required >

                                        </div>

                                    </div>


                                     <div class="form-group">

                                        <label class="control-label  col-md-2">Select Amb IMG<span class="vd_red">*</span></label>

                                        <div id="first-name-input-wrapper"  class="controls col-md-8">

                                            <input type="file" placeholder="Select icon" class="width-120 required"  name="amb_img" id="" required >

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

    <div class="vd_container">

        <div class="vd_content clearfix">

            <!-- <div class="vd_head-section clearfix">

                <div class="vd_panel-header">

                    <div class="vd_panel-menu hidden-sm hidden-xs" data-intro="<strong>Expand Control</strong><br/>To expand content page horizontally, vertically, or Both. If you just need one button just simply remove the other button code." data-step=5  data-position="left">

                        <div data-action="remove-navbar" data-original-title="Remove Navigation Bar Toggle" data-toggle="tooltip" data-placement="bottom" class="remove-navbar-button menu"> <i class="fa fa-arrows-h"></i> </div>

                        <div data-action="remove-header" data-original-title="Remove Top Menu Toggle" data-toggle="tooltip" data-placement="bottom" class="remove-header-button menu"> <i class="fa fa-arrows-v"></i> </div>

                        <div data-action="fullscreen" data-original-title="Remove Navigation Bar and Top Menu Toggle" data-toggle="tooltip" data-placement="bottom" class="fullscreen-button menu"> <i class="glyphicon glyphicon-fullscreen"></i> </div>

                    </div>

                </div>

            </div> -->

            <div class="vd_content-section clearfix">

                <div class="panel widget light-widget">

                    <div class="panel-body">

                        <div class="panel widget">

                           

                            <div class="panel-heading vd_bg-grey">

                                <h3 class="panel-title" style="color:white"> <span class="menu-icon"> <i class="fa fa-dot-circle-o"></i> </span>Added SubTypes</h3>

                            </div>

                            <div  class="panel-body table-responsive">

                                <table id="example" class="table table-hover display">

                                    <thead>

                                        <tr>

                                            <!--<th>#</th>-->

                                            <th>icon</th>
                                             <th>Amb Img</th>

                                            <th>Type</th>

                                            <th>SubType</th>

                                            <th>Date</th>

                                            <!-- <th>email</th>



                                            <th>status</th>

                                            <th>Edit / Delete</th> -->

                                            <!--<th>Member</th>-->

<!--                                            <th>Status</th>-->

<!--                                            <th style="width:20%">Action</th>-->

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php

                                        if (!empty($types)) {

                                            foreach ($types as $val) {

                                                ?>

                                                <tr>
                                                    <td> <?php if (!empty($val->icon)) { ?>

                            <div id="image-div">

                                <img id="img" src="<?= $val->icon ?>" style="height: 100px;width: 100px"/>

                            </div>

                        <?php } else {

                            ?>

                            <img id="myno_img" style="width: 100px;height: 100px" src="../avatar/no-image.jpg" alt="your image" />

                        <?php }

                        ?>
</td>
<td> <?php if (!empty($val->amb_img)) { ?>

                            <div id="image-div">

                                <img id="img" src="<?= $val->amb_img ?>" style="height: 100px;width: 100px"/>

                            </div>

                        <?php } else {

                            ?>

                            <img id="myno_img" style="width: 100px;height: 100px" src="../avatar/no-image.jpg" alt="your image" />

                        <?php }

                        ?>
</td>
                                                  <td><?= $val->t_name ?></td>     

                                                    <td><?= $val->name ?></td>

                                                    

                                         


                                                     <td><span class="menu-action hiderow<?= $val->id ?>"><a id="<?= $val->id ?>" data-original-title="edit" data-toggle="tooltip" data-placement="top" class="btnaction btn menu-icon vd_bd-yellow vd_yellow"> <i class="fa fa-pencil"></i> </a> </span></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                    </tbody>

                                </table>

                                <?= !empty($links) ? $links : ''; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



        </div>

    </div>

</div>


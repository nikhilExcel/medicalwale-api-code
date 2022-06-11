<script>

    function imageIsLoadededit(e) {

        $('#myImgedit').show();

        $('#myno_img').hide();

        $('#myImgedit').attr('src', e.target.result);

        $('#image-div').hide();

    };

    $(function(){

        $('#myImgedit').hide();

        $("#edituploadBtn").change(function () {

            $(".edit-file").val($("#edituploadBtn").val());

            if (this.files && this.files[0]) {

                var reader = new FileReader();

                reader.onload = imageIsLoadededit;

                reader.readAsDataURL(this.files[0]);

            }

        });

       

    });

</script>
<script>

    function imageIsLoadededitt(e) {

        $('#myImgeditt').show();

        $('#myno_imgg').hide();

        $('#myImgeditt').attr('src', e.target.result);

        $('#image-divv').hide();

    };

    $(function(){

        $('#myImgeditt').hide();

        $("#edituploadBtnn").change(function () {

            $(".edit-filee").val($("#edituploadBtnn").val());

            if (this.files && this.files[0]) {

                var reader = new FileReader();

                reader.onload = imageIsLoadededitt;

                reader.readAsDataURL(this.files[0]);

            }

        });

       

    });

</script>
<div class="panel widget light-widget col-md-12">

    <div class="panel-body">

        <h3 class="mgbt-xs-20">Edit Subtype</h3>

        <hr/>

        <form enctype="multipart/form-data" class="form-horizontal"  action="<?php echo $this->config->base_url() ?>admin/update_sub" method="post" role="form" id="register-form">



            <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label  col-md-4">SubType<span class="vd_red">*</span></label>

                    <div id="first-name-input-wrapper"  class="controls col-md-8">

                        <input type="text" placeholder="John" value="<?php echo!empty($post['name']) ? $post['name'] : ''; ?>" class="width-120 required" name="name" id="name" required >

                    </div>

                </div>

            </div>


            <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label  col-md-4">Basic Details<span class="vd_red">*</span></label>

                    <div id="first-name-input-wrapper"  class="controls col-md-8">

                        <input type="text" placeholder="John" value="<?php echo!empty($post['details']) ? $post['details'] : ''; ?>" class="width-120 required" name="detail" id="name" required >

                    </div>

                </div>

            </div>
    <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label  col-md-4">Features<span class="vd_red">*</span></label>

                    <div id="first-name-input-wrapper"  class="controls col-md-8">

                        <input type="text" placeholder="John" value="<?php echo!empty($post['feature']) ? $post['feature'] : ''; ?>" class="width-120 required" name="features" id="name" required >

                    </div>

                </div>

            </div>
            <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label  col-md-4">When to Use<span class="vd_red">*</span></label>

                    <div id="first-name-input-wrapper"  class="controls col-md-8">

                        <input type="text" placeholder="John" value="<?php echo!empty($post['when_use']) ? $post['when_use'] : ''; ?>" class="width-120 required" name="use" id="name" required >

                    </div>

                </div>

            </div>
               <div class="form-group">

                

            </div>
              <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label  col-md-4">Cost<span class="vd_red">*</span></label>

                    <div id="first-name-input-wrapper"  class="controls col-md-8">

                        <input type="text" placeholder="John" value="<?php echo!empty($post['cost']) ? $post['cost'] : ''; ?>" class="width-120 required" name="cost" id="name" required >

                    </div>

                </div>

            </div>
             <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label col-md-4">Image</label>

                    <div id="email-input-wrapper"  class="controls col-sm-6 col-md-8">

                        <input class="edit-file" placeholder="Choose File" disabled="disabled" />

                        <div class="fileUpload btn btn-primary">

                            <span>Upload Photo</span>

                            <input  id="edituploadBtn" type="file"  name="icon" class="upload" />

                        </div>

                        <br/>

                        <img id="myImgedit" style="width: 100px;height: 100px" src="../avatar/no-image.jpg" alt="your image" />

                        <?php if (!empty($post['icon'])) { ?>

                            <div id="image-div">

                                <img id="img" src="<?php echo $post['icon'] ?>" style="height: 100px;width: 100px"/>

                            </div>

                        <?php } else {

                            ?>

                            <img id="myno_img" style="width: 100px;height: 100px" src="../avatar/no-image.jpg" alt="your image" />

                        <?php }

                        ?>

                    </div>

                </div>

            </div>
 
             <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label col-md-4">Vehicle Image</label>

                    <div id="email-input-wrapper"  class="controls col-sm-6 col-md-8">

                        <input class="edit-filee" placeholder="Choose File" disabled="disabled" />

                        <div class="fileUpload btn btn-primary">

                            <span>Upload Photo</span>

                            <input  id="edituploadBtnn" type="file"  name="amb_img" class="upload" />

                        </div>

                        <br/>

                        <img id="myImgeditt" style="width: 100px;height: 100px" src="../avatar/no-image.jpg" alt="your image" />

                        <?php if (!empty($post['amb_img'])) { ?>

                            <div id="image-divv">

                                <img id="img" src="<?php echo $post['amb_img'] ?>" style="height: 100px;width: 100px"/>

                            </div>

                        <?php } else {

                            ?>

                            <img id="myno_imgg" style="width: 100px;height: 100px" src="../avatar/no-image.jpg" alt="your image" />

                        <?php }

                        ?>

                    </div>

                </div>

            </div>

         
            <input type="hidden" value="<?php echo !empty($post['id']) ? $post['id'] : ''; ?>" name="id"/>

       
            <div id="vd_login-error" class="alert alert-danger hidden"><i class="fa fa-exclamation-circle fa-fw"></i> Please fill the necessary field </div>

            <div class="form-group">

                <div class="col-md-9"></div>

                <div class="col-md-3 mgbt-xs-10 mgtp-20">

                    <div class="vd_checkbox  checkbox-success"></div>

                    <div class="vd_checkbox checkbox-success"></div>

                    <div class="mgtp-10">

                        <button class="btn vd_bg-green vd_white" type="submit" id="submit-register" name="submit-register">Submit</button>

                    </div>

                </div>

                <div class="col-md-12 mgbt-xs-5"> </div>

            </div>

        </form>

    </div>

</div>
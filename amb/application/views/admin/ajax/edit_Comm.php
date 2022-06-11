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

<div class="panel widget light-widget col-md-12">

    <div class="panel-body">

        <h3 class="mgbt-xs-20">Edit Earning Status</h3>

        <hr/>

        <form enctype="multipart/form-data" class="form-horizontal"  action="<?php echo $this->config->base_url() ?>admin/Earnings/update" method="post" role="form" id="register-form">



                    <div class="form-group">

                <div class="col-md-12">

                    <label class="control-label col-md-4">PAYMENT STATUS</label>

                    <div id="email-input-wrapper"  class="controls col-sm-6 col-md-8">

                        <div class="vd_radio radio-success">
  <input type="hidden" value="<?php echo!empty($post['ride_id']) ? $post['ride_id'] : ''; ?>" name="ride_id"/>
                            <input type="radio" <?php echo!empty($post['payment_status']) ? $post['payment_status'] == 1 ? 'checked' : ''  : '' ?> class="radiochk" value="1" id="optionsRadios8" name="payment_status">

                            <label for="optionsRadios8"> PAID</label>

                            <input type="radio" <?php echo empty($post['payment_status']) ? 'checked' : '' ?> value="0" class="radiochk" id="optionsRadios9" name="payment_status">

                            <label for="optionsRadios9"> UNPAID</label>

                        </div>

                    </div>

                </div>

            </div>

         
            

       
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
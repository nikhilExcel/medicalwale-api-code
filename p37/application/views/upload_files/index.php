<div class="container">
    <div class="row">
        <p><?php echo $this->session->flashdata('statusMsg'); ?></p>
        <form enctype="multipart/form-data" action="" method="post">
            <div class="form-group">
                <label>Choose Files</label>
                <input type="file" class="form-control" name="userFiles[]" multiple/>
            </div>
            <div class="form-group">
                <input class="form-control" type="submit" name="fileSubmit" value="UPLOAD"/>
            </div>
        </form>
    </div>
    <div class="row">
        <ul class="gallery">
            <?php if(!empty($files)): foreach($files as $file): ?>
            <li class="item">
                <img src="<?php echo base_url('uploads/files/'.$file['file_name']); ?>" alt="" >
                <p>Uploaded On <?php echo date("j M Y",strtotime($file['created'])); ?></p>
            </li>
            <?php endforeach; else: ?>
            <p>Image(s) not found.....</p>
            <?php endif; ?>
        </ul>
    </div>
</div>
<form action="<?php echo $action; ?>" method="post" name="payuForm" id="payuForm" style="display: block">
    <input type="hidden" name="key" value="<?php echo $key ?>" />
    <input type="hidden" name="hash" value="<?php echo $HASH ?>"/>
    <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
    <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
    <input type="hidden" name="firstname" value="<?php echo $firstname; ?>" />
    <input type="hidden" name="email" value="<?php echo $email; ?>" />
    <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
    <textarea type="hidden" name="productinfo"><?php echo "$productinfo"; ?></textarea>
    <input type="hidden" name="surl" value="<?php echo $surl; ?>" />
    <input type="hidden" name="furl" value="<?php echo  $furl; ?>"/>
</form>
<script type="text/javascript">
    var payuForm = document.forms.payuForm;
    payuForm.submit();
</script>
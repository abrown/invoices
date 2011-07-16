<?php
    if( !isset($payment) ){
        $payment = new stdClass();
        $payment->invoice_id = Http::getParameter('invoice_id', 'int');
        $payment->type = '';
        $payment->billed = 0;
        $payment->total = 0;
        $payment->description = 'Enter a description here...';
    }
?>
<form method="POST" action="<?php echo $action; ?>" id="payment-edit">
    <div id="messages"></div>
    <fieldset>
        <legend>Payment</legend>
        <input type="hidden" name="Payment[invoice_id]" value="<?php echo $payment->invoice_id ?>" />
        <label>Date (DDMMYYYY)</label>
        <?php $billed = ($payment->billed) ? Entry::__getStringFromDateTime($payment->billed) : ''; ?>
        <input type="text" name="Payment[billed]" value="<?php echo $billed ?>" />
        <span class="help"></span><br/>
        <label>Type</label>
        <input type="text" name="Payment[type]" value="<?php echo $payment->type ?>" />
        <span class="help"></span><br/>
        <label>Total</label>
        <?php $total = number_format($payment->total, 2, '.', ''); ?>
        <input type="text" name="Payment[total]" value="<?php echo $total ?>" />
        <span class="help"></span><br/>
        <label>Description</label>
        <textarea name="Payment[description]"><?php echo $payment->description; ?></textarea>
        <span class="help"></span><br/>
    </fieldset>
    <input type="submit" value="Save" id="payment-save"/>
</form>
<script src="<?php echo Configuration::get('base_url') . '/libraries/jquery.js'; ?>"></script>
<script type="text/javascript">
    var VALIDATE_URL = '<?php echo Configuration::get('base_url') . '/xhtml.php/payments/new/validate'; ?>';
    $(document).ready(function() {
        // validate
        $('#payment-save').click(function(event){
            event.preventDefault(); event.stopPropagation();
             var POST = $('#payment-edit').serialize();
            $.post(VALIDATE_URL, POST, function(data, status, xhr){
                if( data.match(/No errors found/i) ){ $('#payment-edit').submit(); }
                else $('#messages').html(data);
            });
        });
    });
</script>
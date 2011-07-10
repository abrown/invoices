<?php
    if( !isset($discount) ){
        $discount = new stdClass();
        $discount->id = 0;
        $discount->type = 'fixed';
        $discount->quantity = 0.0;
    }
?>
<tr class="discount">
    <td></td>
    <td>Discount</td>
    <td></td>
    <td>
        Type: <select name="Invoice[Discount][<?php echo $discount->id; ?>][type]" id="discount-type">
            <option value="fixed" <?php if( $discount->type == 'fixed' ) echo 'selected="selected"'; ?>>$</option>
            <option value="percent" <?php if( $discount->type == 'percent' ) echo 'selected="selected"'; ?>>%</option>
        </select>
    </td>
    <td>
        <?php $quantity = number_format($discount->quantity, 2, '.', ''); ?>
        <input type="text" name="Invoice[Discount][<?php echo $discount->id; ?>][quantity]" value="<?php echo $quantity; ?>" id="discount-quantity"/>
    </td>
    <td>
        <?php $total = number_format($discount->quantity, 2, '.', ','); ?>
        <span id="discount-total"><?php echo $total; ?></span>
    </td>                        
</tr>

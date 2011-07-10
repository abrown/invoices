<?php
    if( !isset($entry) ){
        $entry = new stdClass();
        $entry->id = 0;
        $entry->billed = 0;
        $entry->name = ''; 
        $entry->quantity = 0;
        $entry->amount_per = Configuration::get('default_wage');
        $entry->description = 'Enter a description here...';
    }
?>
<tr class="entry">
    <td>
        <span class="entry-remove">-</span>
    </td>
    <td>
        <?php $billed = ($entry->billed) ? Entry::__getStringFromDateTime($entry->billed) : ''; ?>
        <input type="text" name="Invoice[Entry][<?php echo $entry->id; ?>][billed]" value="<?php echo $billed; ?>" />
    </td>
    <td>
        <input type="text" name="Invoice[Entry][<?php echo $entry->id; ?>][name]" value="<?php echo $entry->name; ?>" />
    </td>
    <td>
        <?php $quantity = number_format($entry->quantity, 2, '.', ''); ?>
        <input type="text" name="Invoice[Entry][<?php echo $entry->id; ?>][quantity]" id="entry-<?php echo $entry->id; ?>-quantity" class="entry-quantity"value="<?php echo $quantity; ?>" />
    </td>
    <td>
        <?php $amount_per = number_format($entry->amount_per, 2, '.', ''); ?>
        <input type="text" name="Invoice[Entry][<?php echo $entry->id; ?>][amount_per]" id="entry-<?php echo $entry->id; ?>-amount_per" value="<?php echo $amount_per; ?>" />
    </td>
    <td>
        <?php $total = number_format($entry->quantity*$entry->amount_per, 2, '.', ','); ?>
        <span id="entry-<?php echo $entry->id; ?>-total"><?php echo $total; ?></span>
    </td>    
</tr>
<tr class="entry-description">
    <td></td>
    <td colspan="4">
        <textarea name="Invoice[Entry][<?php echo $entry->id; ?>][description]"><?php echo $entry->description; ?></textarea>
    </td>
</tr>

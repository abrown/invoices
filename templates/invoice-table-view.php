<table id="entries">
    <tr>
        <th>Date (DDMMYYYY)</th><th>Name</th><th>Quantity</th><th>Unit Price</th><th>Total</th>
    </tr>
    <!-- ENTRIES -->
    <?php if( $invoice->entries ){ foreach ($invoice->entries as $entry): ?>
    <tr class="entry">
        <td>
            <?php echo date('j M Y', strtotime($entry->billed)); ?>
        </td>
        <td>
            <?php echo $entry->name; ?>
        </td>
        <td>
            <?php $quantity = number_format($entry->quantity, 2, '.', ''); ?>
            <?php echo $quantity; ?>
        </td>
        <td>
            <?php $amount_per = number_format($entry->amount_per, 2, '.', ''); ?>
            $<?php echo $amount_per; ?>
        </td>
        <td>
            <?php $total = number_format($entry->quantity*$entry->amount_per, 2, '.', ','); ?>
            $<?php echo $total; ?>
        </td>    
    </tr>
    <tr class="entry-description">
        <td colspan="4">
            <?php echo $entry->description; ?>
        </td>
    </tr>
    <?php endforeach; } ?>
    <!-- DISCOUNT -->
    <?php
    if ($invoice->discounts) { foreach ($invoice->discounts as $discount): ?>
    <tr class="discount">
        <td>Discount (<?php echo $discount->type; ?>)</td>
        <td></td>
        <td></td>
        <td></td>
        <td>$<?php echo number_format($discount->quantity, 2, '.', ','); ?></td>                        
    </tr>
    <?php endforeach; }?>
    <!-- PAYMENTS -->
    <?php
    if ($invoice->payments) { foreach ($invoice->payments as $payment): ?>
    <tr class="payment">
        <td><?php echo date('j M Y', strtotime($payment->billed)); ?></td>
        <td>Payment: <?php echo $payment->type; ?></td>
        <td></td>
        <td></td>
        <td>$<?php echo number_format($payment->total, 2, '.', ','); ?></td>                        
    </tr>
    <tr class="payment-description">
        <td colspan="4">
            <?php echo $payment->description; ?>
        </td>
    </tr>
    <?php endforeach; }?>
    <!-- TOTAL -->
    <tr class="entry-total">
        <td>Total</td>
        <td></td>
        <td></td>
        <td></td>
        <?php $total = number_format($invoice->total, 2); ?>
        <td>$<?php echo $total; ?></td>    
    </tr>
</table>
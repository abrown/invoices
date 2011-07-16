<table id="payments">
    <tr>
        <th>Date (DDMMYYYY)</th><th>Type</th><th>Amount</th>
    </tr>
    <?php if( $invoice->payments ){ foreach($invoice->payments as $payment): ?>
    <tr>
        <td><?php echo date('j M Y', strtotime($payment->billed)); ?></td>
        <td><?php echo $payment->type; ?></td>
        <td>$<?php echo number_format($payment->total, 2); ?></td>
    </tr>
    <?php endforeach; } ?>
</table>
<?php
    $url = Configuration::get('base_url').'/xhtml.php/payments/'.$payment->id.'/view';
?>
Sir or Ma'am,

The following payment was made:

    Client:     <?php echo "{$invoice->client_first_name} {$invoice->client_last_name}"; if( $invoice->company ) echo " ({$invoice->company})"; ?>
    Project:    <?php echo "{$invoice->project}"; ?>
    Total:      $<?php echo number_format($payment->total, 2); ?>

    
For more information, please visit <?php echo $url; ?>.


Thank you,

Array Design Studios

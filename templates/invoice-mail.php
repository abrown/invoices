<?php
    $url = Configuration::get('base_url').'/xhtml.php/invoices/'.$invoice->id.'/view';
?>
Sir or Ma'am,

We have published an invoice with the following details:

    Client:     <?php echo "{$invoice->client_first_name} {$invoice->client_last_name}"; if( $invoice->company ) echo " ({$invoice->company})"; ?>
    Project:    <?php echo "{$invoice->project}"; ?>
    Total:      $<?php echo number_format($invoice->total, 2); ?>

    
For more information, please visit <?php echo $url; ?>.


Thank you,

Array Design Studios

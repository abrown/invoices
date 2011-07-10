<tr>
    <td><?php echo $invoice->id; ?></td>
    <td><?php echo date('j M Y', strtotime($invoice->created)); ?></td>
    <td><?php echo $invoice->client_first_name.' '.$invoice->client_last_name; ?></td>
     <td><?php echo $invoice->project; ?></td>
    <td><?php echo $invoice->status; ?></td>
    <td>$<?php echo number_format($invoice->total, 2); ?></td>
    <?php $url = get_base_url('invoicr').'/xhtml.php/invoices/'.$invoice->id.'/'; ?>
    <td><a href="<?php echo $url.'edit'; ?>">Edit</a><a href="<?php echo $url.'delete'; ?>">Delete</a></td>
</tr>
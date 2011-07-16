<tr>
    <td><?php echo date('j M Y', strtotime($payment->billed)); ?></td>
    <td><?php echo $payment->type; ?></td>
     <td>$<?php echo number_format($payment->total, 2); ?></td>
    <?php 
        $url = Configuration::get('base_url').'/xhtml.php/payments/'.$payment->id.'/'; 
    ?>
    <td>
        <a href="<?php echo $url.'edit'; ?>">Edit</a>
        <a href="<?php echo $url.'view' ?>">View</a>
        <a href="<?php echo $url.'delete'; ?>">Delete</a>
    </td>
</tr>
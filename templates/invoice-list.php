<?php
    // setup
    $Invoice = new Invoice();
    $total = $Invoice->count();
    $items_per_page = 10;
    if( !isset($page) || $page < 0 ) $page = 1;
    $number_of_pages = ceil($total / $items_per_page);
    if( $number_of_pages == 0 ) $number_of_pages = 1;
    $start = ($page - 1) * $items_per_page;
?>
<div class="actions">
    <a href="xhtml.php/invoices/*/enumerate">List Invoices</a>
    <a href="xhtml.php/invoices/*/create">New Invoice</a>
</div>
<table>
    <tr>
        <th>#</th><th>Date</th><th>Client</th><th>Project</th><th>Status</th><th>Amount</th><th> </th>
    </tr>
    <?php
        $list = $Invoice->get_list($start, $items_per_page);
        if( !$list ){
            echo '<tr><td colspan="6">No Invoices Found</td></tr>';
        }
        else{
            foreach($list as $invoice){
                include(Configuration::get('base_dir').DS.'templates'.DS.'invoice-list-item-view.php');
            }
        }
    ?>
</table>
<p class="page-navigation">Pages: 
<?php
    // navigation
    $url = Configuration::get('base_url').'/xhtml.php/invoices/*/enumerate';
    for($i=1; $i<=$number_of_pages; $i++){
        echo "<a href='{$url}?page=$i'>$i</a> ";
    }
?>
</p>
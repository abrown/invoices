<form method="POST" action="<?php echo $action; ?>" id="invoice-create">
    <div id="messages"></div>
    <fieldset>
        <legend>Client</legend>
        <label>First Name</label>
        <input type="text" name="Invoice[client_first_name]" />
        <span class="help"></span><br/>
        <label>Last Name</label>
        <input type="text" name="Invoice[client_last_name]" />
        <span class="help"></span><br/>
        <label>E-mail</label>
        <input type="text" name="Invoice[client_email]" />
        <span class="help"></span><br/>
        <label>Company</label>
        <input type="text" name="Invoice[company]" />
        <span class="help"></span><br/>
    </fieldset>
    <fieldset>
        <legend>Project</legend>
        <label>Name</label>
        <input type="text" name="Invoice[project]" />
        <span class="help"></span><br/>
        <label>Description</label>
        <textarea name="Invoice[description]"></textarea>
        <span class="help"></span><br/>
    </fieldset>
    <fieldset>
        <legend>Items</legend>
        <table id="entries">
            <tr>
                <th></th><th>Date (DDMMYYYY)</th><th>Name</th><th>Quantity</th><th>Unit Price</th><th>Total</th>
            </tr>
            <!-- ENTRIES -->
            <?php
                include(Configuration::get('base_dir') . DS . 'templates' . DS . 'entry-edit.php');
            ?>
            <!-- ADD -->
            <tr class="">
                <td><span id="entry-add">+</span></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>    
            </tr>
            <!-- DISCOUNT -->
            <?php
                include(Configuration::get('base_dir') . DS . 'templates' . DS . 'discount-edit.php');
            ?>
            <!-- TOTAL -->
            <tr class="entry-total">
                <td></td>
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td><span id="total"></span></td>    
            </tr>
        </table>
    </fieldset>
    <input type="submit" value="Save" id="invoice-save"/>
</form>
<script src="<?php echo Configuration::get('base_url') . '/libraries/jquery.js'; ?>"></script>
<script src="<?php echo Configuration::get('base_url') . '/libraries/calculate.js'; ?>"></script>
<script type="text/javascript">
    var ADD_ENTRY_URL = '<?php echo Configuration::get('base_url') . '/ajax.php/entry-edit.php'; ?>'; 
    var VALIDATE_URL = '<?php echo Configuration::get('base_url') . '/xhtml.php/invoices/new/validate'; ?>';
    $(document).ready(function() {
        INVOICR.calculate();
        // add entries
        $('#entry-add').click(function(){
            $.get(ADD_ENTRY_URL, function(data, status, xhr){
                if( xhr.status != 200 ){
                    alert('There was an error with a JQuery AJAX component: '+response+'. Please reload the page.');
                    console.log(xhr);
                }
                else{
                    data = data.replace(/\[0\]/g, '['+INVOICR.next_entry+']');
                    data = data.replace(/-0-/g, '-'+INVOICR.next_entry+'-');
                    $('#entry-add').parent().parent().before(data);
                    INVOICR.next_entry++;
                }
            });
        });
        // remove entries
        $('.entry-remove').live('click', function(){
            $(this).parent().parent().next().remove();
            $(this).parent().parent().remove();
        })
        // calculate total
        $('input, select').live('focus', function(){ INVOICR.calculate(); });
        $('input, select').live('blur', function(){ INVOICR.calculate(); });
        // validate
        $('#invoice-save').click(function(event){
            event.preventDefault(); event.stopPropagation();
             var POST = $('#invoice-create').serialize();
            $.post(VALIDATE_URL, POST, function(data, status, xhr){
                if( data.match(/No errors found/i) ){ $('#invoice-create').submit(); }
                else $('#messages').html(data);
            });
        });
    });
</script>
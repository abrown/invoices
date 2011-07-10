INVOICR = {
    next_entry: 1,
    calculate: function (){
        var total = 0;
        // add entries
        $('.entry-quantity').each(function(i, e){
            var id = $(this).attr('id').match(/\d+/)[0];
            var quantity = INVOICR.getValue( $(this).val() );
            var amount = INVOICR.getValue( $('#entry-'+id+'-amount_per').val() );
            var subtotal = quantity * amount;
            subtotal = INVOICR.getValue(subtotal);
            $('#entry-'+id+'-total').text('$'+subtotal.toFixed(2));
            total += subtotal;
            //console.log(quantity); console.log(amount); console.log($('#entry-'+id+'-amount_per'));
        });
        // add discount
        var discount = 0.0;
        var _discount = INVOICR.getValue( $('#discount-quantity').val() );
        if( $('#discount-type').val() == 'percent' ) discount = total * (_discount/100);
        else if( $('#discount-type').val() == 'fixed' ) discount = _discount;
        discount = INVOICR.getValue(discount);
        $('#discount-total').text('$'+discount.toFixed(2));
        //console.log(discount); console.log( $('#entry-discount-type').val() );
        // display
        total -= discount;
        total = INVOICR.getValue(total)
        $('#total').text('$'+total.toFixed(2));
        //console.log(total);
    },
    getValue: function (string){
        var num = new Number(string);
        num = Math.round(num * 100);
        num = num/100;
        return num;
    }
}
<?php
class PaymentHtml extends AppFormatHtml{
    
    /**
     * Gets base link for HTML forms
     * @return <string> URL
     */
    public function getLink(){
        static $link = null;
        if( $link === null ){
            $link = Configuration::get('base_url').'/xhtml.php/payments';
        }
        return $link;
    }
    
    public function getInvoiceLink(){
        static $link = null;
        if( $link === null ){
            $link = Configuration::get('base_url').'/xhtml.php/invoices';
        }
        return $link;
    }
    
    /**
     * Send formatted error data
     */
    public function error(){
        $error = $this->out;
        // send HTTP header
        if( !headers_sent() ){
            header($_SERVER['SERVER_PROTOCOL'].' '.$error->getCode());
            header('Content-Type: text/html');
        }
        // display error
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // make title
        $title = $error->getCode().' Error';
        $template->replace('title', $title);
        // make body
        $body = '<p><i>Message</i>: '.$error->getMessage().'</p>';
        $body .= '<p><i>Code</i>: '.$error->getCode().'</p>';
        $template->replace('content', $body);
        // display
        $template->display();
    }
    
    protected function enumerateToHtml(){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        $template->replace('title', 'Invoice List');
        // make html
        $template->replaceFromPHPFile('content', Configuration::get('base_dir').DS.'templates'.DS.'invoice-list.php');
        // return
        return $template;
    }
    
    /**
     * Returns HTML version of create call
     * @param <AbstractObject> $instance of class serviced
     * @param <boolean> $created
     * @return Template 
     */
    protected function createToHtml($id){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // options: created or not
        if( $id ){
            // make title
            $title = 'Payment #'.$id.' Created Successfully';
            $template->replace('title', $title);
            // make body
            $body = '<a href="'.$this->getInvoiceLink().'">List All</a> ';
            $template->replace('content', $body);
        }
        else{
            // make title
            $title = 'New '.Routing::getName();
            $template->replace('title', $title);
            $action = $this->getLink().'/new/create';
            $template->replaceFromPHPFile('content', Configuration::get('base_dir').DS.'templates'.DS.'payment-edit.php', array('action'=>$action));            
        }       
        // return
        return $template;
    }
    
    /**
     * 
     * @param array $list 
     */
    protected function editToHtml($payment){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // make title
        $title = 'Edit Payment for Invoice ['.$payment->invoice_id.']';
        $template->replace('title', $title);
        $action = $this->getLink()."/{$payment->id}/update";
        $template->replaceFromPHPFile(
            'content', 
            Configuration::get('base_dir').DS.'templates'.DS.'payment-edit.php', 
            array('action'=>$action, 'payment'=>$payment)
        );            
        // return
        return $template;

    }
    
    /**
     * 
     * @param array $list 
     */
    protected function updateToHtml($payment){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // make title
        $title = 'Payment #'.$payment->id.' Updated Successfully';
        $template->replace('title', $title);
        // make body
        $body = 'Invoice: <a href="'.$this->getInvoiceLink().'">List All</a> ';
        $body .= '<a href="'.$this->getInvoiceILink().'/'.$payment->invoice_id.'/read">View</a> ';
        $body .= '<a href="'.$this->getInvoiceLink().'/'.$payment->invoice_id.'/edit">Edit</a> ';
        $template->replace('content', $body);          
        // return
        return $template;

    }
    
    /**
     * Returns HTML for validate request
     * @param array $errors
     * @return string 
     */
    protected function validateToHtml($errors){
        if( $errors ){
            $html = "<p>We detected the following problems: </p>\n";
            foreach(Set::flatten($errors) as $e){
                $html .= "<p class='error'>$e</p>\n";
            }
        }
        else{
            $html = "<p>No errors found</p>";
        }
        return $html;
    }
    
    /**
     * Displays an invoice in a theme
     * @param Invoice $invoice
     * @return Template 
     */
    protected function viewToHtml($data){
        $invoice = $data['invoice'];
        $payment = $data['payment'];
        $theme = Configuration::get('default_receipt_theme');
        if( !$theme ) throw new Exception('No default theme set', 400);
        $file = Configuration::get('base_dir').DS.'themes'.DS.$theme.'.html';
        if( !is_file($file) ) throw new Exception('Could not find theme: '.$file, 400);
        $invoice_file = Configuration::get('base_dir').DS.'templates'.DS.'invoice-table-view.php';
        $payment_file = Configuration::get('base_dir').DS.'templates'.DS.'payment-table-view.php';
        // make template
        $template = new Template($file);
        $template->token_begin = '[[';
        $template->token_end = ']]';
        $INVOICE = Set::flatten($data);
        $INVOICE['invoice.url'] = Configuration::get('base_url').'/xhtml.php/invoices/'.$invoice->id.'/view'; 
        $INVOICE['invoice.pdf'] = Configuration::get('base_url').'/pdf.php/invoices/'.$invoice->id.'/view'; 
        $INVOICE['payment.url'] = Configuration::get('base_url').'/xhtml.php/payments/'.$payment->id.'/view'; 
        $INVOICE['payment.pdf'] = Configuration::get('base_url').'/pdf.php/payments/'.$payment->id.'/view'; 
        $INVOICE['entry_table'] = $template->getPHPFile($invoice_file, array('invoice'=>$invoice));
        $INVOICE['payment_table'] = $template->getPHPFile($payment_file, array('invoice'=>$invoice));
        foreach($template->findTokens() as $token){
            if( array_key_exists($token, $INVOICE) ) $template->replace($token, $INVOICE[$token]);
        }
        // return
        return $template;
    }
    
    /**
     * Returns HTML for adding a payment
     * @param Payment $payment
     * @return Template 
     */
    protected function deleteToHtml($deleted){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // make title
        $title = 'Payment Deleted Successfully';
        $template->replace('title', $title);
        // make body
        $body = '<a href="'.$this->getInvoiceLink().'">List All</a> ';
        $body .= ($deleted) ? '<p>Deleted successfully.</p>' : '<p>Failed to delete.</p>';
        $template->replace('content', $body);          
        // return
        return $template;
    }
}

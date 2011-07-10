<?php
class InvoiceHtml extends AppFormatHtml{
    
    /**
     * Gets base link for HTML forms
     * @return <string> URL
     */
    public function getLink(){
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
            $title = 'Invoice #'.$id.' Created Successfully';
            $template->replace('title', $title);
            // make body
            $body = '<a href="'.$this->getLink().'">List All</a> ';
            $body .= '<a href="'.$this->getLink().'/'.$id.'/read">View</a> ';
            $body .= '<a href="'.$this->getLink().'/'.$id.'/edit">Edit</a> ';
            $body .= '<a href="'.$this->getLink().'/'.$id.'/delete">Delete</a>';
            $template->replace('content', $body);
        }
        else{
            // make title
            $title = 'New '.Routing::getName();
            $template->replace('title', $title);
            $action = $this->getLink().'/new/create';
            $template->replaceFromPHPFile('content', Configuration::get('base_dir').DS.'templates'.DS.'invoice-create.php', array('action'=>$action));            
        }       
        // return
        return $template;
    }
    
    /**
     * 
     * @param array $list 
     */
    protected function editToHtml($invoice){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // make title
        $title = 'Edit Invoice ['.$invoice->id.']';
        $template->replace('title', $title);
        $action = $this->getLink()."/{$invoice->id}/update";
        $template->replaceFromPHPFile(
            'content', 
            Configuration::get('base_dir').DS.'templates'.DS.'invoice-edit.php', 
            array('action'=>$action, 'invoice'=>$invoice)
        );            
        // return
        return $template;

    }
    
    /**
     * 
     * @param array $list 
     */
    protected function updateToHtml($invoice){
        // get template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
        $template->replace('base_url', Configuration::get('base_url'));
        // make title
        $title = 'Invoice ['.$invoice->id.'] Updated Successfully';
        $template->replace('title', $title);
        // make body
        $body = '<a href="'.$this->getLink().'">List All</a> ';
        $body .= '<a href="'.$this->getLink().'/'.$invoice->id.'/read">View</a> ';
        $body .= '<a href="'.$this->getLink().'/'.$invoice->id.'/edit">Edit</a> ';
        $body .= '<a href="'.$this->getLink().'/'.$invoice->id.'/delete">Delete</a>';
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
}

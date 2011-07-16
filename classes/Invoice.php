<?php
/**
 * Description of Invoice
 *
 * @author andrew
 */
class Invoice extends AppObjectPDO{
    
    public $id;
    public $status;
    public $total;
    public $pdf;
    public $created;
    public $modified;
    public $client_first_name;
    public $client_last_name;
    public $client_email;
    public $project;
    public $company;
    public $description;

    protected $__table = 'invoices';
    protected $__cache = false;
    protected $__foreign = array('entries'=>'invoice_id');
    
    /**
     * Counts number of invoices
     * @return int
     */
    public function count(){
        $query = array(
            'select' => "SELECT COUNT(*)",
            'from' => "FROM `{$this->__table}`"
        );
        // prepare
        $query = $this->toSql($query);
        $sql = $this->getDatabase()->prepare( $query );
        $sql->execute();  
        // return
        $result = $sql->fetch(PDO::FETCH_NUM);
        return $result[0];
    }
    
    /**
     * Lists invoices; adds navigation parameters (start, limit)
     * @return array
     */
    public function get_list($start, $limit){
        $query = array(
            'select' => "SELECT `{$this->__table}`.*",
            'from' => "FROM `{$this->__table}`",
            'limit' => "LIMIT $start, $limit"
        );
        // prepare
        $query = $this->toSql($query);
        $sql = $this->getDatabase()->prepare( $query );
        $sql->execute();     
        // get results
        $results = array();
        while( $row = $sql->fetch(PDO::FETCH_ASSOC) ){
            $r = $this->__bind($row)->toClone();
            $results[] = $r;
        }
        // return
        return $results;
    }
     
    /**
     * Returns total due for this invoice
     * @param type $id 
     */
    public function get_total($invoice = null){
        $total = 0;
        if( !$invoice ) $invoice = $this->read();
        // add entries
        foreach($invoice->entries as $entry){
            $total += $entry->total;
        }
        // subtract discounts
        foreach($invoice->discounts as $discount){
            if( $discount->type == 'percent') $total -= round( $total*($discount->quantity/100), 2);   
            else $total -= $discount->quantity;
        }
        // subtract payments
        if( property_exists($invoice, 'payments') ){
            foreach($invoice->payments as $payment){
                $total -= $payment->total;
            }
        }
        // return
        return $total;
    }
    
    /**
     * Updates total field
     * @return bool
     */
    public function update_total(){
        try{
            $total = $this->get_total();
            $sql = $this->getDatabase()->prepare( "UPDATE `{$this->__table}` SET `total` = :total WHERE `{$this->__primary}` = :_object_identifier" );
            $sql->bindParam( ':total', $total );
            $sql->bindParam( ':_object_identifier', $this->__getID() );
            $sql->execute();
            return true;
        }
        catch(Exception $e){ return false; }
    }
    
    /**
     * Validates POSTed data
     */
    public function validate(){
        $post = Http::getParameter('POST');
        $errors = array();
        // create invoice ruleset
        if( !array_key_exists('Invoice', $post) ){ $errors[] = 'No invoice submitted'; return $errors; }
        $v = new Validation();
        $v->addRule('Invoice.project', Validation::NOT_EMPTY, 'Project name must not be empty');
        $v->addRule('Invoice.total', Validation::IS_NULL, 'Project total must be empty');
        $errors = $v->validateList( Set::flatten($post) );
        // create entry ruleset
        if( !array_key_exists('Entry', $post['Invoice']) ){ $errors[] = 'No billable entries submitted'; return $errors; }
        foreach($post['Invoice']['Entry'] as $i => $entry){
            $v = new Validation();
            // check name
            $v->addRule('name', Validation::NOT_EMPTY, "Entry '$i' must have a name");
            if( !array_key_exists('name', $entry) ) $entry['name'] = '#'.$i;
            // etc...
            $v->addRule('billed', Validation::NOT_EMPTY, "Entry '{$entry['name']}' must not have an empty date");
            $v->addRule('billed', '~\d{8}~', "Entry '{$entry['name']}' must have a valid date");
            $v->addRule('quantity', Validation::NUMERIC, "Entry '{$entry['name']}' must have a valid quantity");
            $v->addRule('amount_per', Validation::NUMERIC, "Entry '{$entry['name']}' must have a valid price");
            // validate
            $errors = array_merge($errors, $v->validateList($entry));
        }
        // create discount ruleset
        foreach($post['Invoice']['Discount'] as $i => $discount){
            $v = new Validation();
            // check type
            $types = array('fixed', 'percent');
            if( !array_key_exists('type', $discount) || !in_array($discount['type'], $types) ) throw new Exception('Discount has no type', 500);
            // check quantity
            $v->addRule('quantity', Validation::NUMERIC, "Discount must have a valid quantity");
            // validate
            $errors = array_merge($errors, $v->validateList($entry));
        }
        // create payment ruleset
        if( array_key_exists('Payment', $post['Invoice']) ){
            foreach($post['Invoice']['Payment'] as $i => $payment){
                // TODO
            }
        }
        // return
        return $errors;        
    }
    
    /**
     * Creates an invoice and related items
     */
    public function create(){
        $post = Http::getParameter('POST');
        if( count($this->validate()) > 0 ) throw new Exception('Invalid data entered', 400);
        // create invoice
        if( $this->id == 'new' ) $this->id = null;
        $this->status = 'Drafted';
        $id = parent::create();
        // create entries
        $this->entries = array();
        foreach($post['Invoice']['Entry'] as $entry){
            $Entry = new Entry();
            $Entry->__bind($entry);
            $Entry->invoice_id = $id;
            $Entry->billed = Entry::__getDateTimeFromString($Entry->billed);
            if( !array_key_exists('total', $entry) ) $Entry->total = round($entry['quantity']*$entry['amount_per'], 2);
            $Entry->create();
            $this->entries[] = $Entry;
        }
        // create discounts
        $this->discounts = array();
        foreach($post['Invoice']['Discount'] as $discount){
            $Discount = new Discount();
            $Discount->__bind($discount);
            $Discount->invoice_id = $id;
            $Discount->create();
            $this->discounts[] = $Discount;
        }
        // update total
        $this->update_total();
        // update cache, see libraries/misc.php
        delete_cache_entry('invoices', $id);
        // return
        return $id;
    }
    
    /**
     * Returns viewable data
     * @return object 
     */
    public function read(){
        parent::read();
        $out = $this->toClone();
        // get entries
        $Entry = new Entry();
        $out->entries = $Entry->get_from_id($this->id);
        // get discounts
        $Discount = new Discount();
        $out->discounts = $Discount->get_from_id($this->id);
        // get payments
        $Payment = new Payment();
        $out->payments = $Payment->get_from_id($this->id);
        // get total
        $out->total = $this->get_total($out);
        // return
        return $out;
    }
    
    /**
     * Placeholder for the 'Edit' view
     * @return Invoice 
     */
    public function edit(){
        return $this->read();
    }
    
    /**
     * Updates records, uses 'create' functionality
     * @return Invoice 
     */
    public function update(){
        $this->delete( false );
        $this->create();
        return $this->toClone();
    }
    
    /**
     * Deletes an invoice and its associations
     * @return bool 
     */
    public function delete($delete_payments = true){
        // get entries
        $Entry = new Entry();
        $Entry->delete_from_id($this->id);
        // get discounts
        $Discount = new Discount();
        $Discount->delete_from_id($this->id);
        // get payments
        if( $delete_payments ){
            $Payment = new Payment();
            $Payment->delete_from_id($this->id);
        }
        // update cache, see libraries/misc.php
        delete_cache_entry('invoices', $id);
        // get total
        return parent::delete();
    }
    
    /**
     * Publishes an invoice
     */
    public function publish(){
        // update status field
        try{
            $status = "Published";
            $sql = $this->getDatabase()->prepare( "UPDATE `{$this->__table}` SET `status` = :status WHERE `{$this->__primary}` = :_object_identifier" );
            $sql->bindParam( ':status', $status );
            $sql->bindParam( ':_object_identifier', $this->__getID() );
            $sql->execute();
        }
        catch(Exception $e){ pr($sql->errorInfo()); }
        // send mail
        $this->mail();
        // return
        return $this->toClone();
    }
    
    /**
     * Sends published invoice to client
     * @return bool 
     */
    public function mail(){
        $this->read();
        // make e-mail template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'mail.php');
        $template->replaceFromPHPFile(
            'content', 
            Configuration::get('base_dir').DS.'templates'.DS.'invoice-list.php',
            array('invoice'=>$this->toClone())
        );
        // send e-mail
        $config = Configuration::getInstance();
        $header = "From: {$config['user']['name']} <{$config['user']['email']}>";
        return mail($this->client_email, "An invoice has been published", $template->toString(), $header);
    }
    
    /**
     * Displays an invoice using a theme
     * @return Invoice 
     */
    public function view(){
        return $this->read();
    }
}
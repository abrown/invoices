<?php
/**
 * Entry
 *
 * @author andrew
 */
class Payment extends AppObjectPDO{
    
    public $id;
    public $invoice_id;
    public $type;
    public $description;
    public $total;
    public $billed;
    public $created;
    public $modified;
    
    protected $__table = 'payments';
    protected $__cache = false;
    
    /**
     * Returns payments related to an Invoice ID
     * @param int $invoice_id 
     * @return array
     */
    public function get_from_id($invoice_id){
        $query = array(
            'select' => "SELECT `{$this->__table}`.*",
            'from' => "FROM `{$this->__table}`",
            'where' => "WHERE `{$this->__table}`.`invoice_id` = ?"
        );
        $query = $this->toSql($query);
        // prepare statement
        $sql = $this->getDatabase()->prepare( $query );
        $sql->bindParam(1, $invoice_id);
        $sql->execute();
        // get result
        $results = array();
        while( $row = $sql->fetch(PDO::FETCH_ASSOC) ){
            $results[] = $this->__bind($row)->toClone();
        }
        // return
        return $results;
    }
    
    /**
     * Deletes payments related to an Invoice ID
     * @param int $invoice_id 
     * @return bool
     */
    public function delete_from_id($invoice_id){
        $sql = $this->getDatabase()->prepare( "DELETE FROM `{$this->__table}` WHERE `invoice_id` = ?" );
        $sql->bindParam(1, $invoice_id);
        $sql->execute();
        return true;
    }
    
    /**
     * Sends receipt
     * @return bool 
     */
    public function mail(){
        $this->read();
        $invoice = new Invoice($this->invoice_id);
        // make e-mail template
        $template = new Template(Configuration::get('base_dir').DS.'templates'.DS.'mail.php');
        $template->replaceFromPHPFile(
            'content', 
            Configuration::get('base_dir').DS.'templates'.DS.'payment-mail.php',
            array('invoice'=>$invoice->read(), 'payment'=>$this->toClone())
        );
        // send e-mail
        $config = Configuration::getInstance();
        $header = "From: {$config['user']['name']} <{$config['user']['email']}>";
        return mail($invoice->client_email, "A payment was made", $template->toString(), $header);
    }
    
    /**
     * Validates POSTed data
     * @return array List of errors in the POSTed data
     */
    public function validate(){
        $post = Http::getParameter('POST');
        $errors = array();
        // create payment ruleset
        if( !array_key_exists('Payment', $post) ){ $errors[] = 'No payment submitted'; return $errors; }
        $v = new Validation();
        $v->addRule('Payment.invoice_id', Validation::NOT_EMPTY, 'Invoice ID must not be empty');
        $v->addRule('Payment.invoice_id', Validation::NUMERIC, 'Invoice ID must be numeric');
        $v->addRule('Payment.billed', Validation::NOT_EMPTY, "Payment date must not be empty");
        $v->addRule('Payment.billed', '~\d{8}~', "Payment date must have a valid date");
        $v->addRule('Payment.type', Validation::NOT_EMPTY, 'Payment type must not be empty');
        $v->addRule('Payment.total', Validation::NOT_EMPTY, 'Payment total must not be empty');
        $v->addRule('Payment.total', Validation::NUMERIC, 'Payment total must be numeric');
        $errors = $v->validateList( Set::flatten($post) );
        // return
        return $errors;        
    }
    
    /**
     * Creates a payment
     * @return int 
     */
    public function create(){
        $post = Http::getParameter('POST');
        if( count($this->validate()) > 0 ) throw new Exception('Invalid data entered', 400); // should have been handled by AJAX
        // create invoice
        if( $this->id == 'new' ) $this->id = null;
        $this->billed = Entry::__getDateTimeFromString($this->billed);
        $this->description = trim($this->description);
        $id = parent::create();
        // recalculate invoice total
        $invoice = new Invoice($this->invoice_id);
        $invoice->update_total();
        // update cache, see libraries/misc.php
        delete_cache_entry('payments', $id);
        // send payment receipt
        $this->mail();
        // return
        return $id;
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
     * Displays a payment receipt
     * @return array 
     */
    public function view(){
        $this->read();
        $invoice = new Invoice($this->invoice_id);
        return array('payment'=>$this->toClone(), 'invoice'=>$invoice->read());
    }
    
    /**
     * Deletes a payment record; updates cache
     * @return bool 
     */
    public function delete(){
        $this->read();
        $id = $this->id;
        $invoice_id = $this->invoice_id;
        // delete
        parent::delete();
        // recalculate invoice total
        $invoice = new Invoice($invoice_id);
        $invoice->update_total();
        // update cache, see libraries/misc.php
        delete_cache_entry('payments', $id);
        // return
        return true;
    }
}
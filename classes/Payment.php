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
    public $quantity;
    public $total;
    public $billed;
    public $created;
    public $modified;
    
    protected $__table = 'payments';
    protected $__cache = false;
    
    /**
     * Returns 
     * @param type $invoice_id 
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
     * Deletes payments related to an invoice ID
     * @param type $invoice_id 
     */
    public function delete_from_id($invoice_id){
        $sql = $this->getDatabase()->prepare( "DELETE FROM `{$this->__table}` WHERE `invoice_id` = ?" );
        $sql->bindParam(1, $invoice_id);
        $sql->execute();
        return true;
    }
}
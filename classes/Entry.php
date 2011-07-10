<?php
/**
 * Entry
 *
 * @author andrew
 */
class Entry extends AppObjectPDO{
    
    public $id;
    public $invoice_id;
    public $name;
    public $description;
    public $type;
    public $quantity;
    public $amount_per;
    public $total;
    public $billed;
    public $created;
    public $modified;
    
    protected $__table = 'entries';
    protected $__cache = false;
    
    /**
     * Returns entries related to an Invoice ID
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
     * Deletes entries related to an invoice ID
     * @param type $invoice_id 
     */
    public function delete_from_id($invoice_id){
        $sql = $this->getDatabase()->prepare( "DELETE FROM `{$this->__table}` WHERE `invoice_id` = ?" );
        $sql->bindParam(1, $invoice_id);
        $sql->execute();
        return true;
    }
    
    /**
     * Returns date from a DDMMYYYY formatted string
     * @param string $string 
     * @return string
     */
    static public function __getDateTimeFromString($string){
        $day = intval( substr($string, 0, 2) );
        $month = intval( substr($string, 2, 2) );
        $year = intval( substr($string, 4) );
        $time = mktime(0, 0, 0, $month, $day, $year);
        return date('Y-m-d H:i:s', $time);
    }
    
    /**
     * Returns date from a DDMMYYYY formatted string
     * @param string $string 
     * @return string
     */
    static public function __getStringFromDateTime($string){
        $time = strtotime($string);
        return date('dmY', $time);
    }
}


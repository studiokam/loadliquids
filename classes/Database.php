<?php 

class Database{
    public $isConn;
    protected $datab;
    private 
    $_count = 0,
    $_lastInsertedId = 0;
    
    // connect to db

    public function __construct(){
        $this->isConn = TRUE;
        try {
            $this->datab = new PDO(
                'mysql:host=' . Config::get('mysql/host') . '; 
                dbname=' . Config::get('mysql/db'), 
                Config::get('mysql/username'), 
                Config::get('mysql/password'));
            $this->datab->query("SET NAMES utf8");
            $this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        
    }
    
    // disconnect from db
    public function Disconnect(){
        $this->datab = NULL;
        $this->isConn = FALSE;
    }
    // get row
    public function getRow($query, $params = []){
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
    // get rows
    public function getRows($query, $params = []){
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            $this->_count = $stmt->rowCount();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
    // count rows
    public function countRows(){
        return $this->_count;
    }
    // insert row
    public function insertRow($query, $params = []){
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return TRUE;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
     // insert row
    public function insertRowWithLastId($query, $params = []){
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            $lastId = $this->datab->lastInsertId();
            return $lastId;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    // update row
    public function updateRow($query, $params = []){
        $this->insertRow($query, $params);
    }
    // delete row
    public function deleteRow($query, $params = []){
        $this->insertRow($query, $params);
    }

    // delete user
    public function deleteRowUser($id) {

        // begin transaction
        $this->datab->beginTransaction();

        try {

            // delete user from users
            $stmt1 = $this->datab->prepare("DELETE FROM users WHERE id = ?");
            $stmt1->bindParam(1, $id, PDO::PARAM_STR);
            $stmt1->execute();

            // delete user from premium
            $stmt2 = $this->datab->prepare("DELETE FROM premium WHERE user_id = ?");
            $stmt2->bindParam(1, $id, PDO::PARAM_STR);
            $stmt2->execute();

            // delete user from orders
            $stmt3 = $this->datab->prepare("DELETE FROM orders WHERE user_id = ?");
            $stmt3->bindParam(1, $id, PDO::PARAM_STR);
            $stmt3->execute();

            // delete user from car_orders
            $stmt4 = $this->datab->prepare("DELETE FROM car_orders WHERE user_id = ?");
            $stmt4->bindParam(1, $id, PDO::PARAM_STR);
            $stmt4->execute();

            // delete user from load_orders
            $stmt5 = $this->datab->prepare("DELETE FROM load_orders WHERE user_id = ?");
            $stmt5->bindParam(1, $id, PDO::PARAM_STR);
            $stmt5->execute();

            // delete user from logs
            $stmt6 = $this->datab->prepare("DELETE FROM logs WHERE user_id = ?");
            $stmt6->bindParam(1, $id, PDO::PARAM_STR);
            $stmt6->execute();

            // delete user from company
            $stmt7 = $this->datab->prepare("DELETE FROM company WHERE user_id = ?");
            $stmt7->bindParam(1, $id, PDO::PARAM_STR);
            $stmt7->execute();


            // commit transaction
            if($this->datab->commit()) {
                Session::flash('deleted', 'Poprawnie usunięto użytkownika.');
                Redirect::to('admin-user-list.php');
            }

            

        } // any errors from the above database queries will be catched
        catch (PDOException $e) {
            // roll back transaction
            $this->datab->rollback();
            echo "blad<br>".$e;
            exit;
        }
    }

    // insert row in to load orders
    public function insertRowLoadOrders($params = []){

        $query = "INSERT INTO load_orders(

        user_id,
        last_edit_date, 
        active, 

        in_country_id, 
        in_date, 
        in_hours, 
        in_company, 
        in_address, 
        in_city, 
        in_post, 
        in_phone, 
        in_email, 
        in_contact_person, 

        out_country_id, 
        out_date, 
        out_hours, 
        out_company, 
        out_address, 
        out_city, 
        out_post, 
        out_phone, 
        out_email, 
        out_contact_person, 

        display_to_date, 
        display_to_hours, 
        price, 
        currency_id, 
        car_type_id, 
        tonnage, 
        load_is, 
        compressor, 
        pump, 
        adr, 
        gps, 
        ready_to_ride, 
        note
        ) 
        VALUE(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);

            $this->_lastInsertedId = $this->datab->lastInsertId();
            return TRUE;
            
            // $lastId = $this->datab->lastInsertId();
            // return $lastId;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }


    // insert row in to load orders
    public function insertRowCarOrders($params = []){

        $query = "INSERT INTO car_orders(

        user_id,
        active, 

        in_country_id, 
        in_date,
        in_city, 
        in_post,

        out_country_id, 
        out_date,
        out_city, 
        out_post,

        display_to_date, 
        display_to_hours, 
        price, 
        currency_id, 
        car_type_id,
        car_details,
        tonnage, 
        compressor, 
        pump, 
        adr, 
        gps, 
        ready_to_ride, 
        note
        ) 
        VALUE(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
        try {

            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            
            $this->_lastInsertedId = $this->datab->lastInsertId();
            return TRUE;
            // $lastId = $this->datab->lastInsertId();
            // return $lastId;

        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function lastInsertedId() {
        return $this->_lastInsertedId;
    }

}

?>
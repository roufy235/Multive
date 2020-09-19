<?php
class DB {
    public array $response = array();
    public PDO $conn;
    protected array $access = array();
    protected string $_emptyJson, $_todayDate;

    public function __construct () {
        global $GLOBALS;
        $this->access = $GLOBALS;
        $this->response['status'] = false;
        $this->response['statusStr'] = '';
        $this->_todayDate = Date('Y-m-d H:i:s');
        $this->_emptyJson = json_decode('{}', true, 512, JSON_THROW_ON_ERROR);
        try {
            $this->conn = new PDO('mysql:dbname='.$this->access['dbName'].';host=localhost', $this->access['user'], $this->access['password']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo 'connected';
        } catch (PDOException $error) {
            //echo $error->getMessage();
        }
    }

    public function verifyUser(string $userId, string $token) : bool {
        $verifyStmt = $this->conn->prepare('');
        $verifyStmt->bindParam(':userId', $userId);
        $verifyStmt->bindParam(':token', $token);
        $verifyStmt->execute();
        return $verifyStmt->rowCount() > 0;
    }

}

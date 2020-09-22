<?php
use PHPMailer\PHPMailer\PHPMailer;
require_once 'access_file.php';
require_once 'SQL.php';
require_once 'EMAIL_TEMPLATE_HOLDER.php';
class DB {
    public array $response = array();
    public PDO $conn;
    protected array $_emptyJson, $access = array();
    protected string $_todayDate;

    public function __construct () {
        global $GLOBALS;
        $this->access = $GLOBALS;
        $this->response['status'] = false;
        $this->response['statusStr'] = '';
        $this->_todayDate = Date('Y-m-d H:i:s');
        /** @noinspection PhpUnhandledExceptionInspection */
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

    protected function validFileExtensionsFunc() : array {
        return ['jpg', 'jpeg', 'png', 'pdf'];
    }

    protected function multiveIsEmailValid($email): bool {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /** @noinspection DuplicatedCode */
    public function multiveFormatTime($timestamp) : string {
        $datetime1 = new DateTime('now');
        $datetime2 = date_create($timestamp);
        $diff = date_diff($datetime1, $datetime2);
        $timeMsg = '';
        if ($diff->y > 0) {
            $timeMsg = $diff->y .' year'. ($diff->y > 1?"s ":' '). $diff->m . ' month'. ($diff->m > 1?"s ":' '). $diff->d .' day'. ($diff->d > 1?"'s":'');
        } else if($diff->m > 0){
            $timeMsg = $diff->m . ' month'. ($diff->m > 1?"s ":' '). $diff->d .' day'. ($diff->d > 1?"'s":'');
        } else if($diff->d > 0){
            $timeMsg = $diff->d .' day'. ($diff->d > 1?"s ":' '). $diff->h .' hour'.($diff->h > 1 ? "'s":'');
        } else if($diff->h > 0){
            $timeMsg = $diff->h .' hour'.($diff->h > 1 ? "s ":' '). $diff->i .' minute'. ($diff->i > 1?"'s":'');
        } else if($diff->i > 0){
            $timeMsg = $diff->i .' minute'. ($diff->i > 1?"s ":' '). $diff->s .' second'. ($diff->s > 1?"'s":'');
        } else if($diff->s > 0){
            $timeMsg = $diff->s .' second'. ($diff->s > 1?"'s":'');
        }
        $timeMsg .= ' ago';
        return $timeMsg;
    }

    protected function multiveGenerateToken (int $length): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            try {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            } catch (Exception $e) {}
        }
        return $randomString;
    }

    public function multiveMailer($email, $name, $message, string $subject, string $senderEmail) : array {
        $mail = new PHPMailer(true);
        $mail->ClearAddresses();  // each AddAddress add to list
        $mail->ClearCCs();
        $mail->ClearBCCs();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->access['phpmailerHost'];
        $mail->Port = $this->access['phpmailerPort'];
        $mail->isHTML(true);
        $mail->Username = $this->access['phpmailerUsername'];
        $mail->Password = $this->access['phpmailerPassword'];
        try {
            $mail->addReplyTo($senderEmail, $name);
            $mail->setFrom($senderEmail, $name);
            $mail->Subject = $subject;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->Body = $message;
            $mail->addAddress($email, "Admin");
            $mail->send();
            $this->response['status'] = true;
        } catch (Exception $e) {
            $this->response['statusStr'] = $e->getMessage();
            $this->response['status'] = false;
        }
        return $this->response;
    }

}

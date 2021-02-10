<?php
class REGISTRATION extends DB {

    public function helloWorld() : array {
        $this->response['statusStr'] = 'Hello World!';
        $this->response['status'] = true;
        return $this->response;
    }

}

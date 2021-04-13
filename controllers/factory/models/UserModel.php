<?php


namespace MultiveLogger\models;


class UserModel {
    public int $userId;
    public function __construct() {
        $this->userId = 0;
    }
    public function getUserId(): int {
        return $this->userId;
    }
    public function setName(int $userId): void {
        $this->userId = $userId;
    }
}

<?php

use ReallySimpleJWT\Exception\ValidateException;
use ReallySimpleJWT\Token;

class TokenGenerator {

    private static function secretKey() : string {
        return $_ENV['TOKEN_SECRET'];
    }

    private static function months(int $numMonth) : int {
        $oneMonth = 2678400;
        return ($numMonth > 0 ?  ($oneMonth * $numMonth) : ($oneMonth * 1));
    }

    public static function validateToken(string $token) : bool {
        return Token::validate($token, self::secretKey());
    }

    public static function getHeaders(string $token) : array {
        $isValid = Token::validate($token, self::secretKey());
        if ($isValid) {
            return Token::getHeader($token, self::secretKey());
        }
        return [];
    }

    public static function getPayload(string $token) : array {
        $isValid = Token::validate($token, self::secretKey());
        if ($isValid) {
            return Token::getPayload($token, self::secretKey());
        }
        return [];
    }

    public static function getBearerToken(string $authorization) : string {
        return str_replace('Bearer ', '', $authorization);
    }

    public static function createToken(array $payload) : string {
        $payload = [
            'iat' => time(),
            'uid' => 1,
            'exp' => time() + self::months(12),
            'iss' => 'localhost',
            'data' => $payload
        ];
        try {
            return Token::customPayload($payload, self::secretKey());
        } catch (ValidateException $e) {}
        return '';
    }

}

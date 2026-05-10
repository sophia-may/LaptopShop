<?php
require_once __DIR__ . '/../config/constants.php';

class JwtHelper {
    private static string $secret;
    
    public static function init(): void {
        self::$secret = JWT_SECRET;
    }
    
    public static function generate(array $payload): string {
        $header = self::base64url_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRE;
        $body = self::base64url_encode(json_encode($payload));
        $sig = self::base64url_encode(hash_hmac('sha256', "$header.$body", self::$secret, true));
        return "$header.$body.$sig";
    }
    
    public static function verify(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        
        [$header, $body, $sig] = $parts;
        $expected = self::base64url_encode(hash_hmac('sha256', "$header.$body", self::$secret, true));
        if (!hash_equals($expected, $sig)) return null;
        $payload = json_decode(self::base64url_decode($body), true);
        if ($payload['exp'] < time()) return null;
        return $payload;
    }

    public static function getBearerToken(): ?string {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    
    private static function base64url_encode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    private static function base64url_decode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}

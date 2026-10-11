<?php
declare(strict_types=1);
final class Database {
    private static ?PDO $connection = null;
    public static function connection(): PDO {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $name = getenv('DB_NAME') ?: 'laboratoire_dentaire';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            self::$connection = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
        }
        return self::$connection;
    }
}
try { $pdo=Database::connection(); } catch (PDOException $e) { http_response_code(503); error_log('DentalLink database connection failed: '.$e->getMessage()); exit('Connexion à la base de données indisponible. Vérifiez la configuration MySQL.'); }

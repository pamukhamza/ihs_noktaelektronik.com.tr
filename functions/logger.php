<?php
class Logger {
    private static $logFile = '../logs/error.log';
    private static $maxFileSize = 10485760; // 10MB

    public static function log($message, $type = 'INFO', $context = []) {
        // Log dizini yoksa oluştur
        $logDir = dirname(self::$logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Log dosyası boyutunu kontrol et
        if (file_exists(self::$logFile) && filesize(self::$logFile) > self::$maxFileSize) {
            // Eski log dosyasını yedekle
            $backupFile = self::$logFile . '.' . date('Y-m-d_H-i-s');
            rename(self::$logFile, $backupFile);
        }

        // Log mesajını oluştur
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user = isset($_SESSION['id']) ? $_SESSION['id'] : 'GUEST';
        $url = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
        
        $logMessage = sprintf(
            "[%s] [%s] [IP: %s] [User: %s] [URL: %s] %s",
            $timestamp,
            $type,
            $ip,
            $user,
            $url,
            $message
        );

        // Context varsa ekle
        if (!empty($context)) {
            $logMessage .= " [Context: " . json_encode($context) . "]";
        }

        // Log dosyasına yaz
        error_log($logMessage . PHP_EOL, 3, self::$logFile);
    }

    public static function error($message, $context = []) {
        self::log($message, 'ERROR', $context);
    }

    public static function info($message, $context = []) {
        self::log($message, 'INFO', $context);
    }

    public static function warning($message, $context = []) {
        self::log($message, 'WARNING', $context);
    }
} 
<?php

class Db
{
    private static ?PDO $pdo = null;

    public static function conn(): PDO
    {
        if (self::$pdo === null) {
            $path = __DIR__ . '/../database/database.sqlite';
            $fresh = !file_exists($path);
            if ($fresh) {
                touch($path);
            }
            self::$pdo = new PDO('sqlite:' . $path);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
            if ($fresh) {
                self::migrate();
                self::seed();
            }
        }
        return self::$pdo;
    }

    public static function migrate(): void
    {
        $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
        self::$pdo->exec($sql);
    }

    public static function seed(): void
    {
        $stmt = self::$pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Administrator', 'admin@hotel.test', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
        $stmt->execute(['Front Desk', 'staff@hotel.test', password_hash('staff123', PASSWORD_DEFAULT), 'staff']);

        $rooms = [
            ['101', 'single',  60,  'available'],
            ['102', 'single',  60,  'available'],
            ['201', 'double',  95,  'available'],
            ['202', 'double',  95,  'maintenance'],
            ['301', 'suite',   180, 'available'],
            ['401', 'deluxe',  260, 'available'],
        ];
        $r = self::$pdo->prepare('INSERT INTO rooms (number, type, rate, status, description) VALUES (?, ?, ?, ?, ?)');
        foreach ($rooms as $row) {
            $r->execute([$row[0], $row[1], $row[2], $row[3], ucfirst($row[1]) . ' room ' . $row[0]]);
        }

        $c = self::$pdo->prepare('INSERT INTO clients (name, email, phone, id_number, address) VALUES (?, ?, ?, ?, ?)');
        $c->execute(['Jane Doe', 'jane@example.com', '+1 555 0100', 'ID-9001', '12 Maple St']);
        $c->execute(['John Smith', 'john@example.com', '+1 555 0200', 'ID-9002', '4 Oak Ave']);
    }

    public static function q(string $sql, array $params = []): PDOStatement
    {
        $st = self::conn()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::q($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::q($sql, $params)->fetchAll();
    }

    public static function scalar(string $sql, array $params = [])
    {
        $st = self::q($sql, $params);
        $row = $st->fetch(PDO::FETCH_NUM);
        return $row[0] ?? null;
    }
}

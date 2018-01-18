<?php

$pdo = new \PDO('sqlite:data/sqlite.db');
$pdo->exec('CREATE TABLE IF NOT EXISTS test
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(200) DEFAULT "",
  email VARCHAR(200) NOT NULL
)');
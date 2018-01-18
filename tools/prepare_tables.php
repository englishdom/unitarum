<?php

$pdo = new \PDO('sqlite:data/sqlite.db');
$pdo->exec('CREATE TABLE IF NOT EXISTS test_users
(
  id integer primary key autoincrement,
  name VARCHAR(200) DEFAULT "",
  email VARCHAR(200) NOT NULL
);

CREATE TABLE IF NOT EXISTS test_roles
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  role VARCHAR(20) NOT NULL
);');
<?php

$pdo = new \PDO('sqlite:data/sqlite.db');
$pdo->exec('CREATE TABLE IF NOT EXISTS test_users
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(200) DEFAULT "",
  email VARCHAR(200) NOT NULL,
  md5_hash VARCHAR(200) DEFAULT ""
);

CREATE UNIQUE INDEX test_users_email_uindex ON test_users (email);

CREATE TABLE IF NOT EXISTS test_roles
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  role VARCHAR(20) NOT NULL
);');
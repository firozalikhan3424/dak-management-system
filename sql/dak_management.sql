CREATE DATABASE IF NOT EXISTS dak_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dak_system;

CREATE TABLE IF NOT EXISTS branches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  branch_name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  status TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS sub_branches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NOT NULL,
  sub_branch_name VARCHAR(100) NOT NULL,
  file_start INT NOT NULL,
  file_end INT NOT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','dispatcher','head_clerk','branch_clerk','officer','co') NOT NULL,
  branch_id INT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE IF NOT EXISTS dak_number_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prefix VARCHAR(20) NOT NULL DEFAULT 'DAK',
  year INT NOT NULL,
  sequence_length INT NOT NULL DEFAULT 4,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS dak_master (
  id INT AUTO_INCREMENT PRIMARY KEY,
  control_no VARCHAR(30) NOT NULL UNIQUE,
  letter_no VARCHAR(100) NOT NULL,
  letter_date DATE NOT NULL,
  originator VARCHAR(200) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  security_class ENUM('Unclassified','Confidential','Secret') NOT NULL,
  receipt_date DATE NOT NULL,
  receipt_mode ENUM('Post','By Hand') NOT NULL,
  ihq TINYINT(1) NOT NULL DEFAULT 0,
  dak_type ENUM('Normal','Priority') NULL,
  branch_id INT NULL,
  sub_branch_id INT NULL,
  speak_case TINYINT(1) NOT NULL DEFAULT 0,
  cutoff_date DATE NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (branch_id) REFERENCES branches(id),
  FOREIGN KEY (sub_branch_id) REFERENCES sub_branches(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS dak_action (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dak_id INT NOT NULL,
  file_no INT NOT NULL,
  action_taken TEXT NOT NULL,
  action_date DATE NOT NULL,
  reply_ref VARCHAR(120) NULL,
  remarks TEXT NULL,
  updated_by INT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (dak_id) REFERENCES dak_master(id),
  FOREIGN KEY (updated_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS dak_references (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dak_id INT NOT NULL,
  reference_no VARCHAR(120) NOT NULL,
  FOREIGN KEY (dak_id) REFERENCES dak_master(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  module VARCHAR(100) NOT NULL,
  action VARCHAR(100) NOT NULL,
  old_value TEXT NULL,
  new_value TEXT NULL,
  ip_address VARCHAR(45) NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO branches (branch_name, description, status) VALUES
('A Branch','Administration',1),('Q Branch','Quartermaster',1),('PRI','Public Relations',1),('Docu Cell','Documentation',1);

INSERT INTO sub_branches (branch_id, sub_branch_name, file_start, file_end, status) VALUES
(1,'A1',1000,1200,1),(1,'A2',1201,1400,1),(1,'A3',1401,1600,1),(2,'Q1',2000,2200,1);

INSERT INTO dak_number_settings (prefix, year, sequence_length) VALUES ('DAK', 2026, 4);

INSERT INTO users (name, username, password, role, branch_id, status) VALUES
('System Admin','admin','$2y$12$0BmreDFt35zYB.FUdcvQ5OjW2EP9i//JgjiwN1yS4QeSb5AWPaY/6','admin',NULL,1),
('Dispatch Clerk','dispatcher1','$2y$12$0BmreDFt35zYB.FUdcvQ5OjW2EP9i//JgjiwN1yS4QeSb5AWPaY/6','dispatcher',NULL,1),
('Head Clerk','headclerk1','$2y$12$0BmreDFt35zYB.FUdcvQ5OjW2EP9i//JgjiwN1yS4QeSb5AWPaY/6','head_clerk',NULL,1),
('Branch Clerk A','brancha1','$2y$12$0BmreDFt35zYB.FUdcvQ5OjW2EP9i//JgjiwN1yS4QeSb5AWPaY/6','branch_clerk',1,1),
('CO User','co1','$2y$12$0BmreDFt35zYB.FUdcvQ5OjW2EP9i//JgjiwN1yS4QeSb5AWPaY/6','co',NULL,1);

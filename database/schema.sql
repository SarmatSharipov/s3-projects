-- database/schema.sql: Модели физикии EDMS
DROP DATABASE IF EXISTS edms;
CREATE DATABASE edms
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edms;
SET NAMES utf8mb4;

-- 1. Шӯъбаҳо
CREATE TABLE departments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

-- 2. Корбарон
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  login VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('chancellery','director','dept_head','employee','admin') NOT NULL,
  department_id INT UNSIGNED NULL,
  CONSTRAINT fk_users_department FOREIGN KEY (department_id) 
    REFERENCES departments(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Ҳуҷҷатҳо
CREATE TABLE documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  number VARCHAR(50) NOT NULL,
  doc_date DATE NOT NULL,
  subject VARCHAR(255) NOT NULL,
  type ENUM('incoming','outgoing','internal') NOT NULL,
  importance ENUM('low','normal','high') NOT NULL DEFAULT 'normal',
  sender VARCHAR(150) NULL,
  receiver VARCHAR(150) NULL,
  scan_file VARCHAR(255) NULL,
  status ENUM('registered','review','in_progress','approved','rejected','done') 
         NOT NULL DEFAULT 'registered',
  created_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_documents_creator FOREIGN KEY (created_by) 
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_docs_number (number), 
  INDEX idx_docs_date (doc_date), 
  INDEX idx_docs_type (type)
) ENGINE=InnoDB;

-- 4. Резолюцияҳо
CREATE TABLE resolutions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_id INT UNSIGNED NOT NULL,
  director_id INT UNSIGNED NOT NULL,
  text TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_resolutions_doc FOREIGN KEY (document_id) 
    REFERENCES documents(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_resolutions_director FOREIGN KEY (director_id) 
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Супоришҳо (Tasks)
CREATE TABLE tasks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_id INT UNSIGNED NOT NULL,
  resolution_id INT UNSIGNED NULL,
  executor_id INT UNSIGNED NOT NULL,
  assigned_by INT UNSIGNED NOT NULL,
  deadline DATETIME NOT NULL,
  status ENUM('new','in_progress','done') NOT NULL DEFAULT 'new',
  report TEXT NULL,
  report_file VARCHAR(255) NULL,
  done_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tasks_doc FOREIGN KEY (document_id) 
    REFERENCES documents(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tasks_res FOREIGN KEY (resolution_id) 
    REFERENCES resolutions(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_tasks_executor FOREIGN KEY (executor_id) 
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_tasks_assigned FOREIGN KEY (assigned_by) 
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_tasks_deadline (deadline), 
  INDEX idx_tasks_status (status)
) ENGINE=InnoDB;

-- 6. Мувофиқаҳо (Approvals)
CREATE TABLE approvals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  step_order INT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  comment TEXT NULL,
  decided_at DATETIME NULL,
  CONSTRAINT fk_approvals_doc FOREIGN KEY (document_id) 
    REFERENCES documents(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_approvals_user FOREIGN KEY (user_id) 
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
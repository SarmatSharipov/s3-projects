CREATE DATABASE IF NOT EXISTS edms
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edms;

CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  login VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('chancellery','director','dept_head','employee','admin') NOT NULL,
  department_id INT NULL,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
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
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX (number), INDEX (doc_date), INDEX (type)
) ENGINE=InnoDB;

CREATE TABLE resolutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document_id INT NOT NULL,
  director_id INT NOT NULL,
  text TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
  FOREIGN KEY (director_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document_id INT NOT NULL,
  resolution_id INT NULL,
  executor_id INT NOT NULL,
  assigned_by INT NOT NULL,
  deadline DATETIME NOT NULL,
  status ENUM('new','in_progress','done') NOT NULL DEFAULT 'new',
  report TEXT NULL,
  report_file VARCHAR(255) NULL,
  done_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
  FOREIGN KEY (resolution_id) REFERENCES resolutions(id) ON DELETE SET NULL,
  FOREIGN KEY (executor_id) REFERENCES users(id),
  FOREIGN KEY (assigned_by) REFERENCES users(id),
  INDEX (deadline), INDEX (status)
) ENGINE=InnoDB;

CREATE TABLE approvals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document_id INT NOT NULL,
  user_id INT NOT NULL,
  step_order INT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  comment TEXT NULL,
  decided_at DATETIME NULL,
  FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Маълумоти аввалин (парол барои ҳамаи корбарон: 123456)
INSERT INTO departments (name) VALUES
 ('Девонхона'),('Шӯъбаи ҳуқуқӣ'),('Шӯъбаи IT');

INSERT INTO users (full_name, login, password, role, department_id) VALUES
 ('Администратор','admin','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','admin',3),
 ('Корманди девонхона','devon','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','chancellery',1),
 ('Роҳбар','director','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','director',1),
 ('Сардори шӯъба','head','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','dept_head',2),
 ('Корманд','employee','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','employee',2);

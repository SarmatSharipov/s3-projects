SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS edms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edms;

DROP TABLE IF EXISTS approvals;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS resolutions;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;

CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  login VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('chancellery','director','dept_head','employee','admin') NOT NULL,
  department_id INT NULL,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  status ENUM('registered','review','in_progress','approved','rejected','done') NOT NULL DEFAULT 'registered',
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX (number), INDEX (doc_date), INDEX (type), INDEX (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE resolutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document_id INT NOT NULL,
  director_id INT NOT NULL,
  text TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
  FOREIGN KEY (director_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO departments (name) VALUES
 ('Девонхона'),('Шӯъбаи ҳуқуқӣ'),('Шӯъбаи IT');

INSERT INTO users (full_name, login, password, role, department_id) VALUES
 ('Администратор','admin','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','admin',3),
 ('Корманди девонхона','devon','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','chancellery',1),
 ('Роҳбар','director','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','director',1),
 ('Сардори шӯъба','head','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','dept_head',2),
 ('Корманд','employee','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','employee',2);

INSERT INTO documents (number, doc_date, subject, type, importance, sender, receiver, status, created_by) VALUES
('2026-0001', '2026-11-17', 'КМН', 'incoming', 'normal', 'Шарипов Сармат', 'Директор', 'registered', 1),
('ВР-2026/041', '2026-11-20', 'Дархости таъмини дафтарӣ', 'incoming', 'normal', 'Назарова М.', 'Директор', 'review', 2),
('СД-2026/018', '2026-11-18', 'Ҷавоб ба Вазорати молия', 'outgoing', 'normal', 'Шарипов С.', 'Вазорати молия', 'approved', 2),
('ДХ-2026/007', '2026-11-17', 'Фармоиши меҳнатӣ №12', 'internal', 'high', 'Раҳимов А.', 'Ҳама', 'in_progress', 1),
('ВР-2026/039', '2026-11-14', 'Шартномаи иҷора', 'incoming', 'normal', 'Қодиров Ф.', 'Директор', 'rejected', 2),
('ДХ-2026/005', '2026-11-10', 'Ҳисоботи моҳонаи октябр', 'internal', 'normal', 'Юсупова Н.', 'Директор', 'done', 2),
('ВР-2026/036', '2026-11-09', 'Мактуби ҳамкорӣ', 'incoming', 'low', 'Назарова М.', 'Директор', 'registered', 2),
('СД-2026/015', '2026-11-08', 'Ҷавоб ба дархости таъминот', 'outgoing', 'normal', 'Шарипов С.', 'Шӯъбаи таъминот', 'approved', 2),
('ДХ-2026/004', '2026-11-05', 'Фармоиши рухсатӣ', 'internal', 'normal', 'Раҳимов А.', 'Кормандон', 'done', 1),
('ВР-2026/030', '2026-11-01', 'Дархости таъмири бино', 'incoming', 'high', 'Қодиров Ф.', 'Директор', 'review', 2),
('ВР-2026/028', '2026-10-28', 'Пешниҳоди шартнома', 'incoming', 'normal', 'Назарова М.', 'Директор', 'in_progress', 2),
('СД-2026/012', '2026-10-25', 'Ҳисоботи семоҳа', 'outgoing', 'normal', 'Юсупова Н.', 'Вазорат', 'done', 2);

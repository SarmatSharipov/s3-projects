USE edms;
SET NAMES utf8mb4;

INSERT INTO departments (name) VALUES
 ('Девонхона'),
 ('Шӯъбаи ҳуқуқӣ'),
 ('Шӯъбаи IT');

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

USE edms;
SET NAMES utf8mb4;

INSERT INTO departments (name) VALUES
 ('Девонхона'),
 ('Шӯъбаи ҳуқуқӣ'),
 ('Шӯъбаи IT'),
 ('Шӯъбаи ҳисобдорӣ'),
 ('Шӯъбаи кадрҳо');

INSERT INTO users (full_name, login, password, role, department_id) VALUES
 ('Администратор','admin','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','admin',3),
 ('Корманди девонхона','devon','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','chancellery',1),
 ('Роҳбар','director','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','director',1),
 ('Сардори шӯъба','head','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','dept_head',2),
 ('Корманд','employee','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','employee',2),
 ('Назарова М.','nazarova','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','chancellery',1),
 ('Раҳимов А.','rahimov','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','dept_head',4),
 ('Шарипов С.','sharipov','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','employee',3),
 ('Юсупова Н.','yusupova','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','employee',4),
 ('Қодиров Ф.','qodirov','$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO','employee',2);

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
('СД-2026/012', '2026-10-25', 'Ҳисоботи семоҳа', 'outgoing', 'normal', 'Юсупова Н.', 'Вазорат', 'done', 2),
('ВР-2026/025', '2026-10-20', 'Дархости хариди компютер', 'incoming', 'high', 'Шарипов С.', 'Директор', 'review', 6),
('ДХ-2026/003', '2026-10-18', 'Фармоиши тағйири ҷадвал', 'internal', 'normal', 'Раҳимов А.', 'Ҳама', 'approved', 7),
('СД-2026/010', '2026-10-15', 'Ҷавоб ба Вазорати маориф', 'outgoing', 'normal', 'Назарова М.', 'Вазорати маориф', 'done', 6),
('ВР-2026/022', '2026-10-12', 'Мактуби шикоят', 'incoming', 'high', 'Қодиров Ф.', 'Директор', 'rejected', 2),
('ДХ-2026/002', '2026-10-08', 'Ҳисоботи кадрӣ', 'internal', 'normal', 'Юсупова Н.', 'Директор', 'done', 9),
('ВР-2026/020', '2026-10-05', 'Дархости рухсатии беморӣ', 'incoming', 'normal', 'Шарипов С.', 'Кадрҳо', 'registered', 8),
('СД-2026/008', '2026-10-01', 'Ҷавоб ба дархости шартнома', 'outgoing', 'normal', 'Назарова М.', 'Ширкати «Пайванд»', 'approved', 6),
('ВР-2026/018', '2026-09-28', 'Пешниҳоди лоиҳа', 'incoming', 'high', 'Қодиров Ф.', 'Директор', 'in_progress', 2),
('ДХ-2026/001', '2026-09-25', 'Фармоиши оғози соли нав', 'internal', 'high', 'Раҳимов А.', 'Ҳама', 'done', 7),
('ВР-2026/015', '2026-09-20', 'Дархости таъмини сӯзишворӣ', 'incoming', 'normal', 'Назарова М.', 'Директор', 'registered', 6),
('СД-2026/005', '2026-09-15', 'Ҳисоботи молиявӣ', 'outgoing', 'high', 'Юсупова Н.', 'Вазорати молия', 'approved', 9),
('ВР-2026/012', '2026-09-10', 'Мактуби табрик', 'incoming', 'low', 'Шарипов С.', 'Директор', 'done', 8),
('ВР-2026/010', '2026-09-05', 'Дархости таъмири автомашина', 'incoming', 'normal', 'Қодиров Ф.', 'Директор', 'review', 2);

INSERT INTO resolutions (document_id, director_id, text) VALUES
(2, 3, 'Ба шӯъбаи ҳисобдорӣ барои баррасӣ ва харид.'),
(4, 3, 'Иҷро шавад тибқи ҷадвал.'),
(10, 3, 'Ба шӯъбаи IT барои арзёбӣ.'),
(13, 3, 'Харид тасдиқ карда шуд.');

INSERT INTO tasks (document_id, resolution_id, executor_id, assigned_by, deadline, status) VALUES
(2, 1, 9, 3, '2026-11-27 17:00:00', 'in_progress'),
(4, 2, 5, 3, '2026-11-25 17:00:00', 'new'),
(10, 3, 8, 3, '2026-11-15 17:00:00', 'done'),
(13, 4, 8, 3, '2026-11-10 17:00:00', 'done'),
(7, NULL, 5, 2, '2026-11-20 17:00:00', 'new'),
(11, NULL, 9, 2, '2026-11-05 17:00:00', 'in_progress');

INSERT INTO approvals (document_id, user_id, step_order, status, comment, decided_at) VALUES
(2, 3, 1, 'pending', NULL, NULL),
(2, 4, 2, 'pending', NULL, NULL),
(3, 3, 1, 'approved', 'Тасдиқ шуд', '2026-11-19 10:00:00'),
(5, 3, 1, 'rejected', 'Шартҳо нопурра', '2026-11-15 14:30:00'),
(6, 3, 1, 'approved', NULL, '2026-11-11 09:00:00'),
(13, 3, 1, 'approved', 'Харид иҷозат дода шуд', '2026-10-22 11:00:00'),
(14, 3, 1, 'approved', NULL, '2026-10-19 16:00:00');

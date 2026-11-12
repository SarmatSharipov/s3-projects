-- database/seed.sql: Додаҳои санҷишӣ барои EDMS
USE edms;
SET NAMES utf8mb4;

-- Шӯъбаҳо
INSERT INTO departments (id, name) VALUES
(1, 'Девонхона'),
(2, 'Шӯъбаи ҳуқуқӣ'),
(3, 'Шӯъбаи IT');

-- Корбарон (Парол барои ҳама: Secret123!)
INSERT INTO users (id, full_name, login, password, role, department_id) VALUES
(1, 'Администратори Низом', 'admin', '$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO', 'admin', 3),
(2, 'Сафоров Ғафур', 'devon', '$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO', 'chancellery', 1),
(3, 'Раҳимов Фарҳод', 'director', '$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO', 'director', 1),
(4, 'Назарова Мадина', 'head_law', '$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO', 'dept_head', 2),
(5, 'Абдуллоев Шариф', 'employee_it', '$2y$10$//t70aMr7UYXvsleSrRBB.LHZk6PS2ttF.Nqv/0vQuRl7M93VapPO', 'employee', 3);

-- Ҳуҷҷатҳо
INSERT INTO documents (id, number, doc_date, subject, type, importance, sender, receiver, status, created_by) VALUES
(1, '№01/2026', '2026-03-01', 'Шартнома оид ба хариди таҷҳизот', 'internal', 'high', 'ҶДММ ТехноСервис', 'Шӯъбаи IT', 'review', 2),
(2, '№02/2026', '2026-03-02', 'Ариза оид ба гузаронидани таъмир', 'incoming', 'normal', 'Шаҳрванд Каримов Б.', 'Девонхона', 'registered', 2);

-- Резолюцияҳо
INSERT INTO resolutions (id, document_id, director_id, text) VALUES
(1, 1, 3, 'Ба Абдуллоев Ш. ҷиҳати иҷро ва назорат супорида шавад.');

-- Супоришҳо
INSERT INTO tasks (document_id, resolution_id, executor_id, assigned_by, deadline, status) VALUES
(1, 1, 5, 3, '2026-03-10 17:00:00', 'in_progress');

-- Мувофиқаҳо
INSERT INTO approvals (document_id, user_id, step_order, status) VALUES
(1, 4, 1, 'approved');
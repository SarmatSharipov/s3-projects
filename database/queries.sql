-- database/queries.sql: Дархостҳои назоратӣ
USE edms;
SET NAMES utf8mb4;

-- 1. Рӯйхати ҳуҷҷатҳо бо маълумоти муаллиф ва шӯъба (JOIN)
SELECT d.number, d.doc_date, d.subject, d.type, u.full_name AS creator
FROM documents d
JOIN users u ON u.id = d.created_by
ORDER BY d.doc_date DESC;

-- 2. Ҷустуҷӯи ҳуҷҷатҳо аз рӯи қисми рақам ё мавзӯъ (LIKE)
SELECT * FROM documents WHERE subject LIKE '%Шартнома%';

-- 3. Шумораи ҳуҷҷатҳо аз рӯи шӯъбаҳо (GROUP BY ва LEFT JOIN)
SELECT dept.name AS department, COUNT(doc.id) AS total_documents
FROM departments dept
LEFT JOIN users u ON u.department_id = dept.id
LEFT JOIN documents doc ON doc.created_by = u.id
GROUP BY dept.id, dept.name;

-- 4. Супоришҳои фаъол ва иҷрокунандагони онҳо
SELECT t.id, d.number AS doc_number, u.full_name AS executor, t.deadline, t.status
FROM tasks t
JOIN documents d ON d.id = t.document_id
JOIN users u ON u.id = t.executor_id
WHERE t.status != 'done';

-- 5. Санҷиши рамзгузории кириллӣ
SELECT name, CHAR_LENGTH(name) AS chars, LENGTH(name) AS bytes 
FROM departments WHERE id = 1;
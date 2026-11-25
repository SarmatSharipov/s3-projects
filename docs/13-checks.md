# Lab 13 — EDMS

PDO + Repository + CRUD + ҷустуҷӯ/филтр/саҳифабандӣ + валидатсия + PRG + ҳисобот

## База
1. phpMyAdmin → Import `database/schema.sql`
2. Import `database/seed.sql`
3. php -S localhost:8000 -t public

## Санҷиш
- /documents — рӯйхат, «Ёфт шуд»
- ҷустуҷӯи ' OR 1=1 -- → 0 сабт
- /documents/create → валидатсия, пас аз сабт 302
- таҳрир / нест кардан (POST)
- /reports/status — ҳисобот

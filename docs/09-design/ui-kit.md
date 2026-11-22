# EDMS — Дизайн (Лаб 09, Figma)

**Файл Figma:** https://www.figma.com/design/0XyWI8mln6I3WLAAbyjTIu  
Саҳифаҳо: `01 Tokens & UI-kit` · `02 Flows & Wireframes` · `03 Mockups & Prototype` (3 саҳифа — маҳдудияти тарифи Starter).

## Мундариҷаи файл
- **UI-kit:** Button (3 variant × 4 state), Input (default/focus/error/disabled), Badge (6 статус), Card, Modal; 22 токени ранг (variables), токенҳои spacing/radius, 6 text style.
- **User flow:** 2 (бақайдгирии ҳуҷҷат, тасдиқи ҳуҷҷат) бо шохаҳои хато.
- **Wireframe:** 6 (login, dashboard, рӯйхат, корт, форма, kanban) — grayscale.
- **Mockup Desktop 1440:** 6 экран; **Mobile 390:** рӯйхат + форма.
- **Prototype:** Login → Dashboard → Ҳуҷҷатҳо → Корти ҳуҷҷат → «Тасдиқ» (Open overlay — modal) ; Ҳуҷҷатҳо → Бақайдгирӣ → Сабт. Нуқтаи оғоз: Login.

## Токенҳо ↔ CSS (public/assets/css/app.css)
| Figma | CSS |
|---|---|
| color/primary | `--edms-primary` (→ `--bs-primary`) |
| color/text | `--edms-text` |
| color/muted | `--edms-muted` |
| color/bg / surface / border | `--edms-bg` / `--edms-surface` / `--edms-border` |
| color/focus | `--edms-focus` |
| status/<ҳолат>-bg/fg | `.st-<ҳолат>` (`--b-bg`, `--b-fg`) |
| radius/md | `--edms-radius` |

## Контрасти WCAG 2.2 AA (≥ 4.5:1 матн)
| Ҷуфт | Матн | Фон | Нисбат | AA |
|---|---|---|---|---|
| Текст на фон | `#0f172a` | `#f8fafc` | 17.1:1 | ✓ |
| Muted на белом | `#475569` | `#ffffff` | 7.6:1 | ✓ |
| Кнопка primary (белый на синем) | `#ffffff` | `#1d4ed8` | 6.7:1 | ✓ |
| Кнопка hover | `#ffffff` | `#1e40af` | 8.7:1 | ✓ |
| Ссылка на белом | `#1d4ed8` | `#ffffff` | 6.7:1 | ✓ |
| Ошибка на белом | `#b91c1c` | `#ffffff` | 6.5:1 | ✓ |
| registered | `#334155` | `#e2e8f0` | 8.4:1 | ✓ |
| review | `#92400e` | `#fef3c7` | 6.4:1 | ✓ |
| in_progress | `#075985` | `#e0f2fe` | 6.6:1 | ✓ |
| approved | `#065f46` | `#d1fae5` | 6.8:1 | ✓ |
| rejected | `#991b1b` | `#fee2e2` | 6.8:1 | ✓ |
| done | `#5b21b6` | `#ede9fe` | 7.6:1 | ✓ |

Дигар: ҳадафи мин. 44×44 px (≥24×24), фокуси намоён (3px `color/focus`), ранг ягона нишона нест (бейдж = нуқта + матн), майдонҳо label доранд.

## Ҷадвали статусҳо
| Объект | Қиммат (БД) | Тавсиф (UI) | Токен |
|---|---|---|---|
| documents | registered | Бақайдгирифта | status/registered |
| documents | review | Дар баррасӣ | status/review |
| documents | in_progress | Дар иҷро | status/in_progress |
| documents | approved | Тасдиқшуда | status/approved |
| documents | rejected | Радшуда | status/rejected |
| documents | done | Иҷрошуда | status/done |
| approvals | pending / approved / rejected | Интизорӣ / Тасдиқ / Рад | review / approved / rejected |
| tasks | new / in_progress / done | Нав / Дар иҷро / Иҷрошуда | registered / in_progress / done |

## Скриншотҳо
PNG-ҳои макетҳо: `docs/09-design/screens/` — аз Figma экспорт кунед (фреймро интихоб → Export → PNG 1x). Git: `git add docs/09-design && git commit -m "design: UI-kit, макеты и прототип Figma (lab-09)" && git tag -a lab-09 -m "Лабораторная работа 9"`.

**Link-и прототип:** _(Share → Anyone with the link → can view; гузоред)_

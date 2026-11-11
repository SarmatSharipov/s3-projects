(() => {
  const d = document, root = d.documentElement;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Theme (with smooth view-transition) ---------- */
  const icon = t => { const i = d.querySelector('[data-theme-toggle] i'); if (i) i.className = t === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars'; };
  const apply = t => { root.setAttribute('data-bs-theme', t); try { localStorage.setItem('theme', t); } catch (e) {} icon(t); };
  icon(root.getAttribute('data-bs-theme'));
  d.querySelectorAll('[data-theme-toggle]').forEach(b => b.addEventListener('click', () => {
    const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    d.startViewTransition && !reduce ? d.startViewTransition(() => apply(next)) : apply(next);
  }));

  /* ---------- Mobile sidebar ---------- */
  const sb = d.querySelector('.sidebar'), bd = d.querySelector('.backdrop-mobile');
  const toggle = open => { sb && sb.classList.toggle('open', open); bd && bd.classList.toggle('show', open); };
  d.querySelectorAll('[data-burger]').forEach(b => b.addEventListener('click', () => toggle(!sb.classList.contains('open'))));
  bd && bd.addEventListener('click', () => toggle(false));

  /* ---------- Search shortcut "/" ---------- */
  const search = d.querySelector('.search-box input');
  addEventListener('keydown', e => {
    if (e.key === '/' && search && !/INPUT|TEXTAREA|SELECT/.test(d.activeElement.tagName)) { e.preventDefault(); search.focus(); }
  });

  /* ---------- Reveal on scroll (staggered) ---------- */
  const targets = [...d.querySelectorAll('.card, .stat, .hero, .alert')].filter(el => !el.closest('.auth-card'));
  targets.forEach((el, i) => { el.classList.add('reveal'); el.style.setProperty('--d', Math.min(i * 0.07, 0.6) + 's'); });
  const io = new IntersectionObserver(es => es.forEach(en => {
    if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
  }), { threshold: .08 });
  targets.forEach(el => io.observe(el));

  /* ---------- Table rows stagger ---------- */
  d.querySelectorAll('tbody tr, table tr:not(:first-child)').forEach((tr, i) => {
    if (i < 40) { tr.classList.add('row-in'); tr.style.setProperty('--d', (0.25 + i * 0.04) + 's'); }
  });

  /* ---------- Count-up numbers ---------- */
  const ease = t => t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
  d.querySelectorAll('[data-count]').forEach(el => {
    const end = parseInt(el.dataset.count, 10) || 0;
    if (reduce || end === 0) { el.textContent = end; return; }
    const t0 = performance.now(), dur = 1300;
    const tick = now => {
      const p = Math.min((now - t0) / dur, 1);
      el.textContent = Math.round(end * ease(p));
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  });

  /* ---------- Cursor spotlight on cards ---------- */
  d.querySelectorAll('.spot').forEach(el => el.addEventListener('pointermove', e => {
    const r = el.getBoundingClientRect();
    el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    el.style.setProperty('--my', (e.clientY - r.top) + 'px');
  }));

  /* ---------- Button ripple + loading state on submit ---------- */
  d.addEventListener('click', e => {
    const b = e.target.closest('.btn'); if (!b || reduce) return;
    const r = b.getBoundingClientRect(), s = Math.max(r.width, r.height);
    const sp = d.createElement('span'); sp.className = 'ripple';
    sp.style.cssText = `width:${s}px;height:${s}px;left:${e.clientX - r.left - s / 2}px;top:${e.clientY - r.top - s / 2}px`;
    b.appendChild(sp); setTimeout(() => sp.remove(), 700);
  });
  d.querySelectorAll('form').forEach(f => f.addEventListener('submit', e => {
    const b = e.submitter || f.querySelector('button:not([type=button])');
    if (b && !b.dataset.noLoad && f.checkValidity()) setTimeout(() => b.classList.add('loading'), 0);
  }));

  /* ---------- Toasts ---------- */
  d.querySelectorAll('.toast-x').forEach((t, i) => {
    t.style.animationDelay = i * 0.12 + 's';
    setTimeout(() => { t.classList.add('out'); setTimeout(() => t.remove(), 450); }, 4500 + i * 300);
    t.addEventListener('click', () => t.remove());
  });

  /* ---------- Show/hide password ---------- */
  d.querySelectorAll('[data-pw]').forEach(b => b.addEventListener('click', () => {
    const inp = b.parentElement.querySelector('input'); const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password'; b.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
  }));
})();

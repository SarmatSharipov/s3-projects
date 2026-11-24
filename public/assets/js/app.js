function debounce(fn, delay = 300) {
    let timerId;
    return (...args) => {
      clearTimeout(timerId);
      timerId = setTimeout(() => fn(...args), delay);
    };
  }
  
  function formatDate(isoDate) {
    if (!isoDate) return '';
    const [year, month, day] = isoDate.split('-');
    if (!year || !month || !day) return isoDate;
    return `${day}.${month}.${year}`;
  }
  
  const FIELD_MESSAGES = {
    title: {
      valueMissing: 'Сарлавҳаи ҳуҷҷатро ворид кунед.',
      tooShort: 'Сарлавҳа бояд ҳадди ақал 4 аломат дошта бошад.',
      tooLong: 'Сарлавҳа набояд аз 120 аломат зиёд бошад.',
    },
    type: {
      valueMissing: 'Намуди ҳуҷҷатро интихоб кунед.',
    },
    doc_date: {
      valueMissing: 'Санаи ҳуҷҷатро нишон диҳед.',
    },
    due_date: {
      valueMissing: 'Мӯҳлати иҷроро нишон диҳед.',
    },
    description: {
      tooLong: 'Тавсиф набояд аз 300 аломат зиёд бошад.',
    },
  };
  
  function messageFor(field) {
    const v = field.validity;
    if (v.valid) return '';
    if (v.customError) return field.validationMessage;
  
    const custom = FIELD_MESSAGES[field.name] ?? {};
    if (v.valueMissing) return custom.valueMissing ?? 'Ин майдон ҳатмист.';
    if (v.tooShort) return custom.tooShort ?? `Ҳадди ақал ${field.minLength} аломат лозим аст.`;
    if (v.tooLong) return custom.tooLong ?? `На зиёда аз ${field.maxLength} аломат.`;
    if (v.typeMismatch) return 'Формати воридот нодуруст аст.';
    if (v.rangeUnderflow) return 'Қимат аз ҳадди иҷозатдодашуда камтар аст.';
    if (v.badInput) return 'Қимати дурустро ворид кунед.';
  
    return 'Қимати майдонро санҷед.';
  }
  
  function showFieldState(field) {
    const message = messageFor(field);
    const feedback = document.getElementById(`${field.id}-error`);
  
    field.classList.toggle('is-invalid', message !== '');
    if (message) {
      field.setAttribute('aria-invalid', 'true');
    } else {
      field.removeAttribute('aria-invalid');
    }
  
    if (feedback) {
      feedback.textContent = message;
    }
  
    return message === '';
  }
  
  function checkDocumentRules(form) {
    const titleField = form.elements.title;
    const docDateField = form.elements.doc_date;
    const dueDateField = form.elements.due_date;
  
    if (titleField) {
      titleField.setCustomValidity('');
      const trimmed = titleField.value.trim();
      if (trimmed.length > 0 && trimmed.length < 4) {
        titleField.setCustomValidity('Сарлавҳа бояд ҳадди ақал 4 аломат дошта бошад.');
      }
    }
  
    if (docDateField && dueDateField) {
      dueDateField.setCustomValidity('');
      if (docDateField.value && dueDateField.value && dueDateField.value < docDateField.value) {
        dueDateField.setCustomValidity('Мӯҳлати иҷро наметавонад пеш аз санаи ҳуҷҷат бошад.');
      }
    }
  }
  
  function initDocumentForm() {
    const form = document.getElementById('doc-create-form');
    if (!form) return;
  
    const docDateField = form.elements.doc_date;
    const dueDateField = form.elements.due_date;
    const getFields = () => form.querySelectorAll('input, select, textarea');
  
    if (docDateField && dueDateField) {
      docDateField.addEventListener('change', () => {
        dueDateField.min = docDateField.value;
      });
    }
  
    const refresh = (field) => {
      checkDocumentRules(form);
      showFieldState(field);
      if (dueDateField && field !== dueDateField && dueDateField.classList.contains('is-invalid')) {
        showFieldState(dueDateField);
      }
    };
  
    form.addEventListener('focusout', (event) => {
      if (event.target.matches('input, select, textarea')) {
        refresh(event.target);
      }
    });
  
    form.addEventListener('input', (event) => {
      if (event.target.classList.contains('is-invalid')) {
        refresh(event.target);
      }
    });
  
    form.addEventListener('change', (event) => {
      if (event.target.matches('select, input[type="date"]')) {
        refresh(event.target);
      }
    });
  
    form.addEventListener('submit', (event) => {
      checkDocumentRules(form);
      let firstInvalid = null;
  
      getFields().forEach((field) => {
        const isValid = showFieldState(field);
        if (!isValid && !firstInvalid) {
          firstInvalid = field;
        }
      });
  
      if (firstInvalid) {
        event.preventDefault();
        firstInvalid.focus();
      }
    });
  }
  
  const STATUS_LABELS = {
    registered: 'Бақайдгирифта',
    review: 'Дар баррасӣ',
    in_progress: 'Дар иҷро',
    approved: 'Тасдиқшуда',
    rejected: 'Радшуда',
    done: 'Иҷрошуда',
  };
  
  function createCell(text, label = '', className = '') {
    const td = document.createElement('td');
    td.textContent = text ?? '';
    if (label) td.setAttribute('data-label', label);
    if (className) td.className = className;
    return td;
  }
  
  function createDocumentRow(doc) {
    const tr = document.createElement('tr');
  
    const regCell = createCell(doc.reg_number, 'Рақам');
  
    const titleCell = document.createElement('td');
    titleCell.setAttribute('data-label', 'Сарлавҳа');
    const link = document.createElement('a');
    link.href = `document.html?id=${encodeURIComponent(doc.id)}`;
    link.textContent = doc.title;
    titleCell.append(link);
  
    const typeCell = createCell(doc.doc_type, 'Намуд');
    const senderCell = createCell(doc.sender, 'Фиристанда');
    const dateCell = createCell(formatDate(doc.doc_date), 'Сана');
  
    const statusCell = document.createElement('td');
    statusCell.setAttribute('data-label', 'Ҳолат');
    const badge = document.createElement('span');
    badge.className = 'badge-status';
    if (doc.status in STATUS_LABELS) {
      badge.classList.add(`st-${doc.status}`);
    }
    badge.textContent = STATUS_LABELS[doc.status] ?? doc.status;
    statusCell.append(badge);
  
    const actionsCell = document.createElement('td');
    actionsCell.className = 'text-end text-nowrap';
  
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'btn btn-sm btn-outline-danger';
    deleteButton.dataset.deleteUrl = `/documents/${encodeURIComponent(doc.id)}/delete`;
    deleteButton.dataset.docTitle = `${doc.reg_number} — ${doc.title}`;
    deleteButton.textContent = 'Нест кардан';
  
    actionsCell.append(deleteButton);
    tr.append(regCell, titleCell, typeCell, senderCell, dateCell, statusCell, actionsCell);
    return tr;
  }
  
  function renderDocuments(tbody, documents) {
    if (documents.length === 0) {
      const tr = document.createElement('tr');
      const td = createCell(
        'Ҳуҷҷат ёфт нашуд. Шартҳои ҷустуҷӯро тағйир диҳед.',
        '',
        'text-center text-body-secondary py-4'
      );
      td.colSpan = 7;
      tr.append(td);
      tbody.replaceChildren(tr);
      return;
    }
  
    tbody.replaceChildren(...documents.map(createDocumentRow));
  }
  
  function initDocumentSearch() {
    const input = document.getElementById('q');
    const typeSelect = document.getElementById('type');
    const statusSelect = document.getElementById('st');
    const tbody = document.getElementById('documents-tbody');
  
    if (!input || !tbody) return;
  
    const statusBox = document.getElementById('doc-search-status');
    const setStatus = (text) => {
      if (statusBox) statusBox.textContent = text;
    };
  
    let controller = null;
  
    async function search() {
      controller?.abort();
      controller = new AbortController();
  
      const params = new URLSearchParams();
      const q = input.value.trim();
      const type = typeSelect?.value ?? '';
      const status = statusSelect?.value ?? '';
  
      if (q !== '') params.set('q', q);
      if (type !== '') params.set('type', type);
      if (status !== '') params.set('status', status);
  
      tbody.setAttribute('aria-busy', 'true');
      setStatus('Ҷустуҷӯ...');
  
      try {
        const url = `/api/documents${params.toString() ? `?${params.toString()}` : ''}`;
        const response = await fetch(url, {
          headers: { Accept: 'application/json' },
          signal: controller.signal,
        });
  
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }
  
        const documents = await response.json();
        renderDocuments(tbody, documents);
        setStatus(
          documents.length > 0
            ? `Ҳуҷҷатҳо ёфт шуданд: ${documents.length}`
            : 'Ҳуҷҷат ёфт нашуд.'
        );
      } catch (error) {
        if (error.name === 'AbortError') return;
        console.error('Хатои ҷустуҷӯи фаврӣ:', error);
        setStatus('Ҷустуҷӯро иҷро кардан муяссар нашуд. Пайвастро санҷед ва бори дигар кӯшиш кунед.');
      } finally {
        tbody.removeAttribute('aria-busy');
      }
    }
  
    const debouncedSearch = debounce(search, 300);
  
    input.addEventListener('input', debouncedSearch);
    typeSelect?.addEventListener('change', search);
    statusSelect?.addEventListener('change', search);
  }
  
  function initDeleteConfirm() {
    const modalElement = document.getElementById('confirm-delete-modal');
    if (!modalElement || !window.bootstrap) return;
  
    const form = document.getElementById('confirm-delete-form');
    const nameElement = document.getElementById('confirm-delete-name');
    const submitButton = form?.querySelector('[type="submit"]');
    if (!form || !submitButton) return;
  
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    let lastTrigger = null;
  
    document.addEventListener('click', (event) => {
      const button = event.target.closest('[data-delete-url]');
      if (!button) return;
  
      lastTrigger = button;
      form.action = button.dataset.deleteUrl;
      if (nameElement) {
        nameElement.textContent = button.dataset.docTitle ?? '';
      }
      submitButton.disabled = false;
      modal.show();
    });
  
    modalElement.addEventListener('hidden.bs.modal', () => {
      if (lastTrigger?.isConnected) {
        lastTrigger.focus();
      }
    });
  
    form.addEventListener('submit', () => {
      submitButton.disabled = true;
    });
  }
  
  initDocumentForm();
  initDocumentSearch();
  initDeleteConfirm();
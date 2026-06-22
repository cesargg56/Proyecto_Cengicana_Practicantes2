(function () {
  const DATA_SELECTOR = 'script[data-lab-table-config]';
  const SKIP_TYPES = new Set(['hidden', 'submit', 'button', 'reset']);

  function parseConfig(form) {
    const configNode = form.querySelector(DATA_SELECTOR);
    if (!configNode) {
      return { lotes: [], muestras: {}, loteActual: '' };
    }

    try {
      return JSON.parse(configNode.textContent || '{}');
    } catch (error) {
      return { lotes: [], muestras: {}, loteActual: '' };
    }
  }

  function getControlLabel(control) {
    const id = control.getAttribute('id');
    const escapedId = id && window.CSS && CSS.escape ? CSS.escape(id) : id;
    const explicit = escapedId ? control.ownerDocument.querySelector(`label[for="${escapedId}"]`) : null;
    if (explicit) {
      return explicit.textContent.trim();
    }

    const label = control.closest('label');
    if (label) {
      return Array.from(label.childNodes)
        .filter((node) => node.nodeType === Node.TEXT_NODE)
        .map((node) => node.textContent.trim())
        .join(' ')
        .trim() || control.name;
    }

    return control.name.replace(/_/g, ' ');
  }

  function normalizeText(value) {
    return (value || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase();
  }

  function isSharedAnalysisControl(control) {
    const scope = (control.dataset.labScope || '').toLowerCase();
    if (scope === 'row') {
      return false;
    }

    if (scope === 'single' || control.dataset.labSingle === '1') {
      return true;
    }

    const name = normalizeText(control.name);
    const label = normalizeText(getControlLabel(control));
    const searchable = `${name} ${label}`;

    return /(^|[_\s-])(control|blanco|blancos|blk)([_\s-]|$)/.test(searchable)
      || name.includes('blanco')
      || name.startsWith('blk_');
  }

  function isPrimaryControl(control, footer) {
    if (!control.name || control.closest('.form-footer') || control.closest('.table-wrap')) {
      return false;
    }

    if (footer && footer.contains(control)) {
      return false;
    }

    const type = (control.getAttribute('type') || '').toLowerCase();
    if (SKIP_TYPES.has(type)) {
      return false;
    }

    return !control.name.endsWith('[]');
  }

  function hideOriginal(control) {
    control.disabled = true;

    const field = control.closest('.field');
    if (field && !field.querySelector('.form-footer')) {
      const fieldControls = Array.from(field.querySelectorAll('input, select, textarea'))
        .filter((item) => !item.closest('.table-wrap'));

      if (fieldControls.length <= 1) {
        field.classList.add('lab-original-field-hidden');
        return;
      }
    }

    const label = control.closest('label');
    if (label) {
      label.classList.add('lab-original-field-hidden');
    }
  }

  function buildDataInput(definition, rowIndex) {
    const input = definition.template.cloneNode(true);
    input.disabled = false;
    input.name = `${definition.name}[]`;
    input.removeAttribute('id');
    input.dataset.baseName = definition.name;
    input.dataset.rowIndex = String(rowIndex);
    return input;
  }

  function fillLoteOptions(select, lotes, selected) {
    select.innerHTML = '';

    const empty = document.createElement('option');
    empty.value = '';
    empty.textContent = 'Seleccione';
    select.appendChild(empty);

    lotes.forEach((lote) => {
      const option = document.createElement('option');
      option.value = lote;
      option.textContent = lote;
      option.selected = lote === selected;
      select.appendChild(option);
    });
  }

  function buildLaboratorioSpecialOptions(definitions) {
    const usedValues = new Set();

    return definitions.reduce((options, definition) => {
      const label = (definition.label || definition.name || '').trim();
      if (!label || usedValues.has(label)) {
        return options;
      }

      usedValues.add(label);
      options.push({ value: label, label });
      return options;
    }, []);
  }

  function fillLaboratorioOptions(select, muestras, lote, preferred, specialOptions = []) {
    const values = muestras[lote] || [];
    select.innerHTML = '';

    const empty = document.createElement('option');
    empty.value = '';
    empty.textContent = values.length || specialOptions.length ? 'Seleccione' : 'Sin número';
    select.appendChild(empty);

    const usedValues = new Set(['']);
    specialOptions.forEach(({ value, label }) => {
      const option = document.createElement('option');
      option.value = value;
      option.textContent = label;
      option.selected = value === preferred;
      select.appendChild(option);
      usedValues.add(value);
    });

    values.forEach((numero) => {
      const value = String(numero);
      if (usedValues.has(value)) {
        return;
      }

      const option = document.createElement('option');
      option.value = value;
      option.textContent = value;
      option.selected = value === preferred;
      select.appendChild(option);
      usedValues.add(value);
    });
  }

  function laboratorioKey(row) {
    const lote = row.querySelector('select[name="lote[]"]')?.value || '';
    const numero = row.querySelector('select[name="numero_laboratorio[]"]')?.value || '';
    return lote && numero ? `${lote}||${numero}` : '';
  }

  function updateLaboratorioAvailability(tbody) {
    if (!tbody) {
      return;
    }

    const rows = dataRows(tbody);
    const selected = new Map();

    rows.forEach((row) => {
      const key = laboratorioKey(row);
      if (!key) {
        return;
      }
      selected.set(key, (selected.get(key) || 0) + 1);
    });

    rows.forEach((row) => {
      const lote = row.querySelector('select[name="lote[]"]')?.value || '';
      const labSelect = row.querySelector('select[name="numero_laboratorio[]"]');
      const current = labSelect?.value || '';

      if (!labSelect) {
        return;
      }

      let hasDuplicate = false;
      Array.from(labSelect.options).forEach((option) => {
        if (!option.value) {
          option.disabled = false;
          return;
        }

        const key = lote ? `${lote}||${option.value}` : '';
        const count = selected.get(key) || 0;
        const isCurrent = option.value === current;
        option.disabled = !isCurrent && count > 0;

        if (isCurrent && count > 1) {
          hasDuplicate = true;
        }
      });

      labSelect.setCustomValidity(hasDuplicate
        ? 'Este número de laboratorio ya fue seleccionado para este lote.'
        : '');
    });
  }

  function hasDuplicateLaboratorios(tbody) {
    if (!tbody) {
      return false;
    }

    const seen = new Set();

    for (const row of dataRows(tbody)) {
      const key = laboratorioKey(row);
      if (!key) {
        continue;
      }

      if (seen.has(key)) {
        return true;
      }

      seen.add(key);
    }

    return false;
  }

  function dataRows(tbody) {
    return tbody ? Array.from(tbody.querySelectorAll('tr.lab-data-row')) : [];
  }

  function lastDataRow(tbody) {
    const rows = dataRows(tbody);
    return rows[rows.length - 1] || null;
  }

  function nextLote(lotes, current) {
    if (!lotes.length) {
      return '';
    }

    const index = lotes.indexOf(current);
    return lotes[index + 1] || lotes[0];
  }

  function reindexRows(tbody) {
    dataRows(tbody).forEach((row, index) => {
      row.querySelector('.lab-row-index').textContent = String(index + 1);
      row.querySelectorAll('[data-row-index]').forEach((input) => {
        input.dataset.rowIndex = String(index);
      });
    });
  }

  function cleanupOriginalLayout(formBody) {
    formBody.querySelectorAll('.field-group, .grid2').forEach((group) => {
      if (group.querySelector('.form-footer')) {
        return;
      }

      const controls = Array.from(group.querySelectorAll('input, select, textarea'));
      if (controls.length && controls.every((control) => control.disabled)) {
        group.classList.add('lab-original-field-hidden');
      }
    });

    formBody.querySelectorAll('h3').forEach((heading) => {
      const next = heading.nextElementSibling;
      if (next && next.classList.contains('lab-original-field-hidden')) {
        heading.classList.add('lab-original-field-hidden');
      }
    });
  }

  function createRow({
    definitions,
    lotes,
    muestras,
    selectedLote,
    rowIndex,
    specialOptions,
  }) {
    const row = document.createElement('tr');
    row.className = 'lab-data-row';

    const indexCell = document.createElement('td');
    indexCell.className = 'lab-row-index';
    indexCell.textContent = String(rowIndex + 1);
    row.appendChild(indexCell);

    const loteCell = document.createElement('td');
    const loteSelect = document.createElement('select');
    loteSelect.name = 'lote[]';
    loteSelect.required = true;
    fillLoteOptions(loteSelect, lotes, selectedLote);
    loteCell.appendChild(loteSelect);
    row.appendChild(loteCell);

    const labCell = document.createElement('td');
    const labSelect = document.createElement('select');
    labSelect.name = 'numero_laboratorio[]';
    fillLaboratorioOptions(labSelect, muestras, loteSelect.value, '', specialOptions);
    labCell.appendChild(labSelect);
    row.appendChild(labCell);

    loteSelect.addEventListener('change', () => {
      fillLaboratorioOptions(labSelect, muestras, loteSelect.value, '', specialOptions);
      updateLaboratorioAvailability(row.parentElement);
    });

    labSelect.addEventListener('change', () => {
      updateLaboratorioAvailability(row.parentElement);
    });

    definitions.forEach((definition) => {
      const cell = document.createElement('td');
      cell.appendChild(buildDataInput(definition, rowIndex));
      row.appendChild(cell);
    });

    const actionCell = document.createElement('td');
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'lab-table-icon-button';
    removeButton.textContent = '×';
    removeButton.title = 'Quitar fila';
    removeButton.addEventListener('click', () => {
      const tbody = row.parentElement;
      if (!tbody || dataRows(tbody).length <= 1) {
        return;
      }
      row.remove();
      reindexRows(tbody);
      updateLaboratorioAvailability(tbody);
    });
    actionCell.appendChild(removeButton);
    row.appendChild(actionCell);

    return row;
  }

  function initForm(form) {
    if (form.dataset.labTableReady === '1') {
      return;
    }

    const footer = form.querySelector('.form-footer');
    const formBody = form.querySelector('.form-body') || form;
    const config = parseConfig(form);
    const primaryControls = Array.from(formBody.querySelectorAll('input, select, textarea'))
      .filter((control) => isPrimaryControl(control, footer));
    const controls = primaryControls.filter((control) => !isSharedAnalysisControl(control));
    const sharedControls = primaryControls.filter(isSharedAnalysisControl);

    if (!controls.length && !sharedControls.length) {
      return;
    }

    const definitions = controls.map((control) => ({
      name: control.name,
      label: getControlLabel(control),
      template: control.cloneNode(true),
    }));
    const sharedDefinitions = sharedControls.map((control) => ({
      name: control.name,
      label: getControlLabel(control),
      template: control.cloneNode(true),
    }));
    const laboratorioSpecialOptions = buildLaboratorioSpecialOptions(sharedDefinitions);

    primaryControls.forEach(hideOriginal);
    cleanupOriginalLayout(formBody);

    const wrapper = document.createElement('section');
    wrapper.className = 'lab-table-panel';
    wrapper.innerHTML = `
      <div class="lab-table-toolbar">
        <div class="section-title">Datos de análisis por muestra</div>
        <div class="lab-table-actions">
          <button type="button" class="lab-table-button" data-add-row>+ Agregar fila</button>
          <button type="button" class="lab-table-button" data-add-lote>+ Agregar lote</button>
        </div>
      </div>
      <div class="table-wrap lab-entry-table-wrap">
        <table class="lab-entry-table">
          <thead>
            <tr></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    `;

    const header = wrapper.querySelector('thead tr');
    ['#', 'Lote', 'Número de laboratorio', ...definitions.map((definition) => definition.label), ''].forEach((label) => {
      const th = document.createElement('th');
      th.textContent = label;
      header.appendChild(th);
    });

    const tbody = wrapper.querySelector('tbody');

    tbody.appendChild(createRow({
      definitions,
      lotes: config.lotes || [],
      muestras: config.muestras || {},
      selectedLote: config.loteActual || (config.lotes || [])[0] || '',
      rowIndex: 0,
      specialOptions: laboratorioSpecialOptions,
    }));
    updateLaboratorioAvailability(tbody);

    wrapper.querySelector('[data-add-row]').addEventListener('click', () => {
      const lastRow = lastDataRow(tbody);
      const lastLote = lastRow?.querySelector('select[name="lote[]"]')?.value || config.loteActual || '';
      tbody.appendChild(createRow({
        definitions,
        lotes: config.lotes || [],
        muestras: config.muestras || {},
        selectedLote: lastLote,
        rowIndex: dataRows(tbody).length,
        specialOptions: laboratorioSpecialOptions,
      }));
      updateLaboratorioAvailability(tbody);
    });

    wrapper.querySelector('[data-add-lote]').addEventListener('click', () => {
      const lastLote = lastDataRow(tbody)?.querySelector('select[name="lote[]"]')?.value || config.loteActual || '';
      tbody.appendChild(createRow({
        definitions,
        lotes: config.lotes || [],
        muestras: config.muestras || {},
        selectedLote: nextLote(config.lotes || [], lastLote),
        rowIndex: dataRows(tbody).length,
        specialOptions: laboratorioSpecialOptions,
      }));
      updateLaboratorioAvailability(tbody);
    });

    form.addEventListener('submit', (event) => {
      updateLaboratorioAvailability(tbody);
      if (hasDuplicateLaboratorios(tbody)) {
        event.preventDefault();
        form.reportValidity();
      }
    });

    if (footer) {
      footer.insertAdjacentElement('afterend', wrapper);
    } else {
      formBody.appendChild(wrapper);
    }

    form.dataset.labTableReady = '1';
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(initForm);
  });
})();

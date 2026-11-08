(function () {
  'use strict';
  var $  = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  function showMenu(key) {
    var page = $('#menu-' + key);
    if (!page) return;
    $$('.menu-page').forEach(function (p) { p.classList.toggle('hidden', p !== page); });
    $$('.nav-link').forEach(function (a) { a.classList.toggle('active', a.dataset.menu === key); });
    var link = $('.nav-link.active[data-menu="' + key + '"]');
    var sub = link && link.closest('ul.sub');
    if (sub) {
      sub.classList.remove('hidden');
      var parent = $('[data-toggle="' + sub.id + '"]');
      if (parent) { parent.classList.add('open'); parent.setAttribute('aria-expanded', 'true'); }
    }
    if (window.history && history.replaceState) history.replaceState(null, '', '?menu=' + key);
    applySearch();
  }

  var search = $('#global-search');
  function applySearch() {
    var q = search ? search.value.trim().toLowerCase() : '';
    var page = $('.menu-page:not(.hidden)');
    if (!page) return;
    $$('tbody tr:not(.empty)', page).forEach(function (tr) {
      tr.classList.toggle('hidden', q !== '' && tr.textContent.toLowerCase().indexOf(q) === -1);
    });
  }
  if (search) search.addEventListener('input', applySearch);

  function openDialog(d) { if (d && !d.open) d.showModal(); }

  function openForm(entity, row) {
    var d = $('#modal-' + entity);
    if (!d) return;
    var form = $('form', d);
    form.reset();
    var editing = !!row;
    form.elements.action.value = editing ? 'update' : 'create';
    form.elements.id.value = editing ? row.__id : '';
    $('.modal-title', d).textContent = (editing ? 'Edit ' : 'Tambah ') + d.dataset.title;

    if (editing) {
      Object.keys(row).forEach(function (k) {
        var el = form.elements[k];
        if (el && k !== 'action' && k !== 'id' && k !== 'entity' && k !== 'csrf') el.value = row[k];
      });
    }
    $$('[data-required-create]', form).forEach(function (el) {
      el.required = !editing;
      el.placeholder = editing ? 'Kosongkan jika tidak diubah' : '';
    });
    openDialog(d);
    var first = $('input:not([type=hidden]), select', form);
    if (first) first.focus();
  }

  document.addEventListener('click', function (e) {
    var t;

    if ((t = e.target.closest('[data-add]'))) {
      e.preventDefault();
      if (t.dataset.menu) showMenu(t.dataset.menu);
      openForm(t.dataset.add, null);
      return;
    }
    if ((t = e.target.closest('[data-edit]'))) {
      e.preventDefault();
      var row = {};
      try { row = JSON.parse(t.dataset.row); } catch (err) { return; }
      openForm(t.dataset.edit, row);
      return;
    }
    if ((t = e.target.closest('[data-delete]'))) {
      e.preventDefault();
      var d = $('#modal-delete');
      d.querySelector('[name=entity]').value = t.dataset.delete;
      d.querySelector('[name=id]').value = t.dataset.id;
      $('#delete-name').textContent = t.dataset.name;
      openDialog(d);
      return;
    }
    if ((t = e.target.closest('[data-toggle]'))) {
      var list = document.getElementById(t.dataset.toggle);
      var willOpen = list.classList.contains('hidden');
      list.classList.toggle('hidden', !willOpen);
      t.classList.toggle('open', willOpen);
      t.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      return;
    }
    if ((t = e.target.closest('[data-menu]'))) {
      e.preventDefault();
      showMenu(t.dataset.menu);
      return;
    }
    if ((t = e.target.closest('[data-close]'))) {
      var dlg = t.closest('dialog');
      if (dlg) dlg.close();
      return;
    }
    if (e.target.tagName === 'DIALOG') e.target.close();
  });

  function rupiah(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

  function refreshLines(form) {
    var rows = $$('.line', form), total = 0;
    rows.forEach(function (row) {
      var sel = $('select', row), qty = $('input[type=number]', row), info = $('.line-info', row);
      var opt = sel.options[sel.selectedIndex];
      var harga = opt && opt.dataset.harga ? Number(opt.dataset.harga) : 0;
      var hasStok = !!opt && opt.dataset.stok !== undefined;
      var stok = hasStok ? Number(opt.dataset.stok) : 0;
      if (hasStok) qty.max = stok; else qty.removeAttribute('max');
      total += harga * (Number(qty.value) || 0);
      info.textContent = harga ? rupiah(harga) + (hasStok ? ', stok ' + stok : '') : '';
    });
    var out = $('[data-lines-total]', form);
    if (out) out.textContent = rupiah(total);
    $$('[data-remove-line]', form).forEach(function (b) { b.disabled = rows.length < 2; });
  }

  function addLine(form) {
    var tpl = $('template[data-line-template]', form), box = $('[data-lines]', form);
    if (!tpl || !box) return;
    box.appendChild(tpl.content.cloneNode(true));
    refreshLines(form);
  }

  function openLinesForm(id) {
    var dlg = document.getElementById(id);
    if (!dlg) return;
    var form = $('form', dlg);
    form.reset();
    $('[data-lines]', form).innerHTML = '';
    addLine(form);
    openDialog(dlg);
    var first = $('select', form);
    if (first) first.focus();
  }

  function cell(tag, text, cls) {
    var el = document.createElement(tag);
    el.textContent = text;
    if (cls) el.className = cls;
    return el;
  }

  function openDetail(info) {
    $('#detail-title').textContent = info.title;
    $('#detail-sub').textContent = info.sub;

    var body = $('#detail-lines');
    body.innerHTML = '';
    if (!info.lines.length) {
      var empty = document.createElement('tr');
      empty.className = 'empty';
      var td = cell('td', 'Tidak ada barang.');
      td.colSpan = 4;
      empty.appendChild(td);
      body.appendChild(empty);
    }
    info.lines.forEach(function (l) {
      var tr = document.createElement('tr');
      l.forEach(function (c, i) { tr.appendChild(cell('td', c, i ? 'num' : '')); });
      body.appendChild(tr);
    });

    var dl = $('#detail-totals');
    dl.innerHTML = '';
    info.totals.forEach(function (r, i) {
      var row = document.createElement('div');
      if (i === info.totals.length - 1) row.className = 'grand';
      row.appendChild(cell('dt', r[0]));
      row.appendChild(cell('dd', r[1]));
      dl.appendChild(row);
    });
    openDialog($('#modal-detail'));
  }

  function openProses(kind, id) {
    var dlg = $('#modal-proses'), terima = kind === 'terima';
    dlg.querySelector('[name=aksi]').value = terima ? 'penerimaan_terima' : 'penerimaan_batal';
    dlg.querySelector('[name=id]').value = id;
    $('#proses-title').textContent = terima ? 'Terima pengadaan' : 'Batalkan pengadaan';
    $('#proses-text').textContent = terima
      ? 'Pengadaan #' + id + ' diterima. Stok semua barang di dalamnya langsung ditambahkan ke kartu stok.'
      : 'Pengadaan #' + id + ' dibatalkan dan stok tidak berubah. Tindakan ini tidak dapat diurungkan.';
    var btn = $('#proses-submit');
    btn.textContent = terima ? 'Terima barang' : 'Batalkan pengadaan';
    btn.className = 'btn ' + (terima ? 'btn-dark' : 'btn-danger');
    openDialog(dlg);
  }

  document.addEventListener('click', function (e) {
    var t;
    if ((t = e.target.closest('[data-open-form]'))) {
      e.preventDefault();
      openLinesForm(t.dataset.openForm);
      return;
    }
    if ((t = e.target.closest('[data-add-line]'))) {
      addLine(t.closest('form'));
      return;
    }
    if ((t = e.target.closest('[data-remove-line]'))) {
      var form = t.closest('form'), row = t.closest('.line');
      if (row && $$('.line', form).length > 1) row.remove();
      refreshLines(form);
      return;
    }
    if ((t = e.target.closest('[data-proses]'))) {
      openProses(t.dataset.proses, t.dataset.id);
      return;
    }
    if ((t = e.target.closest('[data-detail]'))) {
      var info;
      try { info = JSON.parse(t.dataset.detail); } catch (err) { return; }
      openDetail(info);
    }
  });

  ['input', 'change'].forEach(function (name) {
    document.addEventListener(name, function (e) {
      var form = e.target.closest && e.target.closest('[data-lines-form]');
      if (form && e.target.closest('.line')) refreshLines(form);
    });
  });

  $$('dialog[data-autoopen]').forEach(openDialog);

  $$('.toast').forEach(function (el, i) {
    setTimeout(function () {
      el.classList.add('leaving');
      setTimeout(function () { el.remove(); }, 450);
    }, 4500 + i * 600);
  });
})();

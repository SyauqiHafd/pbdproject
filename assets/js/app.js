/* Navigasi menu, pencarian tabel, modal tambah/ubah/hapus, notifikasi */
(function () {
  'use strict';
  var $  = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* ---------- pindah menu (tanpa reload) ---------- */
  function showMenu(key) {
    var page = $('#menu-' + key);
    if (!page) return;
    $$('.menu-page').forEach(function (p) { p.classList.toggle('hidden', p !== page); });
    $$('.nav-link').forEach(function (a) { a.classList.toggle('active', a.dataset.menu === key); });
    if (window.history && history.replaceState) history.replaceState(null, '', '?menu=' + key);
    applySearch();
  }

  /* ---------- pencarian pada tabel yang sedang tampil ---------- */
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

  /* ---------- modal ---------- */
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
    // klik di luar kartu modal (backdrop) menutup modal
    if (e.target.tagName === 'DIALOG') e.target.close();
  });

  /* ---------- notifikasi otomatis hilang ---------- */
  $$('.toast').forEach(function (el, i) {
    setTimeout(function () {
      el.classList.add('leaving');
      setTimeout(function () { el.remove(); }, 450);
    }, 4500 + i * 600);
  });
})();

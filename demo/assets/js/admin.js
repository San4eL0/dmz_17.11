document.addEventListener('DOMContentLoaded', function () {
  var BOOKINGS = [
    { id: 1, category: 'Стандарт', price: 3500, guest: 'Иванов Иван', phone: '+7(900)111-22-33', email: 'ivan@example.ru', period: '17.11.2026 — 20.11.2026', status: 'Новая' },
    { id: 2, category: 'Люкс', price: 8500, guest: 'Петрова Анна', phone: '+7(900)444-55-66', email: 'anna@example.ru', period: '20.11.2026 — 23.11.2026', status: 'Одобрена' },
    { id: 3, category: 'Студия', price: 6000, guest: 'Сидоров Пётр', phone: '+7(900)777-88-99', email: 'petr@example.ru', period: '22.11.2026 — 24.11.2026', status: 'Отклонена' },
    { id: 4, category: 'Стандарт', price: 3900, guest: 'Козлова Мария', phone: '+7(900)123-45-67', email: 'maria@example.ru', period: '25.11.2026 — 27.11.2026', status: 'Новая' }
  ];

  var loginGate = document.getElementById('loginGate');
  var adminPanel = document.getElementById('adminPanel');
  var bodyEl = document.getElementById('adminBody');
  var emptyEl = document.getElementById('emptyAdmin');
  var tableEl = document.getElementById('adminTable');

  var ADMIN_LOGIN = 'hotel123';
  var ADMIN_PASS = 'adminHotel';

  function statusClass(s) {
    return 'status status-' + s.toLowerCase();
  }

  function render() {
    bodyEl.innerHTML = '';
    emptyEl.hidden = BOOKINGS.length !== 0;
    tableEl.style.display = BOOKINGS.length ? '' : 'none';
    BOOKINGS.forEach(function (b) {
      var tr = document.createElement('tr');
      var approveBtn = b.status !== 'Одобрена'
        ? '<form class="inline-form"><button class="btn btn-success btn-sm" data-act="approve" data-id="' + b.id + '">Одобрить</button></form>'
        : '';
      var rejectBtn = b.status !== 'Отклонена'
        ? '<form class="inline-form"><button class="btn btn-secondary btn-sm" data-act="reject" data-id="' + b.id + '">Отклонить</button></form>'
        : '';
      var delBtn = '<form class="inline-form"><button class="btn btn-danger btn-sm" data-act="delete" data-id="' + b.id + '">Удалить</button></form>';
      tr.innerHTML =
        '<td>' + b.id + '</td>' +
        '<td>' + b.category + '<br><small>' + b.price.toLocaleString('ru-RU') + ' ₽/сут</small></td>' +
        '<td>' + b.guest + '</td>' +
        '<td>' + b.phone + '<br><small>' + b.email + '</small></td>' +
        '<td>' + b.period + '</td>' +
        '<td><span class="' + statusClass(b.status) + '">' + b.status + '</span></td>' +
        '<td class="actions">' + approveBtn + rejectBtn + delBtn + '</td>';
      bodyEl.appendChild(tr);
    });
  }

  function unauthorized() {
    alert('Нужно войти в систему администратора.');
  }

  document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var l = document.getElementById('admLogin').value.trim();
    var p = document.getElementById('admPass').value;
    if (l === ADMIN_LOGIN && p === ADMIN_PASS) {
      loginGate.setAttribute('hidden', true);
      adminPanel.removeAttribute('hidden');
      render();
    } else {
      alert('Неверный логин или пароль.');
    }
  });

  document.getElementById('logoutBtn').addEventListener('click', function () {
    adminPanel.setAttribute('hidden', true);
    loginGate.removeAttribute('hidden');
  });

  bodyEl.addEventListener('click', function (e) {
    var btn = e.target.closest('button[data-act]');
    if (!btn) return;
    if (adminPanel.hasAttribute('hidden')) { unauthorized(); return; }
    var id = parseInt(btn.getAttribute('data-id'), 10);
    var act = btn.getAttribute('data-act');
    var b = BOOKINGS.find(function (x) { return x.id === id; });
    if (!b) return;
    if (act === 'approve') b.status = 'Одобрена';
    else if (act === 'reject') b.status = 'Отклонена';
    else if (act === 'delete') { BOOKINGS = BOOKINGS.filter(function (x) { return x.id !== id; }); }
    render();
  });

  render();
});
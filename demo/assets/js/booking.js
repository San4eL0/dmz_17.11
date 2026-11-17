document.addEventListener('DOMContentLoaded', function () {
  var ROOMS = {
    1: { category: 'Стандарт', price: 3500, places: 2, area: 20 },
    2: { category: 'Стандарт', price: 3900, places: 2, area: 22 },
    3: { category: 'Студия', price: 6000, places: 2, area: 30 },
    4: { category: 'Студия', price: 6500, places: 3, area: 34 },
    5: { category: 'Люкс', price: 8500, places: 3, area: 45 },
    6: { category: 'Люкс', price: 9500, places: 4, area: 55 }
  };

  var id = parseInt(new URLSearchParams(window.location.search).get('id'), 10);
  var room = ROOMS[id];
  var summary = document.getElementById('roomSummary');
  if (room) {
    summary.textContent = room.category + ' · ' + room.places + ' мест · ' +
      room.area + ' м² · ' + room.price.toLocaleString('ru-RU') + ' ₽/сутки';
  }

  var form = document.getElementById('bookingForm');
  var successBox = document.getElementById('success');

  function setError(input, msg) {
    input.classList.add('invalid');
    var err = input.parentElement.querySelector('.error-text');
    if (!err) { err = document.createElement('div'); err.className = 'error-text'; input.parentElement.appendChild(err); }
    err.textContent = msg;
  }
  function clearError(input) {
    input.classList.remove('invalid');
    var err = input.parentElement.querySelector('.error-text');
    if (err) err.remove();
  }
  function validName(v) { return /^[А-Яа-яЁё\s.\-]+$/.test(v); }
  function validPhone(v) { return /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/.test(v); }
  function validEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
  function parseDmy(v) {
    var m = /^(\d{2})\.(\d{2})\.(\d{4})$/.exec(v);
    if (!m) return null;
    var d = new Date(+m[3], +m[2] - 1, +m[1]);
    return (d.getFullYear() === +m[3] && d.getMonth() === +m[2] - 1 && d.getDate() === +m[1]) ? d : null;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var ok = true;
    var lastname = document.getElementById('lastname');
    var firstname = document.getElementById('firstname');
    var phone = document.getElementById('phone');
    var email = document.getElementById('email');
    var dateIn = document.getElementById('dateIn');
    var dateOut = document.getElementById('dateOut');
    [lastname, firstname, phone, email, dateIn, dateOut].forEach(clearError);

    var lv = lastname.value.trim();
    var fv = firstname.value.trim();
    var pv = phone.value.trim();
    var ev = email.value.trim();
    var din = parseDmy(dateIn.value.trim());
    var dout = parseDmy(dateOut.value.trim());

    if (!fv || !validName(fv)) { setError(firstname, 'Имя обязательно: кириллица, пробелы, точки и тире.'); ok = false; }
    if (!lv || !validName(lv)) { setError(lastname, 'Фамилия обязательна: кириллица, пробелы, точки и тире.'); ok = false; }
    if (!pv || !validPhone(pv)) { setError(phone, 'Формат +7(XXX)XXX-XX-XX.'); ok = false; }
    if (!ev || !validEmail(ev)) { setError(email, 'Введите корректный e-mail.'); ok = false; }
    if (!din) { setError(dateIn, 'Дата заезда обязательна (ДД.ММ.ГГГГ).'); ok = false; }
    if (!dout) { setError(dateOut, 'Дата выезда обязательна (ДД.ММ.ГГГГ).'); ok = false; }
    if (din && dout) {
      var today = new Date(); today.setHours(0, 0, 0, 0);
      if (din < today) { setError(dateIn, 'Дата заезда не может быть раньше текущей даты.'); ok = false; }
      if (dout <= din) { setError(dateOut, 'Дата выезда должна быть позже даты заезда.'); ok = false; }
    }

    if (ok && successBox) successBox.hidden = false;
  });
});
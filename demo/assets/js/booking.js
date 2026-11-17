document.addEventListener('DOMContentLoaded', function () {
  var ROOMS = {
    1: { category: 'Стандарт', price: 3500, places: 2, area: 20, imgs: ['assets/img/room-standard-1.jpg', 'assets/img/room-standard-2.jpg'] },
    2: { category: 'Стандарт', price: 3900, places: 2, area: 22, imgs: ['assets/img/room-standard-1.jpg', 'assets/img/room-standard-2.jpg'] },
    3: { category: 'Студия', price: 6000, places: 2, area: 30, imgs: ['assets/img/room-studio-1.jpg', 'assets/img/room-studio-2.jpg'] },
    4: { category: 'Студия', price: 6500, places: 3, area: 34, imgs: ['assets/img/room-studio-1.jpg', 'assets/img/room-studio-2.jpg'] },
    5: { category: 'Люкс', price: 8500, places: 3, area: 45, imgs: ['assets/img/room-lux-1.jpg', 'assets/img/room-lux-2.jpg'] },
    6: { category: 'Люкс', price: 9500, places: 4, area: 55, imgs: ['assets/img/room-lux-1.jpg', 'assets/img/room-lux-2.jpg'] }
  };

  var id = parseInt(new URLSearchParams(window.location.search).get('id'), 10);
  var room = ROOMS[id] || ROOMS[1];
  var summary = document.getElementById('roomSummary');
  summary.textContent = room.category + ' · ' + room.places + ' мест · ' +
    room.area + ' м² · ' + room.price.toLocaleString('ru-RU') + ' ₽/сутки';

  /* ===== Слайдер изображений (задание: скролл картинок в бронировании) ===== */
  var clip = document.getElementById('galleryClip');
  var dotsWrap = document.getElementById('galDots');
  var prevBtn = document.getElementById('galPrev');
  var nextBtn = document.getElementById('galNext');
  var current = 0;

  var slides = room.imgs.map(function (src, i) {
    var s = document.createElement('div');
    s.className = 'gallery-slide' + (i === 0 ? ' active' : '');
    s.innerHTML = '<img src="' + src + '" alt="' + room.category + '">';
    clip.appendChild(s);
    var dot = document.createElement('button');
    dot.className = 'gallery-dot' + (i === 0 ? ' active' : '');
    dot.setAttribute('aria-label', 'Слайд ' + (i + 1));
    dot.addEventListener('click', function () { show(i); });
    dotsWrap.appendChild(dot);
    return { slide: s, dot: dot };
  });

  function show(i) {
    current = (i + slides.length) % slides.length;
    slides.forEach(function (s, idx) {
      s.slide.classList.toggle('active', idx === current);
      s.dot.classList.toggle('active', idx === current);
    });
  }

  prevBtn.addEventListener('click', function () { show(current - 1); });
  nextBtn.addEventListener('click', function () { show(current + 1); });

  // Автопрокрутка раз в 5 секунд
  var auto = setInterval(function () { show(current + 1); }, 5000);
  function pause() { clearInterval(auto); }
  function resume() { auto = setInterval(function () { show(current + 1); }, 5000); }
  document.getElementById('gallery').addEventListener('mouseenter', pause);
  document.getElementById('gallery').addEventListener('mouseleave', resume);

  /* ===== Маска телефона +7(XXX)XXX-XX-XX ===== */
  var phone = document.getElementById('phone');

  phone.addEventListener('input', function () {
    var digits = phone.value.replace(/\D/g, '');
    if (digits[0] === '8') digits = '7' + digits.slice(1);
    if (digits[0] === '9') digits = '7' + digits;
    if (!digits) { phone.value = ''; return; }
    if (digits[0] !== '7') digits = '7' + digits;

    var out = '+7';
    if (digits.length > 1) out += '(' + digits.slice(1, 4);
    if (digits.length >= 4) out += ')' + digits.slice(4, 7);
    if (digits.length >= 7) out += '-' + digits.slice(7, 9);
    if (digits.length >= 9) out += '-' + digits.slice(9, 11);
    phone.value = out;
  });

  /* ===== Валидация формы ===== */
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
document.addEventListener('DOMContentLoaded', function () {
  /* ===== Слайдер изображений номера ===== */
  var clip = document.getElementById('galleryClip');
  var dotsWrap = document.getElementById('galDots');
  var prevBtn = document.getElementById('galPrev');
  var nextBtn = document.getElementById('galNext');
  var images = (typeof window.ROOM_IMAGES !== 'undefined' && window.ROOM_IMAGES.length) ? window.ROOM_IMAGES : [];
  var current = 0;

  if (clip && images.length) {
    var slides = images.map(function (src, i) {
      var s = document.createElement('div');
      s.className = 'gallery-slide' + (i === 0 ? ' active' : '');
      s.innerHTML = '<img src="' + src + '" alt="Номер">';
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

    var auto = setInterval(function () { show(current + 1); }, 5000);
    var galleryEl = document.getElementById('gallery');
    galleryEl.addEventListener('mouseenter', function () { clearInterval(auto); });
    galleryEl.addEventListener('mouseleave', function () {
      auto = setInterval(function () { show(current + 1); }, 5000);
    });
  }

  /* ===== Маска телефона +7(XXX)XXX-XX-XX ===== */
  var phone = document.getElementById('phone');
  if (phone) {
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
  }

  /* ===== Логика валидации дат по занятым периодам (задание 4) ===== */
  if (typeof window.BOOKED_RANGES === 'undefined') return;

  var dateIn = document.getElementById('date_in');
  var dateOut = document.getElementById('date_out');
  if (!dateIn || !dateOut) return;

  var today = new Date();
  var isoToday = today.getFullYear() + '-' +
    String(today.getMonth() + 1).padStart(2, '0') + '-' +
    String(today.getDate()).padStart(2, '0');

  function iso(d) {
    return d.getFullYear() + '-' +
      String(d.getMonth() + 1).padStart(2, '0') + '-' +
      String(d.getDate()).padStart(2, '0');
  }

  var blocked = new Set();
  window.BOOKED_RANGES.forEach(function (range) {
    var start = new Date(range.date_in + 'T00:00:00');
    var end = new Date(range.date_out + 'T00:00:00');
    var cur = new Date(start);
    while (cur < end) {
      blocked.add(iso(cur));
      cur.setDate(cur.getDate() + 1);
    }
  });

  function belongsToBlockedPeriod(dateStr) {
    return blocked.has(dateStr);
  }

  dateIn.addEventListener('change', function () {
    if (!dateIn.value) return;
    var d = new Date(dateIn.value + 'T00:00:00');
    if (d < today) {
      dateIn.setCustomValidity('Дата заезда не может быть раньше текущей даты.');
    } else if (belongsToBlockedPeriod(iso(d))) {
      dateIn.setCustomValidity('Этот период уже забронирован (одобрен администратором).');
    } else {
      dateIn.setCustomValidity('');
    }
    var plus = new Date(d);
    plus.setDate(plus.getDate() + 1);
    dateOut.min = iso(plus);
  });

  dateOut.addEventListener('change', function () {
    if (!dateIn.value || !dateOut.value) return;
    var end = new Date(dateOut.value + 'T00:00:00');
    var start = new Date(dateIn.value + 'T00:00:00');
    var cur = new Date(start);
    var conflict = false;
    while (cur < end) {
      if (blocked.has(iso(cur))) { conflict = true; break; }
      cur.setDate(cur.getDate() + 1);
    }
    if (conflict) {
      dateOut.setCustomValidity('Выбранный период пересекается с уже забронированным (одобренным).');
    } else {
      dateOut.setCustomValidity('');
    }
  });

  dateIn.min = isoToday;
});
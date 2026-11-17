document.addEventListener('DOMContentLoaded', function () {
  if (typeof window.BOOKED_RANGES === 'undefined') return;

  var dateIn = document.getElementById('date_in');
  var dateOut = document.getElementById('date_out');
  if (!dateIn || !dateOut) return;

  // Минимальная дата заезда — сегодня (дата заезда не раньше текущей даты)
  var today = new Date();
  var isoToday = today.getFullYear() + '-' +
    String(today.getMonth() + 1).padStart(2, '0') + '-' +
    String(today.getDate()).padStart(2, '0');

  function iso(d) {
    return d.getFullYear() + '-' +
      String(d.getMonth() + 1).padStart(2, '0') + '-' +
      String(d.getDate()).padStart(2, '0');
  }

  // Список всех дат, попадающих в одобренные периоды (как YYYY-MM-DD)
  var blocked = new Set();
  window.BOOKED_RANGES.forEach(function (range) {
    var start = new Date(range.date_in + 'T00:00:00');
    var end = new Date(range.date_out + 'T00:00:00'); // выезд — не активен, но период закрыт до него
    var cur = new Date(start);
    while (cur < end) {
      blocked.add(iso(cur));
      cur.setDate(cur.getDate() + 1);
    }
  });

  function belongsToBlockedPeriod(dateStr) {
    return blocked.has(dateStr);
  }

  // Перехватываем ввод через нативные date-пикеры: ставим min/максимум через события.
  // Для текстовых полей (ДД.ММ.ГГГГ) показываем подсказку.
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
    // минимальная дата выезда — следующий день после заезда
    var plus = new Date(d);
    plus.setDate(plus.getDate() + 1);
    dateOut.min = iso(plus);
  });

  dateOut.addEventListener('change', function () {
    if (!dateIn.value || !dateOut.value) return;
    var end = new Date(dateOut.value + 'T00:00:00');
    var addOne = new Date(dateOut.value + 'T00:00:00');
    addOne.setDate(addOne.getDate() + 1);
    if (belongsToBlockedPeriod(iso(addOne))) {
      // Проверяем весь интервал [заезд, выезд) на пересечение с занятыми периодами
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
    } else {
      dateOut.setCustomValidity('');
    }
  });

  function toDmy(dateStr) {
    var parts = dateStr.split('-');
    return parts[2] + '.' + parts[1] + '.' + parts[0];
  }

  // Синхронизация: если поле пустое, но date-пикер задан — подставляем
  function syncDmy(input, value) {
    if (input && value && !input.value) {
      input.value = toDmy(value);
    }
  }

  dateIn.min = isoToday;
});
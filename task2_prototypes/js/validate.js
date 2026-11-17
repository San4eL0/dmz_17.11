document.addEventListener('DOMContentLoaded', function () {
  var form = document.querySelector('.booking-form');
  if (!form) return;

  var successBox = document.getElementById('success');

  function setError(id, message) {
    var field = document.getElementById(id);
    if (!field) return;
    field.classList.add('invalid');
    var err = field.parentElement.querySelector('.error-text');
    if (!err) {
      err = document.createElement('div');
      err.className = 'error-text';
      field.parentElement.appendChild(err);
    }
    err.textContent = message;
  }

  function clearError(id) {
    var field = document.getElementById(id);
    if (!field) return;
    field.classList.remove('invalid');
    var err = field.parentElement.querySelector('.error-text');
    if (err) err.remove();
  }

  function validName(value) {
    return /^[А-Яа-яЁё\s.\-]+$/.test(value);
  }

  function validPhone(value) {
    return /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/.test(value);
  }

  function validEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var ok = true;

    var lastname = document.getElementById('lastname').value.trim();
    var firstname = document.getElementById('firstname').value.trim();
    var phone = document.getElementById('phone').value.trim();
    var email = document.getElementById('email').value.trim();
    var dateIn = document.getElementById('dateIn').value;
    var dateOut = document.getElementById('dateOut').value;

    clearError('lastname');
    clearError('firstname');
    clearError('phone');
    clearError('email');
    clearError('dateIn');
    clearError('dateOut');

    if (!lastname || !validName(lastname)) {
      setError('lastname', 'Фамилия обязательна: только кириллица, пробелы, точки и тире.');
      ok = false;
    }
    if (!firstname || !validName(firstname)) {
      setError('firstname', 'Имя обязательно: только кириллица, пробелы, точки и тире.');
      ok = false;
    }
    if (!phone || !validPhone(phone)) {
      setError('phone', 'Телефон должен быть в формате +7(XXX)XXX-XX-XX.');
      ok = false;
    }
    if (!email || !validEmail(email)) {
      setError('email', 'Введите корректный e-mail.');
      ok = false;
    }
    if (!dateIn) {
      setError('dateIn', 'Укажите дату заезда.');
      ok = false;
    }
    if (!dateOut) {
      setError('dateOut', 'Укажите дату выезда.');
      ok = false;
    }

    if (ok && dateIn && dateOut && new Date(dateOut) <= new Date(dateIn)) {
      setError('dateOut', 'Дата выезда должна быть позже даты заезда.');
      ok = false;
    }

    if (ok && successBox) {
      successBox.hidden = false;
    }
  });
});
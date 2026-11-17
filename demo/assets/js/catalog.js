document.addEventListener('DOMContentLoaded', function () {
  var ROOMS = [
    { id: 1, category: 'Стандарт', price: 3500, places: 2, area: 20, img: 'assets/img/room-standard-1.jpg', feats: ['2 места', '20 м²', 'Кондиционер', 'Телевизор', 'Wi-Fi'] },
    { id: 2, category: 'Стандарт', price: 3900, places: 2, area: 22, img: 'assets/img/room-standard-2.jpg', feats: ['2 места', '22 м²', 'Кондиционер', 'Телевизор', 'Холодильник', 'Wi-Fi'] },
    { id: 3, category: 'Студия', price: 6000, places: 2, area: 30, img: 'assets/img/room-studio-1.jpg', feats: ['2 места', '30 м²', 'Кондиционер', 'Холодильник', 'Wi-Fi'] },
    { id: 4, category: 'Студия', price: 6500, places: 3, area: 34, img: 'assets/img/room-studio-2.jpg', feats: ['3 места', '34 м²', 'Кондиционер', 'Телевизор', 'Холодильник', 'Wi-Fi'] },
    { id: 5, category: 'Люкс', price: 8500, places: 3, area: 45, img: 'assets/img/room-lux-1.jpg', feats: ['3 места', '45 м²', 'Кондиционер', 'Телевизор', 'Холодильник', 'Wi-Fi'] },
    { id: 6, category: 'Люкс', price: 9500, places: 4, area: 55, img: 'assets/img/room-lux-2.jpg', feats: ['4 места', '55 м²', 'Кондиционер', 'Телевизор', 'Холодильник', 'Wi-Fi'] }
  ];

  var grid = document.getElementById('roomGrid');
  var empty = document.getElementById('empty');
  var countEl = document.getElementById('count');

  function render(list) {
    grid.innerHTML = '';
    list.forEach(function (r) {
      var feats = r.feats.map(function (f) { return '<li>' + f + '</li>'; }).join('');
      var card = document.createElement('article');
      card.className = 'room-card';
      card.innerHTML =
        '<img src="' + r.img + '" alt="' + r.category + '">' +
        '<h2>' + r.category + '</h2>' +
        '<p class="price">' + r.price.toLocaleString('ru-RU') + ' ₽ / сутки</p>' +
        '<ul class="features">' + feats + '</ul>' +
        '<a href="booking.html?id=' + r.id + '" class="btn btn-primary">Забронировать</a>';
      grid.appendChild(card);
    });
    empty.hidden = list.length !== 0;
    countEl.textContent = list.length;
  }

  function applyFilter() {
    var cat = document.getElementById('category').value;
    var pmin = parseFloat(document.getElementById('price_min').value) || 0;
    var pmax = parseFloat(document.getElementById('price_max').value) || Infinity;
    var places = document.getElementById('places').value;

    var res = ROOMS.filter(function (r) {
      if (cat && r.category !== cat) return false;
      if (r.price < pmin || r.price > pmax) return false;
      if (places && r.places !== parseInt(places, 10)) return false;
      return true;
    });
    render(res);
  }

  document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault();
    applyFilter();
  });

  document.getElementById('resetBtn').addEventListener('click', function () {
    document.getElementById('category').value = '';
    document.getElementById('price_min').value = '';
    document.getElementById('price_max').value = '';
    document.getElementById('places').value = '';
    render(ROOMS);
  });

  render(ROOMS);
});
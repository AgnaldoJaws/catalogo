document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnNearMe');
    if (!btn) return;

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        if (!navigator.geolocation) return alert('Geolocalização não suportada.');

        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = document.getElementById('lat');
            const lng = document.getElementById('lng');
            const form = document.getElementById('filtersForm');
            if (lat && lng && form) {
                lat.value = pos.coords.latitude.toFixed(6);
                lng.value = pos.coords.longitude.toFixed(6);
                // define sort nearest por UX
                const sort = form.querySelector('select[name="sort"]');
                if (sort) sort.value = 'nearest';
                form.submit();
            }
        }, () => alert('Não foi possível obter sua localização.'));
    });
});

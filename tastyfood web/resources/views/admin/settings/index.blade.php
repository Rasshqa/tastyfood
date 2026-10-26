@extends('layouts.admin')

@section('header', 'Pengaturan')

@section('content')
<div class="max-w-4xl">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">Informasi Umum</h2>
        <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Brand</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'PADANG RICE') }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="site_description" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                    <input type="text" name="location" value="{{ old('location', $settings['location'] ?? '') }}" required class="w-full px-4 py-2 border rounded-lg" placeholder="Jl. Terusan Mars Utara III No.8D, Bandung">
                </div>
            </div>

            {{-- Restaurant Location with Map + Search --}}
            <h3 class="text-md font-bold mb-3 mt-8 pt-4 border-t">Lokasi Restoran (Peta & Delivery)</h3>
            <p class="text-sm text-gray-600 mb-3">Cari alamat atau klik pada peta untuk menentukan lokasi presisi restoran. Lokasi ini digunakan untuk halaman kontak dan perhitungan jarak delivery.</p>

            {{-- Search Address --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Alamat Restoran</label>
                <div class="flex gap-2">
                    <input type="text" id="address_search" placeholder="Ketik nama jalan, gedung, atau tempat..." class="flex-1 px-4 py-2 border rounded-lg">
                    <button type="button" id="search_btn" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-sm font-semibold whitespace-nowrap">
                        Cari
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Contoh: "Jl. Asia Afrika Bandung" atau "Trans Studio Mall"</p>
                <div id="search_results" class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden"></div>
            </div>

            {{-- Map --}}
            <div id="map" class="h-80 rounded-lg border border-gray-300 mb-3" style="z-index: 1;"></div>

            {{-- Coordinates --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" step="any" name="contact_latitude" id="contact_latitude" value="{{ old('contact_latitude', $settings['contact_latitude'] ?? '-6.9475') }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="number" step="any" name="contact_longitude" id="contact_longitude" value="{{ old('contact_longitude', $settings['contact_longitude'] ?? '107.6191') }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div id="detected_info" class="mb-4 p-3 bg-blue-50 rounded text-xs text-gray-700 hidden">
                <strong>Alamat terdeteksi:</strong> <span id="detected_address">-</span><br>
                <strong>Koordinat:</strong> <span id="detected_coords">-</span>
            </div>


            <h3 class="text-md font-bold mb-3 mt-6 pt-4 border-t">Chat Settings</h3>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="hidden" name="chat_enabled" value="0">
                    <input type="checkbox" name="chat_enabled" value="1" {{ ($settings['chat_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Enable chat feature for customers</span>
                </label>
            </div>

            <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .search-result { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #eee; }
    .search-result:hover { background-color: #fef3c7; }
    .search-result:last-child { border-bottom: none; }
    #search_results { max-height: 220px; overflow-y: auto; }
</style>
<script>
let map, marker;

document.addEventListener('DOMContentLoaded', function() {
    const lat = parseFloat(document.getElementById('contact_latitude').value) || -6.9475;
    const lng = parseFloat(document.getElementById('contact_longitude').value) || 107.6191;

    map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('contact_latitude').value = pos.lat.toFixed(7);
        document.getElementById('contact_longitude').value = pos.lng.toFixed(7);
        reverseGeocode(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('contact_latitude').value = e.latlng.lat.toFixed(7);
        document.getElementById('contact_longitude').value = e.latlng.lng.toFixed(7);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('contact_latitude').addEventListener('change', updateMarkerFromInput);
    document.getElementById('contact_longitude').addEventListener('change', updateMarkerFromInput);

    // Fix map display after tab switch / modal open
    setTimeout(() => map.invalidateSize(), 300);
});

function updateMarkerFromInput() {
    const lat = parseFloat(document.getElementById('contact_latitude').value);
    const lng = parseFloat(document.getElementById('contact_longitude').value);
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
    }
}

async function searchAddress(query) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5&addressdetails=1`;
    const response = await fetch(url);
    return await response.json();
}

document.getElementById('search_btn').addEventListener('click', async function() {
    const query = document.getElementById('address_search').value.trim();
    if (!query) return;
    this.textContent = 'Mencari...';
    this.disabled = true;
    try {
        const results = await searchAddress(query);
        if (results.length === 0) { alert('Alamat tidak ditemukan.'); return; }
        if (results.length === 1) { selectResult(results[0]); }
        else { showResults(results); }
    } catch (e) { alert('Gagal mencari.'); }
    finally { this.textContent = 'Cari'; this.disabled = false; }
});

document.getElementById('address_search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('search_btn').click(); }
});

function showResults(results) {
    const container = document.getElementById('search_results');
    container.innerHTML = '';
    container.classList.remove('hidden');
    results.forEach(r => {
        const div = document.createElement('div');
        div.className = 'search-result';
        div.innerHTML = `<div class="text-sm font-medium">${r.display_name.split(',')[0]}</div><div class="text-xs text-gray-500">${r.display_name}</div>`;
        div.onclick = () => { selectResult(r); container.classList.add('hidden'); };
        container.appendChild(div);
    });
}

function selectResult(result) {
    const lat = parseFloat(result.lat), lng = parseFloat(result.lon);
    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 17);
    document.getElementById('contact_latitude').value = lat.toFixed(7);
    document.getElementById('contact_longitude').value = lng.toFixed(7);
    document.getElementById('address_search').value = result.display_name;
    reverseGeocode(lat, lng);
}

function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
        .then(r => r.json())
        .then(data => {
            const addr = data.display_name || '-';
            document.getElementById('detected_address').textContent = addr;
            document.getElementById('detected_coords').textContent = lat.toFixed(7) + ', ' + lng.toFixed(7);
            document.getElementById('detected_info').classList.remove('hidden');
            // Also fill location field if empty
            const locField = document.querySelector('[name="location"]');
            if (locField && !locField.value) locField.value = addr;
        })
        .catch(() => {});
}
</script>
@endsection

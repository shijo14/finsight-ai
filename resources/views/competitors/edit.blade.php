<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Competitor Details</h2>
            <a href="{{ route('competitors.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </x-slot>

    <!-- Map Scripts -->
    @if(config('services.google.maps_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places" defer></script>
    @else
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white">
                    <form action="{{ route('competitors.update', $competitor) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <!-- Left: Form details -->
                            <div class="lg:col-span-5 space-y-4">
                                <h3 class="font-semibold text-gray-900 text-lg border-b pb-2">Competitor Details</h3>

                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Competitor Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $competitor->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @error('name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="building_name" class="block text-sm font-medium text-gray-700">Building Name</label>
                                    <input type="text" id="building_name" name="building_name" value="{{ old('building_name', $competitor->building_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @error('building_name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" id="address" name="address" value="{{ old('address', $competitor->address) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @error('address') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $competitor->latitude) }}" required class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @error('latitude') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $competitor->longitude) }}" required class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @error('longitude') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="occupancy_rate" class="block text-sm font-medium text-gray-700">Occupancy Rate (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" id="occupancy_rate" name="occupancy_rate" value="{{ old('occupancy_rate', $competitor->occupancy_rate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @error('occupancy_rate') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="average_rent" class="block text-sm font-medium text-gray-700">Avg Monthly Rent ($)</label>
                                        <input type="number" step="0.01" min="0" id="average_rent" name="average_rent" value="{{ old('average_rent', $competitor->average_rent) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @error('average_rent') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes / Remarks</label>
                                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes', $competitor->notes) }}</textarea>
                                    @error('notes') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="pt-4 flex items-center space-x-3">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-700 text-sm transition">
                                        Update Competitor
                                    </button>
                                    <a href="{{ route('competitors.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 font-semibold rounded-md shadow-sm hover:bg-gray-50 text-sm transition">
                                        Cancel
                                    </a>
                                </div>
                            </div>

                            <!-- Right: Interactive Location Picker map -->
                            <div class="lg:col-span-7 flex flex-col space-y-4">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex-1 flex flex-col min-h-[400px]">
                                    <div class="mb-3">
                                        <h4 class="font-semibold text-gray-900 text-sm">Pin Building Location</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">Drag the marker or click on the map to modify coordinates.</p>
                                    </div>
                                    <div id="picker-map" class="flex-1 rounded-lg border border-gray-200 overflow-hidden shadow-inner bg-white" style="min-height: 380px;"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map picker initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isGoogle = typeof google !== 'undefined' && typeof google.maps !== 'undefined';
            
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            const competitorLat = parseFloat(latInput.value);
            const competitorLng = parseFloat(lngInput.value);

            if (isGoogle) {
                const mapEl = document.getElementById('picker-map');
                if (!mapEl) return;
                
                const map = new google.maps.Map(mapEl, {
                    center: { lat: competitorLat, lng: competitorLng },
                    zoom: 13
                });

                const marker = new google.maps.Marker({
                    position: { lat: competitorLat, lng: competitorLng },
                    map: map,
                    draggable: true
                });

                marker.addListener('dragend', (de) => {
                    latInput.value = de.latLng.lat().toFixed(6);
                    lngInput.value = de.latLng.lng().toFixed(6);
                });

                map.addListener('click', (e) => {
                    marker.setPosition(e.latLng);
                    latInput.value = e.latLng.lat().toFixed(6);
                    lngInput.value = e.latLng.lng().toFixed(6);
                });
            } else {
                const mapEl = document.getElementById('picker-map');
                if (!mapEl) return;
                
                const map = L.map('picker-map').setView([competitorLat, competitorLng], 13);
                
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    maxZoom: 20
                }).addTo(map);

                const marker = L.marker([competitorLat, competitorLng], { draggable: true }).addTo(map);

                marker.on('dragend', (de) => {
                    const position = de.target.getLatLng();
                    latInput.value = position.lat.toFixed(6);
                    lngInput.value = position.lng.toFixed(6);
                });

                map.on('click', (e) => {
                    marker.setLatLng(e.latlng);
                    latInput.value = e.latlng.lat.toFixed(6);
                    lngInput.value = e.latlng.lng.toFixed(6);
                });
            }
        });
    </script>
</x-app-layout>

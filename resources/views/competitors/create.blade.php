<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Track Competitor</h2>
            <a href="{{ route('competitors.index') }}" class="text-sm font-semibold text-neutral-500 dark:text-slate-400 hover:text-neutral-900 dark:text-slate-100">Back to List</a>
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
            <div class="glass-card overflow-hidden sm:rounded-xl">
                <div class="p-6">
                    <form action="{{ route('competitors.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <!-- Left: Form details -->
                            <div class="lg:col-span-5 space-y-4">
                                <h3 class="font-semibold text-neutral-900 dark:text-slate-100 text-lg border-b pb-2">Competitor Details</h3>

                                <div>
                                    <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Competitor Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. Skyline Realty">
                                    @error('name') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="building_name" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Building Name</label>
                                    <input type="text" id="building_name" name="building_name" value="{{ old('building_name') }}" required class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. Apex Tower">
                                    @error('building_name') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Address</label>
                                    <input type="text" id="address" name="address" value="{{ old('address') }}" required class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. 123 Main St, New York">
                                    @error('address') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="latitude" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" required class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 bg-neutral-100 dark:bg-white/5 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. 40.7128">
                                        @error('latitude') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="longitude" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" required class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 bg-neutral-100 dark:bg-white/5 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. -74.0060">
                                        @error('longitude') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="occupancy_rate" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Occupancy Rate (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" id="occupancy_rate" name="occupancy_rate" value="{{ old('occupancy_rate') }}" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. 92.5">
                                        @error('occupancy_rate') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="average_rent" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Avg Monthly Rent ($)</label>
                                        <input type="number" step="0.01" min="0" id="average_rent" name="average_rent" value="{{ old('average_rent') }}" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="e.g. 2100">
                                        @error('average_rent') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Notes / Remarks</label>
                                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50 text-sm" placeholder="Any special offers, utilities included, or building condition..."></textarea>
                                    @error('notes') <span class="text-xs text-neutral-500 dark:text-slate-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="pt-4 flex items-center space-x-3">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 btn-primary font-semibold rounded-md shadow-sm text-sm transition">
                                        Save Competitor
                                    </button>
                                    <a href="{{ route('competitors.index') }}" class="btn-secondary text-sm">
                                        Cancel
                                    </a>
                                </div>
                            </div>

                            <!-- Right: Interactive Location Picker map -->
                            <div class="lg:col-span-7 flex flex-col space-y-4">
                                <div class="bg-neutral-100 dark:bg-white/5 rounded-xl p-4 border border-neutral-200 dark:border-white/10 flex-1 flex flex-col min-h-[400px]">
                                    <div class="mb-3">
                                        <h4 class="font-semibold text-neutral-900 dark:text-slate-100 text-sm">Pin Building Location</h4>
                                        <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Click anywhere on the map to set the building's geographic coordinates.</p>
                                    </div>
                                    <div id="picker-map" class="flex-1 rounded-lg border border-neutral-200 dark:border-white/10 overflow-hidden shadow-inner bg-white" style="min-height: 380px;"></div>
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
            
            // Initial center (New York downtown as default)
            let defaultLat = 40.7128;
            let defaultLng = -74.0060;

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            // If coordinates already entered (e.g. validation error return)
            if(latInput.value && lngInput.value) {
                defaultLat = parseFloat(latInput.value);
                defaultLng = parseFloat(lngInput.value);
            }

            if (isGoogle) {
                const mapEl = document.getElementById('picker-map');
                if (!mapEl) return;
                
                const map = new google.maps.Map(mapEl, {
                    center: { lat: defaultLat, lng: defaultLng },
                    zoom: 12
                });

                let marker = null;

                // Place initial marker if coordinates exist
                if(latInput.value && lngInput.value) {
                    marker = new google.maps.Marker({
                        position: { lat: defaultLat, lng: defaultLng },
                        map: map,
                        draggable: true
                    });
                }

                // Click event on map
                map.addListener('click', (e) => {
                    const lat = e.latLng.lat();
                    const lng = e.latLng.lng();

                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);

                    if (marker) {
                        marker.setPosition(e.latLng);
                    } else {
                        marker = new google.maps.Marker({
                            position: e.latLng,
                            map: map,
                            draggable: true
                        });

                        marker.addListener('dragend', (de) => {
                            latInput.value = de.latLng.lat().toFixed(6);
                            lngInput.value = de.latLng.lng().toFixed(6);
                        });
                    }
                });
            } else {
                // Initialize Leaflet picker map
                const mapEl = document.getElementById('picker-map');
                if (!mapEl) return;
                
                const map = L.map('picker-map').setView([defaultLat, defaultLng], 12);
                
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    maxZoom: 20
                }).addTo(map);

                let marker = null;

                // Place initial marker if coordinates exist
                if(latInput.value && lngInput.value) {
                    marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                }

                // Click event on map
                map.on('click', (e) => {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;

                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);

                    if (marker) {
                        marker.setLatLng(e.latlng);
                    } else {
                        marker = L.marker(e.latlng, { draggable: true }).addTo(map);

                        marker.on('dragend', (de) => {
                            const position = de.target.getLatLng();
                            latInput.value = position.lat.toFixed(6);
                            lngInput.value = position.lng.toFixed(6);
                        });
                    }
                });
            }
        });
    </script>
</x-app-layout>

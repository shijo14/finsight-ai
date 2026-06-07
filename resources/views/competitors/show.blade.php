<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Competitor Profile: {{ $competitor->building_name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('competitors.edit', $competitor) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 font-semibold text-sm transition">Edit</a>
                <a href="{{ route('competitors.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition">Back to List</a>
            </div>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left: details -->
                    <div class="lg:col-span-5 space-y-6">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-800">
                                {{ $competitor->name }}
                            </span>
                            <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $competitor->building_name }}</h3>
                            <p class="text-sm text-gray-500 mt-1 flex items-center">
                                <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                {{ $competitor->address }}
                            </p>
                        </div>

                        <!-- Core Metrics Card Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Average Rent</span>
                                <h4 class="text-2xl font-bold text-indigo-700 mt-1">
                                    {{ $competitor->average_rent ? '$' . number_format($competitor->average_rent, 2) : 'N/A' }}
                                </h4>
                                <span class="text-[10px] text-gray-400 mt-0.5 block">per month</span>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Occupancy Rate</span>
                                <h4 class="text-2xl font-bold text-indigo-700 mt-1">
                                    {{ $competitor->occupancy_rate ? $competitor->occupancy_rate . '%' : 'N/A' }}
                                </h4>
                                <span class="text-[10px] text-gray-400 mt-0.5 block">active tenants</span>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 space-y-2">
                            <h4 class="font-semibold text-gray-900 text-sm">Notes & Remarks</h4>
                            @if($competitor->notes)
                                <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $competitor->notes }}"</p>
                            @else
                                <p class="text-sm text-gray-400 italic">No notes recorded for this competitor building.</p>
                            @endif
                        </div>

                        <!-- Coordinates details -->
                        <div class="text-xs text-gray-400 flex items-center justify-between border-t pt-4">
                            <span><strong>Latitude:</strong> {{ $competitor->latitude }}</span>
                            <span><strong>Longitude:</strong> {{ $competitor->longitude }}</span>
                            <span><strong>Added:</strong> {{ $competitor->created_at->format('M j, Y') }}</span>
                        </div>

                        <div class="pt-2">
                            <form action="{{ route('competitors.destroy', $competitor) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold" onclick="return confirm('Remove this competitor building?')">Remove from Tracking</button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Mini Map -->
                    <div class="lg:col-span-7 flex flex-col space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex-1 flex flex-col min-h-[350px]">
                            <div class="mb-3 flex justify-between items-center">
                                <h4 class="font-semibold text-gray-900 text-sm">Building Location</h4>
                                <span class="text-xs text-gray-400">Fixed Point Marker</span>
                            </div>
                            <div id="show-map" class="flex-1 rounded-lg border border-gray-200 overflow-hidden shadow-inner bg-white" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isGoogle = typeof google !== 'undefined' && typeof google.maps !== 'undefined';
            
            const competitorLat = parseFloat("{{ $competitor->latitude }}");
            const competitorLng = parseFloat("{{ $competitor->longitude }}");

            if (isGoogle) {
                const mapEl = document.getElementById('show-map');
                if (!mapEl) return;
                
                const map = new google.maps.Map(mapEl, {
                    center: { lat: competitorLat, lng: competitorLng },
                    zoom: 14
                });

                new google.maps.Marker({
                    position: { lat: competitorLat, lng: competitorLng },
                    map: map,
                    title: "{{ $competitor->building_name }}"
                });
            } else {
                const mapEl = document.getElementById('show-map');
                if (!mapEl) return;
                
                const map = L.map('show-map').setView([competitorLat, competitorLng], 14);
                
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    maxZoom: 20
                }).addTo(map);

                L.marker([competitorLat, competitorLng]).addTo(map)
                    .bindPopup("<strong>{{ $competitor->building_name }}</strong><br>{{ $competitor->address }}")
                    .openPopup();
            }
        });
    </script>
</x-app-layout>

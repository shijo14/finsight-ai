<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Competitor Tracking</h2>
            <a href="{{ route('competitors.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 font-semibold text-sm transition">Track New Competitor</a>
        </div>
    </x-slot>

    <!-- Map Scripts -->
    @if(config('services.google.maps_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places" defer></script>
    @else
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @endif

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800 shadow-sm border border-green-200">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left panel: List of competitors -->
                <div class="lg:col-span-5 flex flex-col space-y-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="font-semibold text-gray-900 text-lg">Competitor Buildings</h3>
                        <p class="text-xs text-gray-500 mt-1">Track location and occupancy of competing properties in your area.</p>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-4" style="max-height: calc(100vh - 300px);">
                        @if($competitors->isEmpty())
                            <div class="bg-white rounded-xl shadow-sm border border-dashed border-gray-300 p-8 text-center">
                                <h4 class="text-base font-semibold text-gray-900">No competitor buildings tracked yet</h4>
                                <p class="mt-2 text-xs text-gray-500">Add competitor details to compare rents and monitor geographical presence.</p>
                                <a href="{{ route('competitors.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition">Get Started</a>
                            </div>
                        @else
                            @foreach($competitors as $competitor)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-indigo-300 transition duration-150 relative">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-800">
                                                {{ $competitor->name }}
                                            </span>
                                            <h4 class="font-bold text-gray-900 mt-2 text-base">{{ $competitor->building_name }}</h4>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ Str::limit($competitor->address, 45) }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @if($competitor->average_rent)
                                                <div class="text-lg font-bold text-gray-900">${{ number_format($competitor->average_rent, 0) }}<span class="text-xs text-gray-500 font-normal">/mo</span></div>
                                            @endif
                                            @if($competitor->occupancy_rate)
                                                <div class="text-xs text-green-600 font-semibold mt-1">{{ $competitor->occupancy_rate }}% Occ.</div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($competitor->notes)
                                        <p class="text-xs text-gray-600 bg-gray-50 rounded-lg p-2.5 mt-3 border border-gray-100 italic">"{{ Str::limit($competitor->notes, 80) }}"</p>
                                    @endif

                                    <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                                        <div class="space-x-3">
                                            <a href="{{ route('competitors.show', $competitor) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">View Details</a>
                                            <a href="{{ route('competitors.edit', $competitor) }}" class="text-gray-600 hover:text-gray-900 font-semibold">Edit</a>
                                        </div>
                                        <form action="{{ route('competitors.destroy', $competitor) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-semibold" onclick="return confirm('Remove this competitor building?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Right panel: Interactive map -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 h-full min-h-[500px] flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-semibold text-gray-800">Map View</span>
                            @if(!config('services.google.maps_key'))
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    Offline Map Active (Leaflet)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-50 text-green-800 border border-green-200">
                                    Google Maps Active
                                </span>
                            @endif
                        </div>
                        <div id="map" class="flex-1 rounded-lg border border-gray-200 overflow-hidden shadow-inner bg-gray-50" style="min-height: 450px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Initialization JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isGoogle = typeof google !== 'undefined' && typeof google.maps !== 'undefined';
            const competitors = @json($competitors);

            // Default center: coordinates for downtown city/general region (centered on NY if empty)
            let centerLat = 40.7128;
            let centerLng = -74.0060;

            if (competitors.length > 0) {
                let latSum = 0;
                let lngSum = 0;
                competitors.forEach(c => {
                    latSum += parseFloat(c.latitude);
                    lngSum += parseFloat(c.longitude);
                });
                centerLat = latSum / competitors.length;
                centerLng = lngSum / competitors.length;
            }

            if (isGoogle) {
                const mapEl = document.getElementById('map');
                if (!mapEl) return;
                const map = new google.maps.Map(mapEl, {
                    center: { lat: centerLat, lng: centerLng },
                    zoom: competitors.length > 0 ? 13 : 11,
                    styles: [
                        { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#e9e9e9" }, { "lightness": 17 }] },
                        { "featureType": "landscape", "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }, { "lightness": 20 }] },
                        { "featureType": "road.highway", "elementType": "geometry.fill", "stylers": [{ "color": "#ffffff" }, { "lightness": 17 }] },
                        { "featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [{ "color": "#ffffff" }, { "lightness": 29 }, { "weight": 0.2 }] },
                        { "featureType": "road.arterial", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }, { "lightness": 18 }] },
                        { "featureType": "road.local", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }, { "lightness": 16 }] },
                        { "featureType": "poi", "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }, { "lightness": 21 }] },
                        { "featureType": "poi.park", "elementType": "geometry", "stylers": [{ "color": "#dedede" }, { "lightness": 21 }] },
                        { "elementType": "labels.text.stroke", "stylers": [{ "visibility": "on" }, { "color": "#ffffff" }, { "lightness": 16 }] },
                        { "elementType": "labels.text.fill", "stylers": [{ "saturation": 36 }, { "color": "#333333" }, { "lightness": 40 }] },
                        { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                        { "featureType": "transit", "elementType": "geometry", "stylers": [{ "color": "#f2f2f2" }, { "lightness": 19 }] },
                        { "featureType": "administrative", "elementType": "geometry.fill", "stylers": [{ "color": "#fefefe" }, { "lightness": 20 }] },
                        { "featureType": "administrative", "elementType": "geometry.stroke", "stylers": [{ "color": "#fefefe" }, { "lightness": 17 }, { "weight": 1.2 }] }
                    ]
                });

                const bounds = new google.maps.LatLngBounds();

                competitors.forEach(c => {
                    const position = { lat: parseFloat(c.latitude), lng: parseFloat(c.longitude) };
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: c.name
                    });

                    bounds.extend(position);

                    const contentString = `
                        <div class="p-2 min-w-[200px]">
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-800 mb-1">${c.name}</span>
                            <h3 class="font-bold text-sm text-gray-900">${c.building_name}</h3>
                            <p class="text-[11px] text-gray-600 mt-1">${c.address}</p>
                            <div class="mt-2 text-xs border-t pt-1.5 flex justify-between">
                                <span><strong>Rent:</strong> ${c.average_rent ? '$' + parseFloat(c.average_rent).toLocaleString() : 'N/A'}</span>
                                <span><strong>Occ:</strong> ${c.occupancy_rate ? c.occupancy_rate + '%' : 'N/A'}</span>
                            </div>
                            <a href="/competitors/${c.id}" class="mt-2.5 block text-center bg-indigo-600 text-white rounded text-[11px] font-semibold py-1 hover:bg-indigo-700 transition">View Details</a>
                        </div>
                    `;

                    const infowindow = new google.maps.InfoWindow({
                        content: contentString
                    });

                    marker.addListener('click', () => {
                        infowindow.open(map, marker);
                    });
                });

                if (competitors.length > 1) {
                    map.fitBounds(bounds);
                }
            } else {
                const mapEl = document.getElementById('map');
                if (!mapEl) return;
                const map = L.map('map').setView([centerLat, centerLng], competitors.length > 0 ? 13 : 11);
                
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    maxZoom: 20
                }).addTo(map);

                const markers = [];

                competitors.forEach(c => {
                    const contentString = `
                        <div class="p-1 min-w-[180px]">
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-800 mb-1">${c.name}</span>
                            <h3 class="font-bold text-sm text-gray-900">${c.building_name}</h3>
                            <p class="text-[11px] text-gray-600 mt-1">${c.address}</p>
                            <div class="mt-2 text-xs border-t pt-1.5 flex justify-between">
                                <span><strong>Rent:</strong> ${c.average_rent ? '$' + parseFloat(c.average_rent).toLocaleString() : 'N/A'}</span>
                                <span><strong>Occ:</strong> ${c.occupancy_rate ? c.occupancy_rate + '%' : 'N/A'}</span>
                            </div>
                            <a href="/competitors/${c.id}" class="mt-2.5 block text-center bg-indigo-600 text-white rounded text-[11px] font-semibold py-1 hover:bg-indigo-700 transition">View Details</a>
                        </div>
                    `;
                    const marker = L.marker([parseFloat(c.latitude), parseFloat(c.longitude)])
                        .addTo(map)
                        .bindPopup(contentString);
                    
                    markers.push(marker);
                });

                if (markers.length > 1) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }
        });
    </script>
</x-app-layout>

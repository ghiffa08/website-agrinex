/**
 * Satellite Map Alpine.js Component
 * Self-contained component untuk Leaflet interactive satellite map
 */
function satelliteMap() {
    return {
        // State
        map: null,
        layer: null,
        provider: 'esri',
        
        // Location coordinates
        lat: -6.9863524,
        lng: 108.6008761,
        
        // Initialize map
        init() {
            // Wait for Leaflet to be available
            if (!window.L) {
                console.warn('Leaflet not loaded yet');
                return;
            }
            
            // Small delay to ensure DOM is ready
            setTimeout(() => {
                this.initMap();
            }, 300);
        },
        
        // Create Leaflet map instance
        initMap() {
            if (this.map) return; // Already initialized
            
            try {
                // Create map
                this.map = L.map('satelliteMap', {
                    zoomControl: true,
                    attributionControl: false
                }).setView([this.lat, this.lng], 18);
                
                // Add default Esri satellite layer
                this.layer = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    {
                        maxZoom: 19,
                        attribution: 'Esri'
                    }
                ).addTo(this.map);
                
                // Add marker with popup
                L.marker([this.lat, this.lng])
                    .bindPopup('<b>Lokasi Sensor</b><br>Lahan Desa Geresik')
                    .addTo(this.map);
                
                // Add circle overlay for coverage area
                L.circle([this.lat, this.lng], {
                    radius: 50,
                    color: '#16a34a',
                    fillColor: '#16a34a',
                    fillOpacity: 0.15,
                    weight: 2
                }).addTo(this.map);
                
            } catch (error) {
                console.error('Error initializing satellite map:', error);
            }
        },
        
        // Switch between Esri and Google satellite providers
        switchProvider(newProvider) {
            if (!this.map || !this.layer) return;
            
            // Update provider state
            this.provider = newProvider;
            
            // Remove current layer
            this.map.removeLayer(this.layer);
            
            // Add new layer based on provider
            if (newProvider === 'esri') {
                this.layer = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    {
                        maxZoom: 19,
                        attribution: 'Esri'
                    }
                ).addTo(this.map);
            } else if (newProvider === 'google') {
                this.layer = L.tileLayer(
                    'https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
                    {
                        maxZoom: 20,
                        attribution: 'Google',
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    }
                ).addTo(this.map);
            }
        }
    };
}

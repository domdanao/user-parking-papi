<!DOCTYPE html>
<html>
<head>
  <title>Parking Slot Provisioning</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      height: 100vh;
    }
    
    #map-container {
      flex: 2;
      height: 100%;
      position: relative;
    }
    
    #map {
      height: 100%;
      width: 100%;
    }
    
    #search-container {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 1;
      width: 400px;
    }
    
    #search-input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
      font-size: 14px;
      box-sizing: border-box;
    }
    
    #form-container {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      border-left: 1px solid #ccc;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    
    input, select {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    
    button {
      background-color: #4285F4;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }
    
    button:hover {
      background-color: #3367D6;
    }

    .info-box {
      padding: 10px;
      margin-top: 10px;
      background-color: #f3f3f3;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <div id="map-container">
    <div id="search-container">
      <input 
        id="search-input" 
        type="text" 
        placeholder="Search for a location..."
      >
    </div>
    <div id="map"></div>
  </div>
  
  <div id="form-container">
    <h2>Provision Parking Slot</h2>
    <p>Click on the map to place a pin at the parking slot location.</p>
    
    <div class="info-box" id="location-info">
      <p><strong>Selected Location:</strong></p>
      <p>Latitude: <span id="lat">Not set</span></p>
      <p>Longitude: <span id="lng">Not set</span></p>
      <p>Address: <span id="address">Not set</span></p>
    </div>
    
    <form id="parking-form">
      <div class="form-group">
        <label for="slot-id">Slot ID</label>
        <input type="text" id="slot-id" required>
      </div>
      
      <div class="form-group">
        <label for="slot-type">Slot Type</label>
        <select id="slot-type">
          <option value="standard">Standard</option>
          <option value="compact">Compact</option>
          <option value="handicap">Handicap</option>
          <option value="electric">Electric Vehicle</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="hourly-rate">Hourly Rate ($)</label>
        <input type="number" id="hourly-rate" min="0" step="0.01" required>
      </div>
      
      <div class="form-group">
        <label for="available-from">Available From</label>
        <input type="time" id="available-from" required>
      </div>
      
      <div class="form-group">
        <label for="available-to">Available To</label>
        <input type="time" id="available-to" required>
      </div>
      
      <input type="hidden" id="latitude" name="latitude">
      <input type="hidden" id="longitude" name="longitude">
      <input type="hidden" id="formatted-address" name="formatted-address">
      
      <button type="submit">Save Parking Slot</button>
    </form>
  </div>

  <script>
    let map;
    let marker;
    let geocoder;
    let searchBox;
    
    function initMap() {
      // Initialize map with default center (you can set this to your city)
      map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 40.7128, lng: -74.0060 }, // New York City
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        streetViewControl: true
      });
      
      // Initialize geocoder for reverse geocoding (coordinates to address)
      geocoder = new google.maps.Geocoder();
      
      // Initialize the search box
      const input = document.getElementById('search-input');
      searchBox = new google.maps.places.SearchBox(input);
      
      // Bias the SearchBox results towards current map's viewport
      map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
      });
      
      // Listen for the event when a user selects a prediction
      searchBox.addListener('places_changed', function() {
        const places = searchBox.getPlaces();
        
        if (places.length === 0) {
          return;
        }
        
        // Get the first place
        const place = places[0];
        
        if (!place.geometry || !place.geometry.location) {
          console.log("Returned place contains no geometry");
          return;
        }
        
        // Center map on the selected place
        map.setCenter(place.geometry.location);
        map.setZoom(17); // Zoom in closer
        
        // Place a marker
        placeMarker(place.geometry.location);
      });
      
      // Add click listener to the map
      map.addListener('click', function(event) {
        placeMarker(event.latLng);
      });
      
      // Try to get user's current location to center the map
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          const userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          map.setCenter(userLocation);
        });
      }
    }
    
    function placeMarker(location) {
      // Remove existing marker if there is one
      if (marker) {
        marker.setMap(null);
      }
      
      // Create new marker
      marker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true, // Allow marker to be dragged for fine-tuning
        animation: google.maps.Animation.DROP
      });
      
      // Update form with new coordinates
      updateLocationInfo(location);
      
      // Add drag end listener for when the marker is moved
      marker.addListener('dragend', function() {
        updateLocationInfo(marker.getPosition());
      });
    }
    
    function updateLocationInfo(location) {
      // Update the displayed coordinates
      document.getElementById('lat').textContent = location.lat().toFixed(6);
      document.getElementById('lng').textContent = location.lng().toFixed(6);
      
      // Update hidden form fields
      document.getElementById('latitude').value = location.lat();
      document.getElementById('longitude').value = location.lng();
      
      // Perform reverse geocoding to get the address
      geocoder.geocode({ 'location': location }, function(results, status) {
        if (status === 'OK' && results[0]) {
          const address = results[0].formatted_address;
          document.getElementById('address').textContent = address;
          document.getElementById('formatted-address').value = address;
        } else {
          document.getElementById('address').textContent = 'Address not found';
          document.getElementById('formatted-address').value = '';
        }
      });
    }
    
    // Form submission handler
    document.getElementById('parking-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Check if location has been set
      if (!document.getElementById('latitude').value) {
        alert('Please select a location on the map first.');
        return;
      }
      
      // Gather all form data
      const formData = {
        slotId: document.getElementById('slot-id').value,
        slotType: document.getElementById('slot-type').value,
        hourlyRate: document.getElementById('hourly-rate').value,
        availableFrom: document.getElementById('available-from').value,
        availableTo: document.getElementById('available-to').value,
        latitude: document.getElementById('latitude').value,
        longitude: document.getElementById('longitude').value,
        address: document.getElementById('formatted-address').value
      };
      
      // Here you would typically send this data to your server
      console.log('Parking slot data to save:', formData);
      
      // Example AJAX call (you'll need to implement your actual API endpoint)
      /*
      fetch('https://your-api-endpoint/parking-slots', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        alert('Parking slot saved successfully!');
        // Reset form or redirect as needed
      })
      .catch(error => {
        console.error('Error saving parking slot:', error);
        alert('Failed to save parking slot. Please try again.');
      });
      */
      
      // For demo purposes, just show success
      alert('Parking slot saved successfully! (Demo mode)');
    });
  </script>
  
  <!-- Include the Places library -->
  <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>
</body>
</html>

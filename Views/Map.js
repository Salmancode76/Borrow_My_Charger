/*
let markers = [];

async function LoadMap() {
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    let center = await getUserLocation();
    let chargerData = [];

    // Initialize the map
    const map = new Map(document.getElementById("map"), {
        zoom: 6,
        center: center || { lat: 37.7749, lng: -122.4194 }, // San Francisco fallback
        mapId: "DEMO_MAP_ID",
    });

    // Function to fetch and update charger data
    function updateChargerData() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../AJAX.php", true);  // Simple GET request without query parameters

        xhr.onload = async function () {
            if (xhr.status === 200) {
                try {
                    chargerData = JSON.parse(xhr.responseText);
                    console.log("Loaded charger data:", chargerData);

                    if (!center && chargerData.length > 0) {
                        center = {
                            lat: parseFloat(chargerData[0].latitude),
                            lng: parseFloat(chargerData[0].longitude)
                        };
                    }

                    // Clear existing markers before adding new ones
                    markers.forEach(marker => marker.setMap(null));
                    markers = [];  // Reset marker array

                    chargerData.forEach(charger => {
                        const markerPosition = {
                            lat: parseFloat(charger.latitude),
                            lng: parseFloat(charger.longitude)
                        };

                        const marker = new AdvancedMarkerElement({
                            map: map,
                            position: markerPosition,
                            title: charger.charge_name
                        });

                        const infoWindow = new google.maps.InfoWindow({
                            content: `<h3>${charger.charge_name}</h3><p>Cost: ${charger.cost}</p>`
                        });

                        marker.addListener('click', () => {
                            infoWindow.open(map, marker);
                        });

                        markers.push(marker);  // Add new marker to the array
                    });

                } catch (error) {
                    console.error("Error parsing charger data:", error);
                }
            } else {
                console.error("Failed to load charger data");
            }
        };

        xhr.send();
    }

    setInterval(updateChargerData, 30000);  // Fetch charger data every 30 seconds

    updateChargerData();  // Initial data load
}

// Function to get the user's current location
function getUserLocation() {
    return new Promise((resolve) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    resolve({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                },
                error => {
                    console.warn("Location not available:", error.message);
                    resolve(null);  // Default to null if location is not available
                }
            );
        } else {
            resolve(null);  // Fallback if geolocation is not supported
        }
    });
}
 * 
 * *
 */

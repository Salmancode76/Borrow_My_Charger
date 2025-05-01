async function LoadMap() {
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    let center = await getUserLocation(); 

    if (!center && chargerData.length > 0) {
        center = {
            lat: parseFloat(chargerData[0].latitude),
            lng: parseFloat(chargerData[0].longitude)
        };
    }

    if (!center) {
        center = { lat: 37.7749, lng: -122.4194 };
    }

    const map = new Map(document.getElementById("map"), {
        zoom: 6,
        center: center,
        mapId: "DEMO_MAP_ID",
    });

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
    });
}

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
                    resolve(null); 
                }
            );
        } else {
            resolve(null); 
        }
    });
}

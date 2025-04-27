<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Homeless Bulletin Board - Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --main-green: #45a049;
      --light-green: #a2f7a5;
      --background-gradient: linear-gradient(135deg, #d4fc79, #96e6a1);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--background-gradient);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      animation: fadeInBody 1.2s ease-out;
    }
    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: rgba(70, 160, 80, 0.9);
      padding: 20px 50px;
      position: sticky;
      top: 0;
      z-index: 1000;
      backdrop-filter: blur(8px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .nav-logo a {
      font-size: 26px;
      font-weight: 700;
      color: white;
      text-decoration: none;
      transition: 0.3s;
    }
    .nav-logo a:hover {
      text-shadow: 0 0 10px #ffffffaa;
    }
    .nav-links {
      display: flex;
      gap: 30px;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      position: relative;
      transition: color 0.3s ease;
    }
    .nav-links a::after {
      content: '';
      position: absolute;
      width: 0%;
      height: 2px;
      bottom: -5px;
      left: 0;
      background-color: white;
      transition: width 0.3s;
    }
    .nav-links a:hover::after {
      width: 100%;
    }
    .nav-links a:hover {
      color: #e0ffe0;
    }
    .section-title {
      text-align: center;
      font-size: 42px;
      font-weight: 800;
      color: #1b4d3e;
      margin-top: 60px;
      animation: fadeUp 1s ease-out forwards;
    }
    .recent-requests {
      width: 90%;
      max-width: 1000px;
      margin: 40px auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      animation: fadeUp 1.2s ease-out forwards;
    }
    .request-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(8px);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .request-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .request-card h3 {
      margin-bottom: 10px;
      font-size: 20px;
      color: #333;
    }
    .request-card p {
      font-size: 16px;
      color: #555;
    }
    .urgent-request {
      border: 2px solid red;
      background-color: #ffe6e6;
    }
    #map-wrapper {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(14px);
      margin: 50px auto;
      padding: 20px;
      border-radius: 25px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      width: 90%;
      max-width: 1200px;
      animation: fadeUp 1.5s ease-out;
    }
    #map-container {
      width: 100%;
      height: 500px;
      border-radius: 20px;
      overflow: hidden;
    }
    @keyframes fadeInBody {
      0% { opacity: 0; }
      100% { opacity: 1; }
    }
    @keyframes fadeUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }
      .nav-links {
        flex-direction: column;
        width: 100%;
        margin-top: 10px;
      }
      .nav-links a {
        margin: 10px 0;
      }
      .section-title {
        font-size: 30px;
      }
    }
  </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="nav-logo">
    <a href="home.php">🏠 Homeless Bulletin Board</a>
  </div>
  <div class="nav-links">
    <a href="home.php">Home</a>
    <a href="login.html">Log In</a>
    <a href="signup.html">Sign Up</a>
    <a href="reqs.html">Submit Request</a>
    <a href="logout.php">Log Out</a> <!-- 🔥 Added logout -->
  </div>
</nav>

<!-- Welcome Message -->
<h2 class="section-title">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

<!-- Recent Requests Section -->
<h2 class="section-title">New Incoming Requests</h2>
<div style="text-align: center; margin-top: 20px;">
  <label for="categoryFilter"><strong>Filter by Category:</strong></label>
  <select id="categoryFilter" onchange="fetchRequests()">
    <option value="All">All Categories</option>
    <option value="Shelter">Shelter</option>
    <option value="Food">Food</option>
    <option value="Clothing">Clothing</option>
    <option value="Medical">Medical</option>
    <option value="Hygiene">Hygiene</option>
    <option value="Job Assistance">Job Assistance</option>
    <option value="Transportation">Transportation</option>
    <option value="Pet Services">Pet Services</option>
    <option value="Other">Other</option>
  </select>

  <label for="sortOrder" style="margin-left: 20px;"><strong>Sort by:</strong></label>
  <select id="sortOrder" onchange="fetchRequests()">
    <option value="newest">Newest First</option>
    <option value="priority">Highest Priority First</option>
  </select>

</div>
<div class="recent-requests" id="recent-requests"></div>

<!-- Map Section -->
<h2 class="section-title">Find Nearby Homeless Shelters</h2>
<section id="map-wrapper">
  <div id="map-container"></div>
</section>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>

<!-- Map Script -->
<script>
  let map, userMarker;
  const shelters = [
    { lat: 40.7128, lng: -74.0060, name: 'Shelter 1', address: '123 Main St, NYC' },
    { lat: 40.730610, lng: -73.935242, name: 'Shelter 2', address: '456 Elm St, NYC' },
    { lat: 40.7580, lng: -73.9855, name: 'Shelter 3', address: '789 Oak St, NYC' }
  ];

  function initMap() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        map = new google.maps.Map(document.getElementById("map-container"), {
          zoom: 12,
          center: userLocation
        });
        userMarker = new google.maps.Marker({
          position: userLocation,
          map: map,
          title: 'You are here',
          icon: {
            url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
          }
        });
        shelters.forEach(function(shelter) {
          const marker = new google.maps.Marker({
            position: { lat: shelter.lat, lng: shelter.lng },
            map: map,
            title: shelter.name
          });
          const infoWindow = new google.maps.InfoWindow({
            content: `<strong>${shelter.name}</strong><br>${shelter.address}`
          });
          marker.addListener('click', function() {
            infoWindow.open(map, marker);
          });
        });
      }, function() {
        alert("Unable to get your location.");
      });
    } else {
      alert("Geolocation is not supported by your browser.");
    }
  }
</script>

<script>
async function fetchRequests() {
  const category = document.getElementById('categoryFilter').value;
  const sortOrder = document.getElementById('sortOrder').value;
  const container = document.getElementById('recent-requests');

  try {
    const response = await fetch('fetch_requests.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ category, sortOrder })
    });

    const requests = await response.json();
    container.innerHTML = '';

    if (requests.length > 0) {
      requests.forEach(request => {
        const urgentClass = request.priorityScore >= 8 ? ' urgent-request' : '';
        container.innerHTML += `
          <div class="request-card${urgentClass}">
            <h3>${request.title}</h3>
            <p>${request.description}</p>
            <p><strong>Category:</strong> ${request.category}</p>
            <p><strong>Priority:</strong> ${request.priorityScore}</p>
          </div>
        `;
      });
    } else {
      container.innerHTML = '<p>No help requests found for this filter.</p>';
    }
  } catch (error) {
    console.error('Error fetching requests:', error);
    container.innerHTML = '<p>Error loading help requests.</p>';
  }
}

// Load all requests initially
window.onload = fetchRequests;
</script>

</body>
</html>
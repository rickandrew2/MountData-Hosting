const apiKey = 'ee6c6136deb54af785f110855242810'; // Replace with your actual WeatherAPI key
const latitude = mountainLatitude; // use your fetched latitude
const longitude = mountainLongitude; // use your fetched longitude

async function fetchWeather() {
    const url = `https://api.weatherapi.com/v1/forecast.json?key=${apiKey}&q=${latitude},${longitude}&days=4&aqi=no&alerts=no`;

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error("Weather data not found.");

        const data = await response.json();
        
        // Debug logging
        console.log('Full API response:', data);
        console.log('Number of forecast days:', data.forecast.forecastday.length);
        
        displayWeather(data);
        displayForecast(data.forecast.forecastday);
    } catch (error) {
        console.error('Error fetching weather:', error);
        document.querySelector(".weather-info").innerHTML = `
            <div class="alert alert-danger">
                Weather information not available: ${error.message}
            </div>`;
    }
}

function displayWeather(data) {
    const { temp_c, condition, wind_mph, wind_kph, wind_dir, feelslike_c, humidity } = data.current;
    const weatherDesc = condition.text;
    const icon = condition.icon;

    document.querySelector(".weather-info").innerHTML = `
        <div class="current-weather-card">
            <div class="weather-main">
                <div class="weather-icon-wrap">
                    <img src="${icon}" alt="Weather icon" class="weather-icon" />
                    <h2 class="temperature">${temp_c}°<span class="temp-unit">C</span></h2>
                </div>
                <div class="weather-details">
                    <p class="weather-desc current">${weatherDesc}</p>
                    <p class="feels-like">Feels like ${feelslike_c}°C</p>
                </div>
            </div>
            <div class="weather-stats">
                <div class="stat-item">
                    <i class="fas fa-wind"></i>
                    <div class="stat-info">
                        <span class="stat-label">Wind</span>
                        <span class="stat-value">${wind_mph} mph</span>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-compass"></i>
                    <div class="stat-info">
                        <span class="stat-label">Direction</span>
                        <span class="stat-value">${wind_dir}</span>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-tint"></i>
                    <div class="stat-info">
                        <span class="stat-label">Humidity</span>
                        <span class="stat-value">${humidity}%</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function displayForecast(forecastDays) {
    // Debug the received data
    console.log('Total forecast days received:', forecastDays.length);
    console.log('Forecast days:', forecastDays);

    // Take only next 2 days instead of 3
    const nextTwoDays = forecastDays.slice(1, 3);  // Changed from nextThreeDays
    
    console.log('Next two days:', nextTwoDays);  // Updated log message

    const forecastContainer = document.querySelector(".forecast-info");
    forecastContainer.innerHTML = `
        <div class="forecast-wrapper">
            <div class="forecast-title">
                <h3>Next 2 Days</h3>  <!-- Changed from "Next 3 Days" -->
                <div class="title-underline"></div>
            </div>
            <div class="forecast-cards">
                ${nextTwoDays.map(day => {  // Changed from nextThreeDays
                    const date = new Date(day.date);
                    const options = { weekday: 'long', month: 'short', day: 'numeric' };
                    const formattedDate = date.toLocaleDateString('en-US', options);
                    const { avgtemp_c, maxtemp_c, mintemp_c, condition, daily_chance_of_rain } = day.day;
                    const weatherDesc = condition.text;
                    const icon = condition.icon;

                    return `
                        <div class="forecast-card">
                            <div class="forecast-day">
                                <h5 class="forecast-date">${formattedDate}</h5>
                                <div class="weather-icon-container">
                                    <img src="${icon}" alt="${weatherDesc}" class="weather-icon" />
                                </div>
                                <p class="weather-desc">${weatherDesc}</p>
                                <div class="temp-details">
                                    <div class="temp-row">
                                        <i class="fas fa-temperature-high"></i>
                                        <span>${maxtemp_c}°C</span>
                                    </div>
                                    <div class="temp-row">
                                        <i class="fas fa-temperature-low"></i>
                                        <span>${mintemp_c}°C</span>
                                    </div>
                                    <div class="temp-row">
                                        <i class="fas fa-cloud-rain"></i>
                                        <span>${daily_chance_of_rain}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        </div>
    `;
}

// Updated CSS styles
const style = document.createElement('style');
style.textContent = `
    /* Current Weather Styles */
    .current-weather-card {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .weather-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .weather-icon-wrap {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .weather-icon {
        width: 64px;
        height: 64px;
        filter: drop-shadow(0 0 8px rgba(255,255,255,0.3));
    }

    .temperature {
        font-size: 3.5rem;
        font-weight: 700;
        margin: 0;
    }

    .temp-unit {
        font-size: 1.5rem;
        opacity: 0.8;
    }

    .weather-desc {
        font-size: 1.2rem;
        margin: 0.5rem 0;
        color: #333;
    }

    .weather-desc.current {
        font-size: 1.8rem;
        color: white;
        font-weight: 600;
    }

    .feels-like {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .weather-stats {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.2);
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }

    .stat-item i {
        font-size: 1.5rem;
        opacity: 0.8;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .stat-value {
        font-weight: 600;
    }

    /* Forecast Styles */
    .forecast-wrapper {
        width: 100%;
        padding: 1rem 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .forecast-title {
        text-align: center;
        margin-bottom: 2rem;
        width: 100%;
    }

    .forecast-title h3 {
        margin: 0;
        color: #333;
        font-size: 1.8rem;
    }

    .title-underline {
        height: 3px;
        width: 100px;
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        margin: 0.5rem auto;
        border-radius: 2px;
    }

    .forecast-cards {
        display: grid;
        grid-template-columns: repeat(2, minmax(250px, 300px));  /* Changed from 3 to 2 columns */
        gap: 2rem;  /* Increased gap for better spacing */
        width: 100%;
        max-width: 650px;  /* Reduced from 900px for 2 cards */
        margin: 0 auto;
        justify-content: center;
    }

    .forecast-card {
        width: 100%;
    }

    .forecast-day {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .forecast-day:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15);
    }

    .forecast-date {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #333;
    }

    .weather-icon-container {
        margin: 1rem 0;
    }

    .temp-details {
        width: 100%;
        margin-top: 1rem;
    }

    .temp-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0.5rem 0;
    }

    .temp-row i {
        color: #4CAF50;
        width: 20px;
    }

    @media (max-width: 768px) {
        .forecast-cards {
            grid-template-columns: minmax(250px, 300px);
            gap: 1.5rem;  /* Adjusted gap for mobile */
        }

        .forecast-day {
            padding: 1.2rem;
        }

        .forecast-title h3 {
            font-size: 1.5rem;
        }

        .weather-main {
            justify-content: center;
            text-align: center;
        }

        .weather-details {
            width: 100%;
            text-align: center;
        }

        .weather-stats {
            justify-content: center;
            gap: 1.5rem;
        }

        .stat-item {
            min-width: 140px;
        }
    }

    @media (max-width: 400px) {
        .stat-item {
            min-width: 100%;
            justify-content: center;
        }
    }
`;

// Add Font Awesome
const fontAwesome = document.createElement('link');
fontAwesome.rel = 'stylesheet';
fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
document.head.appendChild(fontAwesome);

document.head.appendChild(style);

// Call fetchWeather() when the page loads
document.addEventListener("DOMContentLoaded", fetchWeather);

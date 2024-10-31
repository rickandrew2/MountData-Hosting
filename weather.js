const apiKey = 'ee6c6136deb54af785f110855242810'; // Replace with your actual WeatherAPI key
const latitude = mountainLatitude; // use your fetched latitude
const longitude = mountainLongitude; // use your fetched longitude

async function fetchWeather() {
    const url = `https://api.weatherapi.com/v1/forecast.json?key=${apiKey}&q=${latitude},${longitude}&days=5&aqi=no&alerts=no`;

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error("Weather data not found.");

        const data = await response.json();
        displayWeather(data);
        displayForecast(data.forecast.forecastday);
    } catch (error) {
        console.error(error);
        document.querySelector(".weather-info").innerHTML = "Weather information not available";
    }
}

function displayWeather(data) {
    const { temp_c, condition, wind_mph, wind_kph, wind_dir } = data.current;
    const weatherDesc = condition.text;
    const icon = condition.icon;

    document.querySelector(".weather-info").innerHTML = `
        <div class="container current-weather text-center mb-3">
            <h3>Weather Condition</h3>
            <img src="${icon}" alt="Weather icon" class="img-fluid" />
            <p><strong>${weatherDesc}</strong></p>
            <p>Temperature: ${temp_c}°C</p>
            <p>Wind Speed: ${wind_mph} mph (${wind_kph} kph)</p>
            <p>Wind Direction: ${wind_dir}</p>
        </div>
    `;
}

function displayForecast(forecastDays) {
    const forecastContainer = document.querySelector(".forecast-info");
    forecastContainer.innerHTML = ""; // Clear previous forecast data

    // Create a row for forecast items
    forecastContainer.innerHTML += `<div class="row">`;

    forecastDays.forEach(day => {
        const date = new Date(day.date);
        const options = { weekday: 'short', day: '2-digit' };
        const formattedDate = date.toLocaleDateString('en-US', options); // Format: Mon 28, Tue 29, etc.
        const { avgtemp_c, condition } = day.day;
        const weatherDesc = condition.text;
        const icon = condition.icon;

        // Add each forecast day in a responsive column
        forecastContainer.innerHTML += `
            <div class="col-6 col-sm-4 col-md-2 text-center mb-3">
                <div class="forecast-day mx-2">
                    <p><strong>${formattedDate}</strong></p>
                    <img src="${icon}" alt="Weather icon" class="img-fluid" />
                    <p>${weatherDesc}</p>
                    <p>Avg Temp: ${avgtemp_c}°C</p>
                </div>
            </div>
        `;
    });

    forecastContainer.innerHTML += `</div>`; // Close the row
}

// Call fetchWeather() when the page loads
document.addEventListener("DOMContentLoaded", fetchWeather);

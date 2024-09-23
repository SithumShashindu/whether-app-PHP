<?php
function getCityWeather($city) {
    $apiKey = "fa132c9ddd78384ecb76a7eb61aed290";
    $apiUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apiKey . "&units=metric"; // Added units=metric for Celsius

    $weatherData = @file_get_contents($apiUrl);
    if ($weatherData === false) {
        return "Weather data not available.";
    }

    $weatherArray = json_decode($weatherData, true);

    if (isset($weatherArray['cod']) && $weatherArray['cod'] != 200) {
        return "Invalid city name. Please enter a valid city name.";
    }

    $tempC = $weatherArray['main']['temp'];
    $weatherDescription = ucfirst($weatherArray['weather'][0]['description']);
    $weatherMain = $weatherArray['weather'][0]['main'];
    $humidity = $weatherArray['main']['humidity'];
    $windSpeed = $weatherArray['wind']['speed'];

    // Define suitability based on weather conditions
    $suitability = "";
    $percentage = 0;

    if ($weatherMain == "Clear") {
        $suitability = "Clear skies are great for visiting!";
        $percentage = 90;
    } elseif ($weatherMain == "Clouds") {
        $suitability = "Cloudy weather is decent for visiting.";
        $percentage = 70;
    } elseif ($weatherMain == "Rain") {
        $suitability = "It's raining, which might not be ideal for outdoor activities.";
        $percentage = 40;
    } elseif ($weatherMain == "Thunderstorm") {
        $suitability = "Thunderstorms are dangerous. Not suitable for visiting.";
        $percentage = 20;
    } elseif ($weatherMain == "Snow") {
        $suitability = "Snow can be beautiful, but also cold and slippery.";
        $percentage = 50;
    } else {
        $suitability = "Current weather conditions are uncertain.";
        $percentage = 60;
    }

    // Adjust percentage based on temperature
    if ($tempC > 35) {
        $suitability .= " It's very hot outside.";
        $percentage -= 20;
    } elseif ($tempC < 10) {
        $suitability .= " It's quite cold outside.";
        $percentage -= 20;
    }

    $percentage = max(0, min(100, $percentage)); // Ensure percentage is between 0 and 100

    return [
        'temperature' => $tempC,
        'description' => $weatherDescription,
        'humidity' => $humidity,
        'windSpeed' => $windSpeed,
        'suitability' => $suitability,
        'percentage' => $percentage
    ];
}

$cityWeather = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cityName = $_POST["city"];
    $weatherDetails = getCityWeather($cityName);
    if (is_array($weatherDetails)) {
        $cityWeather = "Temperature: " . $weatherDetails['temperature'] . "°C<br>" .
                       "Condition: " . $weatherDetails['description'] . "<br>" .
                       "Humidity: " . $weatherDetails['humidity'] . "%<br>" .
                       "Wind Speed: " . $weatherDetails['windSpeed'] . " m/s<br>" .
                       "Suitability: " . $weatherDetails['suitability'] . "<br>" .
                       "Suitability Percentage: " . $weatherDetails['percentage'] . "%";
    } else {
        $cityWeather = $weatherDetails;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit City in Sri Lanka</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <h1>Check if a City in Sri Lanka is Suitable for Visiting</h1>
        <form method="post">
            Enter City Name: <input type="text" name="city" required>
            <button type="submit">Submit</button>
        </form>
        <h2>City Suitability Report:</h2>
        <p><?php echo $cityWeather; ?></p>
    </div>
</body>
</html>


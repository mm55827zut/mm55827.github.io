/*trochę przepisywalem z yt a troche copilot pododawał*/
class WeatherApp {
  constructor(apiKey, resultsBlockSelector) {
    this.apiKey = apiKey;
    this.resultsBlock = document.querySelector(resultsBlockSelector);
    this.currentWeather = undefined;
    this.forecast = undefined;
  }
  getCurrentWeather(query) {
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${query}&appid=${this.apiKey}&units=metric&lang=pl`;

    const req = new XMLHttpRequest();
    req.open('GET', url, true);
    req.addEventListener('load', () => {
      this.currentWeather = JSON.parse(req.responseText);
      console.log(this.currentWeather);
      this.drawWeather();
    });
    req.send();
  }

  getForecast(query) {
    const url = `https://api.openweathermap.org/data/2.5/forecast?q=${query}&appid=${this.apiKey}&units=metric&lang=pl`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        this.forecast = data.list;
        console.log(this.forecast);
        this.drawWeather();
      });
  }
  getWeather(query) {
    this.resultsBlock.innerHTML = '';
    this.currentWeather = undefined;
    this.forecast = undefined;

    this.getCurrentWeather(query);
    this.getForecast(query);
  }

  drawWeather() {
    this.resultsBlock.innerHTML = '';

    if (this.currentWeather) {
      const date = new Date(this.currentWeather.dt * 1000);
      const dateString = `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
      const temp = this.currentWeather.main.temp;
      const feelsLike = this.currentWeather.main.feels_like;
      const icon = this.currentWeather.weather[0].icon;
      const description = this.currentWeather.weather[0].description;

      const block = this.createBlock(dateString, temp, feelsLike, icon, description);
      this.resultsBlock.appendChild(block);
    }
    if (this.forecast) {
      for (let i = 0; i < this.forecast.length; i++) {
        let weather = this.forecast[i];
        const date = new Date(weather.dt * 1000);
        const dateString = `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
        const temp = weather.main.temp;
        const feelsLike = weather.main.feels_like;
        const icon = weather.weather[0].icon;
        const description = weather.weather[0].description;

        const block = this.createBlock(dateString, temp, feelsLike, icon, description);
        this.resultsBlock.appendChild(block);
      }
    }
  }
  createBlock(date, temperature, feelsLike, icon, description) {
    const weatherBlock = document.createElement('div');
    weatherBlock.className = 'weather-block';

    const dateElement = document.createElement('div');
    dateElement.className = 'date';
    dateElement.innerText = date;
    weatherBlock.appendChild(dateElement);

    const tempElement = document.createElement('div');
    tempElement.className = 'temperature';
    tempElement.innerText = `${temperature} °C`;
    weatherBlock.appendChild(tempElement);

    const feelsLikeElement = document.createElement('div');
    feelsLikeElement.className = 'temperature-feels-like';
    feelsLikeElement.innerText = `Odczuwalna: ${feelsLike} °C`;
    weatherBlock.appendChild(feelsLikeElement);

    const iconElement = document.createElement('img');
    iconElement.className = 'weather-icon';
    iconElement.src = `https://openweathermap.org/img/wn/${icon}@2x.png`;
    weatherBlock.appendChild(iconElement);

    const descriptionElement = document.createElement('div');
    descriptionElement.className = 'weather-description';
    descriptionElement.innerText = description;
    weatherBlock.appendChild(descriptionElement);

    return weatherBlock;
  }
}
const API_KEY = 'bce9bef668668097ac372a6e5c6db4e9';

const app = new WeatherApp(API_KEY, '#results');

document.querySelector('#checkButton').addEventListener('click', () => {
  const location = document.querySelector('#locationInput').value;
  app.getWeather(location);
});

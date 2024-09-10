# Pogodynka - Średnia Temperatura z wybranych miast

Aplikacja oparta na Symfony 7.1.4, która oblicza średnią temperaturę dla wybranych miast w Polsce na podstawie danych
z [WeatherAPI](https://www.weatherapi.com/) dla określonej daty.

## Funkcje

- Obliczenie średniej temperatury dla wskazanych miast we wskazanym dniu.

## Wymagania

- Zainstalowany Docker i Docker Compose na Twoim komputerze.
- Zainstalowany Git.
- Konto w serwisie WeatherAPI i klucz API (możesz utworzyć darmowe
  konto [tutaj](https://www.weatherapi.com/signup.aspx)).

## Instrukcja konfiguracji

### 1. Klonowanie repozytorium

```
cd <folder-projektu>
git clone https://github.com/SAREKRUTACJA/pogodynka.git
```

### 2. Ustawienie pliku środowiskowego

```
cp .env.example .env
```

Następnie otwórz plik .env w edytorze tekstowym i dodaj swój klucz API w polu WEATHER_API_KEY.

```
WEATHER_API_KEY=twoj_klucz_weatherapi
```
Można też zmienić precyzję zaokrąglania średniej do wskazanej ilości miejsc po przecinku

```
AVERAGE_TEMP_PRECISION=1
```

### 3. Uruchonienie aplikacji za pomocą Dockera

```
docker-compose up -d
```

### 4. Instalacja zależności za pomocą Composera

Po uruchomieniu kontenerów Dockera, musisz zainstalować zależności projektu. Uruchom następujące polecenie:

```
docker-compose exec php composer install
```

### 5. Dostęp do aplikacji

```
Aplikacja powinna być teraz dostępna pod adresem http://localhost:8070.
```

### 6. Testowanie API

Aby przetestować API pogodowe, możesz wysłać żądanie POST do endpointu /average_temp np. za pomocą Postman'a:

- Endpoint: http://localhost:8070/average_temp
- Metoda: POST
- Body (JSON):

```
{
  "date": "2024-07-01",
  "cities": ["Poznan", "Gdansk", "Warszawa"]
}
```

Oczekiwana odpowiedź

```
{
  "average_temp": 18.7
}
```

Można też użyć curl:

```
curl -X POST http://localhost:8070/average_temp \
    -H "Content-Type: application/json" \
    -d '{"date": "2024-07-01", "cities": ["Poznan", "Gdansk", "Warszawa"]}'
```
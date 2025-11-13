# Biograf API

Backend API for the Biograf cinema booking application built with PHP. This API works in conjunction with external movie APIs (like TMDB) - it handles cinema venues, showtimes, users, and bookings while movie data is fetched from the external API.

## 🎯 Architecture

**Frontend:** Fetches movie data from external API (TMDB, OMDB, etc.)  
**Backend (This API):** Manages cinemas, showtimes, users, and bookings  
**Integration:** Showtimes reference external movie IDs, frontend combines the data

## 📁 Folder Structure

```
biograf-api/
├── api/
│   ├── cinemas/          # Cinema endpoints
│   │   ├── read.php      # GET all cinemas
│   │   └── read_one.php  # GET single cinema
│   ├── showtimes/        # Showtime endpoints
│   │   └── read.php      # GET showtimes (filter by cinema/movie/date)
│   ├── auth/             # Authentication endpoints
│   │   ├── register.php  # POST register user
│   │   └── login.php     # POST login user
│   └── bookings/         # Booking endpoints
│       ├── create.php    # POST create booking
│       └── read.php      # GET bookings (filter by user)
├── config/
│   └── database.php      # Database connection
├── database_schema.sql   # SQL to create tables
├── test.php             # Test database connection
└── debug.php            # View table structures
```

## 🚀 Setup Instructions

### 1. Database Setup
1. Open phpMyAdmin: `http://localhost:8888/phpMyAdmin`
2. Create database: `biograf_db`
3. Import the SQL file: `database_schema.sql`
   - Or run the SQL queries manually in the SQL tab

### 2. Configure Database Connection
File: `config/database.php`
```php
private $host = "localhost";
private $db_name = "biograf_db";
private $username = "root";
private $password = "root"; // MAMP default
```

### 3. Test the API
- **Connection test:** `http://localhost:8888/biograf-api/test.php`
- **Debug tables:** `http://localhost:8888/biograf-api/debug.php`
- **Get cinemas:** `http://localhost:8888/biograf-api/api/cinemas/read.php`

## 📡 API Endpoints

### Cinemas
```
GET  /api/cinemas/read.php           - Get all cinemas
GET  /api/cinemas/read_one.php?id=1  - Get specific cinema
```

### Showtimes
```
GET  /api/showtimes/read.php                          - Get all showtimes
GET  /api/showtimes/read.php?cinema_id=1              - Filter by cinema
GET  /api/showtimes/read.php?movie_id=550             - Filter by movie (external API ID)
GET  /api/showtimes/read.php?date=2025-11-10          - Filter by date
GET  /api/showtimes/read.php?cinema_id=1&date=2025-11-10  - Combine filters
```

### Authentication
```
POST /api/auth/register.php
Body: { "name": "John Doe", "email": "john@example.com", "password": "pass123" }

POST /api/auth/login.php
Body: { "email": "john@example.com", "password": "pass123" }
```

### Bookings
```
POST /api/bookings/create.php
Body: { "user_id": 1, "showtime_id": 1, "seats": 2, "total_price": 240.00 }

GET  /api/bookings/read.php              - Get all bookings
GET  /api/bookings/read.php?user_id=1    - Get user's bookings
```

## 🔗 Integration with External Movie API

The `showtimes` table stores:
- `movie_id` - The external API's movie ID (e.g., TMDB movie ID)
- `movie_title` - Cached title for quick reference (optional)

**Frontend Integration Example:**
```javascript
// 1. Get showtimes from this API
const showtimes = await fetch('http://localhost:8888/biograf-api/api/showtimes/read.php?cinema_id=1');

// 2. For each showtime, fetch movie details from TMDB
const movieDetails = await fetch(`https://api.themoviedb.org/3/movie/${showtime.movie_id}?api_key=YOUR_KEY`);

// 3. Combine the data in your frontend
const combined = {
  ...showtime,
  movie: movieDetails
};
```

## 📊 Database Schema

- **cinemas** - Cinema venues (name, address, city, total_seats)
- **users** - User accounts (name, email, password)
- **showtimes** - Movie showtimes (cinema_id, movie_id, show_date, show_time, price)
- **bookings** - User bookings (user_id, showtime_id, seats, total_price)

## 🧪 Testing with Postman

Import these requests:
1. GET cinemas
2. GET showtimes with filters
3. POST register (creates user)
4. POST login (authenticates user)
5. POST create booking
6. GET user bookings

## 📝 Notes

- All responses are in JSON format
- CORS headers are enabled for cross-origin requests
- Passwords are hashed using `password_hash()`
- Foreign keys maintain data integrity
- Sample data is included in `database_schema.sql`

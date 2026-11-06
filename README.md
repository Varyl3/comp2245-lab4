# COMP2245 Lab 4

This is Lab 4 for Your Name.

A library catalogue search. The page is `index.html`. JavaScript in `app.js`
asks `subjects.php` and `books.php` for JSON with `fetch()`, and the PHP files
read the `catalogue` database in MySQL.

## Run it

1. Load the database as root. You can run this again at any time to reset it.

   ```sh
   mysql -u root -p < catalogue.sql
   ```

2. Copy the config. `config.php` is in `.gitignore`, so it isn't committed.

   ```sh
   cp config.example.php config.php
   ```

3. Start PHP's built-in server in this folder:

   ```sh
   php -S localhost:8000
   ```

4. Open <http://localhost:8000/>. Opening `index.html` straight from the
   file system won't work, because `fetch()` needs the server.

## Browsers tested

- 

# Disconnect - A Digital Wellbeing Tracker

A minimalist web-based habit tracker that helps users reduce unintentional screen time by logging platform avoidance, tracking mood, and visualising patterns over time.

Built with PHP, MySQL, HTML, CSS, and JavaScript.

## Requirements

Before running this project, you will need the following installed:

- [XAMPP](https://www.apachefriends.org) — bundles Apache, MySQL, and PHP in one installation
- [MySQL Workbench](https://dev.mysql.com/downloads/workbench/) — for running the database setup file
- A modern web browser (Chrome/Safari recommended)

## Setup Instructions

### Step 1 — Install and start XAMPP

1. Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org)
2. Open the XAMPP Manager
   - **Mac:** `/Applications/XAMPP/manager-osx.app`
   - **Windows:** Open the XAMPP Control Panel from the Start menu
3. Start both **Apache** and **MySQL**

### Step 2 — Place the project files

Clone or download this repository and place the `Disconnect` folder inside the XAMPP `htdocs` directory:

- **Mac:** `/Applications/XAMPP/htdocs/Disconnect`
- **Windows:** `C:/xampp/htdocs/Disconnect`

Your folder structure should look like this:
htdocs/
  Disconnect/
    db.php
    track.php
    mood.php
    analytics.php
    charts.js
    styles.css
    setup.sql

### Step 3 — Set up the database

1. Open **MySQL Workbench**
2. Connect to your local instance — use these credentials:
   - Host: `127.0.0.1`
   - Port: `3306`
   - Username: `root`
   - Password: *(leave blank)*
3. Click **Open a SQL Script** from the File menu, navigate to the `Disconnect` folder, and open `setup.sql`
4. Click the lightning bolt icon (or press `Cmd+Enter` on Mac / `Ctrl+Enter` on Windows) to run the script
5. This will automatically create the `disconnect` database, create the `daily_logs` table, and insert sample data for demonstration purposes

### Step 4 — Open the application

Open your browser and go to:
http://localhost/Disconnect/track.php

You should see the Track page with a streak counter, heatmap, and logging form.

## Pages

1. Track - `http://localhost/Disconnect/track.php` (Home page - log platform avoidance, view streak and heatmap)
2. Log Mood - `http://localhost/Disconnect/mood.php` (Log daily mood rating & add notes)
3. Analytics - `http://localhost/Disconnect/analytics.php` (View mood trends & weekly success rate charts)

## Troubleshooting

**The page shows a 403 Forbidden error**

The project folder is not in the correct location. Make sure it is placed directly inside `htdocs` as shown in Step 2. On Mac, you may also need to fix file permissions by opening Terminal and running:

```bash
chmod -R 755 /Applications/XAMPP/htdocs/Disconnect
```

**The page shows a 500 error**

Add these two lines to the very top of `track.php` temporarily to see the actual error message:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

The most common cause is that MySQL is not running — check the XAMPP Manager and ensure both Apache and MySQL show as running.

**The page loads but shows "Connection failed"**

The database has not been set up yet. Return to Step 3 and run `setup.sql` in MySQL Workbench.

**The page loads but there is no CSS styling**

Check that `styles.css` is in the same folder as `track.php`. The filename is case-sensitive on Mac — it must be `styles.css` not `Styles.css` or `style.css`.

**Charts on the analytics page are not showing**

The charts require an internet connection to load Chart.js from a CDN. Ensure you are connected to the internet when viewing the analytics page.

## Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Server-side logic and database communication |
| MySQL | Database storage |
| HTML / CSS | Page structure and styling |
| JavaScript | Chart interactivity |
| Chart.js | Data visualisation (loaded via CDN) |
| XAMPP | Local development server |

---

## Project Structure
Disconnect/
db.php — database connection
track.php — home page with streak, heatmap, and avoidance logging
mood.php — mood logging page
analytics.php — analytics page with charts and summary stats
charts.js — Chart.js configuration for both charts
styles.css — all styles across every page
setup.sql — run once in MySQL Workbench to create the database

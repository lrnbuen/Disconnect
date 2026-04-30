-- create the database
CREATE DATABASE IF NOT EXISTS disconnect;
USE disconnect;

-- create the single table that stores all logs
CREATE TABLE IF NOT EXISTS daily_logs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    log_date   DATE         NOT NULL,
    platform   VARCHAR(100) NOT NULL DEFAULT '',
    avoided    TINYINT(1)   NOT NULL DEFAULT 0,
    mood       TINYINT               DEFAULT NULL,
    notes      TEXT                  DEFAULT NULL
);

-- disable safe update mode to allow the delete
SET SQL_SAFE_UPDATES = 0;

-- clear any existing data
DELETE FROM daily_logs;

-- 6 weeks ago
INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES
(CURDATE() - INTERVAL 41 DAY, 'Instagram', 0, 2, 'opened it without thinking'),
(CURDATE() - INTERVAL 40 DAY, 'Instagram', 0, 2, NULL),
(CURDATE() - INTERVAL 39 DAY, 'TikTok', 1, 3, NULL),
(CURDATE() - INTERVAL 38 DAY, 'Instagram', 0, 2, 'bored in the evening'),
(CURDATE() - INTERVAL 37 DAY, 'TikTok', 1, 3, NULL),
(CURDATE() - INTERVAL 36 DAY, 'Instagram', 0, 2, NULL),
(CURDATE() - INTERVAL 35 DAY, 'Instagram', 1, 3, 'felt more present today');

-- 5 weeks ago
INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES
(CURDATE() - INTERVAL 34 DAY, 'Instagram', 1, 3, NULL),
(CURDATE() - INTERVAL 33 DAY, 'TikTok', 0, 2, NULL),
(CURDATE() - INTERVAL 32 DAY, 'Instagram', 1, 4, 'read a book instead'),
(CURDATE() - INTERVAL 31 DAY, 'Instagram', 1, 3, NULL),
(CURDATE() - INTERVAL 30 DAY, 'Reddit', 1, 4, NULL),
(CURDATE() - INTERVAL 29 DAY, 'Instagram', 0, 3, NULL),
(CURDATE() - INTERVAL 28 DAY, 'Instagram', 1, 4, NULL);

-- 4 weeks ago
INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES
(CURDATE() - INTERVAL 27 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 26 DAY, 'Instagram', 1, 4, 'felt focused'),
(CURDATE() - INTERVAL 25 DAY, 'TikTok', 1, 5, 'best mood in weeks'),
(CURDATE() - INTERVAL 24 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 23 DAY, 'Reddit', 0, 3, 'fell back into old habits'),
(CURDATE() - INTERVAL 22 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 21 DAY, 'Instagram', 1, 5, NULL);

-- 3 week 3 ago
INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES
(CURDATE() - INTERVAL 20 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 19 DAY, 'TikTok', 1, 5, NULL),
(CURDATE() - INTERVAL 18 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 17 DAY, 'Instagram', 1, 5, 'went for a walk instead'),
(CURDATE() - INTERVAL 16 DAY, 'Reddit', 1, 4, NULL),
(CURDATE() - INTERVAL 15 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 14 DAY, 'Instagram', 1, 5, NULL);

-- 2 weeks ago
INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES
(CURDATE() - INTERVAL 13 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 12 DAY, 'TikTok', 1, 5, NULL),
(CURDATE() - INTERVAL 11 DAY, 'Instagram', 1, 5, 'really clear head'),
(CURDATE() - INTERVAL 10 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 9 DAY, 'Reddit', 1, 5, NULL),
(CURDATE() - INTERVAL 8 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 7 DAY, 'Instagram', 1, 5, NULL);

-- last week 
INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES
(CURDATE() - INTERVAL 6 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 5 DAY, 'TikTok', 1, 5, NULL),
(CURDATE() - INTERVAL 4 DAY, 'Instagram', 1, 5, NULL),
(CURDATE() - INTERVAL 3 DAY, 'Instagram', 1, 4, NULL),
(CURDATE() - INTERVAL 2 DAY, 'Reddit', 1, 5, NULL),
(CURDATE() - INTERVAL 1 DAY, 'Instagram', 1, 5, NULL),
(CURDATE(), 'Instagram', 1, 4, 'feeling great');
<?php
require 'db.php';

$active  = 'mood';
$message = '';
$msgType = '';

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mood  = (int)$_POST['mood'];
    $notes = $conn->real_escape_string(trim($_POST['notes'] ?? ''));
    $today = date('Y-m-d');

    // validate mood is between 1 and 5
    if ($mood < 1 || $mood > 5) {
        $message = 'please select a mood rating.';
        $msgType = 'error';
    } else {
        // check if a mood has already been logged today
        $check = $conn->query("SELECT id FROM daily_logs WHERE log_date='$today' AND mood IS NOT NULL");

        if ($check->num_rows > 0) {
            // update the existing mood entry for today
            $conn->query("UPDATE daily_logs SET mood=$mood, notes='$notes' WHERE log_date='$today' AND mood IS NOT NULL");
        } else {
            // insert a new mood entry
            $conn->query("INSERT INTO daily_logs (log_date, platform, avoided, mood, notes) VALUES ('$today', '', 0, $mood, '$notes')");
        }

        header('Location: mood.php?saved=1');
        exit;
    }
}

// show confirmation after redirect
if (isset($_GET['saved'])) {
    $message = 'mood logged for today';
    $msgType = 'success!';
}

// get the last 7 mood entries for the right column
$history    = $conn->query("SELECT log_date, mood, notes FROM daily_logs WHERE mood IS NOT NULL ORDER BY log_date DESC LIMIT 7");
$moodLabels = [1 => 'rough', 2 => 'low', 3 => 'okay', 4 => 'good', 5 => 'great'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disconnect — Log Mood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1 class="site-title">Disconnect</h1>
    <nav class="main-nav">
        <a href="track.php"     class="<?= $active === 'track'     ? 'active' : '' ?>">Track</a>
        <a href="mood.php"      class="<?= $active === 'mood'      ? 'active' : '' ?>">Log Mood</a>
        <a href="analytics.php" class="<?= $active === 'analytics' ? 'active' : '' ?>">Analytics</a>
    </nav>
</header>

<main class="two-col">

    <!-- left column: mood form -->
    <div class="col-left">

        <!-- feedback message -->
        <?php if ($message): ?>
            <div class="message <?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- mood rating form -->
        <div class="card stretch-card">
            <h2 class="card-title">how are you feeling today?</h2>

            <form method="POST" action="mood.php">

                <!-- five mood buttons — each submits the form with a different value -->
                <div class="mood-buttons">
                    <div class="mood-btn-group">
                        <button type="submit" name="mood" value="1" class="btn-mood m1">1</button>
                        <span class="mood-btn-label">rough</span>
                    </div>
                    <div class="mood-btn-group">
                        <button type="submit" name="mood" value="2" class="btn-mood m2">2</button>
                        <span class="mood-btn-label">low</span>
                    </div>
                    <div class="mood-btn-group">
                        <button type="submit" name="mood" value="3" class="btn-mood m3">3</button>
                        <span class="mood-btn-label">okay</span>
                    </div>
                    <div class="mood-btn-group">
                        <button type="submit" name="mood" value="4" class="btn-mood m4">4</button>
                        <span class="mood-btn-label">good</span>
                    </div>
                    <div class="mood-btn-group">
                        <button type="submit" name="mood" value="5" class="btn-mood m5">5</button>
                        <span class="mood-btn-label">great</span>
                    </div>
                </div>

                <!-- optional notes field -->
                <div class="field notes-field">
                    <label for="notes">add notes or triggers (optional)</label>
                    <textarea name="notes" id="notes" placeholder="anything on your mind today?"></textarea>
                </div>

            </form>
        </div>

    </div>

    <!-- right column: mood history — stretches to match left column height -->
    <div class="col-right">
        <div class="card full-height-card">
            <h2 class="card-title">recent mood</h2>

            <?php if ($history->num_rows === 0): ?>
                <p class="empty-msg">no mood entries yet — rate your mood on the left</p>
            <?php else: ?>
                <ul class="log-list">
                    <?php while ($row = $history->fetch_assoc()): ?>
                        <li class="log-item mood-entry mood-<?= $row['mood'] ?>">
                            <span class="log-date"><?= $row['log_date'] ?></span>
                            <span class="log-platform"><?= $moodLabels[$row['mood']] ?></span>
                            <span class="mood-pip m<?= $row['mood'] ?>"><?= $row['mood'] ?>/5</span>
                            <?php if ($row['notes']): ?>
                                <span class="mood-note"><?= htmlspecialchars($row['notes']) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

</main>

<footer>Disconnect — track your attention</footer>

</body>
</html>

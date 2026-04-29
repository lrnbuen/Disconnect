<?php
require 'db.php';

$active  = 'track';
$message = '';
$msgType = '';

//handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $avoided = (int)$_POST['avoided'];
    $today   = date('Y-m-d');

    //use typed value if "other" was selected, otherwise use the dropdown
    if ($_POST['platform_select'] === 'other') {
        $platform = $conn->real_escape_string(trim($_POST['custom_platform'] ?? ''));
    } else {
        $platform = $conn->real_escape_string($_POST['platform_select']);
    }

    if (empty($platform)) {
        $message = 'please enter a platform name';
        $msgType = 'error';
    } else {
        //check if this platform has already been logged today
        $check = $conn->query("SELECT id FROM daily_logs WHERE log_date='$today' AND platform='$platform'");

        if ($check->num_rows > 0) {
            $message = "you've already logged $platform today";
            $msgType = 'warning';
        } else {
            //save entry to database
            $conn->query("INSERT INTO daily_logs (log_date, platform, avoided) VALUES ('$today', '$platform', $avoided)");
            header('Location: track.php?saved=1');
            exit;
        }
    }
}

//show confirmation message after redirect
if (isset($_GET['saved'])) {
    $message = 'logged!';
    $msgType = 'success';
}

//calculate streak — count consecutive avoided days backwards from today
$streakQuery = $conn->query("SELECT log_date, avoided FROM daily_logs ORDER BY log_date DESC");
$logsByDate  = [];

while ($row = $streakQuery->fetch_assoc()) {
    $d = $row['log_date'];
    if (!isset($logsByDate[$d])) $logsByDate[$d] = 1;
    if ($row['avoided'] == 0)   $logsByDate[$d] = 0;
}

$streak   = 0;
$checkDay = date('Y-m-d');
while (isset($logsByDate[$checkDay]) && $logsByDate[$checkDay] == 1) {
    $streak++;
    $checkDay = date('Y-m-d', strtotime($checkDay . ' -1 day'));
}

//build heatmap data for the last 84 days (12 weeks)
$heatQuery  = $conn->query("SELECT log_date, avoided FROM daily_logs WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 83 DAY)");
$heatByDate = [];

while ($row = $heatQuery->fetch_assoc()) {
    $d = $row['log_date'];
    if (!isset($heatByDate[$d])) $heatByDate[$d] = 1;
    if ($row['avoided'] == 0)   $heatByDate[$d] = 0;
}

//get 7 most recent log entries for the right column
$recent = $conn->query("SELECT log_date, platform, avoided FROM daily_logs ORDER BY log_date DESC, id DESC LIMIT 7");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disconnect — Track</title>
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

    <!-- left column: streak, heatmap, form -->
    <div class="col-left">

        <!-- streak counter -->
        <div class="card streak-card">
            <span class="streak-number"><?= $streak ?></span>
            <span class="streak-label">day streak</span>
        </div>

        <!-- heatmap grid -->
        <div class="card">
            <p class="card-label">last 12 weeks</p>
            <div class="heatmap">
                <?php for ($i = 83; $i >= 0; $i--):
                    $d     = date('Y-m-d', strtotime("-$i days"));
                    $class = 'sq empty';
                    if (isset($heatByDate[$d])) {
                        $class = $heatByDate[$d] == 1 ? 'sq hit' : 'sq miss';
                    }
                ?>
                    <div class="<?= $class ?>" title="<?= $d ?>"></div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- feedback message -->
        <?php if ($message): ?>
            <div class="message <?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- logging form -->
        <div class="card">
            <h2 class="card-title">how did today go?</h2>

            <form method="POST" action="track.php">

                <div class="field">
                    <label for="platformSelect">platform</label>
                    <select name="platform_select" id="platformSelect">
                        <option value="Instagram">Instagram</option>
                        <option value="TikTok">TikTok</option>
                        <option value="YouTube">YouTube</option>
                        <option value="Twitter">Twitter</option>
                        <option value="Reddit">Reddit</option>
                        <option value="Gaming">Gaming</option>
                        <option value="other">Other — type your own</option>
                    </select>
                </div>

                <!-- shown only when "other" is selected -->
                <div class="field" id="customField" style="display:none;">
                    <label for="customPlatform">platform name</label>
                    <input type="text" name="custom_platform" id="customPlatform" placeholder="e.g. Netflix, Facebook, etc...">
                </div>

                <!-- green = avoided, brown = used -->
                <div class="btn-row">
                    <button type="submit" name="avoided" value="1" class="btn btn-success">✓ avoided it</button>
                    <button type="submit" name="avoided" value="0" class="btn btn-used">✗ used it</button>
                </div>

            </form>

            <p class="sub-prompt">how are you feeling? <a href="mood.php">log your mood →</a></p>
        </div>

    </div>

    <!-- right column: recent logs — stretches to match left column height -->
    <div class="col-right">
        <div class="card full-height-card">
            <h2 class="card-title">recent logs</h2>

            <?php if ($recent->num_rows === 0): ?>
                <p class="empty-msg">no logs yet — start tracking on the left</p>
            <?php else: ?>
                <ul class="log-list">
                    <?php while ($row = $recent->fetch_assoc()): ?>
                        <li class="log-item <?= $row['avoided'] ? 'avoided' : 'used' ?>">
                            <span class="log-date"><?= $row['log_date'] ?></span>
                            <span class="log-platform"><?= htmlspecialchars($row['platform']) ?></span>
                            <span class="log-status"><?= $row['avoided'] ? '✓ avoided' : '✗ used' ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

</main>

<footer>Disconnect — track your attention</footer>

<!-- show/hide custom platform input -->
<script>
    const dropdown    = document.getElementById('platformSelect');
    const customField = document.getElementById('customField');
    const customInput = document.getElementById('customPlatform');

    dropdown.addEventListener('change', function () {
        const isOther = this.value === 'other';
        customField.style.display = isOther ? 'block' : 'none';
        if (isOther) customInput.focus();
        else customInput.value = '';
    });
</script>

</body>
</html>

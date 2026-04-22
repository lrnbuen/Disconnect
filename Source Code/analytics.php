<?php
require 'db.php';

$active = 'analytics';

// fetch mood data for the last 30 days (for the line chart)
$moodQuery  = $conn->query("SELECT log_date, mood FROM daily_logs WHERE mood IS NOT NULL ORDER BY log_date ASC LIMIT 30");
$moodDates  = [];
$moodValues = [];

while ($row = $moodQuery->fetch_assoc()) {
    $moodDates[]  = $row['log_date'];
    $moodValues[] = (int)$row['mood'];
}

// fetch weekly success rate for the last 6 weeks (for the bar chart)
$weekQuery = $conn->query("
    SELECT
        YEAR(log_date)                    AS yr,
        WEEK(log_date, 1)                 AS wk,
        MIN(log_date)                     AS week_start,
        ROUND(AVG(avoided) * 100)         AS success_pct
    FROM daily_logs
    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 6 WEEK)
    GROUP BY yr, wk
    ORDER BY yr ASC, wk ASC
");
$weekLabels  = [];
$weekSuccess = [];

while ($row = $weekQuery->fetch_assoc()) {
    $weekLabels[]  = date('d M', strtotime($row['week_start']));
    $weekSuccess[] = (int)$row['success_pct'];
}

// average mood on avoided days vs non-avoided days
$avgQuery = $conn->query("
    SELECT avoided, ROUND(AVG(mood), 1) AS avg_mood
    FROM daily_logs
    WHERE mood IS NOT NULL
    GROUP BY avoided
");
$avgMoodAvoided = '—';
$avgMoodUsed    = '—';

while ($row = $avgQuery->fetch_assoc()) {
    if ($row['avoided'] == 1) $avgMoodAvoided = $row['avg_mood'];
    else                      $avgMoodUsed    = $row['avg_mood'];
}

// total logs and overall success rate
$totals      = $conn->query("SELECT COUNT(*) AS total, SUM(avoided) AS successes FROM daily_logs WHERE platform != ''")->fetch_assoc();
$totalLogs   = $totals['total'] ?? 0;
$successRate = $totalLogs > 0 ? round(($totals['successes'] / $totalLogs) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disconnect — Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<main>

    <!-- summary stat cards -->
    <div class="stats-row">
        <div class="stat-card">
            <span class="stat-num"><?= $successRate ?>%</span>
            <span class="stat-lbl">overall success rate</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?= $avgMoodAvoided ?></span>
            <span class="stat-lbl">avg mood when avoided</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?= $avgMoodUsed ?></span>
            <span class="stat-lbl">avg mood when used</span>
        </div>
        <div class="stat-card">
            <span class="stat-num"><?= $totalLogs ?></span>
            <span class="stat-lbl">total logs</span>
        </div>
    </div>

    <!-- mood over time chart -->
    <div class="card">
        <h2 class="card-title">mood over time</h2>
        <?php if (empty($moodDates)): ?>
            <p class="empty-msg">no mood data yet — start logging on the mood page.</p>
        <?php else: ?>
            <div class="chart-wrap">
                <canvas id="moodChart"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <!-- weekly success rate chart -->
    <div class="card">
        <h2 class="card-title">weekly success rate</h2>
        <?php if (empty($weekLabels)): ?>
            <p class="empty-msg">no tracking data yet — start logging on the track page.</p>
        <?php else: ?>
            <div class="chart-wrap">
                <canvas id="successChart"></canvas>
            </div>
        <?php endif; ?>
    </div>

</main>

<footer>Disconnect — track your attention</footer>

<script src="charts.js"></script>

<!-- pass php data to charts.js as javascript variables -->
<script>
    const moodDates   = <?= json_encode($moodDates) ?>;
    const moodValues  = <?= json_encode($moodValues) ?>;
    const weekLabels  = <?= json_encode($weekLabels) ?>;
    const weekSuccess = <?= json_encode($weekSuccess) ?>;

    if (moodDates.length > 0)  buildMoodChart(moodDates, moodValues);
    if (weekLabels.length > 0) buildSuccessChart(weekLabels, weekSuccess);
</script>

</body>
</html>

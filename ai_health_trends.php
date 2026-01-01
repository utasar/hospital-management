<?php
session_start();
include("dbconnection.php");
include("ai_modules/ai_api_handler.php");

$aiHandler = new AIAPIHandler($con);

// Check if patient is logged in
if(!isset($_SESSION['patientid'])) {
    header("Location: patientlogin.php");
    exit();
}

$patientId = $_SESSION['patientid'];

// Get patient information
$sql = "SELECT * FROM patient WHERE patientid = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$patient = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get health trends
$sql = "SELECT * FROM ai_health_trends WHERE patientid = ? ORDER BY recorded_at DESC LIMIT 20";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$healthTrends = [];
while($row = mysqli_fetch_assoc($result)) {
    $healthTrends[] = $row;
}
mysqli_stmt_close($stmt);

// Get lifestyle recommendations
$sql = "SELECT * FROM ai_lifestyle_recommendations WHERE patientid = ? AND status = 'Active' ORDER BY created_at DESC";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lifestyleRecs = [];
while($row = mysqli_fetch_assoc($result)) {
    $lifestyleRecs[] = $row;
}
mysqli_stmt_close($stmt);

// Generate sample data if none exists
if(empty($healthTrends)) {
    $sampleMetrics = [
        ['metric' => 'Blood Pressure', 'value' => '120/80', 'trend' => 'Stable'],
        ['metric' => 'Heart Rate', 'value' => '72 bpm', 'trend' => 'Stable'],
        ['metric' => 'Weight', 'value' => '70 kg', 'trend' => 'Improving'],
        ['metric' => 'Blood Sugar', 'value' => '95 mg/dL', 'trend' => 'Stable']
    ];
    
    foreach($sampleMetrics as $metric) {
        $sql = "INSERT INTO ai_health_trends (patientid, metric_name, metric_value, trend_direction) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "isss", $patientId, $metric['metric'], $metric['value'], $metric['trend']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    // Reload trends
    $sql = "SELECT * FROM ai_health_trends WHERE patientid = ? ORDER BY recorded_at DESC LIMIT 20";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $healthTrends = [];
    while($row = mysqli_fetch_assoc($result)) {
        $healthTrends[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Generate lifestyle recommendations if none exist
if(empty($lifestyleRecs)) {
    $sampleRecs = [
        ['category' => 'Exercise', 'recommendation' => 'Start with 20 minutes of brisk walking daily', 'factors' => 'Based on your current fitness level'],
        ['category' => 'Diet', 'recommendation' => 'Include more green leafy vegetables in your meals', 'factors' => 'To improve overall nutrition'],
        ['category' => 'Sleep', 'recommendation' => 'Maintain a consistent sleep schedule of 7-8 hours', 'factors' => 'For better recovery and health'],
        ['category' => 'Hydration', 'recommendation' => 'Drink at least 2 liters of water daily', 'factors' => 'To maintain proper hydration']
    ];
    
    foreach($sampleRecs as $rec) {
        $sql = "INSERT INTO ai_lifestyle_recommendations (patientid, category, recommendation, personalization_factors) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "isss", $patientId, $rec['category'], $rec['recommendation'], $rec['factors']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    // Reload recommendations
    $sql = "SELECT * FROM ai_lifestyle_recommendations WHERE patientid = ? AND status = 'Active' ORDER BY created_at DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $lifestyleRecs = [];
    while($row = mysqli_fetch_assoc($result)) {
        $lifestyleRecs[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Trends & Analytics</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .trends-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .trends-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .trends-header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .trends-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .metric-box {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .trend-improving { border-left-color: #28a745; }
        .trend-stable { border-left-color: #ffc107; }
        .trend-declining { border-left-color: #dc3545; }
        
        .trend-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-block;
        }
        
        .trend-badge.improving { background: #d4edda; color: #155724; }
        .trend-badge.stable { background: #fff3cd; color: #856404; }
        .trend-badge.declining { background: #f8d7da; color: #721c24; }
        
        .recommendation-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="trends-container">
    <div class="trends-header">
        <h1>📈 Health Trends & Analytics</h1>
        <p style="font-size: 1.2em;">Track Your Health Journey with AI Insights</p>
    </div>
    
    <div class="trends-card">
        <h3>Your Health Metrics</h3>
        <div class="row">
            <?php foreach($healthTrends as $trend): ?>
            <div class="col-md-6">
                <div class="metric-box trend-<?php echo strtolower($trend['trend_direction']); ?>">
                    <h5><?php echo htmlspecialchars($trend['metric_name']); ?></h5>
                    <p style="font-size: 1.5em; margin: 10px 0; font-weight: bold;">
                        <?php echo htmlspecialchars($trend['metric_value']); ?>
                    </p>
                    <span class="trend-badge <?php echo strtolower($trend['trend_direction']); ?>">
                        <?php 
                        echo $trend['trend_direction'] == 'Improving' ? '↑ ' : 
                             ($trend['trend_direction'] == 'Declining' ? '↓ ' : '→ ');
                        echo $trend['trend_direction']; 
                        ?>
                    </span>
                    <small style="display: block; margin-top: 5px; color: #666;">
                        Recorded: <?php echo date('M d, Y H:i', strtotime($trend['recorded_at'])); ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="trends-card">
        <h3>Health Trend Visualization</h3>
        <div class="chart-container">
            <canvas id="healthTrendChart"></canvas>
        </div>
        <p style="text-align: center; color: #666; margin-top: 15px;">
            <small>Visual representation of your health metrics over time</small>
        </p>
    </div>
    
    <div class="trends-card">
        <h3>💪 Personalized Lifestyle Recommendations</h3>
        <p>AI-generated suggestions tailored to your health profile</p>
        
        <?php foreach($lifestyleRecs as $rec): ?>
        <div class="recommendation-card">
            <h5>🎯 <?php echo htmlspecialchars($rec['category']); ?></h5>
            <p style="margin: 10px 0; font-size: 1.1em;">
                <?php echo htmlspecialchars($rec['recommendation']); ?>
            </p>
            <small style="color: #666;">
                <em><?php echo htmlspecialchars($rec['personalization_factors']); ?></em>
            </small>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="ai_dr_cares.php" style="color: white; text-decoration: none; font-size: 1.1em; margin-right: 20px;">
            ← Back to Dr. Cares AI
        </a>
        <a href="patientprofile.php" style="color: white; text-decoration: none; font-size: 1.1em;">
            My Profile
        </a>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script>
// Create health trend chart
const ctx = document.getElementById('healthTrendChart').getContext('2d');

const healthData = <?php echo json_encode(array_reverse($healthTrends)); ?>;

// Extract data for chart
const labels = healthData.map(item => new Date(item.recorded_at).toLocaleDateString());
const uniqueMetrics = [...new Set(healthData.map(item => item.metric_name))];

const datasets = uniqueMetrics.map((metric, index) => {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'];
    const metricData = healthData.filter(item => item.metric_name === metric);
    
    return {
        label: metric,
        data: metricData.map(item => {
            // Extract numeric value from metric_value
            const match = item.metric_value.match(/\d+/);
            return match ? parseInt(match[0]) : 0;
        }),
        borderColor: colors[index % colors.length],
        backgroundColor: colors[index % colors.length] + '33',
        tension: 0.4
    };
});

const healthTrendChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [...new Set(labels)],
        datasets: datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            title: {
                display: true,
                text: 'Health Metrics Trend Analysis'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>
</html>

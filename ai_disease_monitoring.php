<?php
session_start();
include("dbconnection.php");

// Check if patient is logged in
if(!isset($_SESSION['patientid'])) {
    header("Location: patientlogin.php");
    exit();
}

$patientId = $_SESSION['patientid'];

// Handle form submission for adding monitoring data
if(isset($_POST['add_monitoring'])) {
    $diseaseName = mysqli_real_escape_string($con, $_POST['disease_name']);
    $vitalSigns = mysqli_real_escape_string($con, $_POST['vital_signs']);
    
    // Anomaly detection with numerical analysis
    $alertTriggered = 0;
    $alertReason = '';
    $status = 'Normal';
    
    // Extract numerical values and check against thresholds
    // This is a simplified example - production should use ML models
    $vitalSignsLower = strtolower($vitalSigns);
    
    // Blood pressure detection (e.g., "BP: 160/100" or "Blood Pressure: 160/100")
    if(preg_match('/(\d{2,3})\s*\/\s*(\d{2,3})/', $vitalSigns, $bpMatches)) {
        $systolic = intval($bpMatches[1]);
        $diastolic = intval($bpMatches[2]);
        
        if($systolic >= 140 || $diastolic >= 90) {
            $alertTriggered = 1;
            $alertReason = "High blood pressure detected ($systolic/$diastolic mmHg). Please consult your doctor.";
            $status = 'Alert';
        } elseif($systolic < 90 || $diastolic < 60) {
            $alertTriggered = 1;
            $alertReason = "Low blood pressure detected ($systolic/$diastolic mmHg). Please monitor closely.";
            $status = 'Warning';
        }
    }
    
    // Blood sugar detection (e.g., "Blood Sugar: 250 mg/dL")
    if(preg_match('/(\d{2,3})\s*(mg\/dl|mmol)/i', $vitalSigns, $bsMatches)) {
        $bloodSugar = intval($bsMatches[1]);
        
        if($bloodSugar > 180) {
            $alertTriggered = 1;
            $alertReason = "High blood sugar level detected ($bloodSugar mg/dL). Please consult your doctor.";
            $status = 'Alert';
        } elseif($bloodSugar < 70) {
            $alertTriggered = 1;
            $alertReason = "Low blood sugar level detected ($bloodSugar mg/dL). Please take action immediately.";
            $status = 'Alert';
        }
    }
    
    // Heart rate detection (e.g., "Heart Rate: 110 bpm")
    if(preg_match('/(\d{2,3})\s*bpm/i', $vitalSigns, $hrMatches)) {
        $heartRate = intval($hrMatches[1]);
        
        if($heartRate > 100) {
            $alertTriggered = 1;
            $alertReason = "Elevated heart rate detected ($heartRate bpm). Please monitor closely.";
            $status = 'Warning';
        } elseif($heartRate < 60) {
            $alertTriggered = 1;
            $alertReason = "Low heart rate detected ($heartRate bpm). Please consult your doctor if symptomatic.";
            $status = 'Warning';
        }
    }
    
    // Fallback to keyword detection if no numerical values found
    if(!$alertTriggered) {
        if(strpos($vitalSignsLower, 'high') !== false || strpos($vitalSignsLower, 'elevated') !== false) {
            $alertTriggered = 1;
            $alertReason = 'Elevated vital signs detected. Please consult your doctor.';
            $status = 'Alert';
        } elseif(strpos($vitalSignsLower, 'low') !== false) {
            $alertTriggered = 1;
            $alertReason = 'Low vital signs detected. Please monitor closely.';
            $status = 'Warning';
        }
    }
    
    $sql = "INSERT INTO ai_chronic_disease_monitoring 
            (patientid, disease_name, vital_signs, measurement_date, alert_triggered, alert_reason, status) 
            VALUES (?, ?, ?, NOW(), ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ississ", $patientId, $diseaseName, $vitalSigns, $alertTriggered, $alertReason, $status);
    
    if(mysqli_stmt_execute($stmt)) {
        $successMessage = "Monitoring data added successfully!";
        if($alertTriggered) {
            $alertMessage = $alertReason;
        }
    }
    mysqli_stmt_close($stmt);
}

// Get monitoring history
$sql = "SELECT * FROM ai_chronic_disease_monitoring WHERE patientid = ? ORDER BY measurement_date DESC LIMIT 50";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$monitoringData = [];
while($row = mysqli_fetch_assoc($result)) {
    $monitoringData[] = $row;
}
mysqli_stmt_close($stmt);

// Get active alerts
$sql = "SELECT * FROM ai_chronic_disease_monitoring 
        WHERE patientid = ? AND alert_triggered = 1 AND status != 'Resolved' 
        ORDER BY measurement_date DESC LIMIT 10";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$activeAlerts = [];
while($row = mysqli_fetch_assoc($result)) {
    $activeAlerts[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chronic Disease Monitoring</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .monitoring-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .monitoring-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .monitoring-header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .monitoring-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .alert-box {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid;
        }
        
        .alert-box.alert-critical {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        
        .alert-box.alert-warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        
        .alert-box.success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        
        .monitoring-entry {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #667eea;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-normal { background: #d4edda; color: #155724; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-alert { background: #f8d7da; color: #721c24; }
        
        .btn-ai {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
        }
        
        .btn-ai:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>

<div class="monitoring-container">
    <div class="monitoring-header">
        <h1>🏥 Chronic Disease Monitoring</h1>
        <p style="font-size: 1.2em;">AI-Powered Real-Time Health Monitoring</p>
    </div>
    
    <?php if(isset($successMessage)): ?>
    <div class="alert-box success">
        <strong>✓ Success!</strong> <?php echo $successMessage; ?>
    </div>
    <?php endif; ?>
    
    <?php if(isset($alertMessage)): ?>
    <div class="alert-box alert-critical">
        <strong>⚠️ Alert!</strong> <?php echo $alertMessage; ?>
    </div>
    <?php endif; ?>
    
    <?php if(!empty($activeAlerts)): ?>
    <div class="monitoring-card">
        <h3>🚨 Active Alerts</h3>
        <p>These alerts require your attention</p>
        
        <?php foreach($activeAlerts as $alert): ?>
        <div class="alert-box alert-<?php echo ($alert['status'] == 'Alert') ? 'critical' : 'warning'; ?>">
            <strong><?php echo htmlspecialchars($alert['disease_name']); ?></strong><br>
            <span><?php echo htmlspecialchars($alert['alert_reason']); ?></span><br>
            <small>Recorded: <?php echo date('M d, Y H:i', strtotime($alert['measurement_date'])); ?></small>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="monitoring-card">
        <h3>Add Monitoring Data</h3>
        <p>Record your daily health measurements for chronic disease monitoring</p>
        
        <form method="post" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Disease/Condition Name:</label>
                        <input type="text" class="form-control" name="disease_name" 
                               placeholder="e.g., Diabetes, Hypertension, Asthma" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Vital Signs/Measurements:</label>
                        <input type="text" class="form-control" name="vital_signs" 
                               placeholder="e.g., Blood Sugar: 110 mg/dL, BP: 120/80" required>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="add_monitoring" class="btn btn-ai">
                📊 Add Monitoring Data
            </button>
        </form>
    </div>
    
    <div class="monitoring-card">
        <h3>📋 Monitoring History</h3>
        <p>Your chronic disease monitoring records</p>
        
        <?php if(empty($monitoringData)): ?>
        <div class="alert-box" style="background: #e7f3ff; border-left-color: #2196F3; color: #0c5460;">
            No monitoring data recorded yet. Start tracking your health by adding measurements above.
        </div>
        <?php else: ?>
            <?php foreach($monitoringData as $entry): ?>
            <div class="monitoring-entry">
                <div class="row">
                    <div class="col-md-8">
                        <h5><?php echo htmlspecialchars($entry['disease_name']); ?></h5>
                        <p style="margin: 8px 0;">
                            <strong>Measurements:</strong> <?php echo htmlspecialchars($entry['vital_signs']); ?>
                        </p>
                        <small style="color: #666;">
                            Recorded: <?php echo date('M d, Y H:i', strtotime($entry['measurement_date'])); ?>
                        </small>
                    </div>
                    <div class="col-md-4 text-right">
                        <span class="status-badge status-<?php echo strtolower($entry['status']); ?>">
                            <?php echo htmlspecialchars($entry['status']); ?>
                        </span>
                        <?php if($entry['alert_triggered']): ?>
                        <p style="margin-top: 10px; color: #dc3545; font-size: 0.9em;">
                            ⚠️ <?php echo htmlspecialchars($entry['alert_reason']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="monitoring-card">
        <h3>ℹ️ About Chronic Disease Monitoring</h3>
        <div class="row">
            <div class="col-md-6">
                <h5>Features:</h5>
                <ul>
                    <li>Real-time vital signs tracking</li>
                    <li>AI-powered anomaly detection</li>
                    <li>Automatic alerts for concerning values</li>
                    <li>Historical data visualization</li>
                    <li>Personalized health insights</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Tips for Effective Monitoring:</h5>
                <ul>
                    <li>Record measurements at the same time daily</li>
                    <li>Be consistent with your tracking</li>
                    <li>Note any symptoms or changes</li>
                    <li>Share data with your healthcare provider</li>
                    <li>Follow medical advice for concerning alerts</li>
                </ul>
            </div>
        </div>
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
</body>
</html>

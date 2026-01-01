<?php
session_start();
include("dbconnection.php");

// Check if doctor is logged in
if(!isset($_SESSION['doctorid'])) {
    header("Location: doctorlogin.php");
    exit();
}

$doctorId = $_SESSION['doctorid'];

// Get doctor's patients
$sql = "SELECT DISTINCT p.* FROM patient p 
        INNER JOIN appointment a ON p.patientid = a.patientid 
        WHERE a.doctorid = ? AND a.status = 'Approved'
        ORDER BY p.patientname";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $doctorId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$patients = [];
while($row = mysqli_fetch_assoc($result)) {
    $patients[] = $row;
}
mysqli_stmt_close($stmt);

// Get selected patient's AI insights
$selectedPatientId = isset($_GET['patientid']) ? intval($_GET['patientid']) : null;
$patientInsights = null;

if($selectedPatientId) {
    // Get patient details
    $sql = "SELECT * FROM patient WHERE patientid = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selectedPatientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $patientDetails = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Get recent symptom analyses
    $sql = "SELECT * FROM ai_symptom_analysis WHERE patientid = ? ORDER BY created_at DESC LIMIT 5";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selectedPatientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $symptomAnalyses = [];
    while($row = mysqli_fetch_assoc($result)) {
        $symptomAnalyses[] = $row;
    }
    mysqli_stmt_close($stmt);
    
    // Get health risk predictions
    $sql = "SELECT * FROM ai_health_risk_predictions WHERE patientid = ? ORDER BY prediction_date DESC LIMIT 5";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selectedPatientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $healthRisks = [];
    while($row = mysqli_fetch_assoc($result)) {
        $healthRisks[] = $row;
    }
    mysqli_stmt_close($stmt);
    
    // Get chronic disease monitoring
    $sql = "SELECT * FROM ai_chronic_disease_monitoring WHERE patientid = ? ORDER BY measurement_date DESC LIMIT 10";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selectedPatientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $diseaseMonitoring = [];
    while($row = mysqli_fetch_assoc($result)) {
        $diseaseMonitoring[] = $row;
    }
    mysqli_stmt_close($stmt);
    
    // Get health trends
    $sql = "SELECT * FROM ai_health_trends WHERE patientid = ? ORDER BY recorded_at DESC LIMIT 10";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selectedPatientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $healthTrends = [];
    while($row = mysqli_fetch_assoc($result)) {
        $healthTrends[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor AI Dashboard - Patient Insights</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .dashboard-header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .dashboard-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .patient-selector {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .insight-box {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #667eea;
        }
        
        .risk-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-block;
        }
        
        .risk-low { background: #d4edda; color: #155724; }
        .risk-medium { background: #fff3cd; color: #856404; }
        .risk-high { background: #f8d7da; color: #721c24; }
        .risk-critical { background: #fc8181; color: #742a2a; }
        
        .summary-stat {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .summary-stat h3 {
            margin: 0;
            font-size: 2em;
        }
        
        .summary-stat p {
            margin: 5px 0 0 0;
        }
        
        .btn-ai {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>🏥 Doctor AI Dashboard</h1>
        <p style="font-size: 1.2em;">AI-Powered Patient Insights & Analysis</p>
    </div>
    
    <div class="patient-selector">
        <h4>Select Patient for AI Insights</h4>
        <form method="get" action="">
            <div class="row">
                <div class="col-md-10">
                    <select name="patientid" class="form-control" required>
                        <option value="">-- Select a Patient --</option>
                        <?php foreach($patients as $patient): ?>
                        <option value="<?php echo $patient['patientid']; ?>" 
                                <?php echo ($selectedPatientId == $patient['patientid']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($patient['patientname']); ?> 
                            (ID: <?php echo $patient['patientid']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-ai" style="width: 100%;">View Insights</button>
                </div>
            </div>
        </form>
    </div>
    
    <?php if($selectedPatientId && isset($patientDetails)): ?>
    
    <div class="dashboard-card">
        <h3>📊 Patient Summary</h3>
        <div class="row">
            <div class="col-md-3">
                <div class="summary-stat">
                    <h3><?php echo count($symptomAnalyses); ?></h3>
                    <p>AI Analyses</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stat">
                    <h3><?php echo count($healthRisks); ?></h3>
                    <p>Risk Predictions</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stat">
                    <h3><?php echo count($diseaseMonitoring); ?></h3>
                    <p>Monitoring Records</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stat">
                    <h3><?php echo count($healthTrends); ?></h3>
                    <p>Health Metrics</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="dashboard-card">
                <h4>🩺 Recent Symptom Analyses</h4>
                <?php if(empty($symptomAnalyses)): ?>
                <p style="color: #666;">No symptom analyses recorded yet.</p>
                <?php else: ?>
                    <?php foreach($symptomAnalyses as $analysis): ?>
                    <div class="insight-box">
                        <strong>Symptoms:</strong> <?php echo htmlspecialchars($analysis['symptoms']); ?><br>
                        <strong>AI Diagnosis:</strong> <?php echo htmlspecialchars($analysis['ai_diagnosis']); ?><br>
                        <strong>Confidence:</strong> <?php echo ($analysis['confidence_score'] * 100); ?>%<br>
                        <small>Analyzed: <?php echo date('M d, Y H:i', strtotime($analysis['created_at'])); ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="dashboard-card">
                <h4>⚠️ Health Risk Predictions</h4>
                <?php if(empty($healthRisks)): ?>
                <p style="color: #666;">No health risk predictions available.</p>
                <?php else: ?>
                    <?php foreach($healthRisks as $risk): ?>
                    <div class="insight-box">
                        <strong><?php echo htmlspecialchars($risk['risk_type']); ?></strong>
                        <span class="risk-badge risk-<?php echo strtolower($risk['risk_level']); ?>">
                            <?php echo $risk['risk_level']; ?> Risk
                        </span><br>
                        <strong>Probability:</strong> <?php echo ($risk['probability'] * 100); ?>%<br>
                        <strong>Factors:</strong> <?php echo htmlspecialchars($risk['factors']); ?><br>
                        <small>Predicted: <?php echo date('M d, Y', strtotime($risk['prediction_date'])); ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="dashboard-card">
                <h4>🏥 Disease Monitoring</h4>
                <?php if(empty($diseaseMonitoring)): ?>
                <p style="color: #666;">No disease monitoring data available.</p>
                <?php else: ?>
                    <?php foreach($diseaseMonitoring as $monitor): ?>
                    <div class="insight-box">
                        <strong><?php echo htmlspecialchars($monitor['disease_name']); ?></strong>
                        <span class="risk-badge risk-<?php echo strtolower($monitor['status']); ?>">
                            <?php echo $monitor['status']; ?>
                        </span><br>
                        <strong>Measurements:</strong> <?php echo htmlspecialchars($monitor['vital_signs']); ?><br>
                        <?php if($monitor['alert_triggered']): ?>
                        <strong style="color: #dc3545;">Alert:</strong> <?php echo htmlspecialchars($monitor['alert_reason']); ?><br>
                        <?php endif; ?>
                        <small>Recorded: <?php echo date('M d, Y H:i', strtotime($monitor['measurement_date'])); ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="dashboard-card">
                <h4>📈 Health Trends</h4>
                <?php if(empty($healthTrends)): ?>
                <p style="color: #666;">No health trend data available.</p>
                <?php else: ?>
                    <?php foreach($healthTrends as $trend): ?>
                    <div class="insight-box">
                        <strong><?php echo htmlspecialchars($trend['metric_name']); ?>:</strong> 
                        <?php echo htmlspecialchars($trend['metric_value']); ?><br>
                        <strong>Trend:</strong> 
                        <span class="risk-badge <?php 
                            echo ($trend['trend_direction'] == 'Improving') ? 'risk-low' : 
                                 (($trend['trend_direction'] == 'Declining') ? 'risk-high' : 'risk-medium'); 
                        ?>">
                            <?php echo $trend['trend_direction']; ?>
                        </span><br>
                        <small>Recorded: <?php echo date('M d, Y H:i', strtotime($trend['recorded_at'])); ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php elseif($selectedPatientId): ?>
    <div class="dashboard-card">
        <p style="text-align: center; color: #666;">Patient data not found or you don't have access to this patient.</p>
    </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="doctorprofile.php" style="color: white; text-decoration: none; font-size: 1.1em;">
            ← Back to Doctor Dashboard
        </a>
    </div>
</div>

<script src="js/jquery.min.js"></script>
</body>
</html>

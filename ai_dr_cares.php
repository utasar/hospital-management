<?php
session_start();
include("dbconnection.php");
include("ai_modules/ai_api_handler.php");

$aiHandler = new AIAPIHandler($con);

// Check if patient is logged in
$isLoggedIn = isset($_SESSION['patientid']);
$patientId = $isLoggedIn ? $_SESSION['patientid'] : null;

// Handle symptom analysis form submission
if(isset($_POST['analyze_symptoms']) && $isLoggedIn) {
    $symptoms = $_POST['symptoms'];
    $analysis = $aiHandler->analyzeSymptoms($symptoms, $patientId);
    
    // Get medication recommendations
    if($analysis['analysis_id']) {
        $medications = $aiHandler->getMedicationRecommendations($analysis['analysis_id'], $patientId);
    }
    
    $analysisComplete = true;
}

// Handle preventive care request
if(isset($_POST['get_preventive_care']) && $isLoggedIn) {
    $preventiveCare = $aiHandler->generatePreventiveCare($patientId);
    $preventiveCareComplete = true;
}

// Handle health risk prediction
if(isset($_POST['check_health_risks']) && $isLoggedIn) {
    $healthRisks = $aiHandler->predictHealthRisks($patientId);
    $healthRisksComplete = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Cares AI - AI-Powered Health Assistant</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .ai-container {
            padding: 50px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .ai-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .ai-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .ai-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .ai-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .ai-icon {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .feature-box {
            padding: 20px;
            border-left: 4px solid #667eea;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .result-box {
            padding: 20px;
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .medication-item {
            padding: 15px;
            background: #f0f9ff;
            border: 1px solid #bee3f8;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .risk-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 5px;
        }
        
        .risk-low { background: #c6f6d5; color: #22543d; }
        .risk-medium { background: #fef5e7; color: #744210; }
        .risk-high { background: #fed7d7; color: #742a2a; }
        .risk-critical { background: #fc8181; color: #742a2a; }
        
        .btn-ai {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-ai:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .login-prompt {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    
<div class="ai-container">
    <div class="container">
        <div class="ai-header">
            <h1>🤖 Dr. Cares AI</h1>
            <p style="font-size: 1.2em;">Your Intelligent Health Companion</p>
        </div>
        
        <?php if(!$isLoggedIn): ?>
        <div class="login-prompt">
            <h3>Welcome to Dr. Cares AI</h3>
            <p>Please <a href="patientlogin.php">login</a> to access AI-powered health features</p>
            <a href="patientlogin.php" class="btn btn-ai">Login Now</a>
        </div>
        <?php else: ?>
        
        <div class="row">
            <!-- Symptom Analysis -->
            <div class="col-md-6">
                <div class="ai-card">
                    <div class="ai-icon">🩺</div>
                    <h3>Symptom Analysis</h3>
                    <p>Describe your symptoms and get AI-powered health insights</p>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <label>Describe Your Symptoms:</label>
                            <textarea class="form-control" name="symptoms" rows="4" 
                                placeholder="E.g., I have a fever, cough, and headache..." required></textarea>
                        </div>
                        <button type="submit" name="analyze_symptoms" class="btn btn-ai">
                            Analyze Symptoms
                        </button>
                    </form>
                    
                    <?php if(isset($analysisComplete) && $analysisComplete): ?>
                    <div class="result-box">
                        <h5>AI Analysis Results</h5>
                        <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($analysis['diagnosis']); ?></p>
                        <p><strong>Confidence:</strong> <?php echo ($analysis['confidence'] * 100); ?>%</p>
                        
                        <?php if(isset($medications) && !empty($medications)): ?>
                        <h6 style="margin-top: 20px;">Recommended Medications:</h6>
                        <?php foreach($medications as $med): ?>
                        <div class="medication-item">
                            <strong><?php echo htmlspecialchars($med['name']); ?></strong><br>
                            <small>
                                Dosage: <?php echo htmlspecialchars($med['dosage']); ?> | 
                                Frequency: <?php echo htmlspecialchars($med['frequency']); ?> | 
                                Duration: <?php echo htmlspecialchars($med['duration']); ?>
                            </small><br>
                            <em><?php echo htmlspecialchars($med['reasoning']); ?></em>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <p style="margin-top: 15px; color: #d9534f;">
                            <strong>Important:</strong> This is an AI suggestion. Please consult with a doctor for proper diagnosis.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Preventive Care -->
            <div class="col-md-6">
                <div class="ai-card">
                    <div class="ai-icon">💪</div>
                    <h3>Preventive Care Advice</h3>
                    <p>Get personalized preventive health recommendations</p>
                    
                    <form method="post" action="">
                        <button type="submit" name="get_preventive_care" class="btn btn-ai">
                            Get Preventive Care Tips
                        </button>
                    </form>
                    
                    <?php if(isset($preventiveCareComplete) && $preventiveCareComplete): ?>
                    <div class="result-box">
                        <h5>Your Preventive Care Plan</h5>
                        <?php foreach($preventiveCare as $care): ?>
                        <div class="feature-box">
                            <strong><?php echo htmlspecialchars($care['category']); ?></strong>
                            <span class="risk-badge risk-<?php echo strtolower($care['priority']); ?>">
                                <?php echo $care['priority']; ?> Priority
                            </span>
                            <p style="margin-top: 10px;"><?php echo htmlspecialchars($care['text']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Health Risk Prediction -->
            <div class="col-md-12">
                <div class="ai-card">
                    <div class="ai-icon">📊</div>
                    <h3>Health Risk Assessment</h3>
                    <p>AI-powered prediction of potential health risks based on your profile</p>
                    
                    <form method="post" action="">
                        <button type="submit" name="check_health_risks" class="btn btn-ai">
                            Check My Health Risks
                        </button>
                    </form>
                    
                    <?php if(isset($healthRisksComplete) && $healthRisksComplete): ?>
                    <div class="result-box">
                        <h5>Health Risk Predictions</h5>
                        <?php if(!empty($healthRisks)): ?>
                            <?php foreach($healthRisks as $risk): ?>
                            <div class="feature-box">
                                <h6>
                                    <?php echo htmlspecialchars($risk['type']); ?>
                                    <span class="risk-badge risk-<?php echo strtolower($risk['level']); ?>">
                                        <?php echo $risk['level']; ?> Risk
                                    </span>
                                </h6>
                                <p><strong>Probability:</strong> <?php echo ($risk['probability'] * 100); ?>%</p>
                                <p><strong>Contributing Factors:</strong> <?php echo htmlspecialchars($risk['factors']); ?></p>
                                <p><strong>Recommendations:</strong> <?php echo htmlspecialchars($risk['recommendations']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Great news! No significant health risks detected based on your current profile. Keep maintaining a healthy lifestyle!</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="ai-card" style="text-align: center;">
                    <h4>Additional AI Features</h4>
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-md-3">
                            <a href="ai_chatbot.php" class="btn btn-ai" style="width: 100%;">
                                💬 AI Chatbot
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="ai_health_trends.php" class="btn btn-ai" style="width: 100%;">
                                📈 Health Trends
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="ai_disease_monitoring.php" class="btn btn-ai" style="width: 100%;">
                                🏥 Disease Monitoring
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="patientprofile.php" class="btn btn-ai" style="width: 100%;">
                                👤 My Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/vendors/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>

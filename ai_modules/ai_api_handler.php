<?php
/**
 * AI API Handler
 * Handles communication with AI models and services
 * Provides abstraction layer for AI functionality
 */

class AIAPIHandler {
    private $con;
    private $model_configs = [];
    
    public function __construct($connection) {
        $this->con = $connection;
        $this->loadModelConfigs();
    }
    
    /**
     * Load AI model configurations from database
     */
    private function loadModelConfigs() {
        $sql = "SELECT * FROM ai_model_config WHERE is_active = 1";
        $result = mysqli_query($this->con, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $this->model_configs[$row['model_name']] = $row;
            }
        }
    }
    
    /**
     * Analyze symptoms using AI
     * @param array $symptoms Array of symptom descriptions
     * @param int $patientId Patient ID
     * @return array Analysis results
     */
    public function analyzeSymptoms($symptoms, $patientId) {
        // Simulate AI analysis (in production, this would call actual AI model)
        $symptomsText = is_array($symptoms) ? implode(', ', $symptoms) : $symptoms;
        
        // Simple keyword-based analysis for demonstration
        $diagnosis = $this->performSymptomAnalysis($symptomsText);
        
        // Store analysis in database
        $sql = "INSERT INTO ai_symptom_analysis (patientid, symptoms, ai_diagnosis, confidence_score) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "issd", $patientId, $symptomsText, $diagnosis['diagnosis'], $diagnosis['confidence']);
        mysqli_stmt_execute($stmt);
        
        $analysisId = mysqli_insert_id($this->con);
        mysqli_stmt_close($stmt);
        
        return [
            'analysis_id' => $analysisId,
            'diagnosis' => $diagnosis['diagnosis'],
            'confidence' => $diagnosis['confidence'],
            'recommendations' => $diagnosis['recommendations']
        ];
    }
    
    /**
     * Perform symptom analysis logic
     */
    private function performSymptomAnalysis($symptomsText) {
        $symptomsLower = strtolower($symptomsText);
        
        // Simple pattern matching (in production, use ML models)
        if (strpos($symptomsLower, 'fever') !== false && strpos($symptomsLower, 'cough') !== false) {
            return [
                'diagnosis' => 'Possible respiratory infection. Recommend consultation with a doctor.',
                'confidence' => 0.75,
                'recommendations' => ['Rest', 'Hydration', 'Monitor temperature', 'Consult doctor if symptoms persist']
            ];
        } elseif (strpos($symptomsLower, 'headache') !== false) {
            return [
                'diagnosis' => 'Possible tension headache or migraine. Consider stress management.',
                'confidence' => 0.65,
                'recommendations' => ['Rest in quiet environment', 'Adequate hydration', 'Stress reduction']
            ];
        } elseif (strpos($symptomsLower, 'stomach') !== false || strpos($symptomsLower, 'nausea') !== false) {
            return [
                'diagnosis' => 'Possible digestive issue. Monitor diet and hydration.',
                'confidence' => 0.70,
                'recommendations' => ['Light diet', 'Hydration', 'Avoid spicy foods']
            ];
        } else {
            return [
                'diagnosis' => 'General symptoms detected. Recommend professional medical evaluation.',
                'confidence' => 0.50,
                'recommendations' => ['Schedule doctor appointment', 'Monitor symptoms']
            ];
        }
    }
    
    /**
     * Get medication recommendations based on diagnosis
     */
    public function getMedicationRecommendations($analysisId, $patientId) {
        // Get the diagnosis
        $sql = "SELECT ai_diagnosis FROM ai_symptom_analysis WHERE analysis_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $analysisId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $analysis = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$analysis) {
            return [];
        }
        
        // Generate medication recommendations (simplified)
        $recommendations = $this->generateMedicationRecommendations($analysis['ai_diagnosis']);
        
        // Store recommendations
        foreach ($recommendations as $med) {
            $sql = "INSERT INTO ai_medication_recommendations 
                    (analysis_id, patientid, medication_name, dosage, frequency, duration, ai_reasoning) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "iisssss", 
                $analysisId, $patientId, $med['name'], $med['dosage'], 
                $med['frequency'], $med['duration'], $med['reasoning']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        return $recommendations;
    }
    
    /**
     * Generate medication recommendations based on diagnosis
     */
    private function generateMedicationRecommendations($diagnosis) {
        $diagnosisLower = strtolower($diagnosis);
        $recommendations = [];
        
        if (strpos($diagnosisLower, 'respiratory') !== false || strpos($diagnosisLower, 'fever') !== false) {
            $recommendations[] = [
                'name' => 'Paracetamol',
                'dosage' => '500mg',
                'frequency' => 'Every 6 hours',
                'duration' => '3-5 days',
                'reasoning' => 'For fever and pain relief'
            ];
        }
        
        if (strpos($diagnosisLower, 'headache') !== false) {
            $recommendations[] = [
                'name' => 'Ibuprofen',
                'dosage' => '400mg',
                'frequency' => 'Every 8 hours as needed',
                'duration' => '2-3 days',
                'reasoning' => 'For headache relief'
            ];
        }
        
        if (strpos($diagnosisLower, 'digestive') !== false) {
            $recommendations[] = [
                'name' => 'Antacid',
                'dosage' => '10ml',
                'frequency' => 'After meals',
                'duration' => '3-5 days',
                'reasoning' => 'For digestive comfort'
            ];
        }
        
        // Always add general advice
        if (empty($recommendations)) {
            $recommendations[] = [
                'name' => 'General Care',
                'dosage' => 'N/A',
                'frequency' => 'As needed',
                'duration' => 'Ongoing',
                'reasoning' => 'Consult with doctor for specific medication'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Generate preventive care advice
     */
    public function generatePreventiveCare($patientId, $healthConditions = []) {
        $advice = [];
        
        // General preventive care advice
        $advice[] = [
            'category' => 'Exercise',
            'text' => 'Engage in at least 30 minutes of moderate physical activity 5 days a week',
            'priority' => 'High'
        ];
        
        $advice[] = [
            'category' => 'Nutrition',
            'text' => 'Maintain a balanced diet with fruits, vegetables, and whole grains',
            'priority' => 'High'
        ];
        
        $advice[] = [
            'category' => 'Hydration',
            'text' => 'Drink at least 8 glasses of water daily',
            'priority' => 'Medium'
        ];
        
        $advice[] = [
            'category' => 'Sleep',
            'text' => 'Aim for 7-9 hours of quality sleep each night',
            'priority' => 'High'
        ];
        
        $advice[] = [
            'category' => 'Stress Management',
            'text' => 'Practice stress-reduction techniques like meditation or deep breathing',
            'priority' => 'Medium'
        ];
        
        // Store advice in database
        foreach ($advice as $item) {
            $sql = "INSERT INTO ai_preventive_care (patientid, advice_category, advice_text, priority) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "isss", $patientId, $item['category'], $item['text'], $item['priority']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        return $advice;
    }
    
    /**
     * Predict health risks based on patient data
     */
    public function predictHealthRisks($patientId) {
        // Get patient data
        $sql = "SELECT * FROM patient WHERE patientid = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $patientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $patient = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$patient) {
            return [];
        }
        
        // Calculate age
        $dob = new DateTime($patient['dob']);
        $now = new DateTime();
        $age = $now->diff($dob)->y;
        
        $risks = [];
        
        // Age-based risk assessment
        if ($age > 50) {
            $risks[] = [
                'type' => 'Cardiovascular Disease',
                'level' => 'Medium',
                'probability' => 0.35,
                'factors' => 'Age above 50 years',
                'recommendations' => 'Regular blood pressure monitoring, annual cardiovascular checkup'
            ];
        }
        
        if ($age > 40) {
            $risks[] = [
                'type' => 'Type 2 Diabetes',
                'level' => 'Low',
                'probability' => 0.25,
                'factors' => 'Age factor',
                'recommendations' => 'Annual glucose screening, maintain healthy weight'
            ];
        }
        
        // Store risk predictions
        foreach ($risks as $risk) {
            $sql = "INSERT INTO ai_health_risk_predictions 
                    (patientid, risk_type, risk_level, probability, factors, recommendations) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "issdss", 
                $patientId, $risk['type'], $risk['level'], 
                $risk['probability'], $risk['factors'], $risk['recommendations']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        return $risks;
    }
    
    /**
     * Process chatbot message
     */
    public function processChatbotMessage($userId, $userType, $message) {
        $messageLower = strtolower($message);
        $response = '';
        $intent = '';
        
        // Intent detection and response generation
        if (strpos($messageLower, 'appointment') !== false || strpos($messageLower, 'book') !== false) {
            $intent = 'appointment_booking';
            $response = "I can help you book an appointment. Please visit our Appointment page or tell me your preferred date and department.";
        } elseif (strpos($messageLower, 'symptom') !== false || strpos($messageLower, 'sick') !== false || strpos($messageLower, 'pain') !== false) {
            $intent = 'symptom_check';
            $response = "I can help analyze your symptoms. Please describe what you're experiencing in detail.";
        } elseif (strpos($messageLower, 'medication') !== false || strpos($messageLower, 'medicine') !== false) {
            $intent = 'medication_info';
            $response = "I can provide information about medications. Please specify which medication you'd like to know about.";
        } elseif (strpos($messageLower, 'hello') !== false || strpos($messageLower, 'hi') !== false) {
            $intent = 'greeting';
            $response = "Hello! I'm Dr. Cares AI, your virtual health assistant. How can I help you today?";
        } else {
            $intent = 'general_query';
            $response = "I'm here to help! You can ask me about appointments, symptoms, medications, or general health advice.";
        }
        
        // Store conversation
        $sql = "INSERT INTO ai_chatbot_conversations (user_id, user_type, message, response, intent) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "issss", $userId, $userType, $message, $response, $intent);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return [
            'response' => $response,
            'intent' => $intent
        ];
    }
}
?>

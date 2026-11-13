<?php
/**
 * Safety-first assistant foundation.
 * This module is deterministic guidance only: it is NOT a diagnostic,
 * prescribing, triage, or clinical decision-support system.
 */
class AIAPIHandler
{
    private $con;

    public function __construct($connection)
    {
        $this->con = $connection;
    }

    /**
     * Record a patient's own symptom description and return a safe next step.
     * No diagnosis, probability, or treatment recommendation is generated.
     */
    public function analyzeSymptoms($symptoms, $patientId)
    {
        $symptomsText = is_array($symptoms) ? implode(', ', $symptoms) : (string) $symptoms;
        $symptomsText = trim($symptomsText);
        if ($symptomsText === '' || strlen($symptomsText) > 4000) {
            throw new InvalidArgumentException('Please enter symptoms using no more than 4,000 characters.');
        }

        $urgent = $this->containsEmergencyWarning($symptomsText);
        $message = $urgent
            ? 'Potential emergency warning detected. This tool cannot assess emergencies. Contact your local emergency service now or go to the nearest emergency department. Do not wait for an online response.'
            : 'This tool cannot diagnose symptoms or determine how serious they are. Please contact a licensed healthcare professional for advice. If symptoms are severe, rapidly worsening, or you feel unsafe, seek urgent medical help.';

        // The legacy schema requires ai_diagnosis and confidence_score columns.
        // Store an explicit non-diagnostic notice and a neutral zero, not a model score.
        $stmt = mysqli_prepare($this->con,
            'INSERT INTO ai_symptom_analysis (patientid, symptoms, ai_diagnosis, confidence_score) VALUES (?, ?, ?, ?)');
        $neutralScore = 0.00;
        mysqli_stmt_bind_param($stmt, 'issd', $patientId, $symptomsText, $message, $neutralScore);
        mysqli_stmt_execute($stmt);
        $analysisId = mysqli_insert_id($this->con);
        mysqli_stmt_close($stmt);

        return array(
            'analysis_id' => $analysisId,
            'diagnosis' => $message,
            'confidence' => null,
            'urgent' => $urgent,
            'recommendations' => array()
        );
    }

    private function containsEmergencyWarning($text)
    {
        $patterns = array(
            '/chest pain/i', '/difficulty breathing/i', '/trouble breathing/i',
            '/cannot breathe/i', '/severe bleeding/i', '/unconscious/i',
            '/passed out/i', '/face droop/i', '/one-sided weakness/i',
            '/suicid(e|al)/i', '/overdose/i'
        );

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        return false;
    }

    /** AI must never make medication or dosage recommendations. */
    public function getMedicationRecommendations($analysisId, $patientId)
    {
        return array();
    }

    /** Generic educational reminders only; not personalized medical advice. */
    public function generatePreventiveCare($patientId, $healthConditions = array())
    {
        return array(
            array(
                'category' => 'Professional care',
                'text' => 'Ask a licensed healthcare professional which preventive screenings and vaccinations are appropriate for you.',
                'priority' => 'Medium'
            ),
            array(
                'category' => 'Personal health',
                'text' => 'For advice tailored to your health, medical history, medicines, pregnancy status, or age, speak with your healthcare professional.',
                'priority' => 'Medium'
            )
        );
    }

    /** Do not infer clinical risk from age or incomplete legacy records. */
    public function predictHealthRisks($patientId)
    {
        return array();
    }

    /** General navigation support; no diagnosis or treatment advice. */
    public function processChatbotMessage($userId, $userType, $message)
    {
        $message = trim((string) $message);
        if ($message === '' || strlen($message) > 2000) {
            throw new InvalidArgumentException('Please enter a message of 1 to 2,000 characters.');
        }

        if ($this->containsEmergencyWarning($message)) {
            $intent = 'urgent_help';
            $response = 'This chat cannot assess emergencies. Contact your local emergency service now or go to the nearest emergency department. Do not wait for an online response.';
        } else {
            $lower = strtolower($message);
            if (strpos($lower, 'appointment') !== false || strpos($lower, 'book') !== false) {
                $intent = 'appointment_booking';
                $response = 'I can point you to appointment services. Please use the appointment section of your account or contact the clinic. I cannot assess medical urgency.';
            } elseif (strpos($lower, 'medication') !== false || strpos($lower, 'medicine') !== false || strpos($lower, 'dose') !== false) {
                $intent = 'medication_question';
                $response = 'I cannot recommend medicines or doses. Ask your doctor or pharmacist, and contact them promptly about side effects or possible interactions.';
            } elseif (strpos($lower, 'symptom') !== false || strpos($lower, 'pain') !== false || strpos($lower, 'sick') !== false) {
                $intent = 'symptom_question';
                $response = 'I cannot diagnose symptoms or determine how serious they are. Please contact a licensed healthcare professional. Seek urgent help for severe or rapidly worsening symptoms.';
            } else {
                $intent = 'general_query';
                $response = 'I can help with basic navigation such as appointments. I am not a medical professional and cannot diagnose, interpret test results, or recommend treatment.';
            }
        }

        $stmt = mysqli_prepare($this->con,
            'INSERT INTO ai_chatbot_conversations (user_id, user_type, message, response, intent) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'issss', $userId, $userType, $message, $response, $intent);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return array('response' => $response, 'intent' => $intent);
    }
}
?>

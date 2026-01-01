<?php
/**
 * Smart Appointment Reminder System
 * Generates intelligent reminders based on patient health conditions
 */

require_once('dbconnection.php');

class SmartAppointmentReminder {
    private $con;
    
    public function __construct($connection) {
        $this->con = $connection;
    }
    
    /**
     * Generate reminders for upcoming appointments
     */
    public function generateReminders() {
        // Get all approved appointments in the next 7 days
        $sql = "SELECT a.*, p.patientname, p.mobileno, p.loginid 
                FROM appointment a 
                INNER JOIN patient p ON a.patientid = p.patientid 
                WHERE a.status = 'Approved' 
                AND a.appointmentdate >= CURDATE() 
                AND a.appointmentdate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        
        $result = mysqli_query($this->con, $sql);
        
        while($appointment = mysqli_fetch_assoc($result)) {
            $this->createReminderSchedule($appointment);
        }
    }
    
    /**
     * Create reminder schedule for an appointment
     */
    private function createReminderSchedule($appointment) {
        $appointmentDate = new DateTime($appointment['appointmentdate'] . ' ' . $appointment['appointmenttime']);
        $now = new DateTime();
        
        // Get patient health priority
        $priority = $this->getPatientHealthPriority($appointment['patientid']);
        
        // Create reminders based on priority
        $reminderTimes = $this->calculateReminderTimes($appointmentDate, $priority);
        
        foreach($reminderTimes as $reminderTime) {
            // Check if reminder already exists
            $sql = "SELECT * FROM ai_appointment_reminders 
                    WHERE appointmentid = ? AND reminder_time = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "is", $appointment['appointmentid'], $reminderTime);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 0) {
                // Create new reminder
                $sql = "INSERT INTO ai_appointment_reminders 
                        (appointmentid, patientid, reminder_type, reminder_time) 
                        VALUES (?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->con, $sql);
                $reminderType = 'In-App'; // Can be Email, SMS, Push, or In-App
                mysqli_stmt_bind_param($stmt, "iiss", 
                    $appointment['appointmentid'], 
                    $appointment['patientid'], 
                    $reminderType, 
                    $reminderTime);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    /**
     * Calculate reminder times based on health priority
     */
    private function calculateReminderTimes($appointmentDate, $priority) {
        $reminders = [];
        
        switch($priority) {
            case 'high':
                // High priority: 7 days, 3 days, 1 day, 3 hours before
                $reminders[] = (clone $appointmentDate)->modify('-7 days')->format('Y-m-d H:i:s');
                $reminders[] = (clone $appointmentDate)->modify('-3 days')->format('Y-m-d H:i:s');
                $reminders[] = (clone $appointmentDate)->modify('-1 day')->format('Y-m-d H:i:s');
                $reminders[] = (clone $appointmentDate)->modify('-3 hours')->format('Y-m-d H:i:s');
                break;
                
            case 'medium':
                // Medium priority: 3 days, 1 day before
                $reminders[] = (clone $appointmentDate)->modify('-3 days')->format('Y-m-d H:i:s');
                $reminders[] = (clone $appointmentDate)->modify('-1 day')->format('Y-m-d H:i:s');
                break;
                
            default:
                // Normal priority: 1 day before
                $reminders[] = (clone $appointmentDate)->modify('-1 day')->format('Y-m-d H:i:s');
                break;
        }
        
        return $reminders;
    }
    
    /**
     * Determine patient health priority based on AI data
     */
    private function getPatientHealthPriority($patientId) {
        // Check for high-risk health predictions
        $sql = "SELECT COUNT(*) as high_risks FROM ai_health_risk_predictions 
                WHERE patientid = ? AND risk_level IN ('High', 'Critical')";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $patientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if($row['high_risks'] > 0) {
            return 'high';
        }
        
        // Check for active disease monitoring alerts
        $sql = "SELECT COUNT(*) as alerts FROM ai_chronic_disease_monitoring 
                WHERE patientid = ? AND alert_triggered = 1 AND status != 'Resolved'";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $patientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if($row['alerts'] > 0) {
            return 'high';
        }
        
        // Check for recent symptom analyses
        $sql = "SELECT COUNT(*) as analyses FROM ai_symptom_analysis 
                WHERE patientid = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $patientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if($row['analyses'] > 2) {
            return 'medium';
        }
        
        return 'normal';
    }
    
    /**
     * Get pending reminders for a patient
     */
    public function getPendingReminders($patientId) {
        $sql = "SELECT r.*, a.appointmentdate, a.appointmenttime, d.doctorname, dept.departmentname
                FROM ai_appointment_reminders r
                INNER JOIN appointment a ON r.appointmentid = a.appointmentid
                INNER JOIN doctor d ON a.doctorid = d.doctorid
                INNER JOIN department dept ON a.departmentid = dept.departmentid
                WHERE r.patientid = ? 
                AND r.sent_status = 0 
                AND r.reminder_time <= NOW()
                ORDER BY r.reminder_time DESC";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $patientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $reminders = [];
        while($row = mysqli_fetch_assoc($result)) {
            $reminders[] = $row;
            
            // Mark as sent
            $updateSql = "UPDATE ai_appointment_reminders SET sent_status = 1 WHERE reminder_id = ?";
            $updateStmt = mysqli_prepare($this->con, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "i", $row['reminder_id']);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
        }
        
        mysqli_stmt_close($stmt);
        return $reminders;
    }
}

// If called directly (for cron jobs)
if(php_sapi_name() === 'cli' || isset($_GET['cron'])) {
    $reminder = new SmartAppointmentReminder($con);
    $reminder->generateReminders();
    echo "Reminders generated successfully\n";
}
?>

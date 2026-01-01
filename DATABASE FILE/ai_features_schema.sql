-- AI Features Database Schema
-- Additional tables for AI-enhanced hospital management system

-- Table for AI symptom analysis
CREATE TABLE IF NOT EXISTS `ai_symptom_analysis` (
  `analysis_id` int(10) NOT NULL AUTO_INCREMENT,
  `patientid` int(10) NOT NULL,
  `symptoms` text NOT NULL,
  `ai_diagnosis` text NOT NULL,
  `confidence_score` float(5,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`analysis_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for AI medication recommendations
CREATE TABLE IF NOT EXISTS `ai_medication_recommendations` (
  `recommendation_id` int(10) NOT NULL AUTO_INCREMENT,
  `analysis_id` int(10) NOT NULL,
  `patientid` int(10) NOT NULL,
  `medication_name` varchar(255) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `ai_reasoning` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recommendation_id`),
  KEY `analysis_id` (`analysis_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for preventive care advice
CREATE TABLE IF NOT EXISTS `ai_preventive_care` (
  `care_id` int(10) NOT NULL AUTO_INCREMENT,
  `patientid` int(10) NOT NULL,
  `advice_category` varchar(100) NOT NULL,
  `advice_text` text NOT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('Active','Completed','Dismissed') DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`care_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for health risk predictions
CREATE TABLE IF NOT EXISTS `ai_health_risk_predictions` (
  `prediction_id` int(10) NOT NULL AUTO_INCREMENT,
  `patientid` int(10) NOT NULL,
  `risk_type` varchar(100) NOT NULL,
  `risk_level` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `probability` float(5,2) DEFAULT NULL,
  `factors` text,
  `recommendations` text,
  `prediction_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prediction_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for chatbot conversations
CREATE TABLE IF NOT EXISTS `ai_chatbot_conversations` (
  `conversation_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `user_type` enum('patient','doctor','admin') NOT NULL,
  `message` text NOT NULL,
  `response` text NOT NULL,
  `intent` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversation_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for chronic disease monitoring
CREATE TABLE IF NOT EXISTS `ai_chronic_disease_monitoring` (
  `monitoring_id` int(10) NOT NULL AUTO_INCREMENT,
  `patientid` int(10) NOT NULL,
  `disease_name` varchar(255) NOT NULL,
  `vital_signs` text,
  `measurement_date` datetime NOT NULL,
  `alert_triggered` tinyint(1) DEFAULT 0,
  `alert_reason` text,
  `status` varchar(50) DEFAULT 'Normal',
  PRIMARY KEY (`monitoring_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for health trend analytics
CREATE TABLE IF NOT EXISTS `ai_health_trends` (
  `trend_id` int(10) NOT NULL AUTO_INCREMENT,
  `patientid` int(10) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_value` varchar(100) NOT NULL,
  `trend_direction` enum('Improving','Stable','Declining') DEFAULT 'Stable',
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`trend_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for lifestyle recommendations
CREATE TABLE IF NOT EXISTS `ai_lifestyle_recommendations` (
  `lifestyle_id` int(10) NOT NULL AUTO_INCREMENT,
  `patientid` int(10) NOT NULL,
  `category` varchar(100) NOT NULL,
  `recommendation` text NOT NULL,
  `personalization_factors` text,
  `status` enum('Active','Completed','Archived') DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lifestyle_id`),
  KEY `patientid` (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for appointment reminders
CREATE TABLE IF NOT EXISTS `ai_appointment_reminders` (
  `reminder_id` int(10) NOT NULL AUTO_INCREMENT,
  `appointmentid` int(10) NOT NULL,
  `patientid` int(10) NOT NULL,
  `reminder_type` enum('Email','SMS','Push','In-App') NOT NULL,
  `reminder_time` datetime NOT NULL,
  `sent_status` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reminder_id`),
  KEY `appointmentid` (`appointmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for AI model configurations
-- IMPORTANT: API keys should be stored encrypted in production
-- Use AES_ENCRYPT() when inserting and AES_DECRYPT() when retrieving
CREATE TABLE IF NOT EXISTS `ai_model_config` (
  `config_id` int(10) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) NOT NULL,
  `model_type` varchar(100) NOT NULL,
  `endpoint_url` varchar(255) DEFAULT NULL,
  `api_key` varbinary(500) DEFAULT NULL COMMENT 'Encrypted API key - use AES_ENCRYPT/AES_DECRYPT',
  `configuration` text,
  `is_active` tinyint(1) DEFAULT 1,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

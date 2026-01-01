# AI Features Documentation

## Overview
This document describes the AI-enhanced features integrated into the Hospital Management System.

## Features Implemented

### 1. Dr. Cares AI Module
**File**: `ai_dr_cares.php`

The main AI health assistant interface that provides:
- **Symptom Analysis**: AI-powered symptom checker that analyzes patient-reported symptoms and provides preliminary diagnosis
- **Medication Recommendations**: AI-generated medication suggestions based on symptom analysis
- **Preventive Care Advice**: Personalized preventive health recommendations
- **Health Risk Assessment**: Prediction of potential health risks based on patient profile

**Usage**: Accessible to logged-in patients at `/ai_dr_cares.php`

### 2. AI Chatbot
**Files**: `ai_chatbot.php`, `css/ai-chatbot-widget.css`, `js/ai-chatbot-widget.js`

An intelligent conversational interface that:
- Provides 24/7 virtual health assistance
- Answers common health questions
- Guides patients to appropriate services
- Maintains conversation history
- Supports quick action buttons

**Features**:
- **Full Page Chatbot**: `/ai_chatbot.php` - Dedicated chatbot interface
- **Widget Version**: Floating chatbot widget that can be embedded on any page

**Usage**: 
- Full page: Accessible to all logged-in users
- Widget: Include CSS and JS files to add floating chatbot to any page

### 3. Health Trends & Analytics
**File**: `ai_health_trends.php`

Advanced health monitoring and visualization:
- Visual trend charts using Chart.js
- Health metrics tracking (Blood Pressure, Heart Rate, Weight, Blood Sugar, etc.)
- Trend direction indicators (Improving, Stable, Declining)
- Personalized lifestyle recommendations
- AI-generated health insights

**Usage**: Accessible to logged-in patients at `/ai_health_trends.php`

### 4. Chronic Disease Monitoring
**File**: `ai_disease_monitoring.php`

Real-time disease monitoring system:
- Track chronic health conditions
- Record vital signs and measurements
- AI-powered anomaly detection
- Automatic alerts for concerning values
- Historical monitoring data

**Features**:
- Real-time alert system
- Automated risk detection
- Patient-friendly data entry
- Comprehensive monitoring history

**Usage**: Accessible to logged-in patients at `/ai_disease_monitoring.php`

### 5. Doctor AI Dashboard
**File**: `ai_doctor_dashboard.php`

Specialized interface for doctors featuring:
- Patient AI insights summary
- Recent symptom analyses from patients
- Health risk predictions
- Disease monitoring data
- Health trend visualizations
- Comprehensive patient health overview

**Usage**: Accessible to logged-in doctors at `/ai_doctor_dashboard.php`

### 6. Smart Appointment Reminder System
**File**: `ai_modules/smart_appointment_reminder.php`

Intelligent reminder scheduling:
- AI-based priority assessment
- Adaptive reminder frequency based on health conditions
- Multiple reminder types (Email, SMS, Push, In-App)
- Automatic reminder generation

**Features**:
- High-priority patients: 7 days, 3 days, 1 day, 3 hours before appointment
- Medium-priority: 3 days, 1 day before
- Normal-priority: 1 day before

**Usage**: Run as cron job or manually via `/ai_modules/smart_appointment_reminder.php?cron=1`

### 7. AI API Handler
**File**: `ai_modules/ai_api_handler.php`

Backend AI processing engine:
- Symptom analysis algorithms
- Medication recommendation logic
- Preventive care generation
- Health risk prediction
- Chatbot message processing
- Centralized AI model configuration

## Database Schema

The following tables were added to support AI features:

1. **ai_symptom_analysis** - Stores symptom analysis results
2. **ai_medication_recommendations** - AI-generated medication suggestions
3. **ai_preventive_care** - Preventive health advice records
4. **ai_health_risk_predictions** - Health risk assessments
5. **ai_chatbot_conversations** - Chatbot conversation history
6. **ai_chronic_disease_monitoring** - Chronic disease tracking data
7. **ai_health_trends** - Health metrics and trends
8. **ai_lifestyle_recommendations** - Personalized lifestyle advice
9. **ai_appointment_reminders** - Smart appointment reminder system
10. **ai_model_config** - AI model configuration settings

**Schema File**: `DATABASE FILE/ai_features_schema.sql`

## Installation

### 1. Database Setup
```sql
-- Import the AI features schema
SOURCE /path/to/DATABASE FILE/ai_features_schema.sql;
```

### 2. Include Chatbot Widget (Optional)
To add the floating chatbot widget to any page:

```html
<!-- In the <head> section -->
<link rel="stylesheet" href="css/ai-chatbot-widget.css">

<!-- Before closing </body> tag -->
<script src="js/ai-chatbot-widget.js"></script>
```

### 3. Configure Cron Job for Reminders (Optional)
```bash
# Add to crontab to run daily
0 8 * * * php /path/to/ai_modules/smart_appointment_reminder.php
```

## AI Model Integration

Currently, the system uses rule-based algorithms for demonstration. For production:

### Integrating External AI Models:

1. **Update `ai_model_config` table** with your AI service details:
   ```sql
   INSERT INTO ai_model_config (model_name, model_type, endpoint_url, api_key, configuration) 
   VALUES ('symptom_analyzer', 'tensorflow', 'https://your-api.com/analyze', 'your-api-key', '{}');
   ```

2. **Modify `ai_api_handler.php`** to call external APIs:
   - Replace rule-based logic with API calls
   - Handle API authentication
   - Process API responses

### Recommended AI Services:
- **Symptom Analysis**: Ada Health API, Infermedica, Your.MD
- **Medication Recommendations**: DrugBank API, RxNorm API
- **Chatbot**: Dialogflow, Microsoft Bot Framework, Rasa
- **Health Predictions**: Custom TensorFlow/PyTorch models

## Security & Compliance

### Data Privacy
- All health data is stored with patient consent
- Access control enforced at application level
- Sensitive data encrypted in transit (use HTTPS)

### HIPAA/GDPR Compliance Features:
1. **Data Minimization**: Only necessary health information is collected
2. **Access Control**: Role-based access (patient, doctor, admin)
3. **Audit Trail**: All AI analyses are logged with timestamps
4. **Patient Rights**: Patients can view all AI-generated insights about them
5. **Data Retention**: Implement data retention policies as per regulations

### Security Recommendations:
1. **Enable HTTPS** for all pages
2. **Implement database encryption** for sensitive fields
3. **Regular security audits** of AI modules
4. **API key management** - Never commit API keys to version control
5. **Input validation** - All user inputs are sanitized
6. **Session management** - Secure session handling for logged-in users

## API Endpoints

### For Future Development:

```
GET  /api/ai/analyze-symptoms       - Analyze patient symptoms
POST /api/ai/chatbot                - Process chatbot messages
GET  /api/ai/health-risks/:patientId - Get health risk predictions
GET  /api/ai/health-trends/:patientId - Get patient health trends
POST /api/ai/disease-monitoring     - Add disease monitoring data
GET  /api/ai/reminders/:patientId   - Get appointment reminders
```

## Frontend Technologies

- **Bootstrap**: Responsive UI framework
- **Chart.js**: Health trend visualizations
- **Custom CSS**: AI-specific styling with gradients and animations
- **Vanilla JavaScript**: Chatbot widget functionality

## Backend Technologies

- **PHP**: Server-side logic and AI processing
- **MySQL**: Database for AI data storage
- **Prepared Statements**: SQL injection prevention
- **OOP Design**: Modular AI handler class

## Future Enhancements

1. **Machine Learning Models**:
   - Integrate TensorFlow.js for client-side predictions
   - Connect to cloud-based ML services
   - Train custom models on hospital data

2. **Advanced Features**:
   - Voice-enabled chatbot
   - Multi-language support
   - Mobile app integration
   - Telemedicine integration
   - IoT device data integration (wearables, glucometers, etc.)

3. **Analytics Dashboard**:
   - Hospital-wide health trends
   - Disease outbreak prediction
   - Resource allocation optimization

4. **Notification System**:
   - Email notifications for critical alerts
   - SMS reminders for appointments
   - Push notifications for mobile apps

## Support

For questions or issues related to AI features:
- Review this documentation
- Check database logs for errors
- Ensure all required tables exist
- Verify user session is active

## License

These AI enhancements are part of the Hospital Management System and follow the same license as the main project.

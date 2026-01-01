# Hospital Management System

The `hospital-management` repository is an application designed to streamline and optimize the management of a hospital's day-to-day operations. This system centralizes tasks such as patient management, doctor scheduling, and staff monitoring all in one place.

## About the Project

This repository includes:
- **Patient Management**: Records and monitors patient information, including admissions, discharges, and medical history.
- **Doctor Scheduling**: Assigns and manages shifts for doctors efficiently.
- **Staff Management**: Tracks staff availability, roles, and schedules.
- Provides a centralized platform for optimizing workflows and reducing manual paperwork.

## Language Composition

This project involves:
- **Backend**: Implemented to handle the server-side functionalities and database operations.
- **Frontend**: A user-friendly interface for real-time access to data and operations.

## Features

### Core Features
- Manage patient records and history easily.
- Schedule, assign, and monitor doctor and staff shifts.
- Simplify hospital workflows to improve operational efficiency.

### AI-Powered Features ✨ NEW
- **Dr. Cares AI Module**: AI-powered symptom analysis, medication recommendations, and preventive care advice
- **Intelligent Chatbot**: 24/7 virtual health assistant for patient interaction
- **Health Trends & Analytics**: Visual health monitoring with trend analysis and personalized lifestyle recommendations
- **Chronic Disease Monitoring**: Real-time monitoring with automated alerts for anomalies
- **Doctor AI Dashboard**: AI insights and patient health summaries for healthcare providers
- **Smart Appointment Reminders**: Adaptive reminder system based on patient health conditions

📖 **See [AI_FEATURES_README.md](AI_FEATURES_README.md) for detailed AI features documentation**

## How to Use

1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/utasar/hospital-management.git
   ```
2. Navigate to the project directory:
   ```bash
   cd hospital-management
   ```
3. Setup and run the system:
   - Make sure the backend is running (if applicable):
     ```bash
     npm start    # Or any backend setup command based on the implementation
     ```
   - Access the frontend:
     Open your browser and go to the local instance of the system (e.g., `http://localhost:3000`).

## Setup Instructions

### Database Setup
1. Import the main database schema:
   ```bash
   mysql -u root -p ohmsphp < "DATABASE FILE/ohmsphp.sql"
   ```

2. Import the AI features schema:
   ```bash
   mysql -u root -p ohmsphp < "DATABASE FILE/ai_features_schema.sql"
   ```

### Configuration
1. Update database connection in `dbconnection.php` with your credentials
2. Ensure PHP 5.6 or newer is installed
3. Configure web server (Apache/Nginx) to serve the application

### Accessing the System
- **Admin Login**: `admin` / `Password@123`
- **Main Interface**: Navigate to your configured URL (e.g., `http://localhost/hospital-management`)
- **AI Features**: Access via patient/doctor dashboards after login

## Technology Stack

- **Backend**: PHP (5.6+), MySQL
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **AI Features**: Custom AI algorithms, Chart.js for visualizations
- **Security**: Prepared statements, session management, HIPAA/GDPR compliance considerations

## Future Enhancements

- **Machine Learning Integration**: Connect to TensorFlow/PyTorch models for advanced predictions
- **Telehealth Features**: Video consultations and remote patient monitoring
- **Multi-Hospital Support**: Enable management of multiple branches
- **Mobile Apps**: iOS and Android applications
- **IoT Integration**: Support for wearable devices and health monitors

## Contributing

Contributions are welcome! Please ensure any AI features maintain HIPAA/GDPR compliance standards.

## License

See LICENSE file for details.

---

This solution focuses on scalability and improving hospital operational workflows with cutting-edge AI technology to enhance patient care and doctor efficiency.

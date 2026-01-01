<?php include 'header.php'; ?>

<style>
    .privacy-section {
        padding: 60px 0;
        background: #f8f9fa;
    }
    
    .privacy-content {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .privacy-content h1 {
        color: #667eea;
        margin-bottom: 20px;
    }
    
    .privacy-content h2 {
        color: #764ba2;
        margin-top: 30px;
        margin-bottom: 15px;
    }
    
    .privacy-content p, .privacy-content li {
        line-height: 1.8;
        color: #555;
    }
    
    .highlight-box {
        background: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
    }
</style>

<section class="privacy-section">
    <div class="container">
        <div class="privacy-content">
            <h1>🔒 Privacy Policy & Data Security</h1>
            <p><strong>Last Updated:</strong> <?php echo date('F d, Y'); ?></p>
            
            <div class="highlight-box">
                <strong>Important Notice:</strong> We are committed to protecting your health information and maintaining compliance with HIPAA and GDPR regulations.
            </div>
            
            <h2>1. Information We Collect</h2>
            <p>We collect and process the following types of information:</p>
            <ul>
                <li><strong>Personal Information:</strong> Name, date of birth, contact details, address</li>
                <li><strong>Health Information:</strong> Symptoms, diagnoses, medications, treatment records, vital signs</li>
                <li><strong>AI-Generated Data:</strong> Symptom analyses, health risk predictions, medication recommendations</li>
                <li><strong>Usage Data:</strong> Chatbot conversations, appointment history, health trend data</li>
            </ul>
            
            <h2>2. How We Use Your Information</h2>
            <p>Your information is used for:</p>
            <ul>
                <li>Providing healthcare services and AI-powered health insights</li>
                <li>Improving diagnosis accuracy and treatment recommendations</li>
                <li>Monitoring chronic diseases and providing preventive care</li>
                <li>Communicating appointment reminders and health alerts</li>
                <li>Enhancing our AI algorithms (with anonymized data only)</li>
            </ul>
            
            <h2>3. Data Security Measures</h2>
            <p>We implement comprehensive security measures:</p>
            <ul>
                <li><strong>Encryption:</strong> All data transmitted via HTTPS encryption</li>
                <li><strong>Access Control:</strong> Role-based access (patients, doctors, administrators)</li>
                <li><strong>Authentication:</strong> Secure login with password protection</li>
                <li><strong>Audit Trails:</strong> All AI analyses are logged with timestamps</li>
                <li><strong>Data Minimization:</strong> We collect only necessary information</li>
            </ul>
            
            <h2>4. HIPAA Compliance</h2>
            <p>We adhere to the Health Insurance Portability and Accountability Act (HIPAA) by:</p>
            <ul>
                <li>Maintaining privacy of Protected Health Information (PHI)</li>
                <li>Implementing appropriate safeguards for electronic PHI</li>
                <li>Limiting disclosure of health information to authorized parties only</li>
                <li>Providing patients with access to their health records</li>
                <li>Training staff on HIPAA requirements</li>
            </ul>
            
            <h2>5. GDPR Compliance</h2>
            <p>For users in the European Union, we comply with GDPR by:</p>
            <ul>
                <li>Obtaining explicit consent for data processing</li>
                <li>Providing the right to access your data</li>
                <li>Enabling data portability and deletion requests</li>
                <li>Appointing a Data Protection Officer</li>
                <li>Reporting data breaches within 72 hours</li>
            </ul>
            
            <h2>6. AI Features & Data Processing</h2>
            <p>Our AI features process your health data to provide:</p>
            <ul>
                <li>Symptom analysis and preliminary diagnosis</li>
                <li>Medication recommendations based on your condition</li>
                <li>Health risk predictions and preventive advice</li>
                <li>Personalized lifestyle recommendations</li>
                <li>Chronic disease monitoring and alerts</li>
            </ul>
            
            <div class="highlight-box">
                <strong>Important:</strong> AI-generated insights are for informational purposes only and do not replace professional medical advice. Always consult with qualified healthcare providers.
            </div>
            
            <h2>7. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access your health information at any time</li>
                <li>Request corrections to your data</li>
                <li>Withdraw consent for AI processing</li>
                <li>Request deletion of your data (subject to legal requirements)</li>
                <li>Export your health data</li>
                <li>File a complaint with regulatory authorities</li>
            </ul>
            
            <h2>8. Data Sharing</h2>
            <p>We do NOT share your personal health information with third parties except:</p>
            <ul>
                <li>With your explicit consent</li>
                <li>With healthcare providers involved in your care</li>
                <li>When required by law or court order</li>
                <li>In medical emergencies to protect your health</li>
            </ul>
            
            <h2>9. Data Retention</h2>
            <p>We retain your health information:</p>
            <ul>
                <li>For as long as necessary to provide healthcare services</li>
                <li>In compliance with legal and regulatory requirements</li>
                <li>Until you request deletion (where legally permissible)</li>
            </ul>
            
            <h2>10. Cookies and Tracking</h2>
            <p>We use cookies to:</p>
            <ul>
                <li>Maintain your session and login status</li>
                <li>Remember your preferences</li>
                <li>Improve system performance and user experience</li>
            </ul>
            <p>You can disable cookies in your browser settings, but this may affect functionality.</p>
            
            <h2>11. Contact Us</h2>
            <p>For privacy concerns, data requests, or questions about our AI features:</p>
            <ul>
                <li><strong>Email:</strong> privacy@hmsphp.com</li>
                <li><strong>Phone:</strong> 1010 2020 36360</li>
                <li><strong>Address:</strong> 1942 Poe Lane, Kansas City</li>
            </ul>
            
            <h2>12. Changes to This Policy</h2>
            <p>We may update this privacy policy periodically. We will notify you of significant changes via email or through our system notifications.</p>
            
            <div class="highlight-box" style="margin-top: 40px;">
                <strong>Your Consent:</strong> By using our AI-powered healthcare services, you acknowledge that you have read and understood this Privacy Policy and agree to the collection and use of your information as described.
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="index.php" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px;">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

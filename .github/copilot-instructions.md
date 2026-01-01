# GitHub Copilot Instructions for Hospital Management System

## Project Overview

This is a PHP-based Hospital Management System designed to streamline hospital operations including patient management, doctor scheduling, appointments, billing, and treatment records.

## Technology Stack

- **Backend**: PHP 5.6+ with MySQL/MariaDB
- **Database**: MySQL (ohmsphp)
- **Frontend**: HTML, CSS, JavaScript, Bootstrap
- **Server**: Apache/Nginx with PHP support

## Database Configuration

- **Database Name**: `ohmsphp`
- **Connection File**: `dbconnection.php`
- **Default Connection**: localhost with root user
- **Database Schema**: Located in `DATABASE FILE/ohmsphp.sql`

## Project Structure

- **Root Level**: Main application PHP files for different modules
- **assets/**: Static assets (CSS, JS, images, fonts)
- **inc/**: Include files and utilities
- **php/**: Additional PHP modules
- **DATABASE FILE/**: Database schema and setup files

## Key Modules

1. **Admin Management**: admin.php, adminlogin.php, adminprofile.php
2. **Doctor Management**: doctor.php, doctorlogin.php, doctorprofile.php, doctortimings.php
3. **Patient Management**: patient.php, patientlogin.php, patientprofile.php
4. **Appointments**: appointment.php, appointmentapproval.php, viewappointment.php
5. **Billing**: billing.php, payment.php, viewbilling.php
6. **Prescriptions**: prescription.php, viewprescription.php
7. **Treatments**: treatment.php, treatmentrecord.php

## Coding Standards

### PHP Code Style

1. **File Structure**:
   - Include header file at the top (e.g., `include("adheader.php")`)
   - Include database connection (e.g., `include("dbconnection.php")`)
   - Process POST data for form submissions
   - Query data for editing (if editid parameter exists)
   - HTML/Form structure

2. **Variable Naming**:
   - Use lowercase with no spaces for POST variables (e.g., `$_POST[submit]`, `$_POST[adminname]`)
   - Use descriptive names for database fields matching table columns
   - Use `$con` for database connection variable

3. **Database Operations**:
   - Use `mysqli_query()` for all database operations
   - Always check query results and handle errors
   - Use `mysqli_error($con)` for error reporting
   - Use parameterized table/column names in queries
   - **Security Note**: Current code uses direct variable interpolation in SQL queries - when modifying, consider adding input validation and sanitization

4. **Form Handling**:
   - Check `isset($_POST[submit])` for form submissions
   - Check `isset($_GET[editid])` to differentiate between INSERT and UPDATE operations
   - Use UPDATE queries when editid is present
   - Use INSERT queries for new records

5. **User Feedback**:
   - Use Bootstrap alert classes for success messages: `<div class='alert alert-success'>`
   - Use `echo "<script>alert('message');</script>"` for JavaScript alerts
   - Display `mysqli_error()` for database errors during development

6. **Session Management**:
   - Use `$_SESSION[adminid]`, `$_SESSION[doctorid]`, `$_SESSION[patientid]` for user sessions
   - Check session variables to determine user context and permissions

### HTML/Frontend Standards

1. **Bootstrap Framework**: Use Bootstrap classes for styling and responsive design
2. **Form Elements**: Follow existing form structure with proper labels and validation
3. **Navigation**: Use consistent header/footer includes (adheader.php, adfooter.php, etc.)

### Security Considerations

When working with this codebase:

1. **SQL Injection**: The current code is vulnerable to SQL injection. When adding new features:
   - Validate and sanitize all user inputs
   - Consider using prepared statements with mysqli_prepare()
   - Never trust user input directly in SQL queries

2. **Password Storage**: Passwords are stored in plain text. For new features:
   - Consider using password_hash() and password_verify()
   - Implement secure password policies

3. **Session Security**:
   - Always validate session data
   - Implement proper session timeout mechanisms

4. **XSS Prevention**:
   - Sanitize output when displaying user-generated content
   - Use htmlspecialchars() or htmlentities() for output escaping

## Common Patterns

### Adding a New Module

1. Create main file (e.g., `newmodule.php`)
2. Include appropriate header file
3. Include `dbconnection.php`
4. Implement form submission handling with INSERT/UPDATE logic
5. Create corresponding view file (e.g., `viewnewmodule.php`)

### Database Query Pattern

```php
if(isset($_POST[submit])) {
    if(isset($_GET[editid])) {
        // UPDATE operation
        $sql = "UPDATE table SET field='$_POST[field]' WHERE id='$_GET[editid]'";
    } else {
        // INSERT operation
        $sql = "INSERT INTO table(field) values('$_POST[field]')";
    }
    
    if($qsql = mysqli_query($con,$sql)) {
        // Success message
    } else {
        echo mysqli_error($con);
    }
}
```

### Editing Existing Records

```php
if(isset($_GET[editid])) {
    $sql = "SELECT * FROM table WHERE id='$_GET[editid]'";
    $qsql = mysqli_query($con,$sql);
    $rsedit = mysqli_fetch_array($qsql);
}
```

## Database Tables

Main tables include:
- `admin`: Administrator accounts
- `doctor`: Doctor information and credentials
- `patient`: Patient records
- `appointment`: Appointment scheduling
- `billing`: Billing and payment records
- `prescription`: Prescription records
- `treatment`: Treatment records
- `department`: Hospital departments
- `room`: Room management
- `medicine`: Medicine inventory

Refer to `DATABASE FILE/ohmsphp.sql` for complete schema.

## Testing Approach

- Manual testing through the web interface
- Test with default admin credentials (see `01 LOGIN DETAILS & PROJECT INFO.txt`)
- Verify database operations through phpMyAdmin or similar tools
- Test all CRUD operations for each module

## Important Notes

1. **PHP Version**: Recommended PHP 5.6 or newer
2. **Default Credentials**: Admin login - Username: `admin`, Password: `Password@123`
3. **Database Setup**: Import `DATABASE FILE/ohmsphp.sql` before running the application
4. **Patient Registration**: Making an appointment automatically creates a patient account
5. **Status Field**: Most entities have a status field (Active/Inactive)

## When Making Changes

1. **Preserve Existing Patterns**: Follow the established coding patterns in the codebase
2. **Test Thoroughly**: Test all affected functionality after making changes
3. **Consider Security**: Add input validation and sanitization for new code
4. **Maintain Consistency**: Keep naming conventions and file structures consistent
5. **Document Database Changes**: Update schema documentation if modifying database structure

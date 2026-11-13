<?php
session_start();
require_once __DIR__ . '/dbconnection.php';
require_once __DIR__ . '/ai_modules/ai_api_handler.php';

if (!isset($_SESSION['patientid'])) {
    header('Location: patientlogin.php');
    exit;
}

if (empty($_SESSION['ai_csrf_token'])) {
    $_SESSION['ai_csrf_token'] = bin2hex(random_bytes(32));
}

$analysis = null;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['analyze_symptoms'])) {
    $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    if (!hash_equals($_SESSION['ai_csrf_token'], $token)) {
        http_response_code(400);
        $error = 'Your form session expired. Reload the page and try again.';
    } else {
        try {
            $handler = new AIAPIHandler($con);
            $symptoms = isset($_POST['symptoms']) ? $_POST['symptoms'] : '';
            $analysis = $handler->analyzeSymptoms($symptoms, (int) $_SESSION['patientid']);
        } catch (Exception $exception) {
            $error = $exception instanceof InvalidArgumentException
                ? $exception->getMessage()
                : 'We could not save your request. Please contact the clinic if you need help.';
            if (!($exception instanceof InvalidArgumentException)) {
                error_log('AI symptom intake error: ' . $exception->getMessage());
            }
        }
    }
}

function ai_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health information | Hospital Management System</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background:#f2f6fa; padding:32px 12px; color:#203040; }
        .panel { max-width:760px; margin:0 auto; padding:28px; background:#fff; border-radius:12px; box-shadow:0 6px 24px rgba(0,0,0,.08); }
        .notice { padding:16px; margin:18px 0; border-left:5px solid #b45309; background:#fff7ed; }
        .urgent { border-color:#b91c1c; background:#fef2f2; }
        textarea { min-height:130px; }
    </style>
</head>
<body>
<main class="panel">
    <h1>Health information assistant</h1>
    <p>This feature records what you choose to share and gives general next-step guidance. It does not use a medical AI model.</p>
    <div class="notice" role="note">
        <strong>Not for emergencies or medical decisions.</strong> It cannot diagnose, assess severity, recommend medication, or replace a clinician. For severe or rapidly worsening symptoms, contact your local emergency service now.
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert"><?php echo ai_h($error); ?></div>
    <?php endif; ?>

    <?php if ($analysis !== null): ?>
        <div class="notice <?php echo $analysis['urgent'] ? 'urgent' : ''; ?>" role="status">
            <strong><?php echo $analysis['urgent'] ? 'Urgent help may be needed.' : 'Next step'; ?></strong>
            <p><?php echo ai_h($analysis['diagnosis']); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo ai_h($_SESSION['ai_csrf_token']); ?>">
        <div class="form-group">
            <label for="symptoms">Describe what you would like to discuss with your healthcare professional</label>
            <textarea class="form-control" id="symptoms" name="symptoms" maxlength="4000" required></textarea>
        </div>
        <button type="submit" name="analyze_symptoms" class="btn btn-primary">Get general next-step guidance</button>
    </form>

    <hr>
    <p>For diagnosis, medication, test interpretation, or personalized care, contact a licensed healthcare professional.</p>
    <a href="patientprofile.php">Back to patient profile</a>
</main>
</body>
</html>

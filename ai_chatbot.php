<?php
session_start();
include("dbconnection.php");
include("ai_modules/ai_api_handler.php");

$aiHandler = new AIAPIHandler($con);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['patientid']) || isset($_SESSION['doctorid']) || isset($_SESSION['adminid']);

if(!$isLoggedIn) {
    header("Location: patientlogin.php");
    exit();
}

// Determine user type and ID
if(isset($_SESSION['patientid'])) {
    $userId = $_SESSION['patientid'];
    $userType = 'patient';
} elseif(isset($_SESSION['doctorid'])) {
    $userId = $_SESSION['doctorid'];
    $userType = 'doctor';
} else {
    $userId = $_SESSION['adminid'];
    $userType = 'admin';
}

// Handle chat message submission
if(isset($_POST['send_message'])) {
    $message = $_POST['message'];
    $chatResponse = $aiHandler->processChatbotMessage($userId, $userType, $message);
}

// Get conversation history
$sql = "SELECT * FROM ai_chatbot_conversations WHERE user_id = ? AND user_type = ? ORDER BY created_at DESC LIMIT 50";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "is", $userId, $userType);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$conversations = [];
while($row = mysqli_fetch_assoc($result)) {
    $conversations[] = $row;
}
mysqli_stmt_close($stmt);
$conversations = array_reverse($conversations);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Cares AI Chatbot</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .chat-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .chat-header h2 {
            margin: 0;
            font-size: 1.8em;
        }
        
        .chat-messages {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        
        .message.user {
            justify-content: flex-end;
        }
        
        .message-content {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            position: relative;
        }
        
        .message.user .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message.bot .message-content {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 4px;
        }
        
        .message-time {
            font-size: 0.75em;
            opacity: 0.7;
            margin-top: 5px;
        }
        
        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }
        
        .chat-input {
            display: flex;
            gap: 10px;
        }
        
        .chat-input input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            outline: none;
            font-size: 1em;
        }
        
        .chat-input input:focus {
            border-color: #667eea;
        }
        
        .btn-send {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-send:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .bot-avatar {
            width: 35px;
            height: 35px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            font-size: 1.2em;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background: #764ba2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            color: white;
            font-size: 1.2em;
        }
        
        .quick-actions {
            padding: 15px 20px;
            background: #f0f0f0;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .quick-action-btn {
            padding: 8px 15px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }
        
        .quick-action-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <h2>🤖 Dr. Cares AI Chatbot</h2>
        <p style="margin: 5px 0 0 0; font-size: 0.9em;">Your 24/7 Virtual Health Assistant</p>
    </div>
    
    <div class="quick-actions">
        <button class="quick-action-btn" onclick="sendQuickMessage('Book an appointment')">📅 Book Appointment</button>
        <button class="quick-action-btn" onclick="sendQuickMessage('I have symptoms')">🩺 Check Symptoms</button>
        <button class="quick-action-btn" onclick="sendQuickMessage('Medication information')">💊 Medication Info</button>
        <button class="quick-action-btn" onclick="sendQuickMessage('Health tips')">💪 Health Tips</button>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <!-- Welcome message -->
        <div class="message bot">
            <div class="bot-avatar">🤖</div>
            <div class="message-content">
                <div>Hello! I'm Dr. Cares AI, your virtual health assistant. How can I help you today?</div>
                <div class="message-time">Just now</div>
            </div>
        </div>
        
        <?php foreach($conversations as $conv): ?>
        <!-- User message -->
        <div class="message user">
            <div class="message-content">
                <div><?php echo htmlspecialchars($conv['message']); ?></div>
                <div class="message-time"><?php echo date('H:i', strtotime($conv['created_at'])); ?></div>
            </div>
            <div class="user-avatar">👤</div>
        </div>
        
        <!-- Bot response -->
        <div class="message bot">
            <div class="bot-avatar">🤖</div>
            <div class="message-content">
                <div><?php echo nl2br(htmlspecialchars($conv['response'])); ?></div>
                <div class="message-time"><?php echo date('H:i', strtotime($conv['created_at'])); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(isset($chatResponse)): ?>
        <!-- Latest user message -->
        <div class="message user">
            <div class="message-content">
                <div><?php echo htmlspecialchars($message); ?></div>
                <div class="message-time">Just now</div>
            </div>
            <div class="user-avatar">👤</div>
        </div>
        
        <!-- Latest bot response -->
        <div class="message bot">
            <div class="bot-avatar">🤖</div>
            <div class="message-content">
                <div><?php echo nl2br(htmlspecialchars($chatResponse['response'])); ?></div>
                <div class="message-time">Just now</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="chat-input-area">
        <form method="post" action="" class="chat-input" id="chatForm">
            <input type="text" name="message" id="messageInput" placeholder="Type your message here..." required autocomplete="off">
            <button type="submit" name="send_message" class="btn-send">Send</button>
        </form>
    </div>
</div>

<div style="text-align: center; margin-top: 20px;">
    <a href="<?php echo ($userType == 'patient') ? 'patientprofile.php' : (($userType == 'doctor') ? 'doctorprofile.php' : 'admin.php'); ?>" 
       style="color: white; text-decoration: none; font-size: 1.1em;">
        ← Back to Dashboard
    </a>
</div>

<script src="js/jquery.min.js"></script>
<script>
    // Auto-scroll to bottom of chat
    function scrollToBottom() {
        var chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Scroll to bottom on page load
    scrollToBottom();
    
    // Quick message sender
    function sendQuickMessage(message) {
        document.getElementById('messageInput').value = message;
        document.getElementById('chatForm').submit();
    }
    
    // Focus on input
    document.getElementById('messageInput').focus();
</script>
</body>
</html>

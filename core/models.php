<?php
require_once 'dbConfig.php';

//auth
/*
function isLoggedIn() {
    //return isset($_SESSION['user_id']);
    echo isset($_SESSION['user_id']);
}*/

function isHR() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'HR';
}

function isApplicant() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Applicant';
}

//redirect

function redirect($url) {
    header("Location: $url");
    exit();
}


//login and register
function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM users WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}


function insertNewUser($pdo, $username, $password, $role) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username, $role); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO users (username, password, role) 
		VALUES (?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $password, $role])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}


//hr

//create job
function createJobPost($pdo, $title, $description) {
    try {
        $stmt = $pdo->prepare("INSERT INTO job_posts (title, description) VALUES (?, ?)");
        $stmt->execute([$title, $description]);
        return true;
    } catch (PDOException $e) {
        error_log("Error creating job post: " . $e->getMessage());
        return false;
    }
}

//get job posts
function getJobPosts($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM job_posts ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all posts as an associative array
    } catch (PDOException $e) {
        error_log("Error fetching job posts: " . $e->getMessage());
        return [];  // Return an empty array in case of an error
    }
}

//job post by ID
function getJobPostById($pdo, $jobID) {
    $sql = "SELECT * FROM job_posts WHERE id = :jobID";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':jobID', $jobID, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateApplicationStatus($pdo, $applicationID, $newStatus) {
    $sql = "UPDATE applications 
            SET application_status = :newStatus 
            WHERE id = :applicationID";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
    $stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);

    return $stmt->execute();
}

function getApplicationsByJobId($pdo, $jobId) {
    $sql = "SELECT ja.id AS application_id, u.username, ja.applicant_id, ja.application_status, 
                   ja.application_message, ja.resume 
            FROM applications ja
            JOIN users u ON ja.applicant_id = u.id
            WHERE ja.job_id = :jobId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// get all jobs
function getAllJobPosts($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM job_posts");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all job posts
    } catch (PDOException $e) {
        error_log("Error in getAllJobPosts: " . $e->getMessage());
    }
    return []; // Return an empty array if there's an error
}


// apply for job
function applyForJob($pdo, $applicantID, $jobID, $message, $resumePath) {
    try {
        // sets default status to pending
        $applicationStatus = 'Pending';

        // prepare SQL query
        $stmt = $pdo->prepare("INSERT INTO applications (applicant_id, job_id, application_status, applied_at, application_message, resume) 
                               VALUES (:applicant_id, :job_id, :application_status, CURRENT_TIMESTAMP, :application_message, :resume)");

        // bind parameters
        $stmt->bindParam(':applicant_id', $applicantID);
        $stmt->bindParam(':job_id', $jobID);
        $stmt->bindParam(':application_status', $applicationStatus);
        $stmt->bindParam(':application_message', $message);
        $stmt->bindParam(':resume', $resumePath);

        // execute the query and check if it was successful
        if ($stmt->execute()) {
            return true;
        } else {
            // If execution failed, log the error
            $errorInfo = $stmt->errorInfo();
            error_log("Error inserting application: " . implode(", ", $errorInfo));
            return false;
        }
    } catch (PDOException $e) {
        // log the PDO exception
        error_log("Error in applyForJob: " . $e->getMessage());
        return false;
    }
}



// get applications
function getUserApplications($pdo, $userID) {
    $sql = "SELECT ja.id AS applicationID, jp.title AS job_title, ja.application_status AS status
            FROM applications ja
            JOIN job_posts jp ON ja.job_id = jp.id
            WHERE ja.applicant_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// send a follow up message
function sendFollowUpMessage($pdo, $senderID, $messageContent) {
    $sql = "INSERT INTO messages (applicant_id, message) VALUES (:sender_id, :message_content)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sender_id', $senderID, PDO::PARAM_INT);
    $stmt->bindParam(':message_content', $messageContent, PDO::PARAM_STR);  
    
    return $stmt->execute(); 
}

//messages
function getMessages($userId, $otherUserId)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT messages.*, 
               sender.username AS sender_name, 
               receiver.username AS receiver_name 
        FROM messages 
        INNER JOIN users AS sender ON messages.sender_id = sender.id
        INNER JOIN users AS receiver ON messages.receiver_id = receiver.id
        WHERE (messages.sender_id = :userId AND messages.receiver_id = :otherUserId)
           OR (messages.sender_id = :otherUserId AND messages.receiver_id = :userId)
        ORDER BY messages.sent_at ASC
    ");
    $stmt->execute(['userId' => $userId, 'otherUserId' => $otherUserId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sendMessage($senderId, $receiverId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    return $stmt->execute([$senderId, $receiverId, $content]);
}
?>


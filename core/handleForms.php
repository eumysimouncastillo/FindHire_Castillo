<?php  
require_once 'dbConfig.php';
require_once 'models.php';

//login and register
if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
    $role = $_POST['role'];

	if (!empty($username) && !empty($password)) {

        $insertQuery = insertNewUser($pdo, $username, password_hash($password, PASSWORD_DEFAULT), $role);
        $_SESSION['message'] = $insertQuery['message'];

        if ($insertQuery['status'] == '200') {
            $_SESSION['message'] = $insertQuery['message'];
            $_SESSION['status'] = $insertQuery['status'];
            header("Location: ../views/auth/login.php");
        }

        else {
            $_SESSION['message'] = $insertQuery['message'];
            $_SESSION['status'] = $insertQuery['status'];
            header("Location: ../views/auth/register.php");
        }

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../views/auth/register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];
        $roleFromDB = $loginQuery['userInfoArray']['role'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
            $_SESSION['role'] = $roleFromDB;
			
            if (isHR()) {
                redirect('../views/hr/dashboard.php');
            } 
            elseif (isApplicant()) {
                redirect('../views/applicant/dashboard.php');
            }
            else {
            redirect('../views/auth/login.php');
            }
            //header("Location: ../index.php");


		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../views/auth/login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../views/auth/register.php");
	}

}

//hr

//create job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createJob'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    if (createJobPost($pdo, $title, $description)) {
        $_SESSION['message'] = "Job post created successfully!";
        header("Location: ../views/hr/jobPosts.php");
        exit;
    } else {
        $_SESSION['error'] = "Error creating job post...";
        header("Location: ../views/hr/jobPosts.php");
        exit;
    }
}

//job application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyToJobBtn'])) {
    $jobID = $_POST['jobID'] ?? '';
    $applicantID = $_POST['applicantID'] ?? $_SESSION['user_id'];
    $message = $_POST['message'] ?? '';
    $resume = $_FILES['resume'] ?? null;

    // validate inputs
    if (empty($jobID) || empty($message) || !$resume || $resume['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "All fields are required, please upload your resume in a PDF file.";
        
        exit;
    }

    // Upload resume
    $uploadDirectory = '../assets/uploads/'; 
    $resumePath = $uploadDirectory . basename($resume['name']); 

    if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
        $isApplicationSaved = applyForJob($pdo, $applicantID, $jobID, $message, $resumePath);
        
        if ($isApplicationSaved) {
            $_SESSION['message'] = "Application submitted successfully!";
            header("Location: ../views/applicant/dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Error submitting application...";
            header("Location: ../views/applicant/dashboard.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error uploading resume...";
        header("Location: ../views/applicant/dashboard.php");
        exit;
    }
}

// handle follow-up message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendFollowUpBtn'])) {
    $applicationID = $_POST['applicationID'] ?? '';
    $followUpMessage = $_POST['followUpMessage'] ?? '';
    $senderID = $_SESSION['user_id'];

    // validate inputs
    if (empty($applicationID) || empty($followUpMessage)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../job_application.php");
        exit;
    }

    // save follow-up message
    if (sendFollowUpMessage($pdo, $senderID, $followUpMessage)) {
        $_SESSION['message'] = "Follow-up sent successfully!";
        header("Location: ../views/applicant/applications.php");
        exit;
    } else {
        $_SESSION['error'] = "Error sending follow-up...";
        header("Location: ../views/applicant/applications.php");
        exit;
    }
}

?>
<?php
require_once 'core/models.php';

if (isHR()) {
    redirect('views/hr/dashboard.php');
} 
elseif (isApplicant()) {
    redirect('views/applicant/dashboard.php');
}
else {
    redirect('views/auth/login.php');
}




?>
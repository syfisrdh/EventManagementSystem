<?php
    include('../../config.php');
    include('../../utils.php');

    session_start();
    validateSession('student_id', '../../index.php');

    $student_id = $_SESSION['student_id'];
	$sql = "SELECT * FROM student WHERE student_id = '$student_id'";
	$result = mysqli_query($conn, $sql);
	if($result){
		$row = mysqli_fetch_assoc($result);
		if($row){
			$student_name= $row['student_name'];
			$student_ic = $row['student_ic'];
			$student_id = $row['student_id'];
			$student_email = $row['student_email'];
			$student_phone = $row['student_phone'];
			$student_address = $row['student_address'];
			$student_profilePic = $row['student_profilePic'];
		}
	}

    customHeader('Student Profile', '../../../public/css/style.css', '../../../public/icon/icon.png');
?>


    <body>
        <?php
            studentNavigation();
            popUpSuccess('student_profile.php')
        ?>
        
        <div id="popup-form" class="popup-container">
            <div class="changepass-box">
                <form action="student_profile.php" method="POST" class="changepass-form">
                	<h1>Change Password</h1>
                    <p>New Password</p>
                    <input type="password" id="new_pwd" name="new_pwd" required>
                    <p>Confirm New Password</p>
                    <input type="password" id="confirm_pwd" name="confirm_pwd" required>
                    <br><br>
					<div>
						<button class="normal-btn" type="button" value="" onclick="location.href='student_profile.php'">Cancel</button>
						<button class="normal-btn" type="submit">Confirm</button>
					</div>
                </form>
            </div>
        </div>

        <div class="profile-row">
            <div class="profile-left">
                <?php echo "<img src='../../../public/storage/profile/$student_profilePic' alt='Profile Picture'>";?> 
            </div>
            
            <div class="profile-right">
                <h1> My Profile </h1>
				<table width="100%" class="table-profile">
                    <tr>    
						<th> Name </th>
						<td class="fill">:</td>
						<td><?php echo"$student_name";?></td>
					</tr>  
                    <tr>    
						<th> Identity Card Number </th>
						<td class="fill">:</td>
						<td><?php echo"$student_ic";?></td>
					</tr> 
                    <tr>    
						<th> Matrics Number </th>
						<td class="fill">:</td>
						<td><?php echo"$student_id";?></td>
					</tr> 
                    <tr>    
						<th> E-mail </th>
						<td class="fill">:</td>
						<td><?php echo"$student_email";?></td>
					</tr> 
                    <tr>    
						<th> Phone Number </th>
						<td class="fill">:</td>
						<td><?php echo"$student_phone";?></td>
					</tr> 
                    <tr>    
						<th> Address </th>
						<td class="fill">:</td>
						<td><?php echo"$student_address";?></td>
					</tr>  
                </table>
                <div>
                    <br><br>
                    <button class="normal-btn" type="button" onclick="open_change_pass()">Change Password</button>
                    <button class="normal-btn" type="button" onclick="location.href='student_profile_edit.php'">Update</button>
                </div>
            </div>
        </div>
    </body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $newpass = trim($_POST["new_pwd"]);
            $confirmpass = trim($_POST["confirm_pwd"]);
            $pwd_hash = trim(password_hash($confirmpass, PASSWORD_DEFAULT));


            if($newpass == $confirmpass){
                $student_id = $_SESSION['student_id'];
                $sqlUpdate = "UPDATE student SET student_pwd = '$pwd_hash' WHERE student_id = '$student_id'";
                $status = executeQuery($conn, $sqlUpdate);
                if($status){
                    echo '<script>popup_message("New password has been updated");</script>';
                }
                else{
                    echo '<script>popup_message("There was an error updating your new password");</script>';
                }
            }
            else{
                echo '<script>popup_message("New password and confirm password do not match");</script>';
            }
        }

        $conn -> close();
    ?>

</html>
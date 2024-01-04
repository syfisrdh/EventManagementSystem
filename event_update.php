<?php
include("config.php");

// Start or resume the session
session_start();

// Check if the user is not logged in, redirect to the login page
// if (!isset($_SESSION['student_id'])) {
//     header("Location: index.php");
//     exit();
// }

// Function to insert data into the database table using prepared statements
function update_table($conn, $sql){
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        echo "Error: " . $sql . " : " . mysqli_error($conn) . "<br>";
        return false;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Values for add or edit
    $event_id = $_POST['event_id'];
    $event_pwd = $_POST['event_pwd'];
    $event_status = $_POST['event_status'];
    $event_posterDesc= $_POST['event_posterDesc'];

    $target_dir = "uploads/poster/";


    $uploadstat = 0;

    // Retrieve certification student details for display
    $stmtSelect = $conn->prepare("SELECT * FROM event WHERE event_id = ?");
    $stmtSelect->bind_param("i", $event_id);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    $event = $result->fetch_assoc();
    $stmtSelect->close();


    // Check if there is an image to be uploaded
    if (isset($_FILES['poster-img']) && $_FILES['poster-img']["name"] != "") {
        
        $filetmp = $_FILES['poster-img'];
        $uploadfileName = $filetmp["name"];
        $imageFileType = strtolower(pathinfo($uploadfileName, PATHINFO_EXTENSION));
        $randomString = bin2hex(random_bytes(8)); // Generates a random string (16 characters in this case)
        $timestamp = time();
        $target_file = $target_dir . $event_id . "_" . $randomString . "_" . $timestamp . "." . $imageFileType;
        $imgnewname = $event_id . "_" . $randomString . "_" . $timestamp . "." . $imageFileType;
        

        $currimagetarget_file = "uploads/poster/" . $event['event_poster'];


        // Check file size > 100mb
        if ($_FILES['poster-img']["size"] > 100000000) {
            $uploadstat = 0;
            echo "<script>alert('Size image is too big. Please resize');</script>";
            echo "<script>window.location.href='mycertification_edit.php';</script>";
            exit();
        }

        // Allow only these file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", ])) {
            $uploadstat = 0;
            echo "<script>alert('Sorry, only JPG, JPEG & PNG files are allowed.');</script>";
            echo "<script>window.location.href='mycertification_edit.php';</script>";
            exit();
        }

        if (file_exists($currimagetarget_file)){
            unlink($currimagetarget_file);
            $uploadstat = 1;
        }
        else{
            $uploadstat = 1;
        }

        if($uploadstat){
            $sql = "UPDATE event SET event_pwd = '$event_pwd', event_status = '$event_status', event_posterDesc = '$event_posterDesc', event_poster='$imgnewname' WHERE  event_id='$event_id'";
            $status = update_table($conn, $sql);
            if($status){
                if(move_uploaded_file($_FILES["poster-img"]["tmp_name"], $target_file)){  	
                    // Display a popup message
                    echo "<script>alert('Event updated successfully!');</script>";
    
                    // Redirect to the profile page after successful update
                    echo "<script>window.location.href='event_view.php?id=$event_id';</script>";
                    exit();
                }
                else{
                    echo "<script>alert('Failed to update event details');</script>";
    
                    // Redirect to the profile page after successful update
                    echo "<script>window.location.href='event_view.php?id=$event_id';</script>";
                }
            }
            else{
                echo '<script>connection_fail();</script>';
            }
        }

    } else {
        // No image to upload, insert data into the database

        $sql = "UPDATE event SET event_pwd = '$event_pwd', event_status = '$event_status', event_posterDesc = '$event_posterDesc' WHERE  event_id='$event_id'";
        $status = update_table($conn, $sql);

        // $stmtUpdate = $conn->prepare("UPDATE student SET cert_semester=?, cert_year=?, cert_date=?, cert_name=?, cert_level=? WHERE std_ID=? AND cert_ID=?");
        // $stmtUpdate->bind_param("ssssssi", $cert_semester, $cert_year, $cert_date, $cert_name, $cert_level, $studentNo, $cert_ID);


        if ($status) {
            // Display a popup message
           // Display a popup message
           echo "<script>alert('Event updated successfully!');</script>";
           // Redirect to the profile page after successful update
           echo "<script>window.location.href='event_view.php?id=$event_id';</script>";
           exit();
       }
       else{
            echo "<script>alert('Failed to update event details');</script>";
    
            // Redirect to the profile page after successful update
            echo "<script>window.location.href='event_view.php?id=$event_id';</script>";
       }
    
        $stmtUpdate->close();
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <header>
    <img class="banner" src="img/banner.png">
    </header>
</head>
<body>

<?php include("navigation_pmfki.php"); ?>

<h2>View Event Details</h2>

<?php
    

    if(isset($_GET["id"]) && $_GET["id"] != ""){
        $sql = "SELECT e.*, p.pmfki_name, a.name
        FROM event e
        LEFT JOIN pmfki p ON e.pmfki_id = p.pmfki_id
        LEFT JOIN fki_admin a ON e.admin_id = a.admin_id
        WHERE e.event_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $_GET["id"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $event_id = $row["event_id"];
            $event_name = $row["event_name"];
            $event_synopsis = $row["event_synopsis"];
            $event_objective = $row["event_objective"];
            $event_impact = $row["event_impact"];
            $event_posterDesc = $row["event_posterDesc"];
            $event_startDate = $row["event_startDate"];
            $event_endDate = $row["event_endDate"];
            $event_startTime = $row["event_startTime"];
            $event_endTime = $row["event_endTime"];
            $event_venue = $row["event_venue"];
            $event_poster = $row["event_poster"];
            $event_pwd = $row["event_pwd"];
            $event_status = $row["event_status"];
            $admin_name = $row["name"];
            $pmfki_name = $row["pmfki_name"];
        }
       
    }

?>

<main>
<div class="row">
            <div class="col-left"> 
                <br>
                <img src="uploads/poster/<?php echo $event_poster; ?>" alt="poster img" class="view-event-poster">
            </div>

            <br><br>
            <div class="col-right"> 
                <br>
                <form action="event_update.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="event_id" name="event_id" value="<?=$_GET['id']?>">
                <table class="update-event-table">
                    <tbody>
                        <tr>
                            <th>Event Name</th>
                            <td><b>:</b></td>
                            <td><?php echo $event_name; ?></td>
                        </tr>
                        <tr>
                            <th>Synopsis</th>
                            <td><b>:</b></td>
                            <td><?php echo $event_synopsis; ?></td>
                        </tr>
                        <tr>
                            <th>Objective</th>
                            <td><b>:</b></td>
                            <td><?php echo $event_objective; ?></td>
                        </tr>
                        <tr>
                            <th>Impact</th>
                            <td><b>:</b></td>
                            <td><?php echo $event_impact; ?></td>
                        </tr>                        
                        <tr>
                            <th>Date</th>
                            <td><b>:</b></td>
                            <td>
                                <?php
                                $formattedStartDate = date("d/m/Y", strtotime($event_startDate));
                                $formattedEndDate = date("d/m/Y", strtotime($event_endDate));

                                if ($formattedStartDate == $formattedEndDate) {
                                    echo $formattedStartDate;
                                } else {
                                    echo $formattedStartDate . " - " . $formattedEndDate;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Time</th>
                            <td><b>:</b></td>
                            <td>
                                <?php
                                $formattedStartTime = date("h:i A", strtotime($event_startTime));
                                $formattedEndTime = date("h:i A", strtotime($event_endTime));

                                echo $formattedStartTime . " - " . $formattedEndTime;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Venue</th>
                            <td><b>:</b></td>
                            <td><?php echo $event_venue; ?></td>
                        </tr>                        
                        <tr>
                            <th>Submitted By</th>
                            <td><b>:</b></td>
                            <td><?php echo $pmfki_name; ?></td>
                        </tr>
                        <tr>
                            <th>Approved By</th>
                            <td><b>:</b></td>
                            <td><?php echo $admin_name; ?></td>
                        </tr>
                        <tr>
                            <th>Event Password</th>
                            <td><b>:</b></td>
                            <td><input type="text" name="event_pwd" value="<?php echo $event_pwd; ?>"></td>
                        </tr>
                        <tr>
                            <th>Event status</th>
                            <td><b>:</b></td>
                            <td>
                                <select name="event_status">
                                    <option value="A" <?php if ($event_status == 'A') echo 'selected'; ?>>ACTIVE</option>
                                    <option value="C" <?php if ($event_status == 'C') echo 'selected'; ?>>CLOSED</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Poster Description</th>
                            <td><b>:</b></td>
                            <td><textarea name="event_posterDesc" cols="50"><?php echo $event_posterDesc; ?></textarea></td>
                        </tr>
                        <tr>
                            <th>Poster</th>
                            <td><b>:</b></td>
                            <td>
                                <input type="file" name="poster-img" id="poster-img" accept=".jpg, .jpeg, .png" >
                            </td>
                        </tr>                        
                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <input type="submit" id="update-confirm-button" value="Update">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
			</div>
            </div>
</div>
</main>

<footer>
    <br><small><i> </i></small>
</footer>
</body>

</html>
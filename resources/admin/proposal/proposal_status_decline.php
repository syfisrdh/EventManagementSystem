<?php
    include('../../config.php');
    include('../../utils.php');

    session_start();
    validateSession('admin_id', '../../index.php');

    customHeader('Admin Proposal', '../../../public/css/style.css', '../../../public/icon/icon.png');
?>

    <body>
        <?php
            adminNavigation();
        ?>

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
                    $event_startDate = $row["event_startDate"];
                    $event_endDate = $row["event_endDate"];
                    $event_startTime = $row["event_startTime"];
                    $event_endTime = $row["event_endTime"];
                    $event_venue = $row["event_venue"];
                    $event_status = $row["event_status"];
                    $event_adminRemark = $row["event_adminRemark"];
                    $admin_name = $row["name"];
                    $pmfki_name = $row["pmfki_name"];
                }
            }
        ?>

        <main>
            <div class="event-row">
                <div class="proposal-details">
                    <h1>Proposal Details</h1>
                    <form action="proposal_status_decline.php" method="post" enctype="multipart/form-data" class="event-form">
                        <input type="hidden" id="event_id" name="event_id" value="<?=$_GET['id']?>">
                        <table width="100%" class="event-table" >
                            <tr>
                                <th>Event Name</th>
                                <td class="fill">:</td>
                                <td><?php echo $event_name; ?></td>
                            </tr>
                            <tr>
                                <th>Synopsis</th>
                                <td class="fill">:</td>
                                <td><?php echo $event_synopsis; ?></td>
                            </tr>
                            <tr>
                                <th>Objective</th>
                                <td class="fill">:</td>
                                <td><?php echo $event_objective; ?></td>
                            </tr>
                            <tr>
                                <th>Impact</th>
                                <td class="fill">:</td>
                                <td><?php echo $event_impact; ?></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td class="fill">:</td>
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
                                <td class="fill">:</td>
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
                                <td class="fill">:</td>
                                <td><?php echo $event_venue; ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td class="fill">:</td>
                                <td>
                                    <?php
                                        getEventStatusLabel($event_status);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Remark</th>
                                <td class="fill">:</td>
                                <td><textarea name="event_adminRemark" rows="4"><?php echo $event_adminRemark; ?></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <button class="decline-btn" type="submit" name="decline" value="Decline">Decline</button>
                                    <button class="normal-btn" type="button" onclick="location.href='proposal_admin_manage.php?id=<?php echo $event_id; ?>'">Back</button> 
                                </td>
                            </tr>
                        </table>
            		</form>
                </div>
            </div>
        </main>
    </body>
</html>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decline'])) {
        $event_id = $_POST['event_id'];
        $event_adminRemark = $_POST['event_adminRemark'];

        $sql = "UPDATE event SET event_status = 'D', event_adminRemark = '$event_adminRemark', admin_id = '{$_SESSION["admin_id"]}' WHERE event_id='$event_id'";
        $status = executeQuery($conn, $sql);

        if ($status) {
            // Display a popup message
            echo "<script>alert('Proposal Declined Successfully!');</script>";
            // Redirect to the profile page after successful update
            echo "<script>window.location.href='proposal_admin_manage.php?id=$event_id';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to Decline Proposal!');</script>";
            // Redirect to the profile page after unsuccessful update
            echo "<script>window.location.href='proposal_admin_manage.php?id=$event_id';</script>";
        }
    }
?>
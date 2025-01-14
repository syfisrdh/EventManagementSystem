<?php
    include('../../config.php');
    include('../../utils.php');

    session_start();
    validateSession('student_id', '../../index.php');

    $studentId = $_SESSION['student_id'];

    $currentDate = date("Y-m-d H:i:s"); 

    $sql = "SELECT e.* 
            FROM event e
            LEFT JOIN attendee a ON e.event_id = a.event_id AND a.student_id = ?
            WHERE e.event_status='A' 
            AND e.event_startDate > ?
            AND a.attendee_id IS NULL
            ORDER BY e.event_startDate ASC";    

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $studentId, $currentDate);


    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die('Error in SQL query: ' . mysqli_error($conn));
    }

    customHeader('Event Board', '../../../public/css/style.css', '../../../public/icon/icon.png');
?>

    <body>
        <?php
            studentNavigation();
        ?>

        <div id="join_confirmation_popup" class="popup-container">
            <div class="popup-content">
                <p>Are you sure you want to join this event?</p>
                <input type="hidden" id="event_id_to_join" value="">
                <button class="normal-btn" onclick="cancelJoin()">Cancel</button>
                <button class="normal-btn" onclick="confirmJoinAction()">Confirm</button>
            </div>
        </div>

        <div class="table-list">        
            <h1>Available Events</h1>
            <div class="eventboard-row">

            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // format date
                    $startDateFormat = date("d/m/Y", strtotime($row["event_startDate"]));
                    $endDateFormat = date("d/m/Y", strtotime($row["event_endDate"]));

                    // format time
                    $startTime12Hour = date("h:i A", strtotime($row["event_startTime"]));
                    $endTime12Hour = date("h:i A", strtotime($row["event_endTime"]));

                    echo '<div class="card">';
                    echo '<div class="image">';
                    echo '<img src="../../../public/storage/poster/' . $row["event_poster"] . '" alt="Event Poster" class="image-content">';
                    echo '</div>';
                    echo '<div class="details">';
                    echo '<div class="rowtitle">' . $row["event_name"] . '</div>';
                    echo '<div class="row scrollable">' . $row["event_posterDesc"] . '</div>';
                    echo '<div class="row">';
                    echo '<div class="column"><strong>Date</strong></div>';
                    echo '<div class="column">:</div>';
                    echo '<div class="column">' . $startDateFormat . ' - ' . $endDateFormat . '</div>';
                    echo '</div>';
                    
                    echo '<div class="row">';
                    echo '<div class="column"><strong>Time</strong></div>';
                    echo '<div class="column">:</div>';
                    echo '<div class="column">' . $startTime12Hour . ' - ' . $endTime12Hour . '</div>';
                    echo '</div>';
                    
                    echo '<div class="row">';
                    echo '<div class="column"><strong>Venue</strong></div>';
                    echo '<div class="column">:</div>';
                    echo '<div class="column">' . $row["event_venue"] . '</div>';
                    echo '</div>';
                    echo '<div class="row">';
                    echo '<form method="post" action="eventboard.php">';
                    echo '<input type="hidden" name="event_id_to_join" value="' . $row['event_id'] . '">';
                    echo '<button class="accept-btn" type="button" onclick="confirmJoin(' . $row['event_id'] . ')">JOIN NOW!</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>0 results</p>';
            }

            mysqli_close($conn);
            ?>
            </div>
        </div>
    </body>
</html>
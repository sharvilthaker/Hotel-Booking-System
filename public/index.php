        <!DOCTYPE html>
        <html>
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
          <link rel="stylesheet" href="styles.css">
          <body>
        <?php include "templates/header.php";  ?>
    <div class="container mt-5">
        <h2>Available Rooms</h2>
        <table class="table table-striped">
            <tr><th>Room ID</th><th>Room Type</th><th>Price</th><th>Availability</th><th>Action</th></tr>
            <?php
                $rooms = array_map('str_getcsv', file('data/rooms.csv'));
                foreach ($rooms as $room) {
                    $availability = ($room[3] == 'true') ? 'Available' : 'Booked';
                    echo "<tr>
                            <td>{$room[0]}</td>
                            <td>{$room[1]}</td>
                            <td>\${$room[2]}</td>
                            <td>{$availability}</td>
                            <td><a href='book-room.php?room_id={$room[0]}' class='btn btn-primary'>Book Now</a></td>
                          </tr>";
                }
            ?>
            </table>
<!-- This website is styled using Bootstrap, a front-end framework designed for building responsive and modern web pages. -->
        <div class="search-form mt-5">
                <h1 class="title">Welcome to the Hotel Booking System</h1>
                <p>Find the best rooms at the best prices.</p>
                <form method="GET">
                <label for="roomType">Enter Room Type:</label>
                <input type="text" name="roomType" id="roomType"  placeholder="Single, Double, Suite" required>
                <button type="submit">Search</button>
            </form>
            </div>
          <div class="container mt-4">
            <?php
            if (isset($_GET['roomType'])){
                $roomType = trim($_GET['roomType']);

                $json_data = file_get_contents("../data/rooms.json");
                $rooms = json_decode($json_data, true);

                $filteredRooms = include("../includes/filehandler.php");

                if(!empty($filteredRooms)){
                  echo '<table class="table table-striped mt-3">';
                  echo '<thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Price ($)</th><th>Details</th></tr></thead>';
                  echo '<tbody>';
                  foreach ($filteredRooms as $room){
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($room['id']). "</td>";
                      echo "<td>" . htmlspecialchars($room['name']). "</td>";
                      echo "<td>" . htmlspecialchars($room['type']). "</td>";
                      echo "<td>" . htmlspecialchars($room['price']). "</td>";
                      echo "<td><a href='?roomType=" .htmlspecialchars($_GET['roomType']) . "&viewId=" . $room['id'] . "' class='btn btn-info'>View Details</a></td>";
                      echo "</tr>";
                    }
                    echo '</tbody></table>';
                }else{
                  echo '<p>No rooms found for the selected type.</p>';
                }
            }
            ?>
            
            <?php 
              if(isset($_GET['viewId'])){
                  $roomId = intval($_GET['viewId']);
                  $selectedRoom = null;

                  foreach ($filteredRooms as $room){
                    if($room['id'] == $roomId){
                      $selectedRoom = $room;
                      break;
                    }
                  }

                  if($selectedRoom){
                    echo "<div class='mt-4 p-3 border rounded'>";
                    echo "<h3>Room Details</h3>";
                    echo "<p><strong>ID:</strong>".htmlspecialchars($selectedRoom['id'])."</p>";
                    echo "<p><strong>Name:</strong>".htmlspecialchars($selectedRoom['name'])."</p>";
                    echo "<p><strong>Type:</strong>".htmlspecialchars($selectedRoom['type'])."</p>";
                    echo "<p><strong>Price:</strong> $".htmlspecialchars($selectedRoom['price'])."</p>";
                    echo "</div>";
                    
                    echo "<form method='POST' class='p-4 border rounded shadow bg-light'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='name'>Enter Your Name:</label>";
                    echo "<input type='text' name='name' id='userName'><br>";
                    echo "</div>";
                  
                    echo "<div class='mb-3'>";
                    echo "<label for='roomType'>Enter Room Type:</label>";
                    echo "<input type='text' name='roomType' id='roomType'><br>";
                    echo "</div>";

                    echo "<div class='mb-3'>";
                    echo "<label for='checkIn'>Enter Check-in Date:</label>";
                    echo "<input type='date' name='checkIn' id='checkIn'><br>";
                    echo "</div>";

                    echo "<div class='mb-3'>";
                    echo "<label for='checkOut'>Enter Check-out Date:</label>";
                    echo "<input type='date' name='checkOut' id='checkOut'><br>";
                    echo "</div>";
                
                    echo "<button type='submit' class='btn btn-primary'>Submit</button>";
                    echo "</form>";
                    
                  }
              }        
              ?>
            </div>
          <?php 
            $confirmed = false;
            $bookingErrors = [];
            include("../includes/validator.php");


            if($_SERVER["REQUEST_METHOD"] == "POST"){
              $name = trim($_POST['name']);
              $room = trim($_POST['roomType']);
              $checkIn = $_POST['checkIn'];
              $checkOut = $_POST['checkOut'];

              $bookingErrors = validBooking($name,$room,$checkIn,$checkOut);

              if(empty($name)){
                $errors[] = "Please provide your name.";
              }
              if(empty($roomType)){
                $errors[] = "Please provide your roomType.";
              }
              if(empty($checkIn)){
                $errors[] = "Please provide your CheckIn-date.";
              }
              if(empty($checkOut)){
                $errors[] = "Please provide your CheckOut-date.";
              }
              
              if(empty($bookingErrors)){
                saveBooking($name,$room,$checkIn,$checkOut);
                $confirmed = true;
                $confirmBooking = [ 'name' => $name,'room'=>$room, 'checkIn'=>$checkIn,'checkOut' => $checkOut];
              }
              
            if ($confirmed){
              echo "<div class='container mt-4 p-4 border rounded shadow bg-light'>";
              echo "<h3>Booking Confirmed</h3>";
              echo "<p><strong>Name:</strong>". htmlspecialchars($confirmBooking['name'])."</p>";
              echo "<p><strong>Room Type:</strong>". htmlspecialchars($confirmBooking['room'])."</p>";
              echo "<p><strong>Check-In:</strong>". htmlspecialchars($confirmBooking['checkIn'])."</p>";
              echo "<p><strong>Check-Out:</strong>". htmlspecialchars($confirmBooking['checkOut'])."</p>";
              echo "</div>";
            }else{
              echo "<div class='container mt-4 p-4 border rounded shadow bg-light text-danger'>";
              echo "<h4>Booking Errors:</h4>";
              echo "<ul>";
              foreach ($bookingErrors as $error){
                echo "<li>".htmlspecialchars($error)."</li>";
              }
              echo "</ul>";
              echo "</div>";
            }
          }
    ?>
            <?php include("../templates/footer.php"); ?>
        </body>
        </html>

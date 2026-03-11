        <?php
        // Read the JSON file
        if(isset($_GET['roomType'])){
            $roomType = trim($_GET['roomType']);
    
            $json_data = file_get_contents("../data/rooms.json");
            $rooms = json_decode($json_data, true);
    
            $filteredRooms = array_filter($rooms, function($room) use ($roomType){
            return strcasecmp($room['type'], $roomType) == 0;
            });
        }else {
            $filteredRooms = [];
        }
    
        return $filteredRooms;

        function saveBooking($userName, $roomType, $checkIn, $checkOut) {
            $file = "../data/bookings.csv";
            $bookingData = [$userName, $roomType, $checkIn, $checkOut];
        
            $fp = fopen($file, "a"); 
            fputcsv($fp, $bookingData); 
            fclose($fp);
        }
        ?>
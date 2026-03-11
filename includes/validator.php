        <?php 
        function validBooking($userName,$roomType ,$checkIn, $checkOut) {
            $errors = [];

            if (empty($userName)) {
                $errors[] = "Name is required.";
            }
            if (empty($roomType)) {
                $errors[] = "Room type is required.";
            }
            if (empty($checkIn)) {
                $errors[] = "Check-in date is required.";
            }
            if (empty($checkOut)) {
                $errors[] = "Check-out date is required.";
            }

            $validRoom =['Single','Double','Suite'];

            if(!in_array($roomType,$validRoom)){
                $errors[] ="Invalid room type. Please select a valid room type.";
            }
// strtotime() is taken from the PHP documentation to convert date strings to Unix timestamps for comparison
            if(strtotime($checkIn) >= strtotime($checkOut)){
                $errors[] ="Check-in date must be before the check-out date.";
            }

            return $errors;
        }
        ?>
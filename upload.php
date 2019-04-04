<?php


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

//print_r($_FILES);

//    print_r($_FILES['photo']['tmp_name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . generateRandomString().'.jpg');
    
    $result = array(
        'location' => 'test je',
    );
    
    
    echo json_encode($result);
            
?>


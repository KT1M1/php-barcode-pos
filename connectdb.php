<?php

try{
    $pdo = new PDO('mysql:host=timi_chdev_hu-db;dbname=timi', 'timi', 'mHsBYnvG');
}catch (PDOException $e){
    echo $e->getMessage();
}
?>
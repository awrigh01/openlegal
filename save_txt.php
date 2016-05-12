<?php

$updated_text = $_POST['data'];
$file = "test.txt";

echo $updated_text;

file_put_contents($file, $updated_text);

?>
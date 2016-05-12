<?php
$handle = @fopen("test.txt", "r"); //read line one by one


while (!feof($handle)) // Loop til end of file.
{
    $buffer = fgets($handle, 4096); // Read a line.
    
	$buffer = preg_replace('/    +/',' ',$buffer);

	echo $buffer;

 }

?>
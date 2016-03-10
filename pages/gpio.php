<?php
exec("gpio read 1", $status);
print_r($status); //or var_dump($status);
?>
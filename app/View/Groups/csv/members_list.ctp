<?php
foreach ($data as $row) :
    echo implode(',', $row['Email']) . "\n";
endforeach;

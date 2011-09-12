<?php

// load db connection
$db = new PDO("pgsql:dbname=zwemwater;host=localhost", "postgres", "" );
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
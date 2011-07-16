<?php
require 'libraries/start.php';

// install
if( !is_file('install.log') ) Http::redirect('install.php'); 
else Http::redirect('xhtml.php'); 
<?php
file_put_contents(Configuration::get('base_dir').DS.'install.log', date('r'));
Http::redirect('index.php');

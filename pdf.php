<?php

require 'libraries/start.php';

function errorHtml($e) {
    require 'classes/InvoiceHtml.php';
    $html = new InvoiceHtml();
    $html->setData($e);
    $html->error();
    exit();
}

// error check
try {
    $object = Routing::getToken('object');
    $id = Routing::getToken('id');
    $action = Routing::getToken('action');
    pr(Routing::parse());
    if ($object != 'invoices' && $object != 'payments')
        throw new Exception('Invalid URL', 400);
    if (!is_numeric($id))
        throw new Exception('Invalid ID supplied in URL', 400);
    if ($action != 'view')
        throw new Exception('Invalid action supplied in URL', 400);
} catch (Exception $e) {
    errorHtml($e);
}

// find wkhtml; change value below for Windows/Linux path differences
$os = 'windows';
$PATH = Configuration::get('base_dir') . DS . 'libraries' . DS;
if ($os == 'windows') {
    $PATH .= 'wkhtmltopdf-windows' . DS . 'wkhtmltopdf.exe';
} elseif ($os == 'linux') {
    $PATH .= 'wkhtmltopdf-linux' . DS . 'wkhtmltopdf-i386';
    chmod($PATH, 0755);
}
try {
    if (!is_file($PATH))
        throw new Exception('Invalid action supplied in URL', 400);
    if (!is_executable($PATH))
        throw new Exception("'wkhtmltopdf' must be executable", 400);
    // TODO: check permissions
} catch (Exception $e) {
    errorHtml($e);
}

// get vars
$URL = Configuration::get('base_url') . "/xhtml.php/$object/$id/view";
$CACHE = Configuration::get('base_dir') . DS . 'cache' . DS . md5($URL) . '.pdf';

// make PDF if necessary
if (!is_file($CACHE)) {
    $output = shell_exec("$PATH $URL $CACHE");
    pr($output);
}

// check
try {
    if (!is_file($CACHE))
        throw new Exception("'wkhtmltopdf' failed to create the PDF", 400);
    // TODO: check permissions
} catch (Exception $e) {
    errorHtml($e);
}

// show PDF
$fp = fopen($CACHE, 'rb');
header('Content-Type: application/pdf');
while (!feof($fp)) {
    $buff = fread($fp, 4096);
    print $buff;
}
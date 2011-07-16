<?php
define('BASECAMP_API_KEY', 'f509153e6366c89288e947c67122d63a62f0f05d');

function getPeople(){
    // get Basecamp data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://arraystudio.basecamphq.com/people.xml");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
    curl_setopt($ch, CURLOPT_USERPWD, BASECAMP_API_KEY.':X');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/xml',
        'Content-Type: application/xml'
    ));
    $data = curl_exec($ch);
    curl_close($ch);

    // parse xml
    $xml = simplexml_load_string($data);
    return $xml;
}
function getCompany($id){
    // get Basecamp data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://arraystudio.basecamphq.com/companies/$id.xml");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
    curl_setopt($ch, CURLOPT_USERPWD, BASECAMP_API_KEY.':X');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/xml',
        'Content-Type: application/xml'
    ));
    $data = curl_exec($ch);
    curl_close($ch);

    // parse xml
    $xml = simplexml_load_string($data);
    return $xml;
}
function getJson($people){
    $list = array();
    foreach($people as $person){
        $company = getCompany($person->{'company-id'});
        $person->{'company-name'} = $company->name;
        $id = (string) $person->id;
        $list[$id] = "'$id': ".json_encode($person);
    }
    return implode(",\n", $list);
}

// display select box
$people = getPeople();
$json = getJson($people);
?>
<!-- BASECAMP CONTACT PLUGIN -->
<div id="basecamp-contact-plugin">
    <p>Import Basecamp Contact: 
        <select id="basecamp-contacts">
            <option value="">-- SELECT --</option>
            <?php 
            foreach($people as $person){
                $name = $person->{'first-name'}.' '.$person->{'last-name'};
                echo "<option value='$person->id'>$name</option>";
            }
            ?>
        </select>
    </p>
    <script src="<?php echo Configuration::get('base_url') . '/libraries/jquery.js'; ?>"></script>
    <script type="text/javascript">
        $('#basecamp-contacts').change(function(){
            var id = $(this).val();
            if( !id ) return;
            $('input[name="Invoice[client_first_name]"]').val(BASECAMP_CONTACTS[id]['first-name']);
            $('input[name="Invoice[client_last_name]"]').val(BASECAMP_CONTACTS[id]['last-name']);
            $('input[name="Invoice[client_email]"]').val(BASECAMP_CONTACTS[id]['email-address']);
            $('input[name="Invoice[company]"]').val(BASECAMP_CONTACTS[id]['company-name']);
        });
        var BASECAMP_CONTACTS = {
            <?php echo $json; ?>
                    
        };
    </script>
</div>
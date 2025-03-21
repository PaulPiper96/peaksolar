<?php
function concatenateStrings(...$vars) {
    return implode(' ', array_map('strval', $vars));
}
 
 
//write client
function createContact($url, $apiKey, $contactData) {
    // GraphQL Mutation
    $mutation = '
mutation create_contact {
    create_contact(
        contact: {
   
            title: "' . $contactData['title'] . '"
            last_name: "' . $contactData['last_name'] . '"
           
            first_name: "' . $contactData['first_name'] . '"
            email: "' . $contactData['email'] . '"
            source: "' . $contactData['source'] . '"
            phone_mobile: "' . $contactData['phone_mobile'] . '"
            address: {
                street: "' . $contactData['address']['street'] . '"
                city: "' . $contactData['address']['city'] . '"
                zipcode: "' . $contactData['address']['zipcode'] . '"
            }
        }
    ) {
        id
    }
}';
 
 
   
   
 
    // Prepare the POST data
    $postData = json_encode(array(
        'query' => $mutation,
        'variables'
        => null // No variables are required for this mutation
    ));
 
 
    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $apiKey,
    ));
 
   
 
    $headers = getallheaders();
 
    // Execute cURL request
    $result = curl_exec($ch);
    curl_close($ch);
 
    // Decode and handle the response
    $response = json_decode($result, true);
    if (!empty($response['data'])) {
       
        return $response;
    } else {
        echo "An error occurred:\n";
 
        return null;
    }
}
 
 
function createProjectMatch($url, $apiKey, $projectData) {
    // GraphQL Mutation
    $mutation = '
    mutation create_project {
        create_project_match(
            project_match: {
                partner_notes: "' . $projectData['partner_notes'] . '"
                project: {
                    customer_id: ' . $projectData['project']['customer_id'] . '
                    measure_id: ' . $projectData['project']['measure_id'] . '
                    address: {
                        street: "' . $projectData['project']['address']['street'] . '"
                        city: "' . $projectData['project']['address']['city'] . '"
                        zipcode: "' . $projectData['project']['address']['zipcode'] . '"
                    }
                }
            }
        ) {
            id
        }
    }';
 
    // Prepare the POST data
    $postData = json_encode([
        'query' => $mutation,
        'variables' => null
    ]);
 
    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
 
    // Execute cURL request
    $result = curl_exec($ch);
 
   
    curl_close($ch);
 
    // Decode the response
    $response = json_decode($result, true);
 
    if (!empty($response['data']['create_project_match']['id'])) {
       
        return $response['data']['create_project_match']['id'];
    } elseif (!empty($response['errors'])) {
        echo "Errors: \n";
       
        return null;
    } else {
        echo "An unexpected error occurred.\n";
       
        return null;
    }
}
 
 
 
function decrypt_key($encrypted_string, $password) {
 
 // 1) Base64-dekodieren
    $encrypted_string = base64_decode($encrypted_string);
    if ($encrypted_string === false) {
        throw new Exception('Die Base64-Dekodierung ist fehlgeschlagen.');
    }
 
    // 2) "Salted__" und Salt extrahieren
    $salt_marker = 'Salted__';
    $salt_size   = 8;
    if (substr($encrypted_string, 0, strlen($salt_marker)) !== $salt_marker) {
        throw new Exception('Kein gültiges Salt-Marker gefunden.');
    }
 
    $salt       = substr($encrypted_string, strlen($salt_marker), $salt_size);
    $ciphertext = substr($encrypted_string, strlen($salt_marker) + $salt_size);
 
    // 3) Schlüssel und IV via PBKDF2 generieren
    $key_iv = openssl_pbkdf2($password, $salt, 48, 10000, 'sha256');
    $key    = substr($key_iv, 0, 32);  // 256-Bit-Schlüssel
    $iv     = substr($key_iv, 32, 16); // Initialisierungsvektor
 
    // 4) Daten entschlüsseln
    $decrypted_data = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    if ($decrypted_data === false) {
        throw new Exception('Entschlüsselung fehlgeschlagen.');
    }
 
    return $decrypted_data;
}
 
function my_custom_shortcode() {
   // Start output buffering.
    ob_start();
 
    // 1) Check if the form has been submitted and if the action matches.
    if ( isset($_POST['action']) && $_POST['action'] === 'process_form' && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
       
        // 1a) Verify nonce to protect against CSRF.
        if ( ! isset($_POST['my_form_nonce']) || ! wp_verify_nonce($_POST['my_form_nonce'], 'my_form_action') ) {
            wp_die( 'Sicherheitsüberprüfung fehlgeschlagen, bitte Seite neu laden. Sollte das nicht die erste Fehlermeldung sein, bitte an Service@Flh-mediadigital.de wenden. Vielen Dank' );
        }
 
        // 1b) Now safely process your form data.
        $vorname          = isset($_POST['vorname'])          ? sanitize_text_field($_POST['vorname'])          : null;
        $anrede         = isset($_POST['anrede'])          ? sanitize_text_field($_POST['anrede'])          : null;
       
         // 1b) Now safely process your form data.
        $mail          = isset($_POST['mail'])          ? sanitize_text_field($_POST['mail'])          : null;
       
        $nachname         = isset($_POST['nachname'])         ? sanitize_text_field($_POST['nachname'])         : null;
        $household        = isset($_POST['household'])        ? sanitize_text_field($_POST['household'])        : null;
        $house_type       = isset($_POST['house_type'])       ? sanitize_text_field($_POST['house_type'])       : null;
        $current_heating  = isset($_POST['current_heating'])  ? sanitize_text_field($_POST['current_heating'])  : null;
        $time_frame       = isset($_POST['time_frame'])       ? sanitize_text_field($_POST['time_frame'])       : null;
        $reason           = isset($_POST['reason'])           ? sanitize_text_field($_POST['reason'])           : null;
        $electric_vehicle = isset($_POST['electric_vehicle']) ? sanitize_text_field($_POST['electric_vehicle']) : null;
        $zipcode          = isset($_POST['zipcode'])          ? sanitize_text_field($_POST['zipcode'])          : null;
        $phone          = isset($_POST['phone'])          ? sanitize_text_field($_POST['phone'])          : null;
        $street = isset($_POST['street'])          ? sanitize_text_field($_POST['street'])          : null;
        $ort= isset($_POST['ort'])          ? sanitize_text_field($_POST['ort'])          : null;
 
 
       
       
       
    if (defined('ENCRYPTION_KEY')) {
        $encrypted_file= ENCRYPTION_KEY;
         $password ='pw';
    $apikey = decrypt_key($encrypted_file, $password);
       
         //API Key auslesen
         $apiKey=str_replace(['-n ', '"'], '', $apikey);
    $apiKey=str_replace(['', '"'], '', $apikey);
    $apiKey=str_replace([' ', ''], '', $apiKey);
    $apiKey = trim($apiKey);
       
        //Kontaktendaten aus dem Formular lesen
        $contactData = array(
             "title" =>$anrede ,
    "last_name" => $nachname,
    "first_name" => $vorname,
    "email" => $mail,
    "source" => "Website",
    "phone_mobile" =>$phone  ,
    "address" => array(
        "street" => $street,
        "city" => $ort,
        "zipcode" => $zipcode,
    )
);      
        $graphqlEndpoint = "https://login.hero-software.de/api/external/v7/graphql";
        $usercreate = createContact($graphqlEndpoint, $apiKey, $contactData);
       
        if ($usercreate) {
            $userid = $usercreate['data']['create_contact']['id'];
// Data for the project
//
//
 
   
$Anfragedetails=concatenateStrings(  "Personenanzahl:",          $household,", Haustyp:",          $house_type,    ", Heizung: ",         $current_heating, ", zeit. Rahmen: ",   $time_frame,
    ", Grund: ",           $reason,
    ", E-Fahrzeug: ",      $electric_vehicle);
   
   
$projectData = [
    'partner_notes' => $Anfragedetails,
    'project' => [
        'customer_id' => $userid,
        'measure_id' => 7002,
        'address' => [
         "street" => $street,
        "city" => $ort,
        "zipcode" => $zipcode,
        ]
    ]
];
 
$projectId = createProjectMatch($graphqlEndpoint, $apiKey, $projectData);
if ($projectId)
{
   if ($projectId  )
{
   echo '<script type="text/javascript">alert("Vielen Dank für Ihre Anfrage! Wir melden uns in Kürze.")</script>';
}
       
}
 
           
}
       
       
       
    } else {
       wp_die( 'Sicherheitsüberprüfung fehlgeschlagen, bitte Seite neu laden. Sollte das nicht die erste Fehlermeldung sein, bitte an Service@Flh-mediadigital.de wenden. Vielen Dank' );
    }
         
    }
 
    // 2) Output the form.
    ?>
    <form class="form-container" action="" method="post">
        <?php
            // The first parameter 'my_form_action' is your "action name"
            // The second parameter 'my_form_nonce' is the "nonce field name"
            wp_nonce_field( 'my_form_action', 'my_form_nonce' );
        ?>
        <input type="hidden" name="action" value="process_form">
       
 
        <!-- Anzahl Personen im Haushalt -->
        <div class="form-group">
            <label for="household">Wieviele Personen wohnen im Haushalt</label>
            <select name="household" id="household" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="mehr">Mehr</option>
            </select>
        </div>
 
        <!-- Welche Art Haus -->
        <div class="form-group">
            <label for="house_type">Welche Gebäudeart ist Ihr Haus?</label>
            <select name="house_type" id="house_type" required>
                <option value="Freistehendes Einfamilienhaus">Freistehendes Einfamilienhaus</option>
                <option value="Doppelhaushälfte">Doppelhaushälfte</option>
                <option value="Reihenhaus">Reihenhaus</option>
                <option value="Mehrfamilienhaus">Mehrfamilienhaus</option>
                <option value="Firma/Lager">Firma/Lager</option>
            </select>
        </div>
 
        <!-- Aktuelle Heizung -->
        <div class="form-group">
            <label for="current_heating">Aktuelle Heizung:</label>
            <select name="current_heating" id="current_heating" required>
                <option value="Gas">Gasheizung</option>
                <option value="Ölheizun">Ölheizung</option>
                <option value="Fernwärme">Fernwärme</option>
                <option value="Wärmepumpe">Wärmepumpe</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>
       
       
 
        <!-- Zeitlicher Rahmen -->
        <div class="form-group">
            <label for="time_frame">Wann soll das Projekt umgesetzt werden</label>
            <select name="time_frame" id="time_frame" required>
                <option value="Sofort">Sofort</option>
                <option value="3-6">3-6 Monate</option>
                <option value="6-12">6-12 Monate</option>
                <option value="12+">12+ Monate</option>
            </select>
        </div>
 
        <!-- Grund für die Anschaffung -->
        <div class="form-group">
            <label for="reason">Was ist das Ziel der Investition??</label>
            <select name="reason" id="reason" required>
                <option value="Autarkie">Autarkie</option>
                <option value="Stromkosten senken">Stromkosten senken</option>
                <option value="Schutz vor steigenden Stromkosten">Schutz vor steigenden Stromkosten</option>
                <option value="Stromkosten reduzieren für Sauna/Pool">Stromkosten reduzieren für Sauna/Pool</option>
                <option value="Klimaschutz">Klimaschutz</option>
                <option value="Rendite">Rendite</option>
            </select>
        </div>
 
        <!-- Ist ein E-Fahrzeug in Planung -->
        <div class="form-group">
            <label for="electric_vehicle">Ist ein E-Auto geplant</label>
            <select name="electric_vehicle" id="electric_vehicle" required>
                <option value="Ja">Ja</option>
                <option value="existiert">Bereits vorhanden</option>
                <option value="Nein">Nein</option>
            </select>
        </div>
       
                <!--Anrede -->
        <div class="form-group">
             <label for="anrede">Anrede</label>
            <select name="anrede" id="anrede" >
                 <option value=""></option>
                <option value="Herr">Herr</option>
                <option value="Frau">Frau</option>
                <option value="Famillie">Famillie</option>
                <option value="Eheleute">Eheleute</option>
                <option value="Dr.">Dr.</option>
                <option value="Prof.">Prof. </option>
                <option value="Prof. Dr.">Prof. Dr.</option>
             </select>  
        </div>
       
                <!-- Vorname -->
        <div class="form-group">
            <label for="vorname">Vorname</label>
            <input type="text" name="vorname" id="vorname" placeholder="Vorname" maxlength="50">
        </div>
 
        <!-- Name -->
        <div class="form-group">
            <label for="nachname">Nachname</label>
            <input type="text" name="nachname" id="nachname" placeholder="Nachname"  required>
        </div>
       
       
     <div class="form-group">
    <label for="mail">Mail</label>
    <input
        type="email"
        name="mail"
        id="mail"
        placeholder="Mail"
        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
        required
        title="Bitte geben Sie eine gültige E-Mail-Adresse ein. Beispiel: name@domain.de"
    >
</div>
       
       
            <!--Telefon -->
        <div class="form-group">
            <label for="phone">Telefon</label>
            <input type="text" name="phone" id="phone" placeholder="" maxlength="10" required>
        </div>
       
 
       
            <!--Street -->
        <div class="form-group">
            <label for="street">Straße und Hausnummer</label>
            <input type="text" name="street" id="street" placeholder="" maxlength="20" required>
        </div>
 
                <!--Ort -->
        <div class="form-group">
            <label for="ort">Ort</label>
            <input type="text" name="ort" id="ort" placeholder="" maxlength="40" required>
        </div>
 
        <!-- Postleitzahl -->
        <div class="form-group">
            <label for="zipcode">Postleitzahl:</label>
            <input type="text" name="zipcode" id="zipcode" placeholder="00000" maxlength="5" required>
        </div>
 
        <!-- Submit Button -->
        <button type="submit" class="submit-button">Angebot einholen</button>
    </form>
 
    <style>
        .form-container {
            background-color: white;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #f9a31a;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #e38900;
        }
    </style>
 
    <?php
    // 3) Return the buffered output.
    return ob_get_clean();
}
 
function display_form_results() {
   
}
add_shortcode('form_shortcode', 'my_custom_shortcode');
 
 

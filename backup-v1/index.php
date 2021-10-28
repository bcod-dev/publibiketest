<?php
ini_set("upload_max_filesize", "100M");
ini_set("post_max_size", "150M");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("libs/phpmailer/Exception.php");
include("libs/phpmailer/PHPMailer.php");
include("libs/phpmailer/SMTP.php");

$isHTTPS = $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" ||
    $_SERVER["HTTP_X_FORWARDED_PORT"] == "443" ||
    $_SERVER["PORT"] == "443" ||
    $_SERVER["SERVER_PORT"] == "443" ||
    $_SERVER["HTTPS"] == "on";

//Force HTTPS
if($isHTTPS == false && $_SERVER["HTTP_HOST"] != "localhost:9000")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

session_start();
$sendCountLimit = isset($_SESSION['send_count']) ? $_SESSION['send_count'] : 0;
$sessionTimeout = 300;

function sendCount(){
    if(!isset($_SESSION['send_count'])){
        $_SESSION['send_count'] = 1;
    } else {
        $_SESSION['send_count'] = $_SESSION['send_count'] + 1;
    }

    //If still can send, update the latest time
    if((int)$_SESSION['send_count'] < 5){
        $_SESSION['send_count_time'] = time();
    }
}


//sendCount();die(var_dump($_SESSION['send_count']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if((int)$_SESSION['send_count'] > 5){ //Not allow sendiing 
        //If not passed timeout
        //var_dump($_SESSION['send_count']);
        //die(var_dump(time() - $_SESSION['send_count_time']));
        $timeoutCount = time() - $_SESSION['send_count_time'];
        if( $timeoutCount < $sessionTimeout){
            //Prevent sending
            $result = array(
                "POST" => $_POST,
                "FILES" => $_FILES,
                "SESSION" => $_SESSION,
                "upload_ok" => 0,
                "image_url" => null,
                "email_ok" => null,
                "email_error" => null, 
                "success" => false,
                "limited" => true,
                "limited_in" => $sessionTimeout - $timeoutCount
            );
        
            header('Content-type: application/json');
            echo json_encode($result);
            exit();
        }
    
        //Otherwise, clear timeout and allow send
        $_SESSION['send_count'] = 0;
        $_SESSION['send_count_time'] = time();
        
    }



    //Get POST data
    $lat = isset($_POST["lat"]) ? $_POST["lat"] : "";
    $lng = isset($_POST["lng"]) ? $_POST["lng"] : "";
    $location = $lat != "" && $lng != "" && $lat != "null" && $lng != "null" ? $lat.",".$lng: "";
    $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
    $bikeId = isset($_POST["bike_id"]) ? $_POST["bike_id"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $phoneNumber = isset($_POST["phone_number"]) ? $_POST["phone_number"] : "";
    $googleMapLink = isset($_POST["google_map_link"]) ? $_POST["google_map_link"] : "";
    $googleMapLink = ($googleMapLink == "null" || $googleMapLink == null) ? "" : $googleMapLink;
    $imageData = isset($_POST['image_base64']) ? $_POST['image_base64'] : "";

    //die($imageData);

    //Process upload 
    $uploadDir = "uploads";
    $isUploadOk = 0;
    $uploadedFilePath = null;

    if(!file_exists($uploadDir)){
        mkdir("$uploadDir");
        chmod("$uploadDir", 0755);
    }

    // if(isset($_FILES["image"])) {
    // $check = getimagesize($_FILES["image"]["tmp_name"]);
    //     if($check !== false){
    //         $fileName = $_FILES["image"]["name"];
    //         $ext = pathinfo($fileName)["extension"];
    //         $name = pathinfo($fileName)["filename"];

    //         $uploadFileName = md5($lat.$lng.microtime().rand()).".".$ext;
    //         $uploadFilePath = $uploadDir."/".$uploadFileName;
    //         if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadFilePath)) {
    //             $isUploadOk = 1;
    //             $uploadedFilePath = $uploadFilePath;
    //         } 
    //     }
    // } else {
    //     $isUploadOk = 0;
    //}

    //Process if upload using base 64
    if($imageData != null && $imageData != "null" && $imageData != ""){
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
        
            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                throw new \Exception('invalid image type');
            }
            $imageData = str_replace( ' ', '+', $imageData );
            $imageDataFile = base64_decode($imageData);
            
            if ($imageDataFile === false) {
                $isUploadOk = -1;
            }
        } else {
            $isUploadOk = -1;
        }

        if($isUploadOk >= 0 ) { 
            $uploadFileName = md5($lat.$lng.microtime().rand());
            $uploadedFilePath = $uploadDir."/".$uploadFileName.".".$type;
            file_put_contents($uploadedFilePath, $imageDataFile);
            $isUploadOk = 1;
        }
    }

    //Sending email
    $imageUrl = null;
    $emailOK = false;
    $emailError = null;
    if($isUploadOk >= 0){
        $domain =  str_replace("\\",'/',"http://".$_SERVER['HTTP_HOST'].substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT'])));
        $imageUrl = $isUploadOk == 1?  $domain."/".$uploadedFilePath : "";

        $emailTitle = "LOSTBIKE! ".date("Y-m-d H:i:s");
        $emailBody = "
        - Koordinaten: $location \r\n
        - Link zu Google Maps: $googleMapLink \r\n
        - Link zum Bild: $imageUrl \r\n
        - Bemerkung: $comment \r\n
        - Betriebsnummer des Velos: $bikeId \r\n
        - E-Mail-Adresse: $email \r\n
        - Telefonnummer: $phoneNumber \r\n
        ";


        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();   
            $mail->Timeout = 5;
            $mail->SMTPAuth   = true;

            $mail->Host       = 'cressida.kreativmedia.ch';
            $mail->Username   = 'found@publibike-service.ch';
            $mail->Password   = 'N1n5s_0v';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;     

            //Recipients
            $mail->setFrom('found@publibike-service.ch', 'Publibke');

            $mail->addAddress('email2lostbiketicket@o-1pbg85gwazpe7aslaozn27obujy8xem83d72llqp14nos04kem.0y-a2afuai.eu25.apex.salesforce.com');
            //$mail->addAddress('jerry.pham@mndigitalswat.com');
            $mail->addAddress('sofian.zubi@publibike.ch');

            // Content
            $mail->isHTML(false);
            $mail->Subject = $emailTitle;
            $mail->Body    = $emailBody;
            //$mail->AltBody = $emailBody;

            $mail->send();
            //echo 'Message has been sent';
            $emailOK = true;
            sendCount();
        } catch (Exception $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $emailError = $email->ErrorInfo;
        }


    }
    

    $result = array(
        "POST" => $_POST,
        "FILES" => $_FILES,
        "upload_ok" => $isUploadOk,
        "image_url" => $imageUrl,
        "email_ok" => $emailOK,
        "email_error" => $emailError, 
        "success" => $emailOK && $isUploadOk >= 0,
        "limited" => false,
        "limited_in" => 0,
        "send_count" => $_SESSION['send_count']
    );

    header('Content-type: application/json');
    echo json_encode($result);
    exit();
}
?>
<html lang="en">
<!-- html Head -->

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>PubliBike - <?php echo $_SERVER['SERVER_NAME'] ?></title>
    <!-- Custom styles -->
    <link rel="stylesheet" href="public/assets/css/style.css" />
    <link rel="stylesheet" href="public/assets/css/style2.css" />
    <!-- Fontawesome styles -->
    <link rel="stylesheet" href="public/assets/vendor/fontawesome-free/css/all.min.css" />
</head>
<!-- End of html Head -->

<body>
    <main>
    <div style="height:300px; background-clor:red" id="map"></div>
        <div class="box">
            <div class="form-finding">
               
                
                
                

                <div class="form-label-group">
                    <button class="btn btn-secondary btn-block" onClick="getCurrentLocation();"> <i class="fas fa-map-marker-alt mr-2"></i> Mein Standort
                    </button>
                    <!--
                    <button class="btn btn-secondary btn-block" onClick="initMap();">
                        <i class="fas fa-spinner"></i>
                    </button>
-->
                </div>
                <!-- <div class='small text-center'>Falls die Standort-Übermittlung nicht funktioniert, überprüfen Sie bitte Ihre Standort-Einstellungen.</div> -->
                <div class="text-center mb-4">
                    <div class="form-finding-img-box">
                    <a class="mb-4" href="javascript:document.getElementById('image').click();">
                        <img id="image-display" class="mb-4 form-finding-img" src="./public/assets/img/IMG_2910.PNG" alt=""/>
                        <input style="display:none" id="image" type="file" name="image" accept="image/*" onChange="readURL(this);" capture="camera">
                        <i style="font-size:30px;" class="fas fa-camera new-photo"></i>
                        </a>
                    </div>
                    <div>
                    Foto aufnehmen - <i>optional</i>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="inputBikeID">Betriebsnummer des Velos</label>
                    <input id="inputBikeID" class="form-control" placeholder="oberhalb dem Schlossbildschirm" autofocus="false" />
                </div>

                <input type="checkbox" class="show-all" id="show-all">
                <label class="show-all-label border p-1" for="show-all">
                    <span class="show-more">Mehr Angaben<i class="fas fa-caret-down ml-2"></i></span>
                    <span class="show-less">Weniger Angaben<i class="fas fa-caret-up ml-2"></i></span>
                </label>

                <div class="all-items">
                <div class="form-label-group">
                    <label for="inputComment">Bemerkung - <i>optional</i></label>
                    <textarea id="inputComment" class="form-control" rows="2" placeHolder="Falls z.B. die Standort-Ermittlung nicht funktioniert, bitte hier die Adresse eingeben"></textarea>
                </div>

                

                <div class="form-label-group">
                    <label for="inputEmail">E-Mail-Adresse - <i>optional</i></label>
                    <input type="email" id="inputEmail" class="form-control" placeholder=""  />
                </div>

                <div class="form-label-group">
                    <label for="inputPhoneNumber">Telefonnummer - <i>optional</i></label>
                    <input type="number" id="inputPhoneNumber" class="form-control" />
                </div>
</div>
                <div class="box-action">
                    <button type="submit" class="btn btn-lg btn-primary btn-block" onClick="doSubmit();">
                    Senden
                    </button>
                    <!--
                    <br/>
                    <a href="javascript:doSubmit();" >Ajax submit</a>
-->
                </div>
            </div>

        </div>
    </main>
    <div class="loading" style="display:none">lädt...&#8230;</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> -->
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="./public/assets/js/script.js"></script>
    <script src="./public/assets/js/svg4everybody.min.js"></script>
    <script src="./public/assets/js/svg4everybody.legacy.min.js"></script>
    <script>
        svg4everybody();
    </script>


    <script>

var map, infoWindow;
        var pos = {
            lat: null,
            lng: null
        }
        var googleMapLink = null;

        //Check current permission
        if(navigator.permissions != undefined){
            navigator.permissions.query({name:'geolocation'})
                .then(function(permissionStatus) {
                    console.log(permissionStatus); //permissionStatus.state = prompt/granted/denied
                    switch(permissionStatus.state) {
                        case 'granted':
                            onGranted();
                            break;
                        case 'prompt':
                            onPrompt();
                            break;
                            onNoPermission();
                        default: 
                    }
                permissionStatus.onchange = function() {
                    console.log('geolocation permission state has changed to ', this.state);

                    switch(permissionStatus.state) {
                        case 'granted':
                            onChangedToGranted();
                            break;
                        case 'prompt':
                            onPrompt();
                            break;
                            onNoPermission();
                        default: 
                    }
                };
            });
        } else {
            console.log('navigator.permission is undefined');
            //showError('Getting location is not supported, please enter the address in comment!');
        }


    function onGranted(){ //For first time check
        console.log("onGranted");
        getCurrentLocation();
    }

    function onChangedToGranted(){
        console.log("onChangedToGranted");
    }

    function onPrompt(){
        console.log('onPrompt');
    }

    function onNoPermission(){
        console.log('onNoPermission');
        showError('Es fehlt die Berechtigung für die Standort-Ermittlung.Bitte die Adresse im Feld "Bemerkung" eingeben.');
    }
        

        function initMap(){
            map = new google.maps.Map(document.getElementById('map'), {
            //center: {lat: -34.397, lng: 150.644},
            zoom: 2,
            panControl: true,
                zoomControl: false,
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: false,
                overviewMapControl: false,
                rotateControl: false
            });
            infoWindow = new google.maps.InfoWindow;
            var berlin = {
                lat: 52.520008,
                lng: 13.404954
            }
            map.setCenter(berlin);
        }
        function onFinishGetLocation(){
            $('.loading').hide();
        }
      function getCurrentLocation() {
        // Try HTML5 geolocation.
        $('.loading').show();
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            pos.lat = position.coords.latitude;
            pos.lng = position.coords.longitude;
            googleMapLink = pos.lat != null && pos.lng != null ?
             "http://www.google.com/maps/place/" + pos.lat +"," + pos.lng : 
             null;

            //TODO only allow sending email when location found?

            infoWindow.setPosition(pos);
            infoWindow.setContent('Ihr Standort');
            infoWindow.open(map);

            var centerPos = {
                lat: pos.lat - 250,
                lng: pos.lng
            }

            

            map.setCenter(centerPos);
            map.setZoom(16);

            // var myLatlng = new google.maps.LatLng(centerPos.lat,centerPos.lng);
            // var marker2 = new google.maps.Marker({
            //     position: myLatlng,
            //     map: map,
            //     title: 'Hello World!'
            // });

            onFinishGetLocation();
          }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
          });
        } else {
          // Browser doesn't support Geolocation
          handleLocationError(false, infoWindow, map.getCenter());
        }
      }

      function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        console.log('handleLocationError');
        var msg = browserHasGeolocation ?
                              'Fehler: die Standort-Ermittlung funktioniert nicht, überprüfen Sie bitte Ihre Standort-Einstellungen. Alternativ können Sie die Adresse als Bemerkung angeben.' :
                              'Fehler: Ihr Browser unterstützt keine Standort-Ermittlung.';

        showError(msg);
        /*infoWindow.setPosition(pos);
        //TODO Error
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
        */
      }


      function showError(msg){
          console.log('Showing error: ', msg);
          $('.loading').hide();
          $('.alert').remove();
          if(msg){
            var dom = $('<div class="alert alert-warning alert-dismissible fade show" role="alert">'
                     + msg +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> ' +
                        '<span aria-hidden="true">&times;</span>'+
                    '</button>' +
                '</div>');

                $('.form-finding').prepend(dom);
          }
      }

      function showSuccess(msg){
          console.log('Showing success: ', msg);
          $('.alert').remove();
          $('.loading').hide();
          if(msg){
            var dom = $('<div class="alert alert-success alert-dismissible fade show" role="alert">'
                     + msg +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> ' +
                        '<span aria-hidden="true">&times;</span>'+
                    '</button>' +
                '</div>');

                $('.form-finding').prepend(dom);
          }
      }



    





   //Submit form
   function clearForm(){
       //window.location = window.location; //Refresh the page
   }

   function onSuccess(){
        showSuccess('Besten Dank für Ihre Unterstützung, Ihre Nachricht wurde erfolgreich übermittelt.')
        $('.loading').hide();
        scrollTop();
        clearForm();
   }

   function onError(error){
       if(error != null && error != undefined){
           showError(error)
       } else {
            showError("Fehler: Es besteht ein Problem mit dem Server. Bitte nochmals versuchen.");
       }
        $('.loading').hide();
        scrollTop();
   }

   var resizedImageBase64 = null;
   function doSubmit(){
       if((pos.lat == null || pos.lng == null) && $('#inputComment').val().trim() == ""){
           showError('Bitte den Standort durch die Geolokalisierung oder als Bemerkung angeben.');
           return false;
       }
       console.log("doSubmit()", "Start uploading and sending email");
       $('.loading').show();
        var file = $('#image')[0].files[0];
        
        var data = new FormData();
        //data.append( 'image',  file), "filename";
        if(resizedImageBase64 != null){
            data.append('image_base64', resizedImageBase64);
        }
        data.append('lat', pos.lat);
        data.append('lng', pos.lng);
        data.append('comment', $('#inputComment').val()),
        data.append('bike_id', $('#inputBikeID').val()),
        data.append('email', $('#inputEmail').val()),
        data.append('phone_number', $('#inputPhoneNumber').val()),
        data.append('google_map_link', googleMapLink),

        $.ajax({ 
            url: 'index.php', 
            type: 'post', 
            data: data, 
            contentType: false, 
            processData: false, 
            success: function(response){ 
                console.log('response', response);
                if(response.success){
                    onSuccess();
                } else {
                    var message = null;
                    if(response.limited){
                        //alert('limit');
                        var limitedIn = response.limited_in;
                        var message = "Besten Dank für Ihre Unterstützung, Sie haben jedoch zu viele Übermittlungen in kurzer Zeit getätigt (" + limitedIn + ")";
                    }
                    onError(message);
                }
            },
            error: function(errror){
                onError(error)
            }
        }); 

        
        return false;
    }

    function scrollTop(){
        $('.box').stop().animate({scrollTop:0}, 200, 'swing', function() {});
    }

  

    function imageToDataUri(img, width, height) {

        // create an off-screen canvas
        var canvas = document.createElement('canvas'),
            ctx = canvas.getContext('2d');

        // set its dimension to target size
        canvas.width = width;
        canvas.height = height;

        // draw source image into the off-screen canvas:
        ctx.drawImage(img, 0, 0, width, height);

        // encode image to data-uri with base64 version of compressed image
        return canvas.toDataURL('image/jpeg', 0.9); //quality
        }

    //Capture image
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var imageBase64 = e.target.result;
                //Resize image
                //$('#image-display').attr('src', imageBase64);

                var img = new Image();
                img.onload = function() {
                    console.log(this.width, this.height, 'kaka');
                    var ew = 800;
                    var eh = 800;
                    var ratio = this.width/ew;
                    if(ratio > 1){
                        var eh = this.height/ratio;
                    } else {
                        ew = this.width;
                        eh = this.height;
                    }
                    var newImg = imageToDataUri(this, ew, eh);
                    $('#image-display').attr('src', newImg);
                    resizedImageBase64 = newImg;

                    //Test
                    // var img2 = new Image();
                    // img2.onload = function(){
                    //     console.log(this.width, this.height, 'hoho');
                    // }
                    // img2.src = newImg;

                    // console.log(newImg.length/1024);

                }
                img.src = imageBase64;                    
            };

            reader.readAsDataURL(input.files[0]);
        }
    }


    //TODO Error/Success 
    </script>
    <script defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCg0-frHnM59_GK8tZvBZe_m4UJwlfH6Y0&callback=initMap">
    </script>

</body>

</html>
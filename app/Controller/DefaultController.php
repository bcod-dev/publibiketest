<?php
namespace Controller;

use TinyFw\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TinyFw\Lang;

class DefaultController extends Controller
{

    public function langAction()
    {
        //_act_=lang&lang=us
        $defaultLang = "en";
        $lang = isset($_GET['lang']) ? $_GET['lang'] : $defaultLang;
        if(!in_array($lang, array_keys($this->lang->langs))){
            $lang = $defaultLang;
        }
        
        Lang::instance()->setLang($lang);

        $return = isset($_GET['return']) ? $_GET['return'] : 'index';
        return $this->redirect('?_act_='.trim($return));
    }

    public function indexAction()
    {
        $lang = Lang::instance()->getLang();
        $langs = Lang::instance()->getSupportedLangs();

        return $this->render(array('lang' => $lang, 'langs' => $langs));
    }

    public function trans($key)
    {
        return trans($key);
    }

    public function reportAction()
    {  
        $domain =  str_replace("\\",'/',"http://".$_SERVER['HTTP_HOST'].substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT'])));
		

        $lat = isset($_POST["lat"]) ? $_POST["lat"] : "";
        $lng = isset($_POST["lng"]) ? $_POST["lng"] : "";
        $location = $lat != "" && $lng != "" && $lat != "null" && $lng != "null" ? $lat.",".$lng: "";
        $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
        $bikeId = isset($_POST["bike_id"]) ? $_POST["bike_id"] : "";
        $email = isset($_POST["email"]) ? $_POST["email"] : "";
        $phoneNumber = isset($_POST["phone_number"]) ? $_POST["phone_number"] : "";
        $googleMapLink = isset($_POST["google_map_link"]) ? $_POST["google_map_link"] : "";
        $googleMapLink = ($googleMapLink == "null" || $googleMapLink == null) ? "" : $googleMapLink;
        //$imageData = isset($_POST['image_base64']) ? $_POST['image_base64'] : "";
        $ip = $this->getRealIp();


        if($this->input->requestMethod() == 'POST'){
			//echo "Hello";
			//echo "<pre>";print_r($_POST);//exit;
			//echo "<pre>";print_r($_FILES);
			//$fileNames = array_filter($_FILES['image']['name']);
			//echo "<pre>";print_r($fileNames);//exit;
            $limitResult  = $this->checkLimit($ip);
            $limited = $limitResult['limited'];
            $limitedIn = $limitResult['limitedIn'];
            
            //Upload image
            $uploadOkay = false;
            $imageUrl = null;
            if(!$limited){
				 // File upload configuration 
				//$uploadDir = dirname(__FILE__, 3).'/uploads/';
		    		$uploadDir = 'https://github.com/bcod-dev/publibiketest/tree/master/uploads/';
				$allowTypes = array('jpg','png','jpeg','gif'); 
				if(!empty($fileNames)){ 
					foreach($_FILES['image']['name'] as $key=>$val){ 
						// File upload path 
						$fileName = basename($_FILES['image']['name'][$key]); 
						$targetFilePath = $uploadDir . $fileName; 
						 
						// Check whether file type is valid 
						$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
						if(in_array($fileType, $allowTypes)){ 
							// Upload file to server 
							if(move_uploaded_file($_FILES["image"]["tmp_name"][$key], $targetFilePath)){ 
								//echo "upload";
								if($isUploadOk >= 0 ) { 
									$isUploadOk = 1;
								}
								$imageUrl = null;
								if($isUploadOk > 0 ){
									$domain =  str_replace("\\",'/',"http://".$_SERVER['HTTP_HOST'].substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT'])));
									$imageUrl = $isUploadOk == 1?  $domain."/uploads/".$fileName : "";   
								}

								return array(
									'isUploadOk' => $isUploadOk, 
									'imageUrl' => $imageUrl,
									'isUploadOk' => $isUploadOk,
									'isUploadOk' => $isUploadOk
								);
								
							}else{ 
								//echo "Not upload";
								$isUploadOk = -1;	
							} 
						}else{ 
							throw new \Exception('invalid image type');
						} 
					}
				}	
				/*if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
				for($count = 0; $count<count($_FILES["image"]["name"]); $count++)
					{
						$imageData = $_FILES['image']['name'][$count];
						$_FILES["file"]["name"] = $_FILES["image"]["name"][$count];
						$_FILES["file"]["type"] = $_FILES["image"]["type"][$count];
						$_FILES["file"]["tmp_name"] = $_FILES["image"]["tmp_name"][$count];
						$_FILES["file"]["error"] = $_FILES["image"]["error"][$count];
						$_FILES["file"]["size"] = $_FILES["image"]["size"][$count]; 
						
						$uploadResult = $this->doUpload($imageData);
						$uploadOkay = $uploadResult['isUploadOk'];
						$imageUrl = $uploadResult['imageUrl'];
						
						$uploadDir = dirname(__FILE__, 3).'/uploads/';
						//$img = $_FILES['image']['name'];
						$tmp = $_FILES["image"]["tmp_name"][$count];

						// get uploaded file's extension
						$ext = strtolower(pathinfo($imageData, PATHINFO_EXTENSION));

						// can upload same image using rand function
						$final_image = rand(1000,1000000).$imageData;

						 
						$path = $uploadDir.strtolower($final_image); 

						if(move_uploaded_file($imageData,$uploadDir)) 
						{
							echo "Hello";
							//echo $insert?'ok':'err';
						}
						
					}
				}	
				*/
			}
			//exit;

            //Send mail
            $mailOkay = false;
            $mailError = null;
            if(!$limited){
                $emailTitle = $this->trans('email_tittle')." ".date("Y-m-d H:i:s");
                $emailBody = "
                - ".$this->trans('coordinate').": $location \r\n
                - ".$this->trans('link_to_google_map').": $googleMapLink \r\n
                - ".$this->trans('link_to_picture').": $imageUrl \r\n
                - ".$this->trans('Bemerkung').": $comment \r\n
                - ".$this->trans('lost_bike_id').": $bikeId \r\n
                - ".$this->trans('email_addess').": $email \r\n
                - ".$this->trans('phone_number').": $phoneNumber \r\n
                ";
                
                $maillResult = $this->sendMail($emailTitle, $emailBody);
                $mailOkay = $maillResult['mailOkay'];
                $mailError = $maillResult['mailError'];
            }

            

            //Do Sendmail
            $result = array(
                "POST" => $_POST,
                "FILES" => $_FILES,
                "SESSION" => $_SESSION,
                "upload_ok" => $uploadOkay,
                "image_url" => $imageUrl,
                "email_ok" => $mailOkay,
                "email_error" => $mailError, 
                "success" => $mailOkay && $uploadOkay >=0,
                "limited" => $limited,
                "limited_in" => $limitedIn,
                "ip" => $ip,
                "env" => _ENV
            );
            if($result['success'] == true){
                //Limit here => only when send success is limited
                $this->setLimit($ip);
            }
            return $this->renderJSON($result);
        }

        return $this->render(array());
    }

    public function setLimit($ip)
    {
        if(!isset($_SESSION['ip'])){
            $_SESSION['ip'] = $ip;
        }

        $_SESSION['lastTime'] = time();
        
    }

    public function checkLimit($ip)
    {
        $result  = array(
            'limited' => false, 
            'limitedIn' => -1,
        );

        //1 time limit 60s per ip 
        $limit = 60; 
        $lastTime = isset($_SESSION['lastTime']) ? $_SESSION['lastTime'] : 0;
        $limitedIn = $limit - (time() - $lastTime) > 0 ? $limit - (time() - $lastTime) : 0;

    

        if(isset($_SESSION['ip']) && $_SESSION['ip'] == $ip && $limitedIn > 0 ) //Same ip and already pass 1 min
        {
            $result['limited'] = true;
        }

        $result['limitedIn'] = $limitedIn;


        $result['isset'] = isset($_SESSION['ip']);
        $result['timelimit_gt_0'] = $limitedIn > 0;
        //die(print_r($result));

        return $result;
    }

    public function getRealIp()
    {
        if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
                $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
                return trim($addr[0]);
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }


    public function doUpload($imageData)
    {
        $uploadDir = dirname(__FILE__, 3).'/uploads';
        if(!file_exists($uploadDir)){
            mkdir($uploadDir, 0775);
        }

        $uploadFileName = "";
        $uploadFileType = "";
        
        $isUploadOk = 0;
        if($imageData != null && $imageData != "null" && $imageData != ""){
            /*if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                $uploadFileType = $type;
            
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
            }*/
			$allowed = array('jpg','jpeg','png','gif');
			$ext = strtolower(pathinfo($imageData, PATHINFO_EXTENSION));
			
			if(!empty($imageData)){
				if (!in_array($ext, $allowed)) {
                    throw new \Exception('invalid image type');
                }
				if ($imageData === false) {
                    $isUploadOk = -1;
                }
			}else {
                $isUploadOk = -1;
            }
    
            if($isUploadOk >= 0 ) { 
				echo "upload ok";
				$fileNameCmps = explode(".", $imageData);
				$fileExtension = strtolower(end($fileNameCmps));
				$newFileName = md5(time() . $imageData) . '.' . $fileExtension;
				/*echo "ImgType-".$imageFileType = strtolower(pathinfo($imageData,PATHINFO_EXTENSION));
                $uploadFileName = md5(microtime().rand());
                $uploadedFilePath = $uploadDir."/".$imageData; // Corect*/
                $uploadedFilePath = $uploadDir."/".$newFileName;
                //file_put_contents($uploadedFilePath, $imageData);
                file_put_contents($uploadedFilePath, $imageData);
                $isUploadOk = 1;
            }
        }
        $imageUrl = null;
        if($isUploadOk > 0 ){
            $domain =  str_replace("\\",'/',"http://".$_SERVER['HTTP_HOST'].substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT'])));
            $imageUrl = $isUploadOk == 1?  $domain."/uploads/".$uploadFileName.".".$uploadFileType : "";  
        }

        return array(
            'isUploadOk' => $isUploadOk, 
            'imageUrl' => $imageUrl,
            'isUploadOk' => $isUploadOk,
            'isUploadOk' => $isUploadOk
        );        
    }
    

    public function sendMail($emailTitle, $emailBody)
    {
        $result = array(
            'mailOkay' => false, 
            'mailError' => ''
        );

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
            $mail->setFrom('kadambari@bcod.co.in', 'Publibke');

            if(_ENV == "dev")
            {
                $mail->addAddress('kadambari@bcod.co.in');
                //$mail->addAddress('sofian.zubi@publibike.ch');
            }
            else 
            {
                $mail->addAddress('email2lostbiketicket@o-1pbg85gwazpe7aslaozn27obujy8xem83d72llqp14nos04kem.0y-a2afuai.eu25.apex.salesforce.com');
            }
            
            // Content
            $mail->isHTML(false);
            if(_ENV == "dev"){
                $mail->Subject = "[TEST ENV]".$emailTitle;
            } else {
                $mail->Subject = $emailTitle;
            }
            $mail->Body    = $emailBody;
            //$mail->AltBody = $emailBody;

            $mail->send();
            //echo 'Message has been sent';
            $result['mailOkay'] = true;

        } catch (Exception $e) {
            $result['mailError'] = $mail->ErrorInfo;
        }

        return $result;
    }
}

<?php
require '../vendor/autoload.php';
//require '../database/data_layer.php';
use Twilio\Rest\Client;
use PHPMailer\PHPMailer\PHPMailer;
class business_layer{

    function sendEmail($address,$subject, $body, $attachmentPath=0){
        //need to set time for smtp purposes
        date_default_timezone_set('America/New_York');

        //new instance, set to google smtp settings standards
        $mail = new PHPMailer(TRUE);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        //email address to send from
        $mail->Username = $_SERVER['EMAIL_ADDR'];
        //password
        $mail->Password = $_SERVER['EMAIL_PASSWORD'];

        try{
            //set attributes to passed in
            $mail->setFrom('team-pearl-rit@gmail.com', 'RIT Team Pearl');
            $mail->addAddress($address);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->IsHTML(true);
            //add the attachment
            if ($attachmentPath){
                //make db path relative using ../
                $mail->addAttachment("../".$attachmentPath);
            }
            //Send it
            $mail->send();
            return true;
        }
        catch (\Exception $e)//Slash is for generic exception class
        {
           echo $e->getMessage();
           return false;
        }
    }

    function sendText($text){
        // Twilio credentials
        $account_sid = $_SERVER['TWILIO_SID'];
        $auth_token = $_SERVER['TWILIO_TOKEN'];
        $twilio_number = $_SERVER['TWILIO_PHONE'];;

        //Create twilio client using our credentials
        $client = new Client($account_sid, $auth_token);
        //send message
        $client->messages->create(
            // Where to send a text message (your cell phone?)
            '+15856455810',//should be a passed in $phoneNumber but free version doesnt allow it.
            array(
                'from' => $twilio_number,
                'body' => $text
            )
        );
    }

    function uploadFile($fileArray,$callBack){
        //Passed in name of the file with extension
        $name = $fileArray['name'];
        if ($callBack == 'csv') $name = "currEmpCSV.csv";
        //Temp file stored from upload. Full path
        $tempFile = $fileArray['tmp_name'];
        //targetFile
        $targetFile = "../assets/uploads/" . $name;
        //File type
        $type = strtolower(end(explode('.',$name)));

        $notAllowedTypes = array("exe", "js", "sql", "php");

        //only go forward if the file type is allowed
            //only go forward if it doesnt exst
        if( !in_array($type,$notAllowedTypes) || ($callBack == 'csv' && $type == 'csv')){
            //if it already exists overwrite it
            move_uploaded_file($tempFile,$targetFile);
            return true;
        }
        else {
            //Not correct file type
            return false;
        }
    }

    function passwordReset($email){
        $hashedEmail = password_hash($email,PASSWORD_DEFAULT);

        $subject = "RRCC Account Password Reset";
        $body = "<h1>Please click the link below to reset your password</h1>";
        $body .= "<h2 href='localhost/views/resetConfirm.php?email=$hashedEmail' >Reset</h2>";
        //generate url to send in email. ../resetPassword.php?emailToReset
        //send email
        //confirm email that is being reset
        //if input matches $_GET as well as some unique identifier then reset the password


        // $dataLayer = new data_layer();
        // generate a random 10 character string.
        // $genPass = substr(md5(microtime()),rand(0,26),10);
        // database call to set new password and update temp password flag
        // $dataLayer->setUserTempPass($email,$genPass);
        // echo "your new password is $genPass";

        //address , subject line, body
        //if($bizLayer->sendEmail($_POST['email'], 'Password Reset test', "Your New Password is $genPass")){
        //};
    }

    function validateAndSanitize($postData){

        //$validatedPOST = array();
        //$validatedPOST['phone'] = $postData['phone'];// this should be validated and sanitized
        //return $validatedPOST;
    }

    function createNewsTable($notificationArray){
        $string = "";
        $rowCount = 1;
        $nextRowCount = 2;
        foreach ($notificationArray as $rowArray) {
            $currNotiID = $rowArray['notificationID'];
            $currTitle = $rowArray['title'];
            $currBody = $rowArray['body'];
            //split file path using /
            //Take the end of the array becuse it is the name of the file
            $currAttachmentName = end(explode("/",$rowArray['attachment']));
            $currActiveYN = (intval($rowArray['active']));

            $string .= <<<END
            <form class="" action="adminAction.php?id={$currNotiID}" method="post" enctype="multipart/form-data">
            <tr class='collapsed'>
                <td><i onclick="dropDownToggle(this)" class='fas fa-chevron-circle-up'></i></td> <!-- Onclick this icon needs to be updated to fas fa-chevron-circle-up -->
                <td>
                    <input type="text" name="title" disabled value="{$currTitle}">
                </td>
                <td>
                    <select disabled name='activeYN' class='disabledDrop'>
                        <option value='1'>Yes</option>
                        <option
END;
            if (!$currActiveYN) $string .= " selected ";
            $string .= <<<END

                         value='0'>No</option>
                    </select>
                </td>
                <td>
                    <i id='notiEditButton' onclick="dropDownModify(this,'noti');" class='fas fa-pencil-alt'></i>
                    <button class="hidden" id='notiSaveEditButton' type= "submit" name="modifyNoti" value="modifyNoti"><i class="fas fa-save"></i></button>
                </td>
                <td>
                    <button type="submit" name= "deleteNoti" value="deleteNoti"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>

            <tr class='spacer'><td></td></tr>

            <tr class='un-collapsed'>
                <td colspan='5' class='full'>
                    <h2>Body</h2>
                    <textarea id='bodyContent' name="body" disabled>{$currBody}</textarea>
                    <h2>Attachment</h2>
                    <i class="fas fa-times-circle"></i><span>{$currAttachmentName}</span>
                    <h2>User Ack. Report</h2>
                    <i class="fas fa-download"></i><span>user_report.csv</span>
                </td>
            </tr>
            <tr class='spacer'><td></td></tr>
        </form>
END;
        $rowCount++;
        $nextRowCount++;
        }

        return $string;
    }

    function createUserTable($allUserArray){
        //$phone,$fName,$lName,$tempPassYN,$password, $email, $deptID, $authID, $userID, $activeYN
        $string = '';
        foreach ($allUserArray as $thisUserArray) {
            $currID = $thisUserArray['userID'];
            $currFName = $thisUserArray['fName'];
            $currLName = $thisUserArray['lName'];
            $currActiveYN = $thisUserArray['activeYN'];
            $currEmail = $thisUserArray['email'];
            $currDeptID = $thisUserArray['deptID'];
            $currAuthID = $thisUserArray['authID'];
            $currPhone = $thisUserArray['phone'];

            $string .= <<<END
            <form class="" action="adminAction.php?id={$currID}" method="post">
                <tr class='collapsed'>
                    <td><i onclick="dropDownToggle(this)" class='fas fa-chevron-circle-down'></i></td>
                    <td><input type="text" name="fName" disabled value="{$currFName}"></td>
                    <td><input type="text" name="lName" disabled value="{$currLName}"></td>
                    <td>
                        <i id='empEditButton' onclick="dropDownModify(this,'emp');" class='fas fa-pencil-alt'></i>
                        <button class="hidden" id='empSaveEditButton' type= "submit" name="modifyEmp" value="modifyEmp"><i class="fas fa-save" onclick=''></i></button>
                    </td>
                    <td>
                        <button type="submit" name= "deleteEmp" value="deleteEmp"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>

                <tr class='spacer'><td></td></tr>

                <!-- Row that is hidden in collapsed row, needs JS to unhide this https://codepen.io/andornagy/pen/gaGBZz -->

                <tr class='un-collapsed'>
                    <td colspan='3' class='leftUnCollapsed'>
                        <h2>Active</h2>
                        <select disabled name='activeYN' class='disabledDrop'>
                            <option value=1>Yes</option>
                            <option
END;
            if (!$currActiveYN) $string .= " selected ";
            $string .= <<<END
                        value=0>No</option>

                        </select>

                        <h2>Department</h2>
                        <select disabled name='deptID' class='disabledDrop'>
                            <option
END;
            if ($currDeptID == 1) $string .= " selected ";
            $string .= <<<END
                            value=1>HR</option>
                            <option
END;
            if ($currDeptID == 2) $string .= " selected ";
            $string .= <<<END
                            value=2>Admin</option>
                            <option
END;
            if ($currDeptID == 3) $string .= " selected ";
            $string .= <<<END
                            value=3>Sales</option>
                            <option
END;
            if ($currDeptID == 4) $string .= " selected ";
            $string .= <<<END
                            value=4>Production</option>
                            <option
END;
            if ($currDeptID == 5) $string .= " selected ";
            $string .= <<<END
                            value=5>Operations</option>
                            <option
END;
            if ($currDeptID == 6) $string .= " selected ";
            $string .= <<<END
                            value=6>Food and Beverage</option>
                            <option
END;
            if ($currDeptID == 7) $string .= " selected ";
            $string .= <<<END
                            value=7>Garage</option>
                        </select>

                        <h2>Email</h2>
                        <input type="text" name="email" class='email' disabled value="{$currEmail}">
                    </td>
                    <td colspan='2' class='rightUnCollapsed'>
                        <h2>Authorization</h2>
                        <select disabled name='authID' class='disabledDrop fullWidth'>
                            <option
END;
            if ($currAuthID == 2) $string .= " selected ";
            $string .= <<<END
                            value=2>Employee</option>
                            <option
END;
            if ($currAuthID == 3) $string .= " selected ";
            $string .= <<<END
                            value=3>Depart. Head</option>
                            <option
END;
            if ($currAuthID == 4) $string .= " selected ";
            $string .= <<<END
                            value=4>Administrator</option>
                        </select>

                        <h2>Phone Number</h2>
                        <input type="text" name="phone" disabled value="{$currPhone}">
                    </td>
                </tr>
            </form>
END;
        }

        return $string;
    }


}

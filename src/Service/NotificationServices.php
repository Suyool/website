<?php

namespace App\Service;

use App\Entity\Notification\content;
use App\Entity\Notification\Notification;
use App\Entity\Notification\Template;
use App\Entity\Notification\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class NotificationServices
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $suyoolServices;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, $certificate, $hash_algo)
    {
        $this->mr = $mr->getManager('notification');

        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->suyoolServices = $suyoolServices;
    }

    public function checkUser($userid,$lang,$hash_algo,$certificate)
    {
        $singleUser = $this->mr->getRepository(Users::class)->findOneBy(['suyoolUserId' => $userid]);
        if($singleUser == null){
            $suyoolUser = $this->suyoolServices->GetUser($userid, $hash_algo, $certificate);
            // dd($suyoolUser);
            if($suyoolUser != null){
                $userFirstname = $suyoolUser["FirstName"];
                $userLastname = $suyoolUser["LastName"];
                $userLang = $suyoolUser["LanguageID"];
    
                $user = new Users;
                $user
                    ->setsuyoolUserId($userid)
                    ->setfname($userFirstname)
                    ->setlname($userLastname)
                    ->setlang($userLang);
    
                $this->mr->persist($user);
                $this->mr->flush();
                
            $userid=$this->mr->getRepository(Users::class)->findOneBy(['suyoolUserId'=>$userid,'lang'=>$lang]);
            }
            
        }else{
            $userid=$this->mr->getRepository(Users::class)->findOneBy(['suyoolUserId'=>$userid,'lang'=>$lang]);
        }
        

        if ($userid != null) {
            return true;
        } else {
            return false;
        }
    }

    public function PushSingleNotification($notificationId, $userId, $content, $params, $additionalData)
    {
        $paramsTextDecoded = json_decode($params, true);
        foreach ($paramsTextDecoded as $field => $value) {
            $$field = $value;
        }

        $singleUser = $this->mr->getRepository(Users::class)->findOneBy(['suyoolUserId' => $userId]);
        if ($singleUser == null) {
            $suyoolUser = $this->suyoolServices->GetUser($userId, $this->hash_algo, $this->certificate);

            $userFirstname = $suyoolUser["FirstName"];
            $userLastname = $suyoolUser["LastName"];
            $userLang = $suyoolUser["LanguageID"];

            $user = new Users;
            $user
                ->setsuyoolUserId($userId)
                ->setfname($userFirstname)
                ->setlname($userLastname)
                ->setlang($userLang);

            $this->mr->persist($user);
            $this->mr->flush();
            // echo "user coming from api";
        } else {
            $userFirstname = $singleUser->getfname();
            $userLastname = $singleUser->getlname();
            $userLang = $singleUser->getlang();
            // echo "user coming from db";
        }

        $notTemplate = $this->mr->getRepository(content::class)->findOneBy(['id' => $content]);
        if ($notTemplate != null) {
            if ($userLang == 1) {
                $title = $notTemplate->gettitleEN();
                $subject = $notTemplate->getsubjectEN();
                $body = $notTemplate->getbodyEN();
                $notification = $notTemplate->getnotificationEN();
                $proceedButton = $notTemplate->getproceedButtonEN();
            } else {
                $title = $notTemplate->gettitleAR();
                $subject = $notTemplate->getsubjectAR();
                $body = $notTemplate->getbodyAR();
                $notification = $notTemplate->getnotificationAR();
                $proceedButton = $notTemplate->getproceedButtonAR();
            }
        } else {
            echo "No Template availble for this id!!";
        }

        eval("\$title = \"$title\";");
        // echo "<br>" . $title;
        eval("\$subject = \"$subject\";");
        // echo "<br>" . $subject;
        eval("\$body = \"$body\";");
        // echo "<br>" . $body;
        eval("\$notification = \"$notification\";");
        // echo "<br>" . $notification;
        eval("\$proceedButton = \"$proceedButton\";");
        // echo "<br>" . $proceedButton;

        $PushSingle = $this->suyoolServices->PushSingleNotification($userId, $title, $subject, $body, $notification, $proceedButton, $notTemplate->getisInbox(), $notTemplate->getflag(), $notTemplate->getnotificationType(), $notTemplate->getisPayment(), $notTemplate->getisDebit(), $additionalData);
        // echo json_encode($PushSingle);

        // $file = "notification.txt";



        // $PushSingle["globalCode"] = 0;
        if ($PushSingle["globalCode"] == 0) {
            $singleNotification = $this->mr->getRepository(Notification::class)->findOneBy(['id' => $notificationId]);

            // $myfile = fopen($file, "a");
            // fwrite($myfile, $singleNotification->getId() . " --- ");
            // fclose($myfile);

            if ($singleNotification != null) {
                $singleNotification
                    ->setstatus("send")
                    ->seterrorMsg("success")
                    ->setproceedButton($proceedButton)
                    ->settitleOut($title)
                    ->setbodyOut($notification)
                    ->settitleIn($subject)
                    ->setbodyIn($body)
                    ->setsendDate(date('Y-m-d H:i:s'));
                $this->mr->persist($singleNotification);
                $this->mr->flush();
            } else {
                echo "notification not existe!";
            }
        } else {
            $singleNotification = $this->mr->getRepository(Notification::class)->findOneBy(['id' => $notificationId]);
            if ($singleNotification != null) {
                $singleNotification
                    ->setstatus("canceled")
                    ->seterrorMsg($PushSingle["flagCode"]);
                $this->mr->persist($singleNotification);
                $this->mr->flush();
            } else {
                echo "notification not existe!";
            }
        }

        // dd($PushSingle);
        return 1;
    }

    public function addNotification($userId, $content, $params, $bulk, $additionalData = null)
    {
        //Bulk 1 to be added if bulknotification 0 if single notification
        $notification = new Notification;
        $notification
            ->setuserId($userId)
            ->setbulk($bulk)
            ->setcontentId($content)
            ->setstatus("pending")
            ->seterrorMsg(null)
            ->setparams($params)
            ->setadditionalData($additionalData);

        $this->mr->persist($notification);
        $this->mr->flush();

        return 1;
    }

    public function PrcessingNot($notId)
    {

        $singleNotification = $this->mr->getRepository(Notification::class)->findOneBy(['id' => $notId]);
        $singleNotification
            ->setstatus("processing");
        $this->mr->persist($singleNotification);
        $this->mr->flush();

        return 1;
    }


    public function PushBulkNotification($notificationId, $userId, $content, $params, $additionalData)
    {
        $paramsTextDecoded = json_decode($params, true);
        foreach ($paramsTextDecoded as $field => $value) {
            $$field = $value;
        }

        $userIds = explode(",", $userId);

        // dd($userId);

        foreach ($userIds as $userId) {
            $singleUser = $this->mr->getRepository(Users::class)->findOneBy(['suyoolUserId' => $userId]);
            if ($singleUser == null) {
                $suyoolUser = $this->suyoolServices->GetUser($userId, $this->hash_algo, $this->certificate);

                $userFirstname = $suyoolUser["FirstName"];
                $userLastname = $suyoolUser["LastName"];
                $userLang = $suyoolUser["LanguageID"];

                $user = new Users;
                $user
                    ->setsuyoolUserId($userId)
                    ->setfname($userFirstname)
                    ->setlname($userLastname)
                    ->setlang($userLang);

                $this->mr->persist($user);
                $this->mr->flush();
                // echo "user coming from api";
            } else {
                $userFirstname = $singleUser->getfname();
                $userLastname = $singleUser->getlname();
                $userLang = $singleUser->getlang();
                // echo "user coming from db";
            }
        }


        $notTemplate = $this->mr->getRepository(content::class)->findOneBy(['id' => $content]);
        if ($notTemplate != null) {
            if ($userLang == 1) {
                $title = $notTemplate->gettitleEN();
                $subject = $notTemplate->getsubjectEN();
                $body = $notTemplate->getbodyEN();
                $notification = $notTemplate->getnotificationEN();
                $proceedButton = $notTemplate->getproceedButtonEN();
            } else {
                $title = $notTemplate->gettitleAR();
                $subject = $notTemplate->getsubjectAR();
                $body = $notTemplate->getbodyAR();
                $notification = $notTemplate->getnotificationAR();
                $proceedButton = $notTemplate->getproceedButtonAR();
            }
        } else {
            echo "No Template availble for this id!!";
        }

        eval("\$title = \"$title\";");
        // echo "<br>" . $title;
        eval("\$subject = \"$subject\";");
        // echo "<br>" . $subject;
        eval("\$body = \"$body\";");
        // echo "<br>" . $body;
        eval("\$notification = \"$notification\";");
        // echo "<br>" . $notification;
        eval("\$proceedButton = \"$proceedButton\";");
        // echo "<br>" . $proceedButton;

        $BroadCast = $this->suyoolServices->PushBulkNotification($userIds, $title, $subject, $body, $notification, $proceedButton, $notTemplate->getisInbox(), $notTemplate->getflag(), $notTemplate->getnotificationType(), $notTemplate->getisPayment(), $notTemplate->getisDebit(), $additionalData);
        // echo json_encode($PushSingle);
        if ($BroadCast["globalCode"] == 0) {
            $BulkNotification = $this->mr->getRepository(Notification::class)->findOneBy(['id' => $notificationId]);

            if ($BulkNotification != null) {
                $BulkNotification
                    ->setstatus("send")
                    ->seterrorMsg("success")
                    ->setproceedButton($proceedButton)
                    ->settitleOut($title)
                    ->setbodyOut($notification)
                    ->settitleIn($subject)
                    ->setbodyIn($body)
                    ->setsendDate(date('Y-m-d H:i:s'));
                $this->mr->persist($BulkNotification);
                $this->mr->flush();
            } else {
                echo "notification not existe!";
            }
        } else {
            $BulkNotification = $this->mr->getRepository(Notification::class)->findOneBy(['id' => $notificationId]);
            if ($BulkNotification != null) {
                $BulkNotification
                    ->setstatus("canceled")
                    ->seterrorMsg($PushSingle["flagCode"]);
                $this->mr->persist($BulkNotification);
                $this->mr->flush();
            } else {
                echo "notification not existe!";
            }
        }

        // dd($PushSingle);
        return 1;
    }

    public function getContent($template)
    {
        $templateId = $this->mr->getRepository(Template::class)->findOneBy(['identifier' => $template]);
        $index = $templateId->getIndex();
        $content = $this->mr->getRepository(content::class)->findOneBy(['template' => $templateId->getId(), 'version' => $index]);

        return $content;
    }
}

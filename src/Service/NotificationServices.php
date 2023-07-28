<?php

namespace App\Service;

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

    public function __construct(ManagerRegistry $mr, SuyoolServices1 $suyoolServices, $certificate, $hash_algo)
    {
        $this->mr = $mr->getManager('notification');

        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->suyoolServices = $suyoolServices;
    }

    public function PushSingleNotification($notificationId, $userId, $notificationTemplate, $params)
    {
        $paramsTextDecoded = json_decode($params, true);
        foreach ($paramsTextDecoded as $field => $value) {
            $$field = $value;
        }

        // dd($amount);
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
            echo "user coming from api";
        } else {
            $userFirstname = $singleUser->getfname();
            $userLastname = $singleUser->getlname();
            $userLang = $singleUser->getlang();
            echo "user coming from db";
        }

        $notTemplate = $this->mr->getRepository(Template::class)->findOneBy(['id' => $notificationTemplate]);
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
        echo "<br>" . $title;
        eval("\$subject = \"$subject\";");
        echo "<br>" . $subject;
        eval("\$body = \"$body\";");
        echo "<br>" . $body;
        eval("\$notification = \"$notification\";");
        echo "<br>" . $notification;
        eval("\$proceedButton = \"$proceedButton\";");
        echo "<br>" . $proceedButton;

        $PushSingle = $this->suyoolServices->PushSingleNotification($userId, $title, $subject, $body, $notification, $proceedButton);
        if ($PushSingle["globalCode"] == 0) {
            $singleNotification = $this->mr->getRepository(Notification::class)->findOneBy(['id' => $notificationId]);

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
                    ->setstatus("not complete")
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

    public function addNotification($userId, $notificationTemplate)
    {

        $notification = new Notification;
        $notification
            ->setuserId($userId)
            ->settemplateId($notificationTemplate)
            ->setstatus("complete")
            ->seterrorMsg(null)
            ->setparams("");

        $this->mr->persist($notification);
        $this->mr->flush();

        return 1;
    }

    public function cron()
    {

        $not = $this->mr->getRepository(Notification::class)->findBy(['status' => "pending"]);

        foreach ($not as $notify) {
            $PushSingleNot = $this->PushSingleNotification($notify->getId(), $notify->getuserId(), $notify->gettemplateId(), $notify->getparams());
        }
        // dd($not);

        return 1;
    }
}

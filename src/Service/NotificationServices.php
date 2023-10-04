<?php

namespace App\Service;

use App\Entity\Notification\content;
use App\Entity\Notification\Notification;
use App\Entity\Notification\Template;
use App\Entity\Notification\Users;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class NotificationServices
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $suyoolServices;
    private $logger;

    public function __construct(LoggerInterface $logger, ManagerRegistry $mr, SuyoolServices $suyoolServices, $certificate, $hash_algo)
    {
        $this->mr = $mr->getManager('notification');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->suyoolServices = $suyoolServices;
        $this->logger = $logger;
    }

    public function checkUser($userid, $lang)
    {
        try {

            $singleUser = $this->mr->getRepository(Users::class)->findOneBy(['suyoolUserId' => $userid]);

            if ($singleUser == null) {
                $suyoolUser = $this->suyoolServices->GetUser($userid, $this->hash_algo, $this->certificate);
                //dd($suyoolUser);
                if (is_null($suyoolUser)) return false;

                $user = new Users;
                $user
                    ->setsuyoolUserId($userid)
                    ->setfname($suyoolUser["FirstName"])
                    ->setlname($suyoolUser["LastName"])
                    ->setlang($suyoolUser["LanguageID"]);
                $this->mr->persist($user);
                $this->mr->flush();
                $this->logger->debug("New User: {$suyoolUser['FirstName']}, {$suyoolUser['LastName']}, {$suyoolUser['LanguageID']}");
            } else {
                $this->logger->debug("Existing User: " . $singleUser->getsuyoolUserId() . " " . $singleUser->getfname() . " " . $singleUser->getlname());
            }
            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->debug(json_encode($suyoolUser));
            return false;
        }
    }

    public function PushSingleNotification($notificationId, $userId, $content, $params, $additionalData)
    {
        $paramsTextDecoded = json_decode($params, true);
        foreach ($paramsTextDecoded as $field => $value) {
            $$field = $value;
        }
        if (isset($amount)) {
            $amount = number_format($amount);
        }
        if (isset($numgrids)) {
            if ($numgrids > 1) {
                $numgrids = $numgrids . " Grids";
            } else {
                $numgrids = $numgrids . " Grid";
            }
        }

        if (isset($bouquetgrids)) {
            if ($bouquetgrids > 1) {
                $bouquetgrids = $bouquetgrids . " Grids";
            } else {
                $bouquetgrids = $bouquetgrids . " Grid";
            }
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
                ->setfname($suyoolUser["FirstName"])
                ->setlname($suyoolUser["LastName"])
                ->setlang($suyoolUser["LanguageID"]);
            $this->mr->persist($user);
            $this->mr->flush();
        } else {
            $userFirstname = $singleUser->getfname();
            $userLastname = $singleUser->getlname();
            $userLang = $singleUser->getlang();
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
        eval("\$subject = \"$subject\";");
        eval("\$body = \"$body\";");
        eval("\$notification = \"$notification\";");
        eval("\$proceedButton = \"$proceedButton\";");

        $PushSingle = $this->suyoolServices->PushSingleNotification($userId, $title, $subject, $body, $notification, $proceedButton, $notTemplate->getisInbox(), $notTemplate->getflag(), $notTemplate->getnotificationType(), $notTemplate->getisPayment(), $notTemplate->getisDebit(), $additionalData);

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
                echo "notification not exist!";
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
                echo "notification not exist!";
            }
        }
        return 1;
    }

    public function addNotification($userId, $content, $params, $bulk, $additionalData = null)
    {
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
                    ->setfname($suyoolUser["FirstName"])
                    ->setlname($suyoolUser["LastName"])
                    ->setlang($suyoolUser["LanguageID"]);
                $this->mr->persist($user);
                $this->mr->flush();
            } else {
                $userFirstname = $singleUser->getfname();
                $userLastname = $singleUser->getlname();
                $userLang = $singleUser->getlang();
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
        eval("\$subject = \"$subject\";");
        eval("\$body = \"$body\";");
        eval("\$notification = \"$notification\";");
        eval("\$proceedButton = \"$proceedButton\";");

        $BroadCast = $this->suyoolServices->PushBulkNotification($userIds, $title, $subject, $body, $notification, $proceedButton, $notTemplate->getisInbox(), $notTemplate->getflag(), $notTemplate->getnotificationType(), $notTemplate->getisPayment(), $notTemplate->getisDebit(), $additionalData);
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
                echo "notification not exist!";
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
                echo "notification not exist!";
            }
        }
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

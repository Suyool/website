<?php


namespace App\Controller;


use App\Entity\AubLogs;
use App\Entity\AubUser;
use App\Entity\AubUsers;
use App\Service\SuyoolServices;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;


class AubRallyPaperController extends AbstractController
{
    private $hash_algo;
    private $certificate;
    private $helper;
    private $client;
    private $mr;
    private $suyoolServices;
    private $cache;


    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, AdapterInterface $cache)
    {
        $this->hash_algo = $_ENV['ALGO'];
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->client = HttpClient::create();
        $this->helper = new Helper($this->client);
        $this->mr = $mr->getManager('default');
        $this->suyoolServices = $suyoolServices;
        $this->cache = $cache;
    }

    /**
     * @Route("/aub-rally-paper", name="app_aub_rally_paper")
     */
    public function aubRallyPaper(Request $request, SessionInterface $session, PaginatorInterface $paginatorInterface)
    {
        if ($session->has('expire_time') && time() > $session->get('expire_time')) {
            // Session has expired, invalidate the session
            $session->invalidate();
            // Optionally, redirect to login page or perform other actions
            return $this->redirectToRoute('app_aub_login');
        }
        // Check if the user is not logged in
        $status = $request->query->get('status');
        if (!$session->has('username')) {
            // Redirect to login page
            return $this->redirectToRoute('app_aub_login');
        }
        $teamCode = $session->get('team_code');

        $hash = base64_encode(hash($this->hash_algo,  $teamCode . $this->certificate, true));
        $body = [
            'code' =>  $teamCode,
            'secureHash' => $hash
        ];
        $data = $this->suyoolServices->rallyPaperOverview($body);

        $item = $this->cache->getItem($teamCode);
        $item->set($data)->expiresAfter(1800);
        $this->cache->save($item);
        /*
        0 pending
        1 downoaded the app
        2 fully
        3 requested
        */
        if (!empty($data)) {
            $data['toBeDisplayed'] = []; // Initialize the 'toBeDisplayed' array
            if (is_null($status)) {
                if (isset($data['status'])) {
                    // foreach ($data['status'] as $status => $statused) {
                    foreach ($data['status']['pending'] as $statused) {
                        switch ($statused['status']) {
                            case 0:
                                $displayedStatus = 'Pending Enrollment';
                                $class = 'pending';
                                break;
                            case 1:
                                $displayedStatus = 'Pending Enrollment';
                                $class = 'pending';
                                break;
                            case 2:
                                $displayedStatus = 'Fully Enrolled';
                                $class = 'fully';
                                break;
                            case 3:
                                $displayedStatus = 'Requested Card';
                                $class = 'requested';
                                break;
                            case 4:
                                $displayedStatus = 'Card Payment';
                                $class = 'card';
                                break;
                        }
                        if (is_null($statused['fullName'])) {
                            $statused['fullName'] = "";
                        }
                        $toBeDisplayedItem[] = [
                            'status' => $displayedStatus,
                            'fullyname' => $statused['fullName'],
                            'mobileNo' => $statused['mobileNo'],
                            'id' => $statused['id'],
                            'status2' => $statused['status'],
                            'class' => $class
                        ];
                    }
                    // }
                } else {
                    $toBeDisplayedItem = [];
                }
            } else {
                if (isset($data['status'][$status])) {
                    foreach ($data['status'][$status] as $statused) {
                        if (is_null($status)) {
                            if (is_null($statused['fullName'])) {
                                $statused['fullName'] = "";
                            }
                            $toBeDisplayedItem[] = [
                                'status' => 'All Members',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status'],
                            ];
                        } else if ($status == 'pending') {
                            if (is_null($statused['fullName'])) {
                                $statused['fullName'] = "";
                            }
                            $toBeDisplayedItem[] = [
                                'status' => 'Pending enrollment',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status'],
                                'class' => $status
                            ];
                        } else if ($status == 'downloaded') {
                            if (is_null($statused['fullName'])) {
                                $statused['fullName'] = "";
                            }
                            $toBeDisplayedItem[] = [
                                'status' => 'Pending Enrollment',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status'],
                                'class' => 'pending'
                            ];
                        } else if ($status == 'fully') {
                            if (is_null($statused['fullName'])) {
                                $statused['fullName'] = "";
                            }
                            $toBeDisplayedItem[] = [
                                'status' => 'Fully Enrolled',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status'],
                                'class' => $status
                            ];
                        } else if ($status == 'requested') {
                            if (is_null($statused['fullName'])) {
                                $statused['fullName'] = "";
                            }
                            $toBeDisplayedItem[] = [
                                'status' => 'Requested Card',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status'],
                                'class' => $status
                            ];
                        } else if ($status == 'card') {
                            if (is_null($statused['fullName'])) {
                                $statused['fullName'] = "";
                            }
                            $toBeDisplayedItem[] = [
                                'status' => 'Card Payment',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status'],
                                'class' => $status
                            ];
                        }
                    }
                } else {
                    $toBeDisplayedItem = [];
                }
            }





            if ($request->isXmlHttpRequest()) {
                $data['toBeDisplayed2'] = $toBeDisplayedItem;

                return new JsonResponse([
                    'response' =>  $data,
                    'error' => 'Success.'
                ], 200);
            }
            $data['toBeDisplayed'][] = $toBeDisplayedItem;
            $parameters = [
                'status' => true,
                'message' => 'Returning Data',
                'body' => $data,
                'teamCode' => $teamCode
            ];
        } else {
            $parameters = [
                'teamCode' => $teamCode,
            ];
            $parameters['status'] = false;
            $parameters['message'] = 'Empty Data';
        }


        // dd($parameters);

        return $this->render('aubRallyPaper/index.html.twig', $parameters);
    }

    /**
     * @Route("/rallypaperinvitation/{code}", name="aub_invitation")
     */
    public function aubInvitation(Request $request, $code = null): Response
    {
        $group = $this->mr->getRepository(AubUsers::class)->findOneBy(['username' => $code]);
        if (empty($group)) {
            return $this->redirectToRoute("homepage");
        }
        if ($request->isXmlHttpRequest()) {
            $requestParam = $request->request->all();

            $switch = isset($requestParam['switch']) ? $requestParam['switch'] : 0;
            $mobile = $requestParam['mobile'];
            $hash = base64_encode(hash($this->hash_algo, $mobile . $code . $switch . $this->certificate, true));

            $form_data = [
                'mobileNo' => $mobile,
                'teamCode' => $code,
                'switchTeam' => $switch,
                'secureHash' => $hash

            ];
            $response = $this->suyoolServices->rallyPaperInvite($form_data);

            $logs = new AubLogs();
            $logs
                ->setidentifier($mobile)
                ->setrequest(json_encode($form_data))
                ->setresponse(json_encode($response));
            $this->mr->persist($logs);
            $this->mr->flush();
            //            $response = [
            //                "globalCode" => 0,
            //                "flagCode" => 2,
            //                "title" => "Number Already Linked",
            //                "body" => "Your phone number is already linked to this team Team2. You're eligible to help them earn points. What are you waiting for?",
            //                "buttonText" => "copy link"
            //            ];
            return new JsonResponse($response);
        }
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "HOW_CAN_MY_TEAM_EARN_POINTS_WITH_SUYOOL_PRE_CHALLENGE",
                "Desc" => "YOUR_TEAM_CAN_SCORE"
            ],
            "THREE" => [
                "Title" => "WHAT_ACTION_WILL_GIVE_POINTS",
                "Desc" => "WHEN_ONE_OF_YOUR_MEMBERS"
            ],
            "FOUR" => [
                "Title" => "HOW_TO_INVITE_MEMBERS_TO",
                "Desc" => "ALL_YOU_HAVE_TO_DO_IS"
            ],
            "FIVE" => [
                "Title" => "HOW_CAN_I_KNOW_RANK",
                "Desc" => "YOU_CAN_TRACK_YOUR_TEAM"
            ],
        ];
        $parameters['code'] = $code;
        $parameters['displayName'] = $group->getDisplayName();

        return $this->render('aubRallyPaper/invitation.html.twig', $parameters);
    }

    /**
     * @Route("/aub-rally-paper-ranking", name="app_aub_rally_paper_ranking")
     * @Cache(smaxage="120", public=true)
     */
    public function aubRallyPaperRanking(Request $request)
    {
        // $cacheKey = 'teamRanking';
        // $cachedRanking = $this->cache->getItem($cacheKey);
        // $cachedRankings = $cachedRanking->get();
        // if(!empty($cachedRankings['rankingsData'])) {
        //     $response = $cachedRanking->get();
        // }else {
        $response = $this->suyoolServices->getTeamsRankings();
        // }

        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "HOW_CAN_MY_TEAM_EARN_POINTS_WITH_SUYOOL_PRE_CHALLENGE",
                "Desc" => "YOUR_TEAM_CAN_SCORE"
            ],
            "THREE" => [
                "Title" => "WHAT_ACTION_WILL_GIVE_POINTS",
                "Desc" => "WHEN_ONE_OF_YOUR_MEMBERS"
            ],
            "FOUR" => [
                "Title" => "HOW_TO_INVITE_MEMBERS_TO",
                "Desc" => "ALL_YOU_HAVE_TO_DO_IS"
            ],
            "FIVE" => [
                "Title" => "HOW_TO_ENROLL_TO_SUYOOL",
                "Desc" => "TO_ENROLL_IN_SUYOOL"
            ],
            "SIX" => [
                "Title" => "IF_THEY_ALREADY_HAVE",
                "Desc" => "IF_THEY_ALREADY_HAVE_A_SUYOOL"
            ],
            "SEVEN" => [
                "Title" => "HOW_TO_ORDER_CARD",
                "Desc" => "ONCE_YOUR_INFORMATION_IS_VALIDATED"
            ],
            "EIGHT" => [
                "Title" => "CAN_I_ALSO_PAY_ONLINE",
                "Desc" => "YES_YOU_CAN_USE_CARD_ONLINE"
            ],
            "NINE" => [
                "Title" => "HOW_CAN_I_KNOW_RANK",
                "Desc" => "YOU_CAN_TRACK_YOUR_TEAM"
            ],
        ];
        $parameters['rankingsData'] = $response['rankingsData'];
        return $this->render('aubRallyPaper/rank.html.twig', $parameters);
    }

    /**
     * @Route("/aub-login", name="app_aub_login")
     */
    public function aubLogin(Request $request, SessionInterface $session)
    {
        if ($session->has('expire_time')) {
            return $this->redirectToRoute('app_aub_rally_paper');
        }
        if ($request->isXmlHttpRequest()) {

            $username = $request->request->get('_username');
            $password = $request->request->get('_password');
            $user = $this->mr->getRepository(AubUsers::class)->findOneBy(['username' => $username]);
            $session->set('team_displayName', $user->getDisplayName());

            if (!$user) {
                return new JsonResponse(['error' => 'User not found.'], 400);
            }
            $hashedPassword = md5($password);
            if ($user->getPassword() === $hashedPassword) {
                $session->set('username', $user->getUsername());
                $session->set('team_code', $user->getCode());

                $session->set('expire_time', time() + 1800);

                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse([
                    'flagCode' => 2,
                    'error' => 'Invalid credentials. Please try again.'
                ], 200);
            }
        }
        return $this->render('aubRallyPaper/login.html.twig');
    }

    /**
     * @Route("/aub-search", name="app_search",methods="POST")
     */
    public function search(Request $request, SessionInterface $session)
    {
        $datacharacter = json_decode($request->getContent(false), true);
        $status = $datacharacter["status"];
        $teamCode = $session->get('team_code');
        $hash = base64_encode(hash($this->hash_algo,  $teamCode . $this->certificate, true));
        $body = [
            'code' => $teamCode,
            'secureHash' => $hash
        ];
        $item = $this->cache->getItem($teamCode);
        $data = $item->get();

        if (!empty($data)) {
            $data['toBeDisplayed'] = []; // Initialize the 'toBeDisplayed' array
            if (!is_null($status)) {
                // Check if the requested status exists in the data
                if (array_key_exists($status, $data['status'])) {
                    foreach ($data['status'][$status] as $statused) {
                        switch ($statused['status']) {
                            case 0:
                                $displayedStatus = 'Pending Enrollment';
                                $class = 'pending';
                                break;
                            case 1:
                                $displayedStatus = 'Pending Enrollment';
                                $class = 'pending';
                                break;
                            case 2:
                                $displayedStatus = 'Fully Enrolled';
                                $class = 'fully';
                                break;
                            case 3:
                                $displayedStatus = 'Requested Card';
                                $class = 'requested';
                                break;
                            case 4:
                                $displayedStatus = 'Card Payment';
                                $class = 'card';
                                break;
                        }
                        $toBeDisplayedItem[] = [
                            'status' => $displayedStatus,
                            'fullyname' => $statused['fullName'],
                            'mobileNo' => $statused['mobileNo'],
                            'id' => $statused['id'],
                            'status2' => $statused['status'],
                            'class' => $class
                        ];
                    }
                    $data['toBeDisplayed'][] = $toBeDisplayedItem;
                } else {
                    // If the requested status doesn't exist in the data, return empty result
                    return new JsonResponse([
                        'data' => []
                    ], 200);
                }
            }
            $parameters = [
                'status' => true,
                'message' => 'Returning Data',
                'body' => $data,
                'teamCode' => $teamCode
            ];
        } else {
            $parameters['status'] = false;
            $parameters['message'] = 'Empty Data';
        }

        // Filter the results based on the search character
        $foundResults = [];
        foreach ($parameters['body']['toBeDisplayed'][0] as $body) {
            $input = $datacharacter['char'];
            if (stripos($body['fullyname'], $input) !== false) {
                // Partial match found, add it to the result array
                if (is_null($body['fullyname'])) {
                    $body['fullyname'] = "";
                }
                $foundResults[] = $body;
            }
        }

        if (empty($foundResults)) {
            return new JsonResponse([
                'data' => []
            ], 200);
        }

        $parameters['body']['toBeDisplayed'][0] = $foundResults;
        return new JsonResponse([
            'status' => true,
            'data' => $parameters['body']['toBeDisplayed'][0]
        ]);
    }
}

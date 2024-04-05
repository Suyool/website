<?php


namespace App\Controller;


use App\Entity\AubLogs;
use App\Entity\AubUser;
use App\Entity\AubUsers;
use App\Service\SuyoolServices;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class AubRallyPaperController extends AbstractController
{
    private $hash_algo;
    private $certificate;
    private $helper;
    private $client;
    private $mr;
    private $suyoolServices;


    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices)
    {
        $this->hash_algo = $_ENV['ALGO'];
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->client = HttpClient::create();
        $this->helper = new Helper($this->client);
        $this->mr = $mr->getManager('default');
        $this->suyoolServices = $suyoolServices;
    }

    /**
     * @Route("/aub-rally-paper", name="app_aub_rally_paper")
     */
    public function aubRallyPaper(Request $request, SessionInterface $session)
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
        $hash = base64_encode(hash($this->hash_algo,  'code2' . $this->certificate, true));
        $body = [
            'code' => 'code2',
            'secureHash' => $hash
        ];
        $data = $this->suyoolServices->rallyPaperOverview($body);
        /*
        0 pending
        1 requested
        2 fully
        3 activated
        4 card payment
        */
        // dd($data);
        if (!empty($data)) {
            $data['toBeDisplayed'] = []; // Initialize the 'toBeDisplayed' array
            if (is_null($status)) {
                foreach ($data['status'] as $status => $statused) {
                    foreach ($data['status'][$status] as $statused) {
                        switch ($statused['status']) {
                            case 0:
                                $displayedStatus = 'Pending Modification';
                                break;
                            case 1:
                                $displayedStatus = 'Requested Card';
                                break;
                            case 2:
                                $displayedStatus = 'Fully Enrolled';
                                break;
                            case 3:
                                $displayedStatus = 'Activated Card';
                                break;
                            case 4:
                                $displayedStatus = 'Card Payment';
                                break;
                        }
                        $toBeDisplayedItem[] = [
                            'status' => $displayedStatus,
                            'fullyname' => $statused['fullName'],
                            'mobileNo' => $statused['mobileNo'],
                            'id' => $statused['id'],
                            'status2' => $statused['status']
                        ];
                    }
                }
            } else {
                if (isset($data['status'][$status])) {
                    foreach ($data['status'][$status] as $statused) {
                        if (is_null($status)) {
                            $toBeDisplayedItem[] = [
                                'status' => 'All Members',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status']
                            ];
                        } else if ($status == 'pending') {
                            $toBeDisplayedItem[] = [
                                'status' => 'Pending modification',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status']
                            ];
                        } else if ($status == 'requested') {
                            $toBeDisplayedItem[] = [
                                'status' => 'Requested Card',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status']
                            ];
                        } else if ($status == 'fully') {
                            $toBeDisplayedItem[] = [
                                'status' => 'Fully Enrolled',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status']
                            ];
                        } else if ($status == 'activated') {
                            $toBeDisplayedItem[] = [
                                'status' => 'Activated Card',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status']
                            ];
                        } else if ($status == 'card') {
                            $toBeDisplayedItem[] = [
                                'status' => 'Card Payment',
                                'fullyname' => $statused['fullName'],
                                'mobileNo' => $statused['mobileNo'],
                                'id' => $statused['id'],
                                'status2' => $statused['status']
                            ];
                        }
                    }
                } else {
                    $toBeDisplayedItem = [];
                }
            }
            $data['toBeDisplayed'][] = $toBeDisplayedItem;
            // switch ($status) {
            //     case 0:
            //         $data[]['toBeDisplayed'] = [
            //             'status' => 'Pending modification',
            //         ];
            //         break;
            //     case 1:
            //         $data[]['toBeDisplayed'] = [
            //             'status' => 'Requested Card',
            //             'fullyname' => $data['status']['requested']['fullName'],
            //             'mobileNo' => $data['status']['requested']['mobileNo'],
            //             'id' => $data['status']['requested']['id']
            //         ];
            //         break;
            //     case 2:
            //         $data[]['toBeDisplayed'] = [
            //             'status' => 'Fully Enrolled',
            //             'fullyname' => $data['status']['fully']['fullName'],
            //             'mobileNo' => $data['status']['fully']['mobileNo'],
            //             'id' => $data['status']['fully']['id']
            //         ];
            //         break;
            //     case 3:
            //         $data[]['toBeDisplayed'] = [
            //             'status' => 'Activated Card',
            //             'fullyname' => $data['status']['activated']['fullName'],
            //             'mobileNo' => $data['status']['activated']['mobileNo'],
            //             'id' => $data['status']['activated']['id']
            //         ];
            //         break;
            //     case 4:
            //         $data[]['toBeDisplayed'] = [
            //             'status' => 'Card Payment',
            //             'fullyname' => $data['status']['card']['fullName'],
            //             'mobileNo' => $data['status']['card']['mobileNo'],
            //             'id' => $data['status']['card']['id']
            //         ];
            //         break;
            // }
            $parameters = [
                'status' => true,
                'message' => 'Returning Data',
                'body' => $data,
                'teamCode'=>$teamCode
            ];
        } else {
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

        return $this->render('aubRallyPaper/invitation.html.twig', $parameters);
    }

    /**
     * @Route("/aub-rally-paper-ranking", name="app_aub_rally_paper_ranking")
     */
    public function aubRallyPaperRanking(Request $request)
    {
        $response = $this->suyoolServices->getTeamsRankings();

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
}

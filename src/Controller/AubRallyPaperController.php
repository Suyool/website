<?php


namespace App\Controller;


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


    public function __construct(ManagerRegistry $mr,SuyoolServices $suyoolServices)
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
        if (!$session->has('username')) {
            return $this->redirectToRoute('app_aub_login');
        }
        $teamCode = $session->get('team_code');

        return $this->render('aubRallyPaper/index.html.twig');
    }

    /**
     * @Route("/rallypaperinvitation/{code}", name="aub_invitation")
     */
    public function aubInvitation(Request $request, $code =null): Response
    {
        if ($request->isXmlHttpRequest()) {
            $requestParam = $request->request->all();

            $switch = isset($requestParam['switch']) ?$requestParam['switch'] : 0;
            $mobile = $requestParam['mobile'];
            $hash = base64_encode(hash($this->hash_algo, $mobile . $code . $switch . $this->certificate, true));

            $form_data = [
                'mobileNo' => $mobile,
                'teamCode' => $code,
                'switchTeam' => $switch,
                'secureHash' => $hash

            ];
            $response = $this->suyoolServices->rallyPaperInvite($form_data);
            return new JsonResponse($response);

        }
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_IMA",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_IMA"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_IMA"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
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

        return $this->render('aubRallyPaper/rank.html.twig');
    }

    /**
     * @Route("/aub-login", name="app_aub_login")
     */
    public function aubLogin(Request $request, SessionInterface $session)
    {
        if ($session->has('username')) {
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
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['flagCode' => 2,
                    'error' => 'Invalid credentials. Please try again.'], 200);
            }
        }
        return $this->render('aubRallyPaper/login.html.twig');
    }
}
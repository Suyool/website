<?php
namespace App\Controller;
use App\Entity\emailsubscriber;
use App\Entity\Managers;
use App\Entity\Rates;
use App\Translation\translation;
use App\Utils\Helper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
class DefaultController extends AbstractController
{
    private $trans;
    public function __construct(translation $trans)
    {
        $this->trans=$trans;
    }
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function indexAction(Request $request, TranslatorInterface $translator,EntityManagerInterface $em)
    {
        // dd($em->getRepository(Managers::class)->findAll());
        $trans=$this->trans->translation($request,$translator);
        $message='';
        if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['email']!=null &&!$em->getRepository(emailsubscriber::class)->findOneBy(['email'=>$_POST['email']])){
            $emailSubcriber=new emailsubscriber;
            $emailSubcriber->setEmail($_POST['email']);
            $emailSubcriber->setCreated(new DateTime());
            $em->persist($emailSubcriber);
            $em->flush();
            $message="Email Added";
            header("Refresh:0");
        }
        return $this->render('homepage/CommingSoon.html.twig',['message'=>$message]);
        // return $this->render('homepage/index.html.twig');
    }
}
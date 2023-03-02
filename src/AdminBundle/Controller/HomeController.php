<?php

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\Type\ManagerType;
use App\Entity\Managers;
use App\Utils\Helper;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\AdminBundle\Form\Type\RateType;
use App\Entity\Rates;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="admin_homepage")
     */
    public function indexAction(PaginatorInterface $paginator, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $managersQuery = $this->getDoctrine()
            ->getRepository(Managers::class)
            ->getAllManagers();

        //number of news per page
        $recordsPerPage = 30;

        $parameters['managers'] = $paginator->paginate(
            $managersQuery,
            $request->get('page', 1),
            $recordsPerPage
        );

        return $this->render('@Admin/homepage/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/managers/new", name="new_manager",
     * )
     *
     * @Route(
     *     "/managers/edit/{id}", name="edit_manager",
     *     requirements = {
     *          "id": "\d+"
     *     }
     * )
     *
     */
    public function newManagerAction($id = null, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $manager = new Managers();

        if(isset($id)) {
            $manager = $this->getDoctrine()
                ->getRepository(Managers::class)
                ->find($id);
        }

        $old_password = $manager->getPass();

        $form = $this->createForm(ManagerType::class, $manager);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ( $manager->getPlainPassword() ) {
                $password = $passwordEncoder->encodePassword($manager, $manager->getPlainPassword());
                $manager->setPass($password);
            }else{
                $manager->setPass($old_password);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($manager);
            $entityManager->flush();

            return $this->redirectToRoute("admin_homepage");
        }

        return $this->render('@Admin/homepage/managerForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
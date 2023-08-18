<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConfigureMenuItems\ConfigureMenuItems;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractDashboardController
{
    private $mr;
    private $paginator;
    private $request;

    public function __construct(ManagerRegistry $mr, PaginatorInterface $paginator,RequestStack $request)
    {
        $this->mr = $mr->getManager('default');
        $this->paginator = $paginator;
        $this->request = $request->getCurrentRequest();

    }

    /**
     * @Route("/users", name="admin_users")
     */
    public function index(): Response
    {
        $usersRepository = $this->mr->getRepository(User::class)->findAll();

        $currentPage = $this->request->query->getInt('page', 1);

        $pagination = $this->paginator->paginate(
            $usersRepository,  // Query to paginate
            $currentPage,   // Current page number
            5             // Records per page
        );

        return $this->render('Admin/Users/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route(
     *     "/user/new", name="admin_users_new",
     * )
     *
     * @Route(
     *     "/user/edit/{id}", name="edit_user",
     *     requirements = {
     *           "id": "\d+"
     *     }
     * )
     */
    public function create($id = null,UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $user = new User();
        if(isset($id)) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($id);
        }

        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $hashedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("admin_users");
        }

        return $this->render('Admin/Users/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function configureMenuItems(): iterable
    {
        $configureMenuItems = new ConfigureMenuItems();
        return $configureMenuItems->configureMenuItems();
    }
}


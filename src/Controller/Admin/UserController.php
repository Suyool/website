<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\SearchUsersType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('default');

    }

    /**
     * @Route("dashadmin/users", name="admin_users")
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
//        if (!$this->isGranted('ROLE_ADMIN')) {
//            throw new AccessDeniedException('You do not have permission to access this page.');
//        }
        $formSearch = $this->createForm(SearchUsersType::class);
        $formSearch->handleRequest($request);
        $value = $request->get('search_users',"");

        $UsersQuery = $this->getDoctrine()
            ->getRepository(User::class)
            ->getAllUsers($value);

        $pagination = $paginator->paginate(
            $UsersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15             // Records per page
        );

        return $this->render('Admin/Users/index.html.twig', [
            'users' => $pagination,
            'formSearch' => $formSearch->createView(),
        ]);
    }

    /**
     * @Route(
     *     "dashadmin/user/new", name="admin_users_new",
     * )
     *
     * @Route(
     *     "dashadmin/user/edit/{id}", name="edit_user",
     *     requirements = {
     *           "id": "\d+"
     *     }
     * )
     */
    public function create($id = null,UserPasswordEncoderInterface $passwordEncoder,Request $request): Response
    {

        $user = new User();
        if(isset($id)) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($id);
        }

        $form = $this->createForm(UserFormType::class, $user, [
            'is_edit' => isset($id),
        ]);

        $form->handleRequest($request);

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
}


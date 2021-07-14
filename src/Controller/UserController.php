<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddUserType;
use App\Form\ContactType;
use App\Form\EditUserType;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("users/affiche",name="listUsers")
     */
    public function affiche(Request $request){
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('user/listUsers.html.twig' ,[
            "users"=>$users
        ]);
    }
    /**
     * @Route("users/details/{id}",name="detailUser")
     */
    public function affichedetails($id){
        $repo=$this->getDoctrine()->getRepository(User::class)->find($id);
        return $this->render('user/details.html.twig',['user'=>$repo]);
    }
    /**
     * @Route("users/delete/{id}",name="deleteUser")
     */
    public function delete($id,UserRepository $repo){
        $em=$this->getDoctrine()->getManager();
        $user=$repo->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('listUsers');
    }
    /**
     * @Route("admin/users/add",name="AddUser")
     */
    function Ajout(Request $request,UserPasswordHasherInterface $passwordHasher){
        $user=new User();

        $form=$this->createForm(AddUserType::class,$user);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $em->persist($user);//insert into
            $em->flush();//maj de la BD
            return $this->redirectToRoute("listUsers");
        }
        return $this->render('user/ajouter.html.twig',['f'=>$form->createView()]);
    }

    /**
    @Route("users/update/{id}",name="userUpdate")

     */
    function update($id,UserRepository $repo,Request $request){
        $user=$repo->find($id) ;
        $form=$this->createForm(AddUserType::class,$user);
        $form->add('modifier',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();//maj de la BD
            return $this->redirectToRoute("listUsers");
        }

        return $this->render("user/update.html.twig",['f'=>$form->createView()]);

    }

    /**
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     * @Route("/contact",name="contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer){
            $message = (new \Swift_Message('Réclamation!'))
                ->setFrom('docdocpidev@gmail.com')
                ->setTo('wael.zaghouani@esprit.tn')
                ->setBody(
                    'gggggggg',
                    'text/plain'
                )
            ;

            $mailer->send($message);



        return $this->render('user/details.html.twig');
    }

    /**
     * @param Request $request
     * @param UserRepository $repo
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/user/modifier/{id}",name="modifierutilisateur")
     */
    public function editUser( Request $request, UserRepository $repo,$id){
        $user=$repo->find($id);
        $form = $this->createForm(EditUserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('listUsers');
        }

        return $this->render('user/edituser.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }


}

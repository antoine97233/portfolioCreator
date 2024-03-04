<?php

namespace App\Controller\Admin;

use App\Entity\User as AppUser;
use App\Form\AttributeSkillType;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/skills', name: 'admin.skill.')]
class SkillController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SkillRepository $skillRepository, Security $security): Response
    {
        /** @var UserInterface $user */
        $user = $security->getUser();
        $userId = $user->getId();

        $skillsByUser = $skillRepository->findAllByUser($userId);

        return $this->render('admin/skill/index.html.twig', [
            'controller_name' => 'SkillController',
            'skills' => $skillsByUser,
        ]);
    }


    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        /** @var AppUser $user */
        $user = $security->getUser();

        $form = $this->createForm(AttributeSkillType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $selectedSkill = $data['skill'];
            $scoreSkills = $data['scoreSkills'];
            $user->addSkill($selectedSkill);

            $em->persist($selectedSkill);

            foreach ($scoreSkills as $scoreSkill) {
                /** @var ScoreSkill $scoreSkill */
                $scoreSkill->setSkill($selectedSkill);
                $scoreSkill->setUser($user);
                $em->persist($scoreSkill);
            }

            $em->flush();

            $this->addFlash(
                'success',
                'Skills added successfully'
            );

            return $this->redirectToRoute('admin.skill.index');
        }

        return $this->render('admin/skill/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

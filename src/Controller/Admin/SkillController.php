<?php

namespace App\Controller\Admin;

use App\Entity\User as AppUser;
use App\Entity\Skill;
use App\Form\SkillType;
use App\Form\UserSkillType;
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


        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($skill->getScoreSkills() as $scoreSkill) {
                $scoreSkill->setUser($user);

                $em->persist($scoreSkill);
            }

            $user->addSkill($skill);

            $em->persist($skill);
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

<?php

namespace App\Controller\Admin;

use App\Entity\ScoreSkill;
use App\Entity\User;
use App\Form\ScoreSkillType;
use App\Repository\ScoreSkillRepository;
use App\Security\Voter\ScoreSkillVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/skills', name: 'admin.skill.')]
class ScoreSkillController extends AbstractController
{


    #[Route('/', name: 'index')]
    public function index(
        #[CurrentUser] User $user,
        ScoreSkillRepository $scoreSkillRepository
    ): Response {


        $scoresSkills = $scoreSkillRepository->findAllSkillsWithScoresByUser($user->getId());

        return $this->render('admin/skill/index.html.twig', [
            'scoresSkills' => $scoresSkills,
        ]);
    }


    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
    ): Response {

        $scoreSkill = new ScoreSkill();

        $form = $this->createForm(ScoreSkillType::class, $scoreSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scoreSkill->setUser($user);

            $em->persist($scoreSkill);
            $em->flush();

            $this->addFlash(
                'success',
                'Skills added successfully'
            );

            return $this->redirectToRoute('admin.skill.index');
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Add',
            'table' => 'skill'
        ]);
    }


    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ScoreSkillVoter::EDIT, subject: 'scoreSkill')]
    public function edit(
        ScoreSkill $scoreSkill,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(ScoreSkillType::class, $scoreSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Experience edited successfully'
            );

            return $this->redirectToRoute('admin.skill.index');
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit',
            'table' => 'skill'
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ScoreSkillVoter::DELETE, subject: 'scoreSkill')]
    public function delete(
        ScoreSkill $scoreSkill,
        EntityManagerInterface $em,
    ): Response {

        $em->remove($scoreSkill);
        $em->flush();

        $this->addFlash(
            'success',
            'Skill removed successfully'
        );

        return $this->redirectToRoute('admin.skill.index');
    }
}

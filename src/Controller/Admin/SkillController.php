<?php

namespace App\Controller\Admin;

use App\Entity\Skill;
use App\Entity\User as AppUser;
use App\Form\AttributeSkillType;
use App\Form\SkillType;
use App\Repository\SkillRepository;
use App\Security\Voter\SkillVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/skills', name: 'admin.skill.')]
class SkillController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted(SkillVoter::LIST)]
    public function index(SkillRepository $skillRepository, Security $security): Response
    {
        /** @var UserInterface $user */
        $user = $security->getUser();
        $userId = $user->getId();

        $skillsByUser = $skillRepository->findAllByUserWithScore($userId);


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

    #[Route('/{id}', name: 'remove', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function removeSkill(
        Skill $skill,
        EntityManagerInterface $em,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $user->removeSkill($skill);

        $user->getSkills()->removeElement($skill);
        $em->flush();

        $this->addFlash(
            'success',
            'Skill removed successfully'
        );

        return $this->redirectToRoute('admin.skill.index');
    }



    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted(SkillVoter::CREATE)]
    public function create(
        Request $request,
        EntityManagerInterface $em,
    ) {


        $skill = new Skill();

        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($skill);
            $em->flush();

            $this->addFlash(
                'success',
                'Skills added successfully'
            );

            return $this->redirectToRoute('admin.skill.index');
        }

        return $this->render('admin/skill/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

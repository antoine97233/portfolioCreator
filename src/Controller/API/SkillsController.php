<?php

namespace App\Controller\API;

use App\Entity\Skill;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class SkillsController extends AbstractController
{

    #[Route("/api/skills", name: "api_skills_get", methods: ["GET"])]
    public function index(SkillRepository $skillRepository)
    {

        $skills = $skillRepository->findAll();

        return $this->json($skills, 200, [], [
            'groups' => ['skills.index']
        ]);
    }


    #[Route("/api/skills", name: "api_skills_post", methods: ["POST"])]
    public function create(
        Request $request,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['skills.create']
            ]
        )]
        Skill $skill,
        EntityManagerInterface $em

    ) {

        $em->persist($skill);
        $em->flush();
        return $this->json($skill, 200, [], [
            'groups' => ['skills.index']
        ]);
    }
}

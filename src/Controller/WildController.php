<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramSearchType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class WildController
 * @package App\Controller
 * @route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * @param ProgramRepository $programRepository
     * @param Request $request
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(ProgramRepository $programRepository, Request $request): Response
    {
        $programs = $programRepository->findAllWithCategories();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        };

        $form = $this->createForm(ProgramSearchType::class, null, [
                'method' => Request::METHOD_GET
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $programs = $programRepository->findByTitle($data);
        }

        return $this->render(
            'wild/index.html.twig', [
                'programs' => $programs,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Getting a program with a formatted slug for title
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(string $slug = ''): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/category/{categoryName}" , name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName = ''): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()], ['id' => 'DESC'], 3);

        return $this->render('wild/category.html.twig', [
            'category' => $category,
            'category_name' => $categoryName,
            'programs' => $programs,

        ]);

    }

    /**
     * @Route("/program/{programId}", requirements={"program"="[0-9]+"}, name="show_programId")
     * @param string $programId
     * @return Response
     */

    public function showByProgram(string $programId): Response
    {
        if (!$programId) {
            throw $this
                ->createNotFoundException('No program has been found');
        }
        $programTitle = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => mb_strtolower($programId)]);

        return $this->render('wild/program.html.twig', [
            'seasons' => $programTitle->getSeasons(),
            'program' => $programTitle,

        ]);
    }

    /**
     * @Route("/season/{seasonId}", requirements={"season"="[0-9]+"}, name="show_seasonId")
     * @param int $seasonId
     * @return Response
     */
    public function showBySeason(int $seasonId): Response
    {
        if (!$seasonId) {
            throw $this
                ->createNotFoundException('No season has been found');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => mb_strtolower($seasonId)]);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();


        return $this->render('wild/season.html.twig', [
            'episodes' => $episodes,
            'program' => $program,
            'season' => $season,
        ]);
    }

    /**
     * @Route("/episode/{id}", name="show_episode")
     * @param Episode $episode
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function showEpisode(Episode $episode, Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findBy(['episode' => $episode]);
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment = $form->getData();

            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('wild_show_episode', ['id'=>$episode->getId()]);

        }


        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'program' => $program,
            'season' => $season,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);

    }

}

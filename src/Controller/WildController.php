<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramSearchType;
use App\Repository\ProgramRepository;
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
     * @Route("/", name="index")
     * @param Request $request
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        $programs = $this->getDoctrine()
            ->getRepository(program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $programs = $programRepository->findByTitle($data);
        }


        return $this->render('wild/index.html.twig', [
                'programs' => $programs,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, name="show")
     * @param string $slug
     * @return Response
     */
    public function show(?string $slug): Response
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
     * @param string $category
     * @Route("/category/{category}" ,requirements={"category"="[a-z0-9-]+"}, name="show_category")
     * @return Response
     */
    public function ShowByCategory(string $category): Response
    {
        if (!$category) {
            throw $this
                ->createNotFoundException('No Category has been find');
        }
        $categoryName = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($category)]);

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['Category' => $categoryName->getId()], ['id' => 'DESC'], 3);


        return $this->render('wild/category.html.twig',
            ['Category' => $categoryName,
                'programs' => $program,
            ]);
    }


    /**
     * @Route("/program/{program}", requirements={"program"="[0-9]+"}, name="show_program")
     * @param program $program
     * @return Response
     */

    public function showByProgram(program $program): Response
    {
        $seasons = $program->getSeasons();

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }

    /**
     * @Route("/program/{program}/season/{season}/", requirements={"season"="[0-9]+"}, name="show_season")
     * @param season $season
     * @return Response
     */
    public function showBySeason(season $season): Response
    {
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episodes
        ]);
    }

    /**
     * @Route("/episode/{id}", requirements={"season"="[0-9]+"}, name="show_episode")
     * @param episode $episode
     * @return Response
     */
    public function showByEpisode(episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'program' => $program,
            'season' => $season,

        ]);
    }
}
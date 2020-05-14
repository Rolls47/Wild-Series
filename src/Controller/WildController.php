<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


Class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * @Route("wild/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, name="wild_show")
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
     * @Route("/wild/category/{category}" ,requirements={"category"="[a-z0-9-]+"}, name="show_category")
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
        dump($categoryName);


        return $this->render('wild/category.html.twig',
            [   'Category' => $categoryName,
                'programs' => $program,
            ]);
    }
}
<?php


namespace App\Controller;

use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CategoryController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @route("/wild/category/add", name="category_add")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);
    if ($form->isSubmitted()){
        $category = $form->getData();

        $manager->persist($category);
        $manager->flush();
        return $this->redirectToRoute('category_add');
    }
       return $this->render('category/add.html.twig', [
            'form' => $form->createView()]);
    }
}

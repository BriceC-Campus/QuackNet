<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Form\QuackType;
use App\Repository\QuackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuackController extends AbstractController
{
    #[Route('/', name: 'app_quack_index', methods: ['GET'])]
    public function index(QuackRepository $quackRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('quack/index.html.twig', [
            'quacks' => $quackRepository->findAll(),
        ]);
    }

    #[Route('/quack', name: 'app_quackAdmin_index', methods: ['GET'])]
    public function indexAdmin(QuackRepository $quackRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        return $this->render('quack/indexAdmin.html.twig', [
            'quacks' => $quackRepository->findAll(),
        ]);
    }

    #[Route('/quack/new', name: 'app_quack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, QuackRepository $quackRepository): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('view', $user);

        $quack = new Quack();
        $quack->setDuck($user);
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quackRepository->save($quack, true);
            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/quack/{id}', name: 'app_quack_show', methods: ['GET'])]
    public function show(Quack $quack): Response
    {
        $this->denyAccessUnlessGranted('view', $quack);
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);
    }

    #[Route('/quack/{id}/edit', name: 'app_quack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quack $quack, QuackRepository $quackRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $this->getUser());
        if ($quack->getDuck() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $quackRepository->save($quack, true);

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form,
        ]);
    }

    #[Route('/quack/{id}', name: 'app_quack_delete', methods: ['POST'])]
    public function delete(Request $request, Quack $quack, QuackRepository $quackRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $this->getUser());
        if ($quack->getDuck() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete' . $quack->getId(), $request->request->get('_token'))) {
            $quackRepository->remove($quack, true);
        }

        return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
    }
}

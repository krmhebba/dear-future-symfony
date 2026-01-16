<?php

namespace App\Controller;

use App\Entity\Letter;
use App\Form\LetterType;
use App\Repository\LetterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/letter')]
final class LetterController extends AbstractController
{
    #[Route('/', name: 'app_letter_index', methods: ['GET'])]
    public function index(LetterRepository $letterRepository): Response
    {
        return $this->render('letter/index.html.twig', [
            'letters' => $letterRepository->findBy(['author' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_letter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $letter = new Letter();

        $letter->setAuthor($this->getUser());
        $letter->setIsSent(false);

        $form = $this->createForm(LetterType::class, $letter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($letter);
            $entityManager->flush();

            return $this->redirectToRoute('app_letter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('letter/new.html.twig', [
            'letter' => $letter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_letter_show', methods: ['GET'])]
    public function show(Letter $letter): Response
    {
        $isFuture = $letter->getSendDate() > new \DateTime();

        return $this->render('letter/show.html.twig', [
            'letter' => $letter,
            'is_future' => $isFuture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_letter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Letter $letter, EntityManagerInterface $entityManager): Response
    {
        if ($letter->getSendDate() <= new \DateTime()) {
            $this->addFlash('warning', '⏳ Cette lettre est déjà ouverte. Vous ne pouvez plus la modifier.');
            return $this->redirectToRoute('app_letter_show', ['id' => $letter->getId()]);
        }

        $form = $this->createForm(LetterType::class, $letter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Lettre modifiée avec succès !');

            return $this->redirectToRoute('app_letter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('letter/edit.html.twig', [
            'letter' => $letter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_letter_delete', methods: ['POST'])]
    public function delete(Request $request, Letter $letter, EntityManagerInterface $entityManager): Response
    {
        if ($letter->getSendDate() > new \DateTime()) {
            $this->addFlash('error', 'Impossible de supprimer une lettre avant sa date d\'ouverture !');
            return $this->redirectToRoute('app_letter_show', ['id' => $letter->getId()]);
        }

        if ($this->isCsrfTokenValid('delete' . $letter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($letter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_letter_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Borrow;
use App\Form\BorrowType;
use App\Repository\BorrowRepository;
use App\Repository\ItemRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/borrow')]
class BorrowController extends AbstractController
{
    #[Route('/', name: 'app_borrow_index', methods: ['GET'])]
    public function index(BorrowRepository $borrowRepository): Response
    {
        return $this->render('borrow/index.html.twig', [
            'borrows' => $borrowRepository->findAll(),
        ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ItemRepository $itemRepository
     * @return Response
     * Add a new borrow for an item
     */
    #[Route('/borrow/new/{id}', name: 'app_borrow_new_test', methods: ['GET', 'POST'])]
    public function new
    (
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ItemRepository $itemRepository
    ): Response
    {
        $item = $itemRepository->find($id);
        $borrow = new Borrow();


        if($request->isMethod('POST'))
        {
            if ($item && $item->getBorrow() === null) {
                $today = new DateTime();
                $endDate = $today->add(new DateInterval('P7D'));
                $borrow->setStartDate($today);
                $borrow->setEndDate($endDate);
                $borrow->setUser($this->getUser());
                $item->setBorrow($borrow);
            }
            else {
                $this->addFlash('error', "L'item à déjà été emprunter");
                return $this->redirectToRoute('app_item_index');
            }

            $entityManager->persist($borrow);
            $entityManager->persist($item);
            $entityManager->flush();

            $this->addFlash('success', 'Emprunter avec succès');
            return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('home/index.html.twig');
    }

    /**
     * @param Borrow $borrow
     * @return Response
     * Display a borrow by is ID
     */
    #[Route('/{id}', name: 'app_borrow_show', methods: ['GET'])]
    public function show(Borrow $borrow): Response
    {
        return $this->render('borrow/show.html.twig', [
            'borrow' => $borrow,
        ]);
    }

    /**
     * @param Request $request
     * @param Borrow $borrow
     * @param EntityManagerInterface $entityManager
     * @return Response
     * Edit a borrow
     */
    #[Route('/{id}/edit', name: 'app_borrow_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Borrow $borrow, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BorrowType::class, $borrow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('error', "L'emprunt à bien été éditer");
            return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('borrow/edit.html.twig', [
            'borrow' => $borrow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Borrow $borrow
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_borrow_delete', methods: ['POST'])]
    public function delete(Request $request, Borrow $borrow, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$borrow->getId(), $request->request->get('_token'))) {
            $entityManager->remove($borrow);
            $entityManager->flush();
        }
        $this->addFlash('success', "L'emprunt à bien été supprimer");
        return $this->redirectToRoute('app_borrow_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Collections;
use App\Form\CollectionType;
use App\Repository\CollectionsRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/collection')]
class CollectionsController extends AbstractController
{
    #[Route('/', name: 'app_collection_index', methods: ['GET'])]
    public function index(CollectionsRepository $collectionRepository): Response
    {
        return $this->render('collection/index.html.twig', [
            'collections' => $collectionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_collection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collection = new Collections();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collection);
            $entityManager->flush();

            return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/new.html.twig', [
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collection_show', methods: ['GET'])]
    public function show(Collections $collection): Response
    {
        return $this->render('collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collections $collection, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/edit.html.twig', [
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collection_delete', methods: ['POST'])]
    public function delete(Request $request, Collections $collection, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collection->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/collection/add', name: 'app_collections_additems', methods: ['POST', 'GET'])]
    public function addItems
    (Request $request,
     CollectionsRepository $collectionsRepository,
     EntityManagerInterface $em,
     ItemRepository $itemRepository
    ): Response
    {

        if ($request->isMethod('POST')) {
            $collection = $collectionsRepository->find($_POST['collections']);
            $item = $itemRepository->find($_POST['items']);

            if ($item->getCategory()->getId() === $collection->getCategory()->getId()){
                $collection->addItem($item);

                $em->persist($collection);

                $em->flush();
            }


        }

        return  $this->render('collection/addItem.html.twig', [
            'collections' => $collectionsRepository->findAll(),
            'items' => $itemRepository->findAll(),
        ]);

    }

    #[Route('/search/{id}', name:'app_collections_searchitems')]
    public function searchItems
    (
        Collections $collections,
        ItemRepository $itemRepository,
        Request $request
    ): Response
    {
        if ($request->isMethod('POST')) {
            $items = $itemRepository->searchItemByTitle($_POST['title'], $collections->getId());

           return $this->render('collection/searchResult.html.twig', [
                'items' => $items,
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}

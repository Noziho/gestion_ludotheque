<?php

namespace App\Controller;

use App\Entity\Collections;
use App\Form\CollectionType;
use App\Repository\CollectionsRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/collection')]
class CollectionsController extends AbstractController
{
    /**
     * @param CollectionsRepository $collectionRepository
     * @return Response
     * Display the main pages for all collections
     */
    #[Route('/', name: 'app_collection_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('collection/index.html.twig', [
            'collections' => $userRepository->find($this->getUser())->getCollections()
        ]);
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * Add a new Collection
     */
    #[Route('/new', name: 'app_collection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collection = new Collections();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setUser($this->getUser());
            $entityManager->persist($collection);
            $entityManager->flush();
            $this->addFlash('success', "La collection à bien été créer");
            return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/new.html.twig', [
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    /**
     * @param Collections $collection
     * @return Response
     * Show a collection by ID
     */
    #[Route('/{id}', name: 'app_collection_show', methods: ['GET'])]
    public function show(Collections $collection): Response
    {
        return $this->render('collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }

    /**
     * @param Request $request
     * @param Collections $collection
     * @param EntityManagerInterface $entityManager
     * @return Response
     * Edit a collection
     */
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

            $this->addFlash('success', 'La collection à bien été supprimer');
        }

        return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Request $request
     * @param CollectionsRepository $collectionsRepository
     * @param EntityManagerInterface $em
     * @param ItemRepository $itemRepository
     * @return Response
     * Add an item in a collection
     */
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
            if ($collection && $item){
                if ($item->getCategory()->getId() === $collection->getCategory()->getId()){
                    $collection->addItem($item);

                    $em->persist($collection);

                    $em->flush();
                    $this->addFlash('success', "L'item à bien été ajouter à votre collection");

                }
                else {
                    $this->addFlash('error', "Les catégories ne correspondent pas");
                }
                return  $this->render('collection/addItem.html.twig', [
                    'collections' => $collectionsRepository->findAll(),
                    'items' => $itemRepository->findAll(),
                ]);

            }
        }

        return  $this->render('collection/addItem.html.twig', [
            'collections' => $collectionsRepository->findAll(),
            'items' => $itemRepository->findAll(),
        ]);
    }

    /**
     * @param Collections $collections
     * @param ItemRepository $itemRepository
     * @param Request $request
     * @return Response
     * Search an item by a collection and is title
     */
    #[Route('/search/title/{id}', name:'app_collections_searchitems_by_title')]
    public function searchItemsByTitle
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

    /**
     * @param Collections $collections
     * @param ItemRepository $itemRepository
     * @param Request $request
     * @return Response
     * Search an item by a collection and is editor     */
    #[Route('/search/editor/{id}', name:'app_collections_searchitems_by_editor')]
    public function searchItemsByEditor
    (
        Collections $collections,
        ItemRepository $itemRepository,
        Request $request
    ): Response
    {
        if ($request->isMethod('POST')) {
            $items = $itemRepository->searchItemByEditor($_POST['editor'], $collections->getId());

            return $this->render('collection/searchResult.html.twig', [
                'items' => $items,
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}

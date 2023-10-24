<?php

namespace App\Form;

use App\Entity\Collections;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    public CategoryRepository $categoryRepository;
    public function __construct (CategoryRepository $categoryRepository)
    {
        return$this->categoryRepository = $categoryRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Jeux vidéos'  => $this->categoryRepository->find(1),
                    'Jeux de sociétés'  => $this->categoryRepository->find(2),
                    'Manga'  => $this->categoryRepository->find(3),
                    'Films'  => $this->categoryRepository->find(4),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collections::class,
        ]);
    }
}

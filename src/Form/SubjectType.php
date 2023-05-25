<?php

namespace App\Form;

use App\Entity\Subject;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,  [
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'
                ],
                'label_attr' => [
                    'class' => "mt-2 block text-gray-700 text-sm font-bold mb-2"
                ]
            ])
            ->add('use_tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => function ($tag) {
                    return ("#" . $tag->getName());
                },
                'attr' => [
                    'class' => "border-solid border-2 border-gray-100 rounded-md flex flex-wrap gap-2 max-h-1/5 p-2"
                ],
                'label_attr' => [
                    'class' => "mt-2 block text-gray-700 text-sm font-bold mb-2"
                ],
                'placeholder' => "Choisissez des tags",
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TagType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
            ])
            ->add('comment', CommentType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'
                ],
                'label_attr' => [
                    'class' => "mt-2 block text-gray-700 text-sm font-bold mb-2"
                ]
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => [
                    'class' => "bg-indigo-500 mt-2 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: kate
 * Date: 19.08.16
 * Time: 10:20
 */

namespace AppBundle\Form;


use AppBundle\Entity\Category;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostNewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cat) {
                    return $cat->createQueryBuilder('cat')->orderBy('cat.title', Criteria::ASC);
                },
                'choice_label' => 'title',
                'label' => 'Category',
                'attr' => [
                    'data-placeholder' => '- Category -',
                ],
                'constraints' => new NotBlank(['message' => 'Local Authority should not be blank.'])
            ])
            ->add('tags', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'class' => Tag::class,
                'choice_label' => 'title',
            ]);
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
    }
}
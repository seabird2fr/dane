<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use App\Service\ColorService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class QuestionType extends AbstractType
{
    private $translator;
    private $param;
    private $color;

    public function __construct(TranslatorInterface $translator, ParameterBagInterface $param, ColorService $color)
    {
        $this->translator = $translator;
        $this->param = $param;
        $this ->color= $color->getColorService();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['form_type']) {
            case 'student_questioning':
            case 'student_marking':
                $builder->add('text', TextareaType::class, array(
                    'label' => false,
                    'disabled' => true,
                ));
                $builder->add('answers', CollectionType::class, array(
                    'label' => false,
                    'entry_type' => AnswerType::class,
                    'entry_options' => array('label' => false, 'form_type' => $options['form_type']),
                ));

            // ajout seabird champ justification réponse de l'entité Question
             /*   $builder->add('justify_reponse',TextareaType::class, array(
                    'label' => 'Justifiez de votre réponse',
                    'required'   => true,
                    'attr' => ['minlength' => 20],
                )); */
                break;
            case 'teacher':
                $builder->add('text');

                $builder->add('color',ChoiceType::class, [
                                'choices'  => $this->color,
             'choice_attr' => function($choice, $key, $value) {
             // adds a class like attending_yes, attending_no, etc
            return ['class' => 'background_'.strtolower($key),'data-content'=>'<div style="width:100%;height:40px" class="background_'.strtolower($key).'"></div>'];
                                                        },
                ]);  //ajout seabird


                $builder->add('categories', EntityType::class, array(
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $er) {
                        return $er->createQueryBuilder('c')->andWhere('c.language = :language')->setParameter('language', $this->param->get('locale'))->orderBy('c.shortname', 'ASC');
                     },
                    'choice_label' => 'longname',
                    'multiple' => false
                ));
                $builder->add('answers', CollectionType::class, array(
                    'entry_type' => AnswerType::class,
                    'entry_options' => array(
                        'label' => false,
                        'form_type' => $options['form_type'],
                    ),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ));
                break;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'form_type' => 'student_questioning',
        ]);
    }
}

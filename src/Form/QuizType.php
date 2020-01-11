<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class QuizType extends AbstractType
{
    private $translator;
    private $param;

    public function __construct(TranslatorInterface $translator, ParameterBagInterface $param)
    {
        $this->translator = $translator;
        $this->param = $param;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('summary');
        $builder->add('number_of_questions');
        $builder->add('categories', EntityType::class, array(
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')->andWhere('c.language = :language')->setParameter('language', $this->param->get('locale'))->orderBy('c.shortname', 'ASC');
                 },
                'choice_label' => 'longname',
                'multiple' => true
            ));

        $builder->add('question', EntityType::class, array(
                'class' => question::class,
                'query_builder' => function (QuestionRepository $er) {
                    //return $er->createQueryBuilder('c');
                    return $er->createQueryBuilder('c')->join('c.categories','category')->addSelect('category')->orderBy('category.shortname','ASC');
                    //return $er->createQueryBuilder('c')->andWhere('c.color = :color')->setParameter('color', "jaune");
                    //return $er->createQueryBuilder('c')->join('c.categories','category')->addSelect('category')->where('category.id = :id')->setParameter('id',4) ;
                 },
                'choice_label' => function($question) {

                    //dump($question->getCategories());
                    $label='';
                    foreach ($question->getCategories() as  $category) {
                        
                        $label = $label.' '.$category->getShortname();

                    }

                    if ($question->getColor()==null) $question->setColor("black"); 
                    return $question->getText().' ~~~catégorie: ~~'.$label.' ~  <span class=couleurQuestion style=color:'.$question->getColor().'> ||</span>';
                },
                'multiple' => true,
                'expanded' => true, // ajout checkbox
            ));


        $builder->add('start_quiz_comment');
        $builder->add('show_result_question');
        $builder->add('result_quiz_comment');
        $builder->add('allow_anonymous_workout');
        $builder->add('active');
          // extrait texte de fin de quiz
        $builder->add('extrait_end');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'form_type' => 'student_questioning',
        ]);
    }
}

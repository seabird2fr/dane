<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends Controller
{
    /**
     * @Route("/", defaults={"page": "1"}, name="question_index", methods="GET|POST")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, methods="GET|POST", name="question_index_paginated")
     */
    public function index(int $page, QuestionRepository $questionRepository, CategoryRepository $categorie,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');


// on récupère toutes les categories disponibles
        $categories= $categorie->findAll();

// on récupère toutes les questions disponibles
        $questions = $questionRepository->myFindAll(); 


        if (count($categories)>0){    

            $choix_categorie['Toutes']='toutes';
            foreach ($categories as $value) 
            {

                $choix_categorie[$value->getShortname()]=$value->getId();

            }
            

// formulaire des filtres cumulatifs en auto submit ( catégorie 1, catégorie 2,  ....)
            $form = $this->createFormBuilder()
            ->add('categorie', ChoiceType::class, [
                'label' => 'Choisissez la categorie afin de filtrer',
                'group_by' =>'null',
                'choices'  => [
                    $choix_categorie
                ],
                'attr' => array(
                    'onchange' => 'submit()',
                ),                   
            ])
            ->add('categorie2', ChoiceType::class, [
                'label' => 'Choisissez la categorie ',
                'group_by' =>'null',
                'choices'  => [
                    $choix_categorie
                ],
                'attr' => array(
                    'onchange' => 'submit()',
                ),                   
            ])
            ->getForm();

        }
        else{

            $form = $this->createFormBuilder()
            ->add('categorie', ChoiceType::class, [
                'label' => 'Choisissez la categorie ',
                'group_by' =>'null',
                'choices'  => [
                    'Pas de categorie' => 'Pas de categorie'
                ],
                'attr' => array(
                    'onchange' => 'submit()',
                ),                   
            ])
            ->getForm();


        }

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
          

            if (($request->request->get('form')['categorie']!='toutes') || ($request->request->get('form')['categorie2']!='toutes'))
            {

            
                // on boucle sur toutes les questions dispos
                    foreach ($questions  as $question) 
                    {

                                foreach ($question->getCategories()  as $categorie) 
                                {
                                    $tabcategorie[]=$categorie->getId(); // on recup tous les id categories des questions sous forme d'un tableau
                                }
                                   
                                    // si une des catégorie est à 'toutes' , on rajoute dans le tableau précédent 'toutes'
                                 if (($request->request->get('form')['categorie']=='toutes')) $tabcategorie[]='toutes';
                                 if (($request->request->get('form')['categorie2']=='toutes')) $tabcategorie[]='toutes';

                                // on vérifie que les catégories choisies dans le formulaire sont dans le tableau 
                                if ((in_array($request->request->get('form')['categorie'],$tabcategorie) && in_array($request->request->get('form')['categorie2'],$tabcategorie)) || (in_array($request->request->get('form')['categorie'],$tabcategorie) && in_array('toutes',$tabcategorie)) && (in_array($request->request->get('form')['categorie2'],$tabcategorie) && in_array('toutes',$tabcategorie))) 
                                    $results[]=$question; // on récupère les bonnes questions associées aux catégories cumulées ( filtres)

                                $tabcategorie=[]; // on réinitialise  le tableau catégorie

                    }

            }
            else $results=$questionRepository->findAll($page);

        }
        else

        {

          $results=$questionRepository->findAll($page);
      }

if (empty($results)) $results=[];

      return $this->render('question/index.html.twig', [
        'questions' => $results,
        'form' => $form->createView(),
    ]);
  }

    /**
     * @Route("/new", name="question_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $question = $em->getRepository(Question::class)->create();

        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'teacher'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($question);

            foreach ($question->getAnswers() as $answer) {
                $em->persist($answer);
            }

            $em->flush();

            $this->addFlash('success', sprintf('Question #%s is created.', $question->getId()));

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_show", methods="GET")
     */
    public function show(Question $question): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('question/show.html.twig', ['question' => $question]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods="GET|POST")
     */
    public function edit(Request $request, Question $question, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $form = $this->createForm(QuestionType::class, $question, array('form_type'=>'teacher'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question->setUpdatedAt(new \DateTime());

            foreach ($question->getAnswers() as $answer) {
                $em->persist($answer);
            }

            $em->flush();

            $this->addFlash('success', sprintf('Question #%s mise à jour.', $question->getId()));

            return $this->redirectToRoute('question_index');
            //return $this->redirectToRoute('question_edit', ['id' => $question->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods="DELETE")
     */
    public function delete(Request $request, Question $question, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $em->remove($question);
            $em->flush();

            $this->addFlash('success', sprintf('Question #%s is deleted.', $question->getId()));
        }

        return $this->redirectToRoute('question_index');
    }
}

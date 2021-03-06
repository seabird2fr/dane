<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 * @ORM\Table(name="tbl_quiz")
* @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="Ce titre de quiz est déjà utilisé"
 * )
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_of_questions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    // /**
    //  * @var Category One or more category(ies) of this quiz.
    //  *
    //  * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="quizzes")
    //  * @ORM\JoinColumn()
    //  */
    // private $categories;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="quizzes")
     * @ORM\JoinTable(name="tbl_quiz_category")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workout", mappedBy="quiz", orphanRemoval=true)
     */
    private $workouts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $show_result_question;

    /**
     * @ORM\Column(type="boolean")
     */
    private $show_result_quiz;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="quizzes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    /**
     * @ORM\Column(type="boolean")
     */
    private $allow_anonymous_workout;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $result_quiz_comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $start_quiz_comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $extrait_end;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Question", inversedBy="quizzes")
     */
    private $question;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $access;


    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setActive(true);
        $this->setShowResultQuestion(false);
        $this->setShowResultQuiz(false);
        $this->setNumberOfQuestions(1);
        $this->categories = new ArrayCollection();
        $this->workouts = new ArrayCollection();
        $this->setAllowAnonymousWorkout(false);
        $this->question = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getNumberOfQuestions(): ?int
    {
        return $this->number_of_questions;
    }

    public function setNumberOfQuestions(int $number_of_questions): self
    {
        $this->number_of_questions = $number_of_questions;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Workout[]
     */
    public function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public function addWorkout(Workout $workout): self
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts[] = $workout;
            $workout->setQuiz($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->contains($workout)) {
            $this->workouts->removeElement($workout);
            // set the owning side to null (unless already changed)
            if ($workout->getQuiz() === $this) {
                $workout->setQuiz(null);
            }
        }

        return $this;
    }

    public function getShowResultQuestion(): ?bool
    {
        return $this->show_result_question;
    }

    public function setShowResultQuestion(bool $show_result_question): self
    {
        $this->show_result_question = $show_result_question;

        return $this;
    }

    public function getShowResultQuiz(): ?bool
    {
        return $this->show_result_quiz;
    }

    public function setShowResultQuiz(bool $show_result_quiz): self
    {
        $this->show_result_quiz = $show_result_quiz;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getAllowAnonymousWorkout(): ?bool
    {
        return $this->allow_anonymous_workout;
    }

    public function setAllowAnonymousWorkout(bool $allow_anonymous_workout): self
    {
        $this->allow_anonymous_workout = $allow_anonymous_workout;

        return $this;
    }

    public function getResultQuizComment(): ?string
    {
        return $this->result_quiz_comment;
    }

    public function setResultQuizComment(?string $result_quiz_comment): self
    {
        $this->result_quiz_comment = $result_quiz_comment;

        return $this;
    }

    public function getStartQuizComment(): ?string
    {
        return $this->start_quiz_comment;
    }

    public function setStartQuizComment(?string $start_quiz_comment): self
    {
        $this->start_quiz_comment = $start_quiz_comment;

        return $this;
    }

    public function getExtraitEnd(): ?string
    {
        return $this->extrait_end;
    }

    public function setExtraitEnd(?string $extrait_end): self
    {
        $this->extrait_end = $extrait_end;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->question->contains($question)) {
            $this->question[] = $question;
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->question->contains($question)) {
            $this->question->removeElement($question);
        }

        return $this;
    }

    public function getAccess(): ?bool
    {
        return $this->access;
    }

    public function setAccess(?bool $access): self
    {
        $this->access = $access;

        return $this;
    }

}

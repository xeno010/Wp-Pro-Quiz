<?php

class WpProQuiz_Helper_QuestionImportDatabase
{
    /**
     * @var WpProQuiz_Model_Question[]
     */
    private $questions;

    /**
     * WpProQuiz_Helper_QuestionImportDatabase constructor.
     *
     * @param WpProQuiz_Model_Question[] $questions
     */
    public function __construct($questions)
    {
        $this->questions = $questions;
    }

    public function insert($quizId)
    {
        $this->prepareQuestions($quizId);
        $this->insertToDatabase();
    }

    protected function insertToDatabase() {
        $mapper = new WpProQuiz_Model_QuestionMapper();

        foreach ($this->questions as $question) {
            $mapper->save($question);
        }
    }

    /**
     * @return WpProQuiz_Model_Category[]
     */
    protected function getCategories()
    {
        $mapper = new WpProQuiz_Model_CategoryMapper();

        return $mapper->fetchAll(WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION);
    }

    protected function prepareQuestions($quizId)
    {
        $categories = $this->getCategories();

        foreach ($this->questions as $question) {
            $question->setQuizId($quizId);

            $name = trim($question->getCategoryName());

            if (empty($name)) {
                continue;
            }

            if ($category = $this->findCategoryByName($categories, $name)) {
                $question->setCategoryId($category->getCategoryId());
            }
        }
    }

    /**
     * @param WpProQuiz_Model_Category[] $categories
     * @param string $name
     *
     * @return WpProQuiz_Model_Category
     */
    protected function findCategoryByName($categories, $name)
    {
        foreach ($categories as $category) {
            if ($name === $category->getCategoryName()) {
                return $category;
            }
        }

        return null;
    }
}

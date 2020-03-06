<?php

class WpProQuiz_Helper_JsonQuestionImporter implements WpProQuiz_Helper_QuestionImpoterInterface
{

    protected $json;

    public function __construct($content)
    {
        $this->json = $this->parseContent($content);
    }

    public function getQuestionPreview()
    {
        if (!$this->validate()) {
            return [];
        }

        return $this->getQuestionNames();
    }

    public function import($quizId)
    {
        if (!$this->validate()) {
            return false;
        }

        $models = $this->buildQuestionModels();

        $this->insertQuestionToDatabase($models, $quizId);

        return true;
    }

    protected function validate()
    {
        if (!is_array($this->json)) {
            return false;
        }

        if(!isset($this->json['export_version']) || $this->json['export_version'] != 1) {
            return false;
        }

        return true;
    }

    protected function getQuestionNames()
    {
        $names = [];

        foreach ($this->json['questions'] as $question) {
            $names[] = $question['title'];
        }

        return array_filter($names);
    }

    /**
     * @param resource|string $res
     *
     * @return WpProQuiz_Helper_JsonQuestionImporter|null
     */
    public static function factory($res)
    {
        $importer = null;

        if (is_resource($res)) {
            $res = stream_get_contents($res);
        }

        if (is_string($res) && !empty($res)) {
            $importer = new self($res);
        }

        return $importer;
    }

    protected function parseContent($content)
    {
        return @json_decode($content, true);
    }

    /**
     * @return WpProQuiz_Model_Question[] array
     */
    protected function buildQuestionModels()
    {
        $result = [];

        foreach ($this->getQuestionsArray() as $question) {
            $model = new WpProQuiz_Model_Question(array_diff_key($question, array_flip(['answer_data'])));

            $model->setAnswerData($this->buildAnswerModels($question['answer_data']));

            $result[] = $model;
        }

        return $result;
    }

    protected function buildAnswerModels($answerData)
    {
        $answers = [];

        foreach ($answerData as $answer) {
            $answers[] = new WpProQuiz_Model_AnswerTypes($answer);
        }

        return $answers;
    }

    protected function insertQuestionToDatabase($questions, $quizId)
    {
        $helper = new WpProQuiz_Helper_QuestionImportDatabase($questions);
        $helper->insert($quizId);
    }

    /**
     * @return array
     */
    protected function getQuestionsArray()
    {
        return isset($this->json['questions']) ? $this->json['questions'] : [];
    }
}

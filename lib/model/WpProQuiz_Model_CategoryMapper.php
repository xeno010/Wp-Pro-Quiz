<?php

class WpProQuiz_Model_CategoryMapper extends WpProQuiz_Model_Mapper
{

    /**
     * @param string $type
     *
     * @return WpProQuiz_Model_Category[]
     */
    public function fetchAll($type = WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION)
    {
        $type = $type == WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION ? $type : WpProQuiz_Model_Category::CATEGORY_TYPE_QUIZ;

        $r = array();

        $results = $this->_wpdb->get_results("SELECT * FROM {$this->_tableCategory} WHERE type = '" . $type . "'",
            ARRAY_A);

        foreach ($results as $row) {
            $r[] = new WpProQuiz_Model_Category($row);
        }

        return $r;
    }

    /**
     * @param $quizId
     * @return WpProQuiz_Model_Category[]
     */
    public function fetchByQuiz($quizId)
    {
        $r = array();

        $results = $this->_wpdb->get_results($this->_wpdb->prepare('
			SELECT 
				c.*
			FROM
				' . $this->_tableCategory . ' AS c
				RIGHT JOIN ' . $this->_tableQuestion . ' AS q
			        	ON (c.category_id = q.category_id AND c.type = %s)
			WHERE
				q.quiz_id = %d 
			GROUP BY
		          q.category_id
			ORDER BY
       			c.category_name
		', WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION, $quizId), ARRAY_A);

        foreach ($results as $row) {
            $r[] = new WpProQuiz_Model_Category($row);
        }

        return $r;
    }

    public function save(WpProQuiz_Model_Category $category)
    {
        $type = $category->getType();

        if ($category->getCategoryId() == 0) {
            $this->_wpdb->insert($this->_tableCategory, array(
                'category_name' => $category->getCategoryName(),
                'type' => empty($type) ? 'QUESTION' : $type
            ), array('%s', '%s'));
            $category->setCategoryId($this->_wpdb->insert_id);
        } else {
            $this->_wpdb->update(
                $this->_tableCategory,
                array('category_name' => $category->getCategoryName()),
                array('category_id' => $category->getCategoryId()),
                array('%s'),
                array('%d'));
        }

        return $category;
    }

    public function delete($categoryId)
    {
        $this->_wpdb->update($this->_tableQuestion, array('category_id' => 0), array('category_id' => $categoryId),
            array('%d'), array('%d'));
        $this->_wpdb->update($this->_tableMaster, array('category_id' => 0), array('category_id' => $categoryId),
            array('%d'), array('%d'));

        return $this->_wpdb->delete($this->_tableCategory, array('category_id' => $categoryId), array('%d'));
    }

    public function getCategoryArrayForImport($type = WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION)
    {
        $type = $type == WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION ? $type : WpProQuiz_Model_Category::CATEGORY_TYPE_QUIZ;

        $r = array();

        $results = $this->_wpdb->get_results("SELECT * FROM {$this->_tableCategory} WHERE type = '" . $type . "'",
            ARRAY_A);

        foreach ($results as $row) {
            $r[strtolower($row['category_name'])] = (int)$row['category_id'];
        }

        return $r;
    }
}
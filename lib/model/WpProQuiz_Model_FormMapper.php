<?php

class WpProQuiz_Model_FormMapper extends WpProQuiz_Model_Mapper
{

    public function deleteForm($formIds, $quizId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE FROM ' . $this->_tableForm . '
				WHERE
					form_id IN(' . implode(', ', array_map('intval', (array)$formIds)) . ') AND quiz_id = %d
			', $quizId)
        );
    }

    /**
     * @param WpProQuiz_Model_Form[] $forms
     */
    public function update($forms)
    {
        $values = $values2 = array();

        foreach ($forms as $form) {
            /* @var $form WpProQuiz_Model_Form */

            $data = array(
                $form->getFormId(),
                $form->getQuizId(),
                $form->getFieldname(),
                $form->getType(),
                (int)$form->isRequired(),
                $form->getSort(),
                (int)$form->isShowInStatistic()
            );

            if ($form->getData() === null) {
                $values[] = '(' . $this->_wpdb->prepare('%d, %d, %s, %d, %d, %d, %d', $data) . ')';
            } else {
                $data[] = @json_encode($form->getData());
                $values2[] = '(' . $this->_wpdb->prepare('%d, %d, %s, %d, %d, %d, %d, %s', $data) . ')';
            }
        }

        if (!empty($values)) {
            $this->_wpdb->query('
				REPLACE INTO ' . $this->_tableForm . '
					(form_id, quiz_id, fieldname, type, required, sort, show_in_statistic)
				VALUES ' . implode(', ', $values) . '
			');
        }

        if (!empty($values2)) {
            $this->_wpdb->query('
				REPLACE INTO ' . $this->_tableForm . '
					(form_id, quiz_id, fieldname, type, required, sort, show_in_statistic, data)
				VALUES ' . implode(', ', $values2) . '
			');
        }
    }

    /**
     * @param $quizId
     * @return WpProQuiz_Model_Form[]
     */
    public function fetch($quizId)
    {
        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare('
						SELECT * FROM ' . $this->_tableForm . ' WHERE quiz_id = %d ORDER BY sort', $quizId), ARRAY_A);
        $a = array();

        foreach ($results as $row) {
            $row['data'] = $row['data'] === null ? null : @json_decode($row['data'], true);

            $a[] = new WpProQuiz_Model_Form($row);
        }

        return $a;
    }
}
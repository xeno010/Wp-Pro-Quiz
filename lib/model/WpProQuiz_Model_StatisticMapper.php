<?php

class WpProQuiz_Model_StatisticMapper extends WpProQuiz_Model_Mapper
{

    public function fetchAllByRef($statisticRefId)
    {
        $a = array();

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                'SELECT
							*
						FROM
							' . $this->_tableStatistic . '
						WHERE
							statistic_ref_id = %d', $statisticRefId),
            ARRAY_A);

        foreach ($results as $row) {
            $a[] = new WpProQuiz_Model_Statistic($row);
        }

        return $a;
    }

    public function isStatisticByQuestionId($questionId)
    {
        return $this->_wpdb->get_var(
            $this->_wpdb->prepare(
                "SELECT
					COUNT(*) 
				FROM 
					{$this->_tableStatistic}
				WHERE
					question_id = %d",
                $questionId)
        );
    }

    /*
    public function save($s) {
        $values = array();


        if($s->getUserId()) {
            $refId = $this->_wpdb->get_var(
                $this->_wpdb->prepare('
                    SELECT statistic_ref_id
                    FROM '.$this->_tableStatisticRef.'
                    WHERE quiz_id = %d AND user_id = %d
                ', $quiz_id, $user_id)
            );

            if($refId === null) {
                $this->_wpdb->insert($this->_tableStatisticRef, array(
                    'quiz_id' => 0,
                    'user_id' => 0,
                    'create_time' => 0,
                    'is_old' => 1
                ), array(
                    '%d', '%d', '%d', '%d'
                ));
            }

        }


        foreach($s as $d) {
            $values[] = '( '.implode(', ', array(
                'quiz_id' => $d->getQuizId(),
                'question_id' => $d->getQuestionId(),
                'user_id' => $d->getUserId(),
                'correct_count' => $d->getCorrectCount(),
                'incorrect_count' => $d->getIncorrectCount(),
                'hint_count' => $d->getHintCount(),
                'points' => $d->getPoints()
            )).' )';
        }

        $this->_wpdb->query(
            'INSERT INTO
                '.$this->_tableStatistic.' (
                    quiz_id, question_id, user_id, correct_count, incorrect_count, hint_count, points
                )
            VALUES
                '.implode(', ', $values).'
              ON DUPLICATE KEY UPDATE
                correct_count =  correct_count + VALUES(correct_count),
                incorrect_count =  incorrect_count + VALUES(incorrect_count),
                hint_count =  hint_count + VALUES(hint_count),
                points =  points + VALUES(points)'
        );
    }
    */

    /*
    public function save($quizId, $userId, $array) {
        $ids = $this->_wpdb->get_col($this->_wpdb->prepare("SELECT id FROM {$this->_tableQuestion} WHERE quiz_id = %d", $quizId));

        $ak = array_keys($array);

        if(array_diff($ids, $ak) !== array_diff($ak, $ids))
            return false;

        $values = array();
        $globalValue = array(
            'quiz_id' => $quizId,
            'question_id' => 0,
            'user_id' => $userId,
            'correct_count' => 0,
            'incorrect_count' => 0,
            'hint_count' => 0,
            'correct_answer_count' => 0
        );

        foreach($array as $k => $v) {

            $value = $globalValue;

            $value['question_id'] = $k;

            if(isset($v['tip'])) {
                $value['hint_count'] = 1;
            }

            if($v['correct']) {
                $value['correct_count'] = 1;
            } else {
                $value['incorrect_count'] = 1;
            }

            $value['correct_answer_count'] = isset($v['correct_answer_count']) ? (int)$v['correct_answer_count'] : 0;

            $values[] = '('.implode(', ', $value).')';
        }

        $this->_wpdb->query(
            'INSERT INTO
                '.$this->_tableStatistic.' ('.implode(', ', array_keys($globalValue)).')
            VALUES
                '.implode(', ', $values).'
              ON DUPLICATE KEY UPDATE
                correct_count =  correct_count + VALUES(correct_count),
                incorrect_count =  incorrect_count + VALUES(incorrect_count),
                hint_count =  hint_count + VALUES(hint_count),
                correct_answer_count = correct_answer_count + VALUES(correct_answer_count)'
        );
    }
    */
}
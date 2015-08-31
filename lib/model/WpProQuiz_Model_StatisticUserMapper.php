<?php

class WpProQuiz_Model_StatisticUserMapper extends WpProQuiz_Model_Mapper
{

    public function fetchUserStatistic($refIdUserId, $quizId, $avg = false)
    {
        $where = $avg ? 'sf.user_id = %d' : 'sf.statistic_ref_id = %d';

        $result = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                "SELECT
					SUM(s.correct_count) AS correct_count,
					SUM(s.incorrect_count) AS incorrect_count,
					SUM(s.hint_count) AS hint_count,
					SUM(s.solved_count) AS solved_count,
					SUM(s.points) AS points,
					AVG(s.question_time) AS question_time,
					s.answer_data AS statistic_answer_data,
					q.id AS question_id,
					q.title AS question_name,
					q.answer_data AS question_answer_data,
					q.answer_type,
					SUM(q.points * (s.correct_count + s.incorrect_count)) AS g_points,
					c.category_id,
					c.category_name
				FROM
					{$this->_tableStatisticRef} AS sf
			        INNER JOIN {$this->_tableStatistic} AS s ON(s.statistic_ref_id = sf.statistic_ref_id)
			        INNER JOIN {$this->_tableQuestion} AS q ON(q.id = s.question_id)
			        LEFT JOIN {$this->_tableCategory} AS c ON(c.category_id = q.category_id)
				WHERE
					{$where} AND sf.quiz_id = %d
				GROUP BY
					s.question_id
				ORDER BY
					ISNULL(c.category_name), c.category_name, q.sort",
                $refIdUserId, $quizId), ARRAY_A);

        $r = array();

        foreach ($result as $row) {
            if (!$avg) {
                if ($row['statistic_answer_data'] !== null) {
                    $row['statistic_answer_data'] = json_decode($row['statistic_answer_data'], true);
                }
            } else {
                $row['statistic_answer_data'] = null;
                $row['question_answer_data'] = null;
            }

            $r[] = new WpProQuiz_Model_StatisticUser($row);
        }

        return $r;
    }
}
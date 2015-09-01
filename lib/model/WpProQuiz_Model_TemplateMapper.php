<?php

class WpProQuiz_Model_TemplateMapper extends WpProQuiz_Model_Mapper
{

    /**
     * @param WpProQuiz_Model_Template $template
     * @return WpProQuiz_Model_Template
     */
    public function save($template)
    {
        $this->_wpdb->replace($this->_tableTemplate, array(
            'template_id' => $template->getTemplateId(),
            'name' => $template->getName(),
            'type' => $template->getType(),
            'data' => @serialize($template->getData())
        ), array('%d', '%s', '%d', '%s'));

        $template->setTemplateId($this->getInsertId());

        return $template;
    }

    public function updateName($templateId, $name)
    {
        return $this->_wpdb->update($this->_tableTemplate, array(
            'name' => $name
        ), array(
            'template_id' => $templateId
        ), array('%s'), array('%d'));
    }

    public function delete($templateId)
    {
        return $this->_wpdb->delete($this->_tableTemplate, array('template_id' => $templateId), array('%d'));
    }

    /**
     * @param $type
     * @param bool|true $loadData
     * @return WpProQuiz_Model_Template[]
     */
    public function fetchAll($type, $loadData = true)
    {
        $r = array();

        $result = $this->_wpdb->get_results($this->_wpdb->prepare(
            "SELECT * FROM {$this->_tableTemplate} WHERE type = %d "
            , $type), ARRAY_A);

        foreach ($result as $row) {
            $data = $row['data'];

            unset($row['data']);

            $template = new WpProQuiz_Model_Template($row);

            if ($loadData && WpProQuiz_Helper_Until::saveUnserialize($data, $into)) {
                $template->setData($into);
            }

            $r[] = $template;
        }

        return $r;
    }

    public function fetchById($templateId, $loadData = true)
    {
        $row = $this->_wpdb->get_row($this->_wpdb->prepare(
            "SELECT * FROM {$this->_tableTemplate} WHERE template_id = %d "
            , $templateId), ARRAY_A);

        if ($row !== null) {
            $data = $row['data'];

            unset($row['data']);

            $template = new WpProQuiz_Model_Template($row);

            if ($loadData && WpProQuiz_Helper_Until::saveUnserialize($data, $into)) {
                $template->setData($into);
            }

            return $template;
        }

        return new WpProQuiz_Model_Template();
    }
}
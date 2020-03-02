<?php

class WpProQuiz_Model_Mapper
{
    /**
     * Wordpress Datenbank Object
     *
     * @var wpdb
     */
    protected $_wpdb;

    /**
     * @var string
     */
    protected $_prefix;

    /**
     * @var string
     */
    protected $_tableQuestion;
    protected $_tableMaster;
    protected $_tableLock;
    protected $_tableStatistic;
    protected $_tableToplist;
    protected $_tablePrerequisite;
    protected $_tableCategory;
    protected $_tableStatisticRef;
    protected $_tableForm;
    protected $_tableTemplate;


    function __construct()
    {
        global $wpdb;

        $this->_wpdb = $wpdb;
        $this->_prefix = $wpdb->prefix . 'wp_pro_quiz_';

        $this->_tableQuestion = $this->_prefix . 'question';
        $this->_tableMaster = $this->_prefix . 'master';
        $this->_tableLock = $this->_prefix . 'lock';
        $this->_tableStatistic = $this->_prefix . 'statistic';
        $this->_tableToplist = $this->_prefix . 'toplist';
        $this->_tablePrerequisite = $this->_prefix . 'prerequisite';
        $this->_tableCategory = $this->_prefix . 'category';
        $this->_tableStatisticRef = $this->_prefix . 'statistic_ref';
        $this->_tableForm = $this->_prefix . 'form';
        $this->_tableTemplate = $this->_prefix . 'template';
    }

    public function getInsertId()
    {
        return $this->_wpdb->insert_id;
    }

    /**
     * @param $result
     * @param string $key
     * @return array
     */
    protected function getUserIdFromResult($result, $key = 'user_id')
    {
        return array_filter(wp_list_pluck($result, $key));
    }

    /**
     * @param array $ids
     * @param array $fields
     *
     * @return WP_User[]
     */
    protected function fetchUsers($ids, $fields = ['user_login', 'display_name', 'ID'])
    {
        $user_data_raw = get_users([
            'fields' => $fields,
            'include' => $ids,
        ]);

        $user_data = [];

        foreach ($user_data_raw as $udr) {
            $user_data[$udr->ID] = $udr;
        }

        return $user_data;
    }
}

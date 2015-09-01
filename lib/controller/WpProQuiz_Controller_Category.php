<?php

class WpProQuiz_Controller_Category
{

    public static function ajaxAddCategory($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $categoryMapper = new WpProQuiz_Model_CategoryMapper();

        $category = new WpProQuiz_Model_Category($data);

        $categoryMapper->save($category);

        return json_encode(array(
            'categoryId' => $category->getCategoryId(),
            'categoryName' => $category->getCategoryName()
        ));
    }

    public static function ajaxEditCategory($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $categoryMapper = new WpProQuiz_Model_CategoryMapper();

        $category = new WpProQuiz_Model_Category($data);

        $categoryMapper->save($category);

        return json_encode(array());
    }

    public static function ajaxDeleteCategory($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $categoryMapper = new WpProQuiz_Model_CategoryMapper();

        $category = new WpProQuiz_Model_Category($data);

        $categoryMapper->delete($category->getCategoryId());

        return json_encode(array());
    }
}
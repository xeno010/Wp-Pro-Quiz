<?php

/**
 * @property WpProQuiz_Model_Quiz[]  quizItems
 * @property int quizCount
 * @property WpProQuiz_Model_Category[] categoryItems
 * @property int perPage
 */
class WpProQuiz_View_QuizOverall extends WpProQuiz_View_View
{

    public function show()
    {
        ?>
        <style>
            .wpProQuiz_exportList ul, .wpProQuiz_setQuizCategoryList ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .wpProQuiz_exportList li, .wpProQuiz_setQuizCategoryList li {
                float: left;
                padding: 3px;
                border: 1px solid #B3B3B3;
                margin-right: 5px;
                background-color: #F3F3F3;
            }

            .wpProQuiz_exportList, .wpProQuiz_importList, .wpProQuiz_setQuizCategoryList {
                padding: 20px;
                background-color: rgb(223, 238, 255);
                border: 1px dotted;
                margin-top: 10px;
                /*display: none;*/
            }

            .column-shortcode {
                width: 100px;
            }

            .column-shortcode_leaderboard {
                width: 160px;
            }

            @media screen and (max-width: 782px) {
                .wpProQuiz_InfoBar {
                    display: none;
                }
            }

            #wpProQuiz_tab_donat {
                float: right;
                height: 28px;
                margin: 0 0 0 6px;
                border: 1px solid #ddd;
                border-top: none;
                box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
                background: #FFDB94;
            }

            #wpProQuiz_tab_donat > a {
                color: #3A3A3A !important;
                font-weight: bold !important;
            }

            #wpProQuiz_tab_donat > a:after{
                content: '' !important;
                padding: 0 5px 0 5px !important;
            }

        </style>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                function initGlobal() {
                    var isEmpty = function (str) {
                        str = $.trim(str);
                        return (!str || 0 === str.length);
                    };

                    var ajaxPost = function (func, data, success) {
                        var d = {
                            action: 'wp_pro_quiz_admin_ajax',
                            func: func,
                            data: data
                        };

                        $.post(ajaxurl, d, success, 'json');
                    };

                    var $setCategoryBox = $('#wpProQuiz_setQuizCategoryList_box > div');
                    var $categorySelect = $setCategoryBox.find('[name="category"]');

                    $categorySelect.change(function () {
                        $setCategoryBox.find('#categoryAddBox').toggle($(this).val() == "-1");
                    }).change();

                    $setCategoryBox.find('#categoryAddBtn').click(function () {
                        var name = $.trim($setCategoryBox.find('input[name="categoryAdd"]').val());

                        if (isEmpty(name)) {
                            return;
                        }

                        var data = {
                            categoryName: name,
                            type: 'quiz'
                        };

                        ajaxPost('categoryAdd', data, function (json) {
                            if (json.err) {
                                $('#categoryMsgBox').text(json.err).show('fast').delay(2000).hide('fast');
                                return;
                            }

                            var $option = $(document.createElement('option'))
                                .val(json.categoryId)
                                .text(json.categoryName)
                                .attr('selected', 'selected');

                            $categorySelect.append($option).change();

                        });
                    });

                    $setCategoryBox.find('#setCategoriesStart').click(function () {
                        var items = getCheckedItems();

                        if (!items || !items.length) {
                            alert(wpProQuizLocalize.no_selected_quiz);

                            return false;
                        }

                        var data = {
                            categoryId: $categorySelect.val(),
                            quizIds: items.map(function (i) {
                                return i.ID;
                            })
                        };

                        $('#ajaxLoad').show();

                        ajaxPost('setQuizMultipleCategories', data, function (json) {
                            location.reload();
                        });
                    });

                    $('.wpProQuiz_import').click(function () {
                        showWpProQuizModalBox('', 'wpProQuiz_importList_box');

                        return false;
                    });

                    return true;
                }

                initGlobal();

                function showWpProQuizModalBox(title, id) {
                    var width = Math.min($('.wpProQuiz_quizOverall').width() - 50, 600);
                    var a = '#TB_inline?width=' + width + '&inlineId=' + id;

                    tb_show(title, a, false);
                }

                function getCheckedItems() {
                    var items = $('[name="quiz[]"]:checked').map(function (i) {
                        var $this = $(this);
                        var $tr = $this.parents('tr');

                        var item = {
                            ID: $this.val(),
                            name: $.trim($tr.find('.name .row-title').text())
                        };

                        return item;
                    }).get();

                    return items;
                }

                function handleExportAction() {
                    var items = getCheckedItems();

                    if (!items || !items.length)
                        return false;

                    var $exportBox = $('.wpProQuiz_exportList');
                    var $hiddenBox = $exportBox.find('#exportHidden').empty();
                    var $ulBox = $exportBox.find('ul').empty();

                    $.each(items, function (i, v) {
                        $ulBox.append(
                            $('<li>').text(v.name)
                        );

                        $hiddenBox.append(
                            $('<input type="hidden" name="exportIds[]">').val(v.ID)
                        );
                    });

                    showWpProQuizModalBox('', 'wpProQuiz_exportList_box');

                    return true;
                }

                function handleSetCategoryAction() {
                    var items = getCheckedItems();

                    if (!items || !items.length)
                        return false;

                    var $setCategoryBox = $('.wpProQuiz_setQuizCategoryList');
                    var $hiddenBox = $setCategoryBox.find('#setCategoryHidden').empty();
                    var $ulBox = $setCategoryBox.find('ul').empty();

                    $.each(items, function (i, v) {
                        $ulBox.append(
                            $('<li>').text(v.name)
                        );

                        $hiddenBox.append(
                            $('<input type="hidden" name="exportIds[]">').val(v.ID)
                        );
                    });

                    showWpProQuizModalBox('', 'wpProQuiz_setQuizCategoryList_box');

                    return true;
                }

                function handleDeleteAction() {
                    var items = getCheckedItems();
                    var $form = $('#deleteForm').empty();

                    $.each(items, function (i, v) {
                        $form.append(
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'ids[]',
                                value: v.ID
                            })
                        );
                    });

                    $form.submit();
                }

                function handleAction(action) {
                    switch (action) {
                        case 'export':
                            handleExportAction();
                            return false;
                        case 'set_category':
                            handleSetCategoryAction();
                            return false;
                        case 'delete':
                            handleDeleteAction();
                            return false;
                    }

                    return true;
                }

                $('#doaction').click(function () {
                    return handleAction($('[name="action"]').val());
                });

                $('#doaction2').click(function () {
                    return handleAction($('[name="action2"]').val());
                });

                $('.wpProQuiz_delete').click(function (e) {
                    var b = confirm(wpProQuizLocalize.delete_msg);

                    if (!b) {
                        e.preventDefault();
                        return false;
                    }

                    return true;
                });

                $('#screen-meta-links').append($('#wpProQuiz_tab_donat').show());

            });
        </script>

        <?php
        add_thickbox();

        $this->showImportListBox();
        $this->showExportListBox();
        $this->showSetQuizCategoryListBox();
        ?>

        <div id="wpProQuiz_tab_donat" style="display: none;" class="hide-if-no-js screen-meta-toggle">
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KCZPNURT6RYXY" class="button show-settings" target="_blank"><?php _e('Donate', 'wp-pro-quiz'); ?></a>
        </div>

        <div class="wrap wpProQuiz_quizOverall" style="">
            <h2>
                <?php _e('Quiz overview', 'wp-pro-quiz'); ?>
                <?php if (current_user_can('wpProQuiz_add_quiz')) { ?>
                    <a class="add-new-h2" href="admin.php?page=wpProQuiz&action=addEdit"><?php echo __('Add quiz',
                            'wp-pro-quiz'); ?></a>
                <?php }
                if (current_user_can('wpProQuiz_import')) { ?>
                    <a class="add-new-h2 wpProQuiz_import" href="#"><?php echo __('Import', 'wp-pro-quiz'); ?></a>
                <?php } ?>
            </h2>

            <form action="?page=wpProQuiz&action=deleteMulti" method="post" style="display: none;" id="deleteForm">

            </form>

            <div>
                <div class="wpProQuiz_InfoBar" style="display: none; margin-top:-36px; float: right;">

                    <div style="background-color: #FFFBCC; padding: 6px; border: 1px solid #E6DB55; float: left;">
                        <strong><?php _e('You need special WP-Pro-Quiz modification for your website?',
                                'wp-pro-quiz'); ?></strong><br>
                        <a class="button-primary" href="admin.php?page=wpProQuiz&module=info_adaptation"
                           style="margin-top: 5px;"><?php _e('Learn more', 'wp-pro-quiz'); ?></a>
                    </div>

                    <div
                        style="background-color: #FFFBCC; padding: 3px 35px; border: 1px solid #E6DB55; float: left; margin-left: 10px;">
                        <span style="font-weight: bold; margin-left: 15px;"><?php _e('WP-Pro-Quiz',
                                'wp-pro-quiz'); ?></span>

                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="BF9JT56N7FAQG">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"
                                   border="0" name="submit"
                                   alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                            <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1"
                                 height="1">
                        </form>
                    </div>

                    <div style="clear: both;"></div>
                </div>
                <div style="clear: both;"></div>
            </div>

            <p style="margin-bottom: 0; display: none;">
                <?php if (current_user_can('wpProQuiz_add_quiz')) { ?>
                    <a class="button-secondary" href="admin.php?page=wpProQuiz&action=addEdit"><?php echo __('Add quiz',
                            'wp-pro-quiz'); ?></a>
                <?php }
                if (current_user_can('wpProQuiz_import')) { ?>
                    <a class="button-secondary wpProQuiz_import" href="#"><?php echo __('Import', 'wp-pro-quiz'); ?></a>
                <?php } ?>
            </p>

            <form action="" method="get">
                <input type="hidden" name="page" value="wpProQuiz">

                <?php
                $overviewTable = $this->getTable();
                $overviewTable->prepare_items();

                ?>
                <p class="search-box">
                    <?php $overviewTable->search_box(__('Search'), 'search_id'); ?>

                </p>

                <?php
                $overviewTable->display();
                ?>

            </form>

        </div>

        <?php
    }

    /**
     * @return WpProQuiz_View_QuizOverallTable
     */
    protected function getTable()
    {
        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        return new WpProQuiz_View_QuizOverallTable($this->quizItems, $this->quizCount, $this->categoryItems,
            $this->perPage);
    }

    protected function showImportListBox()
    {
        ?>

        <div id="wpProQuiz_importList_box" style="display: none;">
            <div class="wpProQuiz_importList">
                <form action="admin.php?page=wpProQuiz&module=importExport&action=import" method="POST"
                      enctype="multipart/form-data">
                    <h3 style="margin-top: 0;"><?php _e('Import', 'wp-pro-quiz'); ?></h3>

                    <p><?php _e('Import only *.wpq or *.xml files from known and trusted sources.',
                            'wp-pro-quiz'); ?></p>

                    <div style="margin-bottom: 10px">
                        <?php
                        $maxUpload = (int)(ini_get('upload_max_filesize'));
                        $maxPost = (int)(ini_get('post_max_size'));
                        $memoryLimit = (int)(ini_get('memory_limit'));
                        $uploadMB = min($maxUpload, $maxPost, $memoryLimit);
                        ?>
                        <input type="file" name="import" accept=".wpq,.xml"
                               required="required"> <?php printf(__('Maximal %d MiB', 'wp-pro-quiz'), $uploadMB); ?>
                    </div>
                    <input class="button-primary" name="exportStart" id="exportStart"
                           value="<?php _e('Start import', 'wp-pro-quiz'); ?>" type="submit">
                </form>
            </div>
        </div>

        <?php
    }

    protected function showExportListBox()
    {
        ?>

        <div id="wpProQuiz_exportList_box" style="display: none;">
            <div class="wpProQuiz_exportList">
                <form action="admin.php?page=wpProQuiz&module=importExport&action=export&noheader=true" method="POST">
                    <h3 style="margin-top: 0;"><?php _e('Export', 'wp-pro-quiz'); ?></h3>

                    <p><?php echo __('Choose the respective question, which you would like to export and press on "Start export"',
                            'wp-pro-quiz'); ?></p>
                    <ul></ul>
                    <div style="clear: both; margin-bottom: 10px;"></div>
                    <div id="exportHidden"></div>
                    <div style="margin-bottom: 15px;">
                        <?php _e('Format:'); ?>
                        <label><input type="radio" name="exportType" value="wpq"
                                      checked="checked"> <?php _e('*.wpq'); ?></label>
                        <?php _e('or'); ?>
                        <label><input type="radio" name="exportType" value="xml"> <?php _e('*.xml'); ?></label>
                    </div>
                    <input class="button-primary" name="exportStart" id="exportStart"
                           value="<?php echo __('Start export', 'wp-pro-quiz'); ?>" type="submit">
                </form>
            </div>
        </div>

        <?php
    }

    protected function showSetQuizCategoryListBox()
    {
        ?>

        <div id="wpProQuiz_setQuizCategoryList_box" style="display: none;">
            <div class="wpProQuiz_setQuizCategoryList">
                <form action="#" method="POST">
                    <h3 style="margin-top: 0;"><?php _e('Set Quiz Categories', 'wp-pro-quiz'); ?></h3>

                    <p><?php _e('Sets multiple quiz categories ', 'wp-pro-quiz'); ?></p>

                    <div style="margin-bottom: 10px">
                    </div>
                    <ul></ul>
                    <div style="clear: both; margin-bottom: 10px;"></div>
                    <div id="setCategoryHidden"></div>

                    <div style="margin-bottom: 10px;">
                        <p class="description">
                            <?php _e('You can assign classify category for a quiz.', 'wp-pro-quiz'); ?>
                        </p>

                        <p class="description">
                            <?php _e('You can manage categories in global settings.', 'wp-pro-quiz'); ?>
                        </p>

                        <div>
                            <select name="category">
                                <option value="-1">--- <?php _e('Create new category', 'wp-pro-quiz'); ?> ----</option>
                                <option value="0" selected="selected">--- <?php _e('No category', 'wp-pro-quiz'); ?>
                                    ---
                                </option>
                                <?php
                                foreach ($this->categoryItems as $cat) {
                                    echo '<option value="' . $cat->getCategoryId() . '">' . $cat->getCategoryName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="display: none;" id="categoryAddBox">
                            <h4><?php _e('Create new category', 'wp-pro-quiz'); ?></h4>
                            <input type="text" name="categoryAdd" value="">
                            <input type="button" class="button-secondary" name="" id="categoryAddBtn"
                                   value="<?php _e('Create', 'wp-pro-quiz'); ?>">
                        </div>
                        <div id="categoryMsgBox"
                             style="display:none; padding: 5px; border: 1px solid rgb(160, 160, 160); background-color: rgb(255, 255, 168); font-weight: bold; margin: 5px; ">
                            Kategorie gespeichert
                        </div>
                    </div>

                    <input class="button-primary" name="setCategoriesStart" id="setCategoriesStart"
                           value="<?php _e('Save', 'wp-pro-quiz'); ?>" type="button">
                    <img id="ajaxLoad" style="display: none;" alt="load"
                         src="data:image/gif;base64,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">
                </form>
            </div>
        </div>

        <?php
    }
}
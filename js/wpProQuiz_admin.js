jQuery(document).ready(function($) {
	$.fn.wpProQuiz_preview = function() {
		var methods = {
				openPreview: function(obj) {
					window.open($(obj).attr('href'), 'wpProQuizPreview', 'width=900,height=900');
				}
		};

		var init = function() {
			$('.wpProQuiz_prview').click(function(e) {
				methods.openPreview(this);
				e.preventDefault();
			});
		};

		init();
	};
	
	$.fn.wpProQuiz_questionEdit = function() {

		var setup = function() {
			if($('input[name="answerType"][checked="checked"]').size() < 1) {
				$('input[name="answerType"][value="single"]').attr({'checked': 'checked'});
			}
			
			$('input[name="answerType"]:checked').click();
			$('#wpProQuiz_correctSameText').change();
			$('#wpProQuiz_tip').change();
		};

		var formListener = {
			setup: function() {
				$('input[name="answerType"]').click(function(e) {
					$('.answer_felder').children().css('display', 'none');
	
					switch (this.value) {
					case 'single':
						formListener.displaySingle('radio');
						break;
					case 'multiple':
						formListener.displaySingle('checkbox');
						break;
					case 'free_answer':
						formListener.displayFreeAnswer();
						break;
					case 'sort_answer':
						formListener.displaySortAnswer();
						break;
					}
				});

				$('.addAnswer').click(function(e) {
					formListener.addAnswer(this);
				});

				$('.deleteAnswer').click(function(e) {
					formListener.deleteAnswer(this);
				});

				$('#saveQuestion').click(function(e) {
					return validate();
				});

				$('.sort_answer ul, .classic_answer ul').sortable({
					handle: '.wpProQuiz_move',
					update: function(event, ui) {
						formListener.setValueClassicAnswer();
					}
				});
				
				$('#wpProQuiz_correctSameText').change(function() {
					if(this.checked)
						$('#wpProQuiz_incorrectMassageBox').hide();
					else
						$('#wpProQuiz_incorrectMassageBox').show();
				});
				
				$('#wpProQuiz_tip').change(function(e) {
					if(this.checked)
						$('#wpProQuiz_tipBox').show();
					else
						$('#wpProQuiz_tipBox').hide();
				});
				
			},

			displaySingle: function(type) {
				$('.classic_answer').find('input[name="answerJson[classic_answer][correct][]"]').each(function() {
					 $("<input type=" + type + " />").attr({ name: this.name, value: this.value, checked: this.checked}).insertBefore(this);
				}).remove();

				$('.classic_answer').css('display', 'block');
			},

			displayFreeAnswer: function() {
				$('.free_answer').css('display', 'block');
			},

			displaySortAnswer: function() {
				$('.sort_answer').css('display', 'block');
			},

			addAnswer: function(obj) {
				$(obj).siblings('ul').children().first()
						.clone().css('display', 'block')
						.appendTo($(obj).siblings('ul'));

				formListener.setValueClassicAnswer();
				
				$('.deleteAnswer').click(function(e) {
					formListener.deleteAnswer(this);
				});
			},

			deleteAnswer: function(obj) {
				$(obj).parent('li').remove();

				formListener.setValueClassicAnswer();
			},

			setValueClassicAnswer: function() {
				i = 0;
				$('input[name="answerJson[classic_answer][correct][]"]').each(function() {
					this.value = i++;
				});
			}
		};

		var validate = function () {
			var question = tinymce.editors.question.getContent();
			var type = $('input[name="answerType"]:checked');
			
			if(isEmpty(question)) {
				alert(wpProQuizLocalize.no_question_msg);
				return false;
			}

			if(type.val() == 'single' || type.val() == 'multiple') {
				var findChecked = true;
				if($('input[name="answerJson[classic_answer][correct][]"]:checked').each(function() {
					if($.trim($(this).parent().siblings('textarea').val()) != '')
						findChecked &= true;
					else 
						findChecked = false;
				})
				.size() < 1) {
					alert(wpProQuizLocalize.no_correct_msg);
					return false;
				}

				if(!findChecked) {
					alert(wpProQuizLocalize.no_answer_msg);
					return false;
				}
			} else if(type.val() == 'sort_answer') {
				var findChecked = false;
				$('textarea[name="answerJson[answer_sort][answer][]"]').each(function() {
					if(isEmpty($(this).val())) {
						findChecked |= false;
					} else {
						findChecked = true;
					}
				});
				
				if(!findChecked) {
					alert(wpProQuizLocalize.no_answer_msg);
					return false;
				}
			}

			return true;
			
		};

		var isEmpty = function(str) {
			return (!str || 0 === str.length);
		};
	
		formListener.setup();
		setup();
	};
	
	$.fn.wpProQuiz_questionOverall = function() {

		var methode = {
			saveSort: function() {

				var data = {
					action: 'wp_pro_quiz_update_sort',
					sort: methode.parseSortArray()
				};

				var location = window.location.pathname + window.location.search;
				var url = location.replace('admin.php', 'admin-ajax.php') + '&action=save_sort';
				
				$.post(url, data, function(response) {
					console.debug(response);
					$('#sortMsg').show(400).delay(1000).hide(400);
				});
			},

			parseSortArray: function() {
				var array = new Array();
				
				$('tbody tr').each(function() {
					array.push(this.id.replace('wpProQuiz_questionId_', ''));
				});
				
				return array;
			},
			
			sortUpdate: function(e, ui) {
				$('.wpProQuiz_questionOverall tbody').children().each(function() {
					$t = $(this).children().first().text($(this).index() + 1);
				});
			}
		};
		
		var init = function() {
			$('.wp-list-table tbody').sortable({ handle: '.wpProQuiz_move', update: methode.sortUpdate });

			$('.wpProQuiz_delete').click(function(e) {
				var b = confirm(wpProQuizLocalize.delete_msg);

				if(!b) {
					e.preventDefault();
					return false;
				}

				return true;
			});

			$('#wpProQuiz_saveSort').click(function(e) {
				e.preventDefault();
				methode.saveSort();
			});

			console.debug($);
		};

		init();
	};
	
	$.fn.wpProQuiz_quizEdit = function() {
		var init = function() {
			$('#statistics_on').change(function() {
				if(this.checked) {
					$('#statistics_ip_lock_tr').show();
				} else {
					$('#statistics_ip_lock_tr').hide();
				}
			});
			
			$('#statistics_on').change();
		};
		
		init();
	};

	if($('.wpProQuiz_quizOverall').length)
		$('.wpProQuiz_quizOverall').wpProQuiz_preview();
	
	if($('.wpProQuiz_quizEdit').length)
		$('.wpProQuiz_quizEdit').wpProQuiz_quizEdit();
	
	if($('.wpProQuiz_questionEdit').length)
		$('.wpProQuiz_questionEdit').wpProQuiz_questionEdit();
	
	if($('.wpProQuiz_questionOverall').length)
		$('.wpProQuiz_questionOverall').wpProQuiz_questionOverall();
});
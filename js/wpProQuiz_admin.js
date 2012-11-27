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
	
	$.fn.wpProQuiz_quizOverall = function() {
		
		var methods = {
			changeExport: function(input) {
				$input = $(input);
				$export = $('.wpProQuiz_exportList');
				$ul = $export.find('ul').first();
				$export.find('li').remove();
				
				$('input[name="exportItems"]').each(function() {
					$this = $(this);

					if(this.checked) {
						var text = $this.parent().parent().find('.wpProQuiz_quizName').text();
						$('<li>' + text + '</li>').appendTo($ul);							
					}
				});
			},
			
			startExport: function() {
				$ele = $('input[name="exportItems"]:checked');
				
				if($ele.length < 1) {
					alert(wpProQuizLocalize.no_selected_quiz);
					return false;
				}
				
				$hidden = $('#exportHidden');
				
				$hidden.html('');
				
				$('input[name="exportItems"]').each(function() {
					$this = $(this);
					
					if(this.checked) {
						$('<input type="hidden" value="'+ this.value +'" name="exportIds[]">').appendTo($hidden);						
					}
				});
				
				return true;
			}
		};
		
		var init = function() {
			$('.wpProQuiz_delete').click(function(e) {
				var b = confirm(wpProQuizLocalize.delete_msg);

				if(!b) {
					e.preventDefault();
					return false;
				}

				return true;
			});
			
			$('.wpProQuiz_import').click(function(e) {
				e.preventDefault();
				$('.wpProQuiz_importList').toggle('fast');
				
				$('.wpProQuiz_exportList').hide();
				$('.wpProQuiz_exportCheck').hide();
				
			});
			
			$('.wpProQuiz_export').click(function(e) {
				e.preventDefault();
				
				$('.wpProQuiz_exportList').toggle('fast');
				$('.wpProQuiz_exportCheck').toggle('fast');
				$('.wpProQuiz_importList').hide();
			});
			
			$('input[name="exportItems"]').change(function() {
				methods.changeExport(this);
			});
			
			$('input[name="exportItemsAll"]').change(function() {
				var $input = $('input[name="exportItems"]');
				if(this.checked)
					$input.attr('checked', true);
				else
					$input.attr('checked', false);
				
				$input.change();
			});
			
			$('#exportStart').click(function(e) {

				if(!methods.startExport())
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
					case 'matrix_sort_answer':
						formListener.displayMatrixSortAnswer();
						break;
					case 'cloze_answer':
						formListener.displayClozeAnswer();
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

				$('.sort_answer ul, .classic_answer ul, .matrix_sort_answer ul').sortable({
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
			
			displayMatrixSortAnswer: function() {
				$('.matrix_sort_answer').show();
			},
			
			displayClozeAnswer: function() {
				$('.cloze_answer').show();
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
				var i = 0;
				$('input[name="answerJson[classic_answer][correct][]"]').each(function() {
					this.value = i++;
				});
				
				i = 0;
				$('input[name="answerJson[answer_matrix_sort][sort_string_html][]"]').each(function() {
					this.value = i++;
				});
				
				i = 0;
				$('input[name="answerJson[answer_matrix_sort][answer_html][]"]').each(function() {
					this.value = i++;
				});
			}
		};

		var validate = function () {
			var question = tinymce.editors.question.getContent();
			var type = $('input[name="answerType"]:checked');
			var $points = $('input[name="points"]');
			
			if(isNaN($points.val()) || $points.val() < 1) {
				alert(wpProQuizLocalize.no_nummber_points);
				$points.focus();
				return false;
			}
			
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
			} else if(type.val() == 'matrix_sort_answer') {
				var findChecked = false;
				$('textarea[name="answerJson[answer_matrix_sort][answer][]"]').each(function() {
					if(isEmpty($(this).val())) {
						findChecked |= false;
					} else {
						
						var $sortString = $(this).parent().parent().find('textarea[name="answerJson[answer_matrix_sort][sort_string][]"]');
						
						if(isEmpty($sortString.val())) {
							findChecked |= false;
						} else {
							findChecked = true;
						}
					}
				});
				
				if(!findChecked) {
					alert(wpProQuizLocalize.no_answer_msg);
					return false;
				}
			} else if(type.val() == 'cloze_answer') {
				var clozeText = tinymce.editors.cloze.getContent();
				
				if(isEmpty(clozeText)) {
					alert(wpProQuizLocalize.no_answer_msg);
					return false;
				}
			}

			return true;
			
		};

		var isEmpty = function(str) {
			str = $.trim(str);
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
		
		var methode = {
			addResult: function() {
				$('#resultList').children().each(function() {
					if($(this).css('display') == 'none') {
						
						var $this = $(this);
						var id = $this.find('textarea[name="resultTextGrade[text][]"]').attr('id');

						$this.find('input[name="resultTextGrade[prozent][]"]').val('0');
						$this.find('input[name="resultTextGrade[activ][]"]').val('1').keyup();
						
						tinymce.editors[id].setContent('');
						
						tinyMCE.execCommand('mceRemoveControl', false, id);
						
						$this.parent().children(':visible').last().after($this);
						
						tinyMCE.execCommand('mceAddControl', false, id);
						
						$(this).show();
						
						return false;
					}
				});
			},
			
			deleteResult: function(e) {
				$(e).parent().parent().hide();
				$(e).siblings('input[name="resultTextGrade[activ][]"]').val('0');
			},
			
			changeResult: function(e) {
				var $this = $(e);

				if(methode.validResultInput($this.val())) {
					$this.siblings('.resultProzent').text($this.val());
					$this.removeAttr('style');
					return true;
				}
				
				$this.css('background-color', '#FF9696');
				
				return false;
			},
			
			validResultInput: function(input) {
				
				if(isEmpty(input))
					return false;
				
				input = input.replace(/\,/, '.');

				if(!isNaN(input) && Number(input) <= 100 && Number(input) >= 0) {
					if(input.match(/\./) != null)
						return input.split('.')[1].length < 3;
					
					return true;
				}
				
				return false;
			},
			
			validInput: function() {
				if(isEmpty($('#wpProQuiz_title').val())) {
					alert(wpProQuizLocalize.no_title_msg);
					return false;
				}
				
				if(isEmpty(tinymce.editors.text.getContent())) {
					alert(wpProQuizLocalize.no_quiz_start_msg);
					return false;
				}
				
				if($('#wpProQuiz_resultGradeEnabled:checked').length) {
					var rCheck = true;
					console.debug("hier");
					$('#resultList').children().each(function() {
						if($(this).is(':visible')) {
							if(!methode.validResultInput($(this).find('input[name="resultTextGrade[prozent][]"]').val())) {
								rCheck = false;
								return false;
							}
						}
					});
					
					if(!rCheck) {
						alert(wpProQuizLocalize.fail_grade_result);
						return false;
					}
				}
				
				return true;
			}
			
		};
		
		var isEmpty = function(str) {
			str = $.trim(str);
			return (!str || 0 === str.length);
		};
		
		var init = function() {
			$('#statistics_on').change(function() {
				if(this.checked) {
					$('#statistics_ip_lock_tr').show();
				} else {
					$('#statistics_ip_lock_tr').hide();
				}
			});
			
			$('.addResult').click(function() {
				methode.addResult();
			});
			
			$('.deleteResult').click(function (e) {
				methode.deleteResult(this);
			});
			
			$('input[name="resultTextGrade[prozent][]"]').keyup(function(event) {	
				methode.changeResult(this);
			}).keydown(function(event) {
				if(event.which == 13) {
					   event.preventDefault();
				}				
			});
			
			$('#wpProQuiz_resultGradeEnabled').change(function() {
				if(this.checked) {
					$('#resultGrade').show();
					$('#resultNormal').hide();
				} else {
					$('#resultGrade').hide();
					$('#resultNormal').show();
				}
			});
			
			$('#wpProQuiz_save').click(function(e) {
				if(!methode.validInput())
					e.preventDefault();
			});
			
			$('#statistics_on').change();
			$('#wpProQuiz_resultGradeEnabled').change();
		};
		
		init();
	};
	

	if($('.wpProQuiz_quizOverall').length)
		$('.wpProQuiz_quizOverall').wpProQuiz_preview();
	
	if($('.wpProQuiz_quizOverall').length) {
		$('.wpProQuiz_quizOverall').wpProQuiz_quizOverall();
	}
	
	if($('.wpProQuiz_quizEdit').length)
		$('.wpProQuiz_quizEdit').wpProQuiz_quizEdit();
	
	if($('.wpProQuiz_questionEdit').length)
		$('.wpProQuiz_questionEdit').wpProQuiz_questionEdit();
	
	if($('.wpProQuiz_questionOverall').length)
		$('.wpProQuiz_questionOverall').wpProQuiz_questionOverall();
});
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
			$('input[name="pointsPerAnswer"]').change();
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
				
				$('input[name="pointsPerAnswer"]').change(function() {
					if(this.checked) {
						$('#wpProQuiz_showPointsBox').show();
					} else {
						$('#wpProQuiz_showPointsBox').hide();
					}
				});
				
				$('.wpProQuiz_demoBox a').mouseover(function() {
					$(this).next().show();
				}).mouseout(function() {
					$(this).next().hide();
				}).click(function() {
					return false;
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
			
			var question = '';
			var type = $('input[name="answerType"]:checked');
			var $points = $('input[name="points"]');
			
			if(tinymce.editors.question != undefined && !tinymce.editors.question.isHidden()) {
				question = tinymce.editors.question.getContent();
			} else {
				question = $('textarea[name="question"]').val();
			}
			
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
				var clozeText = '';
				
				if(tinymce.editors.cloze != undefined && !tinymce.editors.cloze.isHidden()) {
					clozeText = tinymce.editors.cloze.getContent();
				} else {
					clozeText = $('textarea[name="answerJson[answer_cloze][text]"]').val();
				}
				
				if(isEmpty(clozeText)) {
					alert(wpProQuizLocalize.no_answer_msg);
					return false;
				}
			} else if(type.val() == 'free_answer') {
				var freeText = $('textarea[name="answerJson[free_answer][correct]"]').val();
				
				if(isEmpty(freeText)) {
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
			},
			
			loadQuestionCopy: function() {
				var list = $('#questionCopySelect');
				var location = window.location.pathname + window.location.search;
				var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_question';
				var data = {
					action: 'wp_pro_quiz_load_question',
					excludeId: 1
				};
				
				list.hide();
				list.empty();
				
				$('#loadDataImg').show();

				$.post(
					url,
					data, 
					function(json) {
						$.each(json, function(i, v) {
						
							var group = $(document.createElement('optgroup'));
							
							group.attr('label', v.name);
							
							$.each(v.question, function(qi, qv) {
								$(document.createElement('option'))								
									.val(qv.id)
									.text(qv.name)
									.appendTo(group);
								
								
							});
							
							list.append(group);
							
						});						
						
						$('#loadDataImg').hide();
						list.show();
					},
					'json'
				);
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
			
			$('#wpProQuiz_questionCopy').click(function(e) {
				var $this = $('.wpProQuiz_questionCopy');
				
				if($this.is(':visible')) {
					$this.hide();
				} else {
					$this.show();
					methode.loadQuestionCopy();
				}
				
				e.preventDefault();
			});
		};

		init();
	};
	
	$.fn.wpProQuiz_quizEdit = function() {
		
		var methode = {
			addResult: function() {
				$('#resultList').children().each(function() {
					if($(this).css('display') == 'none') {
						//TODO rework
						var $this 	= $(this);
						var $text 	= $this.find('textarea[name="resultTextGrade[text][]"]');
						var id 		= $text.attr('id');
						var hidden  = true;

						$this.find('input[name="resultTextGrade[prozent][]"]').val('0');
						$this.find('input[name="resultTextGrade[activ][]"]').val('1').keyup();
						
						if(tinymce.editors[id] != undefined && !tinymce.editors[id].isHidden()) {
							hidden = false;
						}
						
						if(switchEditors != undefined  && !hidden) {
							switchEditors.go(id, 'toggle');
							switchEditors.go(id, 'toggle');
						}
						
						if(tinymce.editors[id] != undefined) {
							tinymce.editors[id].setContent('');
						} else {
							$text.val('');
						}

						if(tinymce.editors[id] != undefined && !hidden) {
							tinyMCE.execCommand('mceRemoveControl', false, id);
						}
						
						$this.parent().children(':visible').last().after($this);
						
						if(tinymce.editors[id] != undefined && !hidden) {
							tinyMCE.execCommand('mceAddControl', false, id);
						}
						
						$(this).show();

						if(switchEditors != undefined && !hidden) {
							switchEditors.go(id, 'toggle');
						}
						
						
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
				
				var text = ''; 
				
				if(tinymce.editors.text != undefined && !tinymce.editors.text.isHidden()) {
					text = tinymce.editors.text.getContent();
				} else {
					text = $('textarea[name="text"]').val();
				}
				
				if(isEmpty(text)) {
					alert(wpProQuizLocalize.no_quiz_start_msg);
					return false;
				}
				
				if($('#wpProQuiz_resultGradeEnabled:checked').length) {
					var rCheck = true;

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
			},
			
			resetLock: function() {
				var location = window.location.pathname + window.location.search;
				var url = location.replace('admin.php', 'admin-ajax.php');
				url = url.replace('action=edit', 'action=reset_lock');
				
				$.post(url, {
					action: 'wp_pro_quiz_reset_lock'
				}, function(data) {
					$('#resetLockMsg').show('fast').delay(2000).hide('fast');
				});
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
			
			$('input[name="quizRunOnce"]').change(function(e) {
				if(this.checked) {
					$('#wpProQuiz_quiz_run_once_type').show();
					$('input[name="quizRunOnceType"]:checked').change();
				} else {
					$('#wpProQuiz_quiz_run_once_type').hide();
				}
			});
			
			$('input[name="quizRunOnceType"]').change(function(e) {
				if(this.checked && (this.value == "1" || this.value == "3")) {
					$('#wpProQuiz_quiz_run_once_cookie').show();
				} else {
					$('#wpProQuiz_quiz_run_once_cookie').hide();
				}
			});
			
			$('input[name="resetQuizLock"]').click(function(e) {
				methode.resetLock();
				
				return false;
			});
			
			$('.wpProQuiz_demoBox a').mouseover(function() {
				$(this).next().show();
			}).mouseout(function() {
				$(this).next().hide();
			}).click(function() {
				return false;
			});
			
			$('input[name="showMaxQuestion"]').change(function() {
				if(this.checked) {
					$('input[name="statisticsOn"]').removeAttr('checked').attr('disabled', 'disabled').change();
					$('#wpProQuiz_showMaxBox').show();
				} else {
					$('input[name="statisticsOn"]').removeAttr('disabled');
					$('#wpProQuiz_showMaxBox').hide();
				}
			});
			
			$('#statistics_on').change();
			$('#wpProQuiz_resultGradeEnabled').change();
			$('input[name="quizRunOnce"]').change();
			$('input[name="quizRunOnceType"]:checked').change();
			$('input[name="showMaxQuestion"]').change();
		};
		
		init();
	};
	
	$.fn.wpProQuiz_statistics = function() {
		var currectTab = 'wpProQuiz_typeAnonymeUser';
		var changePageNav = true;
		
		var methode = {
			loadStatistics: function(userId) {
				var location = window.location.pathname + window.location.search;
				var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_statistics';
				var data = {
					action: 'wp_pro_quiz_load_statistics',
					userId: userId
				};
				
				$('#wpProQuiz_loadData').show();
				$('#wpProQuiz_statistics_content, #wpProQuiz_statistics_overview').hide();
				
				$.post(
					url,
					data,
					methode.setStatistics,
					'json'
				);
			},
			
			setStatistics: function(json) {
				var $table = $('.wpProQuiz_statistics_table');
				var $tbody = $table.find('tbody');
				var points = 0;
				var gPoints = 0;
				var cPoints = 0;
				
				if(currectTab == 'wpProQuiz_typeOverview') {
					return;
				}
				
				var setItem = function(i, j, r) {
					i.find('.wpProQuiz_cCorrect').text(j.cCorrect + ' (' + j.pCorrect + '%)');
					i.find('.wpProQuiz_cIncorrect').text(j.cIncorrect + ' (' + j.pIncorrect + '%)');					
					i.find('.wpProQuiz_cTip').text(j.cTip);
					i.find('.wpProQuiz_cCorrectAnswerPoints').text(j.cCorrectAnswerPoints * i.find('.wpProQuiz_pointsAnswer').text());
					
					if(r == true) {
						if(gPoints > 0) {
							$table.find('.wpProQuiz_cResult').text(
									(Math.round(points / gPoints * 100 * 100) / 100)
									+ "%");
						} else {
							$table.find('.wpProQuiz_cResult').text("0%");
						}
						i.find('.wpProQuiz_cCorrectAnswerPoints').text(cPoints);
					} else {
						points += (j.cCorrectAnswerPoints * i.find('.wpProQuiz_pointsAnswer').text());
						gPoints += (j.cCorrect + j.cIncorrect ) * i.find('.wpProQuiz_points').text();
						cPoints += j.cCorrectAnswerPoints * i.find('.wpProQuiz_pointsAnswer').text();
					}
				};
				
				setItem($table, json.clear, false);
				
				$.each(json.items, function(i, v) {
					setItem($tbody.find('#wpProQuiz_tr_' + v.id), v, false);	
				});
				
				setItem($table.find('tfoot'), json.global, true);
				
				$('#wpProQuiz_loadData').hide();
				$('#wpProQuiz_statistics_content, .wpProQuiz_statistics_table').show();
			},
			
			loadOverview: function() {
				$('.wpProQuiz_statistics_table, #wpProQuiz_statistics_content, #wpProQuiz_statistics_overview').hide();
				$('#wpProQuiz_loadData').show();
				
				var location = window.location.pathname + window.location.search;
				var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_statistics';
				var data = {
					action: 'wp_pro_quiz_load_statistics',
					overview: true,
					pageLimit: $('#wpProQuiz_pageLimit').val(),
					onlyCompleted: Number($('#wpProQuiz_onlyCompleted').is(':checked')),
					page: $('#wpProQuiz_currentPage').val(),
					generatePageNav: Number(changePageNav)
				};
				
				$.post(
					url,
					data,
					function(json) {
						$('#wpProQuiz_statistics_overview_data').empty();
						
						if(currectTab != 'wpProQuiz_typeOverview') {
							return;
						}
						
						var item = $(	'<tr>'
								+ '<th><a href="#">---</a></th>'
								+ '<th class="wpProQuiz_points">---</th>'
								+ '<th class="wpProQuiz_cCorrect" style="color: green;">---</th>'
								+ '<th class="wpProQuiz_cIncorrect" style="color: red;">---</th>'
								+ '<th class="wpProQuiz_cTip">---</th>'
								+ '<th class="wpProQuiz_cResult" style="font-weight: bold;">---</th>'
							+ '</tr>'
						);
						
						$.each(json.items, function(i, v) {
							var d = item.clone();
							
							d.find('a').text(v.userName).data('userId', v.userId).click(function() {
								$('#userSelect').val($(this).data('userId'));
								
								$('#wpProQuiz_typeRegisteredUser').click();
								
								return false;
							});
							
							if(v.completed) {
								d.find('.wpProQuiz_points').text(v.cPoints);
								d.find('.wpProQuiz_cCorrect').text(v.cCorrect + ' (' + v.pCorrect + '%)');
								d.find('.wpProQuiz_cIncorrect').text(v.cIncorrect + ' (' + v.pIncorrect + '%)');
								d.find('.wpProQuiz_cTip').text(v.cTip);
								d.find('.wpProQuiz_cResult').text((Math.round(v.cPoints / v.totalPoints * 100 * 100) / 100) + '%');
							} else {
								d.find('th').removeAttr('style');
							}								
							
							$('#wpProQuiz_statistics_overview_data').append(d);
						});
						
						if(json.page != undefined) {
							methode.setPageNav(json.page);
							changePageNav = false;
						}
						
						$('#wpProQuiz_loadData').hide();
						$('#wpProQuiz_statistics_overview').show();
					},
					'json'
				);
			},
			
			changeTab: function(id) {
				currectTab = id;
				
				if(id == 'wpProQuiz_typeRegisteredUser') {
					methode.loadStatistics($('#userSelect').val());
				} else if( id == 'wpProQuiz_typeAnonymeUser') {
					methode.loadStatistics(0);
				} else {
					methode.loadOverview();
				}
			},
			
			resetStatistic: function(complete) {
				var userId = (currectTab == 'wpProQuiz_typeRegisteredUser') ? $('#userSelect').val() : 0;
				var location = window.location.pathname + window.location.search;
				var url = location.replace('admin.php', 'admin-ajax.php') + '&action=reset';
				var data = {
						action: 'wp_pro_quiz_statistics',
						userId: userId,
						'complete': complete
				};
				
				$.post(url, data, function(e) {
					methode.changeTab(currectTab);
				});
			},
			
			setPageNav: function(page) {
				page = Math.ceil(page / $('#wpProQuiz_pageLimit').val());
				$('#wpProQuiz_currentPage').empty();
				
				for(var i = 1; i <= page; i++) {
					$(document.createElement('option'))
					.val(i)
					.text(i)
					.appendTo($('#wpProQuiz_currentPage'));
				}
				
				$('#wpProQuiz_pageLeft, #wpProQuiz_pageRight').hide();
				
				if($('#wpProQuiz_currentPage option').length > 1) {
					$('#wpProQuiz_pageRight').show();
					
				}
			}
		};
		
		var init = function() {
			$('.wpProQuiz_tab').click(function(e) {
				var $this = $(this);
				
				if($this.hasClass('button-primary')) {
					return false;
				}
				
				if($this.attr('id') == 'wpProQuiz_typeRegisteredUser') {
					$('#wpProQuiz_userBox').show();
				} else {
					$('#wpProQuiz_userBox').hide();
				}
				
				$('.wpProQuiz_tab').removeClass('button-primary').addClass('button-secondary');
				$this.removeClass('button-secondary').addClass('button-primary');
				
				methode.changeTab($this.attr('id'));
							
				return false;
			});
			
			$('#userSelect').change(function() {
				methode.changeTab('wpProQuiz_typeRegisteredUser');
			});
			
			$('.wpProQuiz_update').click(function() {
				methode.changeTab(currectTab);
				
				return false;
			});
			
			$('#wpProQuiz_reset').click(function() {
				
				var c =confirm(wpProQuizLocalize.reset_statistics_msg);
				
				if(c) {
					methode.resetStatistic(false);
				}
				
				
				return false;
			});
			
			$('.wpProQuiz_resetComplete').click(function() {
				
				var c =confirm(wpProQuizLocalize.reset_statistics_msg);
				
				if(c) {
					methode.resetStatistic(true);
				}
				
				return false;
			});
			
			$('#wpProQuiz_pageLimit, #wpProQuiz_onlyCompleted').change(function() {
				$('#wpProQuiz_currentPage').val(0);
				changePageNav = true;
				methode.changeTab(currectTab);
				
				return false;
			});
			
			$('#wpProQuiz_currentPage').change(function() {
				$('#wpProQuiz_pageLeft, #wpProQuiz_pageRight').hide();
				
				if($('#wpProQuiz_currentPage option').length == 1) {
					
				} else if($('#wpProQuiz_currentPage option:first-child:selected').length) {
					$('#wpProQuiz_pageRight').show();
				} else if($('#wpProQuiz_currentPage option:last-child:selected').length) {
					$('#wpProQuiz_pageLeft').show();
				}else {
					$('#wpProQuiz_pageLeft, #wpProQuiz_pageRight').show();
				}
				
				methode.changeTab(currectTab);
			});
			
			$('#wpProQuiz_pageRight').click(function() {
				$('#wpProQuiz_currentPage option:selected').next().attr('selected', 'selected');
				$('#wpProQuiz_currentPage').change();
				
				return false;
			});
			
			$('#wpProQuiz_pageLeft').click(function() {
				$('#wpProQuiz_currentPage option:selected').prev().attr('selected', 'selected');
				$('#wpProQuiz_currentPage').change();
				
				return false;
			});
			
			methode.changeTab('wpProQuiz_typeAnonymeUser');
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
	
	if($('.wpProQuiz_statistics').length)
		$('.wpProQuiz_statistics').wpProQuiz_statistics();
});
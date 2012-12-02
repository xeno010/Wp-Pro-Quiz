(function($) {
	$.wpProQuizFront = function(element, options) {
		var $element = $(element);
		var config = options;
		var plugin = this;
		var correctAnswer = 0;
		var countAnswer = 0;
		var intervalId = 0;
		var startTime = 0;
		var statistics = new Object();
		var points = 0;
		var pointsTotal = 0;
		
		plugin.methode = {
			startQuiz: function() {
				
				statistics = new Object();

				if(config.questionRandom) {
					plugin.methode.questionRandom();
				}
				
				if(config.answerRandom) {
					plugin.methode.answerRandom('.wpProQuiz_questionList');
				} else {
					plugin.methode.answerRandom($element.find('.wpProQuiz_sortable').parent().parent());
				}
				
				if(config.timeLimit) {
					plugin.methode.setTimeLimit();
				}
				
				correctAnswer = 0;
				points = 0;
				
				startTime = new Date();
				
				if(config.checkAnswer) {
					$element.find('input[name="check"]').show();
				} else {				
					$element.find('input[name="next"]').show();
				}
								
				$element.find('.wpProQuiz_text').hide();
				$element.find('.wpProQuiz_quiz').show();
				$element.find('.wpProQuiz_listItem').first().fadeIn(200);
				
				$element.find('.wpProQuiz_sortable').parent().parent().sortable().disableSelection();
				
				$element.find('.wpProQuiz_sortStringList, .wpProQuiz_maxtrixSortCriterion').sortable({
					connectWith: '.wpProQuiz_maxtrixSortCriterion:not(:has(li)), .wpProQuiz_sortStringList',
					placeholder: 'wpProQuiz_placehold'
				}).disableSelection();
			},

			reStartQuiz: function() {
				$element.find('.wpProQuiz_answerCorrect').removeClass('wpProQuiz_answerCorrect');
				$element.find('.wpProQuiz_answerIncorrect').removeClass('wpProQuiz_answerIncorrect');
				
				$element.find('.wpProQuiz_text, input[name="tip"]').show();				
				$element.find('.wpProQuiz_quiz, .wpProQuiz_results, .wpProQuiz_response, .wpProQuiz_correct, .wpProQuiz_incorrect')
					.hide();				
				$element.find('.wpProQuiz_time_limit, .wpProQuiz_time_limit_expired, .wpProQuiz_sort_correct_answer')
					.hide();
				$element.find('.wpProQuiz_quiz').children().first().children().hide();				
				$element.find('.wpProQuiz_sortable, .wpProQuiz_sortStringItem').removeAttr('style');				
				$element.find('input[name="check"]').hide();
				$element.find('input[name="next"]').hide();
				$element.find('.wpProQuiz_questionInput').removeAttr('disabled').removeAttr('checked');
				$element.find('input.wpProQuiz_questionInput[type="text"]').removeAttr('value');
				$element.find('.wpProQuiz_resultsList').children().hide();
				$element.find('.wpProQuiz_cloze input').removeAttr('disabled').removeAttr('value').css('background-color', '');
				$element.find('.wpProQuiz_cloze span').hide();
				
				plugin.methode.resetMatrix();
			},
			
			resetMatrix: function() {
				$element.find('.wpProQuiz_question').each(function() {
					var $tq = $(this);
					var $list = $tq.find('.wpProQuiz_sortStringList');
					
					$tq.find('.wpProQuiz_sortStringItem').each(function() {
						$list.append($(this));
					});
				});
			},
			
			setTimeLimit: function() {
				var $timeLimit = $element.find('.wpProQuiz_time_limit');
				var $span =  $timeLimit.find('span');
				var limit = config.timeLimit;
				
				$timeLimit.css({width: '100%', display: 'block'});	
				$span.html(plugin.methode.parseTime(limit));
		        $timeLimit.find('.progress').css('width', '100%').animate({width: '0%'}, config.timeLimit * 1000);
		        
		        intervalId = setInterval(function() {
		        	$span.html(plugin.methode.parseTime(limit--));
		        	
		        	if(limit < 0) {
		        		clearInterval(intervalId);
		        		intervalId = 0;
		        		$timeLimit.find('.progress').clearQueue().stop();
		        		$element.find('input[name="check"]').click().parent().hide();
		        		$element.find('.wpProQuiz_time_limit_expired').show();
		        		plugin.methode.showResult();
		        	}
		        }, 1000);
			},
			
			parseTime: function(sec) {
				var seconds = parseInt(sec % 60);
		        var  minutes = parseInt((sec / 60) % 60);
		        var hours = parseInt((sec / 3600) % 24);
		        
		        seconds = (seconds > 9 ? '' : '0') + seconds;
		        minutes = (minutes > 9 ? '' : '0') + minutes;
		        hours = (hours > 9 ? '' : '0') + hours;
		        
		        return hours + ':' +  minutes + ':' + seconds;
			},
			
			setQuizTime: function() {
				var sec = (new Date().getTime() - startTime.getTime()) / 1000;
		        $element.find('.wpProQuiz_quiz_time span').first().html(plugin.methode.parseTime(sec));
			},

			setData: function() {
				var i = 0;
				$element.find('.wpProQuiz_questionList').each(function() {
					var j = config.json[i];
					var ii = 0;
					
					pointsTotal += j.points;
					
					$(this).parent().parent()
						.data('type', j.answer_type)
						.data('questionId', j.id)
						.data('points', j.points);
					
					$(this).find('.wpProQuiz_questionInput').each(function() {
						switch(j.answer_type) {
						case 'single':
						case 'multiple':
							if($.inArray(this.value, j.correct) >= 0) {
								$(this).data('correct', '1');
							} else {
								$(this).data('correct', '0');
							}
							break;
						case 'free_answer':
							$(this).data('correct', j.correct);
							break;
						}
					});
					
					ii = 0;
					$(this).find('.wpProQuiz_sortable').each(function() {
						$(this).data('correct', j.correct[ii++]);
					});
					
					ii = 0;
					$(this).parent().find('.wpProQuiz_sortStringList').children().each(function() {
						$(this).data('correct', j.correct[ii++]);
						console.debug($(this));
					});
					
					i++;
				});
			},

			checkAnswer: function(btn) {
				var $question = $(btn).parent();
				var correct = false;
				var checked = $question.find('.wpProQuiz_questionInput');
				var type = $question.data('type');
				
				$question.find('input[name="tip"]').hide();
				
				if(type == 'multiple' || type == 'single') {
					var check = true;
					
					checked.each(function() {
						if($(this).data('correct') == '1') {
							$(this).parent().parent().addClass('wpProQuiz_answerCorrect');

							if(this.checked)
								check &= true;
							else 
								check &= false;
						} else {
							if(this.checked) {
								$(this).parent().parent().addClass('wpProQuiz_answerIncorrect');
								check &= false;
							}
						}
					});

					correct = check;
					
				} else if(type == 'sort_answer') {
					var check = true;
					
					$question.find('.wpProQuiz_sortable').each(function() {
						var $div = $(this);
						var index = $div.parent().index();
						var correct = $div.data('correct');
						
						if(correct == index) {
							$div.parent().addClass('wpProQuiz_answerCorrect');
							check &= true;
						} else {
							$div.parent().addClass('wpProQuiz_answerIncorrect');
							check = false;
						}
						
						$div.css({'box-shadow': '0 0', 'cursor': 'auto'});
					});
					
					var list = $question.find('.wpProQuiz_sortable').parent().parent();
					var items = list.children('li');
					
					list.sortable("destroy");
					
					items.sort(function(a, b) {
					   return $(a).children('div').data('correct') > $(b).children('div').data('correct');
					});
					
					$.each(items, function(idx, itm) { list.append(itm); });
					
					correct = check;
					
				} else if(type == 'free_answer') {
					var value = $.trim(checked.val()).toLowerCase();
					
					if($.inArray(value, checked.data('correct')) >= 0) {
						correct = true;
						checked.parent().parent().addClass('wpProQuiz_answerCorrect');						
					} else {
						checked.parent().parent().addClass('wpProQuiz_answerIncorrect');
					}
				} else if(type == 'matrix_sort_answer') {
					var check = true;
					var $qList = $question.find('.wpProQuiz_questionList').children();
					
					$qList.each(function() {
						var index = $(this).index();
						var $par = $(this).find('.wpProQuiz_maxtrixSortCriterion');
						var item = $par.children().first();
						
						if(item.data('correct') == index) {
							check &= true;
							$par.addClass('wpProQuiz_answerCorrect');
						} else {
							check = false;
							$par.addClass('wpProQuiz_answerIncorrect');
						}
					});
					
					$question.find('.wpProQuiz_sortStringItem').each(function() {
						$qList.eq($(this).data('correct')).find('.wpProQuiz_maxtrixSortCriterion').append(this);
					}).css({'box-shadow': '0 0', 'cursor': 'auto'});
					
					$question.find('.wpProQuiz_sortStringList, .wpProQuiz_maxtrixSortCriterion').sortable("destroy");
					
					correct = check;
				} else if(type == 'cloze_answer') {
					var check = true;
					
					$question.find('.wpProQuiz_cloze').each(function() {
						var $this = $(this);
						var children = $this.children();
						var input = children.first();
						var iText = input.val().toLowerCase();
						var cloze = children.last().text().toLowerCase();
						
						cloze = cloze.substr(1, cloze.length-2);

						if($.trim(cloze) == $.trim(iText)) {
							check &= true;
							input.css('background-color', '#B0DAB0');
						} else {
							check = false;
							input.css('background-color', '#FFBABA');
							children.last().show();
						}
						
						input.attr('disabled', 'disabled');
						
					});

					correct = check;
				}

				$(btn).hide();
				checked.attr('disabled', 'disabled');
				
				if(statistics[$question.data('questionId')] == undefined) {
					statistics[$question.data('questionId')] = new Object();
				}
				
				statistics[$question.data('questionId')].correct = Number(correct);

				$question.find('.wpProQuiz_response').show();

				if(correct) {
					$question.find('.wpProQuiz_correct').show();
					correctAnswer++;
					points += $question.data('points');
				} else {
					$question.find('.wpProQuiz_incorrect').show();
				}
				
				$question.find('input[name="next"]').show();
			},

			nextQuestion: function(btn) {
				var $q = $(btn).parent();
				var $next = $q.next();
				$q.hide();

				if($next.length) {
					$next.show();
					
					if(config.backButton && !config.checkAnswer)
						$next.find('input[name="back"]').show();
				} else {
					if(!config.checkAnswer)
						$element.find('input[name="check"]').click();
						
					plugin.methode.showResult();
				}
			},
			
			backQuestion: function(btn) {
				var $q = $(btn).parent();
				var $prev = $q.prev();
				
				$q.hide();
				$prev.show();
			},

			showResult: function() {
				clearInterval(intervalId);
				
				var $pointsSpan = $element.find('.wpProQuiz_points span');
				
				$element.find('.wpProQuiz_time_limit .progress').clearQueue().stop();
				$element.find('.wpProQuiz_correct_answer').html(correctAnswer);
				$element.find('.wpProQuiz_results').show();
				$element.find('.wpProQuiz_time_limit').hide();
				plugin.methode.setQuizTime();
				
				var pointsProzent = Math.round(points / pointsTotal * 100 * 100) / 100;
				
				$pointsSpan.eq(0).text(points);
				$pointsSpan.eq(1).text(pointsTotal);
				$pointsSpan.eq(2).text(pointsProzent);
				
				var index = plugin.methode.findResultIndex(pointsProzent);
				
				if(index > -1) {
					$element.find('.wpProQuiz_resultsList').children().eq(index).show();
				}
				
				plugin.methode.sendStatistics();
			},
			
			findResultIndex: function(p) {
				var r = config.resultsGrade;
				var index = -1;
				var diff = 999999;
				
				for(var i = 0; i < r.length; i++){
					var v = r[i];
					
					if((p >= v) && ((p-v) < diff)) {
						diff = p-v;
						index = i;
					}
				}
				
				return index;
			},
			
			sendStatistics: function() {
				if(!config.statisticsOn)
					return;
				
				$.ajax({
					url: config.url,
					type: 'POST',
					cache: false,
					data: {action: 'wp_pro_quiz_statistics_save', 'results': statistics, 'quizId': config.quizId}	
				});
			},

			reShowQuestion: function() {
				$element.find('input[name="next"], input[name="check"], input[name="back"]').hide();
				$element.find('.wpProQuiz_quiz').children().first().children().show();
			},

			answerRandom: function(selector) {
				$element.find(selector).each(function() {
					var answer = $(this).children();

					var el = answer.sort(function() {
						return Math.round(Math.random())-0.5;
					}).slice(0, answer.length);

					$(el).appendTo(el[0].parentNode).show();
				});
			},

			questionRandom: function() {
				var answer = $element.find('.wpProQuiz_quiz ol').children();

				var el = answer.sort(function() {
					return Math.round(Math.random())-0.5;
				}).slice(0, answer.length);

				$(el).appendTo(el[0].parentNode);

				var i = 1;
				$(el).each(function() {
					$(this).find('.wpProQuiz_question_page span').eq(0).html(i);
					$(this).find('h3 span').html(i++);
				});
			},
			
			showTip: function(e) {
				$tip = $(e).siblings('.wpProQuiz_tipp');
				$par = $(e).parent();
				
				if(statistics[$par.data('questionId')] == undefined) {
					statistics[$par.data('questionId')] = new Object();
				}
				
				statistics[$par.data('questionId')].tip = 1;
				
				$tip.toggle('fast');
			},
			
			setClozeStyle: function() {
				$element.find('.wpProQuiz_cloze').each(function() {
					var $this = $(this);
					var children = $this.children();
					var input = children.first();
					var cloze = children.last();
					var clone = cloze.clone();
					
					clone.css('visibility', 'hidden');
					
					$('body').append(clone);
					
					var width = clone.width();
					
					clone.remove();
					
					input.width(width + 10);
				});
			}
		};

		plugin.init = function() {
			correctAnswer = 0;
			
			if(config.resultsGrade == undefined)
				config.resultsGrade = [0];
			
			$element.find('.wpProQuiz_quiz, .wpProQuiz_results').hide();
			
			var li = $element.find('.wpProQuiz_quiz').children().first().children();

			li.hide();

			countAnswer = li.length;

			plugin.methode.setData();
			
			plugin.methode.setClozeStyle();

			$element.find('input[name="startQuiz"]').click(function(e) {
				e.preventDefault();
				plugin.methode.startQuiz();
			});

			$element.find('input[name="check"]').click(function(e) {
				e.preventDefault();
				plugin.methode.checkAnswer(this);
			});

			$element.find('input[name="next"]').click(function(e) {
				e.preventDefault();
				plugin.methode.nextQuestion(this);
			});

			$element.find('input[name="restartQuiz"]').click(function(e) {
				e.preventDefault();
				plugin.methode.reStartQuiz();
			});

			$element.find('input[name="reShowQuestion"]').click(function(e) {
				e.preventDefault();
				plugin.methode.reShowQuestion(this);
			});
			
			$element.find('input[name="back"]').click(function(e) {
				plugin.methode.backQuestion(this);
			});
			
			$element.find('input[name="tip"]').click(function(e) {
				plugin.methode.showTip(this);
			});
			
			$(document).mouseup(function(e) {
				
				var $tip = $element.find('.wpProQuiz_tipp');
				var $btn = $element.find('input[name="tip"]');
				
				if(!$tip.is(e.target) && $tip.has(e.target).length == 0 && !$btn.is(e.target))
					$tip.hide('fast');
			});
		};

		plugin.init();
	};
	
	$.fn.wpProQuizFront = function(options) {
		return this.each(function() {
			if(undefined == $(this).data('wpProQuizFront')) {
				$(this).data('wpProQuizFront', new $.wpProQuizFront(this, options));
			}
		});
	};
	
})(jQuery);
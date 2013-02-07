(function($) {
	$.wpProQuizFront = function(element, options) {
		var $e = $(element);
		var config = options;
		var plugin = this;
		var results = new Object();
		var startTime = 0;
		var currentQuestion = null;

		var bitOptions = {
			randomAnswer: 0, 
			randomQuestion: 0,
			disabledAnswerMark: 0,
			checkBeforeStart: 0,
			preview: 0,
			cors: 0
		};
		
		var quizStatus = {
			isQuizStart: 0,
			isLocked: 0,
			loadLock: 0,
			isPrerequisite: 0
		};
		
		var globalNames = {
				check: 'input[name="check"]',
				next: 'input[name="next"]',
				questionList: '.wpProQuiz_questionList'
		};

		var globalElements = {
			back: $e.find('input[name="back"]'),
			next: $e.find(globalNames.next),
			quiz: $e.find('.wpProQuiz_quiz'),
			questionList: $e.find('.wpProQuiz_list'),
			results: $e.find('.wpProQuiz_results'),
			quizStartPage: $e.find('.wpProQuiz_text'),
			timelimit: $e.find('.wpProQuiz_time_limit'),
			toplistShowInButton: $e.find('.wpProQuiz_toplistShowInButton')
		};
		
		var toplistData = {
			token: ''
		};
		
		
		var timelimit = (function() {
			var _counter = config.timelimit;
			var _intervalId = 0;
			var instance = {};
			
			instance.stop = function() {
				if(_counter) {
					window.clearInterval(_intervalId);
					globalElements.timelimit.hide();
				}
			};
				
			instance.start = function() {
				if(!_counter)
					return;
				
				var t = _counter;
				var c = t * 100;
				var g = c;
				
				var $timeText = globalElements.timelimit.find('span');
				var $timeDiv = globalElements.timelimit.find('.wpProQuiz_progress');
				
				globalElements.timelimit.show();
				
				_intervalId = window.setInterval(function() {
					if(c % 100 == 0) {
						$timeText.text(plugin.methode.parseTime(t--));
					}
					
					$timeDiv.css('width', (c / g * 100) + '%');
					
					if(c-- == 0) {
						instance.stop();
						plugin.methode.finishQuiz(true);
					}
					
				}, 10);
			};
			
			return instance;
			
		})();
		
		var checker = function(name, data, $question, $questionList) {
			var correct = true;
			var points = 0;
			var isDiffPoints = $.isArray(data.points);
			
			var func = {
				singleMulti: function() {
					var input = $questionList.find('.wpProQuiz_questionInput').attr('disabled', 'disabled');
					
					$questionList.children().each(function(i) {
						var $item = $(this);
						var index = $item.data('pos');
						var checked = input.eq(i).is(':checked');
						
						if(data.correct[index]) {
							plugin.methode.marker($item, true);
							
							if(!checked) {
								correct = false;
							} else {
								if(isDiffPoints) {
									points += data.points[index];
								}
							}
						} else {
							if(checked) {
								plugin.methode.marker($item, false);
								correct = false;
							} else {
								if(isDiffPoints) {
									points += data.points[index];
								}
							}
						}
					});
				},
				
				sort_answer: function() {
					var $items = $questionList.children();
					
					$items.each(function(i, v) {
						var $this = $(this);
						
						if(i == $this.data('pos')) {
							plugin.methode.marker($this, true);
							
							if(isDiffPoints) {
								points += data.points[i];
							}
						} else {
							plugin.methode.marker($this, false);
							correct = false;
						}
					});
					
					$items.children().css({'box-shadow': '0 0', 'cursor': 'auto'});
					
					$questionList.sortable("destroy");
					
					$items.sort(function(a, b) {
						return $(a).data('pos') > $(b).data('pos') ? 1 : -1;
					});
					
					$questionList.append($items);
				},
				
				matrix_sort_answer: function() {
					var $items = $questionList.children();
					var matrix = new Array();
					
					$items.each(function() {
						var $this = $(this);
						var i = $this.data('pos');
						var $stringUl = $this.find('.wpProQuiz_maxtrixSortCriterion');
						var $stringItem = $stringUl.children();
						
						if(i == $stringItem.data('pos')) {
							plugin.methode.marker($stringUl, true);
							
							if(isDiffPoints) {
								points += data.points[i];
							}
						} else {
							correct = false;
							plugin.methode.marker($stringUl, false);
						}
						
						matrix[i] = $stringUl;
					});
					
					plugin.methode.resetMatrix($question);
					
					$question.find('.wpProQuiz_sortStringItem').each(function() {
						var x = matrix[$(this).data('pos')];
						if(x != undefined)
							x.append(this);
					}).css({'box-shadow': '0 0', 'cursor': 'auto'});
					
					$question.find('.wpProQuiz_sortStringList, .wpProQuiz_maxtrixSortCriterion').sortable("destroy");
				},
				
				free_answer: function() {
					var $li = $questionList.children();
					var value = $li.find('.wpProQuiz_questionInput').attr('disabled', 'disabled').val();
					
					if($.inArray($.trim(value).toLowerCase(), data.correct) >= 0) {
						plugin.methode.marker($li, true);
					} else {
						plugin.methode.marker($li, false);
						correct = false;
					}
				},
				
				cloze_answer: function() {
					$questionList.find('.wpProQuiz_cloze').each(function(i, v) {
						var $this = $(this);
						var cloze = $this.children();
						var input = cloze.eq(0);
						var span = cloze.eq(1);
						
						var inputText = plugin.methode.cleanupCurlyQuotes(input.val());
						var correctText = plugin.methode.cleanupCurlyQuotes(span.text());
						
						correctText = $.trim(correctText.substr(1, correctText.length-2));
						
						if(inputText == correctText) {
							if(isDiffPoints) {
								points += data.points[i];
							}
							
							if(!bitOptions.disabledAnswerMark) {
								input.css('background-color', '#B0DAB0');
							}
						} else {
							if(!bitOptions.disabledAnswerMark) {
								input.css('background-color', '#FFBABA');
							}
							
							correct = false;
							
							span.show();
						}
						
						input.attr('disabled', 'disabled');
						
					});
				}
			};
			
			func[name]();
			
			if(!isDiffPoints && correct) {
				points = data.points;
			}
			
			return {c: correct, p: points};
		}; 
		
		plugin.methode = {
			parseBitOptions: function() {
				if(config.bo) {
					bitOptions.randomAnswer = config.bo & (1 << 0);
					bitOptions.randomQuestion = config.bo & (1 << 1);
					bitOptions.disabledAnswerMark = config.bo & (1 << 2);
					bitOptions.checkBeforeStart = config.bo & (1 << 3);
					bitOptions.preview = config.bo & (1 << 4);
					
					var cors = config.bo & (1 << 5);
					
					if(cors && jQuery.support != undefined && jQuery.support.cors != undefined && jQuery.support.cors == false) {
						bitOptions.cors = cors;
					}
				}
			},
			
			setClozeStyle: function() {
				$e.find('.wpProQuiz_cloze').each(function() {
					var children = $(this).children();
					var input = children.eq(0);
					var clone = children.eq(1).clone();
					
					clone.css('visibility', 'hidden');
					
					$('body').append(clone);
					
					var width = clone.width();
					
					clone.remove();
					
					input.width(width + 10);
				});
			},
			
			parseTime: function(sec) {
				var seconds = parseInt(sec % 60);
		        var minutes = parseInt((sec / 60) % 60);
		        var hours = parseInt((sec / 3600) % 24);
		        
		        seconds = (seconds > 9 ? '' : '0') + seconds;
		        minutes = (minutes > 9 ? '' : '0') + minutes;
		        hours = (hours > 9 ? '' : '0') + hours;
		        
		        return hours + ':' +  minutes + ':' + seconds;
			},
			
			cleanupCurlyQuotes: function(str) {
				str = str.replace(/\u2018/, "'");
				str = str.replace(/\u2019/, "'");
				
				str = str.replace(/\u201C/, '"');
				str = str.replace(/\u201D/, '"');
				
				return $.trim(str).toLowerCase();
			},
			
			resetMatrix: function(selector) {
				selector.each(function() {
					var $this = $(this);
					var $list = $this.find('.wpProQuiz_sortStringList');
					
					$this.find('.wpProQuiz_sortStringItem').each(function() {
						$list.append($(this));
					});
				});
			},
			
			marker: function(e, correct) {
				if(!bitOptions.disabledAnswerMark) {
					if(correct) {
						e.addClass('wpProQuiz_answerCorrect');
					} else {
						e.addClass('wpProQuiz_answerIncorrect');
					}
				}
			},
			
			startQuiz: function() {
				if(quizStatus.loadLock) {
					quizStatus.isQuizStart = 1;
					
					return;
				}
				
				if(quizStatus.isLocked) {
					globalElements.quizStartPage.hide();
					$e.find('.wpProQuiz_lock').show();
					
					return;
				}
				
				if(quizStatus.isPrerequisite) {
					globalElements.quizStartPage.hide();
					$e.find('.wpProQuiz_prerequisite').show();
					
					return;
				}
				
				plugin.methode.loadQuizData();
				
				if(bitOptions.randomQuestion) {
					plugin.methode.random(globalElements.questionList);
				}
				
				if(bitOptions.randomAnswer) {
					plugin.methode.random($e.find(globalNames.questionList));
				}
				
				plugin.methode.random($e.find('.wpProQuiz_sortStringList'));
				plugin.methode.random($e.find('[data-type="sort_answer"]'));
				
				$e.find('.wpProQuiz_listItem').each(function(i, v) {
					var $this = $(this);
					$this.find('.wpProQuiz_question_page span:eq(0)').text(i+1);
					$this.find('> h3 span').text(i+1);
					
					$this.find('.wpProQuiz_questionListItem').each(function(i, v) {
						$(this).find('> span').text(i+1 + '. ');
					});
				});
				
				switch (config.mode) {
					case 3:
						$e.find('input[name="checkSingle"]').show();
						$e.find('.wpProQuiz_question_page').hide();
						break;
					case 2:
						$e.find(globalNames.check).show();
						break;
					case 1:
						$e.find('input[name="back"]').slice(1).show();
					case 0:
						globalElements.next.show();
						break;
				}
				
				var $listItem = globalElements.questionList.children();
				
				if(config.mode == 3) {
					$listItem.show();
				} else {
					currentQuestion = $listItem.eq(0).show();
				}
				
				$e.find('.wpProQuiz_sortable').parents('ul').sortable().disableSelection();
				
				$e.find('.wpProQuiz_sortStringList, .wpProQuiz_maxtrixSortCriterion').sortable({
					connectWith: '.wpProQuiz_maxtrixSortCriterion:not(:has(li)), .wpProQuiz_sortStringList',
					placeholder: 'wpProQuiz_placehold'
				}).disableSelection();
				
				
				timelimit.start();
				
				startTime = +new Date();
				
				results = {comp: {points: 0, correctQuestions: 0}};
				
				globalElements.quizStartPage.hide();
				globalElements.quiz.show();
			},
			
			nextQuestion: function() {
				currentQuestion = currentQuestion.hide().next().show();
				
				plugin.methode.scrollTo(globalElements.quiz);
				
				if(!currentQuestion.length)
					plugin.methode.finishQuiz();					
			},
			
			prevQuestion: function() {
				currentQuestion = currentQuestion.hide().prev().show();
				
				plugin.methode.scrollTo(globalElements.quiz);
			},
			
			finishQuiz: function(timeover) {
				
				timelimit.stop();
				
				var time = (+new Date() - startTime) / 1000;				
				time = (config.timelimit && time > config.timelimit) ? config.timelimit : time;
				
				$e.find('.wpProQuiz_quiz_time span').text(plugin.methode.parseTime(time));
				
				if(timeover) {
					globalElements.results.find('.wpProQuiz_time_limit_expired').show();
				}
				
				plugin.methode.checkQuestion(globalElements.questionList.children());
				
				$e.find('.wpProQuiz_correct_answer').text(results.comp.correctQuestions);
				
				var resultPercent = Math.round(results.comp.points / config.globalPoints * 100 * 100) / 100;
				
				$pointFields = $e.find('.wpProQuiz_points span');
				
				$pointFields.eq(0).text(results.comp.points);
				$pointFields.eq(1).text(config.globalPoints);
				$pointFields.eq(2).text(resultPercent + '%');
				
				$e.find('.wpProQuiz_resultsList > li').eq(plugin.methode.findResultIndex(resultPercent)).show();
				
				plugin.methode.setAverageResult(resultPercent, false);
				
				plugin.methode.sendCompletedQuiz();
				
				globalElements.quiz.hide();
				globalElements.results.show();
				
				plugin.methode.scrollTo(globalElements.results);
			},
			
			sendCompletedQuiz: function() {
				if(bitOptions.preview)
					return;
				
				plugin.methode.ajax({
					action : 'wp_pro_quiz_completed_quiz',
					quizId : config.quizId,
					results : results
				});
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
			
			showQustionList: function() {
				globalElements.toplistShowInButton.hide();
				globalElements.quiz.toggle();
				$e.find('.wpProQuiz_QuestionButton').hide();
				globalElements.questionList.children().show();
				
				$e.find('.wpProQuiz_question_page').hide();
			},
			
			random: function(group) {
				group.each(function() {
					var e = $(this).children().get().sort(function() { 
						return Math.round(Math.random()) - 0.5;
					});
					
					$(e).appendTo(e[0].parentNode);
				});
			},
			
			restartQuiz: function() {
				globalElements.results.hide();
				globalElements.quizStartPage.show();
				globalElements.questionList.children().hide();
				globalElements.toplistShowInButton.hide();
				
				$e.find('.wpProQuiz_questionInput, .wpProQuiz_cloze input').removeAttr('disabled').removeAttr('checked')
					.val('').css('background-color', '');
				
				$e.find('.wpProQuiz_answerCorrect, .wpProQuiz_answerIncorrect').removeClass('wpProQuiz_answerCorrect wpProQuiz_answerIncorrect');
				
				$e.find('.wpProQuiz_listItem').data('check', false);
				
				$e.find('.wpProQuiz_response').hide().children().hide();
				
				plugin.methode.resetMatrix($e.find('.wpProQuiz_listItem'));
				
				$e.find('.wpProQuiz_sortStringItem, .wpProQuiz_sortable').removeAttr('style');
				
				$e.find('.wpProQuiz_clozeCorrect, .wpProQuiz_QuestionButton, .wpProQuiz_resultsList > li').hide();
				
				$e.find('.wpProQuiz_question_page, input[name="tip"]').show();
			},
			
			checkQuestion: function(list) {
				list = (list == undefined) ? currentQuestion : list;
				
				list.each(function() {
					var $this = $(this);
					var $questionList = $this.find(globalNames.questionList);
					var data = config.json[$questionList.data('question_id')];
					var name = data.type;
					
					if($this.data('check')) {
						return true;
					}
					
					if(data.type == 'single' || data.type == 'multiple') {
						name = 'singleMulti';
					}
					
					var result = checker(name, data, $this, $questionList);
					
					$this.find('.wpProQuiz_response').show();
					$this.find(globalNames.check).hide();
					$this.find(globalNames.next).show();
					
					if(results[data.id] == undefined) {
						results[data.id] = new Object();
					}
					
					results[data.id].points = result.p;
					results[data.id].correct = Number(result.c);
					results['comp'].points += result.p;
					
					if(result.c) {
						$this.find('.wpProQuiz_correct').show();
						results['comp'].correctQuestions += 1;
					} else {
						$this.find('.wpProQuiz_incorrect').show();
					}
					
					$this.find('.wpProQuiz_responsePoints').text(result.p);
					
					$this.data('check', true);
				});
			},
			
			showTip: function() {
				var $this = $(this);
				var id = $this.siblings('.wpProQuiz_question').find(globalNames.questionList).data('question_id');

				$this.siblings('.wpProQuiz_tipp').toggle('fast');
				
				if(results[id] == undefined) {
					results[id] = new Object();
				}
				
				results[id].tip = 1;
				
				$(document).bind('mouseup.tipEvent', function(e) {
					
					var $tip = $e.find('.wpProQuiz_tipp');
					var $btn = $e.find('input[name="tip"]');
					
					if(!$tip.is(e.target) && $tip.has(e.target).length == 0 && !$btn.is(e.target)) {
						$tip.hide('fast');
						$(document).unbind('.tipEvent');
					}
				});
			},
			
			ajax: function(data, success, dataType) {
				dataType = dataType || 'json';
				
				if(bitOptions.cors) {
					jQuery.support.cors = true;
				}
				
				$.post(WpProQuizGlobal.ajaxurl, data, success, dataType);
				
				if(bitOptions.cors) {
					jQuery.support.cors = false;
				}
			},
			
			checkQuizLock: function() {
				
				quizStatus.loadLock = 1;
				
				plugin.methode.ajax({
					action: 'wp_pro_quiz_check_lock',
					quizId: config.quizId
				}, function(json) {
					
					if(json.lock != undefined) {
						quizStatus.isLocked = json.lock.is;
						
						if(json.lock.pre) {
							$e.find('input[name="restartQuiz"]').hide();
						}
					}
					
					if(json.prerequisite != undefined) {
						quizStatus.isPrerequisite = 1;
						$e.find('.wpProQuiz_prerequisite span').text(json.prerequisite);
					}
					
					quizStatus.loadLock = 0;
					
					if(quizStatus.isQuizStart) {
						plugin.methode.startQuiz();
					}
				});
			},
			
			loadQuizData: function() {
				plugin.methode.ajax({
					action: 'wp_pro_quiz_load_quiz_data',
					quizId: config.quizId
				}, function(json) {
					if(json.toplist) {
						plugin.methode.handleToplistData(json.toplist);
					}
					
					if(json.averageResult != undefined) {
						plugin.methode.setAverageResult(json.averageResult, true);
					}
				});
			},
			
			setAverageResult: function(p, g) {
				 var v = $e.find('.wpProQuiz_resultValue:eq(' + (g ? 0 : 1) + ') >');
				 
				 v.eq(1).text(p + '%');
				 v.eq(0).css('width', (240 * p / 100) + 'px');
			},
			
			handleToplistData: function(json) {
				var $tp = $e.find('.wpProQuiz_addToplist');
				var $addBox = $tp.find('.wpProQuiz_addBox').show().children('div');
				
				if(json.canAdd) {
					$tp.show();
					$tp.find('.wpProQuiz_addToplistMessage').hide();
					$tp.find('.wpProQuiz_toplistButton').show();
					
					toplistData.token = json.token;
					
					if(json.userId) {
						$addBox.hide();
					} else {
						$addBox.show();
						
						var $captcha = $addBox.children().eq(1);
						
						if(json.captcha) {
							
							$captcha.find('input[name="wpProQuiz_captchaPrefix"]').val(json.captcha.code);
							$captcha.find('.wpProQuiz_captchaImg').attr('src', json.captcha.img);
							$captcha.find('input[name="wpProQuiz_captcha"]').val('');
							
							$captcha.show();
						} else {
							$captcha.hide();
						}
					}
				} else {
					$tp.hide();
				}
			},
			
			scrollTo: function(e) {
				var x = e.offset().top - 100;
				
				if((window.pageYOffset || document.body.scrollTop) > x) {
						$('html,body').animate({scrollTop: x}, 300);
				}
			},
			
			addToplist: function() {
				if(bitOptions.preview)
					return;
				
				var $addToplistMessage = $e.find('.wpProQuiz_addToplistMessage').text(WpProQuizGlobal.loadData).show();
				var $addBox = $e.find('.wpProQuiz_addBox').hide();
				
				plugin.methode.ajax({
					action: 'wp_pro_quiz_add_toplist',
					quizId: config.quizId,
					token: toplistData.token,
					name: $addBox.find('input[name="wpProQuiz_toplistName"]').val(),
					email: $addBox.find('input[name="wpProQuiz_toplistEmail"]').val(),
					captcha: $addBox.find('input[name="wpProQuiz_captcha"]').val(),
					prefix: $addBox.find('input[name="wpProQuiz_captchaPrefix"]').val(),
					points: results.comp.points,
					totalPoints:config.globalPoints
				}, function(json) {
					$addToplistMessage.text(json.text);
					
					if(json.clear) {
						$addBox.hide();
					} else {
						$addBox.show();
					}
					
					if(json.captcha) {
						$addBox.find('.wpProQuiz_captchaImg').attr('src', json.captcha.img);
						$addBox.find('input[name="wpProQuiz_captchaPrefix"]').val(json.captcha.code);
						$addBox.find('input[name="wpProQuiz_captcha"]').val('');
					}
				});
			}
		};

		plugin.init = function() {
			plugin.methode.parseBitOptions();
			plugin.methode.setClozeStyle();
			
			if(bitOptions.checkBeforeStart && !bitOptions.preview) {
				plugin.methode.checkQuizLock();
			}
			
			$e.find('input[name="startQuiz"]').click(function() {
				plugin.methode.startQuiz();
				return false;
			});
			
			globalElements.next.click(function() {
				plugin.methode.nextQuestion();
			});
			
			globalElements.back.click(function() {
				plugin.methode.prevQuestion();
			});
			
			$e.find('input[name="reShowQuestion"]').click(function() {
				plugin.methode.showQustionList();
			});
			
			$e.find('input[name="restartQuiz"]').click(function() {
				plugin.methode.restartQuiz();
			});
			
			$e.find(globalNames.check).click(function() {
				plugin.methode.checkQuestion();
			});
			
			$e.find('input[name="checkSingle"]').click(function() {
				plugin.methode.finishQuiz();
			});
			
			$e.find('input[name="tip"]').click(plugin.methode.showTip);
			
			$e.find('input[name="wpProQuiz_toplistAdd"]').click(plugin.methode.addToplist);
			
			$e.find('input[name="showToplist"]').click(function() {
				globalElements.quiz.hide();
				globalElements.toplistShowInButton.toggle();
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
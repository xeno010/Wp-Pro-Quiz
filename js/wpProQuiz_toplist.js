jQuery(document).ready(function($) {
	var plugin = this;
	
	plugin.toplist = {
		handleRequest: function(json) {
			$('.wpProQuiz_toplist').each(function() {
				var $tp = $(this);
				var data = json[$tp.data('quiz_id')];
				var clone = $tp.find('tbody tr').eq(2);
				
				if(data == undefined) {
					$tp.find('tbody tr').eq(0).hide().end().eq(1).show();	
					return true;
				}
				
				for(var i = 0, c = data.length; i < c; i++) {
					var td = clone.clone().children();
					
					td.eq(0).text(i+1);
					td.eq(1).text(data[i].name);
					td.eq(2).text(data[i].date);
					td.eq(3).text(data[i].points);
					td.eq(4).text(data[i].result + ' %');
					
					if(i & 1) {
						td.addClass('wpProQuiz_toplistTrOdd');
					}
					
					td.parent().show().appendTo($tp.find('tbody'));
				}
				
				$tp.find('tbody tr').eq(0).hide();
			});
		},
		
		fetchIds: function() {
			var ids = new Array();
			
			$('.wpProQuiz_toplist').each(function() {
				ids.push($(this).data('quiz_id'));
			});
			
			return ids;
		},
		
		init: function() {
			var quizIds = plugin.toplist.fetchIds();
			
			if(quizIds.length == 0)
				return;
			
			$.post(WpProQuizGlobal.ajaxurl, {
				action: 'wp_pro_quiz_show_front_toplist',
				quizIds: quizIds
			}, function(json) {
				plugin.toplist.handleRequest(json);
			}, 'json');
		}
	};
	
	plugin.toplist.init();
});
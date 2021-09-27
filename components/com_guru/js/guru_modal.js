var opened_lesson_id = 0;

var modalWindow = {
	parent:"body",
	windowId:null,
	content:null,
	width:null,
	height:null,
	left:null,
	right:null,
	top:null,
	bigmodal:null,
	close:function()
	{
		jQuery(".guru-modal").remove();
		jQuery(".modal-overlay").remove();
	},
	open:function()
	{
		modal_style = "";
		if(this.width != null && this.width != 0){
			screen_width = window.innerWidth;
			screen_height = window.innerHeight;
	
			modal_style = 'style="display:block;"';
		}
		else{
			screen_width = window.innerWidth;
			screen_height = window.innerHeight;
			
			modal_style = 'style="display:block;"';
		}
		
		var modal = "";
		modal += "<div class=\"modal-overlay\"></div>";
		modal += "<div class=\"guru-modal p_modal\" id=\"" + this.windowId + "\">";
		modal += "<div class=\"guru-modal-header\"></div>";
		modal += "<div class=\"guru-modal-dialog\" "+modal_style+">";
		modal += "<div class=\"guru-modal-content\">";
		modal += this.content;
		modal += "</div>";
		modal += "</div>";
		modal += "</div>";
		jQuery(this.parent).append(modal);


		jQuery(".guru-modal-dialog").append("<a class=\"close-window\" id=\"close-window\"></a>");
		jQuery(".close-window").click(function(){modalWindow.close();});
		jQuery(".modal-overlay").click(function(){modalWindow.close();});
		jQuery(".guru-modal").click(function(){modalWindow.close();});
	}
};

var openMyModal = function(width, height, source){
	modalWindow.windowId = "myModal";

	iframe_style = "";
	
	if(width != 0 && height != 0){
		screen_width = window.innerWidth;
		screen_height = window.innerHeight;
		
		modalWindow.width = width;
		modalWindow.height = height;
		
		iframe_style = 'style="width:'+width+'px; height:'+height+'px;"';
	}
	else{
		modalWindow.width = 0;
		modalWindow.height = 0;
		iframe_style = 'style=""';
	}

	modalWindow.content = "<iframe "+iframe_style+" id='g_preview' class='pub_modal_frame' src='" + source + "'>content</iframe>";
	modalWindow.open();
};

jQuery(document).ready(function($) {
	$.ajaxSetup({ cache: false });

	var $html = $('html');
	var $win = $( window );
	var $doc = $( document.body );
	var $modal;
	var cache, params;

	function createModal(source) {
		var html = '';
		var shownav = 'shownav';
		var fontello_right_open = 'fontello-right-open';
		
		if (window.matchMedia("(max-width: 1024px)").matches) {
			// small device and tablet
			shownav = "";
			fontello_right_open = 'fontello-left-open';
		}
		
		// wrapper (start)
		html += '<div class="guru-lesson ' + shownav + '">';
		// header
		html += '<div class=guru-lesson-header>';
		html += '<div class=guru-lesson-header-l>';
		html += '<div class=guru-lesson-header-label></div>';
		html += '</div>';
		html += '<div class=guru-lesson-header-r>';
		html += '<div id="lesson-saved-timer"></div>';
		html += '<a href="javascript:" class=guru-lesson-home><i class=fontello-cancel></i></a>';
		html += '<a href="javascript:" class=guru-lesson-togglenav><i class=' + fontello_right_open + '></i></a>';
		html += '</div>';
		html += '</div>';
		// content
		html += '<div class=guru-lesson-content>';
		html += '<iframe id=frame-lesson-content webkitallowfullscreen mozallowfullscreen allowfullscreen frameborder=0 style="width:100%;height:100%;"></iframe>';
		html += '<div class=guru-lesson-loading><div class=guru-lesson-spinner></div></div>';
		html += '</div>';
		// footer
		html += '<div id=guru-lesson-footer class=guru-lesson-footer>';
		
		if(lesson_view_confirm == 1){
			html += '<div class=guru-lesson-footer-confirm>';
			html += '<button class=guru-lesson-footer-confirm-btn style="display:none"><i class=fontello-list-alt></i> <br /> '+ set_unit_completed +' </button>';
			html += '<button class=guru-lesson-footer-unconfirm-btn style="display:none"><i class=fontello-ok-circled></i> <br /> '+ set_unit_uncompleted +' </button>';
			html += '</div>';
		}

		html += '<div class=guru-lesson-footer-nav>';
		html += '<button class=guru-lesson-footer-prev style="display:none"><i class=fontello-left-open></i> '+prev_lang+'</button> ';
		html += '<button class=guru-lesson-footer-next style="display:none">'+next_lang+' <i class=fontello-right-open></i></button>';
		html += '</div>';
		html += '</div>';
		// sidebar
		html += '<div class=guru-lesson-nav>';
		html += '<div class=guru-lesson-nav-tab>';
		html += '<a href="javascript:" data-id=lessons class="guru-lesson-nav-tabitem active"><i class=fontello-th-list></i></a>';
		html += '<a href="javascript:" data-id=description class="guru-lesson-nav-tabitem"><i class=fontello-doc-text-inv></i></a>';
		
		html += '<a id="comments-button" style="display:none;" href="javascript:" data-id=comments class="guru-lesson-nav-tabitem"><i class=fontello-chat></i></a>';
		
		html += '</div>';
		html += '<div class="guru-lesson-nav-content guru-lesson-nav-lessons"></div>';
		html += '<div class="guru-lesson-nav-content guru-lesson-nav-description"></div>';
		html += '<div class="guru-lesson-nav-content guru-lesson-nav-comments"></div>';
		html += '</div>';
		// wrapper (end)
		html += '</div>';

		$modal = $( html );
		$modal.find('.guru-lesson-home').on('click', destroyWindow );
		$modal.find('.guru-lesson-togglenav').on('click', toggleNav );
		$modal.find('.guru-lesson-footer-prev').on('click', prevLesson );
		$modal.find('.guru-lesson-footer-next').on('click', nextLesson );
		$modal.find('.guru-lesson-nav-tab a').on('click', toggleNavTab );
		$modal.find('.guru-lesson-footer-confirm-btn').on('click', function() {
			confirmLessonView().then(() => {
				getSidebarContent();
				updateLessonStatus();
			})
		});
		$modal.find('.guru-lesson-footer-unconfirm-btn').on('click', function() {
			notConfirmLessonView().then(() => {
				getSidebarContent();
				updateLessonStatus();
			})
		});
		$modal.on('click', '.guru-lesson-nav-lesson', showLesson );
		$modal.on('guru.updateLessonProgress', function(e, lesson_id) {
			params.cid = lesson_id;
			getSidebarContent();
			updateLessonStatus();
		});
		$html.css('overflow', 'hidden');

		var isIOS = ( /iphone|ipad|ipod/i ).test( navigator.userAgent );

		// Fix iOS issue
		// http://stackoverflow.com/questions/35879449/iframe-cannot-scroll-because-of-setting-height-in-css
		if ( isIOS ) {
			$modal.find('.guru-lesson-content').css({
				'overflow-x': 'auto',
				'-webkit-overflow-scrolling': 'touch'
			});
		}

		$doc.append( $modal );

		// loading
		isIOS && $html.css('position', 'fixed');
		$modal.find('iframe').on('load', function() {
			$modal.find('.guru-lesson-loading').fadeOut();
			
			if (params.duration) {
				$("#lesson-saved-timer").html("");
				lesson_duration_text = "";
				lesson_duration = params.duration.split('x');
				
				if(!isNaN(parseInt(lesson_duration[0])) && !isNaN(parseInt(lesson_duration[1]))){
					lesson_duration_text = lesson_duration[0]+"min "+lesson_duration[1]+"sec";
				}
				else if(!isNaN(parseInt(lesson_duration[0])) && isNaN(parseInt(lesson_duration[1]))){
					lesson_duration_text = lesson_duration[0]+"min";
				}
				else if(isNaN(parseInt(lesson_duration[0])) && !isNaN(parseInt(lesson_duration[1]))){
					lesson_duration_text = lesson_duration[1]+"sec";
				}
				
				$("#lesson-saved-timer").html(lesson_duration_text);
			}

			getSidebarContent();
			getComments( params.cid );
			updateLessonStatus();
	
			moveJumpButtons();
			isIOS && $html.css('position', '');

			setTimeout(function() {
				lessons_not_viewed = document.getElementsByClassName("not-view");
			
				if(lessons_not_viewed.length == 0){
					// check course completed
					var course_id = document.getElementById("course_id").value;
					var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=check_course_completed&ajax=1&tmpl=component&course_id=' + course_id;
					$.ajax({
						url: url,
						type: 'get',
						dataType: 'json',
						success: function( resp ) {
							if(resp === true){
								$("#id-lesson-0").addClass("guru-lesson-nav-accessible");
								$(".redirect-lesson").show();
							}
						}
					});
				}
			}, 2000 );

		}).attr('src', source );

		startNewCourseTime(params.pid);
		startLessonTimeView(params.cid);
		getSidebarContent();
		getComments( params.cid );
		updateLessonStatus();
	}

	function destroyWindow() {
		saveCurrentCourseTime();
		saveCurrentLessonTime(opened_lesson_id);
		
		var course_id = document.getElementById("course_id").value;
		var module = $('.guru-lesson-nav-current').attr("data-module-id");
		var lesson_id = $('.guru-lesson-nav-current').attr("data-lesson-id");

		/*var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&pid=' + course_id + '&task=set_lesson_viewed&ajax=1&module='+module+'&lesson_id='+lesson_id;
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( resp ) {
				
			}
		});*/

		document.getElementsByTagName("html")[0].style.overflow = "auto";
		document.getElementsByTagName("html")[0].style.position = "";

		$html.css('overflow', '');
		$win.off('resize.guru-lesson');
		$modal.remove();
		$modal = null;
	}

	function toggleNav( e ) {
		var $btn = $( e.currentTarget );

		if ( $modal.hasClass('shownav') ) {
			$modal.removeClass('shownav');
			$btn.find('i').attr('class', 'fontello-left-open');
		} else {
			$modal.addClass('shownav');
			$btn.find('i').attr('class', 'fontello-right-open');
		}
	}

	function prevLesson() {
		if ( !cache ) {
			return;
		}

		var $lessons = $modal.find('.guru-lesson-nav-lessons'),
			$current = $lessons.find('.guru-lesson-nav-current'),
			$accessible, index;

		if ( !$current.length ) {
			return;
		}

		$accessible = $lessons.find('.guru-lesson-nav-accessible');
		index = $accessible.index( $current );

		if ( index > 0 ) {
			$accessible.eq( index - 1 ).click();
		}
	}

	function nextLesson() {
		if ( !cache ) {
			return;
		}

		var $lessons = $modal.find('.guru-lesson-nav-lessons'),
			$current = $lessons.find('.guru-lesson-nav-current'),
			$accessible;

		if ( !$current.length ) {
			return;
		}

		$accessible = $lessons.find('.guru-lesson-nav-accessible');
		index = $accessible.index( $current );
		
		if ( index < $accessible.length - 1 ) {
			nex_element = $accessible.eq( index + 1 );

			if ( $(nex_element).parent().css('display') == 'none' ){
				return false;
			}

			$accessible.eq( index + 1 ).click();
		}
	}

	function confirmLessonView(){
		return new Promise((resolve, reject) => {
			var current_module_id = $('.guru-lesson-nav-current').attr("data-module-id");
			var current_lesson_id = $('.guru-lesson-nav-current').attr("data-lesson-id");
			
			var url = params.uri + 'index.php?option=com_guru&view=guruTasks&controller=guruTasks&pid=' + params.pid + '&cid=' + params.pid + '&task=set_lesson_viewed&ajax=1&lesson_id=' + parseInt(current_lesson_id) + "&module=" + parseInt(current_module_id);
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'json',
				success: function( resp ) {
					$(".guru-lesson-footer-confirm-btn").hide();
					$(".guru-lesson-footer-unconfirm-btn").show();

					lessons_viewed = document.getElementsByClassName("viewed-"+current_lesson_id);

					if(lessons_viewed[0] !== undefined){
						lessons_viewed[0].style.visibility = "visible";
						lessons_viewed[1].style.visibility = "visible";
					}

					$("#id-lesson-"+current_lesson_id+" .guru-lesson-nav-check").css("background", "rgba(61, 225, 0, 0.34)");
					$("#id-lesson-"+current_lesson_id+" .guru-lesson-nav-check").removeClass("not-view");
					$("#id-lesson-"+current_lesson_id+" .guru-lesson-nav-check i").css("color", "rgb(84, 181, 81)");

					resolve();
				}
			});
		});
	}

	function notConfirmLessonView(){
		return new Promise((resolve, reject) => {
			var current_module_id = $('.guru-lesson-nav-current').attr("data-module-id");
			var current_lesson_id = $('.guru-lesson-nav-current').attr("data-lesson-id");
			
			var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&pid=' + params.pid + '&cid=' + params.pid + '&task=set_lesson_notviewed&ajax=1&lesson_id=' + parseInt(current_lesson_id) + "&module=" + parseInt(current_module_id);
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'json',
				success: function( resp ) {
					$(".guru-lesson-footer-unconfirm-btn").hide();
					$(".guru-lesson-footer-confirm-btn").show();

					lessons_viewed = document.getElementsByClassName("viewed-"+current_lesson_id);

					if(lessons_viewed[0] !== undefined){
						lessons_viewed[0].style.visibility = "hidden";
						lessons_viewed[1].style.visibility = "hidden";
					}

					$("#id-lesson-"+current_lesson_id+" .guru-lesson-nav-check").css("background-color", "#ddd");
					$("#id-lesson-"+current_lesson_id+" .guru-lesson-nav-check").addClass("not-view");
					$("#id-lesson-"+current_lesson_id+" .guru-lesson-nav-check i").css("color", "#9f9f9f");

					resolve();
				}
			});
		});
	}

	function updateLessonNav(index, max) {
		if ( index <= 0 ) {
			$modal.find('.guru-lesson-footer-prev').hide();
		} else {
			$modal.find('.guru-lesson-footer-prev').show();
		}

		if ( index >= max ) {
			$modal.find('.guru-lesson-footer-next').hide();
		} else {
			$modal.find('.guru-lesson-footer-next').show();
		}

		// check course completed
		var course_id = document.getElementById("course_id").value;
		var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=check_course_completed&ajax=1&tmpl=component&course_id=' + course_id;
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( resp ) {
				if(resp === true){
					$("#id-lesson-0").addClass("guru-lesson-nav-accessible");
					$(".redirect-lesson").show();
				}
			}
		});
	}

	function updateConfirmButtons(lesson){
		if(lesson.viewed === true && lesson.data.layout != 12){
			$(".guru-lesson-footer-unconfirm-btn").show();
			$(".guru-lesson-footer-confirm-btn").hide();
		}
		else if(lesson.viewed === false && lesson.data.layout != 12){
			$(".guru-lesson-footer-confirm-btn").show();
			$(".guru-lesson-footer-unconfirm-btn").hide();
		}
		else{
			$(".guru-lesson-footer-confirm-btn").hide();
			$(".guru-lesson-footer-unconfirm-btn").hide();
		}
	}

	function toggleNavTab( e ) {
		var $btn = $( e.currentTarget ),
			id = $btn.data('id'),
			$content = $('.guru-lesson-nav-' + id ),
			$siblings = $content.siblings('.guru-lesson-nav-content');

		$siblings.hide();
		$content.show();
		$btn.addClass('active').siblings('.active').removeClass('active');
	}

	function getSidebarContent() {
		var lang_url = "";
		var lang_value = document.getElementById("modal-lang").value;

		if(lang_value != ""){
			lang_url = "&lang="+lang_value;
		}

		var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&pid=' + params.pid + '&cid=' + params.pid + '&task=get_lessons&ajax=1&Itemid=' + params.item_id + lang_url;
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( resp ) {
				printSidebar( resp );
			}
		});
	}

	function updateLessonStatus() {
		var url = Joomla.getOptions('guru.tabViewComponent');
		
		$.ajax({
			url: url,
			dataType: 'html'
		})
		.done(function(res) {
			var $chapters = $('.guru-tabs.chapter_wrap');
			var $html = $(res);
			var $newChapters = $html.find('.guru-tabs.chapter_wrap');

			$chapters.each(function(idx, elm) {
				var newHtml = $newChapters.get(idx).innerHTML;
				$(elm).html(newHtml);
			});
		});
	}

	function printSidebar( json ) {
		if($modal === null){
			return;
		}
		
		var $lessons = $modal.find('.guru-lesson-nav-lessons'),
			$description = $modal.find('.guru-lesson-nav-description'),
			html = '',
			accessible = 0,
 			index = 0,
			info, module, lessons, lesson, media, text, type, duration, i, j;

		var current_lesson_details = null;

		cache = json = json || {};
		info = json.info || {};
		
		if(cache.kunena.allow_stud == "1"){
			document.getElementById("comments-button").style.display = "none";
		}
		else if(cache.kunena.allow_stud == "0"){
			document.getElementById("comments-button").style.display = "block";
		}
		
		// update lessons
		html += '<ul>';
		for ( i = 0; i < json.modules.length; i++ ) {
			module = json.modules[i];
			lessons = module.lessons;
			html += '<li>';
			
			if(module.can_open_module == '1'){
				html += '<span class=guru-lesson-nav-module><a id="id-module-'+module.module_id+'" class="guru-lesson-nav-lesson guru-lesson-nav-accessible" href="javascript:" data-mindexm="' + i + '">' + module.title + '</a></span>';
			}
			else{
				html += '<span class=guru-lesson-nav-module><a id="id-module-'+module.module_id+'" class="guru-lesson-nav-lesson" href="javascript:">' + module.title + '</a></span>';
			}
			
			html += '<ul class="guru-lesson-nav-list">';
			
			for ( j = 0; j < lessons.length; j++ ) {
				lesson = lessons[j];
				media = lesson.data && lesson.data.media || [];
				text = lesson.data && lesson.data.text || [];
				type = lesson.data && lesson.data.type || [];

				accessible++;

				if(parseInt(lesson.id) > 0){
					html += '<li><a id="id-lesson-'+lesson.id+'" data-openable="'+lesson.can_open_lesson+'" data-lesson-id="' + lesson.id + '" data-module-id="' + module.module_id + '" class="guru-lesson-nav-lesson';
				}
				else{
					html += '<li class="redirect-lesson"><a id="id-lesson-'+lesson.id+'" data-lesson-id="' + lesson.id + '" data-module-id="' + module.module_id + '" class="guru-lesson-nav-lesson';
				}
				
				if ( params.cid == lesson.id ) {
					if(lesson_view_confirm == 0){
						lessons_viewed = document.getElementsByClassName("viewed-"+lesson.id);
						lessons_viewed[0].style.visibility = "visible";
						lessons_viewed[1].style.visibility = "visible";
					}
					
					params.current_lesson = lesson.id;
					current_lesson_details = lesson;
					html += ' guru-lesson-nav-current';
					index = accessible;
					
					var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=get_lesson_description&ajax=1&lesson_id=' + lesson.id;
					$.ajax({
						url: url,
						type: 'get',
						dataType: 'json',
						success: function( resp ) {
							json = resp || {};
							$description.html( json.description || '' );
						}
					});
				}
				if ( +lesson.can_open_lesson ) {
					if(parseInt(lesson.id) > 0){
						html += ' guru-lesson-nav-accessible" href="javascript:" data-mindex="' + i + '" data-cindex="' + j;
					}
					else{
						html += ' guru-lesson-nav-accessible" href="#" onclick="javascript:redirectCourseAfterComplete(\''+lesson.url+'\')" data-mindex="' + i + '" data-cindex="' + j;
					}

				}
				else if(lesson.alias == "completed-course-redirect"){
					html += ' guru-lesson-nav-accessible" href="#" onclick="javascript:redirectCourseAfterComplete(\''+lesson.url+'\')" data-mindex="' + i + '" data-cindex="' + j;
				}
				else{
					if(parseInt(lesson.id) > 0){
						html += ' " href="javascript:" data-mindex="' + i + '" data-cindex="' + j;
					}
				}

				html += '">' + lesson.name;
				
				if ( !+lesson.can_open_lesson ) {
					if(typeof lesson.available_div != "undefined"){
						available_language = document.getElementById("available_language").value;
						html += '<br /> <div class="clearfix available-area-'+lesson.id+'"><div class="pull-left>' + available_language + ":&nbsp;</div>" + lesson.available_div + '</div>';
					}
				}
				
				if ( lesson.data && lesson.data.layout == 12 ) {
					html += '<br><span><i class=fontello-check></i>'+quiz_lesson_lang;
				}
				else {
					if(type.length > 0){
						if ( type.indexOf('video') >= 0 ) {
							html += '<br><span><i class=fontello-play-circled></i>'+video_lesson_lang;
						}
						else if ( type.indexOf('audio') >= 0 ) {
							html += '<br><span><i class=icon-volume-up></i>'+audio_lesson_lang;
						}
						else if ( type.indexOf('docs') >= 0 ) {
							html += '<br><span><i class=fontello-doc-text-inv></i>'+document_lesson_lang;
						}
						else if ( type.indexOf('url') >= 0 ) {
							html += '<br><span><i class=icon-link-ext></i>'+url_lesson_lang;
						}
						else if ( type.indexOf('Article') >= 0 ) {
							html += '<br><span><i class=icon-pencil></i>'+article_lesson_lang;
						}
						else if ( type.indexOf('image') >= 0 ) {
							html += '<br><span><i class=icon-picture></i>'+image_lesson_lang;
						}
						else if ( type.indexOf('text') >= 0 ) {
							html += '<br><span><i class=fontello-doc-text-inv></i>'+text_lesson_lang;
						}
						else if ( type.indexOf('file') >= 0 ) {
							html += '<br><span><i class=icon-folder-empty></i>'+file_lesson_lang;
						}
					}
				}
				
				var class_not_view = (lesson.viewed || params.cid == lesson.id) ? '' : 'not-view';

				html += '</span>';
				html += '<span class="guru-lesson-nav-check ' + class_not_view + '"';

				if(lesson_view_confirm == 0){
					html += (lesson.viewed || params.cid == lesson.id) ? 'style="background:rgba(61, 225, 0, 0.34)"' : '';
				}
				else{
					html += (lesson.viewed) ? 'style="background:rgba(61, 225, 0, 0.34)"' : '';
				}

				html += '>';
				html += '<span class=guru-lesson-nav-check-circle><i class=fontello-ok ';

				if(lesson_view_confirm == 0){
					html += (lesson.viewed || params.cid == lesson.id) ? 'style="color:rgb(84, 181, 81)"' : '';
				}
				else{
					html += (lesson.viewed) ? 'style="color:rgb(84, 181, 81)"' : '';
				}

				html += '></i></span>';
				html += '</span>';

				html += '</a></li>';

				// update lesson header
				if ( params.cid == lesson.id ) {
					$modal.find('.guru-lesson-header-label').html( lesson.name || '' );
				}
			}
			html += '</ul>';
			html += '</li>';
		}
		html += '</ul>';
		$lessons.html( html );
		updateLessonNav( index, accessible );
		updateConfirmButtons(current_lesson_details);
	}

	function showLesson( e ) {
		saveCurrentCourseTime();
		saveCurrentLessonTime(opened_lesson_id);

		$("#lesson-saved-timer").html("");

		var $btn = $( e.currentTarget ),
			data, lesson, media, text, html;

		if ( $btn.hasClass('guru-lesson-nav-current') ) {
			return;
		}

		if ( $btn.hasClass('guru-lesson-nav-accessible') ) {
			data = $btn.data();

			if(data.mindex !== undefined){
				lesson = cache.modules[ data.mindex ].lessons[ data.cindex ] || {};
			}
			else{
				lesson = cache.modules[ data.mindexm ] || {};
			}

			if(typeof lesson.id !== 'undefined'
				&& lesson.hasOwnProperty("course_type") 
				&& lesson.hasOwnProperty("lesson_release") 
				&& lesson.hasOwnProperty("next_lesson")
				&& lesson.course_type == "1" 
				&& lesson.lesson_release == 5
				&& parseInt(lesson.next_lesson) > 0) {
				
				var $next = $("#id-lesson-"+parseInt(lesson.next_lesson));
				if ($next.data('openable') === '1') {
					$next.addClass("guru-lesson-nav-accessible");
					$(".available-area-"+parseInt(lesson.next_lesson)).hide();
				}
			}

			if(lesson.viewed === true){
				$(".guru-lesson-footer-unconfirm-btn").show();
				$(".guru-lesson-footer-confirm-btn").hide();
			}
			else if(lesson.viewed === false){
				$(".guru-lesson-footer-confirm-btn").show();
				$(".guru-lesson-footer-unconfirm-btn").hide();
			}
			else{
				$(".guru-lesson-footer-confirm-btn").hide();
				$(".guru-lesson-footer-unconfirm-btn").hide();
			}

			if(lesson_view_confirm == 0){
				$("#id-lesson-"+lesson.id+" .guru-lesson-nav-check").css("background", "rgba(61, 225, 0, 0.34)");
				$("#id-lesson-"+lesson.id+" .guru-lesson-nav-check").removeClass("not-view");
			}

			lessons_not_viewed = document.getElementsByClassName("not-view");
		
			if(lessons_not_viewed.length == 0){
				// check course completed
				var course_id = document.getElementById("course_id").value;
				var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=check_course_completed&ajax=1&tmpl=component&course_id=' + course_id;
				$.ajax({
					url: url,
					type: 'get',
					dataType: 'json',
					success: function( resp ) {
						if(resp === true){
							$("#id-lesson-0").addClass("guru-lesson-nav-accessible");
							$(".redirect-lesson").show();
						}
					}
				});
			}

			if(lesson_view_confirm == 0){
				lessons_viewed = document.getElementsByClassName("viewed-"+lesson.id);

				if(lessons_viewed[0] !== undefined){
					lessons_viewed[0].style.visibility = "visible";
					lessons_viewed[1].style.visibility = "visible";
				}
			}
			
			opened_lesson_id = lesson.id;
			startLessonTimeView(opened_lesson_id);
			params.current_lesson = lesson.id;
			getComments( lesson.id );
			
			html = '<iframe id=frame-lesson-content webkitallowfullscreen mozallowfullscreen allowfullscreen frameborder=0 style="width:100%;height:100%;"></iframe>';
			$modal.find('.guru-lesson-nav-current').removeClass('guru-lesson-nav-current');
			$btn.addClass('guru-lesson-nav-current').find('.guru-lesson-nav-check-circle i').show();
			setModalActiveIfIsCurrent();
			$modal.find('.guru-lesson-header-label').html( lesson.name || '' );
			$modal.find('.guru-lesson-content iframe').replaceWith( html );

			var isIOS = ( /iphone|ipad|ipod/i ).test( navigator.userAgent );

			// loading
			isIOS && $html.css('position', 'fixed');
			$modal.find('.guru-lesson-loading').show();
			$modal.find('iframe').one('load', function() {
				$modal.find('.guru-lesson-loading').fadeOut();
				moveJumpButtons();
				isIOS && $html.css('position', '');
			}).attr('src', lesson.url );

			if (lesson.duration) {
				lesson_duration_text = "";
				lesson_duration = lesson.duration.split('x');
				
				if(!isNaN(parseInt(lesson_duration[0])) && !isNaN(parseInt(lesson_duration[1]))){
					lesson_duration_text = lesson_duration[0]+"min "+lesson_duration[1]+"sec";
				}
				else if(!isNaN(parseInt(lesson_duration[0])) && isNaN(parseInt(lesson_duration[1]))){
					lesson_duration_text = lesson_duration[0]+"min";
				}
				else if(isNaN(parseInt(lesson_duration[0])) && !isNaN(parseInt(lesson_duration[1]))){
					lesson_duration_text = lesson_duration[1]+"sec";
				}
				
				$("#lesson-saved-timer").html(lesson_duration_text);
			}

			// get description
			var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=get_lesson_description&ajax=1&lesson_url=' + encodeURIComponent(lesson.url);
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'json',
				success: function( resp ) {
					var $description = $modal.find('.guru-lesson-nav-description');
					json = resp || {};
					$description.html( json.description);
				}
			});

			$modal.find('.guru-lesson-content iframe').on('load', function() {
				if (lesson.id) {
					params.cid = lesson.id;
					getSidebarContent();
					updateLessonStatus();
				}
			});
		}
	}

	function startNewCourseTime(pid){
		var current_time = (new Date()).toUTCString();
		var id = "viewed_course_" + parseInt(pid);

		sessionStorage.setItem(id, current_time);
	}

	function startLessonTimeView(lesson_id){
		opened_lesson_id = lesson_id;

		var current_time = (new Date()).toUTCString();
		var id = "viewed_lesson_" + parseInt(lesson_id);

		sessionStorage.setItem(id, current_time);
	}

	function saveCurrentCourseTime(){
		var course_id = document.getElementById("course_id").value;

		var start_years = getCookie(course_id, "years");
		var start_months = getCookie(course_id, "months");
		var start_days = getCookie(course_id, "days");
		var start_hours = getCookie(course_id, "hours");
		var start_minutes = getCookie(course_id, "minutes");
		var start_seconds = getCookie(course_id, "seconds");

		if(start_days < 10){
			start_days = "0"+start_days;
		}

		if(start_hours < 10){
			start_hours = "0"+start_hours;
		}

		if(start_minutes < 10){
			start_minutes = "0"+start_minutes;
		}

		if(start_seconds < 10){
			start_seconds = "0"+start_seconds;
		}

		var start_time = new Date(start_years, start_months, start_days, start_hours, start_minutes, start_seconds, 0);
		var current_time = new Date();
		var dif_time_seconds = current_time - start_time;

		dif_time_seconds = dif_time_seconds / 1000;
		var dif_hour = Math.floor(dif_time_seconds / (60 * 60));
		var divisor_for_minutes = dif_time_seconds % (60 * 60);
		var dif_min = Math.floor(divisor_for_minutes / 60);
		var divisor_for_seconds = divisor_for_minutes % 60;
		var dif_sec = Math.ceil(divisor_for_seconds);

		var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&course_id=' + course_id + '&task=saveDateTime&ajax=1&dif_hour='+dif_hour+"&dif_min="+dif_min+"&dif_sec="+dif_sec;
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( resp ) {
				
			}
		});

		startNewCourseTime(course_id);
	}

	function saveCurrentLessonTime(lesson_id){
		var start_years = getCookieLesson(lesson_id, "years");
		var start_months = getCookieLesson(lesson_id, "months");
		var start_days = getCookieLesson(lesson_id, "days");
		var start_hours = getCookieLesson(lesson_id, "hours");
		var start_minutes = getCookieLesson(lesson_id, "minutes");
		var start_seconds = getCookieLesson(lesson_id, "seconds");
		
		if(start_days < 10){
			start_days = "0"+start_days;
		}

		if(start_hours < 10){
			start_hours = "0"+start_hours;
		}

		if(start_minutes < 10){
			start_minutes = "0"+start_minutes;
		}

		if(start_seconds < 10){
			start_seconds = "0"+start_seconds;
		}

		var start_time = new Date(start_years, start_months, start_days, start_hours, start_minutes, start_seconds, 0);
		var current_time = new Date();
		var dif_time_seconds = current_time - start_time;

		dif_time_seconds = dif_time_seconds / 1000;
		var dif_hour = Math.floor(dif_time_seconds / (60 * 60));
		var divisor_for_minutes = dif_time_seconds % (60 * 60);
		var dif_min = Math.floor(divisor_for_minutes / 60);
		var divisor_for_seconds = divisor_for_minutes % 60;
		var dif_sec = Math.ceil(divisor_for_seconds);

		var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&lesson_id=' + lesson_id + '&task=saveDateTimeLesson&ajax=1&dif_hour='+dif_hour+"&dif_min="+dif_min+"&dif_sec="+dif_sec;
		
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( resp ) {
				
			}
		});
	}

	function getCookie(course_id, name){
		var id = 'viewed_course_' + parseInt(course_id);
		var time = sessionStorage.getItem(id);
		if (!time) {
			return '';
		}

		var date = (new Date(time));
		
		switch (name) {
			case 'years':
				return date.getFullYear();
			case 'months':
				return date.getMonth();
			case 'days':
				return date.getDate();
			case 'hours':
				return date.getHours();
			case 'minutes':
				return date.getMinutes();
			case 'seconds':
				return date.getSeconds();
			default:
				return '';
		}
	}

	function getCookieLesson(lesson_id, name){
		var id = 'viewed_lesson_' + parseInt(lesson_id);
		var time = sessionStorage.getItem(id);
		if (!time) {
			return '';
		}

		var date = (new Date(time));
		
		switch (name) {
			case 'years':
				return date.getFullYear();
			case 'months':
				return date.getMonth();
			case 'days':
				return date.getDate();
			case 'hours':
				return date.getHours();
			case 'minutes':
				return date.getMinutes();
			case 'seconds':
				return date.getSeconds();
			default:
				return '';
		}
	}

	function moveJumpButtons(){
		if($("#frame-lesson-content").contents().find("#g_jump_button_ref_1").length || $("#frame-lesson-content").contents().find("#g_jump_button_ref_2").length){
			$("#guru-lesson-footer #g_jump_button_ref_1").remove();
			$("#guru-lesson-footer #g_jump_button_ref_2").remove();
			
			$("#frame-lesson-content").contents().find("#g_jump_button_ref_1").appendTo('#guru-lesson-footer');
			$("#frame-lesson-content").contents().find("#g_jump_button_ref_2").detach().appendTo('#guru-lesson-footer');
		}
		else{
			$("#guru-lesson-footer #g_jump_button_ref_1").remove();
			$("#guru-lesson-footer #g_jump_button_ref_2").remove();
		}
	}

	function getComments( lesson_id ) {
		var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&lesson_id=' + lesson_id + '&task=get_comments&ajax=1&Itemid=' + params.item_id;
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( resp ) {
				printComments( resp );
			}
		});
	}

	function printComments( json ) {
		if($modal === null){
			return;
		}

		var $comments = $modal.find('.guru-lesson-nav-comments');
		json = json || {};
		$comments.html( json.commentbox + json.commentlist );
		$comments.find('textarea').on('keydown', commentOnKeydown );
		$comments.find('textarea').on('input', commentOnInput );
		$comments.find('button').on('click', commentOnSubmit );
		$comments.on('click', '.guru-comment-edit', commentOnEdit );
		$comments.on('click', '.guru-comment-save', commentOnEditSave );
		$comments.on('click', '.guru-comment-delete', commentOnDelete );
	}

	function commentOnKeydown( e ) {
		var $comments;
		if ( e.keyCode === 13 && !e.shiftKey ) {
			e.preventDefault();
			e.stopPropagation();
			$comments = $modal.find('.guru-lesson-nav-comments');
			$comments.find('button').click();
			return false;
		}
	}

	function commentOnInput( e ) {
		var value = ( e.target.value || '' ).replace(/^\s+|\s+$/g, '');
		var $button = $( e.target ).siblings('button');
		if ( value.length ) {
			$button.removeAttr('disabled');
		} else {
			$button.attr('disabled', 'disabled');
		}
	}

	function commentOnSubmit( e ) {
		e.preventDefault();
		e.stopPropagation();
		var $button = $( e.currentTarget );
		var $textarea = $button.siblings('textarea');
		var message = $textarea.val();
		var id = $textarea.data('id');
		var url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=insert_comment&lessonid=' + id + '&message=' + encodeURIComponent( message ) + '&ajax=1&Itemid=' + params.item_id;

		var uniq = 'message-' + (new Date()).getTime() + '-' + Math.floor( Math.random() * 1000 );

		var html = [
			'<div id="', uniq, '" class="guru-lesson-comment my-message">',
				'<div class="guru-lesson-comment-wrap">',
					'<div class="guru-lesson-comment-avatar"><span></span></div>',
					'<div class="guru-lesson-comment-body guru-reply-body">',
						'<a class="guru-lesson-comment-name" href="javascript:">You</a> ',
						'<span class="guru-text">', message, '</span>',
						'<div class="guru-lesson-comment-meta">',
							'<span class="guru-lesson-comment-time">a few seconds ago</span>',
						'</div>',
					'</div>',
				'</div>',
			'</div>'
		].join('');

		$textarea.closest('.guru-lesson-comments-form').after( html );
		$textarea.val('');
		$button.attr('disabled', 'disabled');

		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function( json ) {
				if ( json && json.html ) {
					$('#' + uniq ).replaceWith( json.html );	
				}				
			}
		});
	}

	function commentOnEdit( e ) {
		var $button = $( e.currentTarget ),
			$ct = $button.closest('.guru-reply-body'),
			$text = $ct.find('.guru-text'),
			$textarea = $ct.find('textarea');

		$text.hide();
		$textarea.show();
		$ct.find('.guru-comment-edit').hide();
		$ct.find('.guru-comment-delete').hide();
		$ct.find('.guru-comment-save').show();
	}

	function commentOnEditSave( e ) {
		var $button = $( e.currentTarget ),
			$ct = $button.closest('.guru-reply-body'),
			$text = $ct.find('.guru-text'),
			$textarea = $ct.find('textarea'),
			data = $button.data(),
			message = $textarea.val(),
			url;

		// optimistic update
		$text.html( message ).show();
		$textarea.hide();
		$ct.find('.guru-comment-edit').show();
		$ct.find('.guru-comment-delete').show();
		$ct.find('.guru-comment-save').hide();

		message = encodeURIComponent( message );
		url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=edit_comment&lessonid=' + data.id + '&comid=' + data.resultId + '&message=' + message + '&ajax=1&Itemid=' + params.item_id;

		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function() {}
		});
	}

	function commentOnDelete( e ) {
		var $button = $( e.currentTarget ),
			data = $button.data(),
			url;

		$button.closest('.guru-lesson-comment').remove();
		url = params.uri + 'index.php?option=com_guru&controller=guruTasks&task=delete_comment&lessonid=' + data.id + '&uid=' + data.resultUser + '&comid=' + data.resultId + '&ajax=1&Itemid=' + params.item_id;

		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function() {}
		});
	}
	
	function setModalActiveIfIsCurrent(){
		var parent_class = $(".guru-lesson-nav-current").parent().attr('class');

		$( ".active-module" ).removeClass("active-module");

		if(parent_class == "guru-lesson-nav-module"){
			parent = $(".guru-lesson-nav-current").parent();
			//parent.attr('class', "guru-lesson-nav-module active-module");
		}
		else{
			$( ".guru-lesson-nav-module" ).removeClass("active-module");
		}
	}

	window.openMyModalNew = function(width, height, source, _params) {
		params = _params;
		createModal(source);
	};
	
	window.reloadCourseLessonsBarJS = function() {
		params.cid = params.current_lesson;
		getSidebarContent();
	};
});

function redirectCourseAfterComplete(redirect_url){
	lessons_not_viewed = document.getElementsByClassName("not-view");
			
	if(lessons_not_viewed.length == 0){
		window.location.href = redirect_url;
	}
	else{
		//alert(course_not_completed_lang);
		return false;
	}
}
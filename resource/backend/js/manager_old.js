//setup
var multiFinderSearch = false;
var treeMenuLineHeight = 18;
var searchResultCount = 0;
var searchResultPosition = 0;
var entitySelectSuggestSearch = false;
var defMenuWidth = 200;
var defShowHideRibbon = "show";
var cloneTableColumns = []; //sirky sloupcu pro naklonovanou fixedheader 
var isLoadingMore = false; //indikator, zda neni zrovna nacitana tabulka s daty showManagerContent())
var showHideHintsFlag;

if ($.cookie("treeMenuWidth") === null) {
	$.cookie("treeMenuWidth", defMenuWidth, {expires: 365, path: '/'});
}
if ($.cookie("showHideRibbon") === null) {
	$.cookie("showHideRibbon", defShowHideRibbon, {expires: 365, path: '/'});
}

var ie8 = $.browser.msie && $.browser.version == '8.0';
var ie7 = $.browser.msie && $.browser.version == '7.0';
var ie6 = $.browser.msie && $.browser.version == '6.0';
var opera = $.browser.opera;

// cache
preloader = new Image();
preloader.src = '/system/Images/manager/preloader.gif';

var imgplusIcon = "/system/Images/icons/16x16/plus.png";
var imgminusIcon = "/system/Images/icons/16x16/minus.png";

//locales		
var vivoLanguageLocale = jQuery.trim($("html").attr("data-vivo-locale"));
if (vivoLanguageLocale == "") {
	vivoLanguageLocale = jQuery.trim($("meta[name='X-Vivo-Locale']").attr("content"));
};
var vivoLanguage = jQuery.trim($("html").attr("lang"));

//onload
$(document).ready(function() {

	window.loaded = 0;

	$(document).ajaxError(function(e, xhr, settings, exception) {
		//alert(xhr.status+': '+xhr.statusText+'\n'+xhr.responseText);
		displayMessage(xhr.status+': '+xhr.statusText+' ('+xhr.responseText+')', "error");
	});

	if (ie6) {
		if (location.pathname != "/system/manager/") {
			location.pathname = "/system/manager/";
		}
		$("html").add("body").css({"overflow":"hidden"});
		$("#loader").hide();
		$("#wrapper .manager_panel").add($("#header .help")).add($("#logoutDialog-shadow")).add($("#logoutDialog")).hide();
		$("#wrapper .manager_content").css({"margin-left" : "0px", "height" : $(window).height() - 47, "background" : "#fff"});
		$("#header").css({"overflow" : "hidden"});
		$("#header #siteChooser span").css({"background" : "none", "border" : "none", "cursor" : "default"});
		$("#wrapper").show();
		$(window).resize(function() {
			$("#wrapper .manager_content").css({"margin-left" : "0px", "height" : $(window).height() - 47});
		});
		$("#wrapper .manager_content").append("<div class='sup-info'><b>Používáte nekompatibilní prohlížeč. Internet Explorer 6 není podporován.</b><br/><br/><img src='/system/Images/supported_browsers.jpg' alt='' class='fRight' />Mezi podporované prohlížeče patří: <ul><li>Mozilla Firefox 3 a výšší</li><li>Chrome</li><li>Opera</li><li>Internet Explorer 7 a výšší</li></ul><br /><hr / ><b>You are using an incompatible version of internet browser. Internet Exporer 6 is not supported.</b><br/><br/>Supported internet browsers are: <ul><li>Mozilla Firefox 3 and higher</li><li>Chrome</li><li>Opera</li><li>Internet Explorer 7 and higher</li></ul></div>").show();
		startclock();
		return;
	}
	
	//fix console (pro IE)
	
	if (Function.prototype.bind && console && typeof console.log == "object") {
		[
			"log","info","warn","error","assert","dir","clear","profile","profileEnd"
		].forEach(function (method) {
			console[method] = this.bind(console[method], console);
		}, Function.prototype.call);
	}
	
	
	if (opera) {
		$(".browser_compact_item_icon_holder").addClass("browser_compact_item_icon_holder_opera");
	}

	window_check();

	if ($("body").hasClass("manager")) {
		
 		$("html").add("body").css({"overflow":"hidden"});

		$(".explorer_content_with_tree").css({'margin-right' : parseInt($.cookie("treeMenuWidth")) + 7 });
		$(".explorer_tree").css({"width" : parseInt($.cookie("treeMenuWidth"))});

		showLessMore();
		showHints();

		//multifinder
		var _mainFinderMultiId = $("form[name='mainFinderMulti']").attr("id");
		if (_mainFinderMultiId)	{
			window.loaded = 1;
			//init multiFinder
			var entities = getAllEntities(_mainFinderMultiId);
			initMultiFinder(_mainFinderMultiId, entities.length);
			//$(".inputFinder").show();

			//view bookmarks from finder
			//viewBookmark(_mainFinderMultiId);
		}

		//show/hide treeMenu
		if ($.cookie("hideTree") == 1) {
			$(".explorer_content_with_tree").css("margin-right", "7px");
			$(".explorer_tree").css({"width":"0px"});
			$(".explorer_tree_hitcher").addClass("explorer_tree_hitcher_right_closed").removeClass("explorer_tree_hitcher_right_open");
	   	$(".explorer_tree_hitcher_cont").addClass("explorer_tree_hitcher_right_closed").removeClass("explorer_tree_hitcher_right_open");
		}	else {
			$(".explorer_tree_hitcher").removeClass("explorer_tree_hitcher_right_closed").addClass("explorer_tree_hitcher_right_open");
			$(".explorer_tree_hitcher_cont").removeClass("explorer_tree_hitcher_right_closed").addClass("explorer_tree_hitcher_right_open");
			//$(".treeViewCont").show();
		}

		//show content!!!
		$("#wrapper").show();
		
		if ($(".viewFrame").length) {
			hidePanelEditButton({"button": $('a#showHighlightPanels'), "title" : Messages.get('Vivo_CMS_UI_Manager_Explorer_Viewer_wysiwiyg_panelsNo')});
			$(".viewFrame")
				.attr("name", "viewFrame")
				.load(function(){
					$(this.contentDocument).find("body").addClass("iframe");
				});
		}

		if ($(".iframeUrl").length) {
			$(".iframeUrl").each(function(i) {
				var iframe_link = $(this);
				iframe_link.attr("id", "iframeUrl" + i);
				iframe_link.click(function(e) {
					$(".iframeUrl").parent().removeClass("selected");
					$("#iframeUrl" + i).parent().addClass("selected");
					$(".viewFrame").hide();
					$("#viewFrame" + i).show();
					e.preventDefault();
				});
			});
		}

		ribbonInit();

		//content height management
		checkHeight();
		$(window).resize(function() {checkHeight();});

		//init treemenu
		initTreeMenu('#treeMenu');

		//viewer - reload iframe
		$("#reloadViewFrame").click(function() {
			$(".viewFrame").each(function() {
				this.contentWindow.location.reload(true);
			});
		}).attr("href", "javascript:void(0)");
		//viewer - open iframe
		$("#openViewFrame").click(function() {
			$(".viewFrame").each(function() {
				window.open(this.contentWindow.location);
			});
		}).attr("href", "javascript:void(0)");

		//show/hide treeMenu - handler
		$(".explorer_tree_hitcher").dblclick(function() {
			$(".manager_content").addClass("manager_content_default");
			if ($.cookie("hideTree") === null) {
		      //$(".treeViewCont").hide();

		      $(".explorer_content_with_tree").animate({
						       marginRight:7
						    }, 200, "linear", function(){
							    	setFakeDialogPathWidth();
										checkMultiTabs();
										makeTableHeaderFixed();
						    	});

		      $(".explorer_tree").animate({
						       width:0
						    }, 200, "linear", function(){
						    	$.cookie("hideTree", 1, {expires: 365, path: '/'});
						    	//$(".treeViewCont").hide();
						    	$(".explorer_tree_hitcher").addClass("explorer_tree_hitcher_right_closed").removeClass("explorer_tree_hitcher_right_open");
						    	$(".explorer_tree_hitcher_cont").addClass("explorer_tree_hitcher_right_closed").removeClass("explorer_tree_hitcher_right_open");
							    	setFakeDialogPathWidth();
							    	checkHeight();
										checkMultiTabs();
										makeTableHeaderFixed();
						    });

			} else {
				//$(".treeViewCont").show();
				$(".explorer_content_with_tree").animate({
						       marginRight: parseInt($.cookie("treeMenuWidth")) + 7
						    }, 210, "linear", function(){
						    		setFakeDialogPathWidth();
										checkMultiTabs();
										makeTableHeaderFixed();
						    	});

				$(".explorer_tree").animate({
						       width: parseInt($.cookie("treeMenuWidth"))
						    }, 200, "linear", function(){
						      $.cookie("hideTree", null, {expires: 365, path: '/'});
						      $(".explorer_tree_hitcher").removeClass("explorer_tree_hitcher_right_closed").addClass("explorer_tree_hitcher_right_open");
						      $(".explorer_tree_hitcher_cont").removeClass("explorer_tree_hitcher_right_closed").addClass("explorer_tree_hitcher_right_open");
						      //openTree(parent.location.pathname);
							      setFakeDialogPathWidth();
							      checkHeight();
								 		checkMultiTabs();
								 		makeTableHeaderFixed();
						    	});
			}
		});

		$(".explorer_tree_hitcher_cont").draggable({
			axis : 'x',
			zIndex: 1000000,
			cursor: 'move',
			snap: true,
			snapTolerance: 40,
			start : function(event, ui) {
				$(".explorer_tree_hitcher_cont").css({"left" : "", "opacity" : "0.6"});
				$("iframe").css("visibility", "hidden");
			},
			drag : function(event, ui) {
				$(".explorer_tree_hitcher_cont").css({"left" : ""});
			},
			stop : function(event, ui) {
				off = ui.offset;
				var mwidth = $(window).width() - off.left;
				$(".explorer_content_with_tree").css({'margin-right' : (mwidth < 0) ? 7 : mwidth });
				$(".explorer_tree").css({"width" : (mwidth - 7 < 0) ? 0 : mwidth - 7});
				$(".explorer_tree_hitcher_cont").css({"left" : ""});

				//$(".footer_status").html(mwidth);

				if (mwidth - 7 <= 0) {

					$.cookie("hideTree", 1, {expires: 365, path: '/'});
		    	$(".explorer_tree_hitcher").addClass("explorer_tree_hitcher_right_closed").removeClass("explorer_tree_hitcher_right_open");
		    	$(".explorer_tree_hitcher_cont").addClass("explorer_tree_hitcher_right_closed").removeClass("explorer_tree_hitcher_right_open");
		    	checkHeight();
				} else {
					$.cookie("hideTree", null, {expires: 365, path: '/'});
		      $(".explorer_tree_hitcher").removeClass("explorer_tree_hitcher_right_closed").addClass("explorer_tree_hitcher_right_open");
		      $(".explorer_tree_hitcher_cont").removeClass("explorer_tree_hitcher_right_closed").addClass("explorer_tree_hitcher_right_open");
		      checkHeight();
				}
				$.cookie("treeMenuWidth", ((mwidth - 7 < 0) ? defMenuWidth : mwidth - 7), {expires: 365, path: '/'});
				//openTree(parent.location.pathname);
				setFakeDialogPathWidth();
				checkMultiTabs();
				makeTableHeaderFixed();
				$("iframe").css("visibility", "visible");
			}
		});

		//system messages - error, warning, info
		$(".message_info").slideDown("slow", function() {checkHeight();}).find("a.message_close").attr("href", "javascript: void(0)").click(function() {$(".message_info").slideUp("fast", function() {$(this).remove(); checkHeight();});});
		$(".message_warning").slideDown("slow", function() {checkHeight();}).find("a.message_close").attr("href", "javascript: void(0)").click(function() {$(".message_warning").slideUp("fast", function() {$(this).remove(); checkHeight();});});
		$(".message_error").slideDown("slow", function() {checkHeight();}).find("a.message_close").attr("href", "javascript: void(0)").click(function() {$(".message_error").slideUp("fast", function() {$(this).remove(); checkHeight();});});
		
		
		//tables
		$("table.vivo_cms_ui_manager_explorer_audit").addClass("vivo-table-fixedheader vivo-table-columnhide");
		
		//tableGrid tr mouseover
		initTableStripes();
		initTableNavigation();
		makeTableHeaderFixed();
		//initTableResizable();
		initTableColumnHiding();

		//browser compact
		$(".browser_compact_item").mouseover(function() {
			$(this).find(".browser_compact_item_icon_holder").addClass("browser_compact_item_icon_holder_sel");
		});

		$(".browser_compact_item").mouseout(function() {
			$(this).find(".browser_compact_item_icon_holder").removeClass("browser_compact_item_icon_holder_sel");
		});

		//start clock
		startclock();

		//manager logout window
		$(window).resize(function() {shadowWindow("#logoutDialog", ".manager_content");});
		shadowWindow("#logoutDialog", ".manager_content");
		
		$('.browsersTrigger').click(function(){
			$(this).toggleClass('open');
			$('.saveButtons.browsers').toggle();
			shadowWindow("#logoutDialog", ".manager_content", false);
		})
			
		checkWidthItemInCompactMode();
	}

	//selectbox

	$('select').not('.simple-selectbox').each(function() {
		if (!$(this).attr("size") && !opera) {
			$(this).selectbox();
		}
		if (opera) {
			var $publ = $(this).find("option[publ|=1]");
			$publ.text($publ.text() + " [P]");
		}
	});
		
	//umoznuje multiselectum tvorenym pomoci jquery.multiselect2side.js zobrazovat napovedu
	$('select[multiple="multiple"]').wrap("<div class='withHint'></div>");
	
	//input filetype
	filestyle();

	initTableInputs(".tableGridForm");
	
	//datetimepicker

	$.datepicker.regional['cs'] = {
		monthNames: ['leden','únor','březen','duben','květen','červen', 'červenec','srpen','září','říjen','listopad','prosinec'],
		monthNamesShort: ['led','úno','bře','dub','kvě','čer', 'čvc','srp','zář','říj','lis','pro'],
		dayNames: ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'],
		dayNamesShort: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
		dayNamesMin: ['ne','po','út','st','čt','pá','so'],
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		prevText: '&#x3c;Dříve', prevStatus: '',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
		nextText: 'Později&#x3e;', nextStatus: '',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
		currentText: 'Nyní', currentStatus: '',
		todayText: 'Dnes', todayStatus: '',
		clearText: '-', clearStatus: '',
		closeText: 'Vložit', closeStatus: '',
		yearStatus: '', monthStatus: '',
		weekText: 'Týd', weekStatus: '',
		dayStatus: 'DD d MM',
		isRTL: false
	};

	$.datepicker.regional['sk'] = {
		monthNames: ['Január','Február','Marec','Apríl','Máj','Jún', 'Júl','August','September','Október','November','December'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Máj','Jún', 'Júl','Aug','Sep','Okt','Nov','Dec'],
		dayNames: ['Nedel\'a','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota'],
		dayNamesShort: ['Ned','Pon','Uto','Str','Štv','Pia','Sob'],
		dayNamesMin: ['Ne','Po','Ut','St','Št','Pia','So'],
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		prevText: '&#x3c;Predchádzajúci', prevStatus: '',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
		nextText: 'Nasledujúci&#x3e;', nextStatus: '',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
		currentText: 'Teraz', currentStatus: '',
		todayText: 'Dnes', todayStatus: '',
		clearText: '-', clearStatus: '',
		closeText: 'Vložiť', closeStatus: '',
		yearStatus: '', monthStatus: '',
		weekText: 'Ty', weekStatus: '',
		dayStatus: 'DD d MM',
		isRTL: false
	};

	$.datepicker.regional['en'] = {
		dateFormat: 'yy-mm-dd',
		closeText: 'Insert'
	};

	$.timepicker.regional['cs'] = {
		timeOnlyTitle: 'Zvolte čas',
		timeText: 'Čas',
		hourText: 'Hodiny',
		minuteText: 'Minuty',
		secondText: 'Sekundy',
		currentText: 'Nyní',
		closeText: 'Zavřít',
		ampm: false
	};

	$.timepicker.regional['sk'] = {
		timeOnlyTitle: 'Zvolte čas',
		timeText: 'Čas',
		hourText: 'Hodiny',
		minuteText: 'Minúty',
		secondText: 'Sekundy',
		currentText: 'Teraz',
		closeText: 'Zavrieť',
		ampm: false
	};

	$.timepicker.setDefaults($.timepicker.regional[vivoLanguageLocale]);
	$.datepicker.setDefaults($.datepicker.regional['']);

	//date
	$('input.date').each(function() {
		$(this).datepicker({
			duration: 0,
			constrainInput: false,
			showOn: $(this).hasClass('hasTriggerIcon') ? 'button' : 'focus',
			buttonImage: '/system/Images/icons/24x24/calendar.png',
			buttonImageOnly: $(this).hasClass('hasTriggerIcon') ? true : false
		});
	});

	//datetime
	$('input.datetime').each(function() {
		$(this).datetimepicker({
			constrainInput: false,
			showOn: $(this).hasClass('hasTriggerIcon') ? 'button' : 'focus',
			buttonImage: '/system/Images/icons/24x24/calendar.png',
			buttonImageOnly: $(this).hasClass('hasTriggerIcon') ? true : false,
			showSecond: true,
			timeFormat: 'hh:mm:ss'
		});
	});

	//time
	$('input.time').each(function() {
		$(this).timepicker({
			constrainInput: false,
			showOn: $(this).hasClass('hasTriggerIcon') ? 'button' : 'focus',
			buttonImage: '/system/Images/icons/24x24/calendar.png',
			buttonImageOnly: $(this).hasClass('hasTriggerIcon') ? true : false,
			showSecond: true,
			timeFormat: 'hh:mm:ss'
		});
	});

	$.timepicker.setDefaults($.timepicker.regional[vivoLanguageLocale]);
	$.datepicker.setDefaults($.datepicker.regional[vivoLanguageLocale]);


	//hide loader info
	
	$("#loader").hide();
	jQuery(document)
		.ajaxStart(function(){
			$("#loader").show();
		})
		.ajaxSuccess(function(){
	  $("#loader").hide();
	 });
	
	//focus username on logon screen
	$("#logonUsername").focus();
		
	/*** TAB SECURE ***/

	//nahrazeni selectboxu
	$('#secure select').hide().children('option').each(function(){
		var r = $("<a href=\""+$(this).parent().attr('name')+"\" name=\""+$(this).attr('value')+"\" class=\"option\">"+$(this).text()+"</a>", this).replaceAll(
			$(this).clone().appendTo($(this).parent().parent())
		).click(function(e){
			e.preventDefault();
			
			if (ie7 && $(this).attr('href').lastIndexOf('/') > 0) {
				var selectName = $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
				$(document.forms['secure'].elements[selectName]).val($(this).attr('name')).change();
			
			} else {
				$(document.forms['secure'].elements[$(this).attr('href')]).val($(this).attr('name')).change();
			}
			$(this).parent().children('a.option').removeClass('active');
			$(this).addClass('active');
		});
		if ($(this).attr('value') == $(this).parent().val()) {
			r.addClass('active');
		}
	});


	//Pridani/odebrani uzivatele
	var clickUserOrGroupA = function(e){
		$(this).hide();
		$(this).siblings('.preloader').show();
	};
	$('#secure tr.userOrGroup td a').click(clickUserOrGroupA);
	
	var vivoajaxifyUserOrGroupA = function(target, data){
		
		var remove = data;

		$(target).siblings('.preloader').hide();
        if ($(target).parents('tr').is('.ugWhisperer')) {
        	var callback = function(data) {
        		    $('#secureFieldsTableContainer').html(data);
        		    $('#secure tr.userOrGroup td a').click(clickUserOrGroupA);
                	$('#secure tr.userOrGroup td a').vivoajaxify(vivoajaxifyUserOrGroupA);
                	$('#ug-whisperer-input').keyup(ugWhispererInputKeyup).blur(ugWhispererInputBlur);
        		};
        	$.post(getSecureViewFieldsAction, {async: 1}, callback, 'json');
        } else if ($(target).parents('tr').is('.secureDisplay-1')) {
			if (remove) {
				$(target).closest('tr').find('a.addRoleLink').attr('title', $(target).parents('tr').find('a.addRoleLink span.remove').attr('title'));
				$(target).closest('tr').find('a.addRoleLink span.remove').show();
				$(target).closest('tr').find('a.addRoleLink span.add').hide();
				$(target).closest('tr.userOrGroup').addClass('inRole');
			} else {
				$(target).closest('tr').find('a.addRoleLink').attr('title', $(target).parents('tr').find('a.addRoleLink span.add').attr('title'));
				$(target).closest('tr').find('a.addRoleLink span.remove').hide();
				$(target).closest('tr').find('a.addRoleLink span.add').show();
				$(target).closest('tr.userOrGroup').removeClass('inRole');
			}
		} else if ($(target).parents('tr').is('.secureDisplay-2')) {
			if (remove) {
				$(target).attr('title', $(target).find('span.remove').attr('title'));
				$(target).find('span.remove').show();
				$(target).find('span.add').hide();
			} else {
				$(target).attr('title', $(target).find('span.remove').attr('title'));
				$(target).find('span.remove').hide();
				$(target).find('span.add').show();
			}
		}

		$(target).show();
	};
	
	$('#secure tr.userOrGroup td a').vivoajaxify(vivoajaxifyUserOrGroupA);

	//Mod zobrazeni
	$("#secure .dialogHeaderHolder a.js-switch").click(function(e) {
		e.preventDefault();
		$.cookie("secureDisplay", $(this).attr('rel'));
		showSecureDisplay($(this).attr('rel'));
	});
	//END TAB SECURE
	
	jsonInputInit();
	
	//hint box (help)
	hintBox();

	//choose site ... menu
	siteMenu();

	//choose ux ... menu
	uxMenu();

	//entitySelect
	entitySelectSuggest();

	keywordSelectSuggest();

	//check header width
	$(window).resize(function() {
		checkHeaderWidth();
	});
	checkHeaderWidth();

	//resize of multifinder
	if (_mainFinderMultiId)	{
		$(window).resize(function() {
			reduceMultifinder(_mainFinderMultiId);
		});
		reduceMultifinder(_mainFinderMultiId);
	}

	$(window).resize(function() {
		setFakeDialogPathWidth();
	});
	setFakeDialogPathWidth();

	$(window).resize(function() {
		checkInputWithIconWidth();
	});
	checkInputWithIconWidth();

	$(window).resize(function() {
		checkMultiTabs();
	});
	checkMultiTabs();

	//colorpicker
	$("input.color").ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});

	$(window).resize(function() {
		managerPanelIconSlider();
	});

	managerPanelIconSlider();
	
	autoloadTable();
	
	//exception popup
	var $exception = $("#exception")
	if ($exception.length) {
	   openModalWindow({
            width: 750,
            height: 220,
            title: "Exception",
            fnc_editor: function() {
                return "<div class='exception'>" + $exception.html() + "</div>";
            },
            fnc_editor_exec: function() {
                $(".vivoModalContent .exception").parent().parent().addClass("exceptionw");
                var exception_window_holder = $(".exceptionw");
                var exception_window = exception_window_holder.parent();
                var exception_detail = exception_window_holder.find("#error_details");
                exception_detail.hide();
                exception_window_holder.find("#tech_info").addClass("closed").click(function(e) {
                    var _this = $(this);
                    if ((_this).is(".closed")) {
                        _this.removeClass("closed").addClass('open');
                        exception_detail.show();
                    } else {
                        _this.removeClass("open").addClass('closed');
                        exception_detail.hide();
                    }
                });
            },
            btn_close_text: "OK" 
        });
        $exception.remove(); 
	}
	
	//click on logo
	$("#header .logo").click(function(e) {
	    e.preventDefault();
	    openModalWindow({
            width: 360,
            height: 270,
            title: "Vivo",
            fnc_editor: function() {
                return "<img src='/system/Images/manager/default/logon_theme.png' alt='VIVO logos' width='360' height='100'/><p class='window-about'>" + $(".footer_status").text() + "<br /><br /><span style=\"font-size: 12px\">developed by Tomáš&nbsp;Zajíček, Zdeněk&nbsp;Staněk, Miroslav&nbsp;Hájek, Peter&nbsp;Krajcár, Tomáš&nbsp;Kormaňák, Tomáš&nbsp;Plecháč, Gabriela&nbsp;Čížková and Jiří&nbsp;Tonar</span></p>";
            },
            fnc_editor_exec: function() {
                $(".vivoModalContent .window-about").parent().parent().addClass("window-about-holder").css("height", "254px");
                $(".modalWindow .action_storno, .modalWindow .tableGridForm").hide();

            },
            resizable: false 
        });
	})
	
	if ($('#editor').length) {
		window.setTimeout(disableEditor, 500);
	}
	
	$('#editor #tabs-multi a').click(function() {
		var scrolled_pos_top = $('.tabMainContent').scrollTop();
		$.cookie('scrolledPosition', scrolled_pos_top, {expires: 356, path: '/'});
	});
	
	if ($.cookie('scrolledPosition')) {
		reScrollToTabs();
	}
	
	//Pro obrazovku s nastavenim User Profile - hlidani checkboxu pro zobrazeni/skryti hitnu + mene dulezitych polozek v editaci
	if (($('input[name="showLessImportantFields"]').length > 0) && ($('input[name="showHints"]').length > 0)) {
		$('#lessShowHide').click(function() {
			var _this_input = $('input[name="showLessImportantFields"]');
			if (_this_input.is(':checked')) {
				_this_input.attr('checked', false);
			} else {
				_this_input.attr('checked', true);
			}
		});
		$('#hintsShowHide').click(function() {
			var _this_input = $('input[name="showHints"]');
			if (_this_input.is(':checked')) {
				_this_input.attr('checked', false);
			} else {
				_this_input.attr('checked', true);
			}
		});
	}
	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function(elt /*, from*/) {
			var len = this.length;
	
			var from = Number(arguments[1]) || 0;
			from = (from < 0)
			? Math.ceil(from)
			: Math.floor(from);
			if (from < 0)
			from += len;
		
			for (; from < len; from++) {
				if (from in this && this[from] === elt)
				return from;
			}
			return -1;
		};
	}
	
}); //EO onload

//---funkce

//disabled inputs
function disableEditor() {
	if ($('#editor').attr('rel') == 'disabled') {
		$('#editor .tabMainContentHolder').append('<div class="disabledLayer"></div>');
		$('#editor #tabContainer-multi ul.tabs li:last-child, #editor .saveButtons, #editor .hintBox').remove();
		$('#editor .tableGridForm :input').each(function() {
			var _this = $(this);
			_this.attr('readonly','readonly').attr('disabled','disabled');
		});
		
		disabledPageHeight();
		
		if (ie7 || ie8) {
			$('#editor .tabMainContentHolder .disabledLayer').show();
		}
		else {
			$('#editor .tabMainContentHolder .disabledLayer').fadeIn();
		}
	}
}

//check a set height of disabledLayer
function disabledPageHeight() {
	$('#editor .tabMainContentHolder .disabledLayer').height($('#editor .tabMainContentHolder form').height());
}

//scroll to tabs if click to another
function reScrollToTabs() {
	$('.tabMainContent').scrollTo($.cookie('scrolledPosition'));
	$.cookie('scrolledPosition', null, {expires: 356, path: '/'});
}


//prevent opening more backend windows
function window_check() {
    if (window != window.parent) return;
    var prefix = "desktop:"  + location.hostname.replace(".", "_") + ":";        
    if (window.name && (window.name.indexOf(prefix) < 0)) {
        window.name = "";
    } 
	if (!window.name || window.name == "") {
		window.name = prefix + String((new Date()).getTime()).replace(/\D/gi,'');
		$.cookie("window.name", window.name, {path: '/', domain: location.hostname});
	} else if ((window.name.indexOf("desktop") == 0) && (window.name != $.cookie("window.name"))) {
    	//alert('"' + window.name + '","' + $.cookie("window.name") + '"');
        openModalWindow({
            width: 320,
            height: 120,
            title: "Oops...",
            fnc_editor: function() {
                return "<p class='window-check-message'><img src='/system/Images/icons/48x48/oops.png' alt='' width='48' height='48' />" + Messages.get("Vivo_CMS_UI_Content_Manager_Layout_window_check") + "</p>";
            },
            fnc_editor_exec: function() {
                $(".vivoModalContent .window-check-message").parent().parent().addClass("window-check-message-holder").css("height", "104px");
                $(".modalWindow .action_storno, .modalWindow .modalWindowClose, .modalWindow .tableGridForm").hide();

            },
            resizable: false 
        });
        return;
    }
	window.setTimeout(window_check, 1000);
}

//datagrid inputs focus
function initTableInputs(_selector) {
	$(_selector + " input").add($(_selector + " select")).add($(_selector + " textarea"))
	.each(function() {
		if ($(this).attr("readonly") && !$(this).hasClass("custom_selectbox_fake")) {
			$(this).addClass("readonlyInput").click(function() {$(this).blur();});
		}
	})
	.focus(function() {
		if (!$(this).hasClass("file") && !$(this).hasClass("radio") && !$(this).hasClass("checkbox") && !$(this).hasClass("icon") && (!($.browser.msie) || ($.browser.msie && !$(this).is("select")))) {
			$(this).addClass("selectedInput");
		}
	});

	$(_selector + " input").add($(_selector + " select")).add($(_selector + " textarea")).blur(function() {
		$(this).removeClass("selectedInput");
	});
}

//--soupatko pro zalozky v multidocumentu - init
function checkMultiTabs() {
	if ($("#tabs-multi").is(":visible")) {
		var li_width = new Array();
		var whole_width = new Number;
		var curDocument = $("#entity_uuid").val(); //uuid dokumentu
		var curPosition = ($.cookie("multiTabPosition") === null) ? 0 : $.cookie("multiTabPosition");
		var curPosotionParams = new Array();
		if (curPosition != 0) {
			curPosotionParams = curPosition.split(":");
		}

		var multiTabWidth = $("#tabs-multi").outerWidth(false);
		$("#tabs-multi li").each(function(i) {
											li_width.push($(this).outerWidth(true));
											whole_width += parseInt($(this).outerWidth(true));
										 });

		whole_width = whole_width + 4; // tabsHolder-multi left padding

		$("#tabs-multi .tabsHolder-multi")
			.css({"width" : whole_width + "px"});

		if (!$("#tabs-multi .rightScroller").length && multiTabWidth < whole_width) {
			$("#tabs-multi").addClass("tabs-multi");
			$("#tabs-multi .tabsHolder-multi")
				.before($(document.createElement("div")).addClass("leftScroller"))
				.before($(document.createElement("div")).addClass("rightScroller"));
		}

		if ($("#tabs-multi").hasClass("tabs-multi") && multiTabWidth >= whole_width) {
			$("#tabs-multi").removeClass("tabs-multi");
		}

		multiTabWidth = $("#tabs-multi").outerWidth(false);


		$("#tabs-multi .rightScroller").css({"left" : ($("#tabs-multi").outerWidth(false) + $("#tabs-multi .rightScroller").width()) + "px"});


		if (multiTabWidth < whole_width) {
			$("#tabs-multi .rightScroller").addClass("rightScrollerActive");
		}

		if (parseInt($("#tabs-multi .tabsHolder-multi").css("margin-left")) < 0) {
			$("#tabs-multi .leftScroller").addClass("leftScrollerActive");
		}

		if ($("#tabs-multi .rightScroller").hasClass("rightScrollerActive")) {
			$("#tabs-multi .rightScroller").bind('click', function() {
																   multiTabMoveRight(whole_width);
																   });
		}

		if ($("#tabs-multi .leftScroller").hasClass("leftScrollerActive")) {
			$("#tabs-multi .leftScroller").bind('click', function() {
																   multiTabMoveLeft(whole_width);
																   });
		}

		if (curPosotionParams[0] == curDocument && curPosotionParams[1] != 0) {
			$("#tabs-multi .tabsHolder-multi").css({"margin-left" : curPosotionParams[1] + "px"});
		}
		if (curPosotionParams[0] != curDocument) {
			$.cookie("multiTabPosition", null, {expires: 365, path: '/'});
		}
	}
}

//--soupatko pro zalozky v multidocumentu - doprava
function multiTabMoveRight(whole_width) {
	var multiTabWidth = $("#tabs-multi").outerWidth(false);
	var curDocument = $("#entity_uuid").val(); //uuid dokumentu
	var mLeft = parseInt($("#tabs-multi .tabsHolder-multi").css("margin-left")) - 300;
	var maxMLeft = multiTabWidth - whole_width;
	mLeft =( mLeft < maxMLeft) ? maxMLeft : mLeft;

	$("#tabs-multi .tabsHolder-multi")
		.animate(
				 {"margin-left" : mLeft + "px"},
				 200,
				 function() {
					if (parseInt($("#tabs-multi .tabsHolder-multi").css("margin-left")) < 0) {
						$("#tabs-multi .leftScroller").addClass("leftScrollerActive");
						$("#tabs-multi .leftScroller").unbind('click');
						$("#tabs-multi .leftScroller").bind('click', function() {
																			   multiTabMoveLeft(whole_width);
																			   });
					}
					 if (multiTabWidth >= whole_width + parseInt($("#tabs-multi .tabsHolder-multi").css("margin-left"))) {
							$("#tabs-multi .rightScroller").removeClass("rightScrollerActive");
							$("#tabs-multi .rightScroller").unbind('click');
					}
					$.cookie("multiTabPosition", curDocument+":"+mLeft, {expires: 365, path: '/'});
				})
}

//--soupatko pro zalozky v multidocumentu - doleva
function multiTabMoveLeft(whole_width) {
	var multiTabWidth = $("#tabs-multi").outerWidth(false);
	var curDocument = $("#entity_uuid").val(); //uuid dokumentu
	var mLeft = parseInt($("#tabs-multi .tabsHolder-multi").css("margin-left")) + 300;
	mLeft = (mLeft > 0) ? 0 : mLeft;
	$("#tabs-multi .tabsHolder-multi")
	.animate(
			 {"margin-left" : mLeft + "px"},
			 200,
			 function() {
				if (parseInt($("#tabs-multi .tabsHolder-multi").css("margin-left")) >= 0) {
					$("#tabs-multi .leftScroller").removeClass("leftScrollerActive");
					$("#tabs-multi .leftScroller").unbind('click');
				}
				if (multiTabWidth < whole_width) {
					$("#tabs-multi .rightScroller").addClass("rightScrollerActive");
					$("#tabs-multi .rightScroller").unbind('click');
					$("#tabs-multi .rightScroller").bind('click', function() {
																   multiTabMoveRight(whole_width);
																   });
				}
				$.cookie("multiTabPosition", curDocument+":"+mLeft, {expires: 365, path: '/'});
			})
}

function checkInputWithIconWidth() {
	$(".inputWithIcon").each(function() {
		$(this).css("width", $(this).parent().width() - 25);
	});
}

function setFakeDialogPathWidth() {
	if ($("#fakePathHolder").is(":visible")) {
		var firstItemOnRightSide = $("#firstRDialogItem").offset();
		var fakePath = $("#fakePathHolder .fakePath").offset();
		var newFakePathWidth = firstItemOnRightSide.left - fakePath.left - 30;

		$("#fakePathHolder .fakePath").css({"width" : newFakePathWidth + "px"});
	}
}

//zkracovani multifinderu pri malem rozliseni obrazovky - init
function reduceMultifinder(_id) {
	var finderData = new Array();
	var allChildWidth = 0;
	var _children = 0;
	$("#finderMulti_" + _id).children().each(function(i) {
		var child = $(this);
		if (child.hasClass("finderMultiPartDir") || child.hasClass("finderMultiPart")) {
			child.addClass("f-"+i);
			finderData["f-"+i] = new Array();
			finderData["f-"+i]['val'] = child.width();
			if (i > 0) {
				finderData["f-"+i]['sum'] = finderData["f-"+ (i - 1)]['sum'] + child.width();
			} else {
				finderData["f-"+i]['sum'] = child.width();
			}
			allChildWidth += child.width();
			_children++;
		}
	});

	for (var i = _children - 1; i >= 0; i--) {
		if (i == _children - 1) {
			finderData["f-"+i]["sum2"] = finderData["f-"+i]["val"];
		} else {
			finderData["f-"+i]["sum2"] = finderData["f-"+ (i + 1)]["sum2"] + finderData["f-"+i]["val"];
		}
	}

	for (var i = 0; i < _children; i++) {
		showHideMultifinderParts(_id, i, finderData);
	}

	if ($("#header .user-icon").text().indexOf("zstanek") > 0) {
	}
}

//zkracovani multifinderu pri malem rozliseni obrazovky - vykreslovani
function showHideMultifinderParts(_id, _part, childData) {
	var finderWidth = $("#finderMulti_" + _id).width();
	var finderRightPadd = 50;

	if (childData['f-' + _part]['sum2'] >= finderWidth - finderRightPadd) {
		$("#finderMulti_" + _id + " .f-" + _part).hide();
	} else {
		if ($("#finderMulti_" + _id + " .f-" + _part).not(":visible"))
			$("#finderMulti_" + _id + " .f-" + _part).show();
	}
}

//zkracovani hlavicky pri malem rozliseni obrazovky
function checkHeaderWidth() {
	var $logo = $("#header .logo");
	var $path = $("#header .path");
	var $language = $("#header .language");
	var $ux = $("#header .ux_profile");
	var $user = $("#header .user");
	var $user_logout = $("#header .user-logout");
	var $time = $("#header .time");
	var $help = $("#header .help");
	var $header = $("#header");
	var path_padd = 15;
	var help_padd = 5;
	var lang_padd = 30;
	var ux_padd = 30;
	var user_padd = 27;
	var time_padd = 49;

	var _width = $header.width() - ($logo.width() + $path.width() + path_padd + $language.width() + lang_padd + $ux.width() + ux_padd + $user.width() + user_padd + $user_logout.width() + $time.width() + time_padd + $help.width() + help_padd);

	if (_width <= 1) {
		$language.hide();

				if (_width < 2 - ($language.width() + lang_padd)) {
					$ux.hide();
						if (_width < 2 - ($language.width() + lang_padd + $ux.width() + ux_padd)) {
							$user.hide();
							if (_width < 2 - ($language.width() + lang_padd + $ux.width() + ux_padd + $user.width() + user_padd)) {
								$user_logout.hide();
									if (_width < 2 - ($language.width() + lang_padd + $ux.width() + ux_padd + $user.width() + user_padd + $user_logout.width())) {
										$time.hide();
										if (_width < 2 - ($language.width() + lang_padd + $ux.width() + ux_padd + $user.width() + user_padd + $user_logout.width() + $time.width() + time_padd)) {
											$help.hide();
										} else {
											if ($help.not(":visible"))
												$help.show();
										}
									} else {
										if ($time.not(":visible"))
											$time.show();
										if ($help.not(":visible"))
											$help.show();
									}
							} else {
								if ($user_logout.not(":visible"))
									$user_logout.show();
								if ($time.not(":visible"))
									$time.show();
								if ($help.not(":visible"))
									$help.show();
							}
						} else {
							if ($user.not(":visible"))
								$user.show();
							if ($user_logout.not(":visible"))
								$user_logout.show();
							if ($time.not(":visible"))
								$time.show();
							if ($help.not(":visible"))
								$help.show();
						}
				} else {
					if ($ux.not(":visible"))
					$ux.show();
					if ($user.not(":visible"))
						$user.show();
					if ($user_logout.not(":visible"))
						$user_logout.show();
					if ($time.not(":visible"))
						$time.show();
					if ($help.not(":visible"))
						$help.show();
				}

	} else {
		if ($language.not(":visible"))
			$language.show();
		if ($ux.not(":visible"))
			$ux.show();
		if ($user.not(":visible"))
			$user.show();
		if ($user_logout.not(":visible"))
			$user_logout.show();
		if ($time.not(":visible"))
			$time.show();
		if ($help.not(":visible"))
			$help.show();
	}
}

//input type file
function filestyle() {
	$("input[type=file]").filestyle({
		image: "/system/Images/icons/16x16/Folder.png",
		imageheight : 26,
		imagewidth : 25,
		width : 250
	});
}

//client-side messages
function displayMessage(_text, _type) { //info, error, warning
	$(".message_info").slideUp(0, function() {$(this).remove(); checkHeight();});
	$(".message_error").slideUp(0, function() {$(this).remove(); checkHeight();});
	$(".message_warning").slideUp(0, function() {$(this).remove(); checkHeight();});
	$("#header").after($(document.createElement("div")).addClass("main_message").addClass("message_" + _type).html("<span>" +_text + "</span><a href='' class='message_close'><span>x</span></a><div class='clear'></div>"));
	$(".message_" + _type).slideDown("slow", function() {checkHeight();}).find("a.message_close").attr("href", "javascript: void(0)").click(function() {$(".message_" + _type).slideUp("fast", function() {$(this).remove(); checkHeight();});});
}

//dotahovani obsahu content browseru
var smc_sending = false;
function showManagerContent(obj, count, highlightNew, act) {
	var $obj = $(obj);
	var act = (typeof act != "undefined" && act != false) ? act : window.contentManagerGetPath.viewContents;
	var query_params = [count];
	var query = 'async=1&act=' + act;
	for (var i = 0; i < query_params.length; i++)
    query += '&args[]=' + encodeURIComponent(query_params[i]);
	
	var contentManagerContent = "";
	var sending = false;
	if (!smc_sending) {
		$.ajax({
		  url: window.location.pathname + "?" + query,
		  type: "GET",
		  dataType: "html",
		  beforeSend: function(jqXHR, textStatus) {
		  	smc_sending = true;
		  	isLoadingMore = true;
		  },
		  error: function(jqXHR, textStatus, errorThrown) {
		  	//displayMessage("ajaxcall: " + textStatus, "error");
		  	isLoadingMore = false;
		  },
		  success: function(res, textStatus, jqXHR) {
		  	if(res && res !== "null") {
					if (res.indexOf("null") > 0 && res.length - res.indexOf("null") == 4) { ///kvuli IE, ktera z nejakeho duvodu dava nakonec "null"
						contentManagerContent = res.substring(0, res.indexOf("null")) ;
					} else {
						contentManagerContent = res;
					}
		  	}
		  },
		  complete: function(jqXHR, textStatus) {
		  	
		  	$(".vivo-table-showmore tr").removeClass("new");
				if (highlightNew) {
					$(".vivo-table-showmore tr").removeClass("last");
					$obj.find("tbody tr:last-child").addClass("last");
				}
				
				if (contentManagerContent) {
					$obj.find("tbody tr:last-child").after(contentManagerContent);
					$obj.find("tbody tr:last-child").prevUntil(".last").addClass("new");
					$obj.find("tbody tr:last-child").addClass("new");
							
					initTableStripes();
					initTableNavigation();
					
					//initTableResizable();
					
					initTableColumnHiding(true); //pouze kvuli sryvani polozek nactenych ajaxem
					makeTableHeaderFixed(); //udrzuje spravne sirky naklonovanych sloupcu fixni hlavicky tabulky
				}
				
			
				if(typeof initShowContextMenu === 'function') {
					initShowContextMenu();
				}
				
		  	smc_sending = false;
		  	isLoadingMore = false;
		  }
		});
	}
	//var contentManagerContent = action(window.contentManagerGetPath.output, 'viewContents', count);	
}

//hover efekt u tabulky
function initTableStripes() {
	$(".tableGrid tr:not(.logDetailTr)").hover(
		function() {
			$(this).find("td").addClass("tr_selected");
		},
		function() {
			$(this).find("td").removeClass("tr_selected");
		}
	);
}

//navigace tabulkou pomoci klaves
function initTableNavigation() {
	jQuery.tableNavigation({
			table_selector: 'table.vivo-table-navigateable',
			row_selector: 'table.vivo-table-navigateable tbody tr',
			table_holder_selector: '.tabMainContent',
			selected_class: 'selected',
			activation_selector: 'a.action_link',
			bind_key_events_to_links: true,
			focus_links_on_select: true,
			disable_links: false,
			select_event: 'click',
			activate_event: 'dblclick',
			activation_element_activate_event: 'click',
			scroll_overlap: 30,
			cookie_name: null,
			focus_tables: true,
			focused_table_class: 'focused',
			jump_between_tables: false,
			disabled: false,
			on_activate: null,
			on_select: null
		});		
}

//skryvani sloupecku tabulky (s vazbou na makeTableHeaderFixed() pres table_clone)
function initTableColumnHiding(only_new) {
	var only_new = (typeof only_new != "unlimited") ? only_new : false;
	var table_selector = "table.vivo-table-columnhide";
	var  table = $(table_selector);
	if (!table.length) return;
	var header = table.find("thead tr:first");
	var th = header.find("th");
	var table_clone = $("#tableClone");
	var header_clone = table_clone.find("tr");
	if (table_clone.length) {
		th = table_clone.find("th");
	}
	
	//rezim, kdy se pouze kontroluje, zda nejaky sloupec neni skryt a pokud je, 
	//tak pri ajaxovem docitano dalsich radku tabulky jsou prislusne sloupce skryty
	if (only_new) {
		for (i = 0; i < th.length; i++) {
			if ($(th[i]).is(".hidden-column")) {
				table.find('.new td:nth-child('+(i + 1)+')').addClass("hidden-column").removeClass("visible-column");
			}
			if ($(th[i]).is(".visible-column")) {
			 	table.find('.new td:nth-child('+(i + 1)+')').removeClass("hidden-column").addClass("visible-column");
			}
		}
		return;	
	}
		
  var windowwidth;
  var windowheight;
  var checkmenu;
       
	th.bind("contextmenu",function(e){
		windowwidth = $(window).width();
		windowheight = $(window).height();
		checkmenu = 1;
		
		var thcontext_menu_mask = $("#thcontext-menu-mask");
		if (!thcontext_menu_mask.length) {
			$("<div id='thcontext-menu-mask'></div>").appendTo(document.body);
			thcontext_menu_mask = $("#thcontext-menu-mask");
		}		
		thcontext_menu_mask
		.css({
			'height': windowheight,
			'width': windowwidth
		})
		.bind("contextmenu",function(){
			$(this).height(0);
			$(this).width(0);
			$('#thcontext-menu').hide();
			checkmenu = 0;
			return false;
		})
		.live("click", function(){
			$(this).height(0);
			$(this).width(0);
			$('#thcontext-menu').hide();
			checkmenu = 0;
			return false;
		});
		
		var thcontext_menu = $("#thcontext-menu");
		//vytvorime context menu
		if (!thcontext_menu.length) {
			var th_checks = function() {
				var html = "<table>";
				var hidden_column = false;
				var hidden_not_allowed = false;
				var check_checked = "";
				var check_disabled = "";
				var column_title = "";
				for(i = 0; i< th.length; i++) {
					hidden_column = $(th[i]).is(".hidden-column");
					hidden_not_allowed = $(th[i]).is(".nothide");
					check_checked = !hidden_column ? " checked='checked'" : "";
					column_title = $.trim($(th[i]).find("span.title").text());
					check_disabled = (hidden_not_allowed || column_title == "") ? " disabled='disabled'" : "";
					label_disabled = (hidden_not_allowed || column_title == "") ? " class='label-disabled'" : "";
					html += "<tr><td><input type='checkbox' name='th["+i+"]' id='th"+i+"' "+check_checked + check_disabled +" /></td><td><label for='th"+i+"'" + label_disabled + ">" + column_title + "</label></td></tr>\n";
				}				
				html += "</table>";
			
				return html;
			}
			
			$("<div id='thcontext-menu'>" + th_checks() + "</div>").appendTo(document.body);
			thcontext_menu = $("#thcontext-menu");
			
			//init ovladacich inputu na context menu
			var input;	
			thcontext_menu.find(":input").each(function(i) {
				input = $(this);
				if (!input.is(":disabled")) {
					input.change(function() {
						if ($(this).is(":checked")) {
							table.find('tr th:nth-child('+(i + 1)+')').removeClass("hidden-column").addClass("visible-column");
							table.find('tr td:nth-child('+(i + 1)+')').removeClass("hidden-column").addClass("visible-column");
							table.find('col:eq('+(i + 0)+')').removeClass("hidden-column").addClass("visible-column");
							if (table_clone.length) {
								table_clone.find('tr th:nth-child('+(i + 1)+')').removeClass("hidden-column").addClass("visible-column");
							}
						} else {
							table.find('tr td:nth-child('+(i + 1)+')').addClass("hidden-column").removeClass("visible-column");
							table.find('tr th:nth-child('+(i + 1)+')').addClass("hidden-column").removeClass("visible-column");
							table.find('col:eq('+(i + 0)+')').addClass("hidden-column").removeClass("visible-column");
							if (table_clone.length) {
								table_clone.find('tr th:nth-child('+(i + 1)+')').addClass("hidden-column").removeClass("visible-column");
							}
						}
						makeTableHeaderFixed(table_selector);
					});
				}
			});
		}
		//zobrazeni context menu
		thcontext_menu.css({
			'top':e.pageY+'px',
			'left':e.pageX+'px'
		}).show();	
		return false;
	});

	$(window).resize(function(){
		if(checkmenu == 1) {
		windowwidth = $(window).width();
			windowheight = $(window).height();
			$('#thcontext_menu_mask').css({
			'height': windowheight,
			'width': windowwidth
			});
		}
	});
}

function initTableResizable(table) {
	//TODO
	var table_selector = "table.vivo-table-resizable";
	var table = $(table_selector);
	if (!table.length) return;
	var head = $(table + ":not('.resizable-enabled')");
	if (!head.length) return;
	head.addClass("resizable-enabled");
	var table_clone = $("#tableClone");
	var head_clone = table_clone.find("tr");
	var head_tr = (table_clone.length) ? head_clone.find("tr") : head.find("tr");
	var head_cells = head_tr.find("th");
	var cell_html = "";
	if (head_cells.length) {
		
		for(i = 0; i < head_cells.length; i++) {
			var dragg_left = (i > 0) ? "<span class='dleft'></span>" : "<span></span>";
			var dragg_right = (i < head_cells.length) ? "<span class='dright'></span>" : "<span></span>";
				
			$(head_cells[i]).wrapInner("<span></span>");
			$(head_cells[i]).prepend(dragg_left);
			$(head_cells[i]).append(dragg_right);

		}
	}		
}

//--move window shadow
function shadowWindow(_idWindow, _containment, center) {

	if (typeof containment == "undefined") containment = "";
	var center = (typeof center == "undefined") ? true : center;

	if (center) {
		$(_idWindow).css({
			"top" : ($(window).height() - $("#header").height() - $("#footer").height() - $(".main_message").height() - $(_idWindow).height())/2,
			"left" : ($(window).width() - $(".manager_panel").width() - $(_idWindow).width())/2
		});
	}

	$(_idWindow).draggable({
		containment: _containment,
		handle : _idWindow + "-header",
		start : function(event, ui) {
			$(_idWindow + "-shadow").css({"width" : $(_idWindow).width(), "height" : $(_idWindow).height(), "top" : $(_idWindow).css("top"), "left" : $(_idWindow).css("left")}).show();
		},
		drag: function(event, ui) {
			$(_idWindow + "-shadow").hide();
			//$(_idWindow + "-shadow").css({"width" : $(_idWindow).width(), "height" : $(_idWindow).height(), "top" : $(_idWindow).css("top"), "left" : $(_idWindow).css("left")}).show();
		},
		stop: function(event, ui) {
			$(_idWindow + "-shadow").show();
			$(_idWindow + "-shadow").css({"width" : $(_idWindow).width(), "height" : $(_idWindow).height(), "top" : $(_idWindow).css("top"), "left" : $(_idWindow).css("left")}).show();
		}
	});

	$(_idWindow + "-header").css({"cursor" : "move"});
	$(_idWindow + "-shadow").css({"width" : $(_idWindow).width(), "height" : $(_idWindow).height(), "top" : $(_idWindow).css("top"), "left" : $(_idWindow).css("left")}).show();
}

//--build layout
function checkHeight() {
	var window_height = $(parent.window).height();
	var window_width = $(parent.window).width();
	var tabs_height = $("#tabs").height();
	tabs_height = (tabs_height === null || typeof tabs_height == 'undefined') ? 0 : tabs_height;
	var ribbon_height = $(".ribbon-holder").height();
	ribbon_height = (ribbon_height === null) ? 0 : ribbon_height;
	tabs_height = ribbon_height ? ribbon_height : tabs_height;
	var tabs_multi_iframe_height = $("#tabs-multi-iframe").height();
	var dialog_height = $(".dialogHeader").height();
	var finder_height = $("#finder").height() > 0 ? $("#finder").height() + 2 : 0;
	var footer_height = $("#footer").height();
	var default_view = window_height -  $("#header").height() - footer_height - finder_height - 3;
	var message_height = $(".main_message").height();
	var h1_height = ($("h1.dialogTitle").height() > 0) ? $("h1.dialogTitle").height() + 1 : 0;
	var manager_panel_width = $(".manager_panel").width();
	message_height = (message_height === null || typeof message_height == 'undefined') ? 0 : message_height + 2;
	var button_bar = $('#buttons-bar').length ? $('#buttons-bar').height() : 0;
	
	
	$("body").css({"overflow-y": "hidden"});
	$(".explorer_panel").css({"height" : default_view - message_height, "overflow-y": "hidden", "overflow-x" : "hidden"});
	$(".manager_panel").css({"height" : window_height - message_height -  $("#header").height() - $("#footer").height() - 4});
	
	if (location.pathname == "/system/manager/content-manager/") {
		var body = window_height -  $("#header").height() - message_height - h1_height - dialog_height - $("#footer").height() - 3;

		$(".explorer_tree_hitcher").add(".explorer_tree_hitcher_cont").css({"height": body});
		$(".explorer_tree").css({"height": body, "overflow-y": "auto", "overflow-x": "hidden"});
	}
	else {
		$(".explorer_tree_hitcher").add(".explorer_tree_hitcher_cont").css({"height" : default_view - message_height});
		$(".explorer_tree").css({"height" : default_view - message_height, "overflow-y": "auto", "overflow-x" : "hidden"});
	}
	if ($(".manager_panel").width() === null || 0) {//logon screen
		$(".manager_content").css({"height" : window_height - message_height -  $("#header").height() - $("#footer").height() - 4, "margin-left" : "0px"});
	} else {
		if (location.pathname == "/system/manager/" && ie7) {
			$(".manager_content").css({"height" : window_height - message_height -  $("#header").height() - $("#footer").height() - 3});
		}
	}

	if (dialog_height > 0) {//page with dialog options
		if (location.pathname != "/system/Editors/browser/" || (location.pathname == "/system/Editors/browser/" && parent.window.location.pathname != "/system/Editors/editor/"))
			$(".tabMainContent").css({"height" : default_view - tabs_height - dialog_height - message_height - h1_height - button_bar, "overflow-y": "auto", "overflow-x" : "hidden", "zoom" : "1"});
	} else { //page without dialog options
		$(".tabContent").css({"height" : default_view - tabs_height - message_height - h1_height - button_bar, "overflow-y": "auto", "overflow-x" : "hidden"});
		$(".tabMainContent").css({"height" : default_view - tabs_height - message_height - h1_height - button_bar, "overflow-y": "auto", "overflow-x" : "hidden"});
	};
	
	//replicator - osetreni zobrazeni exploreru s iframy pri editaci
	var parent_tabcontent_height = null;
	if (window != parent.window) {
		if (parent_tabcontent_height = parent.window.$(".tabContent").height() && location.pathname != "/system/Editors/browser/") {
			$(".tabMainContent").css({"height" : parent_tabcontent_height - tabs_multi_iframe_height - dialog_height - message_height, "overflow-y": "auto", "overflow-x" : "hidden", "zoom" : "1"});
		}
	}

	//replicator - zobrazeni prehledu replikaci
	if ($("#replication").length) {
		var tab_content_height = $("#replication").parents(".tabMainContent").height();
		$(".tabMainContent").css("overflow-y", "hidden");
		$("#replication").css({
			"width": window_width - manager_panel_width,
			"height": tab_content_height,
			"overflow" : "auto"
		});
	}

	//osetreni zobrazeni tabu zabezpeceni - roztahnuti na vysku
	if ($(".leftCell").length && $(".rightCell").length && $(".leftCell .secureDisplay:visible").length) {
		$(".leftCell .secureDisplay, .rightCell .cellContent")
			.css("height", window_height - $(".leftCell .secureDisplay:visible").offset().top - footer_height - 6); //6 - td padding (top + bottom)
	}

	//resize iframe
	$(".viewFrame, .vivoiframe").css({"height" : default_view - tabs_height - dialog_height - message_height, "overflow-y": "auto", "overflow-x" : "hidden", "border" : "0px"});
	$(".vivoiframe").parents(".tabContent").css({"overflow-y": "hidden"});
	
	//manager help
	$(".manager_content > iframe").css({"width" : "100%", "height" : window_height -  $("#header").height() - $("#footer").height() - 3});

	//site
	$(".site").css({"height" : window_height -  $("#header").height() - $("#footer").height() - finder_height - message_height - 3});

	checkInputWithIconWidth();
}

function include(filename) {
	var head = document.getElementsByTagName('head')[0];
	script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	head.appendChild(script);
}

//init treeMenu in #"elm" element

function initTreeMenu(elm) {
	/* zruseno orezavani delky slova a doplneni tri tecek v treemenu
	if (!ie7)
		$(elm).find("a").each(function() {
			var treeLinkCont = $(this).find("span");
			if (treeLinkCont.height() > treeMenuLineHeight) {
				$(this).attr("title", treeLinkCont.text() + " - " + $(this).attr("title"));
				treeLinkCont.addClass("showMore");
				treeLinkCont.wrapInner($(document.createElement("span")).css({"width" : $(this).width() - 35, "height" : treeMenuLineHeight}));
			}
		});
	*/
	//click na obrazek vedle linku vyvola stejnou akci jako samotny klik na link
	var _ico = $(elm).find("img");
	_ico.unbind("click");
	_ico.bind("click", function(){
		var _this_ico = $(this);
		//nebere to next("a"), proto ta obstrukce s parent
		//location.href = _this_ico.next("a").attr("href");
		location.href = _this_ico.parent().find("a").attr("href");
	}).css("cursor" ," pointer");

	$(elm).find("div").unbind("click");
	$(elm).find("div").bind("click", function(e) {
		e.preventDefault();
		e.stopPropagation();
   	e.cancelBubble = true;

   	var _this = $(this);
 		var _path = _this.attr("rel");
		var _params = _this.attr("id");
		_params = _params.split("-");

		
		if (_this.hasClass("expandable-hitarea")) {
			treeMenu(_path, _params[1], _params[2], 1, _params[3]);
		} else {
			treeMenu(_path, _params[1], _params[2], 0, _params[3]);
		}
	});
}

//vykreslovani tree menu + preloader
function treeMenu(_path, level, pos, act, _hash) {
	if (act) {//show new subtree
		//show preloader
		$("#cont-"+level+"-"+pos+"-"+_hash).after(
				$(document.createElement("span"))
				.attr("class",  "preloader")
				.text(" ")
		);
		//get new html content
		var htmlContent = action($("#treeMenu").attr("rel"), 'viewSubTree', _path, level, pos, _hash);
		//add html content
		$("#cont-"+level+"-"+pos+"-"+_hash).after(htmlContent);
		//temp hide html content
		$("#sub-"+parseInt(parseInt(level) + 1)+"-"+pos+"-"+_hash).hide();
		//begin hiding preloader
		$("#li-"+level+"-"+pos+"-"+_hash+" span.preloader").slideUp("fast");
		//show new html content / new subtree menu and call callback
		$("#sub-"+parseInt(parseInt(level) + 1)+"-"+pos+"-"+_hash).slideDown("fast", function() {
			//init action for new html content/new subtree menu
			initTreeMenu('#sub-' + +parseInt(parseInt(level) + 1)+"-"+pos+"-"+_hash);
			//destruct preloader
			$("#li-"+level+"-"+pos+"-"+_hash+" span.preloader").remove();
			//check hitcher
			checkHeight();
		});
		//switch expand and collaps classes
		$("#tree-"+level+"-"+pos+"-"+_hash).removeClass("expandable-hitarea").addClass("collapsable-hitarea");
		$("#li-"+level+"-"+pos+"-"+_hash).removeClass("expandable").addClass("collapsable");

		if ($("#tree-"+level+"-"+pos+"-"+_hash).hasClass("last-expandable-hitarea")) {
			$("#tree-"+level+"-"+pos+"-"+_hash).removeClass("last-expandable-hitarea").addClass("last-collapsable-hitarea");
		}
		else if ($("#tree-"+level+"-"+pos+"-"+_hash).hasClass("last-collapsable-hitarea")) {
			$("#tree-"+level+"-"+pos+"-"+_hash).removeClass("last-collapsable-hitarea").addClass("last-expandable-hitarea");
		}

		if ($("#li-"+level+"-"+pos+"-"+_hash).hasClass("last-expandable")) {
			$("#li-"+level+"-"+pos+"-"+_hash).removeClass("last-expandable").addClass("last-collapsable");
		}
		 else if ($("#li-"+level+"-"+pos+"-"+_hash).hasClass("last-collapsable")) {
			$("#li-"+level+"-"+pos+"-"+_hash).removeClass("last-collapsable").addClass("last-expandable");
		}
	} else { //destruct subtree
		$("#sub-"+parseInt(parseInt(level) + 1)+"-"+pos+"-"+_hash).slideUp("fast", function() {
			$(this).remove();
			//check hitcher
			checkHeight();
		});
		$("#tree-"+level+"-"+pos+"-"+_hash).addClass("expandable-hitarea").removeClass("collapsable-hitarea");
		$("#li-"+level+"-"+pos+"-"+_hash).addClass("expandable").removeClass("collapsable");

		if ($("#tree-"+level+"-"+pos+"-"+_hash).hasClass("last-expandable-hitarea")) {
			$("#tree-"+level+"-"+pos+"-"+_hash).removeClass("last-expandable-hitarea").addClass("last-collapsable-hitarea");
		}
		else if ($("#tree-"+level+"-"+pos+"-"+_hash).hasClass("last-collapsable-hitarea")) {
			$("#tree-"+level+"-"+pos+"-"+_hash).removeClass("last-collapsable-hitarea").addClass("last-expandable-hitarea");
		}

		if ($("#li-"+level+"-"+pos+"-"+_hash).hasClass("last-expandable")) {
			$("#li-"+level+"-"+pos+"-"+_hash).removeClass("last-expandable").addClass("last-collapsable");
		}
		else if ($("#li-"+level+"-"+pos+"-"+_hash).hasClass("last-collapsable")) {
			$("#li-"+level+"-"+pos+"-"+_hash).removeClass("last-collapsable").addClass("last-expandable");
		}
	}
}

//open tree from iframe
function openTree(_path) {
	var htmlContent = action($("#treeMenu").attr("rel"), 'view');
	$(".treeViewContIn").html(htmlContent);
	initTreeMenu('#treeMenu');
}

//refresh ribbon
function refreshRibbon(_path) {
	var htmlContent = action($("#ribbonAction").attr("rel"), 'view');
	$(".ribbon-holder").html(htmlContent);
	ribbonInit();	
}

//nacteni cesty dle cesty naklikane v iframu s webem
function onframeload(parent) {
	if (parent && (parent != window)) {
		var _id = $("form[name='mainFinderMulti']").attr("id");
		$("form[name='mainFinderMulti']").find("input[name='input']").attr("value", parent.location.pathname);
		$("#fakePathHolder .fakePath span").html(parent.location.protocol + "//" +parent.location.host + "" +parent.location.pathname);

		var finderGetTitles = $('#' + _id).find("input[name='getPath[getTitles]']").attr("value");
		var titles = action(finderGetTitles, parent.location.pathname);
		$("input[name='paths']", "#" + _id).val(titles);
		action(window.finderGetPath.set, parent.location.pathname);
		var entities = getAllEntities($("form[name='mainFinderMulti']").attr("id"));
		multiFinder(_id, entities.length);
		openTree(parent.location.pathname);
		refreshRibbon(parent.location.pathname);
	}
}

// Zvyraznovani panelu ve frontendove casti webu v rezimu prohlizeni dokumentu
function highlightPanel() {
	var _iframeHolder, _showHighlightPanelsBtn, _showHighlightPanelsBtnTitleShow, _showHighlightPanelsBtnTitleHide, _showHighlightPanelsBtnTitleNo, _panelName, _panelLayoutName, _this, _sizeX, _sizeY, _panelCount, _showHighlightPanelsBtnSrc, _panelDLPN, _panelDLP, _panelDDT, _panelDDP, _path, _panelClicked, _panelEdit, _panelView;

	_iframeHolder = $("iframe.viewFrame").contents();
	$("head", _iframeHolder).append('<link rel="stylesheet" type="text/css" href="/system/Styles/manager/front-wysiwyg.css?a" />');
		
	_showHighlightPanelsBtn = $('a#showHighlightPanels');
	_showHighlightPanelsBtnTitleShow = Messages.get('Vivo_CMS_UI_Manager_Explorer_Viewer_wysiwiyg_panelsShow');
	_showHighlightPanelsBtnTitleHide = Messages.get('Vivo_CMS_UI_Manager_Explorer_Viewer_wysiwiyg_panelsHide');
	_showHighlightPanelsBtnTitleNo = Messages.get('Vivo_CMS_UI_Manager_Explorer_Viewer_wysiwiyg_panelsNo');
	_panelName = Messages.get('Vivo_CMS_UI_Manager_Explorer_Viewer_wysiwiyg_panelName');
	_panelLayoutName = Messages.get('Vivo_CMS_UI_Manager_Explorer_Viewer_wysiwiyg_panelLayoutName');
	
	_panelCount = -1;
	
	_iframeHolder.find("div[id^='panel_'][class^='panel_Components_']").each(function(i) {
		_this = $(this);
		_this.addClass('isPanel');
		if (_this.children(":first").css("float") != "none") {
			_this.css("float", _this.children(":first").css("float"));
			_this.css("width", _this.children(":first").css("width"));
		}
		/*
		if (_this.children(":first").css("position") == "absolute") {
			_this.css("height", _this.children(":first").css("height"));
			_this.css("width", _this.children(":first").css("width"));
		}
		*/
		
		_panelCount = i;
	});	
	
	if (_panelCount < 0) {
		hidePanelEditButton({"button": _showHighlightPanelsBtn, "title" : _showHighlightPanelsBtnTitleNo});
	}
	else {
		showPanelEditButton({"button": _showHighlightPanelsBtn, "title" : _showHighlightPanelsBtnTitleShow});
	}	
	
	
	_showHighlightPanelsBtn.not('.disabled').unbind('click');
	_showHighlightPanelsBtn.not('.disabled').bind('click', function () {
		_this = $(this);
		if (_this.is('.active')) {
			_this.removeClass('active').attr('title',_showHighlightPanelsBtnTitleShow);
			$('.isPanel', _iframeHolder).each(function () {
				_this = $(this);
				_this.removeClass('highlightedPanel');
				_this.find('.wysiwyg-tools').remove();
				_this.find('.clear-panel').remove();
			});	
		}
		else {
			_this.addClass('active').attr('title',_showHighlightPanelsBtnTitleHide);
			$('.isPanel', _iframeHolder).each(function (i) {
				_this = $(this);
				_this.attr("data-wt", "data"+i);
				_this.addClass('highlightedPanel').append('<div class="wysiwyg-tools"></div><div class="clear-panel" style="clear: both;"></div>');
				_this.append('<div class="clear-panel" style="clear: both;"></div>');
				_sizeX = _this.outerWidth();
				_sizeY = _this.outerHeight();
				$('.wysiwyg-tools', _this)
				.css({
					width : _sizeX-2+"px",
					height : _sizeY-2+"px"
				});

			});	
		}
	});
	
	if (_showHighlightPanelsBtn.is('.active')) {
		$('.isPanel', _iframeHolder).each(function (i) {
			_this = $(this);
			_sizeX = _this.outerWidth();
			_sizeY = _this.outerHeight();
			_this.addClass('highlightedPanel').append('<div class="wysiwyg-tools"></div><div class="clear-panel" style="clear: both;"></div>');
			$('.wysiwyg-tools', _this).css('width',_sizeX-2+"px").css('height',_sizeY-2+"px");
		});		
	}
	
	$('.isPanel', _iframeHolder)
		.hover(function () {
			if (_showHighlightPanelsBtn.is('.active')) {
				_this = $(this);
				_sizeX = _this.outerWidth();
				_sizeY = _this.outerHeight();
				_this.addClass('hover');
				_panelDLPN = _this.attr('data-layout-panel-name');
				_panelDLP = _this.attr('data-layout-path');
				_panelDLN = _this.attr('data-layout-name');
				_panelDDT = _this.attr('data-document-title');
				_panelDDP = _this.attr('data-document-path');
				_panelEdit = '&ribbon_item=editor';
				_panelView = '&ribbon_item=viewer';
				_this.attr('title', _panelDDP);
				_path = $('input[name="actionURL[set]"]').val() + "&args[]=";
				$('.wysiwyg-tools', _this).css('width',_sizeX-2+"px").css('height',_sizeY-2+"px");
				$('.wysiwyg-tools', _this).html('<div class="vivo-info"><div class="vivo-infoheader">'+_panelDDT+'</div><div class="vivo-infocontent"><div class="vivo-infoline1"><span class="vivo-infolabel">'+_panelName+'</span> <a href="'+_path+_panelDDP+_panelEdit+'" data-href="'+_path+_panelDDP+_panelEdit+'" title="'+_panelDDP+' ('+_panelDLPN+')">'+_panelDDT+'</a></div><div class="vivo-infoline2"><span class="vivo-infolabel">'+_panelLayoutName+'</span> <a href="'+_path+_panelDLP+_panelEdit+'" data-href="'+_path+_panelDLP+_panelEdit+'" title="'+_panelDLP+'">'+_panelDLN+'</a></div></div>'); 
				
				_panelClicked = false;
				
				_this.unbind('click');
				$('.wysiwyg-tools a', _this).unbind('click');
								
				$('.wysiwyg-tools a', _this)
				.bind('click', function (e) {
					e.preventDefault();
					location.href = $(this).attr('data-href');
					_panelClicked = true;
				});
				
				_this.bind('click', function (ev) {
					ev.preventDefault();
					if (!_panelClicked) {
						location.href =  _path + _panelDDP + _panelEdit;
					}
				});
			}
		},
		function () {
			if (_showHighlightPanelsBtn.is('.active')) {
				_this = $(this);
				_this.removeClass('hover');
				$('.wysiwyg-tools', _this).css('width',_sizeX-2+"px").css('height',_sizeY-2+"px");
				$('.wysiwyg-tools .vivo-info', _this).remove();
			}
		});
}

//zdisabluje button pro ovladani zvyraznovani panelu
function hidePanelEditButton(params) {
	var _showHighlightPanelsBtn = params.button;
	var _showHighlightPanelsBtnTitleNo = params.title;
	var _showHighlightPanelsBtnSrc;
	
	if (_showHighlightPanelsBtn.is(".active")) {
		_showHighlightPanelsBtn.attr("data-active", "active");
	}
	
	_showHighlightPanelsBtn.addClass('disabled').removeClass('active');
	_showHighlightPanelsBtnSrc = _showHighlightPanelsBtn.find('img').attr('src');
	_showHighlightPanelsBtn.find('img').attr('src', _showHighlightPanelsBtnSrc + "?width=16&bw=1");
	_showHighlightPanelsBtn.attr('title', _showHighlightPanelsBtnTitleNo);
}	

//zdisabluje button pro ovladani zvyraznovani panelu
function showPanelEditButton(params) {
	var _showHighlightPanelsBtn = params.button;
	var _showHighlightPanelsBtnTitleShow = params.title;
	var _showHighlightPanelsBtnSrc;
	
	if (typeof _showHighlightPanelsBtn.attr("data-active") != "undefined") {
		_showHighlightPanelsBtn.addClass("active");
		_showHighlightPanelsBtn.removeAttr("data-active")
	}
	
	_showHighlightPanelsBtn.removeClass('disabled');
	_showHighlightPanelsBtnSrc = _showHighlightPanelsBtn.find('img').attr('src');
	if (_showHighlightPanelsBtnSrc.indexOf("?") >= 0) {
	   _showHighlightPanelsBtn.find('img').attr('src', _showHighlightPanelsBtnSrc.substring(0, _showHighlightPanelsBtnSrc.indexOf("?")));
    } else {
       _showHighlightPanelsBtn.find('img').attr('src', _showHighlightPanelsBtnSrc); 
    }	
	_showHighlightPanelsBtn.attr('title', _showHighlightPanelsBtnTitleShow);
}	


//--inicializace multifinderu (vola multifinder)
function initMultiFinder(_id, last) {
	$('#' + _id).find(".inputFinder").hide();
	$("#finderMulti_" + _id).show();
	multiFinder(_id, last);

	$('#' + _id).find("input[name='input']")
		.keyup(function (e) {
			if (e.keyCode == 27) {
				$(this).blur();
			}
		})
		.blur(function() {
			var inputVal = $(this).val();
			if (inputVal.substring(0,1) == "/") {
				multiFinderSearch = false;
			}
			if (!multiFinderSearch) {
				$('#' + _id).find(".inputFinder").hide();
				$("#finderMulti_" + _id).show();
			}
		});

	$("body")
		.keyup(function(e) {
			if (e.keyCode == 27) {
				destroyMultiFinderSubmenus(_id);
			}
		})
		.click(function() {
			destroyMultiFinderSubmenus(_id);
		});
}

//--vraci cestu (cast cesty) v documents (0 -> /, 1 -> /xxx/, atd)
function getPathPart(part, _id) {
	var pathPart = "";
	if (typeof part == "undefined") part = 0;
		var InputField = $("#" + _id).find("input[name='input']").attr("value");
		//alert(InputField);
		//var InputField = (_InputField != "" && _InputField != "/") ? _InputField + "/" : _InputField;
		//alert(InputField);
		var InputFieldParts = new Array();
		InputFieldParts = InputField.split("/");

		pathPart = "/";
		jQuery.each(InputFieldParts, function(i, val) {
			if (i > 0 && i <= part) {
				pathPart += val;
				/*if (i != part) */pathPart += "/";
			}
		});
	return pathPart;
}

//--vykresluje samotny multifinder
function multiFinder(_id, last) {
	var InputField = $("#" + _id).find("input[name='input']").attr("value");
	var pathsField = $("#" + _id).find("input[name='paths']").attr("value");
	var siteName = $("#" + _id).find("input[name='site[name]']").attr("value");

	var InputFieldParts = new Array();
	InputFieldParts = InputField.split("/");
	/*
	if (InputField.length > 1)
		InputFieldParts[InputFieldParts.length] = "";
	*/

	var pathsFieldParts = new Array();
	pathsFieldParts = pathsField.split("###");

	$("#finderMulti_" + _id).html("");
	$("#finderMulti_" + _id).css("cursor", "text");

	var actionURL = $('#' + _id).find("input[name='actionURL[set]']").attr("value");

	//sitename
	$("#finderMulti_" + _id).append("<a class='finderMultiPart' id='"+_id+"_a_" + 0 + "_holder' href='"+actionURL+"&args[]=/'><span>" + siteName + "</span></a>");

	jQuery.each(InputFieldParts, function(i, val) {
		var pathTitle = (pathsFieldParts[i] == '-') ? val : pathsFieldParts[i];
		if (i < InputFieldParts.length - 1) {
			if (i > 0) $("#finderMulti_" + _id).append("<a class='finderMultiPart' id='"+_id+"_a_" + i + "_holder' href='"+actionURL+"&args[]="+getPathPart(i, _id)+"'><span>" + pathTitle + "</span></a>");
			if (last != 0 || i != InputFieldParts.length - 2) $("#finderMulti_" + _id).append("<div class='finderMultiPartDir'><a href='javascript:void(0)' id='"+_id+"_a_" + i + "_entities' class='finderMultiPartDira'><span>&gt;</span></a><div id='"+_id+"_" + i + "_entities' class='finderMultiPartDirEntities'></div></div>");
			$('#'+_id+'_'+i+'_entities').hide();
			$('#'+_id+'_a_'+i+'_entities').click(function(e) {
				e.stopPropagation();
				e.cancelBubble = true;

				return showEntities(i, _id);
			});

			$('#'+_id+'_a_'+i+'_holder').click(function(e) {
				e.stopPropagation();
				e.cancelBubble = true;
			});

			$('#'+_id+'_a_'+i+'_entities').mouseover(function(e) {
				$(this).addClass("finderMultiPartDiraSel");
				$('#'+_id+'_a_'+parseInt(parseInt(i) + 0)+'_holder').addClass("finderMultiPartSel");
			});
			$('#'+_id+'_a_'+i+'_entities').mouseout(function(e) {
				$(this).removeClass("finderMultiPartDiraSel");
				$('#'+_id+'_a_'+parseInt(parseInt(i) + 0)+'_holder').removeClass("finderMultiPartSel");
			});

			$('#'+_id+'_a_'+i+'_holder')
				.attr("title", ((i == 0) ? siteName : pathTitle) + " - " + getPathPart(i, _id))
				.mouseover(function(e) {
					$(this).addClass("finderMultiPartSel");
				})
				.mouseout(function(e) {
					$(this).removeClass("finderMultiPartSel");
				});
		}
	});

	$("#finderMulti_" + _id).append($("#" + _id + " .inputFinderMultiBookmarkTemp").html());

//	if ($("#header .user-icon").text().indexOf("zstanek") > 0) { }

	$("#finderMulti_" + _id + " .inputFinderMultiBookmark").show();
	$("#finderMulti_" + _id + " .inputFinderMultiBookmark a").click(function(e) {
		e.stopPropagation();
		e.cancelBubble = true;
	});

	//view bookmarks from finder
	viewBookmark(_id);

	$("#finderMulti_" + _id).append("<div style='clear: both'></div>");

	var firstSearchResult = 1;

	var origVal = $('#' + _id).find(".inputFinder").find("input[name='input']").val();
	
	var searchTimer0 = null;
	
	$("#finderMulti_" + _id).click(function() {
		$("#finderMulti_" + _id).hide();
		$('#' + _id).find(".inputFinder").show().find("input[name='input']").focus();
		destroyMultiFinderSubmenus(_id);
		var searchPullDownContent = "";
	});

	//input keyup
	$('#' + _id).find(".inputFinder input").keyup(function(e) {
		var inputVal = $(this).val();

		if (jQuery.trim(inputVal) != "" && inputVal.substring(0,1) != "/" && jQuery.inArray(e.keyCode, [37, 38, 39, 40, 13]) == -1) { // 37 left, 38 up, 39 right, 40 down
			multiFinderSearch = false;
			clearTimeout(searchTimer0);
			searchTimer0 = setTimeout(function(){
				var newSearchstr = $('#' + _id).find(".inputFinder").find("input[name='input']").val();
				if (jQuery.trim(newSearchstr) != "" && newSearchstr.substring(0,1) != "/") {
					$('#' + _id).find(".inputFinder .searchBox").removeShadow();
					$('#' + _id).find(".inputFinder .searchBox").remove();
					var getPath = $('#' + _id).find("input[name='finder[output]']").attr("value");
					searchPullDownContent = action(getPath, "renderSearchPulldown", newSearchstr);
					searchTimer0 = null;
					$('#' + _id).find(".inputFinder").append($(document.createElement("div")).addClass("searchBox"));
					$('#' + _id).find(".inputFinder .searchBox")
						.html(searchPullDownContent)
						.slideDown(200, function() {
							$(this).dropShadow({
								left: 3,
								top: 3,
								blur: 2,
								opacity: .7,
								color: "#8ba9c8",
								swap: false
							});
	
							firstSearchResult = 0;
							searchResultCount = 0;
							searchResultPosition = 0;
							$(this).find("a").each(function() {
								searchResultCount++;
								$(this).attr("id", "pos-" + searchResultCount);
							});
						});
					multiFinderSearch = true;
				}
			}, 500);
		}	
		
		if (inputVal == "" || e.keyCode == 27) {
				$('#' + _id).find(".inputFinder .searchBox").removeShadow();
				$('#' + _id).find(".inputFinder .searchBox").remove();
				firstSearchResult = 1;
		}
		if (inputVal.substring(0,1) != "/" && jQuery.trim(inputVal) != "") {
			switch(e.keyCode) {
				case 38: //up
						e.stopPropagation();
						e.cancelBubble = true;
						e.preventDefault();
						moveSearchResults(_id, -1);
					break;
				case 40: //down
						e.stopPropagation();
						e.cancelBubble = true;
						e.preventDefault();
						moveSearchResults(_id, 1);
					break;
				case 13: //return
						//e.preventDefault();
						if (typeof $('#' + _id + " .inputFinder .searchBox li.active a").attr("href") != "undefined") {
							var redirUrl = $('#' + _id + " .inputFinder .searchBox li.active a").attr("href");
							redirUrl = (redirUrl.substring(redirUrl.length - 1) != "/" && redirUrl.substring(redirUrl.length - 3) != "%2F") ? redirUrl + "/" : redirUrl;
							location.href = redirUrl;
						}
						//return false;
					break;
				case 27: //esc
				break;
				}
			}
			if (inputVal.substring(0,1) == "/") {
				if (e.keyCode == 13) {
					var inputVal_ = inputVal;
					inputVal_ = (inputVal_.substring(inputVal_.length - 1) != "/") ? inputVal_ + "/" : inputVal_;
					location.href = actionURL+"&args[]=" + inputVal_;
				}
			}

			if (e.keyCode == 27) {
				$(this).val(origVal);
				$(this).blur();
			}
		})
	//input keypress
	.keypress(function(e) {
		var inputVal = $(this).val();
		if (e.keyCode == 13) {
			if (inputVal.substring(0,1) != "/") {
				e.preventDefault();
				return false;
			} else {
				e.preventDefault();
				return false;
			}
		}
	});

	$('#' + _id).submit(function() {
		var inputVal_ = $('#' + _id).find(".inputFinder input").val();
		inputVal_ = (inputVal_.substring(inputVal_.length - 1) != "/") ? inputVal_ + "/" : inputVal_;
		location.href = actionURL+"&args[]=" + inputVal_;
		return false;
	});
}

//--skryti zobrazenych submenu pro jednotlive uzly multifinderu
function destroyMultiFinderSubmenus(_id, part) {
 	if (typeof part != "undefined") { //hide all except part
		$("#finderMulti_" + _id + " .multiFinderEntitiesContainer").each(function() {
			if ($(this).parent().attr("id") != _id + "_" + part + "_entities") {
				$(this).parent().html("").hide();
			}
		});
	} else { //hide all
		$("#finderMulti_" + _id + " .multiFinderEntitiesContainer").each(function() {
			$(this).parent().html("").hide();
		});
	}

	$("#finderMulti_" + _id + " #finderBookmarkSelCont").removeShadow().hide();
	destroySiteChooserSubmenu();
}

//skryti submenu pro siteChooser
function destroySiteChooserSubmenu() {
	$("#siteChooser ul").removeShadow().hide();
}

//--vykresluje seznam odkazu pro jednotlive "uzly" v ceste
function showEntities(part, _id) {
	if ($('#'+_id+'_'+part+'_entities').css("display") == 'none') {
		var pathAll = $('#' + _id).find("input[name='input']").attr("value");
		//pathAll = (pathAll != "" && pathAll != "/") ? pathAll + "/" : pathAll;
		var pathPart = (typeof part == 'undefined') ? pathAll : getPathPart(part, _id);
		//pathPart = (pathPart != "" && pathPart != "/") ? pathPart + "/" : pathPart;

		//skryti ostatnich submenu
		destroyMultiFinderSubmenus(_id, part);

		$('#'+_id+'_'+part+'_entities').append(
			$(document.createElement("div"))
			.attr("class",  "multiFinderEntitiesContainer")
			.hide()
		);

		var getPath = $('#' + _id).find("input[name='getPath[getEntities]']").attr("value");
		var actionURL = $('#' + _id).find("input[name='actionURL[set]']").attr("value");
		var entities = action(getPath, pathPart);
		//alert(serialize(entities));
		for (var i = 0; i < entities.length; i++) {
			var entity = entities[i];
			entity.path = entity.path.replace("//", "/");
			var value = entity.path.substring(entity.path.indexOf('/', 1));
			value = value.substring(value.indexOf('/', 1)) + ''; // ve value je cesta k nastaveni do finderu
			var name = entity.path.substring(entity.path.lastIndexOf('/') + 1); // v name je zobrazeny nazev entity
			$('#'+_id+'_'+part+'_entities > div').append($(document.createElement("a"))
								.attr("href", actionURL+"&args[]=" + value + "/")
								//.attr("class", "multiFinderEntitiesHref")
								.addClass("multiFinderEntitiesHref")
								.addClass("multiFinderEntitiesHrefIcon")
								.addClass(entity.is_folder ? "multiFinderEntitiesHrefFolder" : "")
								.css({"background-image" : "url('"+entity.icon+"')"})
								.text(entity.title ? entity.title : name)
							.attr('title', (entity.title ? entity.title : name) + ' - ' + value + '/')
								.wrapInner($(document.createElement("span")))
								.prepend($(document.createElement("span")).addClass(entity.is_published ? "published" : "not-published"))
								).hide();
		}

		$('#'+_id+'_'+part+'_entities').show();
		$('#'+_id+'_'+part+'_entities > div').slideDown(200, function() {
			$(this).dropShadow({
				left: 3,
				top: 3,
				blur: 2,
				opacity: .7,
				color: "#8ba9c8",
				swap: false
			});
		});

		//$('#'+_id+'_'+part+'_entities > div').dropShadow();

		$('#'+_id+'_'+part+'_entities').click(function(e) {
			e.stopPropagation();
			e.cancelBubble = true;
		});
		$('#'+_id+'_'+part+'_entities').show();
	} else {
		$('#'+_id+'_'+part+'_entities').html("");
		$('#'+_id+'_'+part+'_entities').hide();
	}
	return false;
}

function getAllEntities(_id) {
	var InputField = $('#' + _id).find("input[name='input']").attr("value");
	//var InputField = (_InputField != "" && _InputField != "/") ? _InputField + "/" : _InputField;
	var getPath = $('#' + _id).find("input[name='getPath[getEntities]']").attr("value");
	var entities = action(getPath, InputField);
	return entities;
}

//clock depending on server side time
function startclock() {
	var server_time = new Date($("#current_time_year").text(), $("#current_time_month").text(), $("#current_time_day").text(), $("#current_time_hours").text(), $("#current_time_minutes").text(), $("#current_time_seconds").text());
	//alert(server_time);
	var server_hours = server_time.getHours();
	var server_mins = server_time.getMinutes();
	var server_secs = server_time.getSeconds();
	server_secs++;
	if (server_secs == 60) {
		server_mins++;
		server_secs = 0;
	}
	if (server_mins == 60) {
		server_hours++;
		server_mins = 0;
	}
	if (server_hours == 24) {
		server_hours = 00;
	}
	if (server_mins < 10)
	server_mins = "0" + server_mins;
	if (server_secs < 10)
	server_secs = "0" + server_secs;
	//$("#current_time").html(server_hours+":"+server_mins+":"+server_secs);
	$("#current_time_hours").text(server_hours);
	$("#current_time_minutes").text(server_mins);
	$("#current_time_seconds").text(server_secs);
	setTimeout('startclock()',1000);
}

updatePath = function(path, name) {
	var lang = "";
	if ($('#entity_language').length > 0) {
		lang = $('#entity_language').val();
	}
	
	name = name.toLowerCase();
	var name2 = "";
	var cyrillic = ['1','1','2','2','3','3','4','4','5','5','6','6','7','7','8','8','9','9','0','0','х','h','а','a','є','e','б','b','в','v','г','g','д','d','е','e','ё','jo','ж','zh','з','z','и','i','й','j','к','k','л','l','м','m','н','n','о','o','п','p','р','r','с','s','т','t','у','u','ф','f','x','h','ц','c','ч','ch','ш','sh','щ','shh','ъ','','ы','y','ь','','э','eh','ю','ju','я','ja','Є','E','А','A','А','e','Б','B','В','V','Г','G','Д','D','Е','E','Ё','JO','х','H','Ж','ZH','З','Z','И','I','Й','J','К','K','Л','L','М','M','Н','N','О','O','П','P','Р','R','С','S','Т','T','У','U','Ф','F','X','H','Ц','C','Ч','CH','Ш','SH','Щ','SHH','Ъ','','Ы','Y','Ь','','Э','EH','Ю','JU','Я','JA'];

	while(name.indexOf("ß") >= 0)
		name = name.replace("ß", "ss");

	while(name.indexOf("æ") >= 0)
		name = name.replace("æ", "ae");

	while(name.indexOf("Æ") >= 0)
		name = name.replace("Æ", "AE");

	while(name.indexOf("œ") >= 0)
		name = name.replace("œ", "oe");

	while(name.indexOf("Œ") >= 0)
		name = name.replace("Œ", "OE");

	for (var i = 0; i < name.length; i++) {
		var c = name.charAt(i), x;
		
		if ("abcdefghijklmnopqrstuvwxyz0123456789-_".indexOf(c) >= 0 /*&& lang != "ru"*/) {
			name2 += c;
		} else if (((x = "0123456789áÁäÄčČďĎéÉěĚëËíÍïÏľĽĺĹňŇńŃóÓöÖřŘŕŔšŠśŚťŤúÚůŮüÜýÝÿŸžŽźŹćĆął£ĘęŞşÔôðÐçÇŐŰőű&żŻàÀâÂèÈêÊîÎûÛùñÑÅåăĂţŢ".indexOf(c)) >= 0) /* && lang != "ru"*/) {
			name2 += "0123456789aAaAcCdDeEeEeEiIiIlLlLnNnNoOoOrRrRsSsStTuUuUuUyYyYzZzZcCalLEeSsOodDcCOUou-zZaAaAeEeEiIuUunNAaaAtT".charAt(x);
		} else if (((x = cyrillic.indexOf(c)) >= 0) && lang == "ru") {		
			name2 += cyrillic[x+1];
		}
		else if (" _".indexOf(c) >= 0) {			
			name2 += "-";
		}
	}
	while (name2.indexOf("--") >= 0)
		name2 = name2.replace("--", "-");
	
	return path.substring(0, path.lastIndexOf('/') + 1) + name2.replace(/^-*/, '').replace(/-*$/, '');
};


//checking width of text under icon in compact mode
function checkWidthItemInCompactMode() {
	$(".browser_compact_item_icon_holder").each(function() {
		var textWidth = $(this).find(".browser_compact_item_text");
		if ($(this).width() < textWidth.width()) {
			textWidth.addClass("showMore");
			textWidth.wrapInner($(document.createElement("span")));
		}
		textWidth.css({"width" : $(this).width(), "overflow" : "hidden"});
	});
}

//moving in search result box
function moveSearchResults(_id, step) {
	var li = $('#' + _id).find(".inputFinder .searchBox li");
	searchResultPosition += step;
	if (searchResultPosition > searchResultCount) searchResultPosition = 1;//searchResultCount;
	if (searchResultPosition < 0) searchResultPosition = 0;
	$(li).removeClass("active");
	if (searchResultPosition > 0) {
		$(li[searchResultPosition - 1]).addClass("active");
		$(li).parent().scrollTo(li[searchResultPosition - 1], 200);
	}
}

//hint (help) box generator
function hintBox() {
	$(".tableGridForm input").add("select").add(".custom_selectbox_fake").add("textarea").add("div[id^='solmetraUploaderPlaceholder']").add(".withHint").each(function(i) {
		var $this = $(this);
		var help = $this.siblings(".hint");
		//for inputs with trigger image (datepicker ...) - we have to put hint box after trigger image
		var triggerImage = $this.siblings(".ui-datepicker-trigger");
		if (jQuery.trim($(help).text()) != "") {
			var helpTimeout = new Array();
			$this
				.bind("mouseover", function() {
					if (showHideHintsFlag) {
						var _content = (triggerImage.length) ? triggerImage : $this;
						if (! _content.siblings(".hintBox").length)
							_content.after("<div class='hintBox' id='hintBox-"+i+"'><div class='hintBoxHolder'><div class='hintBoxTop'></div><div class='hintBoxMiddle'><div class='hintBoxMiddleContent'>" + $(help).html() +  "</div></div><div class='hintBoxBottom'></div></div></div>");
						$("#hintBox-" + i).hide();
						helpTimeout[i] = setTimeout(function() {
							$("#hintBox-" + i).hide().fadeIn("def");
						}, 500);
					}
				})
				.bind("mouseout", function() {
					if (showHideHintsFlag) {
						clearTimeout(helpTimeout[i]);
						$("#hintBox-" + i).fadeOut("fast", function() {$(this).remove();});
					}
				})
				.bind("focus", function() {
					if (showHideHintsFlag) {
						clearTimeout(helpTimeout[i]);
						$("#hintBox-" + i).fadeOut("fast", function() {$(this).remove();});
					}
				})
				.bind("click", function() {
					if (showHideHintsFlag) {
						clearTimeout(helpTimeout[i]);
						$("#hintBox-" + i).fadeOut("fast", function() {$(this).remove(); });
					}
				});
		}
	});
}

function uxMenu() {
	$("#ux_profile").find("span")
		.click(function(e) {
			var $submenu = $("#ux_profile .ux_profile_params");
			var $trigger = $(this);
			
			if ($submenu.is(":visible")) {
				$submenu.hide();
				$trigger.removeClass("open");
				$("#vivoModalBlocker").remove();
			}
			else {
				$submenu.show();
				$trigger.addClass("open").removeClass("over").disableSelection();

				$("#header").append(
								$(document.createElement("div"))
									.attr({"id" : "vivoModalBlocker"})
									.addClass("modalBlocker")
									.css({"z-index": 1000, "display": "block"})
									.click(function() {
										$submenu.hide();
										$trigger.removeClass("open");
										$(this).remove();
									})
								);

				e.preventDefault();
				e.stopPropagation();
				e.cancelBubble = true;
			}

			$submenu.find("a").click(function() {
				$trigger.text($(this).text());
			});
		})
		.end()
		.hover(function() {
				$(this).find("span").not(".open").addClass("over");
			}, function() {
				$(this).find("span").not(".open").removeClass("over");
			});
}

//choose site ... menu
function siteMenu() {
	$("#siteChooser").click(function(e) {
        var $ul = $(this).find('ul');
        if ($ul.is(":visible")) {
        	$ul.removeShadow().hide();
        } else {
	        $ul.slideDown(100, function() {
	        	$(this).css({"z-index":"1000"});
						if(!ie7 && !ie8 && !opera){
							$(this).removeShadow();
							$(this).dropShadow({
								left: 3,
								top: 3,
								blur: 2,
								opacity: .7,
								color: "#8ba9c8",
								swap: false
							});
						}
					//$(this).css({"z-index":1001});
					});
					e.preventDefault();
					e.stopPropagation();
					e.cancelBubble = true;
			}
    }).hover(function() {
    		$(this).find("span").addClass("over");
    	}, function() {
    		$(this).find("span").removeClass("over");
    	});
}

function viewBookmark(_id) {
	$(".finderBookmarkSela")
		.attr("href", "javascript:void(0)")
		.click(function(e) {
			e.stopPropagation();
			e.cancelBubble = true;
			if ($("#finderBookmarkSelCont").is(":visible")) {
				$("#finderBookmarkSelCont").html("").removeShadow().hide();
			}
			else {
				var getPath = $('#' + _id).find("input[name='finder[output]']").attr("value");
				bookmarkContent = action(getPath, "viewBookmarks");
				$("#finderBookmarkSelCont").html(bookmarkContent).slideDown(200, function() {
					$(this)
						.click(function(e) {
							e.stopPropagation();
							e.cancelBubble = true;
						})
						.removeShadow();

					$(this).dropShadow({
						left: 3,
						top: 3,
						blur: 2,
						opacity: .7,
						color: "#8ba9c8",
						swap: false
					});
				});
			}
		});
}

function entitySelectSuggest() {
	var origVal = "";
	var searchTimer = null;
	var currentURL = window.location.protocol + '//' + window.location.host;
	$(".entitySelect")
	.attr("autocomplete", "off")
	.click(function() {
		origVal = $(this).val();
	})
	.keyup(function(e) {
		var $container = $(this).parent();
		var $input = $(this);
		var inputVal = $(this).val();
		
		var searchPullDownContent = "";
		if (jQuery.trim(inputVal) != "" && inputVal.substring(0,1) != "/" && inputVal.substring(0,4) != "http" && inputVal.substring(0,6) != "mailto" && jQuery.inArray(e.keyCode, [37, 38, 39, 40, 13]) == -1) { // 37 left, 38 up, 39 right, 40 down
			entitySelectSuggestSearch = false;
			clearTimeout(searchTimer);
			searchTimer = setTimeout(function(){
				var newSearchstr = $input.val();
				if (jQuery.trim(newSearchstr) != "" && newSearchstr.substring(0,1) != "/" && newSearchstr.substring(0,4) != "http" && newSearchstr.substring(0,6) != "mailto") {
					var getPath = $input.attr("rel");
					searchPullDownContent = action(getPath, "viewEntities", newSearchstr);
					searchTimer = null;
					$container.find(".entitySelectBox").removeShadow();
					$container.find(".entitySelectBox").remove();
					$input.after($(document.createElement("div")).addClass("entitySelectBox"));
					$container.find(".entitySelectBox")
						.html(searchPullDownContent)
						.css({'width' : $input.width()})
						.slideDown(200, function() {
									$(this).dropShadow({
										left: 3,
										top: 3,
										blur: 2,
										opacity: .7,
										color: "#8ba9c8",
										swap: false
									});
								//firstSearchResult = 0;
								searchResultCount = 0;
								searchResultPosition = 0;
								$(this).find("a").each(function() {
									searchResultCount++;
									$(this)
										.attr("id", "pos-" + searchResultCount)
										.click(function() {
											
											var retrUrl = (currentURL == $(this).attr("href").substring(0, currentURL.length)) ? $(this).attr("href").substring(currentURL.length) : $(this).attr("href");
											$input.val(retrUrl);
											$container.find(".entitySelectBox").removeShadow();
											$container.find(".entitySelectBox").remove();
											entitySelectSuggestSearch = false;
											return false;
										});
										$(this)
											.mouseover(function() {
												entitySelectSuggestSearch = true;
											})
											.mouseout(function() {
												entitySelectSuggestSearch = false;
											});
								});
						});
				}
			}, 500);
		}

		if (jQuery.trim(inputVal) == "" || inputVal.substring(0,1) == "/" || e.keyCode == 27 || inputVal.substring(0,4) == "http" || inputVal.substring(0,6) == "mailto") {
			$container.find(".entitySelectBox").removeShadow();
			$container.find(".entitySelectBox").remove();
			entitySelectSuggestSearch = false;
		}

		if (inputVal.substring(0,1) != "/" && jQuery.trim(inputVal) != "")
			switch(e.keyCode) {
				case 38: //up
						e.stopPropagation();
						e.cancelBubble = true;
						e.preventDefault();
						moveEntitySelectSearchResults($container, -1);
					break;
				case 40: //down
						e.stopPropagation();
						e.cancelBubble = true;
						e.preventDefault();
						moveEntitySelectSearchResults($container, 1);

					break;
				case 13: //return
						//e.preventDefault();
						var actHref = $container.find(" .entitySelectBox li.active a").attr("href");
						actHref = (currentURL == actHref.substring(0, currentURL.length)) ? actHref.substring(currentURL.length) : actHref;
						$input.val(actHref);
						$container.find(".entitySelectBox").removeShadow();
						$container.find(".entitySelectBox").remove();
						entitySelectSuggestSearch = true;
						//return false;
					break;
				case 27: //esc
				break;
			}
			if (e.keyCode == 27) {
				$(this).blur();
			}

	})
	.blur(function() {
		if (!entitySelectSuggestSearch) {
			var $container = $(this).parent();
			var $input = $(this);
			var $inputVal = $input.val();
			$container.find(".entitySelectBox").removeShadow();
			$container.find(".entitySelectBox").remove();
			if ($inputVal.substring(0,1) != "/" && $inputVal.substring(0,4) != "http" && $inputVal.substring(0,6) != "mailto" && jQuery.trim($inputVal) != "")
				$input.val(origVal);
		}

	})
	.keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			return false;
		}
	});

	$(".entitySelectBox").click(function(e) {
		e.preventDefault();
		return false;
	});
}

/*
 * Našeptávač, který nové slovo připojí na konec pole, místo nahrazení starého.
 * Umožňuje mít v jednom poli více takto vytvořených slov.
 * Jako oddělovač používá čárku.
 *
 * @author ssimek
 */
function keywordSelectSuggest() {
	var origVal = "";
	$(".keywordSelect")
	.click(function() {
		origVal = $(this).val();
	})
	.keyup(function(e) {
		var $container = $(this).parent();
		var $input = $(this);
		var inputVal = $(this).val();
		var searchTimer = null;
		var searchPullDownContent = "";

		if (jQuery.trim(inputVal) != "" && inputVal.substring(0,1) != "/" && jQuery.inArray(e.keyCode, [37, 38, 39, 40, 13]) == -1) { // 37 left, 38 up, 39 right, 40 down
		entitySelectSuggestSearch = false
			if (searchTimer === null) {
				searchTimer = setTimeout(function(){
					var tempArr = new Array();
					tempArr = $input.val().split(',');
					var newSearchstr = jQuery.trim(tempArr[tempArr.length-1]);
					if (newSearchstr != "" && newSearchstr.substring(0,1) != "/") {
						var getPath = $input.attr("rel");
						searchPullDownContent = action(getPath, "viewEntities", newSearchstr);
						searchTimer = null;
						$container.find(".entitySelectBox").removeShadow();
						$container.find(".entitySelectBox").remove();
						$input.after($(document.createElement("div")).addClass("entitySelectBox"));
						$container.find(".entitySelectBox")
							.html(searchPullDownContent)
							.css({'width' : $input.width()})
							.slideDown(200, function() {
										$(this).dropShadow({
											left: 3,
											top: 3,
											blur: 2,
											opacity: .7,
											color: "#8ba9c8",
											swap: false
										});
									//firstSearchResult = 0;
									searchResultCount = 0;
									searchResultPosition = 0;
									$(this).find("a").each(function() {
										searchResultCount++;
										$(this)
											.attr("id", "pos-" + searchResultCount)
											.click(function() {
												var tempArr = new Array();
												tempArr = $input.val().split(',');
												$input.val('');
												for(var i=0; i<tempArr.length-1; i++){
													if(jQuery.trim(tempArr[i])!='') {
														$input.val($input.val() + (i==0 ? '' : ',') + jQuery.trim(tempArr[i]));
													}
												}
												$input.val($input.val() + ($input.val()=='' ? '' : ',') + $(this).attr("href"));
												origVal = $input.val();
												$container.find(".entitySelectBox").removeShadow();
												$container.find(".entitySelectBox").remove();
												entitySelectSuggestSearch = false;
												return false;
											});
											$(this)
												.mouseover(function() {
													entitySelectSuggestSearch = true;
												})
												.mouseout(function() {
													entitySelectSuggestSearch = false;
												});
									});
							});

					}
				}, 500);
			}
		} else {
			searchTimer = null;
		}

		if (jQuery.trim(inputVal) == "" || inputVal.substring(0,1) == "/" || e.keyCode == 27) {
			$container.find(".entitySelectBox").removeShadow();
			$container.find(".entitySelectBox").remove();
			entitySelectSuggestSearch = false;
		}

		if (inputVal.substring(0,1) != "/" && jQuery.trim(inputVal) != "")
			switch(e.keyCode) {
				case 38: //up
						e.stopPropagation();
						e.cancelBubble = true;
						e.preventDefault();
						moveEntitySelectSearchResults($container, -1);
					break;
				case 40: //down
						e.stopPropagation();
						e.cancelBubble = true;
						e.preventDefault();
						moveEntitySelectSearchResults($container, 1);

					break;
				case 13: //return
						//e.preventDefault();
					    var tempArr = new Array();
					    tempArr = $input.val().split(',');
					    $input.val('');
					    for(var i=0; i<tempArr.length-1; i++){
					    	if(jQuery.trim(tempArr[i])!='') {
								$input.val($input.val() + (i==0 ? '' : ',') + jQuery.trim(tempArr[i]));
							}
					    }
				     	$input.val($input.val() + ($input.val()=='' ? '' : ',') + $container.find(" .entitySelectBox li.active a").attr("href"));
				     	origVal = $input.val();
						$container.find(".entitySelectBox").removeShadow();
						$container.find(".entitySelectBox").remove();
						entitySelectSuggestSearch = true;
						//return false;
					break;
				case 27: //esc
				break;
			}
			if (e.keyCode == 27) {
				$(this).blur();
			}

	})
	.blur(function() {
		if (!entitySelectSuggestSearch) {
			var $container = $(this).parent();
			var $input = $(this);
			$container.find(".entitySelectBox").removeShadow();
			$container.find(".entitySelectBox").remove();
			$input.val(origVal);
		}

	})
	.keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			return false;
		}
	});

	$(".entitySelectBox").click(function(e) {
		e.preventDefault();
		return false;
	});
}

//moving in search result box
function moveEntitySelectSearchResults(_container, step) {
	var li = _container.find(".entitySelectBox li");
	searchResultPosition += step;
	if (searchResultPosition > searchResultCount) searchResultPosition = 1;//searchResultCount;
	if (searchResultPosition < 0) searchResultPosition = 0;
	$(li).removeClass("active");
	if (searchResultPosition > 0) {
		$(li[searchResultPosition - 1]).addClass("active");
		$(li).parent().scrollTo(li[searchResultPosition - 1], 200);
	}
}

// popups
var popupCallbacks = new Array();
var popupIndex = 1;

function openPopup(url, width, height, popupCallback) {
	if (!width) width = 700;
	if (!height) height = 480;
	var popup = window.open(url, "larsPopup_" + popupIndex,
		"width=" + width + ",height=" + height + ",status=no,scrollbars=yes,resizable=yes");
	popupCallbacks[popupIndex] = popupCallback;
	popup.popupIndex = popupIndex;
	popupIndex++;
}

function closePopup(returnValue) {
	var popupWindow = window;
	while (typeof popupWindow.opener == "undefined" && window.parent != "undefined")
		popupWindow = popupWindow.parent;

	if (typeof popupWindow.opener == "undefined" || popupWindow.opener.popupCallbacks == "undefined") {
		alert("Aplikační chyba:\nNelze zavřít popup, otevírající stránka není definována.");
		return false;
	} else {
		var popupName = popupWindow.name;
		// jmeno okna popupu je vzdy ve tvaru "regentPopup_<por.cislo_popupu>"
		var popupIndex = popupName.substring(popupName.indexOf('_') + 1) * 1;
		var popupCallback = popupWindow.opener.popupCallbacks[popupIndex];

		if (typeof popupCallback == "undefined") {
			alert("Aplikační chyba:\nCallback funkce není definována." + popupWindow.opener.popupCallbacks.length + ' - ' + popupIndex + ' - ' +  popupWindow.opener.popupCallbacks[popupIndex]);
			return false;
		} else {
			popupCallback(returnValue);
			popupWindow.close();
		}
	}
}

//TAB SECURE

function showSecureDisplay(mode) {
	$("#secure .dialogHeaderHolder a")
		.removeClass('act')
		.filter('[rel='+mode+']')
		.addClass('act');
	$("#secure .secureDisplay").hide();
	$("#secure .secureDisplay-"+mode).show();
}

//END TAB SECURE

function getAssocArrayLength(tempArray) {
   var result = 0;
   for (tempValue in tempArray) {
      result++;
   }
   return result;
}

//fixni hlavicka tabulky
function makeTableHeaderFixed() {
	if (ie7) return;
	var table_selector = "table.vivo-table-fixedheader";
	var table = $(table_selector);
	
	if (!table.length) return;
	
	var first_tr = table.find('tr:eq(0)');
	cloneTableColumns = [];
	var is_cloned = $('#tableClone').length;
	clone = (typeof clone == 'undefined') ? true : clone;
	var first_tr_clone;
	var clone_table;
	if(!is_cloned) { //clone
		first_tr_clone = first_tr.clone(false);
		// zobrazime klon
		table.before("<div id='tableClone'><table class='tableGrid'><thead>" + first_tr_clone.html() + "</thead></table></div>");
	}
	clone_table = $('#tableClone');
	clone_table
		.css({"width" : table.parent().width()})
		.find("table").css({"width" : table.width()});
	
	var td_count = first_tr.find('th').length;

	//zjisteni aktualni sirky sloupcu
	for(i = 0; i < td_count; i++) {
		cloneTableColumns.push(table.find("tr:eq(0) th:eq("+i+") div").width()); 
	}
	
	//nastaveni sirky sloupcu pro #tableClone dle aktualni sirky sloupcu tabulky
	var th_width;
	for(i = 0; i < td_count; i++) {	
		th_width = cloneTableColumns[i];
		clone_table.find('th:eq('+i+')')
			.css({
				'width' :  th_width + "px"
			})
			.removeAttr("width");
	}
	$(window).resize(function() {
		makeTableHeaderFixed();
	});
}

//slider na ikonky v manageru panelu - pokud je jich tam vice, nez se jich tam vleze.
function managerPanelIconSlider() {
	var $panel = $(".manager_panel");
	var $menu = $(".manager_panel ul");
	var panelHeight = $panel.outerHeight(true);
	//var lastLi = $menu.find('li:last-child');
	$panel.unbind("mousemove");
	$panel.scrollTop(0);

	$panel.mousemove(function(e){
		var ulHeight = $menu.outerHeight(true);
  		var top = (e.pageY - $panel.offset().top) * (ulHeight - panelHeight) / panelHeight;
  		$panel.scrollTop(Math.round(top));
    });
}

//popup window - ala tinymce popup
function openModalWindow(params) {
	var defaultParams = {
		width : 500, //sirka
		height : 450, //vyska
		rnd : String((new Date()).getTime()).replace(/\D/gi,''), //hash - pomocny prvek
		fnc_callback : "", //callback
		fnc_callback_arg : {}, //argumenty callback fce - objekt
		fnc_editor: "", //fce pro vytvoreni obsahu popupu
		fnc_editor_arg: {}, //argumenty fce pro vytvoreni obsahu - iframe = url, selector pro zdroj dat, ze kterych se ma prvek editoru naklonovat
		fnc_editor_exec: "", //fce pro vykonani inicializace editoru - napr. naplneni nejakymi daty
		fnc_editor_exec_arg: {}, //argumenty pro fnc_editor_exec (vetsinou asi selector, ze ktereho se maji data natahnout)
		btn_ok: true, // povoleni zobrazeni tlacitka Ok/Vlozit
		btn_close_text: "Cancel", //text tlacitka Storno/Cancel
		btn_ok_text: "Insert", //text tlacitka ok/vlozit
		title: "", //titulek popupu - text v zahlavi
		help: { //text napovedy pro dany prvek, obsahuje text/string nebo selector
			text: "",
			selector: ""
		},
		draggable: true, //povoleno pretahovani popupu
		resizable: true, //povoleno zvetsovani/zmensovani popupu
		show_modal_blocker: true //polopruhledna vrstva pod popupem je/neni videt
	};

	//merge default a custom settings
	var settings = $.extend(true, defaultParams, params);

	var top_scroll = (typeof window.pageYOffset == "undefined") ? document.body.scrollTop : window.pageYOffset; //everybody loves IE ...

	var _top = (($(window).height() - settings.height) / 2) + top_scroll;
	settings.top = (_top < 0) ? 0 : _top;
	var _left = ($(window).width() - settings.width) / 2;
	settings.left = (_left < 0) ? 0 : _left;

	z_index_fix = $(".modalWindow").length;

	$("body").append(
		$(document.createElement("div"))
		.attr({"id" : "vivoModalWindow_" + settings.rnd})
		.addClass("modalWindow")
		.css({
			"width": settings.width + "px",
			"height": settings.height + "px",
			"top": settings.top + "px",
			"left": settings.left + "px",
			"z-index": 300000 + z_index_fix
		})
	);
	$("body").append(
		$(document.createElement("div"))
		.attr({"id" : "vivoModalBlocker_" + settings.rnd})
		.addClass("modalBlocker")
		.css({"z-index": 299999 + z_index_fix, "display": "block"})
	);

	if (!settings.show_modal_blocker) {
		$("#vivoModalBlocker_" + settings.rnd).addClass("modalBlockerClear")
	}

	//ziskat zdroj dat pro vlozeni do editoru - url iframu, zclonovany object ... -> vysledkem je tedy bud string (iframe) nebo object (vlozime .html())
	var editor;

	if (typeof settings.fnc_editor == "function") {
		var fnc_editor_arg = $.extend(true, settings.fnc_editor_arg, {rnd: settings.rnd});
		editor = settings.fnc_editor(fnc_editor_arg);
	}

	editor = (typeof editor == "undefined") ? "" : editor;

	//vyplnit obsah okna editorem + pripravit hlavicku
	$("#vivoModalWindow_" + settings.rnd)
		.html(function() {
			return "<div class='modalWindowHeader'>"+
						 "	<div class='modalWindowTitle'>" + (params.title ? params.title : "" ) + "</div>" +
						 ((settings.help.text || settings.help.selector) ? "	<a href='javascript:;' class='modalWindowHelp'></a>" : "") +
						 "	<a href='javascript:;' class='modalWindowClose'></a>" +
						 "</div>" +
						 "<div class='vivoModalHolder'><div class='vivoModalContent'></div></div>";
		});
	$("#vivoModalWindow_" + settings.rnd + " .vivoModalContent").append(editor);

	//pridani paticky s tlacitky
	$("#vivoModalWindow_" + settings.rnd +" .vivoModalContent")
		.css({
			"height": (settings.height - 50 - 0 - $(".modalWindowHeader").outerHeight(true)) + "px"
		})
			$("#vivoModalWindow_" + settings.rnd +" .vivoModalHolder")
			.css({
			"height": (settings.height - 50 - $(".modalWindowHeader").outerHeight(true)) + "px"
		})
		.after('<table class="tableGridForm center">' +
				' <tr class="saveButtons">' +
					' <td width="90"> </td>' +
					' <td>' +
						' <input type="button" class="inputSubmit action_submit" value="'+settings.btn_ok_text+'" />' +
						' <input type="button" class="inputSubmit action_storno" value="'+settings.btn_close_text+'" />' +
					' </td>' +
				' </tr>' +
			' </table>');

	//help
	if (settings.help.text || settings.help.selector) {
		$("#vivoModalWindow_" + settings.rnd +" .modalWindowHelp").click(function() {
			openModalWindow({
				width: 350,
				height: 300,
				title: "",
				fnc_editor: function() {
					//var help_content = settings.help.text ? settings.help.text : settings.help.selector;
					return settings.help.selector;
				}
			});
		});
	}

	//zavolat fci plnici obsah okna
	if (typeof settings.fnc_editor_exec == "function") {
		var fnc_editor_exec_arg = $.extend(true, settings.fnc_editor_exec_arg, {rnd: settings.rnd});
		settings.fnc_editor_exec(fnc_editor_exec_arg);
	}

	//init OK tlacitka - pouze pokud je povoleno a je definovana callback fce
	if (typeof settings.fnc_callback == "function" && settings.btn_ok) {
	$("#vivoModalWindow_" + settings.rnd + " .action_submit").click(function() {
		//zavolat callback fci pri stisku ok
		var fnc_callback_arg = $.extend(true, settings.fnc_callback_arg, {rnd: settings.rnd});
		settings.fnc_callback(fnc_callback_arg);
	});
	} else {
		$("#vivoModalWindow_" + settings.rnd + " .action_submit").hide();
	}

	//init storno tlacitka - vzdy
	$("#vivoModalWindow_" + settings.rnd + " .action_storno, .modalWindowClose").bind("click", function() {
		closeModalWindow(settings.rnd);
	});

	//focus prvniho pole v okne
	$("#vivoModalWindow_" + settings.rnd + " :input:not([readonly='readonly'])").first().focus();

	//dragg & resize
	if (settings.draggable) {
		$("#vivoModalWindow_" + settings.rnd + " .modalWindowHeader").css("cursor", "move");
		$("#vivoModalWindow_" + settings.rnd + ".modalWindow")
			.draggable({
				handle: ".modalWindowHeader",
				containment: ".manager",
				start: function() {
					$(this).find("iframe").css("visibility", "hidden");
				},
				stop: function() {
					$(this).find("iframe").css("visibility", "visible");
				}
			});
	}
	if (settings.resizable)	{
		$("#vivoModalWindow_" + settings.rnd + ".modalWindow")
			.resizable({
				start: function() {
					$(this).find("iframe").css("visibility", "hidden");
				},
				stop: function() {
					$(this).find("iframe").css({"visibility": "visible", "height" : ($(this).find(".vivoModalHolder").height() - 50 - $(".modalWindowHeader").outerHeight(true)) + "px"});
				},
				resize: function() {
					$(this).find(".vivoModalHolder").css("height", ($(this).height() - 50 - $(".modalWindowHeader").outerHeight(true)) + "px");
				}
			});
	}
}

//zavre popup
function closeModalWindow(identificator) {
	$("#vivoModalWindow_" + identificator).add("#vivoModalBlocker_" + identificator).remove();
}

//resetuje selectbox - pokud je graficky selectbox nutno znovu prekreslit (select v popupu, select ajaxovy, ...)
function resetSelectbox(_id) {
	$(_id + "_container").add(_id + "_input").add(_id + "_opener").remove();
	$(_id).show();
	$(_id).selectbox();
	hintBox();
}

//zobrazovani a skryvani polozek v editaci
function showLessMore() {
	if (!$("#lessShowHide").length) return;

	if ($("#lessShowHide").hasClass('act')) {
		$("#lessShowHide .lessShowHideImg").addClass("lessHide");
		$("#lessShowHide").addClass("act");
		$("#lessShowHide").attr("title", jQuery.trim($("#lessShowHide #lessHide").text()) + " " + jQuery.trim($("#lessShowHide .dialogMessage").text()));
	} else {
		$("#lessShowHide .lessShowHideImg").addClass("lessShow");
		$("#lessShowHide").removeClass("act");
		$("#lessShowHide").attr("title", jQuery.trim($("#lessShowHide #lessShow").text()) + " " + jQuery.trim($("#lessShowHide .dialogMessage").text()));

		$("table.tableGridForm > tbody").children().each(function() {
			var _this = $(this);
			if (!_this.is(".important") && !_this.is(".saveButtons") && !_this.is(".section")) {
				_this.hide();
			}
		});
	}
	//lessShowHide
	$("#lessShowHide")
		.attr("href", "javascript:void(0);")
		.click(function() {
			if ($("#lessShowHide").hasClass("act")) {
				$("table.tableGridForm > tbody").children().each(function() {
					var _this = $(this);
					if (!_this.is(".important") && !_this.is(".saveButtons") && !_this.is(".section")) {
						_this.hide();
					}
					//checkHeight();
				});

				$("#lessShowHide .lessShowHideImg").addClass("lessShow").removeClass("lessHide");
				$("#lessShowHide").attr("title", jQuery.trim($("#lessShowHide #lessShow").text()) + " " + jQuery.trim($("#lessShowHide .dialogMessage").text()));
			} else {
				$("table.tableGridForm > tbody").children().each(function() {
					var _this = $(this);
					if (!_this.is(".important") && !_this.is(".saveButtons") && !_this.is(".section")) {
						_this.show();
					}
					//checkHeight();
				});

				$("#lessShowHide .lessShowHideImg").addClass("lessHide").removeClass("lessShow");
				$("#lessShowHide").attr("title", jQuery.trim($("#lessShowHide #lessHide").text()) + " " + jQuery.trim($("#lessShowHide .dialogMessage").text()));
			}
			checkHeight();
			
			if ($('#editor').attr('rel') == 'disabled') {
				disabledPageHeight();
			}
		});
}

//zobrazovani a skryvani napovedy(hintu) v editaci
function showHints() {
	if (!$("#hintsShowHide").length) return;
	if ($("#hintsShowHide").hasClass('act')) {
		$("#hintsShowHide .hintsShowHideImg").addClass("hintsHide");
		$("#hintsShowHide").addClass("act");
		$("#hintsShowHide").attr("title", jQuery.trim($("#hintsShowHide #hintsHide").text()) + " " + jQuery.trim($("#hintsShowHide .dialogMessage").text()));
		showHideHintsFlag = true;
	} else {
		$("#hintsShowHide .hintsShowHideImg").addClass("hintsShow");
		$("#hintsShowHide").removeClass("act");
		$("#hintsShowHide").attr("title", jQuery.trim($("#hintsShowHide #hintsShow").text()) + " " + jQuery.trim($("#hintsShowHide .dialogMessage").text()));
		showHideHintsFlag = false;
	}
	
	//lessShowHide
	$("#hintsShowHide")
		.attr("href", "javascript:void(0);")
		.click(function() {
			if ($("#hintsShowHide").hasClass("act")) {
				showHideHintsFlag = false;
				$("#hintsShowHide .hintsShowHideImg").addClass("hintsShow").removeClass("hintsHide");
				$("#hintsShowHide").attr("title", jQuery.trim($("#hintsShowHide #hintsShow").text()) + " " + jQuery.trim($("#hintsShowHide .dialogMessage").text()));
			} else {
				showHideHintsFlag = true;
				$("#hintsShowHide .hintsShowHideImg").addClass("hintsHide").removeClass("hintsShow");
				$("#hintsShowHide").attr("title", jQuery.trim($("#hintsShowHide #hintsHide").text()) + " " + jQuery.trim($("#hintsShowHide .dialogMessage").text()));
			}
		});
}

function preventEntityChange() {
	if ($("#entity_title").length) {
		return $("#entity_title").val().length;
	} else {
		return false;
	}
}

//pomocna fce - zjistuje zda data jsou validni json data
(function ($) {
    $.isJSON = function (json) {
        json = json.replace(/\\["\\\/bfnrtu]/g, '@');
        json = json.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
        json = json.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
        return (/^[\],:{}\s]*$/.test(json))
    }

    $.fn.isJSON = function () {
        var json = this;
        if (jQuery(json).is(":input")) {
            json = jQuery(json).val();
            json = new String(json);
            return jQuery.isJSON(json)
        } else {
            throw new SyntaxError("$(object).isJSON only accepts fields!");
        }
    }
    String.prototype.isJSON = function () {
        var y = this;
        return jQuery.isJSON(y);
    }
})(jQuery);

//pomocna fce - obdoba php implode
// *     example 1: implode(' ', ['a', 'b', 'c']);
// *     returns 1: 'a b c'
// *     example 2: implode(' ', {first:'a', last: 'b c'});
// *     returns 2: 'a b c'
function implode(glue, pieces) {
  var i = '',
      retVal = '',
      tGlue = '';
  if (arguments.length === 1) {
      pieces = glue;
      glue = '';
  }
  if (typeof(pieces) === 'object') {
      if (Object.prototype.toString.call(pieces) === '[object Array]') {
          return pieces.join(glue);
      } else {
          for (i in pieces) {
              retVal += tGlue + pieces[i];
              tGlue = glue;
          }
          return retVal;
      }
  } else {
      return pieces;
  }
}

//znemozni uzivateli zasah do cms, vyuzivano napr. pri ajaxovych akcich
//pouziti:
//var blocker = addBlocker();
//vykonej akce ...
//removeBlocker(blocker);
function addBlocker() {
	var _hash = String((new Date()).getTime()).replace(/\D/gi,'');
	$("body").append(
		$(document.createElement("div"))
		.attr({"id" : "vivoModalBlocker_" + _hash})
		.addClass("modalBlocker")
		.css({"z-index": 299999 , "display": "block"})
	);
	return _hash;
}

function removeBlocker(_hash) {
	$("#vivoModalBlocker_" + _hash).fadeOut(600, function() {$(this).remove();});
}

/* ******** INIT RIBBON START CODE ******** */
function ribbonInit() {
//inicializace Ribbonu
	var ribbon_holder = $('.manager .ribbon-holder'), show_hide_ribbon_btn, show_hide_ribbon_title_array = [null, null], active_tab, active_tab_id;
	
	$('.tabContentRibbon .tab-content', ribbon_holder).hide();
	active_tab = ribbon_holder.find('a.active').closest('.tab-content');
	if (!active_tab.length) {
	    active_tab = $("#ribbon-tab-tab1");
	}
	active_tab.show();
	ribbon_holder.find('#tabs .tabs li[rel="'+active_tab.attr("id")+'"]').addClass('selected');
	
	if (!ribbon_holder.length) {
		return;
	}
	if ($('a.showHideRibbon', ribbon_holder).length) {
		show_hide_ribbon_btn = $('a.showHideRibbon', ribbon_holder);
		show_hide_ribbon_title_array = [Messages.get('Vivo_UI_Ribbon_min_ribbon'), Messages.get('Vivo_UI_Ribbon_max_ribbon')];
	}
	
	rollRibbonInit($.cookie("showHideRibbon"), ribbon_holder, show_hide_ribbon_title_array);
	
	hoverElement('.tabs li', ribbon_holder);
			
	$('.tabs li.selected', ribbon_holder).live("dblclick", function () {
		var _selected = $(this);
		var sel;
		if(document.selection && document.selection.empty){
			document.selection.empty() ;
		} 
		else if(window.getSelection) {
			sel=window.getSelection();
			if(sel && sel.removeAllRanges) {
				sel.removeAllRanges();
			}
		}
		rollRibbon(_selected, true, ribbon_holder, show_hide_ribbon_title_array);
	});
	
	$('.tabs li a', ribbon_holder).click(function () { 
		var _selected = $(this);
		rollRibbon(_selected, false, ribbon_holder, show_hide_ribbon_title_array);
	});
	
	/*
	$('a.showHideRibbon', ribbon_holder).click(function(e) { 
		var _thisIsShow = $(this);
		if (_thisIsShow.hasClass('active')) {
			var _selected = $('.tabs li.selected');
			rollRibbon(_selected, true, ribbon_holder, show_hide_ribbon_title_array);
		}
		else {
			var _selected = $('.tabs li.ref');
			rollRibbon(_selected, false, ribbon_holder, show_hide_ribbon_title_array);
		}
		e.preventDefault();
	});
	*/
	 
	$('a.disabled, .tabs li a', ribbon_holder).click(function(e) {
		e.preventDefault();
	});
	
	$(".tabs li", ribbon_holder).click(function(e) {
		var _this = $(this);
		ribbon_holder.find('#tabs .tabs li').removeClass('selected');
		_this.addClass('selected');
		ribbon_holder.find('.tab-content').hide();
		ribbon_holder.find('#'+ _this.attr('rel')).show();
	});
	//ajaxove nacitani obsahu ribbonu
	/*
	$(".tabs li a", ribbon_holder).not(".tabs li.selected a").click(function(e) {
		e.preventDefault();
		var _link = $(this);
		var act_select = jQuery.url.setUrl(_link.attr("href")).param("act");
	
		var query_params_select = [jQuery.url.setUrl(_link.attr("href")).param("args[]")];
		var query_select = 'async=1&act=' + act_select;
		for (var i = 0; i < query_params_select.length; i++)
	    query_select += '&args[]=' + encodeURIComponent(query_params_select[i]);
		
		var tabContent = "";
		var tab = "";
		var sending = false;
		if (!smc_sending) {
			$.ajax({
			  url: window.location.pathname + "?" + query_select,
			  type: "POST",
			  dataType: "html",
			  beforeSend: function(jqXHR, textStatus) {
			  	smc_sending = true;
			  },
			  error: function(jqXHR, textStatus, errorThrown) {
			  },
			  success: function(res, textStatus, jqXHR) {
			  	if(res && res !== "null") {
						tab = res;
			  	}
			  },
			  complete: function(jqXHR, textStatus) {
			  	if (tab) {
						smc_sending = changeRibbonContent();	
					} else {
						smc_sending = false;
					}
			  }
			});
		}
	});	
	
	var changeRibbonContent = function() {
		var act_view = window.tabRibbonContainder.view;
		var query_params = [];
		var query = 'async=1&act=' + act_view;
		for (var i = 0; i < query_params.length; i++)
	    query += '&args[]=' + encodeURIComponent(query_params[i]);
  	$.ajax({
		  url: window.location.pathname + "?" + query,
		  type: "POST",
		  dataType: "html",
		  beforeSend: function(jqXHR, textStatus) {
		  	smc_sending = true;
		  },
		  error: function(jqXHR, textStatus, errorThrown) {
		  },
		  success: function(res, textStatus, jqXHR) {
		  	if(res && res !== "null") {
					tabContent = res;
		  	}
		  },
		  complete: function(jqXHR, textStatus) {
		  	ribbon_holder.html(tabContent);
		  	ribbonInit();					
			return false;
		  }
		});
	}
	*/
	
}

//inicializace ribbonu (zobrazen/skryt)
function rollRibbonInit(param, ribbon_holder, showHideRibbonTitleArray) {
	if (param == "hide") {
		ribbon_holder.css('height', '35px');
		$('.tabs li.selected', ribbon_holder).removeClass('selected').addClass('ref');
		$('a.showHideRibbon', ribbon_holder).removeClass('active').attr('title',showHideRibbonTitleArray[1]);
		checkHeight();
	}
	else {
		$('a.showHideRibbon', ribbon_holder).attr('title',showHideRibbonTitleArray[0]);
	}
	return;
}
/* ******** INIT RIBBON END CODE ******** */


/* ******** ROLL RIBBON START CODE ******** */
//Zobrazeni/skryti ribbonu
function rollRibbon(_selected, doubleClick, ribbon_holder, showHideRibbonTitleArray) {
	if (doubleClick) {
		ribbon_holder.animate({
			height: '35px'			
		}, 
		200, 
		function() {
			_selected.removeClass('selected').addClass('ref');
			checkHeight();
			$.cookie("showHideRibbon", "hide");
			$('a.showHideRibbon', ribbon_holder).removeClass('active').attr('title',showHideRibbonTitleArray[1]);
		});
		return;	
	}
	else {
		_selected.addClass('selected').removeClass('ref');
		$.cookie("showHideRibbon", "show");
		ribbon_holder.animate({
			height: '123px'
		}, 
		200, 
		function() {
			checkHeight();
			$('a.showHideRibbon', ribbon_holder).addClass('active').attr('title',showHideRibbonTitleArray[0]);
		});
		return;	
	}
}
/* ******** ROLL RIBBON END CODE ******** */

//inicializuje json input (hromadny i jednotlivy v popupu)
	function jsonInputInit(_id) {
		//je _id -> inicializace jednoho konkretniho prvku (s className json-data-popup) - deje se tak v popupu
		if (typeof _id != "undefined") {
			var $this = $("#" + _id);
			jsonInputInitGen($this, {height: 380, width: 580});
		//neni id, inicializuje se vse co ma class name json-data (obycejne inputy s podporou json dat)
		} else {
			$("input[class^='jsondata-']").each(function() {
				var input = $(this);
				var _class = input.attr("class").split(" ");
				var _type = "";
				if (_class.length) {
					for(_c in _class) {
						if(_class[_c].substring(0, 9) ==  'jsondata-') {
							_type = _class[_c].substring(9);
						}
					}
				}
				jsonInputInitGen(input, {title: input.parent().prev().text(), _type : _type});
			});
		}
	}

//generovani ovladani pro input obsahujici json data
function jsonInputInitGen(jsonInput, params) {
	var jsonInputTypes = ["text_text"];
	//var jsonInput = $(".json-data-text_text");
	if (!jsonInput.length) return;
	if (jQuery.inArray(params._type, jsonInputTypes) == -1) return;
	var defaultParams = {
		width: 600,
		height: 400
	}
	var settings = $.extend(true, defaultParams, params);
	
	var _id = jsonInput.attr("id") ? jsonInput.attr("id") : String((new Date()).getTime()).replace(/\D/gi,'');
	var _id_data = _id + "_data";
	jsonInput.attr("id", _id_data);
	var _val = function() {
		var tmp = {};
		var tmpval = "";
		if (jsonInput.val()) {
			tmp = jQuery.parseJSON(jsonInput.val());
			var count = 0;
			var fake_value = new Array();
			$.each(tmp, function(key, value) {
				fake_value[count] = key + "=" + value;
				count++; 
			});
			tmpval = fake_value.join(",");
		}
		return tmpval;
	}
	jsonInput
		.hide()
		.before("<input type='text' class='text' id='" + _id + "'  value='" + _val() + "' />");
	
	$("#" + _id)
		.click(function() {
			$(this).blur();
			openModalWindow({
				width: settings.width,
				height: settings.height,
				title: settings.title,
				fnc_editor: initJsonDefaultEditor,
				fnc_editor_arg: {
					_type: "wrapper_json" //pro init edituru se pouzije nejdriv wrapper tabulka a pak uz je mozne do ni vkladat dalsi radky (napr. text_text) pomoci fnc_callback
				},
				fnc_editor_exec:initJsonDefaultEditorExec,
				fnc_editor_exec_arg: {
						_type: settings._type,
						data_selector: jsonInput
					},
				fnc_callback: generateJsonLabelDefault,
				fnc_callback_arg: {
					close_popup: true,
					selector: "#" + _id
				},
				btn_ok_text: Messages.get('Vivo_b_insert'),
				btn_close_text: Messages.get('Vivo_b_cancel')
			});
		});
}

//init editoru
function initJsonDefaultEditor(obj) {
		var _type = (typeof obj._type != "undefined") ? obj._type : "text_text";
		var editor = htmlEditorTemplate(_type);
		editor = $(editor);
		return editor;
}

//exec editoru - vygenerovani radku dle dat
function initJsonDefaultEditorExec(obj) {
	if ($(obj.data_selector).val() && $.isJSON($(obj.data_selector).val())) {
		var _obj = jQuery.parseJSON($(obj.data_selector).val());
		for(key in _obj) {
			addRowDefault(key, _obj[key], null, obj.rnd, obj._type);
		}
	}
	addRowDefault(null, null, true, obj.rnd, obj._type);
}

//vygenerovani hodnoty z 
function generateJsonLabelDefault(obj) {
	isStatic = (typeof obj.isStatic == "undefined") ? false : obj.isStatic;
	var _data = _generateJSONData({rnd:obj.rnd});
	var _dataObj = isStatic ? jQuery.parseJSON($(obj.selector + "_data").val()) : _generateJSONData({rnd:obj.rnd, asString: false});
	if (!isStatic) $(obj.selector + "_data").val(_data);
	if (!jQuery.isEmptyObject(_dataObj)) {
		var count = 0;
		var fake_value = new Array();
		$.each(_dataObj, function(key, value) {
			fake_value[count] = key + "=" + value;
			count++; 
		});
		$(obj.selector).val(fake_value.join(","));
	}
	if (obj.close_popup === true) {
		closeModalWindow(obj.rnd);
	}
}

function _generateJSONData(obj) {
	asString = (typeof obj.asString == "undefined") ? true : obj.asString;
	var d = {};
	$("#vivoModalWindow_" + obj.rnd + " table.formTable tr.sortable").each(function(i) {
		var k = $(this).find(".key").val();
		var v = $(this).find("input.value").val();
		if (v != "") {
			d[k] = v;
		}
	});
	if (asString) {
		d = jQuery.toJSON(d);
	}
	return d;
}

function addRowDefault(_key, _value, _empty, _hash, _type) {
	_key = (typeof _key != "undefined") ? _key : null;
	_value = (typeof _value != "undefined") ? _value : null;
	_empty = (typeof _empty != "undefined") ? _empty : null;
	_type = (typeof _type != "undefined") ? _type : "text_text"; //text vs select
	
	template = htmlEditorTemplate(_type);
	
	$(template)
		.insertAfter("#vivoModalWindow_" + _hash + " table.formTable tr:last")
		.find(".key").val(_key).end()
		.find("input.value").val(_value).end()
		.find(".del-row")
		.attr("href", "javascript:void(0);")
		.find("img").attr("src", _empty ? imgplusIcon : imgminusIcon).end()
		.click(function() {
			var _src = $(this).find("img").attr("src");
			if (_empty && _src == imgplusIcon) {
				$(this).find("img").attr("src", imgminusIcon).end();
				//$(this).closest(".ui-icon").hide();
				addRowDefault(null, null, true, _hash, _type);
			} else {
				//$(this).closest(".ui-icon").show();
				$(this).closest("tr").addClass("highlighted");
				if (confirm( Messages.get('Vivo_json_really_delete_row'))) {
					$(this).closest("tr").remove();
				} else {
					$(this).closest("tr").removeClass("highlighted");
				}
			}
	}).end();

	$("#vivoModalWindow_" + _hash).sortable({
		items: 'tr.sortable',
		handle: "span"
	});

	//reset id a selectu
	if (_type == "select") {
		$("#vivoModalWindow_" + _hash).find("[id]").each(function() {
			$(this).attr("id", function() {
				return "new_" + _hash + "_" + $(this).attr("id");
			});
		});
		$("#vivoModalWindow_" + _hash).find("[for]").each(function() {
			$(this).attr("for", function() {
				return "new_" + _hash + "_" + $(this).attr("for");
			});
		});

		$("#vivoModalWindow_" + _hash + " select.selectbox").each(function() {
			var _id = $(this).attr("id");
			if (_id != "") { //pro IE ... v IE je graficky select vypnuty -> select tedy nemusi mit id
				resetSelectbox("#" + _id);
			}
		});
	}
	
	initTableInputs("#vivoModalWindow_" + _hash);
	
}

function htmlEditorTemplate(_type) {
	html = "";
	switch(_type) {
		case "wrapper_json":
			html = '<table class="formTable tableGridForm tableGridForm-smpadd center fixed">\n' +
							'	<tr class="important" id="head">\n' +
							'		<th width="20"></th>\n' +
							'		<th width="120">' + Messages.get('Vivo_json_popup_value') + '</th>\n' +
							'		<th width="190">' + Messages.get('Vivo_json_popup_label') + '</th>\n' +
							'		<th width="30"></th>\n' +
							'	</tr>\n' +
							'</table>\n';
		break;
		case "text_text":
		default:
			html = '' + 
							'	<tr class="important sortable">\n' + 
							'		<td>\n \n' + 
							'			<span class="ui-icon ui-icon-arrowthick-2-n-s"> </span>\n' + 
							'		</td>\n' + 
							'		<td>\n' + 
							'			<input class="text key" />\n' + 
							'		</td>\n' + 
							'		<td>\n' + 
							'			<input class="text value" />\n' + 
							'		</td>\n' + 
							'		<td>\n' + 
							'			<a href="#" class="del-row"><img class="middle" src="/system/Images/icons/16x16/minus.png" /></a>\n' + 
							'		</td>\n' + 
							'	</tr>\n' + 
							'';
	}
	return html;
}

/*
 * jQuery resize event 
 */
(function($,h,c){var a=$([]),e=$.resize=$.extend($.resize,{}),i,k="setTimeout",j="resize",d=j+"-special-event",b="delay",f="throttleWindow";e[b]=250;e[f]=true;$.event.special[j]={setup:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.add(l);$.data(this,d,{w:l.width(),h:l.height()});if(a.length===1){g()}},teardown:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.not(l);l.removeData(d);if(!a.length){clearTimeout(i)}},add:function(l){if(!e[f]&&this[k]){return false}var n;function m(s,o,p){var q=$(this),r=$.data(this,d);r.w=o!==c?o:q.width();r.h=p!==c?p:q.height();n.apply(this,arguments)}if($.isFunction(l)){n=l;return m}else{n=l.handler;l.handler=m}}};function g(){i=h[k](function(){a.each(function(){var n=$(this),m=n.width(),l=n.height(),o=$.data(this,d);if(m!==o.w||l!==o.h){n.trigger(j,[o.w=m,o.h=l])}});g()},e[b])}})(jQuery,this);

//fce pro hover efekt na elementech
function hoverElement(selector, context) {
	if (context != null) {
		$(selector, context).hover(function() {
			$(this).addClass("hover");
		}, function() {
			$(this).removeClass("hover");
		});
		return;
	}
	else {
		$(selector).hover(function() {
			$(this).addClass("hover");
		}, function() {
			$(this).removeClass("hover");
		});
	}
}

function idAttributeFromUTF8(str) {
	result = encodeURIComponent(str);
	result = result.replace(/x/g, 'x78').replace(/%/g, 'x').replace(/\!/g, 'x21').replace(/\*/g, 'x2A').replace(/'/g, 'x27').replace(/\(/g, 'x28').replace(/\)/g, 'x29').replace(/~/g, 'x7E');
	return result;
}

//auto show more in content manager
function autoloadTable() {
	var table_selector = ".vivo-table-showmore";
	var table = $(table_selector);
	
	if (table.length) {
		
		var _header = $("#header").height();
		var _h1_dialog_title = $("h1.dialogTitle").height();
		var _dialogHeader = $(".dialogHeader").height();
		
		var managerContentHeight = $(window).height() - _header - _h1_dialog_title - _dialogHeader - table .height();
		var emptyLinesCount = Math.floor(managerContentHeight / 20);
		
		act = (typeof window.showMoreAction == "undefined") ? false : window.showMoreAction;
		
		if (emptyLinesCount > 0) {
			showManagerContent(table_selector, emptyLinesCount + 10, false, act);
		}

		$(window).resize(function() {
			var managerContentHeight = $(window).height() - _header - _h1_dialog_title - _dialogHeader - table .height();
			var emptyLinesCount = Math.floor(managerContentHeight / 20);
			if (emptyLinesCount > 0) {
				showManagerContent(table_selector, emptyLinesCount + 10, false, act);
			}
		});

		$(".tabMainContent").scroll(function() {
			var managerContentHeight = $(window).height() - $("#footer").height();
			//var offset = $(".manager-content-more").offset();		
			var offset = $(table_selector + " tbody tr:last").offset();
		
			//console.log(managerContentHeight + " " + offset.top);

			if (managerContentHeight > 0 && offset.top > 0 && managerContentHeight > offset.top) {
				showManagerContent(table_selector, 20, true, act)
			}
		});

	}
}

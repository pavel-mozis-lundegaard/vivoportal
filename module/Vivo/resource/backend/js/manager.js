//onload
$(document).ready(function() {
	$("#loader").hide();
	$("#wrapper").show();
	jQuery(document)
		.ajaxStart(function() {
		$("#loader").show();
	})
		.ajaxSuccess(function() {
		$("#loader").hide();
	});

	checkHeight();
		
	startclock();

	//manager logout window
	$(window).resize(function() {shadowWindow("#logoutDialog", ".manager_content");});
	shadowWindow("#logoutDialog", ".manager_content");
	
	$('.browsersTrigger').click(function(){
		$(this).toggleClass('open');
		$('.saveButtons.browsers').toggle();
		shadowWindow("#logoutDialog", ".manager_content", false);
	})	
});

function action() {
	console.log("TODO action ...");
	return false;
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

	//checkInputWithIconWidth();
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

//skryti submenu pro siteChooser
function destroySiteChooserSubmenu() {
	$("#siteChooser ul").removeShadow().hide();
}

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
 		var _path = _this.attr("id").substring(5);
		var _params = _this.attr("id");
		
		if (_this.hasClass("expandable-hitarea")) {
			treeMenu(_path, 1);
		} else {
			treeMenu(_path, 0);
		}
	});
}

//vykreslovani tree menu + preloader
function treeMenu(_path, act) {
	if (act) {//show new subtree
		//show preloader
		$("#cont-" + container).after(
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
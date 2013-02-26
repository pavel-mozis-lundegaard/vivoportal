	
	function selectURL(url, formName, inputName, doMCEStuff){
		doMCEStuff = (typeof doMCEStuff == "undefined") ? true : false; //default - do MCE stuff
		
		var passform = (typeof formName == "undefined") ? passform : formName;
		var fileurl = (typeof inputName == "undefined") ? fileurl : inputName;
		
		document.passform.fileurl.value = url;
		if (doMCEStuff == true)
			FileBrowserDialogue.mySubmit();
	}
	
  var FileBrowserDialogue = {
	    init : function () {
	        // Here goes your code for setting your custom things onLoad.
	        //zruseni css z tematu editoru
	        var allLinks = document.getElementsByTagName("link");
					allLinks[allLinks.length-1].parentNode.removeChild(allLinks[allLinks.length-1]);
					
					try {
						afterInit();
					} catch (error) {}

	    },
	    mySubmit : function () {
	        var URL = document.passform.fileurl.value;
	        var win = tinyMCEPopup.getWindowArg("window");
	        // insert information now	       
	        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
	        // are we an image browser
	        if (typeof(win.ImageDialog) != "undefined" && (document.URL.indexOf('type=image') != -1 || (typeof i_type !="undefined" && i_type == "image"))) {
			        if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
			        if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);
					  }
	        // close popup window
	        tinyMCEPopup.close();
	    }
	}
  
  if (typeof tinyMCEPopup != "undefined")
  	tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
 
  function TinyMCE_vivobrowser_browse (field_name, url, type, win) {

     //alert("Field_Name: " + field_name + "\nURL: " + url + "\nType: " + type + "\nWin: " + win); // debug/testing

    /* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
       the session name and session ID in the request string (can look like this: "?PHPSESSID=88p0n70s9dsknra96qhuk6etm5").
       These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */
		
		var content_path = (typeof parent.window.content_path != "undefined") ? parent.window.content_path : "";
				
		if (typeof parent.window.content_version == "undefined" || parent.window.content_version == "") {
			content_path = "";
		}
		
    var cmsURL = "/system/Editors/editor/?path=" + ((type == 'file') ? '/' : '/Files') + "&content_path="+content_path+"&form_name=null&input_name=null&view=browser"; //window.location.toString();    // script URL - use an absolute path!
    if (cmsURL.indexOf("?") < 0) {
        //add the type as the only query parameter
        cmsURL = cmsURL + "?type=" + type;
    }
    else {
        //add the type as an additional query parameter
        // (PHP session ID is now included if there is one at all)
        cmsURL = cmsURL + "&type=" + type;
    }

    tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : 'Vivo File Browser',
        width : 900,  // Your dimensions may differ - toy around with them!
        height : 500,
        resizable : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : "no"
    }, {
        window : win,
        input : field_name
    });
    return false;
  }
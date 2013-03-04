tinyMCEPopup.requireLangPack();

var icodeDialog = {
    init: function() {
        var f = document.forms[0];
			
        // Get the selected contents as text and place it in the input
        //f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});	
        
        this.resize();	
    },

    insert: function() {
        // Insert the contents from the input into the document
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, GetFormatedCode());
        tinyMCEPopup.close();
    },

		resize : function() {
			var vp = tinyMCEPopup.dom.getViewPort(window), el;

			el = document.getElementById('txtCode');

			el.style.width  = (vp.w - 20) + 'px';
			el.style.height = (vp.h - 120) + 'px';
		}
};

function GetFormatedCode() {
    var strCode = document.forms[0].txtCode.value;

    strCode = strCode.replace(/</gi,"&lt;");
    strCode = strCode.replace(/>/gi, "&gt;");
    //strCode = strCode.replace(/&gt;/gi, ">");
    var strCodeText = '<pre class="' + document.forms[0].selctLanguage.value + '">';
    strCodeText += strCode;
    strCodeText += '</pre><br/>'    
    return strCodeText;
    //alert("done");
}

tinyMCEPopup.onInit.add(icodeDialog.init, icodeDialog);

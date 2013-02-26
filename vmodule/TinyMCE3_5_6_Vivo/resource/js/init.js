function test()
{
    alert("AHOJ");
}

function initTinyMce()
{
    tinyMCE.init({
        // General options
        setup : function(editor) {
            editor.onInit.add(function() {
                editor.dom.bind(editor.getWin(), 'resize', function() {
                    //alert('Resize');
                });
            });
        },
        /*mode : "exact",
        elements : "<?= $attributes['id']; ?>",*/
        mode : "specific_textareas",
        theme : "advanced",
        skin : "vivotheme",
        inlinepopups_skin : "vivopopups",
        content_css : "/Styles/editor.css",
        oninit: function(){try{disabledPageHeight();} catch(error){}},
        language : "<?= Util\Messages::getLanguage() ?>",
        entity_encoding: "raw",
        //valid_elements : "*[*]",
        //extended_valid_elements : "*[*]",
        extended_valid_elements : "iframe[src|width|height|name|align]",
        convert_urls : false,
        apply_source_formatting : true,
        fix_list_elements : true,
        editor_selector : "mceEditor",
        <? if ($view_type == "full"): ?>
        height : "<?= CMS::getUserProfile()->wysiwygHeight>0?CMS::getUserProfile()->wysiwygHeight:'200'?>px",
        plugins : "autolink,safari,style,layer,table,preelementfix,save,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,xhtmlxtras,simplebrowser,vivobrowser,icode",
        theme_advanced_buttons1 : "newdocument,|,preview,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,icode,|,cleanup,removeformat,|,search,replace,|,bold,italic,underline,strikethrough,|,forecolor,backcolor,styleprops,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote",
        theme_advanced_buttons2 : "styleselect,formatselect,|,sub,sup,|,link,unlink,anchor,image,media,hr,|,charmap,|,tablecontrols,|,fullscreen,|,code",
        theme_advanced_buttons3 : "",
        <? else: ?>
        plugins : "-example",
        theme_advanced_buttons1 : "styleselect,formatselect,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        <? endif; ?>
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        theme_advanced_resize_horizontal: false,
        theme_advanced_blockformats : "<?= ($blockformats = Context::$instance->site->editor_blockformats) ? $blockformats : 'p,h2,h3,h4' ?>",
        <? if ($advanced_styles = Context::$instance->site->editor_styles): ?>
            theme_advanced_styles : "<?= $advanced_styles ?>",
        <? endif ?>
        file_browser_callback : "TinyMCE_vivobrowser_browse",
        preelementfix_css_aliases: {
                'XML XHTML XSLT HTML': 'xml',
                'JavaScript': 'jscript',
                'PHP': 'php',
                'SQL': 'sql',
                'CSS' : 'css',
                'Plain/Text' : 'plain',
                'Bash/Shell' : 'shell',
                'Java' : 'java',
                'ActionScript' : 'as3',
                'C#': 'csharp'
             }
    });
}
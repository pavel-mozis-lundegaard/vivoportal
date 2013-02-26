/**
 * Initialize TinyMCE
 * @param mode
 * @param modeSpec
 * @param full
 * @param language
 * @param advancedStyles
 * @param blockFormats
 * @param wysiwygHeight
 */
function initTinyMce(mode, modeSpec, full, language, advancedStyles, blockFormats, wysiwygHeight)
{
    var config;
    config  = {
        // General options
        setup                               : function(editor) {
                                                editor.onInit.add(function() {
                                                    editor.dom.bind(editor.getWin(), 'resize', function() {
                                                        //alert('Resize');
                                                    });
                                                });
                                              },
        /*mode : "exact",
         elements : "<?= $attributes['id']; ?>",*/
        theme                               : "advanced",
        skin                                : "vivotheme",
        inlinepopups_skin                   : "vivopopups",
        content_css                         : ".TinyMCE3_5_6_Vivo.resource/css/editor.css",
        oninit                              : function(){try{disabledPageHeight();} catch(error){}},
        entity_encoding                     : "raw",
        //valid_elements : "*[*]",
        //extended_valid_elements : "*[*]",
        extended_valid_elements             : "iframe[src|width|height|name|align]",
        convert_urls                        : false,
        apply_source_formatting             : true,
        fix_list_elements                   : true,
        theme_advanced_toolbar_location     : "top",
        theme_advanced_toolbar_align        : "left",
        theme_advanced_statusbar_location   : "bottom",
        theme_advanced_resizing             : true,
        theme_advanced_resize_horizontal    : false,
        file_browser_callback               : "TinyMCE_vivobrowser_browse",
        preelementfix_css_aliases           : {
                                                'XML XHTML XSLT HTML'   : 'xml',
                                                'JavaScript'            : 'jscript',
                                                'PHP'                   : 'php',
                                                'SQL'                   : 'sql',
                                                'CSS'                   : 'css',
                                                'Plain/Text'            : 'plain',
                                                'Bash/Shell'            : 'shell',
                                                'Java'                  : 'java',
                                                'ActionScript'          : 'as3',
                                                'C#'                    : 'csharp'
                                              }
    };
    switch (mode) {
        case 'all_textareas':
            config.mode                 = 'textareas';
            break;
        case 'selected_textareas':
            config.mode                 = 'specific_textareas';
            config.editor_selector      = modeSpec;
            break;
        case 'deselected_textareas':
            config.mode                 = 'specific_textareas';
            config.editor_deselector    = modeSpec;
            break;
        case 'exact':
            config.mode                 = 'exact';
            config.elements             = modeSpec;
            break;
        case 'none':
        default:
            config.mode                 = 'none';
            break;
    }
    if (full) {
        if (wysiwygHeight) {
            config.height               = wysiwygHeight + 'px';
        } else {
            config.height               = '200px';
        }
        config.plugins                  = "autolink,safari,style,layer,table,preelementfix,save,advimage,advlink," +
                                          "inlinepopups,preview,media,searchreplace,contextmenu,paste,directionality," +
                                          "fullscreen,noneditable,xhtmlxtras,simplebrowser,vivobrowser,icode";
        config.theme_advanced_buttons1  = "newdocument,|,preview,|,undo,redo,|,cut,copy,paste,pastetext,pasteword," +
                                          "icode,|,cleanup,removeformat,|,search,replace,|,bold,italic,underline," +
                                          "strikethrough,|,forecolor,backcolor,styleprops,|,justifyleft," +
                                          "justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent," +
                                          "blockquote";
        config.theme_advanced_buttons2  = "styleselect,formatselect,|,sub,sup,|,link,unlink,anchor,image,media,hr,|," +
                                          "charmap,|,tablecontrols,|,fullscreen,|,code";
        config.theme_advanced_buttons3  = "";
    } else {
        config.plugins                  = "-example";
        config.theme_advanced_buttons1  = "styleselect,formatselect,bold,italic,underline,separator,strikethrough," +
                                          "justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo," +
                                          "redo,link,unlink";
        config.theme_advanced_buttons2  = "";
        config.theme_advanced_buttons3  = "";
    }
    if (language) {
        config.language = language;
    }
    if (advancedStyles) {
        config.theme_advanced_styles    = advancedStyles;
    }
    if (blockFormats) {
        config.theme_advanced_blockformats  = blockFormats;
    } else {
        config.theme_advanced_blockformats  = 'p,h2,h3,h4';
    }
    tinyMCE.init(config);
}

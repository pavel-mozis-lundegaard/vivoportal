function updatePath(path, name) {
    var lang = "";
//    if ($('#entity_language').length > 0) {
//        lang = $('#entity_language').val();
//    }

    name = name.toLowerCase();
    var name2 = "";
    var cyrillic = ['а','a','б','b','в','v','г','g','д','d','е','e','ё','jo','ж','zh','з','z','и','i','й','j','к','k','л','l','м','m','н','n','о','o','п','p','р','r','с','s','т','t','у','u','ф','f','х','kh','ц','c','ч','ch','ш','sh','щ','shh','ъ','','ы','y','ь','','э','eh','ю','ju','я','ja','А','A','Б','B','В','V','Г','G','Д','D','Е','E','Ё','JO','Ж','ZH','З','Z','И','I','Й','J','К','K','Л','L','М','M','Н','N','О','O','П','P','Р','R','С','S','Т','T','У','U','Ф','F','Х','KH','Ц','C','Ч','CH','Ш','SH','Щ','SHH','Ъ','','Ы','Y','Ь','','Э','EH','Ю','JU','Я','JA'];

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

        if ("abcdefghijklmnopqrstuvwxyz0123456789-_".indexOf(c) >= 0 && lang != "ru") {
            name2 += c;
        } else if (((x = "áÁäÄčČďĎéÉěĚëËíÍïÏľĽĺĹňŇńŃóÓöÖřŘŕŔšŠśŚťŤúÚůŮüÜýÝÿŸžŽźŹćĆął£ĘęŞşÔôðÐçÇŐŰőű&żŻàÀâÂèÈêÊîÎûÛùñÑÅåăĂţŢ".indexOf(c)) >= 0) && lang != "ru") {
            name2 += "aAaAcCdDeEeEeEiIiIlLlLnNnNoOoOrRrRsSsStTuUuUuUyYyYzZzZcCalLEeSsOodDcCOUou-zZaAaAeEeEiIuUunNAaaAtT".charAt(x);
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
}
<?php

$language = $_SESSION['language_select'];

$language_default= getLanguages()[0];
$regulation_file_language=__DIR__."/MainRegulations.".$language.".md";
$regulation_file_default=__DIR__."/MainRegulations.".$language_default.".md";
if(file_exists($regulation_file_language)){
    $regulationBlock= file_get_contents($regulation_file_language);
}elseif(file_exists($regulation_file_default)){
    $regulationBlock= file_get_contents($regulation_file_default);
}else{
    die("not found MainRegulations $language_default");
}

$data = arrayToObject([
    'language' => $language,
    'accessEdit' => CheckAccess('MainRegulations.Edit'),
    'regulations' => Parsedown($regulationBlock, false)
        ]);

if (getPathElement('MainRegulations', 1) == 'edit'
        and CheckAccess('MainRegulations.Edit') === true) {

    $data->regulationsEdit = str_replace(
            "\n\n", "\n&nbsp;\n", str_replace(
                    chr(13) . chr(10), "\n", $regulationBlock
            )
    );

    IncludeClass::Template('MainRegulations.Edit', $data);
    IncludeClass::Element('Style', 'MainRegulations.css', 'style');
} else {
    IncludeClass::Template('MainRegulations', $data);
}

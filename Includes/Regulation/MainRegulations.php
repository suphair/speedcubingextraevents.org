<?php

$language = $_SESSION['language_select'];
$regulationBlock = getBlockText("MainRegulation", $language);

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

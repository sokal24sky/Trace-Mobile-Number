<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material.min.css">
<?php \Altum\Event::add_content(ob_get_clean(), 'head', 'codemirror') ?>

<?php ob_start() ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>

<script>
    'use strict';
    
    try {
        let textarea_elements = document.querySelectorAll('textarea[data-code-editor]');

        textarea_elements.forEach(textarea_element => {
            let code_editor_instance = CodeMirror.fromTextArea(textarea_element, {
                lineNumbers: true,
                lineWrapping: true,
                mode: textarea_element.getAttribute("data-mode") || "javascript",
                theme: <?= \Altum\ThemeStyle::get() == 'light' ? json_encode('default') : json_encode('material') ?>,
                indentUnit: 4,
                tabSize: 4,
                indentWithTabs: true,
                matchBrackets: true,
                autoCloseBrackets: true,
                styleActiveLine: true,
            });
        });
    } catch(error) {
        /* :) */
    }
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'codemirror') ?>

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cleaner Enable/Disable Settings
    |--------------------------------------------------------------------------
    |
    | Control which cleaners should be active
    |
    */
    'cleaners' => [
        'string'     => true,  // Clean basic strings and entities
        'html'       => true,  // Clean HTML tags
        'attribute'  => true,  // Clean HTML attributes
        'javascript' => true,  // Clean JavaScript
        'regex'      => true,  // Clean regex patterns
    ],

    /*
    |--------------------------------------------------------------------------
    | Replacement String
    |--------------------------------------------------------------------------
    |
    | The string that will replace dangerous content
    |
    */
    'replacement' => '',

    /*
    |--------------------------------------------------------------------------
    | Strip 4-Byte Characters
    |--------------------------------------------------------------------------
    |
    | Strip characters that are not supported by older MySQL versions (< 5.5.3)
    | to prevent stored XSS attacks
    |
    */
    'strip_4byte_chars' => false,

    /*
    |--------------------------------------------------------------------------
    | Never Allowed Call Strings
    |--------------------------------------------------------------------------
    |
    | List of strings that are never allowed
    |
    */
    'never_allowed_call_strings' => [
        'javascript',
        'jar',          // Java: jar-protocol is an XSS hazard
        'applescript',  // Mac (will not run the script, but open it in AppleScript Editor)
        'vbscript',     // IE: XSS hazard
        'vbs',          // IE
        'wscript',      // IE
        'jscript',      // IE
        'behavior',     // https://html5sec.org/#behavior
        'mocha',        // old Netscape
        'livescript',   // old Netscape
        'view-source',  // default view source
    ],

    /*
    |--------------------------------------------------------------------------
    | Never Allowed JavaScript Callback Patterns
    |--------------------------------------------------------------------------
    |
    | List of JavaScript callback function patterns that are never allowed
    |
    */
    'never_allowed_js_callback_regex' => [
        '\(?window\)?\.',
        '\(?history\)?\.',
        '\(?location\)?\.',
        '\(?document\)?\.',
        '\(?cookie\)?\.',
        '\(?ScriptElement\)?\.',
        'd\s*a\s*t\s*a\s*:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Evil HTML Tags
    |--------------------------------------------------------------------------
    |
    | List of dangerous HTML tags that should be removed
    |
    */
    'evil_html_tags' => [
        'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
        'blink', 'body', 'embed', 'eval', 'expression', 'form', 'frameset',
        'frame', 'head', 'html', 'ilayer', 'iframe', 'input', 'button',
        'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
        'plaintext', 'style', 'script', 'textarea', 'title', 'math',
        'noscript', 'event-source', 'vmlframe', 'video', 'source', 'svg',
        'xml',
    ],

    /*
    |--------------------------------------------------------------------------
    | Evil Attributes
    |--------------------------------------------------------------------------
    |
    | List of HTML attributes that might contain dangerous content
    |
    */
    'evil_attributes' => [
        'style',
        'xmlns:xdp',
        'formaction',
        'form',
        'xlink:href',
        'seekSegmentTime',
        'FSCommand',
    ],

    /*
    |--------------------------------------------------------------------------
    | Never Allowed String Patterns
    |--------------------------------------------------------------------------
    |
    | Regex patterns that should never be allowed
    |
    */
    'never_allowed_regex' => [
        'javascript\s*:',
        '(document|(document\.)?window)\.(location|on\w*)',
        'expression\s*(\(|&\#40;)',
        'vbscript\s*:',
        'wscript\s*:',
        'jscript\s*:',
        'vbs\s*:',
        'Redirect\s+30\d',
        "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?",
    ],

    /*
    |--------------------------------------------------------------------------
    | Never Allowed Raw Strings
    |--------------------------------------------------------------------------
    |
    | List of strings that should never be allowed in their raw form
    |
    */
    'never_allowed_str' => [
        'document.cookie'   => '[removed]',
        '(document).cookie' => '[removed]',
        'document.write'    => '[removed]',
        '(document).write'  => '[removed]',
        '.parentNode'       => '[removed]',
        '.innerHTML'        => '[removed]',
        '.appendChild'      => '[removed]',
        '-moz-binding'      => '[removed]',
        '<!--'             => '&lt;!--',
        '-->'              => '--&gt;',
        '<![CDATA['        => '&lt;![CDATA[',
        '<comment>'        => '&lt;comment&gt;',
    ],

    /*
    |--------------------------------------------------------------------------
    | Do Not Close HTML Tags
    |--------------------------------------------------------------------------
    |
    | List of HTML tags that should not be automatically closed
    |
    */
    'do_not_close_html_tags' => [
        // Add any tags that shouldn't be auto-closed
    ],

    /*
    |--------------------------------------------------------------------------
    | Never Allowed On Events Afterwards
    |--------------------------------------------------------------------------
    |
    | List of on* event handlers that are never allowed
    |
    */
    'never_allowed_on_events_afterwards' => [
        'onabort', 'onactivate', 'onattribute', 'onafterprint', 'onafterscriptexecute',
        'onanimationcancel', 'onanimationend', 'onanimationiteration', 'onanimationstart',
        'onariaRequest', 'onautocomplete', 'onautocompleteerror', 'onbeforeactivate',
        'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus',
        'onbeforepaste', 'onbeforeprint', 'onbeforescriptexecute', 'onbeforeunload',
        'onbeforeupdate', 'onbegin', 'onblur', 'onbounce', 'oncancel', 'oncanplay',
        'oncanplaythrough', 'oncellchange', 'onchange', 'onclick', 'onclose', 'oncommand',
        'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncuechange', 'oncut', 'ondataavailable',
        'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag',
        'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart',
        'ondrop', 'onend', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus',
        'onfocusin', 'onfocusout', 'onformchange', 'onforminput', 'onhashchange', 'onhelp',
        'oninput', 'oninvalid', 'onkeydown', 'onkeypress', 'onkeyup', 'onlanguagechange',
        'onlayoutcomplete', 'onload', 'onloadeddata', 'onloadedmetadata', 'onloadstart',
        'onlostpointercapture', 'onmediacomplete', 'onmediaerror', 'onmessage', 'onmousedown',
        'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup',
        'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onoffline', 'ononline', 'onoutofsync',
        'onpagehide', 'onpageshow', 'onpaste', 'onpause', 'onplay', 'onplaying', 'onpointercancel',
        'onpointerdown', 'onpointerenter', 'onpointerleave', 'onpointermove', 'onpointerout',
        'onpointerover', 'onpointerup', 'onpopstate', 'onprogress', 'onpropertychange', 'onreadystatechange',
        'onredo', 'onrepeat', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onresume',
        'onreverse', 'onrowclick', 'onrowdelete', 'onrowenter', 'onrowexit', 'onrowinserted', 'onscroll',
        'onseek', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onstorage',
        'onsubmit', 'onsuspend', 'onsyncrestored', 'ontime', 'ontimeerror', 'ontoggle', 'ontrackchange',
        'ontransitioncancel', 'ontransitionend', 'ontransitionrun', 'ontransitionstart', 'onundo',
        'onunhandledrejection', 'onunload', 'onurlflip', 'onvolumechange', 'onwaiting', 'onwheel'
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Settings
    |--------------------------------------------------------------------------
    |
    | Configure which routes or parameters should be excluded from XSS cleaning
    |
    */
    'middleware' => [
        'excluded_routes' => [
            // 'api/webhook/*'
        ],
        'excluded_parameters' => [
            // 'content' // useful for rich text editors
        ],
    ],
];

export const options = {
    fontFamily: "'Fira Code', Consolas, 'Courier New', monospace",
    fontLigatures: true,
    fontSize: 16,
    wordWrap: "on",
};

export const languageDef = {
    keywords: [
        'accept', 'review', 'spam', 'deny', 'skip',
    ],

    names: [
        'uid', 'nick', 'email', 'url', 'content', 'length', 'ip', 'ua',
    ],

    operators: [
        '==', '!=', '<', '>', '<=', '>=', '<-', '~',
    ],

    symbols: /[=><!~-]+/,

    regexpctl: /[(){}[\]$^|\-*+?.]/,
	regexpesc: /\\(?:[bBdDfnrstvwWn0\\/]|@regexpctl|c[A-Z]|x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4})/,

    // The main tokenizer for rule
    tokenizer: {
        root: [
            // identifiers and keywords
            [/[a-z_$][\w$]*/, {
                cases: {
                    '@keywords': 'keyword',
                    '@names': 'type.identifier',
                    '@default': 'identifier',
                }
            }],

            [/[[\]:!;]/, 'delimiter'],

            // whitespace and comments
            { include: '@whitespace' },

            // regular expression: magic
            // [/\/(?=([^\\/]|\\.)+\/([imsxADSUXJu]*)(\s*)(\.|;|\/|,|\)|\]|\}|$))/, { token: 'regexp', bracket: '@open', next: '@regexp' }],
            [/\/(?=([^\\/]|\\.)*)/, { token: 'regexp', bracket: '@open', next: '@regexp' }],

            // delimiters and operators
            [/[{}()[\]]/, '@brackets'],
            [/[<>](?!@symbols)/, '@brackets'],
            [/@symbols/, {
                cases: {
                    '@operators': 'operator',
                    '@default': ''
                }
            }],

            // numbers
            [/\d+/, 'number'],

            // strings
            { include: '@strings' },
        ],

        regexp: [
			[/(\{)(\d+(?:,\d*)?)(\})/, ['regexp.escape.control', 'regexp.escape.control', 'regexp.escape.control']],
			[/(\[)(\^?)(?=(?:[^\]\\/]|\\.)+)/, ['regexp.escape.control', { token: 'regexp.escape.control', next: '@regexrange' }]],
			[/(\()(\?:|\?=|\?!)/, ['regexp.escape.control', 'regexp.escape.control']],
			[/[()]/, 'regexp.escape.control'],
			[/@regexpctl/, 'regexp.escape.control'],
			[/[^\\/]/, 'regexp'],
			[/@regexpesc/, 'regexp.escape'],
			[/\\\./, 'regexp.invalid'],
			[/(\/)([imsxADSUXJu]*)/, [{ token: 'regexp', bracket: '@close', next: '@pop' }, 'keyword.other']],
		],

		regexrange: [
			[/-/, 'regexp.escape.control'],
			[/\^/, 'regexp.invalid'],
			[/@regexpesc/, 'regexp.escape'],
			[/[^\]]/, 'regexp'],
			[/\]/, { token: 'regexp.escape.control', next: '@pop', bracket: '@close' }],
		],

        strings: [
			[/'/, 'string.escape', '@stringBody'],
			[/"/, 'string.escape', '@dblStringBody'],
        ],
        
        stringBody: [
			[/[^\\']+/, 'string'],
			[/\\./, 'string'],
			[/'/, 'string.escape', '@popall'],
        ],
        
		dblStringBody: [
			[/[^\\"]+/, 'string'],
			[/\\./, 'string'],
			[/"/, 'string.escape', '@popall'],
		],

        whitespace: [
            [/[ \t\r\n]+/, 'white'],
            [/(^#.*$)/, 'comment'],
        ],
    },
};

export const configuration = {
    brackets: [
        ["[", "]"],
    ],
    comments: {
        lineComment: "#",
    },
};

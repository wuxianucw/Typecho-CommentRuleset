export const monacoConfig = {
    'vs/nls': {
        availableLanguages: { '*': 'zh-cn' },
    },
};

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

export const createCompletionItems = (range, monaco) => [
    {
        label: 'accept',
        kind: monaco.languages.CompletionItemKind.Event,
        documentation: "标记当前评论为通过",
        insertText: 'accept',
        range: range,
    },
    {
        label: 'review',
        kind: monaco.languages.CompletionItemKind.Event,
        documentation: "标记当前评论为待审核",
        insertText: 'review',
        range: range,
    },
    {
        label: 'spam',
        kind: monaco.languages.CompletionItemKind.Event,
        documentation: "标记当前评论为垃圾",
        insertText: 'spam',
        range: range,
    },
    {
        label: 'deny',
        kind: monaco.languages.CompletionItemKind.Event,
        documentation: "禁止当前评论",
        insertText: 'deny',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'skip',
        kind: monaco.languages.CompletionItemKind.Event,
        documentation: "跳过当前评论，交由下级规则或默认配置处理",
        insertText: 'skip',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'uid',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论用户的 <code>uid</code>，游客为 0",
        insertText: 'uid',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'nick',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论用户的昵称",
        insertText: 'nick',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'email',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论用户的 Email 地址",
        insertText: 'email',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'url',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论用户的个人主页地址，可能为空",
        insertText: 'url',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'content',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论内容",
        insertText: 'content',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'length',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论内容长度",
        insertText: 'length',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'ip',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论用户的 IP 地址",
        insertText: 'ip',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
    {
        label: 'ua',
        kind: monaco.languages.CompletionItemKind.Variable,
        documentation: "评论用户的 User-Agent 值",
        insertText: 'ua',
        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
        range: range,
    },
];

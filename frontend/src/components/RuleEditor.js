import React from 'react';
import InputLabel from '@material-ui/core/InputLabel';
import Button from '@material-ui/core/Button';
import { makeStyles, useTheme } from '@material-ui/core/styles';
import { monacoLoader, MonacoEditor } from '@rimoe/react-monaco-editor';

import { monacoConfig, options as monacoOptions, languageDef, configuration as langConf, createCompletionItems } from './editor-config';

monacoLoader.config(monacoConfig);

const useStyles = makeStyles((theme) => ({

}));

export default function RuleEditor(props) {
    const theme = useTheme();
    const classes = useStyles();

    const [editMode, setEditMode] = React.useState(0); // 0 => 所见即所得编辑模式，1 => 规则文本编辑模式
    const [ruleStructure, setRuleStructure] = React.useState({ "#Main": ["uid", "==", 1] });
    const [ruleText, setRuleText] = React.useState("");

    const handleSwitchMode = () => {
        setEditMode((mode) => [1, 0][mode]);
    };

    const handleEditorChange = (newText) => {
        setRuleText(newText);
    };

    const editorWillMount = (monaco) => {
        if (!monaco.languages.getLanguages().some(({ id }) => (id === "rule"))) {
            monaco.languages.register({ id: "rule" });
            monaco.languages.setMonarchTokensProvider("rule", languageDef);
            monaco.languages.setLanguageConfiguration("rule", langConf);
            monaco.languages.registerCompletionItemProvider("rule", {
                provideCompletionItems: (model, position) => {
                    const word = model.getWordUntilPosition(position);
                    const range = {
                        startLineNumber: position.lineNumber,
                        endLineNumber: position.lineNumber,
                        startColumn: word.startColumn,
                        endColumn: word.endColumn,
                    };
                    return {
                        suggestions: createCompletionItems(range, monaco),
                    };
                }
            });
        }
    };

    return (
        <>
            <InputLabel required shrink>规则内容</InputLabel>
            <div style={{ marginTop: theme.spacing(1) }} />
            <Button
                variant="contained"
                color={["primary", "secondary"][editMode]}
                onClick={handleSwitchMode}
            >切换到{["规则文本", "所见即所得"][editMode]}编辑模式</Button>
            <div style={{ marginTop: theme.spacing(1) }} />
            {editMode === 0 ? (
                <div />
            ) : (
                <MonacoEditor
                    height="600px"
                    language="rule"
                    theme="vs-dark"
                    value={ruleText}
                    onChange={handleEditorChange}
                    editorWillMount={editorWillMount}
                    options={monacoOptions}
                />
            )}
        </>
    );
}

import React from 'react';
import axios from 'axios';
import qs from 'qs';
import InputLabel from '@material-ui/core/InputLabel';
import Button from '@material-ui/core/Button';
import Select from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';
import TextField from '@material-ui/core/TextField';
import ArrowDownwardIcon from '@material-ui/icons/ArrowDownward';
import ArrowUpwardIcon from '@material-ui/icons/ArrowUpward';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import Backdrop from '@material-ui/core/Backdrop';
import CircularProgress from '@material-ui/core/CircularProgress';
import { makeStyles, useTheme } from '@material-ui/core/styles';
import { monacoLoader, MonacoEditor } from '@rimoe/react-monaco-editor';

import { monacoConfig, options as monacoOptions, languageDef, configuration as langConf, createCompletionItems } from './editor-config';

monacoLoader.config(monacoConfig);

const useStyles = makeStyles((theme) => ({
    judgeBlock: {
        marginTop: theme.spacing(1),
    },
    judgeBlockLabel: {
        boxSizing: "border-box",
        width: "100%",
        lineHeight: 1,
        padding: "9px 14px",
        color: "rgba(0, 0, 0, 0.38)",
        fontWeight: 700,
        fontSize: "12px",
        border: "1px solid rgba(0, 0, 0, 0.12)",
        borderRadius: "3px 3px 0 0",
        userSelect: "none",
    },
    judgeBlockContent: {
        border: "1px solid rgba(0, 0, 0, 0.12)",
        borderTop: "none",
        borderRadius: "0 0 3px 3px",
        padding: "14px",
        '&>div:nth-child(2)': {
            marginTop: theme.spacing(1),
        },
    },
    backdrop: {
        zIndex: theme.zIndex.drawer + 1,
        color: '#fff',
    },
}));

function NameSelect({ value, onChange, ...other }) {
    // ['uid', 'nick', 'email', 'url', 'content', 'length', 'ip', 'ua']
    return (
        <Select
            value={value}
            onChange={onChange}
            {...other}
        >
            <MenuItem value="uid">UID</MenuItem>
            <MenuItem value="nick">昵称</MenuItem>
            <MenuItem value="email">邮箱</MenuItem>
            <MenuItem value="url">个人主页</MenuItem>
            <MenuItem value="content">评论内容</MenuItem>
            <MenuItem value="length">评论有效长度</MenuItem>
            <MenuItem value="ip">IP</MenuItem>
            <MenuItem value="ua">User-Agent</MenuItem>
        </Select>
    );
}

function OperatorSelect({ value, isNumeric, onChange, ...other }) {
    // ['==', '!=', '<', '>', '<=', '>=', '<-', '~']
    return isNumeric ? (
        <Select
            value={value}
            onChange={onChange}
            {...other}
        >
            <MenuItem value="==">等于</MenuItem>
            <MenuItem value="!=">不等于</MenuItem>
            <MenuItem value="<">小于</MenuItem>
            <MenuItem value=">">大于</MenuItem>
            <MenuItem value="<=">小于或等于</MenuItem>
            <MenuItem value=">=">大于或等于</MenuItem>
        </Select>
        ) : (
        <Select
            value={value}
            onChange={onChange}
        >
            <MenuItem value="==">等于（全字匹配）</MenuItem>
            <MenuItem value="!=">不等于（全字匹配）</MenuItem>
            <MenuItem value="<-">包含</MenuItem>
            <MenuItem value="~">符合（正则表达式）</MenuItem>
        </Select>
    );
}

function ActionSelect({ value, onChange, ...other }) {
    // ['accept', 'review', 'spam', 'deny', 'skip']
    return (
        <Select
            value={value}
            onChange={onChange}
            {...other}
        >
            <MenuItem value="skip">无动作</MenuItem>
            <MenuItem value="accept">通过评论</MenuItem>
            <MenuItem value="review">标记为待审核</MenuItem>
            <MenuItem value="spam">标记为垃圾</MenuItem>
            <MenuItem value="deny">拒绝评论</MenuItem>
            <MenuItem value="judge">继续判断</MenuItem>
        </Select>
    );
}

function randomKey() {
    let res = "#";
    for (let i = 0; i < 6; i++) res += Math.floor(Math.random() * 16).toString(16);
    return res.toUpperCase();
}

function translateData(data) {
    if (typeof data !== "object" || !data) return undefined;
    const structure = [];
    for (let key in data) {
        const current = data[key];
        if (!current.then) current.then = "skip";
        if (!current.else) current.else = "skip";
        structure.push([
            key, current.name, current.optr, current.target,
            (current.then.indexOf("#") !== -1)
                ? ["judge", current.then]
                : [current.then],
            (current.else.indexOf("#") !== -1)
                ? ["judge", current.else]
                : [current.else],
            current.parent ?? undefined
        ]);
    }
    if (structure.length === 0) return undefined;
    return structure;
}

const RuleEditor = React.forwardRef((props, ref) => {
    const theme = useTheme();
    const classes = useStyles();

    const onChange = props.onChange ?? (() => {});
    const disabled = props.disabled ?? false;
    const [editMode, setEditMode] = React.useState(props.defaultEditMode ?? 0); // 0 => 所见即所得编辑模式，1 => 规则文本编辑模式
    const [confirmDialogOpen, setConfirmDialogOpen] = React.useState(false);
    const [backdropOpen, setBackdropOpen] = React.useState(false);
    const [compileError, setCompileError] = React.useState("");
    const [compileErrorDialogOpen, setCompileErrorDialogOpen] = React.useState(false);
    const [ruleStructure, setRuleStructure] = React.useState(translateData(props.defaultRuleData)
        ?? [["#Main", "uid", "==", "", ["skip"], ["skip"], undefined]]);
    const [highlightBlock, setHighlightBlock] = React.useState([undefined, undefined]);
    const [ruleText, setRuleText] = React.useState(props.defaultRuleText ?? "");

    const structure2Rule = () => { // 使用 structure2Rule() 获得完整规则文本
        const _structure2Rule = (index) => {
            const [, name, operator, _value, _then, _otherwise] = ruleStructure[index];
            const value = (["uid", "length"].indexOf(name) === -1 && operator !== "~")
                ? `'${_value.replace(/\\/g, '\\\\').replace(/'/g, '\\\'')}'`
                : _value;
            const then = (_then[0] === "judge")
                ? _structure2Rule(ruleStructure.findIndex(([key]) => (key === _then[1])))
                : _then[0];
            const otherwise = (_otherwise[0] === "judge")
                ? _structure2Rule(ruleStructure.findIndex(([key]) => (key === _otherwise[1])))
                : _otherwise[0];
            return `[ ${name} ${operator} ${value} ] : ${then} ! ${otherwise} ;`;
        }
        return _structure2Rule(0);
    };

    React.useImperativeHandle(ref, () => ({
        editMode: editMode,
        getRuleText: () => (editMode === 0 ? structure2Rule() : ruleText),
    }));

    const isFirstRun = React.useRef(true);
    React.useEffect(() => {
        if (isFirstRun.current) {
            isFirstRun.current = false;
            return;
        }
        onChange();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [editMode, ruleStructure, ruleText]);

    const handleSwitchMode = () => {
        setConfirmDialogOpen(true);
    };

    const handleConfirmDialogClose = () => {
        setConfirmDialogOpen(false);
    };

    const source = React.useRef();
    React.useEffect(() => (() => {
        if (backdropOpen) source.current.cancel();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }), []);

    const handleConfirmDialogConfirmClick = () => {
        setConfirmDialogOpen(false);
        setEditMode((mode) => {
            if (mode === 0) setRuleText(structure2Rule());
            else {
                if (ruleText.trim() === "") return 0;
                source.current = axios.CancelToken.source();
                setBackdropOpen(true);
                axios.post(window.__pageData.apiBase, qs.stringify({
                    input: ruleText,
                }), {
                    params: {
                        a: "translate",
                    },
                    cancelToken: source.current.token,
                }).then(({ data, status }) => {
                    if (status === 204) setEditMode(0);
                    else if (status === 201) {
                        setCompileError(data.result);
                        setCompileErrorDialogOpen(true);
                    } else if (status === 200) {
                        setRuleStructure(translateData(data));
                        setEditMode(0);
                    } else console.warn(`Unknown status code: ${status}.`);
                    setBackdropOpen(false);
                }).catch((error) => {
                    if (!axios.isCancel(error)) {
                        console.error(error);
                        alert("请求后端 API 失败！");
                    }
                    setBackdropOpen(false);
                });
            }
            return 1;
        });
    }

    const handleCompileErrorDialogClose = () => {
        setCompileErrorDialogOpen(false);
    };

    const handleNameSwitchChange = (index, event) => {
        const { value } = event.target; // see https://stackoverflow.com/a/56629758
        setRuleStructure((oldValue) => {
            let newValue = [...oldValue];
            newValue[index][1] = value;
            newValue[index][2] = "==";
            newValue[index][3] = "";
            return newValue;
        });
    };

    const handleOperatorSwitchChange = (index, event) => {
        const { value } = event.target;
        setRuleStructure((oldValue) => {
            let newValue = [...oldValue];
            newValue[index][2] = value;
            return newValue;
        });
    };

    const handleValueTextChange = (index, event) => {
        const { value } = event.target;
        setRuleStructure((oldValue) => {
            let newValue = [...oldValue];
            newValue[index][3] = value;
            return newValue;
        });
    };

    const handleActionSelectChange = (index, actionType, event) => {
        const { value } = event.target;
        setRuleStructure((oldValue) => {
            let newValue = [...oldValue], action = [...oldValue[index][4 + actionType]];
            if (action[0] === value) return newValue;
            if (value === "judge") { // 新增一个 block
                const newKey = randomKey();
                action = ["judge", newKey];
                newValue.push([newKey, "uid", "==", "", ["skip"], ["skip"], oldValue[index][0]]);
            } else if (action[0] === "judge") { // 删除（可能是）一些 blocks
                const subIndexes = [newValue.findIndex(([key]) => (key === action[1]))];
                while (subIndexes.length > 0) {
                    const refIndex = subIndexes.pop(), refData = [...newValue[refIndex]];
                    if (refIndex < index) index--; // 此时当前项目的 index 将会前移
                    newValue = newValue.slice(0, refIndex).concat(newValue.slice(refIndex + 1));
                    if (refData[4][0] === "judge") subIndexes.push(newValue.findIndex(([key]) => (key === refData[4][1])));
                    if (refData[5][0] === "judge") subIndexes.push(newValue.findIndex(([key]) => (key === refData[5][1])));
                }
                action = [value];
            } else action = [value];
            newValue[index][4 + actionType] = action;
            return newValue;
        });
    };

    const handleHighlightButtonClick = (key) => {
        ((element) => (element && element.scrollIntoView()))(document.querySelector(`div[data-key="${key}"]`));
        setHighlightBlock(([_, timeoutId]) => {
            if (timeoutId !== undefined) clearTimeout(timeoutId);
            return [key, setTimeout(() => {
                setHighlightBlock([undefined, undefined]);
            }, 1000)];
        });
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
            <InputLabel required shrink disabled={disabled}>规则内容</InputLabel>
            <div style={{ marginTop: theme.spacing(1) }} />
            <Button
                variant="outlined"
                color={["primary", "secondary"][editMode]}
                onClick={handleSwitchMode}
                disabled={disabled}
            >切换到{["规则文本", "所见即所得"][editMode]}编辑模式</Button>
            <div style={{ marginTop: theme.spacing(1) }} />
            {editMode === 0 ? (
                <div>{ruleStructure.map(([key, name, operator, value, then, otherwise, refKey], index) => {
                    const isNumeric = ["uid", "length"].indexOf(name) !== -1;
                    const isHighlight = highlightBlock[0] === key;
                    return (
                        <div key={key} className={classes.judgeBlock} data-key={key}>
                            <div
                                className={classes.judgeBlockLabel}
                                style={isHighlight ? { background: "#fffdd1" } : {}}
                            >{key}</div>
                            <div className={classes.judgeBlockContent} style={disabled ? { color: "rgba(0, 0, 0, 0.38)" } : {}}>
                                {/* 如果 {name} {operator} {value}，那么 {then}，否则 {otherwise} */}
                                <div>
                                    {refKey !== undefined && (<>
                                        <Button
                                            startIcon={<ArrowUpwardIcon />}
                                            onClick={() => handleHighlightButtonClick(refKey)}
                                            disabled={disabled}
                                        >{refKey}</Button>
                                        &emsp;
                                    </>)}
                                    如果&emsp;
                                    <NameSelect
                                        value={name}
                                        onChange={(event) => handleNameSwitchChange(index, event)}
                                        disabled={disabled}
                                    />
                                    &emsp;
                                    <OperatorSelect
                                        value={operator}
                                        isNumeric={isNumeric}
                                        onChange={(event) => handleOperatorSwitchChange(index, event)}
                                        disabled={disabled}
                                    />
                                    &emsp;
                                    {isNumeric ? (
                                        <TextField
                                            value={value}
                                            onChange={(event) => handleValueTextChange(index, event)}
                                            required
                                            type="number"
                                            margin="none"
                                            disabled={disabled}
                                        />
                                    ) : (
                                        <TextField
                                            value={value}
                                            onChange={(event) => handleValueTextChange(index, event)}
                                            multiline
                                            fullWidth
                                            helperText="允许多行，书写字符串时两侧无需加引号，书写正则表达式时需要使用 / 作为分隔符"
                                            margin="none"
                                            inputProps={{
                                                style: { fontFamily: "'Fira Code', Consolas, 'Courier New', monospace" }
                                            }}
                                            disabled={disabled}
                                        />
                                    )}
                                </div>
                                <div>
                                    那么&emsp;
                                    <ActionSelect
                                        value={then[0]}
                                        onChange={(event) => handleActionSelectChange(index, 0, event)}
                                        disabled={disabled}
                                    />
                                    {then[0] === "judge" && (<>
                                        &emsp;
                                        <Button
                                            startIcon={<ArrowDownwardIcon />}
                                            onClick={() => handleHighlightButtonClick(then[1])}
                                            disabled={disabled}
                                        >{then[1]}</Button>
                                    </>)}
                                    &emsp;
                                    否则&emsp;
                                    <ActionSelect
                                        value={otherwise[0]}
                                        onChange={(event) => handleActionSelectChange(index, 1, event)}
                                        disabled={disabled}
                                    />
                                    {otherwise[0] === "judge" && (<>
                                        &emsp;
                                        <Button
                                            startIcon={<ArrowDownwardIcon />}
                                            onClick={() => handleHighlightButtonClick(otherwise[1])}
                                            disabled={disabled}
                                        >{otherwise[1]}</Button>
                                    </>)}
                                </div>
                            </div>
                        </div>
                    );
                })}</div>
            ) : (
                <MonacoEditor
                    height="600px"
                    language="rule"
                    theme="vs-dark"
                    value={ruleText}
                    onChange={handleEditorChange}
                    editorWillMount={editorWillMount}
                    options={{ readOnly: disabled, ...monacoOptions }}
                />
            )}
            <Dialog
                open={confirmDialogOpen}
                onClose={handleConfirmDialogClose}
            >
                <DialogTitle>确定要切换吗？</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        由于对已有内容的转换不保证 100% 兼容，我们更建议仅使用一种模式来编辑规则。（如果您还没有开始编辑规则，请忽略此提示直接点击确定）
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleConfirmDialogConfirmClick} color="secondary">
                        确定
                    </Button>
                    <Button onClick={handleConfirmDialogClose} color="primary" autoFocus>
                        取消
                    </Button>
                </DialogActions>
            </Dialog>
            <Dialog
                open={compileErrorDialogOpen}
                onClose={handleCompileErrorDialogClose}
            >
                <DialogTitle>规则编译失败！</DialogTitle>
                <DialogContent>
                    <DialogContentText
                        dangerouslySetInnerHTML={{
                            __html: compileError + "<br />如果需要强制切换，请将输入框清空。强制切换将会使当前编辑内容丢失。"
                        }}
                    />
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleCompileErrorDialogClose} color="primary" autoFocus>
                        确定
                    </Button>
                </DialogActions>
            </Dialog>
            <Backdrop className={classes.backdrop} open={backdropOpen}>
                <CircularProgress color="inherit" />
            </Backdrop>
        </>
    );
});

export default RuleEditor;

import React from 'react';
import axios from 'axios';
import qs from 'qs';
import Fade from '@material-ui/core/Fade';
import CircularProgress from '@material-ui/core/CircularProgress';
import Container from '@material-ui/core/Container';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import TextField from '@material-ui/core/TextField';
import Checkbox from '@material-ui/core/Checkbox';
import Button from '@material-ui/core/Button';
import SaveIcon from '@material-ui/icons/Save';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import Snackbar from '@material-ui/core/Snackbar';
import MuiAlert from '@material-ui/lab/Alert';
import makeStyles from '@material-ui/core/styles/makeStyles';
import useTheme from '@material-ui/core/styles/useTheme';
import { green } from '@material-ui/core/colors';

import RuleEditor from './RuleEditor';

const useStyles = makeStyles((theme) => ({
    placeholder: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        paddingTop: theme.spacing(8),
    },
    nameInput: {
        width: "calc(75% - " + theme.spacing(2) + "px)",
        [theme.breakpoints.up('md')]: {
            width: "50%",
        },
    },
    wrapper: {
        position: 'relative',
        display: 'inline-block',
    },
    buttonSuccess: {
        backgroundColor: green[500],
        '&:hover': {
            backgroundColor: green[700],
        },
    },
    buttonProgress: {
        color: green[500],
        position: 'absolute',
        top: '50%',
        left: '50%',
        marginTop: -12,
        marginLeft: -12,
    },
}));

function createData(name, ruid, remark, status, priority) {
    return { name, ruid, remark, status, priority };
}

function transformRuleset(ruleset) {
    if (typeof ruleset !== "object" || !ruleset) return [];
    const rules = [];
    for (let ruid in ruleset) {
        const { name, remark, status, priority } = ruleset[ruid];
        rules.push(createData(name, ruid, remark, status, priority));
    }
    return rules;
}

export default function EditPage(props) {
    const theme = useTheme();
    const classes = useStyles();
    const { match, history } = props;
    const [ruid, setRUID] = React.useState(match.params.ruid ?? false);
    const [initialState, setInitialState] = React.useState(ruid ? false : true);
    const [name, setName] = React.useState("");
    const [remark, setRemark] = React.useState("");
    const [status, setStatus] = React.useState(["on"]);
    const [priority, setPriority] = React.useState(10);
    const [nameError, setNameError] = React.useState(false);
    const [priorityError, setPriorityError] = React.useState(false);
    const [editMode, setEditMode] = React.useState(0);
    const [ruleData, setRuleData] = React.useState(undefined);
    const [ruleText, setRuleText] = React.useState("");
    const [compileMessage, setCompileMessage] = React.useState("");
    const [isSaved, setIsSaved] = React.useState(ruid ? true : false);
    const [saving, setSaving] = React.useState(false);
    const [adjusted, setAdjusted] = React.useState(false);
    const [[dialogOpen, dialogTitle, dialogContent, dialogButton], setDialog] = React.useState([false, "", "", ""]);
    const editorRef = React.useRef(); // 使用 editorRef.current.getRuleText() 来获取规则文本

    const source = React.useRef();
    React.useEffect(() => {
        source.current = axios.CancelToken.source();
        if (!initialState) {
            axios.get(window.__pageData.apiBase, {
                params: {
                    a: "ruleDetails",
                    ruid: ruid,
                },
                cancelToken: source.current.token,
            }).then(({ data }) => {
                setName(data.name);
                setRemark(data.remark);
                setStatus(data.status);
                setPriority(data.priority);
                setEditMode(data.editMode);
                if (data.editMode === 0) setRuleData(data.ruleData);
                else setRuleText(data.ruleText);
                setCompileMessage(data.compileMessage);
                if (editorRef.current !== undefined) editorRef.current.sync();
                setInitialState(true);
            }).catch((error) => {
                if (!axios.isCancel(error)) {
                    console.error(error);
                    history.push("/overview");
                }
            });
        }
        return () => {
            if (!initialState || saving) source.current.cancel();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const handleNameChange = (event) => {
        const { value } = event.target;
        setName(value);
        setNameError(value === "");
        setIsSaved(false);
    };

    const handleIsEnabledChange = (event) => {
        const { checked } = event.target;
        let newStatus;
        if (checked) {
            newStatus = ["on", ...status.filter((val) => (val !== "off" && val !== "uncompiled"))];
        } else {
            newStatus = ["off", ...status.filter((val) => (val !== "on"))];
        }
        setStatus(newStatus);
        setIsSaved(false);
    };

    const handlePriorityChange = (event) => {
        const { value } = event.target;
        setPriority(value);
        setPriorityError(!/^(0|[1-9][0-9]{0,4})$/.test(value));
        setIsSaved(false);
    };

    const handleRemarkChange = (event) => {
        const { value } = event.target;
        setRemark(value);
        setIsSaved(false);
    };

    const handleRuleEditorChange = () => {
        setIsSaved(false);
    };

    const handleRuleEditorNetworkError = () => {
        setDialog([true, "请求后端 API 失败！", "可能是网络错误或后端处理失败，请打开开发人员工具查看详细信息。", "确定"]);
    };

    const handleSaveButtonClick = () => {
        if (isSaved || saving) return;

        let valid = true;
        if (name === "") {
            setNameError(true);
            valid = false;
        }
        if (!/^(0|[1-9][0-9]{0,4})$/.test(priority)) {
            setPriorityError(true);
            valid = false;
        }
        if (!valid) return;
        if (!editorRef.current.validate()) {
            setDialog([true, "规则内容不合法！", "请检查数值比较的值是否合法，只有非负整数是合法的，非法的值将无法通过编译。", "确定"]);
            return;
        }
        const ruleText = editorRef.current.getRuleText();
        if (ruleText.trim() === "") {
            setDialog([true, "规则内容不能为空！", "空规则将不起作用，请设置规则内容。", "确定"]);
            return;
        }

        source.current = axios.CancelToken.source();
        setSaving(true);
        axios.post(window.__pageData.apiBase, qs.stringify({
            ruid: ruid ? ruid : "",
            name: name,
            status: status,
            remark: remark,
            priority: priority,
            rule: ruleText,
            editMode: editorRef.current.editMode,
        }), {
            params: {
                a: "saveRule",
            },
            cancelToken: source.current.token,
        }).then(({ data: { code, ruid, ruleset }, status }) => {
            if (status === 200) {
                if (!ruid) {
                    setRUID(ruid);
                    history.push("/edit/" + ruid);
                }
                window.__pageData.rules = transformRuleset(ruleset);
                const { status, compileMessage } = ruleset[ruid];
                setStatus(status);
                setCompileMessage(compileMessage);
                setIsSaved(true);
                if (code === 201) {
                    setAdjusted(true);
                }
            } else if (status === 201) {
                setDialog([true, "后端 API 返回错误！",
                    "保存规则时遇到了未知错误，请检查插件目录权限，必要时请移步 <a href='https://github.com/wuxianucw/Typecho-CommentRuleset/issues' target='_blank'>GitHub issues</a>。",
                    "确定"]);
            } else console.warn(`Unknown status code: ${status}.`);
            setSaving(false);
        }).catch((error) => {
            if (!axios.isCancel(error)) {
                console.error(error);
                setDialog([true, "请求后端 API 失败！", "可能是网络错误或后端处理失败，请打开开发人员工具查看详细信息。", "确定"]);
            }
            setSaving(false);
        });
    };

    const handleIsCompiledChange = (event) => {
        const { checked } = event.target;
        let newStatus;
        if (checked) {
            newStatus = status.filter((val) => (val !== "uncompiled"));
        } else {
            newStatus = ["off", "uncompiled"];
        }
        setStatus(newStatus);
        setIsSaved(false);
    };

    const handleDialogClose = () => {
        setDialog(([, ...oldValue]) => {
            return [false, ...oldValue];
        })
    };

    return initialState ? (
        <Container>
            <FormGroup row>
                <TextField
                    value={name}
                    onChange={handleNameChange}
                    inputProps={{
                        onBlur: (event) => setNameError(event.target.value === ""),
                    }}
                    required
                    error={nameError}
                    helperText={nameError ? "规则名称不能为空" : ""}
                    label="规则名称"
                    variant="outlined"
                    margin="normal"
                    className={classes.nameInput}
                    disabled={saving}
                />
                <TextField
                    value={priority}
                    onChange={handlePriorityChange}
                    required
                    error={priorityError}
                    helperText={priorityError ? "请输入一个 0 到 99999 范围内的整数" : "数值越大的规则优先级越高"}
                    type="number"
                    label="优先级"
                    variant="outlined"
                    margin="normal"
                    style={{ width: "25%", marginLeft: theme.spacing(2) }}
                    disabled={saving}
                />
                <div style={{ flexGrow: 1 }} />
                <FormControlLabel
                    control={
                        <Checkbox
                            checked={status.indexOf("on") !== -1}
                            onChange={handleIsEnabledChange}
                            disabled={saving}
                        />
                    }
                    label="启用规则"
                />
            </FormGroup>
            <TextField
                value={remark}
                onChange={handleRemarkChange}
                multiline
                fullWidth
                label="规则备注"
                helperText="可以为空，允许多行"
                margin="normal"
                InputLabelProps={{ shrink: true }}
                disabled={saving}
            />
            <RuleEditor
                ref={editorRef}
                defaultEditMode={editMode}
                defaultRuleData={ruleData}
                defaultRuleText={ruleText}
                onChange={handleRuleEditorChange}
                onNetworkError={handleRuleEditorNetworkError}
                disabled={saving}
            />
            {compileMessage.length > 0 && (
                <MuiAlert
                    elevation={0}
                    variant="filled"
                    severity="error"
                    style={{ marginTop: theme.spacing(1) }}
                ><div dangerouslySetInnerHTML={{ __html: `规则编译失败：${compileMessage}` }} /></MuiAlert>
            )}
            <div>
                <FormControlLabel
                    control={
                        <Checkbox
                            checked={status.indexOf("uncompiled") === -1}
                            onChange={handleIsCompiledChange}
                            disabled={saving}
                        />
                    }
                    label="编译规则"
                />
            </div>
            <div className={classes.wrapper}>
                <Button
                    variant="contained"
                    disableElevation
                    color="primary"
                    className={isSaved ? classes.buttonSuccess : ""}
                    startIcon={<SaveIcon />}
                    onClick={handleSaveButtonClick}
                    disabled={saving}
                >{isSaved ? "已保存" : "保存"}</Button>
                {saving && <CircularProgress size={24} className={classes.buttonProgress} />}
            </div>
            <Snackbar open={adjusted} autoHideDuration={6000} onClose={() => setAdjusted(false)}>
                <MuiAlert elevation={6} variant="filled" onClose={() => setAdjusted(false)} severity="warning">
                    由于规则编译失败，规则状态已被调整。
                </MuiAlert>
            </Snackbar>
            <Dialog
                open={dialogOpen}
                onClose={handleDialogClose}
            >
                <DialogTitle>{dialogTitle}</DialogTitle>
                <DialogContent>
                    <DialogContentText
                        dangerouslySetInnerHTML={{
                            __html: dialogContent
                        }}
                    />
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleDialogClose} color="primary" autoFocus>
                        {dialogButton}
                    </Button>
                </DialogActions>
            </Dialog>
        </Container>
    ) : (
        <div className={classes.placeholder}>
            <Fade in unmountOnExit style={{ transitionDelay: '800ms' }}>
                <CircularProgress color="secondary" />
            </Fade>
        </div>
    );
}

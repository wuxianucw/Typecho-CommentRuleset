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
        marginTop: theme.spacing(1),
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

export default function EditPage(props) {
    const theme = useTheme();
    const classes = useStyles();
    const { match, history } = props;
    const ruid = match.params.ruid ?? false;
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
    const [isSaved, setIsSaved] = React.useState(ruid ? true : false);
    const [saving, setSaving] = React.useState(false);
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
                setInitialState(true);
            }).catch((error) => {
                if (!axios.isCancel(error)) history.push("/overview");
            });
        }
        return () => {
            if (!initialState || saving) source.current.cancel();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const handleNameChange = (event) => {
        setName(event.target.value);
        setNameError(event.target.value === "");
    };

    const handleIsEnabledChange = (event) => {
        let newStatus;
        if (event.target.checked) {
            newStatus = ["on"].concat(status);
        } else {
            newStatus = status.filter((val) => (val !== "on"));
        }
        setStatus(newStatus);
    };

    const handlePriorityChange = (event) => {
        setPriority(event.target.value);
        setPriorityError(!/^(0|[1-9][0-9]{0,4})$/.test(event.target.value));
    };

    const handleRemarkChange = (event) => {
        setRemark(event.target.value);
    };

    const handleRuleEditorChange = () => {
        setIsSaved(false);
    };

    const handleSaveButtonClick = () => {
        if (isSaved || saving) return;
        // TODO: 表单验证
        source.current = axios.CancelToken.source();
        setSaving(true);
        axios.post(window.__pageData.apiBase, qs.stringify({
            ruid: ruid ? ruid : "",
            name: name,
            status: status,
            remark: remark,
            priority: priority,
            rule: editorRef.current.getRuleText(),
            editMode: editorRef.current.editMode,
        }), {
            params: {
                a: "saveRule",
            },
            cancelToken: source.current.token,
        }).then(({ data, status }) => {
            // TODO
        }).catch((error) => {
            if (!axios.isCancel(error)) {
                // TODO
            }
        });
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
                disabled={saving}
            />
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
        </Container>
    ) : (
        <div className={classes.placeholder}>
            <Fade in unmountOnExit style={{ transitionDelay: '800ms' }}>
                <CircularProgress color="secondary" />
            </Fade>
        </div>
    );
}

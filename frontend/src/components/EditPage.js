import React from 'react';
import axios from 'axios';
import Fade from '@material-ui/core/Fade';
import CircularProgress from '@material-ui/core/CircularProgress';
import Container from '@material-ui/core/Container';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import TextField from '@material-ui/core/TextField';
import Checkbox from '@material-ui/core/Checkbox';
import makeStyles from '@material-ui/core/styles/makeStyles';
import useTheme from '@material-ui/core/styles/useTheme';

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

    React.useEffect(() => {
        const source = axios.CancelToken.source();
        if (!initialState) {
            axios.get(window.__pageData.apiBase, {
                params: {
                    a: "ruleDetails",
                    ruid: ruid,
                },
                cancelToken: source.token,
            }).then(({ data }) => {
                setInitialState(true);
                setName(data.name);
                setRemark(data.remark);
                setStatus(data.status);
                setPriority(data.priority);
            }).catch((error) => {
                if (!axios.isCancel(error)) history.push("/overview");
            });
        }
        return () => {
            if (!initialState) source.cancel();
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
                />
                <div style={{ flexGrow: 1 }} />
                <FormControlLabel
                    control={
                        <Checkbox
                            checked={status.indexOf("on") !== -1}
                            onChange={handleIsEnabledChange}
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
            />
        </Container>
    ) : (
        <div className={classes.placeholder}>
            <Fade in unmountOnExit style={{ transitionDelay: '800ms' }}>
                <CircularProgress color="secondary" />
            </Fade>
        </div>
    );
}

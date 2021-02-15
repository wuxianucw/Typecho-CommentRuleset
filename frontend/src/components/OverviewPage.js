import React from 'react';
import axios from 'axios';
import clsx from 'clsx';
import { lighten, makeStyles, useTheme } from '@material-ui/core/styles';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TablePagination from '@material-ui/core/TablePagination';
import TableRow from '@material-ui/core/TableRow';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import Paper from '@material-ui/core/Paper';
import Checkbox from '@material-ui/core/Checkbox';
import IconButton from '@material-ui/core/IconButton';
import Tooltip from '@material-ui/core/Tooltip';
import AddIcon from '@material-ui/icons/Add';
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import LockOpenIcon from '@material-ui/icons/LockOpen';
import CheckCircleOutlineIcon from '@material-ui/icons/CheckCircleOutline';
import HighlightOffIcon from '@material-ui/icons/HighlightOff';
import ReportProblemOutlinedIcon from '@material-ui/icons/ReportProblemOutlined';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import Backdrop from '@material-ui/core/Backdrop';
import CircularProgress from '@material-ui/core/CircularProgress';

const headCells = [
    { id: 'name', numeric: false, disablePadding: true, label: '规则名称' },
    { id: 'ruid', numeric: false, disablePadding: false, label: 'RUID', tooltip: '规则唯一标识符' },
    { id: 'remark', numeric: false, disablePadding: false, label: '规则备注' },
    { id: 'status', numeric: false, disablePadding: false, label: '规则状态' },
    { id: 'priority', numeric: true, disablePadding: false, label: '规则优先级', tooltip: '数值越大的规则优先级越高' },
];

function EnhancedTableHead(props) {
    const { onSelectAllClick, numSelected, rowCount } = props;

    return (
        <TableHead>
            <TableRow>
                <TableCell padding="checkbox">
                    <Checkbox
                        indeterminate={numSelected > 0 && numSelected < rowCount}
                        checked={rowCount > 0 && numSelected === rowCount}
                        onChange={onSelectAllClick}
                    />
                </TableCell>
                {headCells.map((headCell) => (headCell.tooltip ? (
                    <Tooltip key={headCell.id} title={headCell.tooltip}>
                        <TableCell
                            align={headCell.numeric ? 'right' : 'left'}
                            padding={headCell.disablePadding ? 'none' : 'default'}
                        >
                            {headCell.label}
                        </TableCell>
                    </Tooltip>
                ) : (
                    <TableCell
                        key={headCell.id}
                        align={headCell.numeric ? 'right' : 'left'}
                        padding={headCell.disablePadding ? 'none' : 'default'}
                    >
                        {headCell.label}
                    </TableCell>
                )))}
                <TableCell align="right">操作</TableCell>
            </TableRow>
        </TableHead>
    );
}

const useToolbarStyles = makeStyles((theme) => ({
    root: {
        paddingLeft: theme.spacing(2),
        paddingRight: theme.spacing(1),
    },
    highlight: {
        color: theme.palette.secondary.main,
        backgroundColor: lighten(theme.palette.secondary.light, 0.85),
    },
    title: {
        flex: '1 1 100%',
    },
}));

function EnhancedTableToolbar(props) {
    const classes = useToolbarStyles();
    const { numSelected, rowCount, disableDelete, onAddClick, onDeleteClick } = props;

    return (
        <Toolbar
            className={clsx(classes.root, {
                [classes.highlight]: numSelected > 0,
            })}
        >
            {numSelected > 0 ? (
                <Typography className={classes.title} color="inherit" variant="subtitle1" component="div">
                    选中 {numSelected} 条规则
                </Typography>
            ) : (
                <Typography className={classes.title} variant="h6" id="tableTitle" component="div">
                    目前共 {rowCount} 条规则
                </Typography>
            )}

            {numSelected > 0 ? (
                <Tooltip title={disableDelete ? "选中的规则中包含锁定的规则" : "删除规则"}>
                    <span><IconButton
                        disabled={disableDelete}
                        onClick={onDeleteClick}
                    ><DeleteIcon /></IconButton></span>
                </Tooltip>
            ) : (
                <Tooltip title="新增规则">
                    <IconButton onClick={onAddClick}><AddIcon /></IconButton>
                </Tooltip>
            )}
        </Toolbar>
    );
}

const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%',
    },
    paper: {
        width: '100%',
    },
    table: {
        minWidth: 750,
    },
    disableVerticalPadding: {
        padding: '0 16px',
    },
    statusIcon: {
        marginRight: theme.spacing(1),
        '& svg': {
            verticalAlign: 'bottom',
        },
    },
    backdrop: {
        zIndex: theme.zIndex.drawer + 1,
        color: '#fff',
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

export default function OverviewPage(props) {
    const theme = useTheme();
    const classes = useStyles();
    const { history } = props;
    const [rows, setRows] = React.useState(window.__pageData.rules);
    const [selected, setSelected] = React.useState([]);
    const [selectedLockedCount, setSelectedLockedCount] = React.useState(0);
    const [page, setPage] = React.useState(0);
    const [rowsPerPage, setRowsPerPage] = React.useState(10);
    const [remarkDialogOpen, setRemarkDialogOpen] = React.useState(false);
    const [remarkDialogContent, setRemarkDialogContent] = React.useState("");
    const [confirmDeleteDialogOpen, setConfirmDeleteDialogOpen] = React.useState(false);
    const [lockType, setLockType] = React.useState(['', true]);
    const [confirmLockDialogOpen, setConfirmLockDialogOpen] = React.useState(false);
    const [backdropOpen, setBackdropOpen] = React.useState(false);
    const [networkErrorDialogOpen, setNetworkErrorDialogOpen] = React.useState(false);

    React.useEffect(() => (() => {
        window.__pageData.rules = rows;
    }), [rows]);

    const isLocked = (ruid) => {
        const search = rows.filter((row) => row.ruid === ruid);
        if (search.length < 1) return false;
        return search[0].status.indexOf("locked") !== -1;
    };

    const handleSelectAllClick = (event) => {
        if (event.target.checked) {
            let newLockedCount = 0;
            const newSelecteds = rows.map((n) => {
                if (n.status.indexOf("locked") !== -1) newLockedCount++;
                return n.ruid;
            });
            setSelectedLockedCount(newLockedCount);
            setSelected(newSelecteds);
            return;
        }
        setSelectedLockedCount(0);
        setSelected([]);
    };

    const handleClick = (ruid) => {
        const selectedIndex = selected.indexOf(ruid);
        const isItemLocked = isLocked(ruid);
        let newSelected = [];

        if (selectedIndex === -1) {
            newSelected = newSelected.concat(selected, ruid);
        } else if (selectedIndex === 0) {
            newSelected = newSelected.concat(selected.slice(1));
        } else if (selectedIndex === selected.length - 1) {
            newSelected = newSelected.concat(selected.slice(0, -1));
        } else if (selectedIndex > 0) {
            newSelected = newSelected.concat(
                selected.slice(0, selectedIndex),
                selected.slice(selectedIndex + 1),
            );
        }

        if (isItemLocked) setSelectedLockedCount(selectedLockedCount + (selectedIndex === -1 ? 1 : -1));

        setSelected(newSelected);
    };

    const handleChangePage = (_, newPage) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    const handleRemarkDialogClose = () => {
        setRemarkDialogOpen(false);
    };

    const handleShowRemarkClick = (remark) => {
        const showRemark = remark === "" ? "（无备注）" : remark;
        setRemarkDialogContent(showRemark);
        setRemarkDialogOpen(true);
    };

    const isSelected = (ruid) => selected.indexOf(ruid) !== -1;

    const emptyRows = rowsPerPage - Math.min(rowsPerPage, rows.length - page * rowsPerPage);

    const statusMap = {
        on: {
            text: "生效",
            icon: <CheckCircleOutlineIcon htmlColor={theme.palette.success.dark} />,
        },
        off: {
            text: "未生效",
            icon: <HighlightOffIcon color="action" />,
        },
        locked: {
            text: "锁定",
            icon: <LockOutlinedIcon color="action" />,
        },
        uncompiled: {
            text: "未编译",
            icon: <ReportProblemOutlinedIcon htmlColor={theme.palette.warning.dark} />,
        },
    };

    const handleAddClick = () => {
        history.push("/edit");
    };

    const handleEditClick = (ruid) => {
        history.push("/edit/" + ruid);
    };

    const handleDeleteClick = () => {
        setConfirmDeleteDialogOpen(true);
    };

    const handleConfirmDeleteDialogClose = () => {
        setConfirmDeleteDialogOpen(false);
    };

    const source = React.useRef();
    React.useEffect(() => (() => {
        if (backdropOpen) source.current.cancel();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }), []);

    const handleConfirmDeleteDialogConfirmClick = () => {
        setConfirmDeleteDialogOpen(false);
        source.current = axios.CancelToken.source();
        setBackdropOpen(true);
        axios.get(window.__pageData.apiBase, {
            params: {
                a: "removeRules",
                ruid: selected,
            },
            cancelToken: source.current.token,
        }).then(({ data }) => {
            setRows(transformRuleset(data));
            setSelected([]);
            setBackdropOpen(false);
        }).catch((error) => {
            if (!axios.isCancel(error)) {
                console.error(error);
                setNetworkErrorDialogOpen(true);
            }
            setBackdropOpen(false);
        });
    };

    const handleLockClick = (ruid, isLocked) => {
        setLockType([ruid, !isLocked]);
        setConfirmLockDialogOpen(true);
    };

    const handleConfirmLockDialogClose = () => {
        setConfirmLockDialogOpen(false);
    };

    const handleConfirmLockDialogConfirmClick = () => {
        setConfirmLockDialogOpen(false);
        source.current = axios.CancelToken.source();
        setBackdropOpen(true);
        axios.get(window.__pageData.apiBase, {
            params: {
                a: "lockRule",
                ruid: lockType[0],
                opt: lockType[1] ? 1 : 0,
            },
            cancelToken: source.current.token,
        }).then(() => {
            setRows((rows) => {
                const newValue = rows;
                ((status) => {
                    if (lockType[1]) status.push("locked");
                    else status.splice(status.findIndex((i) => i === "locked"), 1);
                })(newValue.find((row) => row.ruid === lockType[0]).status);
                return newValue;
            });
            setBackdropOpen(false);
        }).catch((error) => {
            if (!axios.isCancel(error)) {
                console.error(error);
                setNetworkErrorDialogOpen(true);
            }
            setBackdropOpen(false);
        });
    };

    const handleNetworkErrorDialogClose = () => {
        setNetworkErrorDialogOpen(false);
    };

    return (
        <div className={classes.root}>
            <Paper className={classes.paper}>
                <EnhancedTableToolbar
                    numSelected={selected.length}
                    rowCount={rows.length}
                    disableDelete={selectedLockedCount > 0}
                    onAddClick={handleAddClick}
                    onDeleteClick={handleDeleteClick}
                />
                <TableContainer>
                    <Table
                        className={classes.table}
                    >
                        <EnhancedTableHead
                            classes={classes}
                            numSelected={selected.length}
                            onSelectAllClick={handleSelectAllClick}
                            rowCount={rows.length}
                        />
                        <TableBody>
                            {rows.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                                .map((row) => {
                                    const isItemSelected = isSelected(row.ruid);
                                    const isItemLocked = row.status.indexOf("locked") !== -1;

                                    return (
                                        <TableRow
                                            hover
                                            role="checkbox"
                                            tabIndex={-1}
                                            key={row.ruid}
                                            selected={isItemSelected}
                                        >
                                            <TableCell padding="checkbox">
                                                <Checkbox
                                                    onClick={() => handleClick(row.ruid)}
                                                    checked={isItemSelected}
                                                />
                                            </TableCell>
                                            <TableCell component="th" scope="row" padding="none">
                                                {row.name}
                                            </TableCell>
                                            <TableCell><code>{row.ruid}</code></TableCell>
                                            <TableCell className={classes.disableVerticalPadding}>
                                                <Button onClick={() => handleShowRemarkClick(row.remark)}>点击查看</Button>
                                            </TableCell>
                                            <TableCell className={classes.disableVerticalPadding}>
                                                {row.status.map((status) => (
                                                    <Tooltip key={status} title={statusMap[status].text}>
                                                        <span className={classes.statusIcon}>{statusMap[status].icon}</span>
                                                    </Tooltip>
                                                ))}
                                            </TableCell>
                                            <TableCell align="right">{row.priority}</TableCell>
                                            <TableCell align="right" className={classes.disableVerticalPadding}>
                                                <Tooltip title={isItemLocked ? "锁定状态下禁止编辑" : "编辑"}>
                                                    <span style={{ display: "inline-block" }}>
                                                        <IconButton
                                                            disabled={isItemLocked}
                                                            onClick={() => handleEditClick(row.ruid)}
                                                        ><EditIcon /></IconButton>
                                                    </span>
                                                </Tooltip>
                                                <Tooltip title={isItemLocked ? "解锁" : "锁定"}>
                                                    <span style={{ display: "inline-block" }}>
                                                        <IconButton onClick={() => handleLockClick(row.ruid, isItemLocked)}>
                                                            {isItemLocked ? <LockOpenIcon /> : <LockOutlinedIcon />}
                                                        </IconButton>
                                                    </span>
                                                </Tooltip>
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            {emptyRows > 0 && (
                                <TableRow style={{ height: 53 * emptyRows }}>
                                    <TableCell colSpan={headCells.length + 2} />
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </TableContainer>
                <TablePagination
                    rowsPerPageOptions={[5, 10, 25]}
                    component="div"
                    count={rows.length}
                    rowsPerPage={rowsPerPage}
                    page={page}
                    backIconButtonText="上一页"
                    nextIconButtonText="下一页"
                    labelRowsPerPage="每页规则数："
                    labelDisplayedRows={({ from, to, count }) => `${count} 条中的第 ${from} 至 ${to} 条`}
                    onChangePage={handleChangePage}
                    onChangeRowsPerPage={handleChangeRowsPerPage}
                />
            </Paper>
            <Dialog
                open={remarkDialogOpen}
                fullWidth
                onClose={handleRemarkDialogClose}
            >
                <DialogTitle>查看规则备注</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        {remarkDialogContent}
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleRemarkDialogClose} color="primary" autoFocus>关闭</Button>
                </DialogActions>
            </Dialog>
            <Dialog
                open={confirmDeleteDialogOpen}
                onClose={handleConfirmDeleteDialogClose}
            >
                <DialogTitle>确定删除这些规则吗？</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        此操作不可逆，被删除的规则将无法恢复。
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleConfirmDeleteDialogConfirmClick} color="secondary">
                        确定
                    </Button>
                    <Button onClick={handleConfirmDeleteDialogClose} color="primary" autoFocus>
                        取消
                    </Button>
                </DialogActions>
            </Dialog>
            <Dialog
                open={confirmLockDialogOpen}
                onClose={handleConfirmLockDialogClose}
            >
                <DialogTitle>确定{lockType[1] ? "锁定" : "解锁"}这条规则吗？</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        {lockType[1] ? "规则被锁定后，除非手动解锁，否则将无法被编辑。这能够有效降低误操作的可能性。"
                            : "规则被解锁后，将可以直接被编辑。这可能会增加误操作的风险。"}
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleConfirmLockDialogConfirmClick} color="secondary">
                        确定
                    </Button>
                    <Button onClick={handleConfirmLockDialogClose} color="primary" autoFocus>
                        取消
                    </Button>
                </DialogActions>
            </Dialog>
            <Backdrop className={classes.backdrop} open={backdropOpen}>
                <CircularProgress color="inherit" />
            </Backdrop>
            <Dialog
                open={networkErrorDialogOpen}
                onClose={handleNetworkErrorDialogClose}
            >
                <DialogTitle>请求后端 API 失败！</DialogTitle>
                <DialogContent>
                    <DialogContentText>可能是网络错误或后端处理失败，请打开开发人员工具查看详细信息。</DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleNetworkErrorDialogClose} color="primary" autoFocus>
                        确定
                    </Button>
                </DialogActions>
            </Dialog>
        </div>
    );
}

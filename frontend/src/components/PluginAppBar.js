import React from 'react';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import ExtensionIcon from '@material-ui/icons/Extension';
import AccountCircle from '@material-ui/icons/AccountCircle';
import ExitToAppIcon from '@material-ui/icons/ExitToApp';
import GitHubIcon from '@material-ui/icons/GitHub';
import Tooltip from '@material-ui/core/Tooltip';
import Typography from '@material-ui/core/Typography';
import Badge from '@material-ui/core/Badge';
import useScrollTrigger from '@material-ui/core/useScrollTrigger';
import makeStyles from '@material-ui/core/styles/makeStyles';

const useStyle = makeStyles((theme) => ({
    appBar: {
        [theme.breakpoints.up('sm')]: {
            zIndex: theme.zIndex.drawer + 1,
        },
    },
    title: {
        flexGrow: 1,
    },
    leftButton: {
        marginRight: theme.spacing(2),
    },
    noColor: {
        color: "inherit",
    },
    sectionDesktop: {
        display: 'none',
        [theme.breakpoints.up('md')]: {
            display: 'flex',
        },
    },
    sectionMobile: {
        display: 'flex',
        [theme.breakpoints.up('md')]: {
            display: 'none',
        },
    },
}));

function ElevationScroll(props) {
    const trigger = useScrollTrigger({
        disableHysteresis: true,
        threshold: 0,
    });

    return React.cloneElement(props.children, {
        elevation: trigger ? 4 : 0,
    });
}

export default function PluginAppBar(props) {
    const classes = useStyle();
    const updateAvailable = props.updateAvailable ?? false;

    return (
        <ElevationScroll>
            <AppBar position="fixed" className={classes.appBar}>
                <Toolbar>
                    <div className={classes.sectionMobile}>
                        <IconButton
                            color="inherit"
                            edge="start"
                            onClick={props.onMenuButtonClick}
                            className={classes.leftButton}
                        >
                            <MenuIcon />
                        </IconButton>
                    </div>
                    <div className={classes.sectionDesktop}>
                        <IconButton
                            color="inherit"
                            edge="start"
                            className={classes.leftButton}
                        >
                            <ExtensionIcon />
                        </IconButton>
                    </div>
                    <Typography variant="h6" noWrap className={classes.title}>
                        {window.__pageData.title}
                    </Typography>
                    <div className={classes.sectionDesktop}>
                        <Tooltip title={window.__pageData.account[0]}>
                            <a href={window.__pageData.account[1]} className={classes.noColor}>
                                <IconButton color="inherit"><AccountCircle /></IconButton>
                            </a>
                        </Tooltip>
                        <Tooltip title="登出">
                            <a href={window.__pageData.account[2]} className={classes.noColor}>
                                <IconButton color="inherit"><ExitToAppIcon /></IconButton>
                            </a>
                        </Tooltip>
                        <Tooltip title={`GitHub 仓库${updateAvailable ? '：有新版本可用' : ''}`}>
                            <a
                                className={classes.noColor}
                                href={`https://github.com/wuxianucw/Typecho-CommentRuleset${updateAvailable ? '/releases' : ''}`}
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <IconButton color="inherit" edge="end">
                                    <Badge color="error" variant="dot" invisible={!updateAvailable}>
                                        <GitHubIcon />
                                    </Badge>
                                </IconButton>
                            </a>
                        </Tooltip>
                    </div>
                    <div className={classes.sectionMobile}>
                        <Tooltip title={window.__pageData.account[0]}>
                            <a href={window.__pageData.account[1]} className={classes.noColor}>
                                <IconButton color="inherit" edge="end"><AccountCircle /></IconButton>
                            </a>
                        </Tooltip>
                    </div>
                </Toolbar>
            </AppBar>
        </ElevationScroll>
    );
}

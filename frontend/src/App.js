import React, { lazy, Suspense } from 'react';
import { HashRouter as Router, Route, Redirect } from 'react-router-dom';
import CssBaseline from '@material-ui/core/CssBaseline';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Fade from '@material-ui/core/Fade';
import CircularProgress from '@material-ui/core/CircularProgress';
import { darken, makeStyles } from '@material-ui/core/styles';
import { MuiThemeProvider, createMuiTheme } from '@material-ui/core';
import { blue, pink } from '@material-ui/core/colors';

import PluginAppBar from './components/PluginAppBar';
import ResponsiveDrawer from './components/ResponsiveDrawer';
import MenuList from './components/MenuList';

const OverviewPage = lazy(() => import('./components/OverviewPage'));
const EditPage = lazy(() => import('./components/EditPage'));
const GuidePage = lazy(() => import('./components/GuidePage'));

const theme = createMuiTheme({
    palette: {
        primary: {
            main: blue[700],
        },
        secondary: {
            main: darken(pink.A400, 0.1),
        },
        background: {
            default: '#fff',
        },
    },
});

const useStyles = makeStyles((theme) => ({
    root: {
        display: 'flex',
    },
    toolbar: theme.mixins.toolbar,
    content: {
        padding: theme.spacing(3),
    },
    placeholder: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        paddingTop: theme.spacing(8),
    },
}));

export default function App() {
    const classes = useStyles();
    const [mobileDrawerOpen, setMobileDrawerOpen] = React.useState(false);
    const [rules, setRules] = React.useState(window.__pageData.rules);

    const handleDrawerToggle = () => {
        setMobileDrawerOpen(!mobileDrawerOpen);
    };

    return (
        <Router>
            <MuiThemeProvider theme={theme}>
                <div className={classes.root}>
                    <CssBaseline />
                    <PluginAppBar onMenuButtonClick={handleDrawerToggle} />
                    <ResponsiveDrawer open={mobileDrawerOpen} onMobileClose={handleDrawerToggle}>
                        <div className={classes.toolbar} />
                        <MenuList />
                    </ResponsiveDrawer>
                    <div style={{ flexGrow: 1, width: '100%' }}>
                        <div className={classes.toolbar} />
                        <Route>
                            {({ location, history }) => {
                                const path = location.pathname.split("/").filter((val) => (val !== ""));

                                if (path.length < 1 || path.length > 2) return <Redirect to="/overview" />;
                                if (path.length === 2 && path[0] !== "edit") return <Redirect to="/overview" />;
                                if (["overview", "edit", "guide"].indexOf(path[0]) === -1) return <Redirect to="/overview" />;

                                const handleChange = (_, newValue) => {
                                    if (newValue === path[0]) return;
                                    history.push("/" + newValue);
                                };

                                return (
                                    <Tabs
                                        variant="fullWidth"
                                        value={path[0]}
                                        onChange={handleChange}
                                        indicatorColor="primary"
                                        textColor="primary"
                                        centered
                                    >
                                        <Tab label="规则总览" value="overview" />
                                        <Tab label={path.length > 1 ? `编辑规则 ${path[1]}` : "新增规则"} value="edit" />
                                        <Tab label="配置指南" value="guide" />
                                    </Tabs>
                                );
                            }}
                        </Route>
                        <main className={classes.content}>
                            <Suspense fallback={
                                <div className={classes.placeholder}>
                                    <Fade in unmountOnExit style={{ transitionDelay: '800ms' }}>
                                        <CircularProgress color="secondary" />
                                    </Fade>
                                </div>
                            }>
                                <Route path="/overview" component={(props) => <OverviewPage rows={rules} {...props} />} />
                                <Route path="/edit/:ruid?" component={EditPage} />
                                <Route path="/guide" component={GuidePage} />
                            </Suspense>
                        </main>
                    </div>
                </div>
            </MuiThemeProvider>
        </Router>
    );
}

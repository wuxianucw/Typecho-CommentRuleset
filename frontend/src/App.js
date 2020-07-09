import React from 'react';
import { HashRouter as Router, Route, Redirect } from 'react-router-dom';
import CssBaseline from '@material-ui/core/CssBaseline';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Typography from '@material-ui/core/Typography';
import { darken, makeStyles } from '@material-ui/core/styles';
import { MuiThemeProvider, createMuiTheme } from '@material-ui/core';
import { blue, pink } from '@material-ui/core/colors';

import PluginAppBar from './components/PluginAppBar';
import ResponsiveDrawer from './components/ResponsiveDrawer';
import MenuList from './components/MenuList';

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
}));

export default function App() {
    const classes = useStyles();
    const [mobileOpen, setMobileOpen] = React.useState(false);

    const handleDrawerToggle = () => {
        setMobileOpen(!mobileOpen);
    };

    return (
        <MuiThemeProvider theme={theme}>
            <div className={classes.root}>
                <CssBaseline />
                <PluginAppBar onMenuButtonClick={handleDrawerToggle} />
                <ResponsiveDrawer open={mobileOpen} onMobileClose={handleDrawerToggle}>
                    <div className={classes.toolbar} />
                    <MenuList />
                </ResponsiveDrawer>
                <div style={{ flexGrow: 1 }}>
                    <div className={classes.toolbar} />
                    <Router>
                        <Route>
                            {({ location, history }) => {
                                const path = location.pathname.split("/").filter((val) => (val !== ""));
                                
                                if (path.length < 1 || path.length > 2) return <Redirect to="/overview" />;
                                if (path.length === 2 && path[0] !== "edit") return <Redirect to="/overview" />;
                                if (["overview", "edit", "guide"].indexOf(path[0]) === -1) return <Redirect to="/overview" />;

                                const handleChange = (_, newValue) => {
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
                            <Typography paragraph>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore et dolore magna aliqua. Rhoncus dolor purus non enim praesent elementum
                                facilisis leo vel. Risus at ultrices mi tempus imperdiet. Semper risus in hendrerit
                                gravida rutrum quisque non tellus. Convallis convallis tellus id interdum velit laoreet id
                                donec ultrices. Odio morbi quis commodo odio aenean sed adipiscing. Amet nisl suscipit
                                adipiscing bibendum est ultricies integer quis. Cursus euismod quis viverra nibh cras.
                                Metus vulputate eu scelerisque felis imperdiet proin fermentum leo. Mauris commodo quis
                                imperdiet massa tincidunt. Cras tincidunt lobortis feugiat vivamus at augue. At augue eget
                                arcu dictum varius duis at consectetur lorem. Velit sed ullamcorper morbi tincidunt. Lorem
                                donec massa sapien faucibus et molestie ac.
                            </Typography>
                            <Typography paragraph>
                                Consequat mauris nunc congue nisi vitae suscipit. Fringilla est ullamcorper eget nulla
                                facilisi etiam dignissim diam. Pulvinar elementum integer enim neque volutpat ac
                                tincidunt. Ornare suspendisse sed nisi lacus sed viverra tellus. Purus sit amet volutpat
                                consequat mauris. Elementum eu facilisis sed odio morbi. Euismod lacinia at quis risus sed
                                vulputate odio. Morbi tincidunt ornare massa eget egestas purus viverra accumsan in. In
                                hendrerit gravida rutrum quisque non tellus orci ac. Pellentesque nec nam aliquam sem et
                                tortor. Habitant morbi tristique senectus et. Adipiscing elit duis tristique sollicitudin
                                nibh sit. Ornare aenean euismod elementum nisi quis eleifend. Commodo viverra maecenas
                                accumsan lacus vel facilisis. Nulla posuere sollicitudin aliquam ultrices sagittis orci a.
                            </Typography>
                        </main>
                    </Router>
                </div>
            </div>
        </MuiThemeProvider>
    );
}

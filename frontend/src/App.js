import React from 'react';
import AppBar from '@material-ui/core/AppBar';
import CssBaseline from '@material-ui/core/CssBaseline';
import IconButton from '@material-ui/core/IconButton';
import InboxIcon from '@material-ui/icons/MoveToInbox';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import Link from '@material-ui/core/Link';
import AccountCircle from '@material-ui/icons/AccountCircle';
import MailIcon from '@material-ui/icons/Mail';
import MenuIcon from '@material-ui/icons/Menu';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import { darken, makeStyles } from '@material-ui/core/styles';
import { MuiThemeProvider, createMuiTheme, Tooltip } from '@material-ui/core';
import { blue, pink } from '@material-ui/core/colors';

import ResponsiveDrawer from './components/ResponsiveDrawer';
import NestedList from './components/NestedList';
import ListItemLink from './components/ListItemLink';

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
    appBar: {
        [theme.breakpoints.up('sm')]: {
            zIndex: theme.zIndex.drawer + 1,
        },
    },
    title: {
        flexGrow: 1,
    },
    menuButton: {
        marginRight: theme.spacing(2),
        [theme.breakpoints.up('sm')]: {
            display: 'none',
        },
    },
    noColor: {
        color: "inherit",
    },
    toolbar: theme.mixins.toolbar,
    content: {
        flexGrow: 1,
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
                <AppBar position="fixed" className={classes.appBar}>
                    <Toolbar>
                        <IconButton
                            color="inherit"
                            edge="start"
                            onClick={handleDrawerToggle}
                            className={classes.menuButton}
                        >
                            <MenuIcon />
                        </IconButton>
                        <Typography variant="h6" noWrap className={classes.title}>
                            {window.__pageData.title}
                        </Typography>
                        <Tooltip title={window.__pageData.profile[1]}>
                            <a href={window.__pageData.profile[0]} className={classes.noColor}>
                                <IconButton color="inherit"><AccountCircle /></IconButton>
                            </a>
                        </Tooltip>
                    </Toolbar>
                </AppBar>
                <ResponsiveDrawer open={mobileOpen} onClose={handleDrawerToggle}>
                    <div className={classes.toolbar} />
                    <List>
                        <NestedList open={true} icon={<MailIcon />} primary="Mail 1">
                            <ListItem button>
                                <ListItemIcon><InboxIcon /></ListItemIcon>
                                <ListItemText primary="Inbox 1" />
                            </ListItem>
                            <ListItem button>
                                <ListItemIcon><InboxIcon /></ListItemIcon>
                                <ListItemText primary="Inbox 2" />
                            </ListItem>
                            <ListItem button>
                                <ListItemIcon><InboxIcon /></ListItemIcon>
                                <ListItemText primary="Inbox 3" />
                            </ListItem>
                        </NestedList>
                        <ListItem button>
                            <ListItemIcon><MenuIcon /></ListItemIcon>
                            <ListItemText primary="Menu 1" />
                        </ListItem>
                        <NestedList open={false} icon={<MailIcon />} primary="Mail 2">
                            <ListItem button>
                                <ListItemIcon><InboxIcon /></ListItemIcon>
                                <ListItemText primary="Inbox 1" />
                            </ListItem>
                            <ListItem button>
                                <ListItemIcon><InboxIcon /></ListItemIcon>
                                <ListItemText primary="Inbox 2" />
                            </ListItem>
                            <ListItem button>
                                <ListItemIcon><InboxIcon /></ListItemIcon>
                                <ListItemText primary="Inbox 3" />
                            </ListItem>
                        </NestedList>
                        <ListItemLink icon={<MenuIcon />} primary="GitHub" to="https://github.com/" />
                    </List>
                </ResponsiveDrawer>
                <main className={classes.content}>
                    <div className={classes.toolbar} />
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
            </div>
        </MuiThemeProvider>
    );
}

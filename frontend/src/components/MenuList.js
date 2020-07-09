import React from 'react';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import DashboardIcon from '@material-ui/icons/Dashboard';
import BorderColorIcon from '@material-ui/icons/BorderColor';
import WidgetsIcon from '@material-ui/icons/Widgets';
import SettingsIcon from '@material-ui/icons/Settings';
import makeStyles from '@material-ui/core/styles/makeStyles';

import NestedList from './NestedList';
import ListItemLink from './ListItemLink';

const useStyles = makeStyles((theme) => ({
    nested: {
        paddingLeft: theme.spacing(9),
    },
}));

const icons = [
    <DashboardIcon />,
    <BorderColorIcon />,
    <WidgetsIcon />,
    <SettingsIcon />,
];

export default function MenuList() {
    const classes = useStyles();
    return (
        <List>
            {window.__pageData.menu.map((val, i) => (
                <NestedList
                    key={i}
                    open={val.open}
                    icon={i < icons.length ? icons[i] : null}
                    primary={val.title}
                >
                    {val.children.map((val, i) => (
                        val[1] === true ?
                            <ListItem key={i} button selected className={classes.nested}>
                                <ListItemText primary={val[0]} />
                            </ListItem>
                        :
                            <ListItemLink key={i} primary={val[0]} to={val[1]} className={classes.nested} />
                    ))}
                </NestedList>
            ))}
        </List>
    );
}
